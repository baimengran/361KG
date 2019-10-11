<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\Notice as NoticeValidate;
use think\Validate;
use app\admin\model\Notice as NoticeModel;

class Notice extends Base
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
            $comment = Db::name('notice')->where('delete_time', 0)
                ->field('id,content,status,delete_time,create_time,pic,sort');
            if ($key) {
                $comment = $comment->where('content', 'like', '%' . $key . '%');
            }
            $comment = $comment->order('status desc,create_time desc')->paginate(20);

            $this->assign('val', $key);
            $this->assign('data', $comment);
            return $this->fetch('notice/index');
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
            $cate = (new NoticeModel())->saveAll($data);
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
        return $this->fetch('notice/add_edit');
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
        $validate = new NoticeValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        $data = [
//            'content' => $form['content'],
            'pic' => $form['pic'],
            'status' => 0,
            'create_time' => time(),
            'update_time' => time()
        ];
        try {
            $notice = Db::name('notice')->insert($data);
            if ($notice) {
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
            $notice = Db::name('notice')->where('id', $id)->find();
            $this->assign('data', $notice);
            return $this->fetch('notice/add_edit');
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
        $validate = new NoticeValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        $data = [
            'id' => $form['id'],
//            'content' => $form['content'],
            'pic' => $form['pic'],
            'status' => 0,
            'update_time' => time()
        ];
        try {
            $notice = Db::name('notice')->update($data);
            if ($notice) {
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
            $comment = Db::name('notice')->where('id', $id)->update(['delete_time' => time()]);
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
        $id = $request->param('id');
        try {
            $notice = Db::name('notice')->where('id', $id)->find();
            $notice_num = Db::name('notice')->where('status', 1)->where('delete_time', 0)->count('id');
            if ($notice) {
                if ($notice['status'] == 0) {
//                    if ($notice_num) {
//                        return json(['code' => 2, 'msg' => '当前已有一条公告显示中，请关闭已显示公告后再开启']);
//                    }
                    $notice = Db::name('notice')->where('id', $id)->update(['status' => 1]);
                    return json(['code' => 1, 'msg' => '显示']);
                } else {
                    $notice = Db::name('notice')->where('id', $id)->update(['status' => 0]);
                    return json(['code' => 0, 'msg' => '关闭']);
                }
            } else {
                return json(['code' => 0, 'msg' => '操作评论不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
