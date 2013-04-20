function processSaveObjectType(data) {
	if (data.error != 0) {
		alert(data.errorStr);
		return;
	}
	window.location.href = data.redirect;
}


function saveObjectType() {
	var params = {};
	params.ID = $('input[name="id"]').val();
	params.Name = $('input[name="Name"]').val();
	params.active = $('input[name="Active"]').is(':checked') ? 1 : 0;	
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-admin/object-type/save',
		data: params,
		success: function(data, textStatus){processSaveObjectType(data);},
		dataType: 'json'
	});
}

$(function(){
	
	function showLoader(){
		$("#loader_bg").show();
	}
	
	$(".del_ot").click(function(){
		showLoader();
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить тип объекта '+name+'?')){
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-admin/object-type/delete',
				data: {id:id},
				success: function(data, textStatus){processSaveObjectType(data);},
				dataType: 'json'
			});
		}
	})
})
