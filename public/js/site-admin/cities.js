function saveCity() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.CountryID = $('select[name="CountryID"]').val();
	params.RegionID = $('select[name="RegionID"]').val();
	params.Name = $('input[name="Name"]').val();
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.pageText = $('textarea[name="pageText"]').val();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/city/save',
		data: params,
		success: function(data, textStatus){processSaveCity(data);},
		dataType: 'json'
	});
} 

function reloadRegions(country) {
	$('#RegionID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/city/reload-regions',
		data: {id:country},
		success: function(data, textStatus){showRegions(data);},
		dataType: 'json'
	});
}

function showRegions(data) {
	for (i in data.regions) $('#RegionID').append($('<option />').val(i).html(data.regions[i])); 
}

function processSaveCity(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}

$(function(){
	$(".del_ot").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить город '+name+'?')){
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/city/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveCity(data);},
				dataType: 'json'
			});
		}
	})
})