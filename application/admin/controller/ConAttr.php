<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\ConAttr as ConAttrValidate;
use app\admin\model\ConAttr as ConAttrModel;
use think\Validate;
use app\admin\model\Category as CategoryModel;

class ConAttr extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $id = $this->request->get('id');

            $key = $this->request->post('key');
            $cate = Db::name('con_attr')->alias('ca')->join('category c', 'ca.cate_id=c.id')
                ->field('c.id as category_id,c.title,c.delete_time,ca.id,ca.name,ca.delete_time,ca.create_time,ca.status,ca.type,ca.sort')
                ->where('ca.delete_time', 0)->where('c.delete_time', 0);
            if($id){
                $cate = $cate->where('c.id',$id);
            }
            if ($key) {
                $cate = $cate->where('name', 'like', '%' . $key . '%');
            }
            $param = \think\facade\Request::param();
            $cate = $cate->order('ca.sort asc,create_time desc')->paginate(2,false,['query'=>$param]);

            $category = Db::name('category')->where('delete_time',0)->where('status',1)
                ->where('pid',0)->select();

            $this->assign('id',$id);
            $this->assign('category',$category);
            $this->assign('val', $key);
            $this->assign('data', $cate);
            return $this->fetch('con_attr/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
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
            $cate = Db::name('category')->where('delete_time', 0)->where('pid', 0)->all();
            $this->assign('cate', $cate);
            return $this->fetch('con_attr/add_edit');
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
        $validate = new ConAttrValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $con_attr = ConAttrModel::create($form);
            if ($con_attr) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
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
            $cate = (new ConAttrModel())->saveAll($data);
            return json(['code' => 1, 'msg' => '操作成功']);
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
            $attr = ConAttrModel::get($id);
            if($attr['type_value']!=null){
                $attr['type_value'] = explode(',',$attr['type_value']);
            }

            $cate = CategoryModel::field('id,title')->all();
            $this->assign('cate', $cate);
            $this->assign('data', $attr);
            return $this->fetch('con_attr/add_edit');
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
        $validate = new ConAttrValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        if ($form['type'] == 4 || $form['type'] == 5) {
            if (array_key_exists('che', $form)) {
                $form['type_value'] = implode(',', $form['che']);
                unset($form['che']);
            }else{
                return json(['code' => 0, 'msg' =>'请填写选型']);
            }
        }

        try {
            $cate = ConAttrModel::update($form);
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
        $con_attr_value = Db::name('con_attr_value')->where('con_attr_id', $id)->value('issue_id');
        $issue = Db::name('issue')->where('delete_time',0)->where('id',$con_attr_value)->count('id');
        if ($issue) {
            return json(['code' => 0, 'msg' => '当前属性下存在发布内容，不能删除']);
        }
        try {
            $cate = Db::name('con_attr')->where('id', $id)->update(['delete_time' => time()]);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request){
        $id = $request->post('id');
        try {
            $cate = Db::name('con_attr')->where('id', $id)->value('status');
            if ($cate == 1) {
                Db::name('con_attr')->where('id', $id)->update(['status' => 0]);
                return json(['code' => 0, 'msg' => '关闭']);
            } else {
                Db::name('con_attr')->where('id', $id)->update(['status' => 1]);
                return json(['code' => 1, 'msg' => '开启']);
            }
        }catch(\Exception $e){
            return json(['code'=>-1,'msg'=>'系统错误']);
        }
    }
}
