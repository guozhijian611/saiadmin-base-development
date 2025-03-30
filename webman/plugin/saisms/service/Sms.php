<?php
namespace plugin\saisms\service;

use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Overtrue\EasySms\Strategies\OrderStrategy;
use plugin\saiadmin\exception\ApiException;
use plugin\saiadmin\utils\Cache;
use plugin\saisms\app\logic\SmsConfigLogic;
use plugin\saisms\app\logic\SmsTagLogic;
use plugin\saisms\service\Link;

class Sms
{
    /**
     * 获取配置
     * @return array
     */
    public static function getConfig(): array
    {
        $key = 'saisms_gateway';
        $data = Cache::get($key);
        if ($data) {
            return $data;
        }
        $logic = new SmsConfigLogic();
        $data = $logic->getGateWays();
        $config = [
            'timeout' => 5.0,
            'default' => [
                'strategy' => OrderStrategy::class,
                'gateways' => array_column($data, 'gateway'),
            ],
            'gateways' => []
        ];
        foreach ($data as $item) {
            $config['gateways'][$item['gateway']] = $item['config'];
        }
        Cache::set($key, $config);
        return $config;
    }

    /**
     * Get Sms
     * @param array $config
     * @return EasySms
     * @throws ApiException
     */
    public static function getSender(array $config = []): EasySms
    {
        if (!class_exists(EasySms::class)) {
            throw new ApiException('请执行 composer require overtrue/easy-sms 并重启');
        }
        $config = $config ?: static::getConfig();
        if (!$config) {
            throw new ApiException('未设置SMS配置');
        }
        $easySms = new EasySms($config);
        // 注册自定义网关
        $easySms->extend('link', function($gatewayConfig){
            return new Link($gatewayConfig);
        });

        return $easySms;
    }

    /**
     * Send Sms
     * @param $to
     * @param array $message
     * @param array $gateways
     * @return array
     */
    public static function send($to, array $message, array $gateways = []): array
    {
        $sms = static::getSender();
        try {
            return $sms->send($to, $message, $gateways);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * 按照标签发送
     * @param $tagName
     * @param $to
     * @param $data
     * @param array $gateways
     * @return array
     * @throws InvalidArgumentException
     * @throws NoGatewayAvailableException
     */
    public static function sendByTag($to, $tagName, $data, array $gateways = []): array
    {
        $config = static::getConfig();
        $logic = new SmsTagLogic();
        $tagList = $logic->getTagList(['tag_name' => $tagName]);
        $templates = [];
        foreach ($tagList as $item) {
            foreach ($config['gateways'] as $gatewayName => $gateway) {
                if ($gatewayName === $item['gateway']) {
                    $templates[$gatewayName] = [
                        'template_id' => $item['template_id'],
                        'content' => $item['content'],
                    ];
                }
            }
        }
        $sms = static::getSender();
        return $sms->send($to, [
            // 不同的厂商有不同的模版id
            'template' => function ($gateway) use ($templates) {
                $gatewayName = $gateway->getName();
                $gatewayName = basename(str_replace('\\', '/', $gatewayName));
                return $templates[$gatewayName]['template_id'];
            },
            'content' => function ($gateway) use ($templates, $data) {
                $gatewayName = $gateway->getName();
                $gatewayName = basename(str_replace('\\', '/', $gatewayName));
                return autoReplace($templates[$gatewayName]['content'], $data);
            },
            'data' => function() use ($data) {
                return $data;
            },
        ] , $gateways);
    }
}