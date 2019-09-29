<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/7
 * Time: 17:46
 */

namespace app\common;


class GenerateNo
{
    public function generateNo($prefix){
        return $prefix . (strtotime(date('YmdHis', time()))) . substr(microtime(), 2, 6) . sprintf('%03d', rand(0, 999));
    }
}