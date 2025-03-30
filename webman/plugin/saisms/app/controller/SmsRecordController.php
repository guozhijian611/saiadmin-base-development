<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\saisms\app\logic\SmsRecordLogic;
use plugin\saisms\app\validate\SmsRecordValidate;
use support\Request;
use support\Response;

/**
 * 短信记录控制器
 */
class SmsRecordController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new SmsRecordLogic();
        $this->validate = new SmsRecordValidate;
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
            ['gateway', ''],
            ['mobile', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

}
