var requestedPhoneEventor, requestedPhoneService;

$('.reqphone').live('click', function(){
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/request-call/',
        data: {id:requestedPhoneEventor, sid:requestedPhoneService},
        success: function(data){
            if (typeof(data.error) == 'undefined') {
                $('.reqphone').removeClass('reqphone').addClass('reqphone2').html('Вы уже дали запрос на звонок. Ожидайте.');
                showNotification('Запрос выслан. Вам перезвонят на телефон ' + data.phone);
            }
            else {
                if (data.error == 'login') {
                    showLoginPopup();
                    return;
                }
                if (data.error == 'phone') {
                    showPhoneNumberPopupE();
                    return;
                }
                showError(data.error);
            }
        },
        dataType: 'json'
    });
});


$('.reqstar').live('click', function(){
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/add-star/',
        data: {id:requestedPhoneEventor, sid:requestedPhoneService},
        success: function(data){
            if (typeof(data.error) == 'undefined') {
                $('.reqstar').removeClass('reqstar').addClass('reqstar2').html('Этот ивентор уже находится в избранном.<br />Нажмите если хотите убрать.');
                showNotification("Ивентор добавлен в избранное");
            }
            else {
                if (data.error == 'login') {
                    showLoginPopup();
                }
                else {
                    showError(data.error);
                }
            }
        },
        dataType: 'json'
    });
});

$('.reqstar2').live('click', function(){
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/remove-star/',
        data: {id:requestedPhoneEventor, sid:requestedPhoneService},
        success: function(data){
            if (typeof(data.error) == 'undefined') {
                $('.reqstar2').removeClass('reqstar2').addClass('reqstar').html('Нажмите, если хотите добавить ивентора себе в избранное.');
                showNotification("Ивентор удален из избранного");
            }
            else {
                if (data.error == 'login') {
                    showLoginPopup();
                }
                else {
                    showError(data.error);
                }
            }
        },
        dataType: 'json'
    });
});



$('.delete a').live('click', function(e){

    var p = {};
    p.id = $(this).attr('rel');
    p.page = currentPortfolioPage;

    noty({
        text: 'Вы точно хотите удалить?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/iventor/del-portfolio/',
                        data: p,
                        success: function(data, textStatus){
                            $('#portfolio .pphoto').remove();
                            $('.pager').html('');
                            for (var i in data.images) {
                                if ($('#portfolio .pphoto').length < 1)
                                    $('#portfolio').prepend($('<div class="pphoto" id="portfolio' + data.images[i].Id + '"><a class="thumbik" rel="lightbox[group]" href="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/1024x0_' + data.images[i].FileName + '"><img src="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/199x169_' + data.images[i].FileName + '" alt="" /></a><div class="delete"><a rel="' + data.images[i].Id + '"></a></div></div>'));
                                else
                                    $('#portfolio .pphoto:last').after($('<div class="pphoto" id="portfolio' + data.images[i].Id + '"><a class="thumbik" rel="lightbox[group]" href="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/1024x0_' + data.images[i].FileName + '"><img src="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/199x169_' + data.images[i].FileName + '" alt="" /></a><div class="delete"><a rel="' + data.images[i].Id + '"></a></div></div>'));
                            }
                            for (var i in data.pages) {
                                $('.pager').append($(data.pages[i]));
                            }
                        },
                        dataType: 'json'
                    });
                }
            },
            {
                addClass: 'btn btn-danger',
                text: 'Нет',
                onClick: function($noty) {
                    $noty.close();
                }
            }
        ]
    });
    e.stopPropagation();
});

$('.sdelete a').live('click', function(){
    var p = {};
    p.id = $(this).attr('rel');


    noty({
        text: 'Удалить?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/iventor/del-iventor-service/',
                        data: p,
                        success: function(data){
                            if(typeof(data.ok) != 'undefined'){
                                window.location.reload();
                            }
                        },
                        dataType: 'json'
                    });
                }
            },
            {
                addClass: 'btn btn-danger',
                text: 'Нет',
                onClick: function($noty) {
                    $noty.close();
                }
            }
        ]
    });





});


$('.vdel a').live('click', function(){
    var p = {};
    p.id = $(this).attr('id');


    noty({
        text: 'Удалить?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/iventor/del-video/',
                        data: p,
                        success: function(data){
                            $('#video' + p.id).remove();
                        },
                        dataType: 'json'
                    });
                }
            },
            {
                addClass: 'btn btn-danger',
                text: 'Нет',
                onClick: function($noty) {
                    $noty.close();
                }
            }
        ]
    });
});




$('.sedit a').live('click', function(){
    addServicePopup($(this).attr('rel'));
});

$('.changephone').live('click', function(){
    showPhoneNumberPopup();
});

$('.vclicker').live('click', function() {
    var id = $(this).attr('id');
    var video = $('<div class="bigvideo"></div>');
    video.append($('<div class="relpos"><a></a></div>'));
    video.append($('<iframe width="935" height="526" src="http://www.youtube.com/embed/' + id + '?autoplay=1" frameborder="0" allowfullscreen></iframe>'))
    $('.relpos a', video).click(function(){
        video.remove();
        if ($('.user_popup_shadow').length > 0) $('.user_popup_shadow').remove();
    });
    $(document.body).append(getShadow());
    $(document.body).append(video);
    video.css('top', $(window).scrollTop() + 50 + 'px');
});
var EPreadonly = 1;

function saveClick(id, link) {
    var p = {};
    p.id = id;
    p.website = link;

    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/references/',
        data: p,
        success: function(data, textStatus){
           
        },
 
    });
}

