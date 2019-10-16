<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/1
 * Time: 16:50
 */

namespace app\api\controller;


use think\Db;
use think\facade\Log;
use think\Request;
use think\Validate;
use think\Exception;

class Login
{
    protected $app_id;
    protected $app_secret;
    protected $login_url;


    public function __construct()
    {
        $this->app_id = config('wechat.app_id');
        $this->app_secret = config('wechat.app_secret');

        $this->login_url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $this->userinfo_url = config('wechat.userinfo_url');

    }

    public function login(Request $request)
    {

        $user_info = $request->post();
//        dump($user_info);die;
        $rule = [
            'nickname' => 'require',
            'avatar' => 'require',
        ];
        $msg = [
            'nickname' => '用户昵称不能为空',
            'avatar' => '用户头像不能为空'
        ];

        $validate = new Validate($rule, $msg);
        if (!$validate->check($user_info)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }

        if ($user_info['code'] == null) {
            return json(['code' => 0, 'msg' => '没有code'], 400);

        }
        $data = [
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
            'js_code' => $user_info['code'],
            'grant_type' => 'authorization_code',
        ];

        //初始化
        $cu = curl_init();
        //设置选项
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='
            . $this->app_id . '&secret=' . $this->app_secret . '&js_code=' . $user_info['code'] . '&grant_type=authorization_code';
        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($cu, CURLOPT_CONNECTTIMEOUT, 10);

        //执行并获取内容
        $output = curl_exec($cu);
        //释放句柄
        curl_close($cu);
        if (empty($output)) {
            return json(['code' => 0, 'msg' => '获取open_id失败，微信内部服务器错误'], 500);
        }
        $output = json_decode($output, true);


        if (array_key_exists('errcode', $output)) {
            return json([
                'code' => 0,
                'errcode' => $output['errcode'],
                'msg' => $output['errmsg'],
            ]);
        } else {
            return $this->getToken($output, $user_info);
        }
    }

    public function getToken($output, $user_info)
    {

        try {
            $user = Db::name('user')->where('openid', $output['openid'])->value('id');
            if (!$user) {
                unset($user_info['code']);
                $user_info = array_merge($user_info, $output);
                $charid = strtoupper(md5(uniqid(mt_rand(), true)));
                $user_info['token'] = substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
                unset($user_info['session_key']);
                $user_info['create_time'] = time();
                $user_info['update_time'] = time();
                //判断地理信息是否为空
                if  (($user_info['province'] == '' && $user_info['city'] == '' && $user_info['district']=='')||
                    ($user_info['province'] == 'undefined' && $user_info['city'] == 'undefined' && $user_info['district']=='undefined')) {
                    unset($user_info['district']);
                    unset($user_info['province']);
                    unset($user_info['city']);
                }
//                $user_info['province'] = $user_info['province'] ||$user_info['province'] == 'undefined'  ?? '';
//                $user_info['district'] = $user_info['district'] ||$user_info['district'] == 'undefined'  ?? '';
//                $user_info['city'] = $user_info['city'] ||$user_info['city'] == 'undefined'  ?? '';


                $user['id'] = Db::name('user')->insertGetId($user_info);
            } else {
                $data = [
                    'avatar'=>$user_info['avatar'],
                    'nickname'=>$user_info['nickname'],
                    'district'=>$user_info['district'],
                    'province'=>$user_info['province'],
                    'city'=>$user_info['city']
                ];


//                $user['province'] ? $user['province'] : unset($user_info['province']);
//                $user_info['province'] = $user_info['province'] ||$user_info['province'] == 'undefined'  ?? '';
//                $user_info['district'] = $user_info['district'] ||$user_info['district'] == 'undefined'  ?? '';
//                $user_info['city'] = $user_info['city'] ||$user_info['city'] == 'undefined'  ?? '';
                if  (($user_info['province'] == '' && $user_info['city'] == '' && $user_info['district']=='')||
                    ($user_info['province'] == 'undefined' && $user_info['city'] == 'undefined' && $user_info['district']=='undefined')) {
                    unset($data['district']);
                    unset($data['province']);
                    unset($data['city']);
                }
//                if ($user_info['province'] == 'undefined' && $user_info['city'] == 'undefined' && $user_info['district']=='undefined') {
//                    unset($data['district']);
//                    unset($data['province']);
//                    unset($data['city']);
//                }
                $user = Db::name('user')->where('openid', $output['openid'])
                    ->update($data);
            }
            //获取token
            $token = Db::name('user')->where('openid', $output['openid'])
                ->field('id,token,province,city,district,phone,phone_status,person_status,company_status,
                nickname,fans_num,attention_num,praise_num,avatar,notification_count')
                ->find();
            if ($token) {
                return json(['code' => 1, 'msg' => '登录成功', 'data' => $token]);
            } else {
                return json(['code' => 0, 'msg' => '登录失败']);
            }
        } catch (Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '内部错误'], 500);
        }
    }
}