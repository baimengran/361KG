<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\facade\Log;
use think\Request;

class Notification extends Controller
{

    /**
     * 消息数量
     * @return \think\response\Json
     */
    public function count()
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $notice_count = Db::name('user')->where('id', $user['id'])
            ->where('notification_count', 0)->value('notification_count');
        return json(['code' => 1, 'msg' => '查询成功', 'data' => $notice_count]);
    }

    /**
     * 消息列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }

        try {
            $notice = Db::name('notifications')->where('notifiable_id', $user['id'])
                ->where('notifiable_type', 'User')
                ->field('id,notifiable_id as issue_user_id,data,from_unixtime(create_time,\'%m-%d %H:%i\') as create_time,read_at')->paginate(20);

            $notice = $notice->each(function ($v) {
                $data = json_decode($v['data'], true);
                $v['comment_id'] = $data['reply_id'];
                $v['comment_content'] = subtext($data['reply_content'], 20);
//                $v['comment_user_id'] = $data['user_id'];
//                $v['comment_user_nickname'] = $data['user_name'];
//                $v['comment_user_avatar'] = $data['user_avatar'];
                $v['issue_link'] = $data['issue_link'];
                $v['issue_id'] = $data['issue_id'];
//                $v['issue_title']=$data['issue_title'];
                $v['issue_content'] = subtext($data['issue_content'], 50);
                $v['issue_praise_num'] = $data['issue_praise_num'];
//                $v['issue_browse_num']=$data['$issue_browse_num'];
//                $v['issue_review_num']=$data['issue_review_num'];
//                $v['issue_collect_num']=$data['issue_collect_num'];
                unset($v['data']);
                return $v;
            });
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $notice]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function show(Request $request)
    {
        $form = $request->post();
        if (!$form || !array_key_exists('token', $form) || !array_key_exists('issue_user_id', $form) ||
            !array_key_exists('issue_id', $form)||!array_key_exists('notice_id',$form)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        try{
        $issue = Db::name('issue')->where('id', $form['issue_id'])->find();
        if (!$issue) {
            return json(['code' => 0, 'msg' => '当前内容不存在']);
        }
        if ($issue['delete_time'] != 0) {
            $user = $this->dec_notification_count($form['issue_user_id'],$form['notice_id']);
            if (!$user) {
                return json(['code' => 0, 'msg' => '系统错误']);
            }
            return json(['code' => 0, 'msg' => '当前内容以删除']);
        }
        if ($issue['check_status'] == 2) {
            $user = $this->dec_notification_count($form['issue_user_id'],$form['notice_id']);
            if (!$user) {
                return json(['code' => 0, 'msg' => '系统错误']);
            }
            return json(['code' => 0, 'msg' => '当前内容涉嫌违规已被屏蔽']);
        }
        $issue = (new Issue())->show($request);
        if ($issue) {
            if ($issue->getData()['code'] == 0)
                return json($issue->getData());
            $user = $this->dec_notification_count($form['issue_user_id'],$form['notice_id']);
            if (!$user) {
                return json(['code' => 0, 'msg' => '系统错误']);
            }
            return json($issue->getData());
        }
        }catch (\Exception $e){
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function dec_notification_count($issue_user_id,$notice_id)
    {
        Db::startTrans();
        try {
            //操作用户通知数量
            $user = Db::name('user')->where('id', $issue_user_id)
                ->where('notification_count', '>', 0)->setDec('notification_count', 1);
            //修改消息为已阅
            //如果当前文章审核未通过或被删除，则软删消息
            $notification = Db::name('notifications')->where('id',$notice_id)->where('read_at',0)->update(['read_at'=>time()]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            Db::rollback();
            return false;
        }
    }
}