function getPortfolioPage(page, iventor) {
    var p = {};
    p.id = iventor;
    p.page = page;
    currentPortfolioPage = page;
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/get-portfolio/',
        data: p,
        success: function(data, textStatus){
            $('#portfolio .pphoto').remove();
            $('.pager').html('');
            for (var i in data.images) {
                if ($('#portfolio .pphoto').length < 1)
                    $('#portfolio').prepend($('<div class="pphoto" id="portfolio' + data.images[i].Id + '"><a class="thumbik" rel="lightbox[group]" href="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/1024x0_' + data.images[i].FileName + '"><img src="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/199x169_' + data.images[i].FileName + '" alt="" /></a><div class="delete"><a rel="' + data.images[i].Id + '"></a></div></div>'));
                else
                    $('#portfolio .pphoto:last').after($('<div class="pphoto" id="portfolio' + data.images[i].Id + '"><a class="thumbik" rel="lightbox[group]" href="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/1024x0_' + data.images[i].FileName + '"><img src="' + HTTP_HOST + '/img/users/' + data.images[i].IventorId + '/199x169_' + data.images[i].FileName + '" alt="" /></a><div class="delete"><a rel="' + data.images[i].Id + '"></a></div></div>'));
            }
            for (var i in data.pages) {
                $('.pager').append($(data.pages[i]));
            }
            if (EPreadonly == 1) {
                $('#portfolio div.delete').remove();
            }
        },
        dataType: 'json'
    });
}

function deleteEventorSearch(id) {
    noty({
        text: 'Удалить запрос?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    window.location.href = currPage + '/delete/' + id;
                }
            },
            {
                addClass: 'btn btn-danger',
                text: 'Нет',
                onClick: function($noty) {
                    $noty.close();
                }
            }
        ]
    });
}

function deleteEventorSearch2(id) {
    noty({
        text: 'Удалить запрос?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    window.location.href = currPage + '/delete2/' + id;
                }
            },
            {
                addClass: 'btn btn-danger',
                text: 'Нет',
                onClick: function($noty) {
                    $noty.close();
                }
            }
        ]
    });
}

