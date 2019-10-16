<?php

namespace app\api\controller;

use app\common\Redis;
use app\http\job\AliyunSMS;
use think\Controller;
use think\Db;
use think\facade\Log;
use think\Request;
use think\Validate;

class My extends Controller
{

    /**
     * 个人中心首页
     * @return \think\response\Json
     */
    public function index(Request $request)
    {
        $user_id = $request->post('user_id');
        $users = (new Base())->getUser();
        if ($user_id == null) {

            if (!$users) {
                return json(['code' => 0, 'msg' => '请登录后重试']);
            }
        }
        try {
            $user = Db::name('user')->where('id', $user_id?$user_id:$users['id'])
                ->field('id as user_id,fans_num,attention_num,praise_num,nickname,avatar,province,city,district,person_status,company_status,phone_status')
                ->find();
            if(!$user){
                return json(['code' => 0, 'msg' => '未找到用户']);
            }
            if ($users) {
                $user['city'] = $user['city'].$user['district'];
                unset($user['province']);
                unset($user['district']);
                if ($users['id'] == $user['user_id']) {
                    $user['user_attention'] = 1;
                } else {
                    $user['user_attention'] = 0;
                }

                //查找当前用户是否关注文章用户
                $cu_attention = Db::name('user_issue')->where('module_id', $users['id'])->where('user_id', $user['user_id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                //查询文章用户是否关注当前用户
                $to_attention = Db::name('user_issue')->where('module_id', $user['user_id'])->where('user_id', $users['id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
//                dump($cu_attention);dump($to_attention);die;
                if ($to_attention == 0) {
                    //未关注
                    $user['attention'] = 0;
                }
                if ($to_attention == 1) {
                    //以关注
                    $user['attention'] = 1;
                }
                if ($cu_attention + $to_attention == 2) {
                    //互关
                    $user['attention'] = 2;
                }
            }
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $user]);

        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 我的发布
     * @return \think\response\Json
     */
    public function my_issue(Request $request)
    {
        $user_id = $request->post('user_id');
        $user = (new Base())->getUser();

        if ($user_id == null) {
            if (!$user) {
                return json(['code' => 0, 'msg' => '请登录后重试']);
            }
        }

        try {
            $issue = Db::name('issue')->where('delete_time', 0)->alias('i')->join('user u', 'i.user_id=u.id')
                ->where('user_id', $user_id?$user_id:$user['id'])->where('check_status', 'neq', 2)
                ->field('i.id,i.user_id,i.content,i.pic,i.cate_id,i.praise_num,i.review_num,i.collect_num,from_unixtime(i.create_time, \'%m-%d %H:%i\') as create_time,
                u.city,u.province,u.district,u.company_status,u.person_status,u.phone,i.permission')
                ->paginate(20);
            $issue = $issue->each(function ($v, $k) use ($user) {
//                if ($v['province'] == $v['city']) {
//                    unset($v['province']);
//                    unset($v['district']);
//                } else {
                    $v['city'] = $v['city'].$v['district'];
                    unset($v['province']);
                    unset($v['district']);
//                }
                $phone = $v['phone'];
                if ($v['permission'] != 0) {
                    $v['phone'] = '';
                }

                if ($user) {
                    //当前用户是否点赞
                    $praise = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id', $v['id'])
                        ->where('module_type', 'praise')->where('delete_time', 0)->count('id');
                    $v['user_praise'] = $praise ? 1 : 0;
                    //是否收藏
                    $collect = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $user['id'])
                        ->where('module_id', $v['id'])->where('module_type', 'collect')->count('id');
                    $v['user_collect'] = $collect ? 1 : 0;

                    //判断当前用户是否有权查看拨打电话
                    if ($v['permission'] == -1) {
                        $v['phone'] = '';
                    } else if ($v['permission'] == 1) {
                        if ($user['person_status'] != 1) {
                            $v['phone'] = '';
                        }
                    } else if ($v['permission'] == 2) {
                        if ($user['company_status'] != 1) {
                            $v['phone'] = '';
                        }
                    } else {
                        $v['phone'] = $phone;
                    }

                    //当前内容是否是自己发布
                    if ($user['id'] == $v['user_id']) {
                        $v['user_attention'] = 1;
                        $v['phone'] = $phone;
                    } else {
                        $v['user_attention'] = 0;
                    }
                    //查找当前用户是否关注文章用户
                    $cu_attention = Db::name('user_issue')->where('module_id', $v['user_id'])->where('user_id', $user['id'])
                        ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                    //查询文章用户是否关注当前用户
                    $to_attention = Db::name('user_issue')->where('module_id', $user['id'])->where('user_id', $v['user_id'])
                        ->where('module_type', 'attention')->where('delete_time', 0)->count('id');

                    if ($cu_attention == 0) {
                        //未关注
                        $v['attention'] = 0;
                    }
                    if ($cu_attention == 1) {
                        //以关注
                        $v['attention'] = 1;
                    }
                    if ($cu_attention + $to_attention == 2) {
                        //互关
                        $v['attention'] = 2;
                    }
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

    /**
     * 我的粉丝
     * @return \think\response\Json
     */
    public function my_fans()
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {
            //查询关注了当前用户的数据
            $user_issue = Db::name('user_issue')->alias('ui')->join('user u', 'ui.user_id=u.id')
                ->field('u.id,u.nickname,u.avatar,u.city,u.person_status,u.company_status,ui.user_id')
                ->where('module_id', $user['id'])->where('user_id', 'neq', $user['id'])
                ->where('module_type', 'attention')->where('delete_time', 0)->paginate(20);

            $user_issue = $user_issue->each(function ($v, $k) use ($user) {
                $cu_attention = Db::name('user_issue')->where('module_id', $v['user_id'])->where('user_id', $user['id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                $v['attention'] = 1;
                if ($cu_attention) {
                    $v['attention'] = 2;
                } else {
                    $v['attention'] = 0;
                }
                return $v;
            });
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $user_issue]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 添加关注
     * @param Request $request
     * @return \think\response\Json
     */
    public function add_attention(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 0, 'msg' => '请选择正确用户关注']);
        }
        if ($user['id'] == $id) {
            return json(['code' => 0, 'msg' => '不能关注自己']);
        }

        Db::startTrans();
        try {
            $issue_find = Db::name('user')->where('id', $id)->find();
            if (!$issue_find) {
                return json(['code' => 0, 'msg' => '当前用户不存在']);
            }
            $user_issue = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id', $issue_find['id'])->where('module_type', 'attention')
                ->find();
            if ($user_issue) {
                if ($user_issue['delete_time'] == 0) {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => time()]);
                    $issue = Db::name('user')->where('id', $issue_find['id'])->where('fans_num', '>', 0)->setDec('fans_num', 1);
                    $user = Db::name('user')->where('id', $user['id'])->where('attention_num', '>', 0)->setDec('attention_num');
                    Db::commit();
                    return json(['code' => 1, 'msg' => '已取消关注']);
                } else {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => 0]);
                    $issue = Db::name('user')->where('id', $issue_find['id'])->setInc('fans_num', 1);

                    $user = Db::name('user')->where('id', $user['id'])->setInc('attention_num', 1);
                    Db::commit();
                    return json(['code' => 1, 'msg' => '关注成功']);
                }

            } else {
                $user_issue = Db::name('user_issue')->insertGetId([
                    'user_id' => $user['id'],
                    'module_id' => $issue_find['id'],
                    'module_type' => 'attention',
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
                $issue = Db::name('user')->where('id', $issue_find['id'])->setInc('fans_num');
                $user = Db::name('user')->where('id', $user['id'])->setInc('attention_num');
                Db::commit();
                if ($user_issue) {
                    return json(['code' => 1, 'msg' => '关注成功']);
                }
                Db::rollback();
                return json(['code' => 0, 'msg' => '关注失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 我的关注
     * @return \think\response\Json
     */
    public function my_attention()
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }

        try {
            $user_issue = Db::name('user_issue')->alias('ui')->join('user u', 'ui.module_id=u.id')
                ->field('u.id,u.nickname,u.avatar,u.city,u.person_status,u.company_status,ui.module_id')
                ->where('user_id', $user['id'])
                ->where('module_type', 'attention')->where('delete_time', 0)->paginate(20);
            $user_issue = $user_issue->each(function ($v, $k) use ($user) {
                $cu_attention = Db::name('user_issue')->where('module_id', $user['id'])->where('user_id', $v['module_id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                $v['attention'] = 1;
                if ($cu_attention) {
                    $v['attention'] = 2;
                }
                return $v;
            });

            return json(['code' => 1, 'msg' => '查询成功', 'data' => $user_issue]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 添加收藏
     * @param Request $request
     * @return \think\response\Json
     */
    public function add_collect(Request $request)
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
            $issue = Db::name('issue')->where('id', $id)->where('status', 1)
                ->where('check_status', 'neq', 2)->where('delete_time', 0)->find();
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
                    $issue = Db::name('issue')->where('id', $id)->setInc('collect_num', 1);
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
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 我的收藏
     * @param Request $request
     * @return \think\response\Json
     */
    public function my_collect(Request $request)
    {
        return $this->get_info('collect');
    }

    /**
     * 我的评论
     * @return \think\response\Json
     */
    public function my_review()
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {
            $issue = Db::name('user_issue')->alias('ui')->join('comment u', 'ui.module_id=u.id')
                ->join('user us', 'us.id=u.user_id')
                ->field('u.id,u.content,us.nickname,us.avatar,us.person_status,us.company_status,from_unixtime(u.create_time,\'%m-%d %H:%i\') as create_time')
                ->where('ui.user_id', $user['id'])->where('ui.module_type', 'review')->where('ui.delete_time', 0)
                ->where('u.status', 'neq', 2)
                ->where('u.delete_time', 0)->order('ui.create_time desc')->paginate(20);
            return json(['code' => 1, 'msg' => '查询成功', 'data' => $issue]);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 点赞
     * @param Request $request
     * @return \think\response\Json
     */
    public function add_praise(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 0, 'msg' => '请选择正确内容点赞']);
        }
        Db::startTrans();
        try {
            $issue_find = Db::name('issue')->where('id', $id)->where('status', 1)
                ->where('check_status', 'neq', 2)->where('delete_time', 0)->find();
            if (!$issue_find) {
                return json(['code' => 0, 'msg' => '当前点赞内容不存在']);
            }
            $user_issue = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id', $issue_find['id'])->where('module_type', 'praise')
                ->find();
            if ($user_issue) {
                if ($user_issue['delete_time'] == 0) {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => time()]);
                    $issue = Db::name('issue')->where('id', $id)->where('praise_num', '>', 0)->setDec('praise_num', 1);
                    $user = Db::name('user')->where('id', $issue_find['user_id'])->where('praise_num', '>', 0)->setDec('praise_num');
                    Db::commit();
                    return json(['code' => 1, 'msg' => '已取消点赞']);
                } else {
                    $user_issue = Db::name('user_issue')->where('id', $user_issue['id'])->update(['delete_time' => 0]);
                    $issue = Db::name('issue')->where('id', $id)->setInc('praise_num', 1);

                    $user = Db::name('user')->where('id', $issue_find['user_id'])->setInc('praise_num', 1);
                    Db::commit();
                    return json(['code' => 1, 'msg' => '点赞成功']);
                }

            } else {
                $user_issue = Db::name('user_issue')->insertGetId([
                    'user_id' => $user['id'],
                    'module_id' => $issue_find['id'],
                    'module_type' => 'praise',
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
                $issue = Db::name('issue')->where('id', $id)->setInc('praise_num');
                $user = Db::name('user')->where('id', $issue_find['user_id'])->setInc('praise_num');
                Db::commit();
                if ($user_issue) {
                    return json(['code' => 1, 'msg' => '点赞成功']);
                }
                Db::rollback();
                return json(['code' => 0, 'msg' => '点赞失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 我的点赞
     * @return \think\response\Json
     */
    public function my_praise()
    {
        return $this->get_info('praise');
    }

    /**
     * 我的浏览历史
     * @return \think\response\Json
     */
    public function my_browse()
    {
        return $this->get_info('browse');
    }

    /**
     * 手机验证
     * @param Request $request
     * @return \think\response\Json
     */
    public function binding_phone(Request $request)
    {

        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $phone = $request->post();
        $verification_code = $request->post('verification_code');
        $verification_key = $request->post('verification_key');
        $rule = [
            'phone' => 'require|regex:/^(1[35874]\d{9})$/',
        ];
        $message = [
            'phone.require' => '请填写手机号',
            'phone.regex' => '手机号填写错误'
        ];

        $validate = new Validate($rule, $message);
        if (!$validate->check($phone)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        //检查手机号是否已经验证
        if ($user['phone'] == $phone['phone'] && $user['phone_status'] == 1) {
            return json(['code' => 0, 'msg' => '当前手机号以绑定，请勿重复绑定']);
        }
        $redis = Redis::redis();
        //缓存用户信息，判断请求次数
        if ($redis->exists('user_' . $user['id'] . $phone['phone']) && !$verification_code) {
            if ($redis->get('user_' . $user['id'] . $phone['phone']) == $phone['phone']) {
                $timeout = $redis->ttl('user_' . $user['id'] . $phone['phone']);
                return json(['code' => 0, 'msg' => '您以申请过验证码，短期内请勿重复申请', 'data' => $timeout + 10]);
            }
        }

        if (!$verification_code) {
            $code = rand(100000, 999999);
            $key = 'verificationCode_' . rand(1000000000, 9999999999);
            $redis->set($key, $code, 300);
            $code1 = json_encode(['code' => $code]);
            $send = (new AliyunSMS())->fire($phone['phone'], $code1);
            if ($send['Code'] == 'OK') {
                $redis->set('user_' . $user['id'] . $phone['phone'], $phone['phone'], 50);
                return json(['code' => 1, 'msg' => '发送成功', 'data' => ['key' => $key, 'code' => $code, 'timeout' => 60, 'expired_time' => 300]]);
            } else {
                if ($send['Code'] == 'isv.BUSINESS_LIMIT_CONTROL') {
                    json(['code' => 0, 'msg' => '您当前手机号今日短信发送余量已用完，请明日再试。']);
                }
                Log::init([
                    'type' => 'File',
                    'path' => ERROR_PATH . '/aliyunSMS/',
                    'level' => [],
                    'apart_level' => ['error', 'sql'],
                ]);
                $send = json_encode($send, true);
                Log::record($send, 'error');

                return json(['code' => 0, 'msg' => $send]);
            }
        } else if ($verification_code && $verification_key) {
            if ($redis->exists($verification_key)) {
                $code = $redis->get($verification_key);
                if ($code == $verification_code) {
                    try {
                        $user = Db::name('user')->where('id', $user['id'])->update(['phone' => $phone['phone'], 'phone_status' => 1]);
                        return json(['code' => 1, 'msg' => '操作成功']);
                    } catch (\Exception $e) {
                        $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
                        Log::error(json_encode($data, 256));
                        return json(['code' => 0, 'msg' => '系统错误']);
                    }
                }
            }
            return json(['code' => 0, 'msg' => '验证码以过期']);
        }

    }

    /**
     * 修改地区
     * @param Request $request
     * @return \think\response\Json
     */
    public function update_city(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $form = $request->post();
        $rule = [
            'province' => 'require',
            'city' => 'require',
            'district' => 'require'
        ];
        $message = [
            'province' => '请选择省份',
            'city' => '请选择城市',
            'district' => '请选择区域'
        ];
        $validate = new Validate($rule, $message);
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        try {
            $user = Db::name('user')->where('id', $user['id'])
                ->update(['city' => $form['city'], 'province' => $form['province'], 'district' => $form['district']]);
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 反馈
     * @param Request $request
     * @return \think\response\Json
     */
    public function feedback(Request $request)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        $form = $request->post();
        $rule = [
            'content' => 'require|max:500',
        ];
        $message = [
            'content.require' => '请填写反馈内容',
            'content.max' => '反馈内容不能大于500个字'
        ];
        $validate = new Validate($rule, $message);
        if (!$validate->check($form)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }
        if (array_key_exists('pic', $form) && $form['pic'] != null) {
            $pic = explode(',', $form['pic']);
            if (count($pic) > 9) {
                return json(['code' => 0, 'msg' => '图片最多只能上传9张']);
            }
        }

        try {
            $data = [
                'user_id' => $user['id'],
                'content' => $form['content'],
                'pic' => array_key_exists('pic', $form) ? $form['pic'] : '',
                'create_time' => time(),
                'update_time' => time(),
            ];
            $feedback = Db::name('feedback')->insert($data);
            if ($feedback) {
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            $data = ['code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'message' => $e->getMessage()];
            Log::error(json_encode($data, 256));
            return json(['code' => 0, 'msg' => '系统错误']);
        }
    }

    /**
     * 联系我们
     * @return \think\response\Json
     */
    public function about()
    {
        $site = (new Base())->getSite();
        if ($site == 0) {
            return json(['code' => 0, 'msg' => '系统错误']);
        }
        return json(['code' => 1, 'msg' => '查询成功', 'data' => $site]);
    }

    /**
     * 查询点赞收藏浏览历史
     * @param $field
     * @return \think\response\Json
     */
    public function get_info($field)
    {
        $user = (new Base())->getUser();
        if (!$user) {
            return json(['code' => 0, 'msg' => '请登录后重试']);
        }
        try {
            $issue = Db::name('user_issue')->alias('ui')->join('issue u', 'ui.module_id=u.id')
                ->join('user us', 'us.id=u.user_id')
                ->field('u.cate_id,u.id,u.content,us.nickname,u.praise_num,u.collect_num,u.review_num,u.permission,us.phone,u.pic,us.id as user_id,
                us.city,us.province,us.district,us.company_status,us.person_status,us.avatar')
                ->where('ui.user_id', $user['id'])->where('ui.module_type', $field)->where('ui.delete_time', 0)
                ->where('u.delete_time', 0)->where('u.check_status', 'neq', 2)->order('ui.create_time desc')->paginate(20);

            $issue = $issue->each(function ($v, $k) use ($user) {
//                if ($v['province'] == $v['city']) {
//                    unset($v['province']);
//                    unset($v['district']);
//                } else {
                    $v['city'] = $v['city'].$v['district'];
                    unset($v['province']);
                    unset($v['district']);
//                }
                //当前用户是否点赞
                $praise = Db::name('user_issue')->where('user_id', $user['id'])->where('module_id', $v['id'])
                    ->where('module_type', 'praise')->where('delete_time', 0)->count('id');
                $v['user_praise'] = $praise ? 1 : 0;
                //是否收藏
                $collect = Db::name('user_issue')->where('delete_time', 0)->where('user_id', $user['id'])
                    ->where('module_id', $v['id'])->where('module_type', 'collect')->count('id');
                $v['user_collect'] = $collect ? 1 : 0;
                $phone = $v['phone'];
                //判断当前用户是否有权查看拨打电话
                if ($v['permission'] == -1) {
                    $v['phone'] = '';
                } else if ($v['permission'] == 1) {
                    if ($user['person_status'] != 1) {
                        $v['phone'] = '';
                    }
                } else if ($v['permission'] == 2) {
                    if ($user['company_status'] != 1) {
                        $v['phone'] = '';
                    }
                } else {
                    $v['phone'] = $phone;
                }
                //当前内容是否是自己发布
                if ($user['id'] == $v['user_id']) {
                    $v['user_attention'] = 1;
                    $v['phone'] = $phone;
                } else {
                    $v['user_attention'] = 0;
                }
                //查找当前用户是否关注文章用户
                $cu_attention = Db::name('user_issue')->where('module_id', $v['user_id'])->where('user_id', $user['id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                //查询文章用户是否关注当前用户
                $to_attention = Db::name('user_issue')->where('module_id', $user['id'])->where('user_id', $v['user_id'])
                    ->where('module_type', 'attention')->where('delete_time', 0)->count('id');
                if ($cu_attention == 0) {
                    //未关注
                    $v['attention'] = 0;
                }
                if ($cu_attention == 1) {
                    //以关注
                    $v['attention'] = 1;
                }
                if ($cu_attention + $to_attention == 2) {
                    //互关
                    $v['attention'] = 2;
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
}
