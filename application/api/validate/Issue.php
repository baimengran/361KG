<?php

namespace app\api\validate;

use think\Db;
use think\Validate;

class Issue extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'attr_id' => 'require|attrCheck',
        'content' => 'require|max:1000',
        'province' => 'require',
        'district' => 'require',
        'city' => 'require',
        'permission' => 'require|number|in:0,1,2',
        'valid_time' => 'require|number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'attr_id' => '请选择标签',
        'content.require' => '请输入内容',
        'content.max' => '内容不能大于1000个字符',
        'province.require' => '请选择省份',
        'city' => '请选择城市',
        'district.require' => '请选择区域',
        'permission.require' => '请设置联系权限',
        'permission.number' => '联系权限设置错误',
        'permission.in' => '联系权限设置错误',
        'valid_time.require' => '有效时间必须设置',
        'valid_time.number' => '有效时间设置错误',
    ];

    public function attrCheck($rule, $value, $data)
    {
        $attr_array = explode(',', $rule);
        $count = count($attr_array);
        if (count($attr_array) > 3) {
            return '最多只能选择三个标签';
        }
        try {
            $attr_cate_id = Db::name('attr')->where('id', 'in', $attr_array)->group('cate_id')->having("count(id=$count)")
                ->field('cate_id')->select();
            if (count($attr_cate_id) > 1 || count($attr_cate_id) < 1) {
                return '标签未找到，请刷新网页后重试';
            }

            $cate = Db::name('category')->where('id', $attr_cate_id[0]['cate_id'])->field('id,pid')->find();
            if (!$cate) {
                return '标签未找到';
            }
            $category = Db::name('category')->where('id', $cate['pid'])->count('id');
            if (!$category) {
                return '标签未找到';
            }
        } catch (\Exception $e) {
            return '系统错误';
        }

        return true;
    }
}
