<?php

namespace plugin\saipay\app\controller;

use support\Request;
use support\Response;
use plugin\saipay\app\logic\OrderLogic;
use plugin\saiadmin\basic\BaseController;

/**
 * 订单模块控制器
 */
class OrderController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new OrderLogic();
        parent::__construct();
    }

    /**
     * 订单列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $where = $request->more([
            ['pay_status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $query->order('create_time', 'desc');
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 下单
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $data = $request->post();
        $result = $this->logic->createOrder($data);
        return $this->success($result);
    }

    /**
     * 支付
     * @param Request $request
     * @return Response
     */
    public function pay(Request $request): Response
    {
        $data = $request->post();
        $result = $this->logic->payOrder($data);
        return $this->success($result);
    }

}