function addEditInfoPopup(info) {
    var popup = getUserPopup('Изменить информацию ивентора');
    popup.addClass('user_popup2');
    $('.changepasstitle', popup).css('padding-left', '130px');


    var changeinfo = $(document.createElement('div')).addClass('changeinfo');
    var photoh = $(document.createElement('div')).addClass('photoh');
    var photo = $(document.createElement('div')).addClass('photo');
    var img = $(document.createElement('img')).attr('alt', '');
	var modal = $(document.createElement('img')).attr('alt', '');
	img.attr('id', 'imgid');
	var jcrop_api;

    if ($.trim(info.Image) != '') {
        img.attr('src', HTTP_HOST + '/img/users/' + info.UserId + '/150x150_' + info.Image);
    }
    else {
        img.attr('src', HTTP_HOST + '/150x150.gif');
    }


    photo.append(img);
    photoh.append(photo);

    var fileinput = $(document.createElement('div')).addClass('fileinput').html('');
    var finph = $(document.createElement('input')).attr('type', 'hidden').attr('name', 'newlogo').attr('id', 'newlogo').val('');
    var finp = $(document.createElement('div')).attr('id', 'mm_file2');

    /*
    .change(function(){
        //doFileUpload(finp, finph, info.UserId, img, fileinput);
        //
    });
    */
    photoh.append(finp);
    fileinput.append(finph);
    photoh.append(fileinput);



    changeinfo.append(photoh);

    var inputss = $(document.createElement('div')).addClass('inputss');

    var inputh1 = $(document.createElement('div')).addClass('inputh');
    var inp1h = $(document.createElement('div')).addClass('popup_wide_input2');
    var inp1 = $(document.createElement('input')).attr('type', 'text').attr('name', 'cname').attr('id', 'cname').attr('autocomplete', 'off').val(info.CompanyName).attr('placehold', 'Название').attr('maxlength', '45');
    inp1.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    inp1.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });

    inp1h.append(inp1);
    inputh1.append(inp1h);
    inputss.append(inputh1);

    var eventorcity = {
        id: parseInt(info.CityId),
        country: parseInt(info.CountryID),
        name: info.CityName + ', ' + info.CountryName
    }

    var inputh4 = $(document.createElement('div')).addClass('inputh');
    var inp4h = $(document.createElement('div')).addClass('popup_wide_input2');
    var inp4 = $(document.createElement('input')).attr('type', 'text').attr('autocomplete', 'off').val(info.CityName + ', ' + info.CountryName).attr('placehold', 'Город');
    inp4.change(function(){
        eventorcity.id = 0;
        eventorcity.country = 0;
    });
    inp4.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    inp4.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });

    inp4.autocomplete2({
        serviceUrl: HTTP_HOST + '/index/autocomplite/country/' + eventorcity.country+'/',
        minChars: 1,
        delimiter: /(,|;)\s*/,
        maxHeight: 216,
        width: 382,
        zIndex: 99999,
        deferRequestBy: 0,
        noCache: false,
        onSelect: function(value, data){
            var d = data.split("|");
            eventorcity.id = parseInt(d[0]);
            eventorcity.country = parseInt(d[1]);
            eventorcity.name = value;
        }
    });

    $('.autocomplete').css({
        'margin-left': '-12px',
        'margin-top': '-6px'
    });


    inp4h.append(inp4);
    inputh4.append(inp4h);
    inputss.append(inputh4);

    var inputh5 = $(document.createElement('div')).addClass('inputh');
    var inp5h = $(document.createElement('div')).addClass('popup_wide_input2');
    var inp5 = $(document.createElement('input')).attr('type', 'text').attr('autocomplete', 'off').val(info.Website).attr('placehold', 'Сайт').attr('maxlength', '255');
    inp5.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    inp5.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    }).trigger('blur');
    inp5h.append(inp5);
    inputh5.append(inp5h);
    inputss.append(inputh5);



    var inputh6 = $(document.createElement('div')).addClass('inputh');
    var inp6h = $(document.createElement('div')).addClass('popup_wide_input2');
    var inp6 = $(document.createElement('input')).attr('type', 'text').attr('autocomplete', 'off').val(info.Skype).attr('placehold', 'Skype').attr('maxlength', '100');
    inp6.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    inp6.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    }).trigger('blur');
    inp6h.append(inp6);
    inputh6.append(inp6h);
    inputss.append(inputh6);


    changeinfo.append(inputss);




    var descr = $(document.createElement('div')).addClass('description');
    var ta = $(document.createElement('div')).addClass('ta');
    var tax = $(document.createElement('textarea')).val(info.Description).attr('placehold', 'Информация о компании*');
    tax.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    tax.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    }).blur();

    tax.keyup(function(e){
        if ($(this).val().length > 1500 && $(this).val() != $(this).attr('placehold')) {
            $(this).val($(this).val().substr(0, 1500));
            showError('Информация не должна превышать 1500 символов!');
            e.preventDefault();
            return false;
        }
    });
    ta.append(tax);
    descr.append(ta);
    changeinfo.append($(document.createElement('div')).addClass('clear'));
    changeinfo.append(descr);

    $('.user_popup_c', popup).append(changeinfo);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 50 + 'px');
    $('.savvve1', popup).css('text-align', 'center').css('padding-top', '24px');

    $('.savvve1 a', popup).click(function(){
        var p = {};
        p.name = $.trim(inp1.val()) != inp1.attr('placehold') && $.trim(inp1.val()) != '' ? $.trim(inp1.val()) : false;
        p.website = $.trim(inp5.val()) != inp5.attr('placehold') && $.trim(inp5.val()) != '' ? $.trim(inp5.val()) : '';
        p.skype = $.trim(inp6.val()) != inp6.attr('placehold') && $.trim(inp6.val()) != '' ? $.trim(inp6.val()) : '';
        p.city = eventorcity.id > 0 ? eventorcity.id : false;
        p.photo = finph.val();
        p.descr = $.trim(tax.val()) != tax.attr('placehold') && $.trim(tax.val()) != '' ? $.trim(tax.val()) : false;


        if (!p.name) {
            showError('Введите название!');
            return;
        }

        if (!p.city) {
            showError('Выберите город!');
            return;
        }

        if (!p.descr) {
            showError('Введите информацию о компании!');
            return;
        }
        if (p.descr.length > 1500) {
            showError('Информация не должна превышать 1500 символов!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/change-info/',
            data: p,
            success: function(data, textStatus){
                if (data.error != 0) {
                    showError(data.text);
                    return;
                }
                hideUserPopup();
				hostAddress= top.location.host.toString();        
				url = "http://" + hostAddress + data.s;
				//alert(url);
                //window.location.reload();
				window.location.href = url;
				
            },
            dataType: 'json'
        });
    });
	
		function updateCoords(c)
			{
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
				
			};

		function checkCoords(file,ivid)
			{	
			
				if (parseInt($('#w').val())) {
				 $.ajax({
				   url: '/iventor/upload-logo',
					//dataType: 'json',
					type: 'post',
				   data: {
						
						x: parseInt($('#x').val()),
						y: parseInt($('#y').val()),
						w: parseInt($('#w').val()),
						h: parseInt($('#h').val()),
						src: file
					},
				   success: function(html, msg){
					  $("#jc-hidden-dialog").css("display","none");
					   jcrop_api.destroy();
					   $(".user_popup_shadow").css("z-index","10000");
					   $(".user_popup_rel").css("display","block");
					   
					   $('#cropbox').attr('src', '');
					   $('#cropbox').remove();
					   $('#button4').remove();
					   $('#imgid').attr('src', HTTP_HOST + '/img/users/' + ivid + '/150x150_' + file);
					   $('.photo').removeClass('photoloading');
					   img.show();
						
				   },
				   error: function (xhr, ajaxOptions, thrownError) {
						img.show();
						photo.removeClass('photoloading');
					  }
				 }); 
				}
				else{
				alert('Вы не выбрали область');
				}
			};
			
			
		function position_block(w,h){
		
			$(".user_popup_rel").css("display","none");
			$(".user_popup_shadow").css("z-index","1000000");
			marginleft = -(parseInt(w)/2)+'px';
			margintop = (parseInt(h)/2)+'px';
			top = $('.user_popup_rel').offset().top+50+'px';
			$("#jc-hidden-dialog").css("margin-left",marginleft);
			$("#button4").css("margin-left", (parseInt(w)/2)-($("#button4").width()/2)+'px');
			$("#jc-hidden-dialog").css("top",top);
			$("#jc-hidden-dialog").css("display","block");
		}
		
			
				
				function getDimensions(){
					  return [
						Math.round($('#cropbox').width() / 2),
						Math.round($('#cropbox').height()/2),
						Math.round($('#cropbox').width()/ 4),
						Math.round($('#cropbox').height()/4)
					  ];
			}
				
	
    var uploader2 = new qq.FileUploader({
        element: document.getElementById('mm_file2'),
        action: HTTP_HOST + '/iventor/upload-logo1',
        debug: false,
        uploadButtonText: 'изменить',
        multiple: false,
        allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
        autoUpload: true,
        onComplete: function(id, fileName, result) {
		$("#jc-hidden-dialog").append('<img src="" id="cropbox" />');
		$("#jc-hidden-dialog").append('<div id="button4">Обрезать</div>');
		
			jQuery("#cropbox").attr('src', '/img/users/' + result.ivid + '/' + result.uploaded);
			$('#cropbox').Jcrop({
					aspectRatio: 1,
					boxWidth: 700,
					boxHeight: 500,
					onSelect: updateCoords
				},function(){
				jcrop_api = this;
				jcrop_api.setSelect(getDimensions());
				position_block($("#jc-hidden-dialog").width(),$("#jc-hidden-dialog").height());	
				});
			$('#button4').click(function(){
			checkCoords(result.uploaded, result.ivid);
			});

            finph.val(result.uploaded);
            photo.removeClass('photoloading');
        },
        onUpload: function(id, fileName, xhr) {
            img.hide();
            photo.addClass('photoloading');
        },
        onError: function(id, a, b) {
            img.show();
            photo.removeClass('photoloading');
        }
    });
}

