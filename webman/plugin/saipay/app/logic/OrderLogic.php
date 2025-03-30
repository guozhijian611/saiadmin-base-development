<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saipay\app\logic;

use plugin\saipay\app\model\Order;
use plugin\saiadmin\basic\BaseLogic;
use plugin\saiadmin\exception\ApiException;

/**
 * 订单记录逻辑层
 */
class OrderLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new Order();
    }

    /**
     * 创建订单
     */
    public function createOrder($data): array
    {
        // 1、创建订单
        $orderData = [
            'order_no' => uuid(),
            'order_name' => $data['order_name'],
            'order_price' => $data['order_price'],
            'pay_price' => 0.00,
            'remark' => $data['remark'],
            'pay_method' => $data['pay_method'],
            'pay_status' => 2,
            'order_status' => 1,
        ];
        $order = Order::create($orderData);
        if ($order->id > 0) {
            return $order->toArray();
        } else {
            throw new ApiException('创建订单失败');
        }
    }

    /**
     * 支付订单
     */
    public function payOrder($data): array
    {
        $order = $this->model->findOrEmpty($data['id']);
        if ($order->isEmpty()) {
            throw new ApiException('订单不存在');
        }
        if ($order->pay_status === 1) {
            throw new ApiException('订单已支付');
        }
        if ($order->order_status !== 1) {
            throw new ApiException('当前订单状态无法支付');
        }
        $orderData = $order->toArray();
        if (!empty($data['pay_method'])) {
            $orderData['pay_method'] = $data['pay_method'];
        }
        $orderData['user_id'] = $this->adminInfo['id'];
        return $this->createPay($orderData);
    }

    /**
     * 创建支付
     */
    public function createPay($data): array
    {
        $payLogic = new PayLogic();
        switch($data['pay_method']) {
            case 1:
                return $payLogic->alipayOrder('scan', $data);
            case 2:
                return $payLogic->wechatOrder('scan', $data);
            case 4:
                return $payLogic->unipayOrder('scan', $data);
            default:
                throw new ApiException('创建支付信息失败');
        }
    }

}