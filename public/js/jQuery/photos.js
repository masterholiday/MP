function uploadPhoto(title, descr) {
	//showLoading();
	$.ajaxFileUpload(
			{
				url: HTTP_HOST + '/upload-file',
				secureuri:false,
				fileElementId:'newfile',
				dataType: 'json',
				data: {title: title, description: descr},
				success: function (oResponse, status)
					{	
						if (oResponse.error != 0) {
							alert(oResponse.errorStr);
							hideLoading();
							return;
						}
						movePhotoToCategory($('input[name="ID"]').val(), oResponse.fileID);
					},
				error: function (data, status, e)
					{
						hideLoading();
						alert(e);
					}
				}
			);
	return false;
}

function movePhotoToCategory(catID, fileID) {
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/manage-village/photogallery/add-photo',
		data: {category: catID, file: fileID},
		success: function(data, textStatus){if (typeof(data.success) != 'undefined') showSuccess(data.success); hideLoading(); showPhoto(data.photo);},
		dataType: 'json'
	});
}

function showPhoto(photo) {
	var md = $('<div />').addClass('phgp').attr('id', 'photo_' + photo.ID);
	md.append($('<input type="hidden" name="phid" />').val(photo.ID));
	md.append($('<div />').addClass('photo').append($('<img />').attr('src', HTTP_HOST + '/api/image/show?id=' + photo.ID + '&w=150')));
	md.append($('<div />').addClass('description').append($('<p />').html('<b>' + photo.title + '</b>')).append('<br />').append('<p>' + photo.description + '</p>'));
	md.append($('<div />').addClass('order').append('<input type="text" name="order[' + photo.ID + ']" class="order" value="' + photo.order + '" />'));
	md.append($('<div />').addClass('default').append('<input type="radio" value="' + photo.ID + '" name="default" ' + (photo['default'] == 1 ? 'checked="checked" ' : '') + '/>'));
	md.append($('<div />').addClass('enabled').append('<input type="checkbox" name="enabled[' + photo.ID + ']" ' + (photo.enabled == 1 ? 'checked="checked" ' : '') + '/>'));
	md.append($('<div />').addClass('actions')
		.append('<a href="javascript:;" onclick="editPhoto(' + photo.ID + ');"><img src="' + HTTP_HOST + '/images/manage-village/edit.png" alt="" /></a>')
	);
	md.append('<div class="clear"></div>');
	$('#photo_0').after(md);
	$('#newfile').val('');
	$('#title').val('');
	$('#description').val('');
}

function saveOrdering() {
	var _orders = {};
	$('input.order').each(function(){
		_orders[$(this).attr('name')] = $(this).val();
	});
	_orders.category = $('#catid').val();
	showLoading();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/manage-village/photogallery/save-photos-order',
		data: _orders,
		success: function(data, textStatus){hideLoading(); window.location.reload();},
		dataType: 'json'
	});
}

function saveDafault() {
	showLoading();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/manage-village/photogallery/save-photos-default',
		data: {id: $('input[name="default"]:checked').val(), category: $('#catid').val()},
		success: function(data, textStatus){hideLoading();},
		dataType: 'json'
	});
}

function saveEnabled() {
	var _enabled = {};
	$('div.enabled input').each(function(){
		_enabled[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
	});
	_enabled.category = $('#catid').val();
	showLoading();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/manage-village/photogallery/save-photos-enabled',
		data: _enabled,
		success: function(data, textStatus){hideLoading();},
		dataType: 'json'
	});
}

function editPhoto(id) {
	var title = $('#photo_' + id + ' div.description p:eq(0) b').html();
	var description = $('#photo_' + id + ' div.description p:eq(1)').html();
	showLoading();
	var div = $('<div />').attr('id', 'photoedit').addClass('photoedit').css({
		'left': $(document).width() / 2 - 200 + 'px',
		'top': $(document).scrollTop() + 30 + 'px'
	});
	div.append('<div class="f">_EDIT_: <input name="edit_ph_title" /></div>');
	div.append('<div class="f"><span>_DESCRIPTION_:</span> <textarea name="edit_ph_description"></textarea></div>');
	$('body').append(div);
	$('input[name="edit_ph_title"]').val(title);
	$('textarea[name="edit_ph_description"]').val(description).cleditor();
	div.append('<div class="f"><input type="button" value="_OK_" onclick="savePhoto(' + id + ');" /> <input type="button" value="_CANCEL_" onclick="$(\'#photoedit\').remove();hideLoading();" /></div>');
}

function savePhoto(id) {
	var params = {id: id, category: $('#catid').val(), title: $('input[name="edit_ph_title"]').val(), description: $('textarea[name="edit_ph_description"]').val()};
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/manage-village/photogallery/save-photo-info',
		data: params,
		success: function(data, textStatus){if (data.error != 0) {alert(data.errorStr); return;} $('#photoedit').remove(); hideLoading(); $('#photo_' + id + ' div.description p:eq(0) b').html(params.title); $('#photo_' + id + ' div.description p:eq(1)').html(params.description);},
		dataType: 'json'
	});
}