function addPasswordPopup() {
    var popup = getUserPopup('Изменить пароль ивентора.');
    var dropdowns = $(document.createElement('div')).addClass('dropdowns');

    var dline1 = $(document.createElement('div')).addClass('dropdownline');
    var dline11 = $(document.createElement('div')).addClass('popup_wide_input');
    var inp1 = $(document.createElement('input')).attr('type', 'text').val('Старый пароль').attr('placehold', 'Старый пароль');
    var inp11 = $(document.createElement('input')).attr('type', 'password').attr('name', 'oldpass').attr('id', 'oldpass').attr('autocomplete', 'off').val('').css('display', 'none');
    inp1.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            inp11.show().focus();
        }
    });
    inp11.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            inp1.show();
        }
    });
    dline11.append(inp1).append(inp11);
    dline1.append(dline11);
    dropdowns.append(dline1);

    var dline2 = $(document.createElement('div')).addClass('dropdownline');
    var dline21 = $(document.createElement('div')).addClass('popup_wide_input');
    var inp2 = $(document.createElement('input')).attr('type', 'text').val('Новый пароль').attr('placehold', 'Новый пароль');
    var inp21 = $(document.createElement('input')).attr('type', 'password').attr('name', 'newpass').attr('id', 'newpass').attr('autocomplete', 'off').val('').css('display', 'none');
    inp2.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            inp21.show().focus();
        }
    });
    inp21.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            inp2.show();
        }
    });
    dline21.append(inp2).append(inp21);
    dline2.append(dline21);
    dropdowns.append(dline2);

    var dline3 = $(document.createElement('div')).addClass('dropdownline');
    var dline31 = $(document.createElement('div')).addClass('popup_wide_input');
    var inp3 = $(document.createElement('input')).attr('type', 'text').val('Подтвердить пароль').attr('placehold', 'Подтвердить пароль');
    var inp31 = $(document.createElement('input')).attr('type', 'password').attr('name', 'newpass2').attr('id', 'newpass2').attr('autocomplete', 'off').val('').css('display', 'none');
    inp3.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            inp31.show().focus();
        }
    });
    inp31.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            inp3.show();
        }
    });
    dline31.append(inp3).append(inp31);
    dline3.append(dline31);
    dropdowns.append(dline3);


    $('.user_popup_c', popup).append(dropdowns);

    $('.savvve1 a', popup).click(function(){
        var p = {};
        p.oldpass = inp11.val() != inp1.attr('placehold') && $.trim(inp11.val()) != '' ? inp11.val() : false;
        p.newpass = inp21.val() != inp2.attr('placehold') && $.trim(inp21.val()) != '' ? inp21.val() : false;
        p.newpass2 = inp31.val() != inp3.attr('placehold') && $.trim(inp31.val()) != '' ? inp31.val() : false;

        if (!p.oldpass) {
            showError('Введите старый пароль!');
            return;
        }

        if (!p.newpass) {
            showError('Введите новый пароль!');
            return;
        }

        if (!p.newpass2) {
            showError('Введите подтверждение!');
            return;
        }

        if (p.newpass != p.newpass2) {
            showError('Пароли не совпадают!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/change-pass/',
            data: p,
            success: function(data, textStatus){
                if (data.error != 0) {
                    showError(data.text);
                    return;
                }
                showNotification('Ваш пароль изменен.');
                hideUserPopup();
            },
            dataType: 'json'
        });
    });


    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}




function addServicePopup(preloadID) {
    if (typeof(preloadID) == 'undefined') {
        service.category = 0;
        service.subcategory = 0;
        service.city.id = 0;
        service.city.country = 0;
        service.city.name = '';
    }
    else {
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/get-service/',
            data: {id: preloadID},
            async: false,
            success: function(data){
                //console.log(data);
                service.category = parseInt(data.category);
                service.subcategory = parseInt(data.s.CategoryId);
                service.city.id = parseInt(data.s.CityId);
                service.city.country = parseInt(data.city.country_id);
                service.city.name = data.city.cityname + ', ' + data.city.name;
                service.min = parseInt(data.s.minPrice);
                service.max = parseInt(data.s.maxPrice);
                secCategories[service.category] = data.sublist;

            },
            dataType: 'json'
        });
    }

    var popup = getUserPopup('Добавить услугу.');
    var popupservices = $(document.createElement('div')).addClass('popupservices');
    var mainselect = $('<select name="category" class="selectcategory"><option disabled selected value="0">Категория услуги*</option></select>');
    var msdiv = $('<div class="selectcategoryh"></div>').css('position', 'absolute').css('z-index', '11');
    msdiv.append(mainselect);
    popupservices.append(msdiv);
    popupservices.append($('<div class="clear h60"></div>'));

    for (var i in topCategories) {
        var el = $(document.createElement('option'));
        el.val(topCategories[i].Id);
        el.html(topCategories[i].CategoryName);
        if (topCategories[i].Id == service.category) {
            $('option', mainselect).attr('selected', false);
            el.attr('selected', 'selected');
        }
        mainselect.append(el);
    }

    var secselect = $('<select name="category" class="selectsubcategory"><option disabled selected value="0">Подкатегория*</option></select>');
    var ssdiv = $('<div class="selectsubcategoryh"></div>').css('position', 'absolute').css('z-index', '10');
    ssdiv.append(secselect);
    popupservices.append(ssdiv);
    popupservices.append($('<div class="clear h60"></div>'));

    mainselect.change(function(){loadSubcategories(secselect, $(this).val())});


    var cityd = $('<div class="popup_wide_input"></div>');

    var city = $(document.createElement('input')).attr('type', 'text').attr('autocomplete', 'off').val(service.city.name).attr('placehold', 'Город');
    city.change(function(){
        service.city.id = 0;
        service.city.country = 0;
    });
    city.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    city.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    }).trigger('blur');

    cityd.append(city);

    city.autocomplete2({
        serviceUrl: HTTP_HOST + '/index/autocomplite/country/' + iventor.CountryID+'/',
        minChars: 1,
        delimiter: /(,|;)\s*/,
        maxHeight: 216,
        width: 371,
        zIndex: 99999,
        deferRequestBy: 0,
        noCache: false,
        onSelect: function(value, data){
            var d = data.split("|");
            service.city.id = parseInt(d[0]);
            service.city.country = parseInt(d[1]);
            service.city.name = value;
        }
    });

    $('.autocomplete').css({
        'margin-left': '-11px',
        'margin-top': '-10px'
    });

    popupservices.append(cityd);
    popupservices.append($('<div class="clear h19"></div>'));


    $('.user_popup_c', popup).append(popupservices);
    $('.user_popup_c', popup).append($(document.createElement('h5')).html('Выберите диапазон бюджета для указаной услуги.').css({'padding': '0px', 'text-align': 'center'}));


    var div22 = $('<div style="width: 551px; margin: 0px auto;"></div>');
    div22.append($('<div class="delimline"></div>'));



    var pdiv = $('<div class="selectpriceh"><div class="pricesliderh"><div class="lowprice">100<span>грн</span></div><div class="highprice">10000<span>грн</span></div><div class="priceslider"></div></div></div>');
    div22.append(pdiv);
    div22.append($('<div class="delimline"></div>'));

    $('.user_popup_c', popup).append(div22);


    if (typeof(preloadID) == 'undefined') {
        $('.savvve1 a', popup).addClass('savenotadd');
    }

    $('.savvve1', popup).css('padding-top', '25px');
    $('.savvve1 a', popup).click(function(){
    	var p = {};
    	p.pidcat = parseInt(secselect.val());
        if (isNaN(p.pidcat)) p.pidcat = 0;
        if (typeof(preloadID) != 'undefined') {
            p.id = preloadID;
        }
    	if (p.pidcat == 0) {
    		showError('Выберите подкатегорию');
    		return false;
    	}

        p.city = service.city.id;
    	
    	if(p.city == 0){
            showError('Выберите город');
    		return false;
    	}

        var pvalues = $('.priceslider', pdiv).slider("option", "values");


        p.min = pvalues[0];
        p.max = pvalues[1];

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/add-service/',
            data: p,
            success: function(data){
                if(typeof(data.error) != 'undefined'){
                    showError(data.error);
                    return false;
                }
                hideUserPopup();
                window.location.reload();
            },
            dataType: 'json'
        });
    });


    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');

    selectSmal('selectcategory');
    showSubcategories(secselect, secCategories[service.category], service.subcategory);
    initSlider(pdiv);
}



