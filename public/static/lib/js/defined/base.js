
$(function()
{
	// 重置
	var url = jsreset;
	$('button[type="reset"]').click(function(){location.href=url})

	/****************全选或取消****************/
	var checked=false;
	$('#check-all').click(function(){
		checked=!checked;
		$('.table').find('.ids:checkbox').each(function(index, element) {
			this.checked=checked;
		});
	});
	/*****************************************/
});