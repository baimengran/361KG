<?php

namespace app\api\controller;

use app\common\WechatOline;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\facade\Log;
use think\Request;

class Base extends Controller
{
    public function getUser()
    {
        $token = \think\facade\Request::post('token');
        if (!$token) {
            return false;
        }
        try {
            $user = Db::name('user')->where('token',$token)->find();
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function accessToken()
    {
        $output = (new WechatOline())->getAccessToken();

        if (array_key_exists('errcode', $output)) {
                Log::error('获取token失败:' . $output['errmsg']);
                exit;
        }
        Cache::set('AccessToken', $output['access_token'],7000);
    }
}
