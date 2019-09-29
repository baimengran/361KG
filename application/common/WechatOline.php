<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/11
 * Time: 1:11
 */

namespace app\common;


class WechatOline
{
    public function getAccessToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . config('wechat.app_id') . '&secret=' . \config('wechat.app_secret');
       return $access_token = json_decode($this->http_url($url),true);
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @param $openid
     * @return mixed
     */
    public function userInfo($access_token,$openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
       return $user_info = json_decode($this->http_url($url), true);
    }

    /**
     * 小程序内容安全
     * @param $accessToken
     * @param $content
     * @return mixed
     */
    public function msgSecCheck($accessToken,$content){
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token='.$accessToken;
        $content = json_encode(['content'=>$content],JSON_UNESCAPED_UNICODE);
        return $output = json_decode($this->http_url($url,$content),true);
    }

    function http_url($url, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "error:" . curl_error($ch);
            exit;
        }
        curl_close($ch);
        return $res;

    }
}