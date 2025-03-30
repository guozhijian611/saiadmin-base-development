<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\logic;

use plugin\saiadmin\basic\BaseLogic;
use plugin\saiadmin\exception\ApiException;
use plugin\saiadmin\utils\Helper;
use plugin\saisms\app\model\SmsConfig;

/**
 * 短信配置逻辑层
 */
class SmsConfigLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new SmsConfig();
    }

    public function getGateWays()
    {
        $query = $this->search(['status' => 1])->order('sort', 'desc');
        return $this->getAll($query);
    }

}
