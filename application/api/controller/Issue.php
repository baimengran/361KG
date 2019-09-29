<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Issue extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $condition = $request->post('cate_id');
        $field = '';
        if ($condition) {
            $field = 'cate_id';
            $issue = (new Index())->getIssue($field, $condition);
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        } else {
            return json(['code' => 0, 'msg' => '请选择栏目']);
        }
    }

    public function save(Request $request){
        $user = (new Base())->getUser();
        if(!$user){
            return json(['code'=>0,'msg'=>'请登录后重试']);
        }
        $form = $request->post();

    }


    public function show(Request $request)
    {
        $issue_id = $request->post('id');
        $cate_id = $request->post('cate_id');
        Db::startTrans();
        try {
            $ad = Db::name('ad')->where('delete_time', 0)->where('status', 1)
                ->where('type', $cate_id)->field('id,pic')->select();
            if ($issue_id) {
                $issue = (new Index())->getIssue('', '', 'id', $issue_id, 'find');
                $user = (new Base())->getUser();
                if ($issue) {
                    if ($user) {
                        $user_issue = Db::name('user_issue')->where('user_id', $user['id'])
                            ->where('module_id', $issue['id'])->where('module_type', 'browse')->find();
                        if (!$user_issue) {
                            $user_issue = Db::name('user_issue')->insertGetId([
                                'user_id' => $user['id'],
                                'module_id' => $issue['id'],
                                'module_type' => 'browse',
                                'create_time' => time(),
                                'update_time' => time(),
                            ]);
                        }
                    }
                } else {
                    return json(['code' => 0, 'msg' => '当前内容不存在']);
                }
                Db::name('issue')->where('id', $issue_id)->setInc('browse_num', 1);
                Db::commit();
                return json(['code' => 1, 'msg' => '查询成功', 'data' => [$issue, $ad]]);
            } else {
                Db::rollback();
                return json(['code' => 0, 'msg' => '请选择正确内容查看']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }


    public function collect(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 0, 'msg' => '请选择正确内容收藏']);
        }
        Db::startTrans();
        try {
            $issue = Db::name('issue')->where('id', $id)->where('status', 1)->where('delete_time', 0)->find();
            if (!$issue) {
                return json(['code' => 0, 'msg' => '当前收藏内容不存在']);
            }
            $user_issue = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id', $issue['id'])->where('module_type', 'collect')
                ->find();
            if ($user_issue) {
                if ($user_issue['delete_time'] == 0) {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => time()]);
                    $issue = Db::name('issue')->where('id', $id)->where('collect_num', '>', 0)->setDec('collect_num', 1);
                    Db::commit();
                    return json(['code' => 1, 'msg' => '已取消收藏']);
                } else {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => 0]);
                    $issue = Db::name('issue')->where('id', $id)->where('collect_num', 0)->setInc('collect_num', 1);
                    Db::commit();
                    return json(['code' => 1, 'msg' => '收藏成功']);
                }

            } else {
                $user_issue = Db::name('user_issue')->insertGetId([
                    'user_id' => $user['id'],
                    'module_id' => $issue['id'],
                    'module_type' => 'collect',
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
                $issue = Db::name('issue')->where('id', $id)->setInc('collect_num');
                Db::commit();
                if ($user_issue) {
                    return json(['code' => 1, 'msg' => '收藏成功']);
                }
                Db::rollback();
                return json(['code' => 0, 'msg' => '收藏失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }
}
