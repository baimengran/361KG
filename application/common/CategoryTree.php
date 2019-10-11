<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 16:40
 */

namespace app\common;


class CategoryTree
{
    public function getChildren($datas,$attr, $pid=0,$level = 0)
    {
        static $data = array();
        foreach ($datas as $k => $v) {
            if ($v['pid'] == $pid) {
                if($pid!=0){
                    foreach($data as $i=>$va) {
                        if ($va['id'] == $v['pid']) {
                            $data[$i]['cate'][] = $v;
                        }
                    }
                }else {
                    $data[] = $v;
                }
                unset($datas[$k]);
                $this->getChildren($datas,$attr, $v['id'], $level);
            }

        }
        return $data;
    }
}