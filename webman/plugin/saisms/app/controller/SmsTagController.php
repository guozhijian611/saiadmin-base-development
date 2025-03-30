<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\saisms\app\logic\SmsTagLogic;
use plugin\saisms\app\validate\SmsTagValidate;
use support\Request;
use support\Response;

/**
 * 短信标签控制器
 */
class SmsTagController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new SmsTagLogic();
        $this->validate = new SmsTagValidate;
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
            ['tag_name', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 测试短信发送
     * @param Request $request
     * @return Response
     */
    public function testTag(Request $request): Response
    {
        $data = $request->post();
        $result = $this->logic->testTag($data);
        return $this->success($result);
    }

}
