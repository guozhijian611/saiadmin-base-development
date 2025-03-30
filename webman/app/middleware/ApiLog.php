<?php

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use support\Log;

class ApiLog implements MiddlewareInterface
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    
    public function process(Request $request, callable $handler): Response
    {
        // 获取请求路径和URL
        $path = $request->path();
        $url = $request->url();
        
     
        // 忽略日志查看器相关的请求
        if ($path === 'logs' || 
            strpos($path, 'logview') === 0 || 
            strpos($url, '/logview/') !== false ||
            strpos($url, '/logs') !== false) {
            // 直接处理请求，不记录日志
            return $handler($request);
        }
        
        // 获取请求信息
        $method = $request->method();
        $url = $request->url();
        $params = $request->all();

        // 记录请求开始时间
        $startTime = microtime(true);

        // 处理请求
        $response = $handler($request);

        // 计算请求耗时
        $endTime = microtime(true);
        $runtime = round(($endTime - $startTime) * 1000, 2);

        //获取访问请求头以及携带的参数
        $headers = $request->header();
        // 获取状态码
        $statusCode = $response->getStatusCode();

        // 获取返回数据
        $date = date('Y-m-d H:i:s');

        // 美化输出
        $output = "\n";
        $output .= "┌────────────────────────".$date."───────────────────────────\n";
        $output .= "│ 状态码: " . $this->getStatusEmoji($statusCode) . " {$statusCode}\n";
        $output .= "│ 请求方式: {$method}\n";
        $output .= "│ 接口: {$url}\n";
        $output .= "│ 耗时: {$runtime}ms\n";
        // 如果有请求头则输出请求头
        if (!empty($headers)) {
            $output.= "│ 请求头: ". json_encode($headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT). "\n";
        }
        // 如果有参数则输出参数
        if (!empty($params)) {
            $output .= "│ 参数: " . json_encode($params, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        // 响应数据
        if (!empty($response->rawBody())) { 
            $output .= "│ 响应数据: " . json_encode($response->rawBody(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        $output .= "└────────────────────────────────────────────────────\n";

        echo $output;
        Log::info($output);
        return $response;
    }

    /**
     * 根据状态码返回对应的表情
     */
    private function getStatusEmoji(int $code): string
    {
        return match (true) {
            $code >= 500 => '❌',  // 服务器错误
            $code >= 400 => '⚠️',  // 客户端错误
            $code >= 300 => '↪️',  // 重定向
            $code >= 200 => '✅',  // 成功
            default => '❓'        // 其他
        };
    }
}
