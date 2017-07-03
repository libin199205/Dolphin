
// 图片上传 预览
function imgPreview(fileDom)
{
    //判断是否支持FileReader
    if (window.FileReader) {
        var reader = new FileReader();
    } else {
        alert("您的设备不支持图片预览功能，如需该功能请升级您的设备！");
    }

    //获取文件
    var file = fileDom.files[0];
    var imageType = /^image\//;
    //是否是图片
    if (!imageType.test(file.type)) {
        alert("请选择图片！");
        return;
    }
    //读取完成
    reader.onload = function(e) {
        //获取图片dom
        var img = document.getElementById("preview");
        //图片路径设置为读取的图片
        img.src = e.target.result;

        //获取图片dom
        var alink = document.getElementById("img-like");
        //图片路径设置为读取的图片
        alink.href = e.target.result;
    };
    reader.readAsDataURL(file);
}

// 添加用户
function addUser(e)
{
    $(e).ajaxSubmit(function(data) {
        if(data.code==0){
            layer.msg(data.msg, {icon: 5}, function(){

            });
        }
        if(data.code==1){
            layer.msg(data.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
                window.location.href=data.url;
            });
        }
    })
    return false;
}

// 加载人才参数到分配
function showUser(e)
{
    var id = $(e).attr('data-id');
    $.ajax({
        url:usershow,
        data:{id:id},
        type:'POST',
        dataType:"json",
        success: function(d)
        {
            $('#sava_username').html(d.username);
            $('#save_nickname').val(d.nickname);
            $('#save_sarole').select2();
            var s = d.role_id;
                ss = s.split(",");  // 在每个逗号(,)处进行分解。
            $('#save_sarole').val(ss).trigger('change');

            $('#save_email').val(d.email);
            $('#save_mobile').val(d.mobile);

            if(d.avatar.length>0) {
                $('#img-like').attr('href',image_upload_url+d.avatar);
                var l = '<img id="preview" class="preview"  style="height: 100px;" src="'+image_upload_url+d.avatar+'"/>'
                $('#img-like').html(l)
            } else {
                $('#img-like').attr('href','javascript:;');
                var l = '<img id="preview" class="preview" src="'+image_avatar+'" style="height: 100px;"/>'
                $('#img-like').html(l)
            }
            
            $('#save_id').val(d.id);
        }
    });
    $('#talAllot').modal('show');  // 初始化后立即调用 show 方法
}

// 执行修改操作
function saveUser()
{
    $('#editUser').ajaxSubmit(function(data) {
        if(data.code==0){
            layer.msg(data.msg, {icon: 5}, function(){

            });
        }
        if(data.code==1){
            layer.msg(data.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
                window.location.href=data.url;
            });
        }
    })
    return false;
}

// 执行删除操作
function delUser(e)
{
    var id = $(e).attr('data-id');
    var name = $(e).attr('data-name');

    layer.open({
        type : 0,
        // title : '是否提交？',
        btn: ['确定', '取消'],
        icon : 3,
        closeBtn : 2,
        area: true,
        content: "是否确定删除【"+name+"】",
        scrollbar: true,
        yes: function(){

            $.ajax({
                url:userdel,
                data:{id:id},
                type:'POST',
                dataType:"json",
                success: function(e){
                    if(e.code==1){
                        layer.msg(e.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
                            window.location.href=e.url;
                        });
                    }else{
                        dialog.error(e.msg);
                    }
                }
            });

        },
    });
}


$(function(){
    // 修改状态
    $('.saveStatus').click(function()
    {
        var id    = $(this).attr('data-id');
        var value = $(this).attr('data-val');
        $.ajax({
            url:userstatus,
            data:{id:id,value:value},
            type:'POST',
            dataType:"json",
            success: function(e){
                if(e.code==1){
                    layer.msg(e.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
                        window.location.href=e.url;
                    });
                }else{
                    layer.msg(e.msg, {icon: 5,time: 1500,shade: 0.1}, function(){
                        window.location.reload();//刷新当前页面.
                    });
                }
            }
        });
    })
});
