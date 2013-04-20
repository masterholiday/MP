function doLogin() {
	var params = {login: $('#login').val(), password: $('#password').val()};
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/auth?dologin=yes',
		data: params,
		success: function(response, textStatus){
			if (response.error == 1) {
				alert(response.errorStr);
				$('#login').val('');
				$('#password').val('');
				return;
			}
			window.location.href = response.redirect;
		},
		dataType: 'json'
	});	
}