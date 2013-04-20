function saveCountryPage() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.ParentID = $('select[name="ParentID"]').val();
	params.Title = $('input[name="Title"]').val();
	params.Content = CKEDITOR.instances.Content.getData();
	params.active = $('input[name="active"]').is(':checked') ? 1 : 0;
	params.seoTitle = $('textarea[name="seoTitle"]').val();
	params.seoKeywords = $('textarea[name="seoKeywords"]').val();
	params.seoDescription = $('textarea[name="seoDescription"]').val();
	params.Order = $('input[name="Order"]').val();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/country-page/save',
		data: params,
		success: function(data, textStatus){processSaveCountryPage(data);},
		dataType: 'json'
	});
}

function processSaveCountryPage(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}