<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

// 全局处理 OPTIONS 请求
Route::options('/{path:.+}', function ($request, $path) {
    return response('')->withHeaders([
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Origin' => $request->header('origin', '*'),
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Authorization, Content-Type, X-Requested-With',
    ]);
});

// 日志查看器路由
Route::get('/logs', [\app\controller\LogViewController::class, 'index']);
Route::get('/logview/getLogFiles', [\app\controller\LogViewController::class, 'getLogFiles']);
Route::get('/logview/getLogContent', [\app\controller\LogViewController::class, 'getLogContent']);
Route::post('/logview/clearLog', [\app\controller\LogViewController::class, 'clearLog']);
Route::get('/logview/getSystemInfo', [\app\controller\LogViewController::class, 'getSystemInfo']);

// 404兜底路由
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});


// 禁用默认路由
Route::disableDefaultRoute('','');
