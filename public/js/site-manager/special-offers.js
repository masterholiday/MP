function saveOffer() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.Title = $('input[name="Title"]').val();
	params.startDate = $('input[name="startDate"]').val();
	params.endDate = $('input[name="endDate"]').val();
	params.Content = CKEDITOR.instances.Content.getData();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/special-offers/save',
		data: params,
		success: function(data, textStatus){processSaveOffer(data);},
		dataType: 'json'
	});
}

function processSaveOffer(data) {
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
	
	$(".del_act").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить акцию з заголовком '+name+'?')){
			showLoader();
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-manager/special-offers/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveOffer(data);},
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
			url: HTTP_HOST + '/site-manager/special-offers/state-changer',
			data: {id:id},
			success: function(data, textStatus){processSaveOffer(data);},
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
			if(confirm('Вы действительнно хотите удалить выбраные предложения?')){
				showLoader();
				$.ajax({
					type: 'POST',
					url: HTTP_HOST + '/site-manager/special-offers/grouped-delete',
					data: {'arDelete':arrCheckbox},
					success: function(data, textStatus){processSaveOffer(data);},
					dataType: 'json'
				});
			}
		}
	})
	
})

