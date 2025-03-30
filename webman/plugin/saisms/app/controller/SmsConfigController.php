<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\saiadmin\utils\Cache;
use plugin\saisms\app\logic\SmsConfigLogic;
use plugin\saisms\app\validate\SmsConfigValidate;
use support\Request;
use support\Response;

/**
 * 短信配置控制器
 */
class SmsConfigController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new SmsConfigLogic();
        $this->validate = new SmsConfigValidate;
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
            ['config_name', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 清理缓存
     * @return void
     */
    public function afterChange($type)
    {
        $key = 'saisms_gateway';
        Cache::clear($key);
    }
}
