<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\facade\Log;
use think\Request;
use app\api\validate\Issue as IssueValidate;

class Issue extends Controller
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $pagesize = $request->post('pagesize');
        //栏目
        $column = $request->post('column');
        //分类
        $cate_id = $request->post('cate_id');
        //属性
        $attr_id = $request->post('attr_id');
        //区域
        $province = $request->post('province');
        $city = $request->post('city');
        $district = $request->post('district');
        //搜索
        $search = $request->post('search');

        $condition = '1';
        $operator = 'eq';
        $field = 'recommend';
        if ($column == -1) {
            //推荐
            $condition = '1';
            $operator = 'eq';
            $field = 'recommend';
        } else if ($column == -2) {
            $user = (new Base())->getUser();
            if (!$user) {
                return json(['code' => -1, 'msg' => '请登录后重试']);
            }
            //关注
            $user_issue = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $user['id'])
                ->where('module_type', 'attention')->column('module_id');
            if (count($user_issue) == 0) {
                return json(['code' => 1, 'msg' => '','data'=>['data'=>[]]]);
            }
            $condition = $user_issue;
            $operator = 'in';
            $field = 'user_id';
        } else if ($column == -3) {
            //附近
        } else {
            $condition = $column;
            $operator = '=';
            $field = 'category_id';
        }
        //分类筛选
        if ($cate_id) {
            $condition = $cate_id;
            $operator = 'in';
            $field = 'cate_id';
        }

        try {
            //有效期过期处理
            $issue_valid = Db::name('issue')->where('valid_status', 1)
                ->where('valid_time', 'neq', 0)
                ->where('DATEDIFF(CURDATE(),FROM_UNIXTIME(create_time,\'%Y-%m-%d\'))>7')->update(['valid_status' => 0]);

            //属性筛选
            if ($attr_id) {
                $attr_ids = explode(',', $attr_id);
                $issue_ids = Db::name('attr_value')->where('attr_id', 'in', $attr_ids)->group('issue_id')
                    ->having('count(attr_id)=' . count($attr_ids))->field('issue_id')->column('issue_id');
                $condition = $issue_ids;
                $operator = 'in';
                $field = 'i.id';
            }

            $issue = Db::name('issue')->alias('i')->join('user u', 'i.user_id=u.id')
                ->where('i.delete_time', 0)->where('i.check_status', 'neq', 2)
                ->where('valid_status', 1)
                ->field('i.id,i.content,i.pic,i.user_id,i.praise_num,u.nickname,u.avatar,
                u.province,u.city,u.district,u.person_status,u.company_status,i.review_num,i.permission,u.phone');
            $user = (new Base())->getUser();
//            if (!$user) {
//                $condition = '1';
//                $operator = 'eq';
//                $field = 'recommend';
//                $column = -1;
//            }
            if ($search) {
                $issue = $issue->where('content', 'like', '%' . $search . '%');
            } else {
                if ($column != -3) {
                    $issue = $issue->where($field, $operator, $condition);
                } else {
                    $issue = $issue->where('i.province', $user['province'])->where('i.city', $user['city'])
                        ->where('i.district', $user['district']);
                }
                if ($city && $province && $district) {
                    $issue = $issue->where('i.city', $city)->where('i.province', $province)->where('i.district', $district);
                }
            }

            $issue = $issue->order('i.create_time desc')->paginate($pagesize ?: 20);


            //查询公告
            $notice = Db::name('notice')->where('delete_time', 0)->where('status', 1)
                ->field('id,pic')->select();
//            dump($notice);die;
            $lim = 1;
            $issue = $issue->each(function ($v, $k) use ($user, $notice) {
                static $lim = 0;
                //添加广告
                if ($k / 2 == 1) {
                    if (array_key_exists($lim, $notice)) {
                        $v['ad'] = $notice[$lim];
                        $lim = $lim + 1;
                    }
                }
                $v['city'] = $v['city'] . $v['district'];
                unset($v['province']);
                unset($v['district']);
                $phone = $v['phone'];
                //联系权限
                if ($v['permission'] != 0) {
                    $v['phone'] = null;
                }
                $v['attention'] = 0;
                $v['user_attention'] = 0;
                //判断当前用户是否有权查看拨打电话

                if ($v['permission'] == -1) {
                    $v['phone'] = '';
                } else {
                    $v['phone'] = $phone;
                }
                if ($user) {

                    //是否点攒
                    $praise = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $user['id'])
                        ->where('module_id', $v['id'])->where('module_type', 'praise')->count('id');
                    $v['user_praise'] = $praise ? 1 : 0;
                    //是否收藏
                    $collect = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $user['id'])
                        ->where('module_id', $v['id'])->where('module_type', 'collect')->count('id');
                    $v['user_collect'] = $collect ? 1 : 0;
                    //是否是自己发布的
                    if ($v['user_id'] == $user['id']) {
                        $v['user_attention'] = 1;
                        $v['phone'] = $phone;
                    } else {
                        $v['user_attention'] = 0;
                    }
                    //查询关注信息
                    $cu_attention = Db::name('user_issue')->where('user_id', $user['id'])
                        ->where('module_id', $v['user_id'])->where('module_type', 'attention')
                        ->where('delete_time', 0)->count('id');
                    $to_attention = Db::name('user_issue')->where('user_id', $v['user_id'])
                        ->where('module_id', $user['id'])->where('module_type', 'attention')
                        ->where('delete_time', 0)->count('id');
                    if ($cu_attention == 0) {
                        $v['attention'] = 0;
                    }
                    if ($cu_attention == 1) {
                        $v['attention'] = 1;
                    }
                    if ($cu_attention + $to_attention == 2) {
                        $v['attention'] = 2;
                    }
                } else {
                    $v['user_praise'] = 0;
                    //是否收藏
                    $v['user_collect'] = 0;
                }
                return $v;
            });

            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function save(Request $request)
    {
        $user = (new Base())->getUser();
        if ($user['phone_status'] == 0) {
            return json(['code' => 0, 'msg' => '请先绑定手机后再发布']);
        }
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $form = $request->post();
        $validate = new IssueValidate();
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        if (array_key_exists('pic', $form) && $form['pic'] != null) {
            $pic = explode(',', $form['pic']);
            if (count($pic) > 9) {
                return json(['code' => 0, 'msg' => '图片最多只能上传9张']);
            }
        } else {
            $form['pic'] = '';
        }

        Db::startTrans();
        try {
            $attr_array = explode(',', $form['attr_id']);
            $count = count($attr_array);
            $attr_cate_id = Db::name('attr')->where('delete_time', 0)->where('status', 1)
                ->where('id', 'in', $attr_array)->group('cate_id')->having("count(id=$count)")
                ->field('cate_id')->value('cate_id');

            $category_id = Db::name('category')->where('delete_time', 0)->where('status', 1)
                ->where('id', 'eq', function ($query) use ($attr_cate_id) {
                    $query->name('category')->where('delete_time', 0)->where('status', 1)->where('id', $attr_cate_id)->field('pid');
                })->value('id');
            if (!$category_id) {
                return json(['code' => 0, 'msg' => '当前属性或分类不存在，请刷新后重试']);
            }
            //新增发布数据
            $data = [
                'user_id' => $user['id'],
                'pic' => $form['pic'],
                'cate_id' => $attr_cate_id,
                'category_id' => $category_id,
                'content' => $form['content'],
                'province' => $form['province'],
                'city' => $form['city'],
                'district' => $form['district'],
                'permission' => $form['permission'],
                'valid_time' => $form['valid_time'],
                'create_time' => time(),
                'update_time' => time()
            ];
            //新增
            $issue = Db::name('issue')->insertGetId($data);
            if ($issue) {
                //处理标签
                $attr_array = explode(',', $form['attr_id']);
                $attr_count = count($attr_array);
                $data = [];
                foreach ($attr_array as $v) {
                    $data[] = [
                        'attr_id' => $v,
                        'issue_id' => $issue
                    ];
                }
                //增加属性与发布关联
                $attr_value = Db::name('attr_value')->insertAll($data);
                if ($attr_value != $attr_count) {
                    return json(['code' => 0, 'msg' => '发布失败']);
                }
            } else {
                return json(['code' => 0, 'msg' => '发布失败']);
            }
            Db::commit();
            return json(['code' => 1, 'msg' => '发布成功']);
        } catch (\Exception $e) {
            Db::rollback();
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }


    public function show(Request $request)
    {
        $issue_id = $request->post('issue_id');
        Db::startTrans();
        try {
            if ($issue_id) {
                $issue = Db::name('issue')->alias('i')->join('user u', 'u.id=i.user_id')
                    ->where('i.id', $issue_id)->where('i.delete_time', 0)->where('i.check_status', 'neq', 2)
                    ->field('i.id,i.content,i.pic,i.user_id,i.praise_num,u.nickname,u.avatar,u.province,u.city,u.district,u.person_status,u.company_status,i.review_num,i.permission,u.phone')
                    ->find();
//                dump($issue_id);die;
//                if ($issue['province'] == $issue['city']) {
//                    unset($issue['province']);
//                    unset($issue['district']);
//                } else {
                    $issue['city'] = $issue['city'].$issue['district'];
                    unset($issue['province']);
                    unset($issue['district']);
//                }
                $cu_user = (new Base())->getUser();
                if ($issue) {
                    $issue['attention'] = 0;
                    $issue['user_attention'] = 0;
                    $phone = $issue['phone'];
                    //联系权限
                    if ($issue['permission'] != 0) {
                        $issue['phone'] = null;
                    }
                    if ($cu_user) {
                        //是否点攒
                        $praise = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $cu_user['id'])
                            ->where('module_id', $issue['id'])->where('module_type', 'praise')->count('id');
                        $issue['user_praise'] = $praise ? 1 : 0;
                        //是否收藏
                        $collect = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $cu_user['id'])
                            ->where('module_id', $issue['id'])->where('module_type', 'collect')->count('id');
                        $issue['user_collect'] = $collect ? 1 : 0;
                        //是否是自己发布的
                        if ($issue['user_id'] == $cu_user['id']) {
                            $issue['user_attention'] = 1;
                            $issue['phone'] = $phone;
                        } else {
                            //判断当前用户是否有权查看拨打电话
                            if ($issue['permission'] == -1) {
                                $issue['phone'] = '';
                            } else if ($issue['permission'] == 1) {
                                if ($cu_user['person_status'] != 1) {
                                    $issue['phone'] = '';
                                }
                            } else if ($issue['permission'] == 2) {
                                if ($cu_user['company_status'] != 1) {
                                    $issue['phone'] = '';
                                }
                            } else {
                                $issue['phone'] = $phone;
                            }
                            $issue['user_attention'] = 0;
                        }
                        //查询关注信息
                        $cu_attention = Db::name('user_issue')->where('user_id', $cu_user['id'])
                            ->where('module_id', $issue['user_id'])->where('module_type', 'attention')
                            ->where('delete_time', 0)->count('id');
                        $to_attention = Db::name('user_issue')->where('user_id', $issue['user_id'])
                            ->where('module_id', $cu_user['id'])->where('module_type', 'attention')
                            ->where('delete_time', 0)->count('id');
                        if ($cu_attention == 0) {
                            $issue['attention'] = 0;
                        }
                        if ($cu_attention == 1) {
                            $issue['attention'] = 1;
                        }
                        if ($cu_attention + $to_attention == 2) {
                            $issue['attention'] = 2;
                        }
                        //增加浏览历史
                        $user_issue = Db::name('user_issue')->where('user_id', $cu_user['id'])
                            ->where('module_id', $issue['id'])->where('module_type', 'browse')->find();
                        if (!$user_issue) {
                            $user_issue = Db::name('user_issue')->insertGetId([
                                'user_id' => $cu_user['id'],
                                'module_id' => $issue['id'],
                                'module_type' => 'browse',
                                'create_time' => time(),
                                'update_time' => time(),
                            ]);
                        }
                    } else {
                        $issue['user_praise'] = 0;
                        //是否收藏
                        $issue['user_collect'] = 0;
                    }
                } else {
                    return json(['code' => 0, 'msg' => '当前内容不存在']);
                }
                Db::name('issue')->where('id', $issue_id)->setInc('browse_num', 1);
                Db::commit();
                return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
            } else {
                Db::rollback();
                return json(['code' => 0, 'msg' => '请选择正确内容查看']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    public function delete(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => '请登录后重试']);
        }
        $issue_id = $request->post('issue_id');
        try {
            $issue = Db::name('issue')->where('id', $issue_id)->where('delete_time', 0)
                ->where('check_status', 'neq', 2)->find();
            if (!$issue) {
                return json(['code' => 0, 'msg' => '当前删除内容未找到']);
            } else {
                if ($issue['user_id'] != $user['id']) {
                    return json(['code' => 0, 'msg' => '当前内容不是您发布的，禁止操作']);
                }
            }
            //删除
            $issue = $issue = Db::name('issue')->where('id', $issue_id)->where('delete_time', 0)
                ->where('check_status', 'neq', 2)->where('user_id', $user['id'])
                ->update(['delete_time' => time()]);

            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

}
