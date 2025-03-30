<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 短信配置模型
 */
class SmsConfig extends BaseModel
{

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 数据库表名称
     * @var string
     */
    protected $table = 'saisms_config';

    
    /**
     * 网关名称 搜索
     */
    public function searchConfigNameAttr($query, $value)
    {
        $query->where('config_name', 'like', '%'.$value.'%');
    }

    public function getConfigAttr($value)
    {
        return json_decode($value ?? '', true);
    }

    public function setConfigAttr($value)
    {
        return json_encode($value ?? '', JSON_UNESCAPED_UNICODE);
    }

}
