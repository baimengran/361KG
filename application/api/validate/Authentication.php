<?php

namespace app\api\validate;

use think\Validate;

class Authentication extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'person_auth_name'=>'require|max:50',
        'person_auth_pic_front'=>'require',
        'person_auth_pic_rear'=>'require',
        'person_auth_id_card'=>'require|idCard',
        'company_auth_name'=>'require|max:100',
        'company_auth_pic'=>'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'person_auth_name.require'=>'姓名必须填写',
        'person_auth_name.max'=>'姓名字数过多',
        'person_auth_pic_front.require'=>'请上传正面证件照',
        'person_auth_pic_rear.require'=>'请上传背面证件照',
        'person_auth_id_card.require'=>'请输入证件号码',
        'person_auth_id_card.idCard'=>'证件号码输入错误',
        'company_auth_name.require'=>'请输入企业名称',
        'company_auth_name.max'=>'企业名称字数过多',
        'company_auth_pic.require'=>'请上传企业营业执照'
    ];

    protected $scene=[
        'person'=>['person_auth_name','person_auth_pic_front','person_auth_pic_rear','person_auth_id_card'],
        'company'=>['company_auth_name','company_auth_pic'],
    ];
}
