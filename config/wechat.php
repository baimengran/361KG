<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/24
 * Time: 14:24
 */


use think\facade\Env;

return [
    'app_id'=>Env::get('APPID'),
    'app_secret'=>Env::get('APPSECRET')
];