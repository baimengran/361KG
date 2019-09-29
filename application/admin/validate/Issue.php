<?php

namespace app\admin\validate;

use think\Db;
use think\Validate;

class Issue extends Validate
{
    protected $regex = ['phone'=>'/^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$/'];
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require|min:5|max:50',
        'description' => 'require',
        'cate_id' => 'require|cateCheck',
        'phone'=>'regex:phone|phoneCheck',
        'contact'=>'phoneCheck'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '标题必须填写',
        'title.min' => '标题不能小于5个字',
        'title.max' => '标题不能大于50个字',
        'description.require' => '简介必须填写',
        'cate_id.require' => '请选择栏目',
        'cate_id.cateCheck' => '请选择正确栏目',
        'phone.regex'=>'联系方式填写错误',
    ];

    protected $scene = [
        'edit'=>['title','description'],
    ];

    public function cateCheck($rule, $value, $data)
    {
        $cate = Db::name('category')->where('id', $rule)->count('id');
        if ($cate) {
            return true;
        } else {
            return false;
        }
    }

    public function phoneCheck($rule,$value,$data){
        if($data['phone']!=''&&$data['contact']==''){
            return '请填写联系人';
        }else if($data['contact']!=''&&$data['phone']==''){
            return '请填写联系方式';
        }
        return true;
    }
}
