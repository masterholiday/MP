function saveNews() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.Title = $('input[name="Title"]').val();
	params.Content = CKEDITOR.instances.Content.getData();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/News/save',
		data: params,
		success: function(data, textStatus){processSaveNews(data);},
		dataType: 'json'
	});
}

function processSaveNews(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}
$(function(){
	
	function showLoader(){
		$("#loader_bg").show();
		$("#loader_div").show();
	}
	
	$(".del_news").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить новость з заголовком '+name+'?')){
			showLoader();
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-manager/news/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveNews(data);},
				dataType: 'json'
			});
		}
	})
	
	$(".state").click(function(){
		showLoader();
		_ = $(this).attr('id');
		arID = _.split('-');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-manager/news/state-changer',
			data: {id:id},
			success: function(data, textStatus){processSaveNews(data);},
			dataType: 'json'
		});		
	})
	
	$(".enable_all").click(function(){
		flag = $(this).attr('checked');
		if(!flag){
			$(".news_ch").attr('checked', false);
			$(".enable_all").attr('checked', false);
		}else{
			$(".news_ch").attr('checked', 'checked');
			$(".enable_all").attr('checked', 'checked');
		}
	})	
	
	$("#group_delete").click(function(){
		var arrCheckbox = [];
		$('.news_ch:checked').each(function(index) {
		    arrCheckbox.push($(this).attr('name'));
		});
		if (arrCheckbox.length != 0){
			if(confirm('Вы действительнно хотите удалить выбраные новости?')){
				showLoader();
				$.ajax({
					type: 'POST',
					url: HTTP_HOST + '/site-manager/news/grouped-delete',
					data: {'arDelete':arrCheckbox},
					success: function(data, textStatus){processSaveNews(data);},
					dataType: 'json'
				});
			}
		}
	})
	
})