function initSlider(div) {
    var cmin = service.min > service.minmin ? service.min : service.minmin;
    var cmax = service.max < service.maxmax ? service.max : service.maxmax;

    var min = service.minmin;
    var max = service.maxmax;
    if (service.min == 0 && service.max == 0) {
        cmin = min;
        cmax = max;
    }
    $('.lowprice', div).html(cmin).append($(document.createElement('span')).html(service.currency));
    $('.highprice', div).html(cmax + (cmax == service.maxmax ? '+' : '')).append($(document.createElement('span')).html(service.currency));

    $('.priceslider', div).slider({
        range: true,
        min: min,
        max: max,
        step: service.sliderStep,
        animate: true,
        values: [cmin, cmax],
        slide: function( event, ui ) {
            $('.lowprice', div).html(ui.values[0]).append($(document.createElement('span')).html(service.currency));
            $('.highprice', div).html(ui.values[1] == service.maxmax ? ui.values[1] + '+' : ui.values[1]).append($(document.createElement('span')).html(service.currency));
        }
    });
}



function loadSubcategories(subsel, id, current) {
    if (typeof(secCategories[id]) == 'undefined') {
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/get-subcategories/',
            data: {id:id},
            success: function(data){
                secCategories[id] = data;
                showSubcategories(subsel, secCategories[id], current);
            },
            dataType: 'json'
        });
    }
    else {
        showSubcategories(subsel, secCategories[id], current);
    }
}

function showSubcategories(subsel, list, current) {
    $('option', subsel).each(function(){
        if ($(this).val() != "0") $(this).remove();
        else {
            if (typeof(current) == 'undefined') $(this).attr("selected", "selected");
            else $(this).attr("selected",false);
        }
    });
    $('span', subsel.parent()).remove();
    for (var i in list) {
        var el = $(document.createElement('option'));
        el.val(list[i].Id);
        el.html(list[i].CategoryName);
        if (typeof(current) != 'undefined' && list[i].Id == current) el.attr('selected', 'selected');
        subsel.append(el);

    }
    selectSmal('selectsubcategory');
}

function makeStarred(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/make-starred/',
        data: {id:id},
        success: function(data){
            $('#trans' + id).remove();
       },
        dataType: 'json'

    });
}

var monthes = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
var busydays = {};

function buildCalendar(m, y) {

    var dstart = new Date(y, m, 1, 0, 0, 0);
    var dend = new Date(y, m + 1, 0, 0, 0, 0);

    $('.monthyear').html(monthes[dstart.getMonth()] + ' ' + dstart.getFullYear());

    $('.prevmonth a').unbind('click').click(function(){
        buildCalendar(dstart.getMonth() - 1, dstart.getFullYear());
    });

    $('.nextmonth a').unbind('click').click(function(){
        buildCalendar(dstart.getMonth() + 1, dstart.getFullYear());
    });


    var daystart = dstart.getDay();
    if (daystart == 0) daystart = 7;
    var days = [];
    for (var i = 1; i < daystart; i++) {
        var day = {html: '&nbsp;', className: '', num: 0};
        days.push(day);
    }

    var rowc = days.length;
    var ilast = dend.getDate();
    var currow = 0;
    var lastrow = false;

    for (var i = 0; i < ilast; i++) {
        var dayname = i + 1;
        var day = {html: dayname, className: '', num: dayname};
        var __t = (new Date(y, m, dayname, 0, 0, 0, 0)).getTime();
        if (typeof(busydays[__t]) != 'undefined' && busydays[__t]) day.className = 'busy';
        days.push(day);
        rowc++;
        if (rowc == 7) {
            rowc = 0;
        }
    }

    if (rowc > 0) {
        while (rowc < 7) {
            var day = {html: '&nbsp;', className: '', num: 0};
            days.push(day);
            rowc++;
        }
    }
    $('#ddates').html('');
    var currentDate = new Date();
    for (var i = 0; i < days.length; i++) {
        var n = (i % 7) + 1;
        if (n == 1) {
            $('#ddates').append($(document.createElement('div')).addClass('ddates'));
        }
        var sp = $(document.createElement('span')).addClass('d' + n).addClass(days[i].className).html(days[i].html).attr('num', days[i].num);
        var b1 = y < currentDate.getFullYear();
        var b2 = (y == currentDate.getFullYear()) && (m < currentDate.getMonth());
        var b3 = (y == currentDate.getFullYear()) && (m == currentDate.getMonth()) && (days[i].num <= currentDate.getDate());
        if (b1 || b2 || b3) {
            sp.css('color', '#CCC');
        }
        if (days[i].num != 0) {
            sp.click(function(){
                if ($(this).hasClass('busy')) {
                    $(this).removeClass('busy');
                    changeBusyDate(m,parseInt($(this).attr('num')),y);
                }
                else {
                    var cd1 = new Date();
                    cd1 = new Date(cd1.getFullYear(), cd1.getMonth(), cd1.getDate() + 1, 0, 0, 0, 0);
                    var cd2 = new Date(y, m, parseInt($(this).attr('num')), 0, 0, 0, 0);
                    if (cd2 < cd1) {
                        return;
                    }

                    changeBusyDate(m,parseInt($(this).attr('num')),y);
                    $(this).addClass('busy');
                }
            });
        }
        $('#ddates .ddates:last').append(sp);
    }

    /*<div class="ddates">
        <span class="d1">&nbsp;</span><span class="d2">1</span><span class="d3">2</span><span class="d4">3</span><span class="d5">4</span><span class="d6">5</span><span class="d7">6</span>
    </div>*/

    //console.log(days);
}

