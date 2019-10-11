<?php

namespace app\api\controller;

use app\common\WechatOline;
use think\Controller;
use think\facade\Cache;
use think\facade\Log;
use think\Request;

class Uploads extends Controller
{
    public function upload_one()
    {

    }

    public function upload_more(Request $request)
    {
        try {
            // 获取表单上传文件
            $files = $request->file('pic');
            $img_path = $request->post('path');
            if($img_path==''){
                $img_path='issue';
            }
            if (!$files) {
                return json(['code' => 0, 'msg' => '请上传图片']);
            }
            $path = [];
//            foreach ($files as $file) {
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $files->move('static/uploads/tmp');
                if ($info) {
                    $path[] = [
                        'saveName' => 'static/uploads/tmp/' . str_replace("\\", "/", $info->getSaveName()),
                        'fileName' => $info->getFileName(),
                    ];
                } else {
                    // 上传失败获取错误信息
                    return json(['code' => 0, 'msg' => $files->getError()]);
                }
//            }

            $do = true;
//        while ($do) {
            //获取accessToken
            if (!Cache::has('AccessToken')) {
                (new Base())->accessToken();
            }

            $accessToken = Cache::get('AccessToken');
            $tmpPath = [];
            foreach ($path as $k => $v) {
                $obj = new \CURLFile(realpath($v['saveName']));
                $obj->setMimeType("image/jpeg");
                $media['media'] = $obj;
                //微信安全接口
                $output = (new WechatOline())->imgSecCheck($accessToken, $media);

                if ($output['errcode'] == 87014) {
                    return json(['code' => 0, 'msg' => '您上传的图片含有违规内容', 'data' => $v]);
                } else if ($output['errcode'] != 0) {
                    Log::error('errcode_' . $output['errmsg']);
                    Cache::rm('AccessToken');
                } else if ($output['errcode'] == 0) {
                    $tmpPath[] = $v;
                    $do = false;
                }
            }
            $newPath = "static/uploads/$img_path/" . date('Ymd', time());
            if (!file_exists($newPath)) {
                mkdir("$newPath", 0777, true);
            }
            $newPath2 = $newPath;
            $path_array=[];
            foreach ($tmpPath as $v) {
                $newPath = $newPath2;
                $newPath = $newPath . '/' . $v['fileName'];
                copy($v['saveName'], $newPath);
                $path_array='/'.$newPath;
                unlink($v['saveName']);
            }
//        }
            return json(['code' => 1, 'msg' => '上传成功', 'data' => $path_array]);
        }catch (\Exception $e){
            $data=['code'=>$e->getCode(),'line'=>$e->getLine(),'file'=>$e->getFile(),'message'=>$e->getMessage()];
            Log::error(json_encode($data,256));
            return json(['code'=>0,'msg'=>'系统错误']);
        }
    }
}
