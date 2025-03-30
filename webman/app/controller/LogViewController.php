<?php

namespace app\controller;

use support\Request;
use support\Response;
use Webman\Http\Response as HttpResponse;

class LogViewController
{
    /**
     * 日志查看器首页
     */
    public function index(Request $request): HttpResponse
    {
        return view('log/index');
    }
    
    /**
     * 获取日志文件列表
     */
    public function getLogFiles(Request $request): HttpResponse
    {
        $logPath = runtime_path() . '/logs/';
        $files = [];
        
        if (is_dir($logPath)) {
            $logFiles = glob($logPath . '*.log');
            
            foreach ($logFiles as $file) {
                $fileName = basename($file);
                $fileSize = filesize($file);
                $fileModified = date('Y-m-d H:i:s', filemtime($file));
                
                // 格式化文件大小
                if ($fileSize >= 1048576) {
                    $fileSize = round($fileSize / 1048576, 2) . ' MB';
                } elseif ($fileSize >= 1024) {
                    $fileSize = round($fileSize / 1024, 2) . ' KB';
                } else {
                    $fileSize = $fileSize . ' B';
                }
                
                $files[] = [
                    'name' => $fileName,
                    'size' => $fileSize,
                    'modified' => $fileModified
                ];
            }
            
            // 按修改时间降序排序
            usort($files, function($a, $b) {
                return strtotime($b['modified']) - strtotime($a['modified']);
            });
        }
        
        return json($files);
    }
    
    /**
     * 获取日志内容
     */
    public function getLogContent(Request $request): HttpResponse
    {
        $fileName = $request->get('file');
        $position = (int)$request->get('position', 0);
        $direction = $request->get('direction', 'forward'); // forward向前，backward向后
        $lines = (int)$request->get('lines', 100); // 默认获取100行
        
        if (empty($fileName)) {
            return json(['code' => 1, 'msg' => '未指定文件名']);
        }
        
        $filePath = runtime_path() . '/logs/' . basename($fileName);
        if (!file_exists($filePath)) {
            return json(['code' => 1, 'msg' => '文件不存在']);
        }
        
        $fileSize = filesize($filePath);
        $content = '';
        
        if ($direction === 'backward' && $position > 0) {
            // 向前获取历史日志
            $content = $this->getPreviousLines($filePath, $position, $lines);
            // 计算新的位置，如果返回的内容少于请求的行数，说明已经达到文件开头
            $newPosition = max(0, $position - strlen($content));
            
            return json([
                'code' => 0,
                'data' => $content,
                'position' => $newPosition,
                'hasMore' => ($newPosition > 0) // 是否还有更多历史内容
            ]);
        } elseif ($position < 0 || $position > $fileSize) {
            // 如果位置无效，返回最后100行
            $content = $this->getLastLines($filePath, $lines);
            $position = $fileSize;
        } elseif ($position < $fileSize) {
            // 如果有新内容，返回新内容
            $handle = fopen($filePath, 'r');
            fseek($handle, $position);
            $content = fread($handle, $fileSize - $position);
            fclose($handle);
            $position = $fileSize;
        }
        
        return json([
            'code' => 0,
            'data' => $content,
            'position' => $position,
            'hasMore' => true // 向后获取时默认还有更多
        ]);
    }
    
    /**
     * 获取指定位置之前的日志行
     */
    private function getPreviousLines(string $filePath, int $position, int $lines = 100): string
    {
        if (!file_exists($filePath) || $position <= 0) {
            return '';
        }
        
        // 计算要读取的大小，默认尝试读取前10KB的内容
        $readSize = min($position, 10240); // 最多读取10KB
        $startPos = max(0, $position - $readSize);
        
        $handle = fopen($filePath, 'r');
        fseek($handle, $startPos);
        $content = fread($handle, $position - $startPos);
        fclose($handle);
        
        // 将内容按行分割
        $contentLines = explode("\n", $content);
        
        // 如果行数超过请求的行数，只返回最后的N行
        if (count($contentLines) > $lines) {
            $contentLines = array_slice($contentLines, -$lines);
        }
        
        return implode("\n", $contentLines);
    }
    
