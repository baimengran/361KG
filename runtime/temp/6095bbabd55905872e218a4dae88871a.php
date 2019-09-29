<?php /*a:3:{s:72:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\menu\index.html";i:1569119892;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\header.html";i:1569059483;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\footer.html";i:1569388136;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name=”renderer” content=”webkit|ie-stand|ie-comp” />
<meta name="force-rendering" content="webkit" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>后台管理系统</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="shortcut icon" href="<?php echo url('/public/favicon.ico','',''); ?>">
<link href="/static/admin/css/bootstrap.min.css" rel="stylesheet">
<link href="/static/admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
<link href="/static/admin/css/animate.min.css" rel="stylesheet">
<link href="/static/admin/css/style.min.css" rel="stylesheet">
<link href="/static/admin/css/plugins/chosen/chosen.css" rel="stylesheet">

<link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/static/admin/css/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<link href="/static/admin/css/plugins/cropper/cropper.min.css" rel="stylesheet">
<link href="/static/admin/css/plugins/switchery/switchery.css" rel="stylesheet">
<link href="/static/admin/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="/static/admin/css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">
<link href="/static/admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/static/admin/css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
<link href="/static/admin/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">
<link href="/static/admin/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
<link href="/static/admin/css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
<link href="/static/admin/css/animate.min.css" rel="stylesheet">
<link href="/static/admin/css/style.min862f.css?v=4.1.0" rel="stylesheet">
<!--[if lt IE 9]>
<meta http-equiv="refresh" content="0;ie.html" />
<![endif]-->
<script>
    // if(window.top!==window.self){window.top.location=window.location};
</script>

</head>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>菜单列表</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-12">
                    <div  class="col-sm-2">
                        <div class="input-group" >
                            <button type="button" class="btn btn-primary btn-outline" data-toggle="modal" data-target="#myModal">添加菜单</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="example-wrap">
                <div class="example">
                    <form id="ruleorder" name="ruleorder" method="post" action="<?php echo url('ruleorder'); ?>" >
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="long-tr">
                                    <th width="6%">ID</th>
                                    <th width="19%">权限名称</th>
                                    <th width="15%">节点</th>
                                    <th width="15%">菜单状态</th>
                                    <th width="18%">添加时间</th>
                                    <th width="10%">排序</th>
                                    <th width="15%">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($admin_rule) || $admin_rule instanceof \think\Collection || $admin_rule instanceof \think\Paginator): if( count($admin_rule)==0 ) : echo "" ;else: foreach($admin_rule as $key=>$v): ?>
                                    <tr class="long-td">
                                        <td><?php echo htmlentities($v['id']); ?></td>
                                        <td style='text-align:left;padding-left:<?php if($v['leftpin'] != 0): ?><?php echo htmlentities($v['leftpin']); ?>px<?php endif; ?>'><?php echo htmlentities($v['lefthtml']); ?><?php echo htmlentities($v['title']); ?></td>
                                        <td><?php echo htmlentities($v['name']); ?></td>
                                        <td>
                                            <?php if($v['status'] == 1): ?>
                                                <a class="red" href="javascript:;" onclick="rule_state(<?php echo htmlentities($v['id']); ?>);">
                                                    <div id="zt<?php echo htmlentities($v['id']); ?>"><span class="label label-info">开启</span></div>
                                                </a>
                                            <?php else: ?>
                                                <a class="red" href="javascript:;" onclick="rule_state(<?php echo htmlentities($v['id']); ?>);">
                                                    <div id="zt<?php echo htmlentities($v['id']); ?>"><span class="label label-danger">禁用</span></div>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlentities($v['create_time']); ?></td>
                                        <td style="padding: 3px" >
                                            <div >
                                                <input name="<?php echo htmlentities($v['id']); ?>" value=" <?php echo htmlentities($v['sort']); ?>" width="50%" style="text-align:center;" class="form-control">
                                            </div>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('edit_rule',['id'=>$v['id']]); ?>" class="btn btn-primary btn-outline btn-xs">
                                                <i class="fa fa-paste"></i> 编辑</a>&nbsp;&nbsp;
                                            <a href="javascript:;" onclick="del_rule(<?php echo htmlentities($v['id']); ?>)" class="btn btn-danger btn-outline btn-xs">
                                                <i class="fa fa-trash-o"></i> 删除</a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                <tr>
                                    <td colspan="8" align="right">
                                    <button type="submit"  id="btnorder" class="btn btn-info">更新排序</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
    <div class="modal  fade" id="myModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">添加菜单</h3>
                </div>
                <form class="form-horizontal" name="add_rule" id="add_rule" method="post" action="<?php echo url('add_rule'); ?>">
                    <div class="ibox-content">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">所属父级</label>
                                <div class="col-sm-8">
                                    <select name="pid" class="form-control">
                                        <option value="0">--默认顶级--</option>
                                        <?php if(is_array($admin_rule) || $admin_rule instanceof \think\Collection || $admin_rule instanceof \think\Paginator): if( count($admin_rule)==0 ) : echo "" ;else: foreach($admin_rule as $key=>$v): ?>
                                            <option value="<?php echo htmlentities($v['id']); ?>" style="margin-left:55px;"><?php echo htmlentities($v['lefthtml']); ?><?php echo htmlentities($v['title']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">菜单名称</label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" id="title" placeholder="输入菜单名称" class="form-control"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">节点</label>
                                <div class="col-sm-8">
                                    <input type="text" name="name" id="name" placeholder="模块/控制器/方法" class="form-control"/>
                                    <span class="help-block m-b-none">如：admin/user/adduser (一级节点添加“#”即可)</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">CSS样式</label>
                                <div class="col-sm-8">
                                    <input type="text" name="css" id="css" placeholder="输入菜单名称前显示的CSS样式" class="form-control"/>
                                    <span class="help-block m-b-none"> <a href="http://fontawesome.dashgame.com/" target="_black">选择图标</a> 如fa fa-user </span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">排&nbsp;序</label>
                                <div class="col-sm-8">
                                    <input type="text" name="sort" id="sort" value="50" placeholder="输入排序" class="form-control"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">状&nbsp;态：</label>
                            <div class="input-group col-sm-4">
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="inlineRadio1" value="1" name='status' checked>
                                    <label for="inlineRadio1"> 开启 </label>

                                </div>
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="inlineRadio2" value="0" name='status' >
                                    <label for="inlineRadio2"> 关闭 </label>
                                </div>

                            </div>
                        </div>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 保存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> 关闭</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/static/admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script src="/static/admin/js/hplus.min.js?v=4.1.0"></script>
<script type="text/javascript" src="/static/admin/js/contabs.min.js"></script>
<script src="/static/admin/js/plugins/pace/pace.min.js"></script>
<script src="/static/admin/js/jquery.form.js"></script>
<script src="/static/admin/js/lunhui.js"></script>
<script src="/static/admin/js/ajax_login_overtime.js"></script>
<script src="/static/admin/js/plugins/chosen/chosen.jquery.js"></script>
<!--<script src="/static/admin/js/content.min.js?v=1.0.0"></script>-->
<!--<script src="/static/admin/js/plugins/jsKnob/jquery.knob.js"></script>-->
<!--<script src="/static/admin/js/plugins/jasny/jasny-bootstrap.min.js"></script>-->
<!--<script src="/static/admin/js/plugins/datapicker/bootstrap-datepicker.js"></script>-->
<!--<script src="/static/admin/js/plugins/prettyfile/bootstrap-prettyfile.js"></script>-->
<!--<script src="/static/admin/js/plugins/nouslider/jquery.nouislider.min.js"></script>-->
<script src="/static/admin/js/plugins/switchery/switchery.js"></script>
<!--<script src="/static/admin/js/plugins/ionRangeSlider/ion.rangeSlider.min.js"></script>-->
<!--<script src="/static/admin/js/plugins/iCheck/icheck.min.js"></script>-->
<!--<script src="/static/admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>-->
<!--<script src="/static/admin/js/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>-->
<!--<script src="/static/admin/js/plugins/clockpicker/clockpicker.js"></script>-->
<!--<script src="/static/admin/js/plugins/cropper/cropper.min.js"></script>-->




<script type="text/javascript">

    $(function(){
        $('#add_rule').ajaxForm({
            beforeSubmit: checkForm,
            success: complete,
            dataType: 'json'
        });

        function checkForm(){
            if( '' == $.trim($('#title').val())){
                layer.msg('请输入菜单名称',{icon:2,time:1500,shade: 0.1}, function(index){
                    layer.close(index);
                });
                return false;
            }

            if( '' == $.trim($('#name').val())){
                layer.msg('控制器/方法不能为空',{icon:0,time:1500,shade: 0.1}, function(index){
                    layer.close(index);
                });
                return false;
            }
        }


        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('menu/index'); ?>";
                });
            }else{
                layer.msg(data.msg, {icon: 6,time:1500,shade: 0.1});
                return false;
            }
        }

    });


    //更新排序
    $(function(){
        $('#ruleorder').ajaxForm({
            success: complete,
            dataType: 'json'
        });

        function complete(data){
            if(data.code==1){
                layer.msg(data.msg, {icon: 1,time:1500,shade: 0.1}, function(index){
                    window.location.href="<?php echo url('menu/index'); ?>";
                });
            }else{
                layer.msg(data.msg, {icon: 2,time:1500,shade: 0.1}, function(index){
                    layer.close(index);
                    window.location.href=data.url;
                });
            }
        }
    });


    /**
     * [del_rule 删除菜单]
     * @Author[田建龙 864491238@qq.com]
     * @param   {[type]}    id [用户id]
     */
    function del_rule(id){
        layer.confirm('确认删除此菜单?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON('./del_rule', {'id' : id}, function(res){
                if(res.code == 1){
                    layer.msg(res.msg,{icon:1,time:1500,shade: 0.1},function(index){
                        layer.close(index);
                        window.location.href="<?php echo url('menu/index'); ?>";
                    });

                }else{
                    layer.msg(res.msg,{icon:0,time:1500,shade: 0.1});
                }
            });

            layer.close(index);
        })

    }

    //菜单状态
    function rule_state(id){
        lunhui.status(id,'<?php echo url("rule_state"); ?>');
    }

</script>
</html>