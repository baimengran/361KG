<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class My extends Controller
{
    public function collect(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {
            $issue = Db::name('user_issue')->alias('ui')->join('issue u', 'ui.module_id=u.id')
                ->field('ui.id as user_issue_id,u.cate_id,ui.module_id,ui.module_type,u.id,u.title,from_unixtime(ui.create_time, \'%y-%m-%d %H:%i\') as create_time')
                ->where('ui.user_id', $user['id'])->where('ui.module_type', 'collect')->where('ui.delete_time', 0)
                ->where('u.delete_time', 0)->order('ui.create_time desc')->paginate(20);
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }


    public function review(){
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try{
            $issue = Db::name('user_issue')->alias('ui')->join('comment u', 'ui.module_id=u.id')
                ->join('issue i','i.id=u.issue_id')
                ->field('ui.id as user_issue_id,ui.module_id,ui.module_type,u.id,i.id as issue_id,i.cate_id,i.title,u.content,from_unixtime(ui.create_time, \'%y-%m-%d %H:%i\') as create_time')
                ->where('ui.user_id', $user['id'])->where('ui.module_type', 'review')->where('ui.delete_time', 0)
                ->where('u.status','neq',2)
                ->where('u.delete_time', 0)->order('ui.create_time desc')->paginate(20);
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>'系统错误']);
        }
    }


    public function browse(){
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {
            $issue = Db::name('user_issue')->alias('ui')->join('issue u', 'ui.module_id=u.id')
                ->field('ui.id as user_issue_id,u.cate_id,ui.module_id,ui.module_type,u.id,u.title,from_unixtime(ui.create_time, \'%y-%m-%d %H:%i\') as create_time')
                ->where('ui.user_id', $user['id'])->where('ui.module_type', 'browse')->where('ui.delete_time', 0)
                ->where('u.delete_time', 0)->order('ui.create_time desc')->paginate(20);
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
