<?php

namespace app\admin\controller;

use app\common\CategoryTree;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\Category as CategoryValidate;
use app\admin\model\Category as CategoryModel;
use think\Validate;

class Category extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $key = $this->request->post('key');
            $cate = Db::name('category')->where('delete_time', 0)->where('pid',0);
            if ($key) {
                $cate = $cate->where('title', 'like', '%' . $key . '%');
            }
            $cate = $cate->order('sort asc,create_time desc')->all();
            $this->assign('val', $key);
            $this->assign('data', $cate);
            return $this->fetch('category/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function sort(Request $request)
    {
        $form = $request->post();

        $data = [];
        foreach ($form as $k => $v) {
            $data[] = ['id' => trim($k), 'sort' => trim($v),];
        }

        $rule = [
            'id' => 'require',
            'sort' => 'require|number',
        ];
        $message = [
            'id' => '排序错误',
            'sort.require' => '排序必须填写',
            'sort.number' => '排序必须填写数字'
        ];
        $validate = new Validate($rule, $message);
        foreach ($data as $v) {
            if (!$validate->check($v)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }
        }
        try {
            $cate = (new CategoryModel())->saveAll($data);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        try {
            $cate = Db::name('category')->where('pid', 0)->select();
            $this->assign('cate', $cate);
            return $this->fetch('category/add_edit');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $form = $request->post();
        $validate = new CategoryValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $cate = CategoryModel::create($form);
            if ($cate) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        try {
            $cate = CategoryModel::get($id);
            $this->assign('data', $cate);
            return $this->fetch('category/add_edit');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $form = $request->post();
        $validate = new CategoryValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $cate = CategoryModel::update($form);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $cate_attr = Db::name('con_attr')->where('delete_time',0)->where('cate_id', $id)->count('id');
        if ($cate_attr) {
            return json(['code' => 0, 'msg' => '当前栏目下存在其他属性或分类，不能删除']);
        }
        try {
            $cate = Db::name('category')->where('id', $id)->update(['delete_time' => time()]);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request)
    {
        $id = $request->post('id');
        try {
            $cate = Db::name('category')->where('id', $id)->value('status');
            if ($cate == 1) {
                Db::name('category')->where('id', $id)->update(['status' => 0]);
                return json(['code' => 0, 'msg' => '关闭']);
            } else {
                Db::name('category')->where('id', $id)->update(['status' => 1]);
                return json(['code' => 1, 'msg' => '开启']);
            }
        }catch(\Exception $e){
            return json(['code'=>-1,'msg'=>'系统错误']);
        }
    }
}
