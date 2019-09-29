<?php

namespace app\admin\validate;

use think\Validate;

class ConAttr extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'cate_id' => 'require|gt:0',
        'name' => 'require',
        'status' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'cate_id.require' => '请选择分类',
        'cate_id.gt' => '请选择分类',
        'name' => '属性名称必须填写',
        'status' => '状态必须填写',
    ];
}