function changeBusyDate(m,d,y) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/busy-date/',
        data: {t:y + '-' + (m + 1) + '-' + d},
        success: function(data, textStatus){
            $('#deactivlist').html(data.nice);
        },
        dataType: 'json'
    });
    var d1 = new Date(y,m,d,0,0,0,0);
    if (typeof(busydays[d1.getTime()]) != 'undefined') {
        if (busydays[d1.getTime()]) busydays[d1.getTime()] = false;
        else busydays[d1.getTime()] = true;
    }
    else {
        busydays[d1.getTime()] = true;
    }
}

function changeIActive(el) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/change-active/',
        data: {},
        success: function(data, textStatus){},
        dataType: 'json'
    });
    if ($(el).hasClass('a')) {
        $(el).removeClass('a');
        $('#a2').hide();
        $('#a1').show();
        $('#curactive2').html('активна');
        $('#curactive').html('активна');

    }
    else {
        $(el).addClass('a');
        $('#a1').hide();
        $('#a2').show();
        $('#curactive2').html('<i>неактивна</i>');
        $('#curactive').html('<i>неактивна</i>');
    }
}

function historyShow() {
    window.location.href = BURL + '/month/' + $('#hmonth').val() + '/year/' + $('#hyear').val() + '#paymenthist';
}

function initPayInput() {
    $('#payamount').blur(function(){
        var v = parseFloat($(this).val());
        if (isNaN(v)) v = 0;
        if (v < paystringc) {
            $(this).val(paystring).css('color', '#ad0000');
            $('#selectedsum').html(0);
        }
    }).focus(function(){
            if ($(this).val() == paystring) {
                $(this).val('').css('color', '#363636');
            }
        }).keyup(function(){
            var v = parseFloat($(this).val());
            if (isNaN(v)) v = 0;
            if (v >= paystringc) {
                $('#selectedsum').html(v);
            }
            else {
                $('#selectedsum').html(0);
            }

        }).blur();
}

function getPaymentForm() {
    var p = {days:$('select.selectprem').val()};
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/iventor/get-payment-form/',
        data: p,
        success: function(data, textStatus){
            if (data.error == 1) {
                showError(data.text);
                return;
            }
            $('input[name="operation_xml"]').val(data.xml);
            $('input[name="signature"]').val(data.sign);
            $('#clickandbuy')[0].submit();
        },
        dataType: 'json'
    });
}

function htmlspecialchars_decode(string) {
    // http://kevin.vanzonneveld.net
    //     original by: Mirek Slugen
    //     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //     bugfixed by: loonquawl
    // *     example 1: htmlspecialchars_decode("<p>this -> "</p>", 'ENT_NOQUOTES');
    // *     returns 1: '<p>this -> "</p>'

    string = string.toString();

    // Always encode
    string = string.replace('&gt;', '>');
    string = string.replace('&gt;', '>');
    string = string.replace('&lt;', '<');
    string = string.replace('&lt;', '<');

    return string;
}



function showPhoneNumberPopup() {
    var isBlocked = false;
    var blockedInfo;

    var popup = getUserPopup('Редактировать телефон.');
    $('.savvve1', popup).css('padding-top', '19px');

    var request = $(document.createElement('div')).addClass('phonereq').css('padding-bottom', '0px');
    request.append($(document.createElement('div')).addClass('toptext').html('По указанному телефону будут связываться клиенты, а также вы будете получать смс с их контактными данными.'));
    request.append($(document.createElement('div')).addClass('bw_line'));


    var ph1l = $(document.createElement('div')).addClass('input_button');
    var ph1 = $(document.createElement('div')).addClass('inp');
    var phoneinp = $(document.createElement('input')).val('Телефон').attr('type', 'text');
    phoneinp.focus(function(){
        if ($(this).val() == 'Телефон') $(this).val('');
    });
    phoneinp.blur(function(){
        if ($(this).val() == '') $(this).val('Телефон');
    });
    ph1.append(phoneinp);
    ph1l.append(ph1);
    var ph1b = $(document.createElement('a')).addClass('getcode').attr('sent', '0');
    ph1b.click(function(){
        if ($(this).attr('sent') != '0') return;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/evsms/',
            data: {phone:phoneinp.val()},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    showError(data.error);
                    return;
                }
                phoneinp.attr('disabled', 'disabled');
                ph1.addClass('inpgreen');
                showNotification('Код отправлен');
                ph1b.attr('sent', '1');
                ph2b.attr('check', '1');
            },
            dataType: 'json'
        });
    });
    ph1l.append(ph1b);

    request.append(ph1l);

    var ph2l = $(document.createElement('div')).addClass('input_button');
    var ph2 = $(document.createElement('div')).addClass('inp');
    var codeinp = $(document.createElement('input')).val('Подтвердить код').attr('type', 'text');
    codeinp.focus(function(){
        if ($(this).val() == 'Подтвердить код') $(this).val('');
    });
    codeinp.blur(function(){
        if ($(this).val() == '') $(this).val('Подтвердить код');
    });
    ph2.append(codeinp);
    ph2l.append(ph2);
    var ph2b = $(document.createElement('a')).addClass('confirmcode').attr('check', '0');
    ph2b.click(function(){
        if ($(this).attr('check') != '1') return;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/evsms-check/',
            data: {code:codeinp.val()},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    showError(data.error);
                    codeinp.val('').trigger('blur');
                    return;
                }
                codeinp.attr('disabled', 'disabled');
                ph2.addClass('inpgreen');
                showNotification('Спасибо. Код подтвержден.');
                ph2b.attr('check', '2');
                button.attr('req', '1');
            },
            dataType: 'json'
        });
    });
    ph2l.append(ph2b);

    request.append(ph2l);

    request.append($(document.createElement('div')).addClass('h3'));
    request.append($(document.createElement('div')).addClass('bw_line').css('margin', '0px'));


    $('.user_popup_c', popup).append(request);


    //var button = $(document.createElement('div')).addClass('makerequest1').html('Сделать запрос').attr('req', '0');
    var button = $('.savvve1 a', popup);
    button.attr('req', '0');
    $('.savvve1 a', popup).click(function(){
        if ($(this).attr('req') == '0') return;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/save-new-phone/',
            data: {},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    showError(data.error);
                    return;
                }
                showNotification('Телефон изменен');
                window.location.reload();
            },
            dataType: 'json'
        });
        return;
    });
    //$('.relpos', popup).append(button);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}


