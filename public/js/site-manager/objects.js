var FlashUploadScriptURL = '';

function reloadRegions(country) {
	$('#RegionID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/object/reload-regions',
		data: {id:country},
		success: function(data, textStatus){showRegions(data);},
		dataType: 'json'
	});
}

function showRegions(data) {
	for (i in data.RegionID) $('#RegionID').append($('<option />').val(i).html(data.RegionID[i])); 
}

function reloadCities(region) {
	$('#CityID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/object/reload-cities',
		data: {id:region},
		success: function(data, textStatus){showCities(data);},
		dataType: 'json'
	});
}

function showCities(data) {
	for (i in data.cities) $('#CityID').append($('<option />').val(i).html(data.cities[i])); 
}

function reloadLocations(region) {
	$('#LocationID').children().remove();
	$.ajax({
		type: 'POST',
		url: HTTP_HOST + '/site-manager/object/reload-locations',
		data: {id:region},
		success: function(data, textStatus){showLocations(data);},
		dataType: 'json'
	});
}

function showLocations(data) {
	for (i in data.locations) $('#LocationID').append($('<option />').val(i).html(data.locations[i])); 
}

function processSaveObject(data) {
	if(data.error == 2){
		window.location.reload();
	}else {
		if (data.error != 0) {
			alert(data.errorStr);
			return;
		}
		window.location.href = data.redirect;
	}
}

function showLoader(){
	$("#loader_bg").show();
	$("#loader_div").show();
}


function initLoader(session) {
    $('#mm_file').uploadify({
        'uploader'  : HTTP_HOST + '/tools/uploadify.swf',
        'buttonImg' : HTTP_HOST + '/images/controll/upload_button.png',
        'script'    : FlashUploadScriptURL + '/site-manager/object/upload-files',
        'cancelImg' : HTTP_HOST + '/images/controll/cancel.png',
        'folder'    : '',
        'scriptData': {sessid: session, id: $('#objid').val()},
        'multi'     : true,
        'auto'      : true,
        'fileExt'   : '*.png;*.jpg;*.jpeg;*.gif',
        'fileDesc'  : 'Изображения',
        'width'		: 150,
        'height'	: 21,
        'onComplete': function(event, ID, fileObj, response, data) {
            var resp = {};
            eval('resp = ' + response + ';');
            if (typeof(resp.id) == 'undefined') {alert('Ошибка!'); return;}
            var newimdiv = $(document.createElement('div')).addClass('preview_img_div').attr('id', 'object_image_cont_' + resp.id);
            var delimg = $(document.createElement('img')).attr('src', HTTP_HOST + '/images/controll/close.png').addClass('delete_object_image').attr('id', resp.id).attr('rel', resp.name).attr('alt', '').attr('title', 'Удалить фотографию');
            delimg.click(function(){
                deleteImage(resp.id, resp.name);
            });
            var mainimg = $(document.createElement('img')).attr('src', HTTP_HOST + '/images/controll/red-flag.png').addClass('main_page_image').attr('id', resp.id).attr('rel', resp.name).attr('alt', '').attr('title', 'Главная фотография');
            mainimg.click(function(){
                makeMainImage(resp.id);
            });
            var img = $(document.createElement('img')).attr('src', resp.src).addClass('object_image');
            newimdiv.append(delimg).append(mainimg).append(img);
            $('#object_images').append(newimdiv);
        }
    });

}

function deleteImage(id, name) {
    if(confirm('Вы действительно хотите удалить картинку '+name+'?')){
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/site-manager/object/delete-object-image',
            data: {id:id, parent:$("input[name=ID]").val()},
            success: function(data, textStatus){
                $('#object_image_cont_' + id).remove();
                if (typeof(data.findactive) != 'undefined' && data.findactive == 1 && $('#object_images .preview_img_div:first').length > 0) {
                    var idd = $('#object_images .preview_img_div:first').attr('id').replace('object_image_cont_', '');
                    makeMainImage(idd);
                }
            },
            dataType: 'json'
        });
    }

}

function makeMainImage(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/object/set-main-page',
        data: {id:id, parent:$("input[name=ID]").val()},
        success: function(data, textStatus){
            $('.main_page_image').each(function(){
                var src = $(this).attr('src').replace('green-flag', 'red-flag');
                $(this).attr('src', src);
            });
            var src = $('#object_image_cont_' + id + ' .main_page_image').attr('src').replace('red-flag', 'green-flag');
            $('#object_image_cont_' + id + ' .main_page_image').attr('src', src);
        },
        dataType: 'json'
    });

}


