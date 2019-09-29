<?php

namespace app\api\validate;

use think\Db;
use think\Validate;

class Comment extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'issue_id'=>'require|issueCheck',
        'content'=>'require|max:500',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'issue_id.require'=>'请选择正确内容后评论',
        'content.require'=>'评论内容必须填写',
        'content.min'=>'评论内容不能小于5个字',
        'content.max'=>'评论内容不能大于500个字',
    ];

    public function issueCheck($rule,$value,$data){
        $issue = Db::name('issue')->where('id',$rule)->where('delete_time',0)
            ->where('status',1)->count('id');
        if(!$issue){
            return '当前主题不存在，请刷新后重试';
        }
        return true;
    }
}
