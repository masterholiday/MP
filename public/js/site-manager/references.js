function deleteReference(id) {
	if (confirm('Вы точно хотите удалить отзыв?')) {
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-manager/references/delete',
			data: {id:id},
			success: function(response, textStatus){
				window.location.reload();
			},
			dataType: 'json'
		});	
	}
}

function changeApproved(id) {
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/references/change-approved',
		data: {id:id},
		success: function(response, textStatus){
			window.location.reload();
		},
		dataType: 'json'
	});	
}

function saveReference() {
	var params = {};
	params.name = jQuery.trim($('input[name="Name"]').val()); 
	params.email = jQuery.trim($('input[name="Email"]').val()); 
	params.reference = jQuery.trim($('textarea[name="Reference"]').val());
	params.id = $('input[name="ID"]').val();
	
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/references/save',
		data: params,
		success: function(response, textStatus){
			if (typeof(response.error) == 'undefined') window.location.href = HTTP_HOST + '/site-manager/references';
			else {
				alert(response.error);
				return;
			}
		},
		dataType: 'json'
	});		
}