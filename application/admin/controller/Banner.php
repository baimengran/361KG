<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\Ad as AdModel;
use app\admin\validate\Ad as AdValidate;

class Banner extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $id = $this->request->get('id');

            $ad = Db::name('ad')->where('delete_time', 0);
//            if ($id != '') {
//                $ad = $ad->where('type', $id);
//            }
            $ad = $ad->field('id,pic,title,status,type')->order('status desc,create_time desc')->paginate(20);

//            $category = Db::name('category')->where('delete_time', 0)->where('status', 1)
//                ->where('pid', 0)->select();

//            $ad = $ad->each(function ($v, $k) use ($category) {
//
//                foreach ($category as $i => $cate) {
//                    if ($v['type'] == 0) {
//                        $v['name'] = '首页';
//                    }
//                    if ($v['type'] == $cate['id']) {
//                        $v['name'] = $cate['title'];
//                    }
//                }
//                return $v;
//            });
//            foreach ($ad as $k => $v) {
//                if (!array_key_exists('name', $v)) {
//                    unset($ad[$k]);
//                }
//            }

            $this->assign('id', $id);
//            $this->assign('category', $category);
            $this->assign('data', $ad);
            return $this->fetch('ad/index');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        try {
//            $category = Db::name('category')->where('delete_time', 0)->where('status', 1)
//                ->where('pid', 0)->select();
//            $this->assign('category', $category);
            return $this->fetch('ad/add_edit');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $form = $request->post();
        unset($form['id']);
        unset($form['file']);
        $form['status']=0;
        $validate = new AdValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        $form['type']=0;
        try {
            $ad = (new AdModel())->save($form);
            if ($ad) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        try {
//            $category = Db::name('category')->where('delete_time', 0)->where('status', 1)
//                ->where('pid', 0)->select();
//            $this->assign('category', $category);

            $ad = Db::name('ad')->where('delete_time', 0)->where('id', $id)->find();
            $this->assign('data', $ad);
            return $this->fetch('ad/add_edit');
        } catch (\Exception $e) {
            return $this->fetch('error/500');
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $form = $request->post();
        $validate = new AdValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            unset($form['file']);
            $form['update_time'] = time();
            $ad = Db::name('ad')->update($form);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        try {
            $ad = AdModel::destroy($id);
            if ($ad) return json(['code' => 1, 'msg' => '操作成功']);
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $exception) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function status(Request $request)
    {
        $id = $request->post('id');
        try {
            $cate = Db::name('ad')->where('id', $id)->find();
            if ($cate['status'] == 1) {
                Db::name('ad')->where('id', $id)->update(['status' => 0]);
                return json(['code' => 0, 'msg' => '关闭']);
            } else {
                if($cate['type']==0){
                    $ad_count = Db::name('ad')->where('type',0)->where('delete_time',0)
                        ->where('status',1)->count('id');
                    if($ad_count<3){
                        Db::name('ad')->where('id', $id)->update(['status' => 1]);
                        return json(['code' => 1, 'msg' => '开启']);
                    }else{
                        return json(['code' => 2, 'msg' => '首页最多投放三条广告，请关闭其他广告后开启']);
                    }
                }else{
                    $ad_count = Db::name('ad')->where('type',$cate['type'])->where('delete_time',0)
                        ->where('status',1)->count('id');
                    if($ad_count<1){
                        Db::name('ad')->where('id', $id)->update(['status' => 1]);
                        return json(['code' => 1, 'msg' => '开启']);
                    }else{
                        return json(['code' => 2, 'msg' => '当前按栏目最多投放一条广告，请关闭其他广告后开启']);
                    }
                }
            }
        } catch (\Exception $e) {
            return json(['code' => -1, 'msg' => '系统错误']);
        }
    }
}
