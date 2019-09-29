<?php /*a:3:{s:73:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\ad\add_edit.html";i:1569404740;s:75:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\layout\header.html";i:1569059483;s:75:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\layout\footer.html";i:1569388136;}*/ ?>
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

    <link href="/static/admin/js/plugins/webuploader/webuploader.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo !empty($data) ? '编辑广告' : '新增广告'; ?></h5>
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
                          action="<?php echo !empty($data) ? '/admin/banner/update' : 'save'; ?>">
                        <input type="hidden" name="id" value="<?php echo !empty($data) ? htmlentities($data['id']) : ''; ?>">
                        <div class="form-group">
                        <label class="col-sm-3 control-label">选择广告位置：</label>
                        <div class="input-group col-sm-4">
                        <select class="form-control" name="type" id="ding">
                        <option value="0" <?php echo !empty($data) ? ($data==0?'selected' : ''):''; ?>>首页</option>
                        <?php foreach($category as $v): ?>
                        <option value="<?php echo htmlentities($v['id']); ?>" <?php echo !empty($data) ? ($data['type']==$v['id']?'selected' : ''):''; ?> ><?php echo htmlentities($v['title']); ?></option>
                        <?php endforeach; ?>
                        </select>
                        </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">栏目图标：</label>
                            <div class="input-group col-sm-4">
                                <input type="hidden" id="data_photo" name="pic" value="<?php echo !empty($data) ? htmlentities($data['pic']) : ''; ?>"/>
                                <div id="fileList" class="uploader-list"></div>
                                <div id="imgPicker" style="float:left">选择图片</div>
                                <img id="img_data" height="100px" width="100px"
                                     style="float:left;margin-left: 50px;margin-top: -10px;"
                                     src="<?php echo !empty($data) ? htmlentities($data['pic']) : ''; ?>" onerror="this.src='/static/admin/img/no_img.jpg'"/>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
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



<script type="text/javascript" src="/static/admin/js/plugins/webuploader/webuploader.min.js"></script>

<script type="text/javascript">
    var $list = $('#fileList');
    //上传图片,初始化WebUploader
    var uploader = WebUploader.create({

        auto: true,// 选完文件后，是否自动上传。
        swf: '/static/admin/js/plugins/webupload/Uploader.swf',// swf文件路径
        server: "<?php echo url('upload/upload'); ?>",// 文件接收服务端。
        duplicate: true,// 重复上传图片，true为可重复false为不可重复
        pick: '#imgPicker',// 选择文件的按钮。可选。

        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png'
        },

        'onUploadSuccess': function (file, data, response) {
            $("#data_photo").val(data);
            $("#img_data").attr('src', data).show();
        }
    });

    uploader.on('fileQueued', function (file) {
        $list.html('<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '</h4>' +
            '<p class="state">正在上传...</p>' +
            '</div>');
    });

    // 文件上传成功
    uploader.on('uploadSuccess', function (file) {
        $('#' + file.id).find('p.state').text('上传成功！');
    });

    // 文件上传失败，显示上传出错。
    uploader.on('uploadError', function (file) {
        $('#' + file.id).find('p.state').text('上传出错!');
    });

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
                    window.location.href = "<?php echo url('banner/index'); ?>";
                });
            } else {
                layer.msg(data.msg, {icon: 5, time: 1500, shade: 0.1});
                return false;
            }
        }

    });
</script>
</html>