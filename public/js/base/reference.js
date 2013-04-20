function sendReference() {
	var params = {};
	params.name = jQuery.trim($('input[name="name"]').val()); 
	params.email = jQuery.trim($('input[name="email"]').val()); 
	params.reference = jQuery.trim($('textarea[name="reference"]').val());
	
	if (params.name == '' || params.email == '' || params.reference == '') {
		alert('Заполните все поля!');
		return;
	}
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/references/save',
		data: params,
		success: function(response, textStatus){
			if (typeof(response.error) == 'undefined') window.location.href = HTTP_HOST + '/references/add-success';
			else {
				alert(response.error);
				return;
			}
		},
		dataType: 'json'
	});	
	
}