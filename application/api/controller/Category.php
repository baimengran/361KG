<?php

namespace app\api\controller;

use app\common\CategoryTree;
use think\Controller;
use think\Db;
use think\facade\Log;
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
                ->field('id,title,icon')->where('pid',0)->select();
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $category]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
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
            $attr = Db::name('attr')->where('delete_time', 0)->field('id,name,cate_id')->where('status', 1)->select();

            foreach ($cate as $i => $v) {
                foreach ($attr as $ii => $va) {
                    if ($v['id'] == $va['cate_id']) {
                        $cate[$i]['attr'][] = $va;
                    }
                }
            }
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $cate]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
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
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function category_column()
    {
        try {
            $cate = Db::name('category')->alias('c')->field('id,title,pid,icon')
                ->where('c.delete_time', 0)->where('c.status', 1)
                ->select();
            $attr = Db::name('attr')->where('delete_time', 0)->field('id,name,cate_id')->where('status', 1)->select();
            $cate_children = (new CategoryTree())->getChildren($cate, $attr);
            $data = [
                ['id'=>-3,'title'=>'附近','cate'=>[[
                    'id'=>'',
                    'title'=>'默认分类',
                    'attr'=>[['id'=>'','name'=>'默认标签']]
                ]]
                ],
                ['id'=>-2,'title'=>'关注','cate'=>[[
                    'id'=>'',
                    'title'=>'默认分类',
                    'attr'=>[['id'=>'','name'=>'默认标签']]
                ]]
                ],
                ['id' =>-1, 'title'=>'推荐','cate'=>[[
                    'id'=>'',
                    'title'=>'默认分类',
                    'attr'=>[['id'=>'','name'=>'默认标签']]
                ]]
                ],
            ];

            foreach ($cate_children as $k => $v) {
                foreach ($v['cate'] as $ii => $vv) {
                    foreach ($attr as $i => $va) {
                        if ($vv['id'] == $va['cate_id']) {
                            $cate_children[$k]['cate'][$ii]['attr'][] = $va;
                        }
                    }
                }
            }
            foreach($data as $v){
                array_unshift($cate_children,$v);
            }
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $cate_children]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }

    }
}
