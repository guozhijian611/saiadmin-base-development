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
use plugin\saisms\app\model\SmsTag;
use plugin\saisms\service\Sms;
use Throwable;

/**
 * 短信标签逻辑层
 */
class SmsTagLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new SmsTag();
    }

    public function getTagList($data)
    {
        $query = $this->search($data);
        return $this->getAll($query);
    }

    public function testTag($params)
    {
        $data['mobile'] = $params['mobile'];
        $data['tag_name'] = $params['tag_name'];
        $data['gateway'] = [$params['gateway']];
        $logic = new SmsRecordLogic();
        return $logic->sendCode($data);
    }

}
