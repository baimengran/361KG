<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/11
 * Time: 14:11
 */

namespace app\common;


class Redis extends \Redis
{
    public static function redis()
    {
        $con = new \Redis();
        $con->connect(config('redis.host'), config('redis.port'), 5);
        return $con;
    }
}