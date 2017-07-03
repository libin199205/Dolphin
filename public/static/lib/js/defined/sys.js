// 角色管理页面
$(function(){
	// 修改状态
	$('.saveStatus').click(function()
	{
		var id    = $(this).attr('data-id');
		var value = $(this).attr('data-val');
		var field = $(this).attr('data-field');
		$.ajax({
			url:accstatus,
			data:{id:id,value:value,field:field},
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


// 角色添加
function addRole()
{
	$('#addRole').ajaxSubmit(function(data) {
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

// 显示角色信息
function showRole(e)
{
	var id = $(e).attr('data-id');
	$.ajax({
		url:roleshow,
		data:{id:id},
		type:'POST',
		dataType:"json",
		success: function(d)
		{
			$('#save_name').val(d.name);
			$('#save_description').val(d.description);
			// 是否可登录后台
			if(d.access=='1')
			{
				$('.save_access').prop('checked', true);
			}
			else
			{
				$('.save_access').prop('checked', false);
			}

			// 状态
			if(d.status=='1')
			{
				$('.save_status').prop('checked',true);
			}
			else
			{
				$('.save_status').prop('checked',false);
			}
			$('#save_id').val(d.id);
		}
	});
	$('#Rolesave').modal('show');  // 初始化后立即调用 show 方法
}

// 执行修好角色信息
function saveRole()
{
	$('#editRole').ajaxSubmit(function(data) {
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
function delRole(e)
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
				url:roledel,
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



// 授权管理页面
$(function(){
	// 全选
    $('#checkall').click(function () {
        $('input[name="power[]"]').prop('checked', true);
    });

    // 取消
    $('#checknull').click(function () {
        $('input[name="power[]"]').each(function () {
            $(this).prop('checked', false);
        });
    });

    //点击父级选择子集全部
	$('.checkall:checkbox').click(function()
	{
		if(this.checked===true)
		{
			$(this).parents('.module').find('div input:checkbox').prop('checked',true);
		}
		else
		{
			$(this).parents('.module').find('div input:checkbox').prop('checked',false);
		}
	});

	//选择子集，默认选中父级，子集全部取消，父级默认取消
	$('.limit').click(function()
	{
		var checkValue=$(this).parents('.module').find('input.limit:checked').val();	
		// 检查是否有被选择
		if(checkValue)
		{
			$(this).parents('.module').find('label.pr  input').prop("checked", true);
		}
		else
		{
			$(this).parents('.module').find('label.pr input').prop("checked", false);
		}
	});

	// selsect框选中
	$("#selecteds").change(function(){
		var id = $(this).val();
		var n = $("#selecteds option:selected").attr('data-name');
		var i = $("input[name='power[]']").prop('checked',false);
			$('.power-title').html('【'+n+'】');
			$('#role_id').val(id);
			var ac = $('#clist').find('ul li a').removeClass('active');
				ac = $(".cd_"+id).addClass('active');
		$.ajax({
			url:showPower,
			type:"POST",
			data:{id:id},
			dataType:"json",
			success: function(d){
			    var items = d.power.split(',');
				$.each(items, function (index, item) {
					$("input[name='power[]']").each(function () {
						if ($(this).val() == item)
						{
							i = $(this).prop("checked",true);
							return false;
						}
				    });
				});
			}
		});
		return false;
	});
});

// 角色选中
function isCheck(value)
{
	$("#selecteds").select2('val',value);
}


// 授权操作
function savePower()
{
	$('#editPower').ajaxSubmit(function(data) {
		if(data.code==0){
			layer.msg(data.msg, {icon: 5}, function(){

			});
		}
		if(data.code==1){
			layer.msg(data.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
				// window.location.href=data.url;
			});
		}
	})
	return false;
}