function addVideoPopup() {
    var popup = getUserPopup('Добавить видео запись.');
    var changeinfo = $(document.createElement('div')).addClass('video');

    var tax = $(document.createElement('textarea')).val("Видео ссылка из адресной строки Youtube:\n(пример: http://www.youtube.com/watch?v=i41qWJ6QjPI)").attr('placehold', "Видео ссылка из адресной строки Youtube:\n(пример: http://www.youtube.com/watch?v=i41qWJ6QjPI)");
	tax.css("font-size", '14px')
    tax.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    tax.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    }).blur();
    changeinfo.append(tax);

    $('.user_popup_c', popup).append(changeinfo);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 50 + 'px');
    $('.savvve1', popup).css('padding', '24px 0px 0px 0px');

    $('.savvve1 a', popup).addClass('savenotadd').click(function(){
        var p = {};
        p.descr = $.trim(tax.val()) != tax.attr('placehold') && $.trim(tax.val()) != '' ? $.trim(tax.val()) : false;
		 if (!p.descr) {
            showError('Введите ссылку!');
            return;
        }
		
		if (p.descr.indexOf('feature=player_detailpage') != -1){
		    showError('Видео ссылка должна быть нового формата. Вы можете скопировать ее с адресной строки сервиса Youtube!');
			return 0;
		}		

		p.descr = p.descr.replace('youtube.com', 'youtu.be');
		p.descr = p.descr.replace('feature=player_embedded', '');
		p.descr = p.descr.replace('watch?', '');
		p.descr = p.descr.replace('&', '');
		p.descr = p.descr.replace('v=', '');
		n = p.descr.split("list=");
		for (var i=0;i<n.length;i++)
			{ 
				if(n[i].indexOf('youtu.be') != -1){
				  p.descr = n[i];
				}
			}
		
		
	
		if(p.descr.indexOf('youtu.be') == -1){
		    showError('Видео ссылка допускаеться только с сервиса Youtube!');
			return 0;
		}

		
       

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/add-video/',
            data: p,
            success: function(data, textStatus){
                if (typeof(data.error) != 'undefined') {
                    showError(data.error);
                    return;
                }
                var d = $('<div/>').addClass('video').attr('id', 'video' + data.id);
                var iff = $('<img width="307" height="173" alt=""/>').attr('src', 'http://i.ytimg.com/vi/' + data.link + '/0.jpg');
                var d1 = $('<div class="vclicker"></div>').attr('id', data.link);
                var d2 = $('<div class="vdel"><a id="' + data.id + '"></a></div>');
                d.append(iff).append(d1).append(d2);
                if ($('#videos .video').length > 0) {
                    $('#videos .video:last').after(d);
                }
                else {
                    $('#videos').prepend(d);
                }
                hideUserPopup();
            },
            dataType: 'json'
        });
    });


}


function getPremPrice(days) {
    var p = parseFloat(oPrices[days]);
    $('#selectedsum').html(p.toFixed(2));
}

function showBlockedPhonePopupE(seconds) {

    var popup = getUserPopup('Подтверждение запроса.');
    $('.savvve1', popup).remove();
    var request = $(document.createElement('div')).addClass('nonereq');
    var pink = $(document.createElement('div')).addClass('pink');

    pink.append($(document.createElement('div')).addClass('error3').html('Внимание! Вы три раза допустили ошибку.'));
    pink.append($(document.createElement('div')).addClass('topmes').html('Следующая попытка через:'));
    var clock = $(document.createElement('div')).addClass('clock');
    pink.append(clock);
    pink.append($(document.createElement('div')).addClass('topmes').html('Ознакомтесь пока с <a href="' + HTTP_HOST + '/static/rules">Правилами сервиса</a> и <a href="' + HTTP_HOST + '/faq">FAQ</a>'));


    var seconds_cnt = parseInt(seconds);
    clock.html(getClockTextE(seconds_cnt--));
    var timer_int = setInterval(function(){
        if (seconds_cnt <= 0) {
            clearInterval(timer_int);
            hideUserPopup();
            showPhoneNumberPopupE();
            seconds_cnt = 0;
        }
        clock.html(getClockTextE(seconds_cnt));
        seconds_cnt--;
    }, 1000);

    request.append(pink);
    $('.user_popup_c', popup).append(request);

    var button = $(document.createElement('div')).addClass('makerequest1').html('Сделать запрос').addClass('nonerequest');
    $('.relpos', popup).append(button);
    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}




function getClockTextE(seconds) {
    var seconds_cnt = parseInt(seconds);
    var mins = parseInt(seconds_cnt / 60);
    var secs = seconds_cnt % 60;
    mins = mins.toString();
    secs = secs.toString();
    if (mins.length < 2) mins = '0' + mins;
    if (secs.length < 2) secs = '0' + secs;
    return mins + ':' + secs;
}


