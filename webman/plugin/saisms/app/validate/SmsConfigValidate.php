<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\saisms\app\validate;

use plugin\saisms\app\model\SmsConfig;
use think\Validate;

/**
 * 短信配置验证器
 */
class SmsConfigValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule =   [
        'config_name' => 'require',
        'gateway' => 'require|unique:'.SmsConfig::class,
    ];

    /**
     * 定义错误信息
     */
    protected $message  =   [
        'config_name' => '网关名称必须填写',
        'gateway.require' => '网关必须填写',
        'gateway.unique' => '网关不能重复',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'save' => [
            'config_name',
            'gateway'
        ],
        'update' => [
            'config_name',
            'gateway'
        ],
    ];

    /**
     * 验证是否唯一
     * @access public
     * @param mixed  $value 字段值
     * @param mixed  $rule  验证规则 格式：数据表,字段名,排除ID,主键名
     * @param array  $data  数据
     * @param string $field 验证字段名
     * @return bool
     */
    public function unique($value, $rule, array $data = [], string $field = ''): bool
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }

        if (str_contains($rule[0], '\\')) {
            // 指定模型类
            $db = new $rule[0];
        } else {
            return false;
        }

        $key = $rule[1] ?? $field;
        $map = [];

        if (str_contains($key, '^')) {
            // 支持多个字段验证
            $fields = explode('^', $key);
            foreach ($fields as $key) {
                if (isset($data[$key])) {
                    $map[] = [$key, '=', $data[$key]];
                }
            }
        } elseif (strpos($key, '=')) {
            // 支持复杂验证
            parse_str($key, $array);
            foreach ($array as $k => $val) {
                $map[] = [$k, '=', $data[$k] ?? $val];
            }
        } elseif (isset($data[$field])) {
            $map[] = [$key, '=', $data[$field]];
        }

        $pk = !empty($rule[3]) ? $rule[3] : $db->getPk();

        if (is_string($pk)) {
            if (isset($rule[2])) {
                $map[] = [$pk, '<>', $rule[2]];
            } elseif (isset($data[$pk])) {
                $map[] = [$pk, '<>', $data[$pk]];
            }
        }

        if ($db->withTrashed()->where($map)->field($pk)->find()) {
            return false;
        }

        return true;
    }

}
