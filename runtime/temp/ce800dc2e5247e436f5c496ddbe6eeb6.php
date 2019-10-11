<?php /*a:3:{s:71:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\user\auth.html";i:1569738322;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\header.html";i:1570606922;s:75:"D:\phpstudy_pro\WWW\www.361kg.com\application\admin\view\layout\footer.html";i:1569388136;}*/ ?>
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
<link rel="shortcut icon" href="<?php echo url('/favicon.ico','',''); ?>">
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

    <link href="/static/admin/js/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/switchery/switchery.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo !empty($person) ? '个人认证审核' : '企业认证审核'; ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="form_basic.html#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" name="userEdit" id="userEdit" method="post"
                          action="<?php echo !empty($person) ? '/admin/user/person_status_update' : '/admin/user/company_status_update'; ?>">
                        <input type="hidden" name="id" value="<?php echo !empty($person) ? htmlentities($person['id']) : htmlentities($company?$company['id']:''); ?>">
                        <?php if(isset($person)): ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">姓名：</label>
                            <div class="input-group col-sm-4">
                                <p><?php echo htmlentities($person['person_auth_name']); ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证号码：</label>
                            <div class="input-group col-sm-4">
                                <p><?php echo htmlentities($person['person_auth_id_card']); ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证正面：</label>
                            <div class="input-group col-sm-4">
                                <a class="fancybox" href="<?php echo htmlentities($person['person_auth_pic_front']); ?>" title="正面">
                                    <img alt="image" src="<?php echo htmlentities($person['person_auth_pic_front']); ?>" />
                                </a>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证背面：</label>
                            <div class="input-group col-sm-4">
                                <a class="fancybox" href="<?php echo htmlentities($person['person_auth_pic_rear']); ?>" title="北面">
                                    <img alt="image" src="<?php echo htmlentities($person['person_auth_pic_rear']); ?>" />
                                </a>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">审核：</label>
                            <div class="input-group col-sm-4">
                                不通过
                                <input type="checkbox" name="person_status" class="js-switch" checked/>
                                通过
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div id="person_reason">

                        </div>
                        <?php endif; if(isset($company)): ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">认证企业名称：</label>
                            <div class="input-group col-sm-4">
                                <p><?php echo htmlentities($company['company_auth_name']); ?></p>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">工商营业执照：</label>
                            <div class="input-group col-sm-4">
                                <a class="fancybox" href="<?php echo htmlentities($company['company_auth_pic']); ?>" title="北面">
                                    <img alt="image" src="<?php echo htmlentities($company['company_auth_pic']); ?>" />
                                </a>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">审核：</label>
                            <div class="input-group col-sm-4">
                                不通过
                                <input type="checkbox" name="company_status" class="js-switch" checked/>
                                通过
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div id="person_reason">

                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-danger" href="javascript:history.go(-1);"><i class="fa fa-close"></i>
                                    返回</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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



<script src="/static/admin/js/plugins/fancybox/jquery.fancybox.js"></script>
<script src="/static/admin/js/plugins/switchery/switchery.js"></script>
<script type="text/javascript">
    var elem = document.querySelector('.js-switch');
    var init = new Switchery(elem);

    elem.onchange = function(){
        if(elem.checked){
            $('#person_reason').empty();
        }else{
            html='<div class="form-group">\n' +
                '                            <label class="col-sm-3 control-label">不通过原因：</label>\n' +
                '                            <div class="input-group col-sm-4">\n' +
                '                                <textarea class="form-control" rows="4" placeholder="简短输入审核不通过原因" name="<?php echo !empty($person) ? 'person_reason' : 'company_reason'; ?>" required=""\n' +
                '                                          aria-required="true"></textarea>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                        <div class="hr-line-dashed"></div>';
            $('#person_reason').append(html);
        }
    }

    $(document).ready(function(){$(".fancybox").fancybox({
        type:'iframe',
        openEffect:"none",
        closeEffect:"none",
        width:'80%',
        height:'60%'
    })});
    //提交
    $(function () {
        $('#userEdit').ajaxForm({
            beforeSubmit: checkForm,
            success: complete,
            dataType: 'json'
        });

        function checkForm() {

        }


        function complete(data) {
            if (data.code === 1) {
                layer.msg(data.msg, {icon: 6, time: 1500, shade: 0.1}, function (index) {
                    window.location.href = "<?php echo url('user/index'); ?>";
                });
            } else {
                layer.msg(data.msg, {icon: 5, time: 1500, shade: 0.1});
                return false;
            }
        }

    });
</script>
</html>