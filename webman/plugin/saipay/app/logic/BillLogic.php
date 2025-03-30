<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saipay\app\logic;

use plugin\saipay\app\model\Bill;
use plugin\saipay\app\model\Order;
use plugin\saiadmin\basic\BaseLogic;

/**
 * 账单记录逻辑层
 */
class BillLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new Bill();
    }

    /**
     * 支付通知处理
     */
    public function notifyOrder($trade_no, $money)
    {
        $bill = $this->model->where('order_sn', $trade_no)->where('pay_status', 2)->findOrEmpty();
        if (!$bill->isEmpty()) {
            // 未处理
            $this->transaction(function() use ($bill, $money) {
                $order = Order::where('order_no', $bill->transaction_id)->where('pay_status', 2)->findOrEmpty();
                $bill->pay_status = 1;
                $bill->money = $money;
                if (!$order->isEmpty()) {
                    $order->pay_method = $bill->pay_method;
                    $order->pay_status = 1;
                    $order->pay_price = $money;
                    $order->pay_time = date('Y-m-d H:i:s');
                    $order->order_status = 2;
                    $order->save();
                } else {
                    $bill->extra = '重复支付';
                }
                $bill->save();
            });
        }
    }

    /**
     * 检查订单情况
     * @param $order_sn
     * @return bool
     */
    public function checkOrder($order_sn): bool
    {
        $bill = $this->model->where('order_sn', $order_sn)->where('pay_status', 1)->findOrEmpty();
        if (!$bill->isEmpty()) {
            return true;
        } else {
            return false;
        }
    }

}