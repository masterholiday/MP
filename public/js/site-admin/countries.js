function saveCountry() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.Name = $('input[name="Name"]').val();
	params.sort = $('input[name="sort"]').val();
	params.description = $('textarea[name="description"]').val();
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.pageText = $('textarea[name="pageText"]').val();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/country/save',
		data: params,
		success: function(data, textStatus){processSaveCountry(data);},
		dataType: 'json'
	});
} 

function processSaveCountry(data) {
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
	
	$(".del_ot").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить страну '+name+'?')){
			showLoader();
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/country/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveCountry(data);},
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
			url: HTTP_HOST + '/site-admin/country/state-changer',
			data: {id:id},
			success: function(data, textStatus){processSaveCountry(data);},
			dataType: 'json'
		});		
	})

	$(".enable_all").click(function(){
		flag = $(this).attr('checked');
		if(!flag){
			$(".country_ch").attr('checked', false);
			$(".enable_all").attr('checked', false);
		}else{
			$(".country_ch").attr('checked', 'checked');
			$(".enable_all").attr('checked', 'checked');
		}
	})

	$("#group_delete").click(function(){
		
		var arrCheckbox = [];
		$('.country_ch:checked').each(function(index) {
		    arrCheckbox.push($(this).attr('name'));
		});
		if (arrCheckbox.length != 0){
			if(confirm('Вы действительнно хотите удалить выбраные страны?')){
				showLoader();
				$.ajax({
					type: 'POST',
					url: HTTP_HOST + '/site-admin/country/grouped-delete',
					data: {'arDelete':arrCheckbox},
					success: function(data, textStatus){processSaveCountry(data);},
					dataType: 'json'
				});
			}
		}
	})

})
