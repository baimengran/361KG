<?php /*a:3:{s:72:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\user\index.html";i:1569552615;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\header.html";i:1569059483;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\footer.html";i:1569388136;}*/ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
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

    <!--<style type="text/css">-->
        <!--table{table-layout:fixed;}-->
        <!--td{word-wrap:break-word;}-->
    <!--</style>-->
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>用户列表</h5>
        </div>
        <div class="ibox-content">
            <!--搜索框开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form name="admin_list_sea" class="form-search" method="post" action="<?php echo url('index'); ?>">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" id="key" class="form-control" name="key" value="<?php echo htmlentities($val); ?>"
                                       placeholder="输入需查询的属性名称"/>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-search"></i> 搜索</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--搜索框结束-->
            <div class="hr-line-dashed"></div>
            <div class="example-wrap">
                <div class="example">
                    <table class="table table-bordered table-hover text-center">
                        <thead>
                        <tr class="text-center">
                            <th>昵称</th>
                            <th>头像</th>
                            <th>个人认证</th>
                            <th>企业认证</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <script id="list-template" type="text/html">
                        </script>
                        <tbody id="list-content">
                        <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <tr class="long-td">
                            <td><?php echo htmlentities($v['nickname']); ?></td>
                            <td>
                                <!--<a href="" class="pull-left">-->
                                <img alt="image" width="50px" class="img-circle" src="<?php echo htmlentities($v['avatar']); ?>">
                                <!--</a>-->
                            </td>
                            <td>
                                <?php if($v['person_status']==1): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-info">已认证</span></div>
                                </a>
                                <?php elseif($v['person_status']==0): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-warning">待认证</span></div>
                                </a>
                                <?php elseif($v['person_status']==2): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-danger">认证未通过</span></div>
                                </a>
                                <?php elseif($v['person_status']==-1): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-default">未申请</span></div>
                                </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($v['company_status']==1): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-info">已认证</span></div>
                                </a>
                                <?php elseif($v['company_status']==0): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-warning">待认证</span></div>
                                </a>
                                <?php elseif($v['company_status']==2): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-danger">认证未通过</span></div>
                                </a>
                                <?php elseif($v['company_status']==-1): ?>
                                <a href="javascript:;">
                                    <div id="zd<?php echo htmlentities($v['id']); ?>"><span class="label label-default">未申请</span></div>
                                </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($v['person_status']==0): ?>
                                <a href="javascript:;" onclick="status('<?php echo url('user/person_status',['id'=>$v['id']]); ?>',<?php echo htmlentities($v['id']); ?>)"
                                   class="btn btn-primary btn-xs btn-outline">
                                    <i class="fa fa-paste"></i> 个人认证审核</a>&nbsp;&nbsp;
                                <?php endif; if($v['company_status']==0): ?>
                                <a href="javascript:;" onclick="status('<?php echo url('user/company_status',['id'=>$v['id']]); ?>',<?php echo htmlentities($v['id']); ?>)"
                                   class="btn btn-danger btn-xs btn-outline">
                                    <i class="fa fa-paste"></i> 企业认证审核</a>&nbsp;
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                    <div id="AjaxPage" style="text-align:right;"><?php echo $data; ?></div>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- 加载动画 -->


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



<script type="text/javascript" src="/static/admin/js/demo/form-advanced-demo.min.js"></script>
<script type="text/javascript">

    function status(url, id, w, h) {
        location.href=url;
    }

    //筛选
    $('#category').change(function(){
        id = $('select option:selected').val();

        location.href="/admin/attr/index?id="+id;
    });

    //更新排序
    $(function () {
        $('#add_rule').ajaxForm({
            success: complete,
            dataType: 'json'
        });

        function complete(data) {
            if (data.code === 1) {
                layer.msg(data.msg, {icon: 1, time: 1500, shade: 0.1}, function (index) {
                    window.location.href = "<?php echo url('attr/index'); ?>";
                });
            } else {
                layer.msg(data.msg, {icon: 2, time: 1500, shade: 0.1}, function (index) {
                    layer.close(index);
                    window.location.href ="<?php echo url('attr/index'); ?>";
                });
            }
        }
    });

</script>

</body>
</html>