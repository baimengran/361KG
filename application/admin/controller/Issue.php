<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\ConAttrValue as ConAttrValueModel;
use app\admin\validate\Issue as IssueValidate;

class Issue extends Base
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

            $id = $this->request->get('id');

            $category = $cate = Db::name('category')->alias('c')->join('category ca', 'ca.id=c.pid')
                ->where('c.pid', 'neq', 0)->where('c.delete_time', 0)
                ->where('ca.pid', 0)->where('ca.delete_time', 0)
                ->field('c.id,c.title,ca.title as p_title')->order('c.pid asc')->select();

            $cate = Db::name('issue')->alias('is')->join('category c', 'c.id=is.cate_id')
                ->join('category ca', 'ca.id=c.pid')
                ->field('is.id,is.title,is.status,is.delete_time,is.create_time,is.cate_id,is.praise_num,is.browse_num
                ,is.review_num,is.collect_num,c.id as cate_id,c.title as name,ca.title as p_name,c.id as category_id,is.top,is.check_status,
                is.recommend')
                ->where('is.delete_time', 0);
            if ($key) {
                $cate = $cate->where('is.content', 'like', '%' . $key . '%');
            }

            if ($id) {
                $cate = $cate->where('cate_id', $id);
            }
            $param = \think\facade\Request::param();

            $cate = $cate->order('is.status asc,is.top desc,is.create_time desc')
                ->paginate(20, false, ['query' => $param]);

            $this->assign('id', $id);
            $this->assign('val', $key);
            $this->assign('data', $cate);
            $this->assign('category', $category);
            return $this->fetch('issue/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function show(Request $request)
    {
        $id = $request->param('id');
        try {
            $issue = Db::name('issue')->alias('i')->join('category c', 'c.id=i.cate_id')
                ->join('category ca', 'ca.id=c.pid')->join('user u', 'i.user_id=u.id')
                ->where('i.delete_time', 0)->where('i.id', $id)
                ->field('i.id,i.content,i.province,i.city,i.district,i.permission,i.valid_time,
                c.title as cate_title,ca.title as p_cate_title,i.praise_num,i.browse_num,i.review_num,i.collect_num,
                i.check_status,i.status,i.pic,i.recommend,from_unixtime(i.create_time,\'%Y-%m-%d %H:%i:%s\') as create_time,
                u.nickname,u.avatar')
                ->find();
            if ($issue) {
                if ($issue['pic'] != '') {
                    $issue['pic'] = explode(',', $issue['pic']);
                }
                if ($issue['permission'] == -1) {
                    $issue['permission'] = '不允许查看';
                } else if ($issue['permission'] == 0) {
                    $issue['permission'] = '任何人';
                } else if ($issue['permission'] == 1) {
                    $issue['permission'] = '个人认证';
                } else {
                    $issue['permission'] = '企业认证';
                }

            } else {
                return json(['code' => 0, 'msg' => '当前内容不存在']);
            }
            $this->assign('data', $issue);
            return $this->fetch('issue/show');

        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }


    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
//    public function create(Request $request)
//    {
//        if ($request->isAjax()) {
//            $cate_id = $request->get('id');
//            if (!$cate_id) {
//                return json(['code' => 0, 'msg' => '请选择栏目']);
//            }
//            $attr = Db::name('con_attr')->where('cate_id', $cate_id)->where('delete_time', 0)
//                ->where('status', 1)->order('sort asc')->all();
//            foreach ($attr as $k => $v) {
//                if ($v['type_value'] != null) {
//                    $attr[$k]['type_value'] = explode(',', $v['type_value']);
//                }
//            }
//            return json(['code' => 1, 'data' => $attr]);
//        }
//
//        $cate = Db::name('category')->where('delete_time', 0)->where('status',1)->where('pid', 0)->all();
//        $this->assign('cate', $cate);
//        return $this->fetch('issue/add_edit');
//    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
//    public function save(Request $request)
//    {
//        $form = $request->post();
////        dump($form);die;
//        $validate = new IssueValidate();
//        if (!$validate->check($form)) {
//            return json(['code' => 0, 'msg' => $validate->getError()]);
//        }
//
//        $data = [];
//        if (array_key_exists('file', $form)) {
//            unset($form['file']);
//        }
//        $user = session('uid');
//        if (!$user) {
//            return redirect('login/index');
//        }
//        $issue = ['title' => $form['title'], 'cate_id' => $form['cate_id'], 'user_id' => $user, 'phone' => $form['phone'],
//            'pic' => $form['pic'], 'create_time' => time(), 'update_time' => time(), 'description' => $form['description'], 'contact' => $form['contact']];
//        unset($form['id']);
//        unset($form['cate_id']);
//        unset($form['phone']);
//        unset($form['pic']);
//        unset($form['title']);
//        unset($form['description']);
//        unset($form['contact']);
//        Db::startTrans();
//        try {
//        $issue_id = Db::name('issue')->insertGetId($issue);
//
//        foreach ($form as $k => $v) {
//
//            $data[] = ['con_attr_id' => $k, 'value' => $v, 'issue_id' => $issue_id];
//        }
//        $con_attr_val = Db::name('con_attr_value')->insertAll($data);
//
//        Db::commit();
//        return json(['code' => 1, 'msg' => '操作成功']);
//
//        } catch (\Exception $e) {
//            Db::rollback();
//            return json(['code' => 0, 'msg' => '系统错误']);
//        }
//    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
//    public function edit($id)
//    {
//        $issue = Db::name('issue')->where('delete_time', 0)->where('id', $id)
//            ->field('id,title,top,phone,description,pic,contact,cate_id')->find();
//
//        $con = Db::name('con_attr_value')->alias('cav')->join('con_attr ca', 'ca.id=cav.con_attr_id')
//            ->field('cav.id as con_attr_value_as_id,cav.con_attr_id,cav.value,cav.issue_id,ca.id as con_attr_as_id,ca.name,ca.type,ca.type_value')
//            ->where('cav.issue_id', $issue['id'])->where('ca.delete_time', 0)->where('ca.status', 1)->select();
//        foreach ($con as $k => $v) {
//            //单选，多选
//            if ($v['type'] == 4 || $v['type'] == 5) {
//                $con[$k]['type_value'] = explode(',', $v['type_value']);
//            }
//            //图片
////            if($v['type']==2){
////                $con[$k]['value']=explode(',',$v['value']);
////            }
//        }
//        $issue['con'] = $con;
////        dump($issue);die;
//        $this->assign('data', $issue);
//        return $this->fetch('issue/add_edit');
//    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
//    public function update(Request $request, $id)
//    {
//        $form = $request->post();
//        $validate = new IssueValidate();
//        if (!$validate->scene('edit')->check($form)) {
//            return json(['code' => 0, 'msg' => $validate->getError()]);
//        }
////        dump($form);die;
//        Db::startTrans();
//        try {
//            $issue = ['id' => $form['id'], 'title' => $form['title'], 'update_time' => time(), 'pic' => $form['pic'],
//                'phone' => $form['phone'], 'description' => $form['description'], 'contact' => $form['contact']];
//            $data = [];
//            foreach ($form as $k => $v) {
//                if ($k != 'title' && $k != 'id' && $k != 'file' && $k != 'pic' && $k != 'phone' && $k != 'description' && $k != 'contact') {
//                    $data[] = ['id' => $k, 'value' => $v];
//                }
//            }
//
//            Db::name('issue')->update($issue);
//            $con_attr_value = (new ConAttrValueModel())->saveAll($data);
//            Db::commit();
//            return json(['code' => 1, 'msg' => '操作成功']);
//        } catch (\Exception $e) {
//            Db::rollback();
//            return json(['code' => 0, 'msg' => '系统错误']);
//        }
//    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        try {
            $issue = Db::name('issue')->where('id', $id)->find();
            if ($issue) {
                $issue = Db::name('issue')->where('id', $id)->update(['delete_time' => time()]);
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => 0, 'msg' => '当前删除内容不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

//    public function status(Request $request)
//    {
//        $id = $request->post('id');
//        try {
//            $cate = Db::name('issue')->where('id', $id)->value('status');
//            if ($cate == 1) {
//                Db::name('issue')->where('id', $id)->update(['status' => 0]);
//                return json(['code' => 0, 'msg' => '关闭']);
//            } else {
//                Db::name('issue')->where('id', $id)->update(['status' => 1]);
//                return json(['code' => 1, 'msg' => '开启']);
//            }
//        } catch (\Exception $e) {
//            return json(['code' => -1, 'msg' => '系统错误']);
//        }
//    }

    public function check_status(Request $request)
    {
        $id = $request->get('id');
        $cate = $request->get('cate');
        if ($id == '' || $cate == '') {
            return json(['code' => 0, 'msg' => '请选择正确内容操作']);
        }
        try {
            $comment = Db::name('issue')->where('id', $id)->find();
            if ($comment) {
                if ($cate == 1) {
                    $comment = Db::name('issue')->where('id', $id)->update(['check_status' => 1]);
                } else {
                    $comment = Db::name('issue')->where('id', $id)->update(['check_status' => 2]);
                }
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => 0, 'msg' => '操作评论不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function recommend(Request $request)
    {
        $id = $request->post('id');
        try {
            $issue = Db::name('issue')->where('id', $id)->value('recommend');
            if ($issue == 0) {
                $issue = Db::name('issue')->where('id', $id)->update(['recommend' => 1]);
                return json(['code' => 1, 'msg' => '已推荐']);
            } else {
                $issue = Db::name('issue')->where('id', $id)->update(['recommend' => 0]);
                return json(['code' => 0, 'msg' => '未推荐']);
            }
        } catch (\Exception $e) {
            return json(['code' => 2, 'msg' => '系统错误']);
        }
    }

    /**
     * 头条显示
     * @param Request $request
     * @return \think\response\Json
     */
//    public function top(Request $request)
//    {
//        $id = $request->post('id');
//        try {
//            $cate = Db::name('issue')->where('id', $id)->value('top');
//            if ($cate == 1) {
//                Db::name('issue')->where('id', $id)->update(['top' => 0]);
//                return json(['code' => 0, 'msg' => '关闭']);
//            } else {
//                Db::name('issue')->where('id', $id)->update(['top' => 1]);
//                return json(['code' => 1, 'msg' => '开启']);
//            }
//        } catch (\Exception $e) {
//            return json(['code' => -1, 'msg' => '系统错误']);
//        }
//    }
}
