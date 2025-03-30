<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Cross implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // 如果是OPTIONS请求，返回一个空响应并设置跨域头
        if ($request->method() == 'OPTIONS') {
            return response('')->withHeaders([
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Origin' => $request->header('origin', '*'),
                'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
                'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
            ]);
        }

        // 处理其他请求
        $response = $handler($request);

        // 添加跨域相关的HTTP头
        $response = $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);

        return $response;
    }
}