<?php

namespace app\admin\validate;

use think\Db;
use think\Validate;

class Cate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'cate_id' => 'require|number|cateCheck',
        'title' => 'require|max:20',
        'sort' => 'require|number|lt:1000',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'cate_id.require' => '请选择栏目',
        'cate_id.number' => '栏目错误',
        'cate_id.cateCheck' => '栏目错误',
        'title.require' => '请填写分类名称',
        'title.max' => '分类名称不能超过20个字',
        'sort.require' => '排序必须填写',
        'sort.number' => '排序填写错误',
        'sort.lt'=>'排序不能大于1000'
    ];

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
