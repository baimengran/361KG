<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\Validate;

class Login extends Controller
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->assign('verify_type', config('verify_type'));
        return $this->fetch('login/login');
    }

    public function doLogin()
    {
        $form = Request::only('username,password');

        if (config('verify_type') == 1) {
            $code = input("param.code");
        }

        $rule = [
            'username' => 'require',
            'password' => 'require'
        ];
        $message = [
            'username.require' => '用户名不能为空',
            'password.require' => '密码不能为空'
        ];
        $validate = new Validate($rule, $message);
        if (!$validate->check($form)) {
            return json(['code' => -5, 'url' => '', 'msg' => $validate->getError()]);
        }

        try {
            $hasUser = Db::name('admin')->where('username', $form['username'])->find();
            if (empty($hasUser)) {
                return json(['code' => -1, 'url' => '', 'msg' => '管理员不存在']);
            }

            if (md5($form['password']) != $hasUser['password']) {
                return json(['code' => -2, 'url' => '', 'msg' => '账号或密码错误']);
            }

            if (1 != $hasUser['status']) {
                return json(['code' => -6, 'url' => '', 'msg' => '该账号被禁用']);
            }

            session('uid', $hasUser['id']);         //用户ID
            session('username', $hasUser['username']);  //用户名
            session('portrait', $hasUser['portrait']); //用户头像
//            session('rolename', $info['title']);    //角色名
//            session('rule', $info['rules']);        //角色节点
//            session('name', $info['name']);         //角色权限

            //更新管理员状态
            $param = [
                'loginnum' => $hasUser['loginnum'] + 1,
                'last_login_ip' => request()->ip(),
                'last_login_time' => time(),
                'token' => md5($hasUser['username'] . $hasUser['password'])
            ];

            Db::name('admin')->where('id', $hasUser['id'])->update($param);
            Request::session('admin');
            return json(['code' => 1, 'url' => url('index/index'), 'msg' => '登录成功！']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }


    /**
     * 验证码
     * @return
     */
    public function checkVerify()
    {
        dump(Request::only('code'));die;
        $verify = new Verify();
        $verify->imageH = 32;
        $verify->imageW = 100;
        $verify->codeSet = '0123456789';
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 14;
        return $verify->entry();
    }


    /**
     * 退出登录
     * @return
     */
    public function loginOut()
    {
        Session::clear();
        $this->redirect('login/index');
    }
}