    /**
     * 清空日志文件
     */
    public function clearLog(Request $request): HttpResponse
    {
        // 获取POST请求中的文件名
        $post = $request->post();
        $fileName = $post['file'] ?? '';
        
        if (empty($fileName)) {
            return json(['success' => false, 'message' => '未指定文件名']);
        }
        
        $filePath = runtime_path() . '/logs/' . basename($fileName);
        if (!file_exists($filePath)) {
            return json(['success' => false, 'message' => '文件不存在']);
        }
        
        try {
            // 清空文件内容
            file_put_contents($filePath, '');
            return json(['success' => true, 'message' => '清空成功']);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '清空失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 获取系统资源信息
     */
    public function getSystemInfo(): HttpResponse
    {
        // CPU使用率
        $cpuUsage = 0;
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cpuCores = (int)shell_exec('nproc');
            // 防止除零错误
            if ($cpuCores > 0) {
                $cpuUsage = $load[0] * 100 / $cpuCores;
                $cpuUsage = min(100, round($cpuUsage, 2));
            }
        }
        
        // 内存使用情况
        $memoryInfo = [
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'usage' => 0
        ];
        
        if (PHP_OS === 'Darwin') { // macOS
            $memTotal = shell_exec('sysctl -n hw.memsize');
            $pageSize = shell_exec('sysctl -n vm.pagesize');
            $vmStats = shell_exec('vm_stat');
            
            preg_match('/Pages free: +(\d+)/', $vmStats, $matches);
            $freePages = isset($matches[1]) ? (int)$matches[1] : 0;
            
            $memTotal = (int)$memTotal;
            $memFree = $freePages * (int)$pageSize;
            $memUsed = $memTotal - $memFree;
            
            $memoryInfo['total'] = round($memTotal / 1024 / 1024 / 1024, 2); // GB
            $memoryInfo['free'] = round($memFree / 1024 / 1024 / 1024, 2); // GB
            $memoryInfo['used'] = round($memUsed / 1024 / 1024 / 1024, 2); // GB
            $memoryInfo['usage'] = round(($memUsed / $memTotal) * 100, 2); // 百分比
        } elseif (PHP_OS === 'Linux') { // Linux
            $memInfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)\s+kB/', $memInfo, $matches);
            $memTotal = isset($matches[1]) ? (int)$matches[1] : 0;
            
            preg_match('/MemFree:\s+(\d+)\s+kB/', $memInfo, $matches);
            $memFree = isset($matches[1]) ? (int)$matches[1] : 0;
            
            preg_match('/Buffers:\s+(\d+)\s+kB/', $memInfo, $matches);
            $buffers = isset($matches[1]) ? (int)$matches[1] : 0;
            
            preg_match('/Cached:\s+(\d+)\s+kB/', $memInfo, $matches);
            $cached = isset($matches[1]) ? (int)$matches[1] : 0;
            
            $memFree = $memFree + $buffers + $cached;
            $memUsed = $memTotal - $memFree;
            
            $memoryInfo['total'] = round($memTotal / 1024 / 1024, 2); // GB
            $memoryInfo['free'] = round($memFree / 1024 / 1024, 2); // GB
            $memoryInfo['used'] = round($memUsed / 1024 / 1024, 2); // GB
            $memoryInfo['usage'] = round(($memUsed / $memTotal) * 100, 2); // 百分比
        }
        
        // 磁盘使用情况
        $diskTotal = disk_total_space('/');
        $diskFree = disk_free_space('/');
        $diskUsed = $diskTotal - $diskFree;
        $diskUsage = round(($diskUsed / $diskTotal) * 100, 2);
        
        $diskInfo = [
            'total' => round($diskTotal / 1024 / 1024 / 1024, 2), // GB
            'free' => round($diskFree / 1024 / 1024 / 1024, 2), // GB
            'used' => round($diskUsed / 1024 / 1024 / 1024, 2), // GB
            'usage' => $diskUsage // 百分比
        ];
        
        // 服务器运行时间
        $uptime = 0;
        if (PHP_OS === 'Linux') {
            $uptime = (int)file_get_contents('/proc/uptime');
        } elseif (PHP_OS === 'Darwin') {
            $uptimeStr = shell_exec('sysctl -n kern.boottime');
            preg_match('/sec = (\d+)/', $uptimeStr, $matches);
            if (isset($matches[1])) {
                $bootTime = (int)$matches[1];
                $uptime = time() - $bootTime;
            }
        }
        
        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        $seconds = $uptime % 60;
        
        $uptimeStr = '';
        if ($days > 0) $uptimeStr .= $days . '天 ';
        if ($hours > 0) $uptimeStr .= $hours . '小时 ';
        if ($minutes > 0) $uptimeStr .= $minutes . '分钟 ';
        $uptimeStr .= $seconds . '秒';
        
        // PHP信息
        $phpInfo = [
            'version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . '秒'
        ];
        
        // 返回结果
        return json([
            'code' => 0,
            'data' => [
                'cpu' => [
                    'usage' => $cpuUsage
                ],
                'memory' => $memoryInfo,
                'disk' => $diskInfo,
                'uptime' => $uptimeStr,
                'php' => $phpInfo,
                'time' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ]
        ]);
    }
    
    /**
     * 获取文件最后几行
     */
    private function getLastLines(string $filePath, int $lines = 100): string
    {
        if (!file_exists($filePath)) {
            return '';
        }
        
        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            return '';
        }
        
        $handle = fopen($filePath, 'r');
        $lineCount = 0;
        $pos = $fileSize - 1;
        $beginning = false;
        $text = '';
        
        while ($pos >= 0 && $lineCount < $lines) {
            fseek($handle, $pos);
            $char = fgetc($handle);
            
            if ($char === "\n" && !$beginning) {
                $lineCount++;
            } elseif ($char === "\n" && $beginning) {
                $beginning = false;
            }
            
            if ($lineCount < $lines) {
                $text = $char . $text;
            }
            
            $pos--;
            if ($pos < 0) {
                $beginning = true;
            }
        }
        
        fclose($handle);
        
        return $text;
    }
}
