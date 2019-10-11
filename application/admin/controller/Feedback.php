<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Feedback extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $val = $this->request->post('key');
        try {
            $feedback = Db::name('feedback')->alias('f')->join('user u', 'u.id=f.user_id');
            if ($val) {
                $feedback = $feedback->where('content', 'like', '%' . $val . '%');
            }
            $feedback = $feedback->field('f.id,f.user_id,f.content,f.pic,f.create_time,u.id as u_id,u.nickname')
                ->order('create_time desc')->paginate(20);
            $feedback = $feedback->each(function ($v, $k) {
                $v['pic'] = explode(',', $v['pic']);
                return $v;
            });
            $this->assign('val', $val);
            $this->assign('data', $feedback);
            return $this->fetch('feedback/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function show(){
        $id = \think\facade\Request::param('id');
        try{
            $feedback = Db::name('feedback')->alias('f')->join('user u','u.id=f.user_id')
                ->field('f.id,f.content,f.pic,f.create_time,u.nickname,u.avatar')
                ->where('f.id',$id)->find();
            if($feedback['pic']!=null) {
                $feedback['pic'] = explode(',', $feedback['pic']);
            }
            $this->assign('data',$feedback);
            return $this->fetch('feedback/show');
        }catch (\Exception $e){
            return $this->fetch('error/500');
        }
    }
}
