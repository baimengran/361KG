<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/11
 * Time: 15:32
 */

use think\facade\Env;

return[
    'templateCode'=>Env::get('TEMPLATECODE'),
    'signName'=>Env::get('SIGNNAME'),
    'accessKey'=>Env::get('ALIYUNSMSACCESSKEY'),
    'accessSecret'=>Env::get('ALIYUNSMSACCESSSECRET'),
];