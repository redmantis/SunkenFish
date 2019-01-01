function updatecache(){
	$.ajax({
		type: 'get',
		url: '/home/index/updatecache',
		timeout: 5000,
		data: {id: bookid, hash: hash},
		dataType: 'json',
		success: function(data){
			if(data.status == 'error'){
				layer.msg('已是最新章节，暂无更新！');
			}
			$('#loadingtip').html('（提示：最新章节抓取成功！）');
			if(data.status == 'success'){
				layer.msg('最新章节抓取成功！');
				var newlisthtml='';
				$.each(data.content, function(i, item){
					if(item.title){
						newlisthtml += '<dd><a href="'+item.rewriteurl+'" target="_blank" title="'+item.title+'">'+item.title+'</a></dd>';
					}
				});
				$('#newchapter').html(newlisthtml);
			}
		},
		complete : function(xhr,status){
			if(status == 'timeout'){
				$('#loadingtip').html('（提示：抓取超时，网络繁忙，请刷新后重试！）');
				layer.msg('抓取超时，网络繁忙，请稍后重试！');
			}
		}
	});
}
$(function() {
	if(typeof(bookid) != "undefined"){
		setTimeout(updatecache, 2000);
	}
});
