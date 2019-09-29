<?php /*a:3:{s:76:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\issue\add_edit.html";i:1569382414;s:75:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\layout\header.html";i:1569059483;s:75:"D:\phpstudy_pro\WWW\www.alaer.com\application\admin\view\layout\footer.html";i:1569388136;}*/ ?>
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
    <link rel="stylesheet" type="text/css" href="/static/admin/css/demo/webuploader-demo.min.css">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo !empty($data) ? '编辑内容' : '新增内容'; ?></h5>
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
                          action="<?php echo !empty($data) ? '/admin/issue/update' : 'save'; ?>">
                        <input type="hidden" name="id" value="<?php echo !empty($data) ? htmlentities($data['id']) : ''; ?>">
                        <?php if(empty($data)): ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">选择栏目：</label>
                            <div class="input-group col-sm-4">
                                <select class="form-control" name="cate_id" id="ding">
                                    <option value="0">默认顶级</option>
                                    <?php foreach($cate as $v): ?>
                                    <option value="<?php echo htmlentities($v['id']); ?>" ><?php echo htmlentities($v['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">标题：</label>
                            <div class="input-group col-sm-4">
                                <input id="title" type="text" class="form-control" name="title" required=""
                                       aria-required="true" value="<?php echo !empty($data) ? htmlentities($data['title']) : ''; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">电话：</label>
                            <div class="input-group col-sm-4">
                                <input id="phone" type="text" class="form-control" name="phone"
                                        value="<?php echo !empty($data) ? htmlentities($data['phone']) : ''; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">联系人：</label>
                            <div class="input-group col-sm-4">
                                <input id="contact" type="text" class="form-control" name="contact"  value="<?php echo !empty($data) ? htmlentities($data['contact']) : ''; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">简介：</label>
                            <div class="input-group col-sm-4">
                                <textarea class="form-control" rows="5" name="description" required=""
                                          aria-required="true"><?php echo !empty($data) ? (!empty($data['description'])?$data['description'] : ''):''; ?></textarea>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div id="body">
                            <?php if(isset($data)): foreach($data['con'] as $v): if($v['type']==1): ?>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?php echo htmlentities($v['name']); ?>：</label>
                                            <div class="input-group col-sm-4">
                                                <input id="name" type="text" class="form-control" name="<?php echo htmlentities($v['con_attr_value_as_id']); ?>" value="<?php echo htmlentities($v['value']); ?>">
                                            </div>
                                        </div>
                                    <div class="hr-line-dashed"></div>
                                    <?php elseif($v['type']==3): ?>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?php echo htmlentities($v['name']); ?>：</label>
                                            <div class="input-group col-sm-4">
                                                <textarea type="text" rows="10" name="<?php echo htmlentities($v['con_attr_value_as_id']); ?>" id="desc" placeholder="输入内容" class="form-control"><?php echo htmlentities($v['value']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                    <?php elseif($v['type']==4): ?>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label"><?php echo htmlentities($v['name']); ?>：</label>
                                            <div class="input-group col-sm-4" >
                                                <?php foreach($v['type_value'] as $k=>$item): ?>
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="inlineRadio<?php echo htmlentities($k); ?>" value="<?php echo htmlentities($item); ?>" name="<?php echo htmlentities($v['con_attr_value_as_id']); ?>"
                                                               <?php echo $v['value']==$item ? 'checked' : ''; ?>>
                                                        <label for="inlineRadio<?php echo htmlentities($k); ?>"> <?php echo htmlentities($item); ?> </label>
                                                    </div>
                                                <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <div class="hr-line-dashed"></div>
                                    <?php elseif($v['type']==5): ?>

                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">图片：</label>
                            <div id="uploader" class="wu-example">
                                <div class="queueList">
                                    <div id="dndArea" class="placeholder">
                                        <div id="filePicker"></div>
                                        <input type="hidden" name="pic" id="img"/>
                                        <p>或将照片拖到这里，最多上传9张</p>
                                    </div>
                                </div>
                                <div class="statusBar" style="display:none;">
                                    <div class="progress">
                                        <span class="text">0%</span>
                                        <span class="percentage"></span>
                                    </div>
                                    <div class="info"></div>
                                    <div class="btns">
                                        <div id="filePicker2"></div>
                                        <div class="uploadBtn">开始上传</div>
                                    </div>
                                </div>
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
<script type="text/javascript" src="/static/admin/js/demo/webuploader-demo.min.js"></script>
<?php if(isset($data)): ?>
            <script type="text/javascript">
                let path = '<?php echo htmlentities($data['pic']); ?>';
                $('#img').val(path);
            </script>
<?php endif; ?>
<script id="scri2"></script>
</body>


<!--<script type="text/javascript" src="/static/admin/js/demo/webuploader-demo.min.js"></script>-->
<script type="text/javascript">
    var BASE_URL = 'js/plugins/webuploader/index.html';
    $('#ding').change(function(){
        id = $("option:selected", this).val();
        if(id=='0'){
            $('#body').empty();
            return false;
        }
        ajax(id);

    });

    function ajax(id){
        $.ajax({
            type:'GET',
            url:"<?php echo url('issue/create'); ?>",
            data:{id:id},
            success:function(res){
                if(res.code===0){
                    layer.msg(res.msg, {icon: 2, time: 1500, shade: 0.1}, function (index) {
                        layer.close(index);
                    });
                }else {
                    html = '';
                    data = res.data;
                    console.log(data);
                    for (let i=0;i<data.length;i++) {
                        if(res.data[i].type==1){
                            html+='<div class="form-group">\n' +
                                '                            <label class="col-sm-3 control-label">'+res.data[i].name+'：</label>\n' +
                                '                            <div class="input-group col-sm-4">\n' +
                                '                                <input id="name" type="text" class="form-control" name="'+res.data[i].id+'" \n' +
                                '                                        value="">\n' +
                                '                            </div>\n' +
                                '                        </div>\n' +
                                '                        <div class="hr-line-dashed"></div>';
                        }else if(res.data[i].type==3){
                            html+='<div class="form-group">\n' +
                                '                            <label class="col-sm-3 control-label">'+res.data[i].name+'：</label>\n' +
                                '                            <div class="input-group col-sm-4">\n' +
                                '                                <textarea type="text" rows="10" name="'+res.data[i].id+'" id="desc" placeholder="输入内容" class="form-control"></textarea>\n' +
                                '                            </div>\n' +
                                '                        </div>\n' +
                                '                        <div class="hr-line-dashed"></div>';
                        }else if(res.data[i].type==4){
                            html+='<div class="form-group">\n' +
                                '                            <label class="col-sm-3 control-label">'+res.data[i].name+'：</label>\n' +
                                '                            <div class="input-group col-sm-4" >\n' +
                                '                            <div class="col-sm-10">\n';
                                for(let k=0;k<res.data[i].type_value.length;k++){

                                    html+='<div class="radio radio-info radio-inline">\n' +
                                        '      <input type="radio" id="inlineRadio'+k+'" name="'+res.data[i].id+'" checked  value="'+res.data[i].type_value[k]+'">\n' +
                                        '      <label for="inlineRadio'+k+'"> '+res.data[i].type_value[k]+' </label>\n' +
                                        '</div>';
                                }
                                html+='</div>\n' +
                                '                        </div>\n' +
                                '                        </div>\n' +
                                '                        <div class="hr-line-dashed"></div>';
                        }else if(res.data[i].type==5){
                            html+='';
                        }
                    }

                    $('#body').empty();
                    $('#body').append(html);
                }
            },
            error:function(err){
                layer.msg('系统错误', {icon: 2, time: 1500, shade: 0.1}, function (index) {
                    layer.close(index);
                });
            }
        })

    }

    //提交
    $(function () {
        $('#userEdit').ajaxForm({
            beforeSubmit: checkForm,
            success: complete,
            error:error,
            dataType: 'json'
        });

        function checkForm() {

        }
        function complete(data) {
            if (data.code === 1) {
                layer.msg(data.msg, {icon: 6, time: 1500, shade: 0.1}, function (index) {
                    window.location.href = "<?php echo url('issue/index'); ?>";
                });
            } else {
                layer.msg(data.msg, {icon: 5, time: 1500, shade: 0.1});
                return false;
            }
        }
        function error(err){
            console.log(err)
        }

    });
</script>

</html>