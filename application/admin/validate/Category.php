<?php

namespace app\admin\validate;

use think\Validate;

class category extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'title'=>'require|min:2|max:20',
        'icon'=>'require',
        'sort'=>'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'title.require'=>'栏目名称必须填写',
        'title.min'=>'栏目名称不能小于两个字',
        'title.max'=>'栏目名称不能大于20个字',
        'icon'=>'图标必须上传',
        'sort.require'=>'排序必须填写',
        'sort.number'=>'排序填写错误',
    ];
}
