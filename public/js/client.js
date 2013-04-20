$('.phonec a.del').live('click', function(){
    noty({
        text: 'Удалить телефон?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/client/remove-phone/',
                        data: {},
                        success: function(data){
                            window.location.reload();
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
/*    if (confirm("Удалить телефон?")) {
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/client/remove-phone/',
            data: {},
            success: function(data){
                window.location.reload();
            },
            dataType: 'json'
        });
    }*/
});

function addEditInfoPopup() {
    
    $.ajax({
    	 type: 'POST',
         url: HTTP_HOST + '/client/get-client-data/',
         data: {},
         async:false,
         success: function(data){
        	 var popup = getUserPopup('Изменить информацию пользователя.');
        	 var dropdowns = $(document.createElement('div')).addClass('dropdowns');

        	 var dline1 = $(document.createElement('div')).addClass('dropdownline');
        	    var dline11 = $(document.createElement('div')).addClass('popup_wide_input');
        	    var inp1 = $(document.createElement('input')).attr('type', 'text').attr('name', 'name').attr('id', 'name').attr('placehold', 'Инициалы').attr('autocomplete', 'off').val(data.name);
             inp1.focus(function(){
                 if($(this).val() == 'Инициалы'){
                     $(this).val('');
                 }
             });
             inp1.blur(function(){
                 if($(this).val() == ''){
                     $(this).val('Инициалы');
                 }
             }).trigger('blur');

             dline11.append(inp1);
        	    dline1.append(dline11);
        	    dropdowns.append(dline1);

        	    var dline2 = $(document.createElement('div')).addClass('dropdownline');
        	    var dline21 = $(document.createElement('div')).addClass('popup_wide_input');
        	    var inp2 = $(document.createElement('input')).attr('type', 'text').attr('name', 'email').attr('id', 'email').attr('placehold', 'Email').attr('autocomplete', 'off').val(data.email);
             inp2.focus(function(){
                 if($(this).val() == 'Email'){
                     $(this).val('');
                 }
             });
             inp2.blur(function(){
                 if($(this).val() == ''){
                     $(this).val('Email');
                 }
             }).trigger('blur');

             dline21.append(inp2);
        	    dline2.append(dline21);
        	    dropdowns.append(dline2);

        	    $('.user_popup_c', popup).append(dropdowns);
        	    var dline51 = $(document.createElement('div')).addClass('client_phone');
                dline51.css('margin-top', '0px');
        	    var inp5 = $(document.createElement('input')).attr('type', 'text').attr('name', 'phone').attr('placehold', 'Телефон').attr('id', 'phone').attr('autocomplete', 'off').val(data.phone);
        	    inp5.focus(function(){
        	    	if($(this).val() == 'Телефон'){
        	    		$(this).val('');
        	    	}
        	    });
        	    inp5.blur(function(){
        	    	if($(this).val() == ''){
        	    		$(this).val('Телефон');
        	    	}
        	    }).trigger('blur');



        	    var text = $(document.createElement('div')).addClass('text').text('Для смены телефона нужно заново запросить проверочный код.');
        	    var dline5 = $(document.createElement('div')).addClass('client_phone');
        	    var inp6 = $(document.createElement('input')).attr('type', 'text').attr('name', 'code').attr('id', 'code').attr('autocomplete', 'off').val('Код подтверждения');
        	    inp6.focus(function(){
        	    	if($(this).val() == 'Код подтверждения'){
        	    		$(this).val('');
        	    	}
        	    });
        	    inp6.blur(function(){
        	    	if($(this).val() == ''){
        	    		$(this).val('Код подтверждения');
        	    	}
        	    });
        	    dline5.append(inp6);
        	    var code = $(document.createElement('div')).addClass('code');
        	    dline51.append(inp5);
        	    var dline6 = $(document.createElement('div')).addClass('buttons');
        	    var inpbtn1 = $(document.createElement('input')).attr('type', 'button').attr('name', 'get').attr('id', 'get').attr('class', 'get').val('');
        	    
        	    
        	    inpbtn1.click(function(){
        	    	if($('#code').is(':disabled')){
        	    		return false;
        	    	}
        	    	var p = {};
        	    	p.phone = $('#phone').val();
        	    	$.ajax({
        	    		type: 'POST',
        	    		url: HTTP_HOST + '/index/sms/',
        	    		data: p,
        	    		success: function(data){
        	    			if(typeof(data.error) == 'undefined'){
        	    				showNotification('Код отправлен');
        	    				$('.get_pidtv').css({'display':'block'});
        	    				$('#confirm').removeAttr('disabled');
                                $('input[name="phone"]').attr('disabled','disabled');
                                $('input[name="phone"]').parent('.client_phone').addClass('client_phone_ok');
        	    			}else{
        	    				showError(data.error);
        	    			}
        	    			
        	    		},
        	    		dataType: 'json'
        	    	});
        	    });
        	    dline6.append(inpbtn1);
        	    
        	    var inpbtn2 = $(document.createElement('input')).attr('type', 'button').attr('name', 'confirm').attr('id', 'confirm').attr('class', 'confirm').attr('disabled','desabled').val('');
                inpbtn2.css('margin-top', '30px');
        	    inpbtn2.click(function(){
        	    	if($('#code').is(':disabled')){
        	    		return false;
        	    	}
        	    	var p ={};
        	    	
        	    	p.code = $('#code').val();
        	    	$.ajax({
        	    		type: 'POST',
        	    		url: HTTP_HOST + '/index/sms-check/',
        	    		data: p,
        	    		success: function(data){
        	    			if(typeof(data.error) == 'undefined'){
        	    				$('input[name="code"]').attr('disabled','disabled');
        	    				$('input[name="code"]').parent('.client_phone').addClass('client_phone_ok');
                                showNotification('Спасибо. Код подтвержден.');
                                $('#phone').attr('disabled', 'disabled');
                                $('#code').attr('disabled', 'disabled');
                            }else{
                                $('#code').val('').trigger('blur');
                                showError(data.error);
        	    			}
        	    		},
        	    		dataType: 'json'
        	    	});
        	    });
        	    dline6.append(inpbtn2);
        	    
        	    code.append(text);
        	    code.append(dline51);
        	    code.append(dline5);
        	    code.append(dline6);
        	    code.append('<div class="clear"></div>');
        	    $('.dropdowns', popup).after(code);
        	    
        	    //code.append();
                $('.savvve1', popup).css('padding-top', '25px');
        	    $('.savvve1 a', popup).click(function(){
        	        var p = {};
        	        p.name = inp1.val() != inp1.attr('placehold') && $.trim(inp1.val()) != '' ? inp1.val() : false;
        	        p.email = inp2.val() != inp2.attr('placehold') && $.trim(inp2.val()) != '' ? inp2.val() : '';
        	        p.phone = inp5.val() != inp5.attr('placehold') && $.trim(inp5.val()) != '' ? inp5.val() : '';

        	        if (!p.name) {
        	            showError('Введите инициалы!');
        	            return;
        	        }

        	        /*if (!p.email) {
                        showError('Введите E-mail!');
        	            return;
        	        }
        	        */

        	        /*if (!p.phone) {
                        showError('Введите телефон');
        	            return;
        	        }
        	        */
                    //console.log(p);
                    //return;

        	       $.ajax({
        	            type: 'POST',
        	            url: HTTP_HOST + '/client/update-client-data/',
        	            data: p,
        	            success: function(data){
                            console.log(data);
        	                if (typeof(data.result) == 'undefined') {
        	                    hideUserPopup();
        	                    window.location.reload();
        	                }else{
                                showError(data.result);
        	                }
        	                
        	           },
        	            dataType: 'json'
        	        });
        	    });

        	    $(document.body).append(getShadow());
        	    $(document.body).append(popup);
        	    popup.css('top', $(window).scrollTop() + 100 + 'px');
         },
         dataType: 'json'
    });
    

   
}

function addPasswordPopup() {
    var popup = getUserPopup('Изменить пароль пользователя.');
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
    var inp3 = $(document.createElement('input')).attr('type', 'text').val('Подтвердить').attr('placehold', 'Подтвердить');
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
            url: HTTP_HOST + '/client/change-pass/',
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

function getUserPopup(title) {
    var popup = $(document.createElement('div')).addClass('user_popup');
    var popup_rel = $(document.createElement('div')).addClass('user_popup_rel');
    var popup_top = $(document.createElement('div')).addClass('user_popup_top');
    var changepasstitle = $(document.createElement('div')).addClass('changepasstitle').html(title);
    popup_top.append(changepasstitle);
    popup_rel.append(popup_top);
    popup_rel.append($(document.createElement('div')).addClass('user_popup_c'));
    var popup_bottom = $(document.createElement('div')).addClass('user_popup_bottom');
    var popup_save = $(document.createElement('div')).addClass('savvve1');
    var asave = $(document.createElement('a'));
    popup_save.append(asave);
    popup_bottom.append(popup_save);
    popup_rel.append(popup_bottom);
    popup_rel.append($(document.createElement('div')).addClass('topleft'));
    popup_rel.append($(document.createElement('div')).addClass('bottomright'));
    popup_rel.append($(document.createElement('a')).addClass('close').click(function(){hideUserPopup();}));
    popup.append(popup_rel);
    return popup;
}

function getShadow() {
    return $(document.createElement('div')).addClass('user_popup_shadow');
}

function hideUserPopup() {
    $('.user_popup').remove();
    $('.user_popup_shadow').remove();
}
