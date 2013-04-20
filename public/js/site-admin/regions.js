function saveRegion() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.CountryID = $('select[name="CountryID"]').val();
	params.Name = $('input[name="Name"]').val();
	params.sort = $('input[name="sort"]').val();
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/region/save',
		data: params,
		success: function(data, textStatus){processSaveRegion(data);},
		dataType: 'json'
	});
} 

function processSaveRegion(data) {
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
	
	$(".del_ot").click(function(){
		showLoader();
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить регион '+name+'?')){
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/region/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveRegion(data);},
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
			url: HTTP_HOST + '/site-admin/region/state-changer',
			data: {id:id},
			success: function(data, textStatus){
				processSaveRegion(data);
				},
			dataType: 'json'
		});		
	})
	
	$(".page_state").click(function(){
		showLoader();
		_ = $(this).attr('id');
		arID = _.split('-');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-admin/country-page/page-state-changer',
			data: {id:id},
			success: function(data, textStatus){
				processSaveRegion(data);
				},
			dataType: 'json'
		});		
	})

	$(".enable_all").click(function(){
		flag = $(this).attr('checked');
		if(!flag){
			$(".region_ch").attr('checked', false);
			$(".enable_all").attr('checked', false);
			$(".c_page_ch").attr('checked', false);
		}else{
			$(".region_ch").attr('checked', 'checked');
			$(".enable_all").attr('checked', 'checked');
			$(".c_page_ch").attr('checked', 'checked');
		}
	})

	$("#group_delete").click(function(){
		var arrCheckbox = [];
		$('.region_ch:checked').each(function(index) {
		    arrCheckbox.push($(this).attr('name'));
		});
		alert(arrCheckbox.length);
		if(arrCheckbox.lenght<0){
			if(confirm('Вы действительнно хотите удалить выбраные регионы?')){
				showLoader();
				$.ajax({
					type: 'POST',
					url: HTTP_HOST + '/site-admin/region/grouped-delete',
					data: {'arDelete':arrCheckbox},
					success: function(data, textStatus){processSaveRegion(data);},
					dataType: 'json'
				});
			}
		}
	})

	$(".del_cp").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить страницу '+name+'?')){
			showLoader();
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/country-page/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveRegion(data);},
				dataType: 'json'
			});
		}
	})
	
})