function showPhoneNumberPopupE() {
    var isBlocked = false;
    var blockedInfo;
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/index/sms/',
        async: false,
        data: {istest:1},
        success: function(data) {
            if (typeof(data.error) != 'undefined') {
                isBlocked = true;
                blockedInfo = data;
            }
        },
        dataType: 'json'
    });

    if (isBlocked) {
        hideUserPopup();
        showBlockedPhonePopupE(blockedInfo.seconds);
        return;
    }

    var popup = getUserPopup('Подтверждение запроса.');
    $('.savvve1', popup).remove();

    var request = $(document.createElement('div')).addClass('phonereq');
    request.append($(document.createElement('div')).addClass('toptext').html('Для того чтобы ивенторы или ивент компании связались с Вами, введите и подтвердите свой контактный телефон.'));
    request.append($(document.createElement('div')).addClass('bw_line'));

    var notynoty = $(document.createElement('div')).addClass('notynoty').css('padding', '0px 150px 10px 25px');
    request.append(notynoty);
    //alert(1);

    var ph1l = $(document.createElement('div')).addClass('input_button');
    var ph1 = $(document.createElement('div')).addClass('inp');
    var phoneinp = $(document.createElement('input')).val('Телефон').attr('type', 'text');
    phoneinp.focus(function(){
        if (notynoty.html() == '') {
            var noty = $('.notynoty').noty({text: 'Введите телефон в формате:<br />Россия 7XXXXXXXXXX<br />Украина 38XXXXXXXXXX', type: 'information'});
        }
        if ($(this).val() == 'Телефон') $(this).val('');
    });
    phoneinp.blur(function(){
        if ($(this).val() == '') $(this).val('Телефон');
    });
    ph1.append(phoneinp);
    ph1l.append(ph1);
    var ph1b = $(document.createElement('a')).addClass('getcode').attr('sent', '0');
    ph1b.click(function(){
        if ($(this).attr('sent') != '0') return;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/sms/',
            data: {phone:phoneinp.val()},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    if (typeof(data.seconds) != 'undefined') {
                        hideUserPopup();
                        showBlockedPhonePopupE(data.seconds);
                    }
                    showError(data.error);
                    return;
                }
                phoneinp.attr('disabled', 'disabled');
                ph1.addClass('inpgreen');
                showNotification('Код отправлен');
                ph1b.attr('sent', '1');
                ph2b.attr('check', '1');
            },
            dataType: 'json'
        });
    });
    ph1l.append(ph1b);

    request.append(ph1l);

    var ph2l = $(document.createElement('div')).addClass('input_button');
    var ph2 = $(document.createElement('div')).addClass('inp');
    var codeinp = $(document.createElement('input')).val('Подтвердить код').attr('type', 'text');
    codeinp.focus(function(){
        if ($(this).val() == 'Подтвердить код') $(this).val('');
    });
    codeinp.blur(function(){
        if ($(this).val() == '') $(this).val('Подтвердить код');
    });
    ph2.append(codeinp);
    ph2l.append(ph2);
    var ph2b = $(document.createElement('a')).addClass('confirmcode').attr('check', '0');
    ph2b.click(function(){
        if ($(this).attr('check') != '1') {
            showError('Введите номер телефона на который будет выслан код подтверждения.');
            return;
        }
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/sms-check/',
            data: {code:codeinp.val()},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    if (typeof(data.seconds) != 'undefined') {
                        hideUserPopup();
                        showBlockedPhonePopupE(data.seconds);
                    }
                    showError(data.error);
                    codeinp.val('').trigger('blur');
                    return;
                }
                codeinp.attr('disabled', 'disabled');
                ph2.addClass('inpgreen');
                showNotification('Спасибо. Код подтвержден.');
                ph2b.attr('check', '2');
                button.attr('req', '1');
            },
            dataType: 'json'
        });
    });
    ph2l.append(ph2b);

    request.append(ph2l);
    request.append($(document.createElement('div')).addClass('h3'));
    var remember = $(document.createElement('div')).addClass('save_number save_number_on').html('Запомнить этот номер в личных настройках. Подтверждение телефонного номера при каждом запросе не требуется.');
    remember.click(function(){
        if ($(this).hasClass('save_number_on')) $(this).removeClass('save_number_on');
        else $(this).addClass('save_number_on');
    });
    request.append(remember);

    $('.user_popup_c', popup).append(request);


    var button = $(document.createElement('div')).addClass('makerequest1').html('Сделать запрос').attr('req', '0');
    button.click(function(){
        if ($(this).attr('req') == '0')
        {
            showError('Введите код подтверждения.');
            return;
        }
        var params = {id: requestedPhoneEventor, sid: requestedPhoneService};
        params.saveNumber = remember.hasClass('save_number_on') ? 2 : 1;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/iventor/request-call/',
            data: params,
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    showError(data.error);
                    return;
                }
                $('.reqphone').removeClass('reqphone').addClass('reqphone2').html('Вы уже дали запрос на звонок. Ожидайте.');
                showNotification('Запрос выслан. Вам перезвонят на телефон ' + data.phone);
                hideUserPopup();
            },
            dataType: 'json'
        });
        return;
    });
    $('.relpos', popup).append(button);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}


function showPortfolioShadow() {
    var pos = $('#portfolio').position();
    var shp = $(document.createElement('div')).addClass('portfolio_shadow');
    shp.css('top', pos.top + 11 + 'px');
    shp.css('left', pos.left + 3 + 'px');
    shp.css('height', $('#portfolio').height() - 60 + 'px');
    $(document.body).append(shp);

    var h = $('#portfolio').height() - 60;
    var shp2 = $(document.createElement('div')).addClass('portfolio_shadow_text');
    var img = $(document.createElement('img')).attr('alt', '').attr('src', HTTP_HOST + '/images/loading2.gif');
    shp2.append(img);
    var p = $(document.createElement('p')).html('загрузка фото');
    shp2.append(p);
    $(document.body).append(shp2);

    shp2.css('left', pos.left + 423 + 'px');
    shp2.css('top', pos.top + (h - shp2.height()) / 2 + 'px');
    $('#portfolio .pager').hide();
}

function hidePortfolioShadow() {
    $('.portfolio_shadow').remove();
    $('.portfolio_shadow_text').remove();
    $('#portfolio .pager').show();
}











































