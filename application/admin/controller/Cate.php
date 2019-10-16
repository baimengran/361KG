<?php

namespace app\admin\controller;

use app\common\CategoryTree;
use app\common\WechatOline;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\Cate as CateValidate;
use think\Response;
use think\Validate;
use app\admin\model\Category as CategoryModel;

class Cate extends Controller
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
            $category = Db::name('category')->where('pid', 0)->where('delete_time', 0)
                ->field('id,title')->select();
            $cate = Db::name('category')->alias('c')->join('category ca', 'ca.id=c.pid')
                ->where('c.pid', 'neq', 0)->where('c.delete_time', 0)
                ->field('c.id,c.title,c.sort,c.status,from_unixtime(c.create_time,\'%Y-%m-%d\') as create_time,c.pid,ca.title as cate');
            if ($val) {
                $cate = $cate->where('c.title', 'like', '%' . $val . '%');
            }
            if ($id) {
                $cate = $cate->where('c.pid', $id);
            }
            $param = \think\facade\Request::param();
            $cate = $cate->order('c.sort asc,c.create_time desc')->paginate(20,false,['query'=>$param]);

//            $cate = (new CategoryTree())->getChildren($cate);
            $this->assign('id', $id);
            $this->assign('data', $cate);
            $this->assign('category', $category);
            $this->assign('val', $val);
            return $this->fetch('cate/index');
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
            $cate = Db::name('category')
                ->where('delete_time', 0)->where('status', 1)
                ->where('pid', 0)->select();
            $this->assign('cate', $cate);
            return $this->fetch('cate/add_edit');
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
        $validate = new CateValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $category = Db::name('category')->where('id', $form['cate_id'])->find();
            if (!$category) {
                return json(['code' => 0, 'msg' => '当前栏目不存在']);
            }
            $data = [
                'title' => $form['title'],
                'pid' => $category['id'],
                'path' => $category['path'] . $category['id'] . '-',
                'level' => $category['level'] + 1,
                'sort' => $form['sort'],
                'status' => 1,
                'create_time' => time(),
                'update_time' => time(),
            ];
            $category = Db::name('category')->insert($data);
            if ($category) {
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
            $category = Db::name('category')->where('pid', 0)->where('delete_time', 0)
                ->field('id,title')->select();
            $cate = Db::name('category')->where('id', $id)->find();
            $this->assign('data', $cate);
            $this->assign('cate', $category);
            return $this->fetch('cate/add_edit');
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
        $validate = new CateValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $category = Db::name('category')->where('id', $form['cate_id'])->where('delete_time', 0)->find();
            if (!$category) {
                return json(['code' => 0, 'msg' => '选择的栏目不存在']);
            }
            $data = [
                'title' => $form['title'],
                'sort' => $form['sort'],
                'pid' => $category['id'],
                'path' => $category['path'] . $category['id'] . '-',
                'level' => $category['level'] + 1,
                'update_time' => time(),
            ];
            $cate = Db::name('category')->where('id', $form['id'])->update($data);
            if ($cate) {
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => 0, 'msg' => '操作失败']);
            }
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
            $attr = Db::name('attr')->where('cate_id', $id)->where('delete_time', 0)->count('id');
            if ($attr) {
                return json(['code' => 0, 'msg' => '当前分类下有属性存在，不能删除']);
            }
            $cate = Db::name('category')->where('pid', 'in', function ($query) use ($id) {
                $query->name('category')->where('id', $id)->where('delete_time',0)->field('pid');
            })->where('delete_time',0)->field('id,status,delete_time')->select();
            $num =0;
            foreach($cate as $v){
                if($v['status']==1){
                    $num++;
                }
            }
            if($num==1){
                foreach($cate as $v){
                    if($id==$v['id']&&$v['status']==1){
                        return json(['code'=>2,'msg'=>'当前只有被删除分类处于开启状态，请勿删除']);
                    }
                }
            }
            if (count($cate) == 1) {
                return json(['code' => 2, 'msg' => '分类下必须至少拥有一个分类']);
            }

            $cate = Db::name('category')->where('id', $id)->update(['delete_time' => time()]);
            if ($cate) {
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => 0, 'msg' => '操作失败']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request)
    {
        $id = $request->post('id');
        try {
            $status = Db::name('category')->where('id', $id)->value('status');
            if ($status == 0) {
                $cate = Db::name('category')->where('id', $id)->update(['status' => 1]);
                return json(['code' => 1, 'msg' => '开启']);
            } else {
                $cate = Db::name('category')->where('pid', 'in', function ($query) use ($id) {
                        $query->name('category')->where('id', $id)->where('delete_time',0)->field('pid');
                })->where('status', 1)->where('delete_time',0)->count('id');

                if ($cate == 1) {
                    return json(['code' => 2, 'msg' => '分类下必须至少拥有一个分类']);
                }
                $cate = Db::name('category')->where('id', $id)->update(['status' => 0]);
                return json(['code' => 0, 'msg' => '关闭']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
