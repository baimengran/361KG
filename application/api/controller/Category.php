<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Category extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function category_index()
    {
        try {
            $category = Db::name('category')->where('delete_time', 0)->where('status', 1)
                ->field('id,title,icon')->select();
            return json(['code' => 0, 'msg' => '查询成功', 'data' => $category]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function cate_index(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return json(['code' => 0, 'msg' => '请选择栏目后再操作']);
        }
        try {
            $cate = Db::name('category')->where('pid', $id)->where('delete_time', 0)->where('status', 1)
                ->field('id,title,pid')->select();
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $cate]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function attr(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            return json(['code' => 0, 'msg' => '请选择分类后再操作']);
        }
        try {
            $attr = Db::name('attr')->where('cate_id', $id)->where('delete_time', 0)->where('status', 1)
                ->field('id,name')->select();
            return json(['code' => 0, 'msg' => '查询成功', 'data' => $attr]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}