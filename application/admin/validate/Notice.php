<?php

namespace app\admin\validate;

use think\Validate;

class Notice extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
//	    'content'=>'require|max:50',
        'pic' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'content.require' => '公告内容必须填写',
        'content.max' => '公告内容不能大于50个字',
        'pic.require' => '请上传图片'
    ];
}