$(function(){

	function resetPopUp(){
		$("#data_popup_main_wrap").html('');
		$("#data_popup_title").html('');
	}
	
	$(".del_o").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
		if(confirm('Вы действительно хотите удалить объект '+name+'?')){
			showLoader()
			$.ajax({
				type: 'POST',
				url: HTTP_HOST + '/site-manager/object/delete',
				data: {id:id},
				success: function(data, textStatus){
					processSaveObject(data);
				},
				dataType: 'json'
			});
		}
	})

	$(".state1").click(function(){
		showLoader();
		_ = $(this).attr('id');
		arID = _.split('-');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-manager/object/state-changer',
			data: {id:id},
			success: function(data, textStatus){processSaveObject(data);},
			dataType: 'json'
		});		
	})
	
	$(".enable_all").click(function(){
		flag = $(this).attr('checked');
		if(!flag){
			$(".object_ch").attr('checked', false);
			$(".enable_all").attr('checked', false);
		}else{
			$(".object_ch").attr('checked', 'checked');
			$(".enable_all").attr('checked', 'checked');
		}
	})	
	
	$(".delete_object_image").click(function(){
		var id = $(this).attr('id');
		var name = $(this).attr('rel');
        deleteImage(id, name);
	})
	
	$(".main_page_image").click(function(){
		var id = $(this).attr('id');
        makeMainImage(id);
	})
	
	$(".spec_offer_btn").click(function(){
		_ = $(this).attr('id');
		arID = _.split('_');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-manager/object/show-object-special-offers',
			data: {id:id},
			success: function(data){
				$("#data_popup_title").html('Выберите акцию');
				$("#data_popup_main_wrap").html(data.data);
				$("#loader_bg").show();
				$("#data_popup_bg").show();
				$("#save_special_offers_for_object").click(function(){
					var arrCheckbox = [];
					$('.sp_of:checked').each(function(index) {
					    arrCheckbox.push($(this).attr('name'));
					});
					$.ajax({
						type: 'POST',
						url: HTTP_HOST + '/site-manager/object/set-object-special-offers',
						data: {id:id, arrCheckbox:arrCheckbox},
						success: function(data, textStatus){
							resetPopUp();
							processSaveObject(data);
						},
						dataType: 'json'
					});
				})				
			},
			dataType: 'json'
		});
	})
	
	$(".same_obects").click(function(){
		_ = $(this).attr('id');
		arID = _.split('_');
		var id = arID[1];
		$.ajax({
			type: 'POST',
			url: HTTP_HOST + '/site-manager/object/show-same-objects',
			data: {id:id},
			success: function(data){
				$("#data_popup_title").html('Введите код сопутствующих объектов');
				$("#data_popup_main_wrap").html(data.data);
				$("#loader_bg").show();
				$("#data_popup_bg").show();
				$("#save_same_objects_button").click(function(){
					var sameArr = $("#same_objects_field").val();
					$.ajax({
						type: 'POST',
						url: HTTP_HOST + '/site-manager/object/set-same-objects',
						data: {id:id, sameArr:sameArr},
						success: function(data, textStatus){
							resetPopUp();
							processSaveObject(data);
						},
						dataType: 'json'
					});
				})
			},
			dataType: 'json'
		});
	})
	
	$("#group_delete").click(function(){
		var arrCheckbox = [];
		$('.object_ch:checked').each(function(index) {
		    arrCheckbox.push($(this).attr('name'));
		});
		if (arrCheckbox.length != 0){
			if(confirm('Вы действительнно хотите удалить выбраные объекты?')){
				showLoader();
				$.ajax({
					type: 'POST',
					url: HTTP_HOST + '/site-manager/object/grouped-delete',
					data: {'arDelete':arrCheckbox},
					success: function(data, textStatus){processSaveObject(data);},
					dataType: 'json'
				});
			}
		}
	})
	
})


