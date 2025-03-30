<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saipay\app\logic;

use plugin\saiadmin\utils\Arr;
use plugin\saipay\app\model\Bill;
use plugin\saiadmin\basic\BaseLogic;
use plugin\saiadmin\app\logic\system\SystemConfigLogic;
use plugin\saiadmin\exception\ApiException;
use plugin\saipay\service\AlipayService;
use plugin\saipay\service\WechatPayService;
use plugin\saipay\service\UnipayService;

/**
 * 支付逻辑
 */
class PayLogic extends BaseLogic
{
    /**
     * 创建订单
     * @param $data
     * @return bool
     */
    public function createBill($data): bool
    {
        $bill = Bill::create([
            'order_sn' => $data['order_sn'],
            'user_id' => $data['user_id'],
            'pay_status' => $data['pay_status'],
            'pay_method' => $data['pay_method'],
            'money' => $data['order_price'],
            'message' =>  $data['order_name'],
            'transaction_id' => $data['order_no'],
            'extra' => $data['extra']
        ]);
        return $bill->id > 0;
    }

    /**
     * 支付宝支付
     */
    public function alipayOrder($type, $data): array
    {
        $alipay = AlipayService::getInstance();
        $order = [
            'out_trade_no' => uuid(),
            'subject' => $data['order_name'],
            'total_amount' => $data['order_price']
        ];
        $result = [
            'pay_method' => $data['pay_method'],
            'pay_type' => $type,
            'order_sn' => $order['out_trade_no']
        ];
        switch($type) {
            case 'web':
                $html = $alipay->web($order)->getBody()->getContents();
                $result['html'] = $html;
                break;
            case 'h5':
                if (!empty($data['quit_url'])) {
                    $order['quit_url'] = $data['quit_url'];
                }
                $html = $alipay->h5($order)->getBody()->getContents();
                $result['html'] = $html;
                break;
            case 'scan':
                $resp = $alipay->scan($order);
                $result['code_url'] = $resp['qr_code'];
                break;
            default:
                throw new ApiException('支付方法错误');

        }
        $data['pay_status'] = 2;
        $data['order_sn'] = $order['out_trade_no'];
        $data['extra'] = $order['extra'] ?? '';
        if ($this->createBill($data)) {
            return $result;
        } else {
            throw new ApiException('创建支付账单失败');
        }
    }

    /**
     * 微信支付
     */
    public function wechatOrder($type, $data): array
    {
        $wechat = WechatPayService::getInstance();
        $order = [
            'out_trade_no' => uuid(),
            'description' => $data['order_name'],
            'amount' => [
                'total' => $data['order_price'] * 100,
            ]
        ];
        $result = [
            'pay_method' => $data['pay_method'],
            'pay_type' => $type,
            'order_sn' => $order['out_trade_no']
        ];
        switch($type) {
            case 'mp':
                $order['payer'] = [
                    'openid' => $data['openid']
                ];
                $resp = $wechat->mp($order);
                $result['info'] = $resp;
                break;
            case 'h5':
                $order['scene_info'] = [
                    'payer_client_ip' => $data['client_ip'],
                    'h5_info' => [
                        'type' => 'Wap',
                    ]
                ];
                $resp = $wechat->h5($order);
                $result['url'] = $resp['h5_url'];
                break;
            case 'mini':
                $order['payer'] = [
                    'openid' => $data['openid']
                ];
                $resp = $wechat->mini($order);
                $result['info'] = $resp;
                break;
            case 'scan':
                $logic = new SystemConfigLogic();
                $wxpay_config = $logic->getGroup('wxpay_config');
                $type = Arr::getConfigValue($wxpay_config, 'type');
                if (!empty($type)) {
                    $order['_type'] = $type;
                }
                $resp = $wechat->scan($order);
                $result['code_url'] = $resp['code_url'];
                break;
            default:
                throw new ApiException('支付方法错误');

        }
        $data['pay_status'] = 2;
        $data['order_sn'] = $order['out_trade_no'];
        $data['extra'] = $order['extra'] ?? '';
        if ($this->createBill($data)) {
            return $result;
        } else {
            throw new ApiException('创建支付账单失败');
        }
    }

    /**
     * 银联支付
     */
    public function unipayOrder($type, $data): array
    {
        $unipay = UnipayService::getInstance();
        $order = [
            'txnTime' => date('YmdHis'),
            'orderId' => uuid(),
            'txnAmt' => $data['order_price']
        ];
        $result = [
            'pay_method' => $data['pay_method'],
            'pay_type' => $type,
            'order_sn' => $order['orderId']
        ];
        switch($type) {
            case 'web':
                $html = $unipay->web($order)->getBody()->getContents();
                $result['html'] = $html;
                break;
            case 'h5':
                $html = $unipay->h5($order)->getBody()->getContents();
                $result['html'] = $html;
                break;
            case 'scan':
                $resp = $unipay->scan($order);
                $result['code_url'] = $resp['qrCode'];
                break;
            default:
                throw new ApiException('支付方法错误');

        }
        $data['pay_status'] = 2;
        $data['order_sn'] = $order['orderId'];
        $data['extra'] = $order['extra'] ?? '';
        if ($this->createBill($data)) {
            return $result;
        } else {
            throw new ApiException('创建支付账单失败');
        }
    }
}