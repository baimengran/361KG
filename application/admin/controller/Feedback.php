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
        try{
            $feedback = Db::name('feedback')->alias('f')->join('user u','u.id=f.user_id');
            if($val){
                $feedback = $feedback->where('content','like','%'.$val.'%');
            }
            $feedback = $feedback->field('f.id,f.user_id,f.content,f.pic,f.create_time,u.id as u_id,u.nickname')
                ->order('create_time desc')->paginate(20);
            $this->assign('val',$val);
            $this->assign('data',$feedback);
            return $this->fetch('feedback/index');
        }catch (\Exception $e){
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
