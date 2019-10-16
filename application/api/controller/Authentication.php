<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\facade\Log;
use think\Request;
use app\api\validate\Authentication as AuthenticationValidate;

class Authentication extends Controller
{
    public function person_company(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {

        $form = $request->post();
        if (!array_key_exists('type',$form)||$form['type']=='') {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $scene = '';
        if ($form['type'] == 1) {
            $scene = 'person';
        } else {
            $scene = 'company';
        }
        $validate = new AuthenticationValidate();

        if (!$validate->scene($scene)->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        $data = [];
        if ($form['type'] == 1) {
            //查询用户是否认证
            if($user['person_status']!=-1){
                return json(['code'=>0,'msg'=>'您以申请认证或认证以通过，请勿重复申请']);
            }
            $data = [
                'person_auth_name' => $form['person_auth_name'],
                'person_auth_pic_front' => $form['person_auth_pic_front'],
                'person_auth_pic_rear' => $form['person_auth_pic_rear'],
                'person_auth_id_card' => $form['person_auth_id_card'],
                'person_auth_time' => time(),
                'person_status'=>0,
            ];
        } else {
            //查询用户是否认证
            if($user['company_status']!=-1){
                return json(['code'=>0,'msg'=>'您以申请认证或认证以通过，请勿重复申请']);
            }
            $data = [
                'company_auth_name' => $form['company_auth_name'],
                'company_auth_pic' => $form['company_auth_pic'],
                'company_auth_time' => time(),
                'company_status'=>0
            ];
        }

            $user = Db::name('user')->where('id',$user['id'])->update($data);
            if ($user) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            $data=['code'=>$e->getCode(),'line'=>$e->getLine(),'file'=>$e->getFile(),'message'=>$e->getMessage()];
            Log::error(json_encode($data,256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }

    }
}
