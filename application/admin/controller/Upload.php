<?php

namespace app\admin\controller;
use think\Controller;
use think\File;
use think\Log;
use think\Request;

class Upload
{
	//图片上传
    public function upload(){
       $file = request()->file('file');
       $info = $file->move('static/uploads/');
       if($info){
            return json('/static/uploads/'.str_replace("\\","/",$info->getSaveName()));
        }else{
            return json($file->getError());
        }
    }

    //会员头像上传
    public function uploadface(){
       $file = request()->file('file');
       $info = $file->move('/static/uploads/');
       if($info){
            return json( 'static/uploads/face/'.$info->getSaveName());
        }else{
            return json($file->getError());
        }
    }

    public function uploadAll(){
        $files = request()->file('image');
        dump($files);die;
        foreach($files as $file){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move( '../uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

}