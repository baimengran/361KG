<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\exception\PDOException;
use think\Request;
use app\admin\model\Menu as MenuModel;

class Menu extends Base
{
    public function index()
    {
        $nav = new \org\Leftnav;
        $admin_rule = MenuModel::order('id asc')->all();
        $arr = $nav::rule($admin_rule);
        $this->assign('admin_rule',$arr);
        return $this->fetch();
    }


    /**
     * [add_rule 添加菜单]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function add_rule()
    {
        if(request()->isAjax()){
            $param = input('post.');

            try{
                $result = (new MenuModel())->save($param);
                if(false === $result){
                    return ['code' => -1, 'data' => '', 'msg' => '添加菜单失败'];
                }else{
                    return ['code' => 1, 'data' => '', 'msg' => '添加菜单成功'];
                }
            }catch(\Exception $e){
                return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
            }
        }

        return $this->fetch();
    }


    /**
     * [edit_rule 编辑菜单]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function edit_rule()
    {
        $menu = new MenuModel();
        if(request()->isPost()){
            $param = input('post.');
            try{
                $result =  $menu->save($param, ['id' => $param['id']]);
                if(false === $result){
                    return ['code' => 0, 'data' => '', 'msg' => '添加菜单失败'];
                }else{
//                    dump($param);die;
                    return ['code' => 1, 'data' => '', 'msg' => '编辑菜单成功'];
                }
            }catch(\Exception $e){
                return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
            }
        }
        $id = input('param.id');
        $this->assign('menu',$menu->where('id',$id)->find());
        return $this->fetch();
    }


    /**
     * [roleDel 删除角色]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function del_rule()
    {
        $id = input('param.id');
        $menu = new MenuModel();
        try{
            $menu->where('id', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除菜单成功'];
        }catch(\Exception $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    /**
     * [ruleorder 排序]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function ruleorder()
    {
        if (request()->isAjax()){
            $param = input('post.');
            foreach ($param as $id => $sort){
                Db::name('auth_rule')->where('id' ,$id)->update(['sort'=>$sort]);
            }
            return json(['code' => 1, 'msg' => '排序更新成功']);
        }
    }


    /**
     * [rule_state 菜单状态]
     * @return [type] [description]
     * @author [田建龙] [864491238@qq.com]
     */
    public function rule_state()
    {
        $id = input('param.id');
        $status = Db::name('auth_rule')->where('id',$id)->value('status');//判断当前状态
        if($status==1)
        {
            $flag = Db::name('auth_rule')->where('id',$id)->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('auth_rule')->where('id',$id)->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }

    }
}
