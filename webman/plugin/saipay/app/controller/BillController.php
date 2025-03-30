<?php

namespace plugin\saipay\app\controller;

use support\Request;
use support\Response;
use plugin\saipay\app\logic\BillLogic;
use plugin\saiadmin\basic\BaseController;

/**
 * 账单控制器
 */
class BillController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new BillLogic();
        parent::__construct();
    }

    /**
     * 数据列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $where = $request->more([
            ['order_sn', ''],
            ['transaction_id', ''],
            ['pay_status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $query->order('create_time', 'desc');
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

}
