<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;

class Config extends Base
{
    const WEB_SITE_PATH = CONFIG_PATH . 'web_site.json';

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $data = json_decode(file_get_contents(self::WEB_SITE_PATH),true);
//        dump($data);die;
        $this->assign('site', $data);
        return $this->fetch();
    }

    public function save()
    {
        $form = \think\facade\Request::post();
        $rule = [
            'title' => 'require|max:10',
            'logo' => 'require',
            'name' => 'require|max:50',
            'introduce' => 'require|max:100',
            'contacts'=>"require|max:10",
            'contact_phone'=>'require|regex:/^(1[3584]\d{9})$/',
            'contact_desc'=>'require|max:30'
        ];
        $message = [
            'title.require' => '网站标题必须填写',
            'title.max'=>'网站标题不能超过10个字',
            'logo.require'=>'网站LOGO必须上传',
            'name.require'=>'网站名称必须填写',
            'name.max'=>'网站名称不能超过50个字',
            'introduce.require'=>'网站简介必须填写',
            'introduce.max'=>'网站简介不能超过100个字',
            'contacts.require'=>'联系人必须填写',
            'contacts.max'=>'联系人不能超过10个字',
            'contact_phone.require'=>'联系电话必须填写',
            'contact_phone.regex'=>'联系电话填写错误',
            'contact_desc.require'=>'联系说明必须填写',
            'contact_desc.max'=>'联系说明不能超过30个字'
        ];
        $validate = new Validate($rule, $message);
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }

        $data = json_encode($form);
        $status = file_put_contents(self::WEB_SITE_PATH, $data);
        if ($status) {
            return json(['code' => 1, 'msg' => '设置成功']);
        } else {
            return json(['code' => 0, 'msg' => '设置失败']);
        }
    }
}
