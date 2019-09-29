<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $condition = $request->post('cate_id');
        $page = $request->post('page');
        $field = '';
        if ($condition) {
            $field = 'cate_id';
        }
        try {
            $issue = $this->getIssue($field, $condition);

            if ($page != '') {
                return json(['code' => 1, 'msg' => '查询成功', 'issue' => $issue]);
            }

            //广告
            $ad = Db::name('ad')->where('delete_time', 0)->where('status', 1)
                ->where('type', 0)->field('id,pic')->select();

            //分类
            $cate = Db::name('category')->where('delete_time', 0)->where('status', 1)
                ->where('pid', 0)->field('id,title,icon')->select();

            $data['ad'] = $ad;
            $data['cate'] = $cate;

            return json(['code' => 1, 'msg' => '查询成功', 'issue' => $issue, 'data'=>$data]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }


    public function getIssue($field=null, $condition=null,$field_id=null,$issue_id=null,$mode=null)
    {
        try {
            //列表
            $issue = Db::name('issue')->where('delete_time', 0)->where('status', 1)
                ->where('check_status', 1)->order('create_time desc')->where($field, $condition)
                ->where($field_id,$issue_id)
                ->field('id,cate_id,title,sticky_status,browse_num,review_num,collect_num,phone,
                contact,description,pic,from_unixtime(create_time, \'%y-%m-%d %H:%i\') as create_time');

            if($mode=='find'){
                $issue = $issue->find();
                $issue_ids = $issue['id'];
            }else{
                $issue = $issue->paginate(10);
                $issue_ids = [];
                foreach ($issue as $v) {
                    $issue_ids[] = $v['id'];
                }
            }

            //查询属性和属性值
            $con_attr_value = Db::name('con_attr_value')->alias('cav')
                ->join('con_attr ca', 'ca.id=cav.con_attr_id')
                ->where('cav.issue_id', 'in', $issue_ids)
                ->where('ca.delete_time', 0)->where('ca.status', 1)
                ->field('ca.id,ca.name,ca.type,cav.id,cav.value,cav.issue_id')->where('cav.value','neq','')->select();
            $con_attr_value_v = [];
            foreach ($con_attr_value as $k => $v) {
                if ($v['type'] == 2 || $v['type'] == 5) {
                    $con_attr_value[$k]['value'] = $v['value']?explode(',', $v['value']):[];
                }
            }

            if($mode){
                if($issue){
                    $user = (new Base())->getUser();
                    if ($user) {
                        $user_issue = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id',$issue['id'])
                            ->where('module_type', 'collect')->where('delete_time',0)->count('id');
                        $issue['user_collect'] = $user_issue?1:0;
                    }else{
                        $issue['user_collect']=0;
                    }
                    $issue['attr'] =$con_attr_value;
                }

            }else{
                $issue = $issue->each(function ($v, $k) use ($con_attr_value) {
                    $v['user_collect'] = 0;
                    $user = (new Base())->getUser();
                    if ($user) {
                        $user_issue = Db::name('user_issue')->where('user_id', $user['id'])->where('module_type', 'collect')
                            ->where('delete_time',0)->select();
                        foreach ($user_issue as $va) {
                            if ($va['module_id'] == $v['id']) {
                                $v['user_collect'] = 1;
                            }
                        }
                    }
//                    foreach ($con_attr_value as $item) {
//                        if ($v['id'] == $item['issue_id']) {
//                            $v['attr'][] = $item;
//                        }
//                    }
                    return $v;
                });
            }
            return $issue;
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '系统错误']);
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
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
