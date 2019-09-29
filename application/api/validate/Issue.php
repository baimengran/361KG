<?php

namespace app\api\validate;

use think\Validate;

class Issue extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'attr_id'=>'require|attrCheck',
        'content'=>'require|max:1000',
        'city'=>'require',
        'permission'=>'require|number',
        'valid_time'=>'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [

    ];
}
