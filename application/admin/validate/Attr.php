<?php

namespace app\admin\validate;

use think\Db;
use think\Validate;

class Attr extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'attr' => 'require|array|min:1|attrCheck',
        'attr_one'=>'require|max:20',
        'status' => 'number',
        'sort' => 'require|number|lt:1000',
        'cate_id' => 'require|cateCheck',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'attr.require' => '请添加属性',
        'attr.array' => '属性值填写错误',
        'attr.min' => '请添加属性',
        'attr_one.require'=>'请填写属性',
        'attr_one.max'=>'属性不能大于20个字',
        'status.number' => '状态错误',
        'sort.require' => '排序必须填写',
        'sort.number' => '排序填写错误',
        'sort.lt' => '排序数字不能大于1000',
        'cate_id.require' => '请选择分类',
        'cate_id.cateCheck' => '分类选择错误',
    ];

    protected $scene=[
        'edit'=>['attr_one','sort','status','cate_id'],
        'create'=>['attr','sort','status','cate_id'],
    ];

    public function attrCheck($rule,$value,$data){
        foreach($rule as $v){
            if(strlen($v)>60){
                return '属性不能大于20个字';
            }
        }
        return true;
    }

    public function cateCheck($rule, $value, $data)
    {
        try {
            $cate = Db::name('category')->where('id', $rule)->where('delete_time', 0)->count('id');
            if ($cate) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return '系统错误';
        }
    }
}
