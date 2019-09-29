<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Comment extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $key = $this->request->post('key');
        try {
            $comment = Db::name('comment')->alias('c')->join('user u', 'c.user_id=u.id')
                ->where('delete_time', 0)
                ->field('c.id,c.user_id,c.content,c.delete_time,c.status,u.id as c_user_id,u.nickname,c.create_time');
            if ($key) {
                $comment = $comment->where('content', 'like', '%' . $key . '%');
            }
            $comment = $comment->order('c.status asc,c.create_time desc')->paginate(20);

            $this->assign('val', $key);
            $this->assign('data', $comment);
            return $this->fetch('comment/index');
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
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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
        //
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
            $comment = Db::name('comment')->where('id', $id)->update(['delete_time' => time()]);
            if ($comment) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request)
    {
        $id = $request->get('id');
        $cate = $request->get('cate');
        if ($id == '' || $cate == '') {
            return json(['code' => 0, 'msg' => '请选择正确评论内容操作']);
        }
        try {
            $comment = Db::name('comment')->where('id', $id)->find();
            if ($comment) {
                if ($cate == 1) {
                    $comment = Db::name('comment')->where('id', $id)->update(['status' => 1]);
                } else {
                    $comment = Db::name('comment')->where('id', $id)->update(['status' => 2]);
                }
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => 0, 'msg' => '操作评论不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
