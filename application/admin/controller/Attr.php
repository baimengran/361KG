<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\Attr as AttrValidate;
use think\Validate;
use app\admin\model\Attr as AttrModel;

class Attr extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $val = $this->request->post('key');
        $id = $this->request->get('id');

        try {
            $cate = Db::name('category')->alias('c')->join('category ca', 'ca.id=c.pid')
                ->where('c.pid', 'neq', 0)->where('c.delete_time', 0)
                ->where('ca.pid', 0)->where('ca.delete_time', 0)
                ->field('c.id,c.title,ca.title as p_title')->order('c.pid asc')->select();

            $attr = Db::name('attr')->alias('a')->join('category c', 'c.id=a.cate_id')
                ->join('category ca', 'c.pid=ca.id')
                ->where('ca.delete_time', 0)->where('ca.pid', 0)
                ->where('c.delete_time', 0)->where('a.delete_time', 0);
            if ($val) {
                $attr = $attr->where('title', 'like', '%' . $val . '%');
            }
            if ($id) {
                $attr = $attr->where('cate_id', $id);
            }
            $attr = $attr->field('a.id,a.cate_id,a.name,a.sort,a.delete_time,a.status,c.id as category_id,c.title as cate,ca.title as p_cate,c.delete_time')
                ->order('sort asc,status desc')->paginate(20);

            $this->assign('id', $id);
            $this->assign('val', $val);
            $this->assign('data', $attr);
            $this->assign('category', $cate);
            return $this->fetch('attr/index');
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
            'sort' => 'require|number|lt:1000',
        ];
        $message = [
            'id' => '排序错误',
            'sort.require' => '排序必须填写',
            'sort.lt' => '排序数字不能大于1000',
            'sort.number' => '排序必须填写数字'
        ];
        $validate = new Validate($rule, $message);
        foreach ($data as $v) {
            if (!$validate->check($v)) {
                return json(['code' => 0, 'msg' => $validate->getError()]);
            }
        }
        try {
            $cate = (new AttrModel())->saveAll($data);
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
            $cate = Db::name('category')->alias('c')->join('category ca', 'ca.id=c.pid')
                ->where('c.pid', 'neq', 0)->where('c.delete_time', 0)
                ->where('ca.pid', 0)->where('ca.delete_time', 0)
                ->field('c.id,c.title,ca.title as p_title')->order('c.pid asc')->select();

            $this->assign('cate', $cate);
            return $this->fetch('attr/add_edit');
        } catch (\Exception $exception) {
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
//        dump($form);die;
        $validate = new AttrValidate();
        if (!$validate->scene('create')->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }

        $data = [];
        foreach ($form['attr'] as $v) {
            $data[] = [
                'cate_id' => $form['cate_id'],
                'sort' => $form['sort'],
                'status' => $form['status'],
                'create_time' => time(),
                'update_time' => time(),
                'name' => $v,
            ];
        }

        try {
            $attr = Db::name('attr')->insertAll($data);
            if (count($data) == $attr) {
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
            $cate = Db::name('category')->alias('c')->join('category ca', 'ca.id=c.pid')
                ->where('c.pid', 'neq', 0)->where('c.delete_time', 0)
                ->where('ca.pid', 0)->where('ca.delete_time', 0)
                ->field('c.id,c.title,ca.title as p_title')->order('c.pid asc')->select();

            $attr = Db::name('attr')->where('id', $id)->where('delete_time', 0)->find();
            $this->assign('cate', $cate);
//            dump($attr);die;
            $this->assign('data', $attr);
            return $this->fetch('attr/add_edit');
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
        $validate = new AttrValidate();
        if (!$validate->scene('edit')->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        $form['name'] = $form['attr_one'];
        $form['update_time'] = time();
        unset($form['attr_one']);
        try {
            $attr = Db::name('attr')->update($form);
            if ($attr) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
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
        try {
            $attr_value = Db::name('attr_value')->where('attr_id', $id)->count('id');
            if ($attr_value) {
                return json(['code' => 0, 'msg' => '当前属性下存在发布内容，不能删除']);
            }
            $attr = Db::name('attr')->where('id', $id)->update(['delete_time' => time()]);
            if ($attr) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request)
    {
        $id = $request->post('id');
        try {
            $status = Db::name('attr')->where('id', $id)->value('status');
            if ($status == 0) {
                $cate = Db::name('attr')->where('id', $id)->update(['status' => 1]);
                return json(['code' => 1, 'msg' => '开启']);
            } else {
                $cate = Db::name('attr')->where('id', $id)->update(['status' => 0]);
                return json(['code' => 0, 'msg' => '关闭']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
