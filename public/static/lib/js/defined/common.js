// 启用 禁止 删除
$(function(){
	$('.ajaxStatus').click(function()
	{
		var val = $(this).attr('data-val');
		var ids = $("input[name='ids[]']").map(function(){
		if(this.checked==true)
			return $(this).val();
		}).get().join(',');

		if(ids==''){
			dialog.error('你还没有进行选择！');
			return false;
		}

		layer.open({
			type : 0,
			title : '是否确定？',
			btn: ['确定', '取消'],
			icon : 3,
			closeBtn : 2,
			area: true,
			content: "您确定进行当前该操作吗？",
			scrollbar: true,
			yes: function(){

			    $.ajax({
					url:saveClick,
					data:{val:val,ids:ids},
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

	});
});