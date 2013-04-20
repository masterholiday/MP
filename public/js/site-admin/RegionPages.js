function saveRegionPage() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.ParentID = $('select[name="region"]').val();
	params.Title = $('input[name="Title"]').val();
	params.Content = CKEDITOR.instances.Content.getData();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.Order = $('input[name="Order"]').val();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/region-page/save',
		data: params,
		success: function(data, textStatus){
			processSaveRegionPage(data);
		},
		dataType: 'json'
	});
}

function processSaveRegionPage(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}

$(function(){
	
	function showLoader(){
		$("#loader_bg").show();
	}
	
	$(".del_rp").click(function(){
		showLoader();
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить траницу '+name+'?')){
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/region-page/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveRegionPage(data);},
				dataType: 'json'
			});
		}
	})
	
	$(".page_state").click(function(){
		showLoader();
		_ = $(this).attr('id');
		arID = _.split('-');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-admin/region-page/state-changer',
			data: {id:id},
			success: function(data, textStatus){
				processSaveRegionPage(data);
			},
			dataType: 'json'
		});		
	})

	$(".enable_all").click(function(){
		flag = $(this).attr('checked');
		if(!flag){
			$(".r_page_ch").attr('checked', false);
			$(".enable_all").attr('checked', false);
		}else{
			$(".r_page_ch").attr('checked', 'checked');
			$(".enable_all").attr('checked', 'checked');
		}
	})

})