function showAddSpecForm() {
    if (!$('#obj_spec_add_fields').length) return;
    var visible = $('#obj_spec_add_fields').height() > 0;
    if (!visible) {
        $('#obj_spec_add_fields').show();
        $('#obj_spec_add_fields').animate({height: '185px'}, "slow");
    }
    else {
        $('#obj_spec_add_fields').animate({height: '0px'}, "slow", function(){$('#obj_spec_add_fields').hide(); $('#obj_spec_add_fields input').val(''); $('#obj_spec_add_fields textarea').val(''); $('input[name="new_spec_id"]').val(0);});
    }
}

function addObjectSpec() {
    var oid = parseInt($('#add_new_object form input[name="ID"]').val());
    if (!oid) {
        alert('Сначала сохраните объект!');
        return;
    }
    var parname = $('#obj_spec_add_fields input[name="new_spec_name"]').val();
    var parval = $('#obj_spec_add_fields textarea[name="new_spec_val"]').val();
    var id = $('input[name="new_spec_id"]').val();
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/object/save-special-property',
        data: {oid:oid, parname:parname, parval:parval, id:id},
        success: function(data){
            if (typeof(data.error) != 'undefined') {
                alert(data.error);
                return;
            }
            if (data['new'] == 0) {
                $('#prop' + data.ID + ' td:eq(0)').html(data.PropName);
                $('#prop' + data.ID + ' td:eq(1)').html(data.PropValue);
            }
            else {
                if ($('table.object_add_spec_list').length < 1) {
                    var t = $(document.createElement('table'));
                    t.attr('cellpadding', 0);
                    t.attr('cellspacing', 0);
                    t.attr('width', '100%');
                    t.attr('border', '0');
                    t.addClass('object_add_spec_list');
                    $('#obj_spec_add_fields').after(t);
                }
                var tr = $(document.createElement('tr'));
                var td1 = $(document.createElement('td'));
                var td2 = $(document.createElement('td'));
                var td3 = $(document.createElement('td')).addClass('buttons');
                var td4 = $(document.createElement('td')).addClass('buttons');
                td1.html(data.PropName);
                td2.html(data.PropValue);
                td3.append($(document.createElement('a')).css('margin-bottom', '-3px').addClass('sort_up').click(function(){sortSPropUp(data.ID);}));
                td3.append($(document.createElement('a')).addClass('sort_down').click(function(){sortSPropDown(data.ID);}));
                td4.append($(document.createElement('a')).addClass('edit').click(function(){editSProperty(data.ID);}));
                td4.append($(document.createElement('a')).addClass('delete').click(function(){deleteSProperty(data.ID);}));
                tr.append(td1).append(td2).append(td3).append(td4);
                tr.attr('id', 'prop' + data.ID);
                $('table.object_add_spec_list').append(tr);
            }
            $('#obj_spec_add_fields').animate({height: '0px'}, "slow", function(){$('#obj_spec_add_fields').hide(); $('#obj_spec_add_fields input').val(''); $('#obj_spec_add_fields textarea').val(''); $('input[name="new_spec_id"]').val(0);});
        },
        dataType: 'json'
    });
}

function editSProperty(id) {
    var visible = $('#obj_spec_add_fields').height() > 0;
    if (!visible) {
        $('#obj_spec_add_fields').show();
        $('#obj_spec_add_fields').animate({height: '185px'}, "slow");
    }
    
    $('#obj_spec_add_fields input').val($('#prop' + id + ' td:eq(0)').html()); 
    $('#obj_spec_add_fields textarea').val($('#prop' + id + ' td:eq(1)').html());
    $('input[name="new_spec_id"]').val(id);
}

function deleteSProperty(id) {
    if(confirm('Вы действительно хотите удалить параметр?')){
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/site-manager/object/delete-special-property',
            data: {id:id},
            success: function(data, textStatus){
                $('#prop' + id).remove();
            },
            dataType: 'json'
        });
    }
}

function sortSPropUp(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/object/sort-special-property',
        data: {id:id, direction: 'up'},
        success: function(data, textStatus){
            var prev = $('#prop' + id).prev();
            if (prev.length > 0) {
                prev.before($('#prop' + id).detach());
            }
        },
        dataType: 'json'
    });
}

function sortSPropDown(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/object/sort-special-property',
        data: {id:id, direction: 'down'},
        success: function(data, textStatus){
            var next = $('#prop' + id).next();
            if (next.length > 0) {
                next.after($('#prop' + id).detach());
            }
        },
        dataType: 'json'
    });
}

