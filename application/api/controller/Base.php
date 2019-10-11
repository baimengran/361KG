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
    const WEB_SITE_PATH = CONFIG_PATH . 'web_site.json';

    public function getUser()
    {
        $token = \think\facade\Request::post('token');
        if (!$token) {
            return false;
        }
        try {
            $user = Db::name('user')->where('token', $token)->find();
            return $user;
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return false;
        }
    }

    public function getSite()
    {
        try {
            $data = json_decode(file_get_contents(self::WEB_SITE_PATH), true);
            return $data;
        } catch (Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return 0;
        }
    }


    public function accessToken()
    {
        $output = (new WechatOline())->getAccessToken();

        if (array_key_exists('errcode', $output)) {
            Log::error('获取token失败:' . $output['errmsg']);
            exit;
        }
        Cache::set('AccessToken', $output['access_token'], 7000);
    }
}
