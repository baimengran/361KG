<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Request;


class Admin extends Base
{


    public function edit()
    {
        $id = Request::param('id');
        if (!$id) {
            return view('error/500');
        }
        try {
            $admin = Db::name('admin')->where('id', $id)->find();
            $this->assign('user', $admin);
            return $this->fetch('edit');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function update()
    {
        $form = Request::post();
        unset($form['file']);
        if ($form['password'] == null) {
            unset($form['password']);
        }else {
            $form['password'] = md5($form['password']);
        }
        $admin = Db::name('admin')->update($form);
        return json(['code' => 1, 'msg' => '修改成功']);
    }
}
