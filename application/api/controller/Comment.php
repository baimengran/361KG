<?php

namespace app\api\controller;

use app\common\WechatOline;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Log;
use think\Request;
use app\api\validate\Comment as CommentValidate;

class Comment extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $id = $request->post('issue_id');
        $user = (new Base())->getUser();
        if (!$id) {
            return json(['code' > 0, 'msg' => '请选择正确内容查看评论']);
        }
        try {
            $comment = Db::name('comment')->alias('c')->join('user u', 'c.user_id=u.id')
                ->where('status', 'neq', 2)->where('delete_time', 0)
                ->where('issue_id', $id)
                ->field('c.id,c.content,from_unixtime(c.create_time, \'%y-%m-%d %H:%i\') as create_time,
        u.id as user_id,u.nickname,u.avatar')->order('c.create_time asc')->paginate(20);

            $comment = $comment->each(function ($v, $k) use ($user) {
                if ($user) {
                    if ($user['id'] == $v['user_id']) {
                        $v['delete'] = 1;
                    } else {
                        $v['delete'] = 0;
                    }
                } else {
                    $v['delete'] = 0;
                }
                return $v;
            });

            return json(['code' => 1, 'msg' => '查询成功', 'data' => $comment]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
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
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $form = $request->post();
        $do = true;
        while ($do) {
            //微信安全接口
            //获取accessToken
            if (!Cache::has('AccessToken')) {
                (new Base())->accessToken();
            }

            $accessToken = Cache::get('AccessToken');

            $output = (new WechatOline())->msgSecCheck($accessToken, $form['content']);

            if ($output['errcode'] == 87014) {
                return json(['code' => 0, 'msg' => '您输入的内容含有违规内容']);
            } else if ($output['errcode'] != 0) {
                Log::error('errcode_' . $output['errmsg']);
                Cache::rm('AccessToken');
            } else if ($output['errcode'] == 0) {
                $do = false;
            }
        }
        $validate = new CommentValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        Db::startTrans();
        try {
            $comment = Db::name('comment')->insertGetId([
                'user_id' => $user['id'],
                'issue_id' => $form['issue_id'],
                'content' => $form['content'],
                'create_time' => time(),
                'update_time' => time(),
            ]);
            if ($comment) {
                Db::name('issue')->where('id', $form['issue_id'])->setInc('review_num');
                $user_issue = Db::name('user_issue')->insertGetId([
                    'user_id' => $user['id'],
                    'module_id' => $comment,
                    'module_type' => 'review',
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
                Db::commit();
                return json(['code' => 1, 'msg' => '评论成功']);
            }
            Db::rollback();
            return json(['code' => 0, 'msg' => '评论失败']);
        } catch (\Exception $e) {
            Db::rollback();
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
    public function delete(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $comment_id = $request->post('id');
        if (!$comment_id) {
            return json(['code' => 0, 'msg' => '请选择要删除的评论再操作']);
        }
        Db::startTrans();
        try {
            $comment = Db::name('comment')->where('id', $comment_id)->find();
            if (!$comment) {
                return json(['code' => 0, 'msg' => '要删除的评论不存在']);
            }
            $issue = Db::name('issue')->where('id', $comment['issue_id'])
                ->where('review_num', '>', 0)->setDec('review_num');
            $user_issue = Db::name('user_issue')->where('user_id', $user['id'])
                ->where('module_id', $comment['id'])->where('module_type', 'review')->find();
            $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->delete();
            $comment = Db::name('comment')->where('id', $comment_id)->delete();
            Db::commit();
            return json(['code' => 1, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
