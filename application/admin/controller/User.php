<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class User extends Controller
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
            $user = Db::name('user');
            if ($val) {
                $user = $user->where('nickname', 'like', '%' . $val . '%');
            }
            $user = $user->order('create_time desc')->paginate(20);
            $this->assign('val', $val);
            $this->assign('data', $user);
            return $this->fetch('user/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function person_status()
    {
        $id = $this->request->param('id');
        if (!$id) {
            return $this->fetch('error/500');
        }
        try {
            $user = Db::name('user')->where('id', $id)
                ->field('id,person_auth_name,person_auth_pic_front,person_auth_pic_rear,person_auth_id_card,person_status,person_reason')
                ->find();
            if (!$user) {
                return '<div><p>未找到用户</p></div>';
            }
            $this->assign('person', $user);
            return $this->fetch('user/auth');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function person_status_update()
    {
        $form = $this->request->post();

        try {
            if (array_key_exists('person_status', $form)) {
                if ($form['person_status'] == 'on') {
                    $user = Db::name('user')->where('id', $form['id'])->update(['person_status' => 1, 'person_auth_time' => time()]);
                    if ($user) {
                        return json(['code' => 1, 'msg' => '操作成功']);
                    } else {
                        return json(['code' => 0, 'msg' => '操作失败']);
                    }
                } else {
                    return json(['code' => 0, 'msg' => '操作失败']);
                }
            } else {
                if (array_key_exists('person_reason', $form)) {
                    if ($form['person_reason'] == '') {
                        return json(['code' => 0, 'msg' => '请填写原因']);
                    } else {
                        if (strlen($form['person_reason']) > 150) {
                            return json(['code' => 0, 'msg' => '不通过原因字数过多，请精简字数']);
                        }

                        $user = Db::name('user')->where('id', $form['id'])
                            ->update(['person_status' => 2, 'person_reason' => $form['person_reason'],'person_auth_time'=>time()]);
                        if ($user) {
                            return json(['code' => 1, 'msg' => '操作成功']);
                        }
                        return json(['code' => 0, 'msg' => '操作失败']);
                    }
                } else {
                    return json(['code' => 0, 'msg' => '操作失败']);
                }
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function company_status()
    {
        $id = $this->request->param('id');
        if (!$id) {
            return $this->fetch('error/500');
        }
        try {
            $user = Db::name('user')->where('id', $id)
                ->field('id,company_auth_name,company_auth_pic,company_status,company_reason,company_auth_time')->find();
            if (!$user) {
                return '<div><p>当前用户不存在</p></div>';
            }
            $this->assign('company', $user);
            return $this->fetch('user/auth');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    public function company_status_update(){
        $form = $this->request->post();
        try {
            if (array_key_exists('company_status', $form)) {
                if ($form['company_status'] == 'on') {
                    $user = Db::name('user')->where('id', $form['id'])->update(['company_status' => 1, 'company_auth_time' => time()]);
                    if ($user) {
                        return json(['code' => 1, 'msg' => '操作成功']);
                    } else {
                        return json(['code' => 0, 'msg' => '操作失败']);
                    }
                } else {
                    return json(['code' => 0, 'msg' => '操作失败']);
                }
            } else {
                if (array_key_exists('company_reason', $form)) {
                    if ($form['company_reason'] == '') {
                        return json(['code' => 0, 'msg' => '请填写原因']);
                    } else {
                        if (strlen($form['company_reason']) > 150) {
                            return json(['code' => 0, 'msg' => '不通过原因字数过多，请精简字数']);
                        }

                        $user = Db::name('user')->where('id', $form['id'])
                            ->update(['company_status' => 2, 'company_reason' => $form['company_reason'],'company_auth_time'=>time()]);
                        if ($user) {
                            return json(['code' => 1, 'msg' => '操作成功']);
                        }
                        return json(['code' => 0, 'msg' => '操作失败']);
                    }
                } else {
                    return json(['code' => 0, 'msg' => '操作失败']);
                }
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
