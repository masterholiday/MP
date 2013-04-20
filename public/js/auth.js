function showLoginPopup() {
    var popup = getUserPopup('Введите ваш логин и пароль.');
    $('.savvve1', popup).remove();


    var auth = $(document.createElement('div')).addClass('auth');

    var line1 = $(document.createElement('div')).addClass('input');
    var login = $(document.createElement('input')).attr('type', 'text').val('E-mail').attr('placehold', 'E-mail');
    login.attr('name', 'email');
    login.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    login.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });
    line1.append(login);
    auth.append(line1);

    var line2 = $(document.createElement('div')).addClass('input');
    var pass = $(document.createElement('input')).attr('type', 'text').val('Пароль').attr('placehold', 'Пароль');
    var pass2 = $(document.createElement('input')).attr('type', 'password').attr('name', 'pass').attr('autocomplete', 'off').val('').css('display', 'none');
    pass2.attr('name', 'password');
    pass.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            pass2.show().focus();
        }
    });
    pass2.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            pass.show();
        }
    });
    line2.append(pass).append(pass2);
    auth.append(line2);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h12'));

    var remeberme = $(document.createElement('a')).addClass('remeberme remchecked').html('Запомнить меня');
    remeberme.attr('chk', '1').click(function(){
        if ($(this).attr('chk') == '0') {
            $(this).addClass('remchecked');
            $(this).attr('chk', '1');
        }
        else {
            $(this).removeClass('remchecked');
            $(this).attr('chk', '0');
        }
    });

    auth.append(remeberme);

    var loginb = $(document.createElement('a')).addClass('login').html('Вход');
    loginb.click(function(){
        var params = {login: login.val(), password: pass2.val(), remember: remeberme.hasClass('remchecked') ? 1 : 0};
        if (params.login == '' || params.login == login.attr('placehold')) {
            showError('Введите E-mail');
            return;
        }
        if (params.password == '') {
            showError('Введите пароль');
            return;
        }
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/auth/index/',
            data: params,
            success: function(data){
                if (typeof(data.error) != 'undefined') {
                    showError('Вы ввели неверное имя пользователя или неверный пароль');
                    return;
                }
                if(data.type == 'eventor') {
                    if (typeof IVENTOR_REDIRECT != 'undefined') window.location.href = IVENTOR_REDIRECT;
                    else window.location.href = HTTP_HOST+'/iventor';
                } else {
                    getUserHeader();
                    hideUserPopup();
                }
            },
            dataType: 'json'
        });
    });

    login.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    pass2.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });


    auth.append(loginb);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h11'));

    var line3 = $(document.createElement('div')).css('text-align', 'center');
    var forgot = $(document.createElement('a')).addClass('forgot').html('Забыли пароль, не можете зайти в свой аккаунт?');
    forgot.click(function(){
        //line1.hide();
        $('.changepasstitle', popup).html('Введите E-mail');
        line2.hide();
        line3.hide();
        line31.show();
        loginb.hide();
        line4.hide();
        remeberme.hide();
        $('.clear', auth).hide();
    });
    line3.append(forgot);
    auth.append(line3);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h25'));

    var line31 = $(document.createElement('div')).css('text-align', 'center').css('display', 'none').css('padding', '0px 0px 50px 0px');
    var restore = $(document.createElement('a')).addClass('restore').html('Отправить');
    restore.click(function(){
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/restore-pass/',
            data: {email: login.val()},
            success: function(data, textStatus){
                if(typeof(data.ok) != 'undefined'){
                    showNotification(data.result);
                    hideUserPopup();
                }else{
                    showError(data.result);
                }

            },
            dataType: 'json'
        });
    });
    line31.append(restore);
    auth.append(line31);


    var line4 = $(document.createElement('div')).addClass('register');
    var register = $(document.createElement('a')).html('Нет аккаунта? Регистрируйся!');
    register.click(function(){
        if ($('.user_popup').length > 0) $('.user_popup').remove();
        showRegisterMain();
		
    });
    line4.append(register);
    auth.append(line4);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h15'));

    var line5 = $(document.createElement('div')).addClass('authbuttons');
    var vk = $(document.createElement('a')).addClass('vk');
    vk.click(function(){
        var w = window.open(HTTP_HOST + '/index/vk-login', "vklogin", "location=0,status=0,scrollbars=0,width=800,height=430");
    });
    var fb = $(document.createElement('a')).addClass('fb');
    fb.click(function(){
        var w = window.open(HTTP_HOST + '/index/facebook-login', "fblogin", "location=0,status=0,scrollbars=0,width=800,height=430");
    });
    var gg = $(document.createElement('a')).addClass('gg');
    gg.click(function(){
        var w = window.open(HTTP_HOST + '/index/google-login', "glogin", "location=0,status=0,scrollbars=0,width=800,height=430");
    });

    line5.append(vk);
    line5.append(fb);
    //line5.append(gg);
    line5.append($(document.createElement('div')).html('Вход с помощью социальных сетей.'));

    $('.relpos', popup).append(line5);

    $('.user_popup_c', popup).append(auth);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}


function showRegisterPopup() {
    var popup = getUserPopup('Регистрация пользователя.');
    $('.savvve1', popup).remove();

    var auth = $(document.createElement('div')).addClass('auth');

    var line0 = $(document.createElement('div')).addClass('input');
    var uname = $(document.createElement('input')).attr('type', 'text').val('Инициалы').attr('placehold', 'Инициалы');
    uname.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    uname.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });
    line0.append(uname);
    auth.append(line0);


    var line1 = $(document.createElement('div')).addClass('input');
    var login = $(document.createElement('input')).attr('type', 'text').val('E-mail').attr('placehold', 'E-mail');
    login.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    login.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });
    line1.append(login);
    auth.append(line1);

    var line2 = $(document.createElement('div')).addClass('input');
    var pass = $(document.createElement('input')).attr('type', 'text').val('Пароль').attr('placehold', 'Пароль');
    var pass2 = $(document.createElement('input')).attr('type', 'password').attr('name', 'pass').attr('autocomplete', 'off').val('').css('display', 'none');
    pass.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            pass2.show().focus();
        }
    });
    pass2.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            pass.show();
        }
    });
    line2.append(pass).append(pass2);
    auth.append(line2);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h14'));

    var rules = $(document.createElement('a')).html('Правилами сервиса').attr('href', HTTP_HOST + '/static/rules').attr('target', '_blank');
    var remeberme = $(document.createElement('a')).addClass('remeberme remchecked').html('Я согласен с ').append(rules);

    remeberme.attr('chk', '1').click(function(e){
        if (e.target != this) return;
        if ($(this).attr('chk') == '0') {
            $(this).addClass('remchecked');
            $(this).attr('chk', '1');
        }
        else {
            $(this).removeClass('remchecked');
            $(this).attr('chk', '0');
        }
    });

    auth.append(remeberme);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h15'));

    var loginb = $(document.createElement('a')).addClass('register2').html('Зарегистрироваться');
    loginb.click(function(){
        var name = $.trim(uname.val()) != uname.attr('placehold') ? $.trim(uname.val()) : '';
        var email = $.trim(login.val() != login.attr('placehold') ? $.trim(login.val()) : '');
        var passs = $.trim(pass2.val());
        var agree = remeberme.hasClass('remchecked');

        if (name == '') {
            showError('Введите инициалы');
            return;
        }
        if (email == '') {
            showError('Введите E-mail');
            return;
        }
        if (passs == '') {
            showError('Введите пароль');
            return;
        }
        if (!agree) {
            showError('Вы должны согласится с Правилами Сервиса');
            return;
        }

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/do-register/',
            data: {email: email, pass: passs, name: name, agree: agree ? 1 : 0},
            success: function(data, textStatus){
                if(typeof(data.ok) != 'undefined'){
                    showNotification(data.result);
                    getUserHeader();
                    hideUserPopup();
					_gaq.push(['_trackEvent', 'Registration', 'User']);

                }else{
                    showError(data.result);
                }

            },
            dataType: 'json'
        });
    });


    uname.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    login.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    pass2.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    auth.append(loginb);
    auth.append($(document.createElement('div')).addClass('clear').addClass('h7'));


    var line5 = $(document.createElement('div')).addClass('foriventdisc').html('Если вы ивентор или ивент компания, для регистрации<br />перейдите на страницу ');
    var foriv = $(document.createElement('a')).html('”Для ивенторов“').attr('href', HTTP_HOST + '/faq/iventor/');
    line5.append(foriv);

    $('.relpos', popup).append(line5);

    $('.user_popup_c', popup).append(auth);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}

function getUserHeader() {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/auth/get-header/',
        data: {},
        success: function(data, textStatus){
            if (data.change == 0) return;
            var prevmap = $('.mapuserarea');
            var newmap = $(data.mapuserarea);
            prevmap.before(newmap);
            prevmap.remove();

            var previnfo = $('.top-info-not-auth');
            var newinfo = $(data.topinfo);
            previnfo.before(newinfo);
            previnfo.remove();
            if (typeof(searchParams) != 'undefined') {
                searchParams.canSearch = true;
                searchParams.authorized = true;
            }
            if (typeof(step3AfterLoginHandler) != 'undefined') {
                if (step3AfterLoginHandler !== false) step3AfterLoginHandler();
                step3AfterLoginHandler = false;
            }
        },
        dataType: 'json'
    });
    //mapuserarea
}



function showRegisterEventorPopup() {
    var regParams = {
        city: {
            id: 0,
            country: 0,
            name: '',
            prevcountry: 0
        }
    };
    var popup = getUserPopup('Регистрация ивентора.');
    $('.savvve1', popup).remove();

    var auth = $(document.createElement('div')).addClass('evregister');

    var line0 = $(document.createElement('div')).addClass('input');
    var uname = $(document.createElement('input')).attr('type', 'text').val('Название').attr('placehold', 'Название');
	uname.attr('maxlength','45');
    uname.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    uname.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });
    line0.append(uname);
    auth.append(line0);


    var line1 = $(document.createElement('div')).addClass('input');
    var login = $(document.createElement('input')).attr('type', 'text').val('E-mail').attr('placehold', 'E-mail');
    login.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).val('');
        }
    });
    login.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).val($(this).attr('placehold'));
        }
    });
    line1.append(login);
    auth.append(line1);


    var line01 = $(document.createElement('div')).addClass('input');
    var city = $(document.createElement('input')).attr('type', 'text').val('Город').attr('placehold', 'Город');
    city.change(function(){
        regParams.city.id = 0;
        regParams.city.country = 0;
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
    });
    line01.append(city);
    auth.append(line01);


    city.autocomplete2({
        serviceUrl: HTTP_HOST + '/index/autocomplite/',
        minChars: 1,
        delimiter: /(,|;)\s*/,
        maxHeight: 216,
        width: 382,
        zIndex: 99999,
        deferRequestBy: 0,
        noCache: false,
        onSelect: function(value, data){
            var d = data.split("|");
            regParams.city.id = parseInt(d[0]);
            regParams.city.country = parseInt(d[1]);
            regParams.city.name = value;
            if (regParams.city.prevcountry != regParams.city.country) {
                regParams.city.prevcountry = regParams.city.country;
                ph1b.attr('sent', '0');
                ph2b.attr('check', '0');
                phoneinp.removeAttr('disabled');
                codeinp.removeAttr('disabled');
                ph1.removeClass('inpgreen');
                ph2.removeClass('inpgreen');
                if (regParams.city.country == 9908) {
                    phoneinp.val('').mask("99(999) 999-99-99",{placeholder:" "}).attr('placeholder', 'Телефон* [38(XXX) XXX-XX-XX]');
                }
                else {
                    phoneinp.val('').mask("9(999) 999-99-99",{placeholder:" "}).attr('placeholder', 'Телефон* [7(XXX) XXX-XX-XX]');
                }
                phoneinp.trigger('blur');
                codeinp.val('').trigger('blur');
                loginb.attr('req', '0');
            }
        }
    });

    $('.autocomplete').css({
        'margin-left': '-12px',
        'margin-top': '-6px'
    });




    var ph1l = $(document.createElement('div')).addClass('input_button');
    var ph1 = $(document.createElement('div')).addClass('inp');
	
	var notynoty = $(document.createElement('div')).addClass('notynoty').css({
   padding : '0px 0px 10px 0px',
   width : '100%'
});
    auth.append(notynoty);
	
    var phoneinp = $(document.createElement('input')).val('Телефон').attr('type', 'text').attr('placeholder', 'Телефон');
    phoneinp.focus(function(){
		if (notynoty.html() == '') {
            var noty = $('.notynoty').noty({text: 'Введите телефон в формате:<br />Россия 7XXXXXXXXXX<br />Украина 38XXXXXXXXXX', type: 'information'});
        }
        if ($(this).val() == $(this).attr('placeholder')) $(this).val('');
    });
    phoneinp.blur(function(){
        if ($(this).val() == '') $(this).val($(this).attr('placeholder'));
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
                showNotification('Код отправлен');
                phoneinp.attr('disabled', 'disabled');
                ph1.addClass('inpgreen');
                ph1b.attr('sent', '1');
                ph2b.attr('check', '1');
                loginb.attr('req', '0');
            },
            dataType: 'json'
        });
    });
    ph1l.append(ph1b);

    auth.append(ph1l);


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
                loginb.attr('req', '1');
            },
            dataType: 'json'
        });
    });
    ph2l.append(ph2b);

    auth.append(ph2l);




    var line2 = $(document.createElement('div')).addClass('input');
    var pass = $(document.createElement('input')).attr('type', 'text').val('Пароль').attr('placehold', 'Пароль');
    var pass2 = $(document.createElement('input')).attr('type', 'password').attr('name', 'pass').attr('autocomplete', 'off').val('').css('display', 'none');
    pass.focus(function(){
        if ($(this).val() == $(this).attr('placehold')) {
            $(this).hide();
            pass2.show().focus();
        }
    });
    pass2.blur(function(){
        if ($.trim($(this).val()) == '') {
            $(this).hide();
            pass.show();
        }
    });
    line2.append(pass).append(pass2);
    auth.append(line2);
    auth.append($(document.createElement('div')).addClass('clear'));

    var rules = $(document.createElement('a')).html('Правилами сервиса').attr('href', HTTP_HOST + '/static/rules').attr('target', '_blank');
    var remeberme = $(document.createElement('a')).addClass('remeberme remchecked').html('Я согласен с ').append(rules);

    remeberme.attr('chk', '1').click(function(e){
        if (e.target != this) return;
        if ($(this).attr('chk') == '0') {
            $(this).addClass('remchecked');
            $(this).attr('chk', '1');
        }
        else {
            $(this).removeClass('remchecked');
            $(this).attr('chk', '0');
        }
    });

    auth.append(remeberme);

    var loginb = $(document.createElement('a')).addClass('register2').html('Зарегистрироваться').attr('req', '0');
    loginb.click(function(){
        var name = $.trim(uname.val()) != uname.attr('placehold') ? $.trim(uname.val()) : '';
        var email = $.trim(login.val() != login.attr('placehold') ? $.trim(login.val()) : '');
        var passs = $.trim(pass2.val());
        var agree = remeberme.hasClass('remchecked');
        var city = regParams.city.id;

        if (name == '') {
            showError('Введите Название');
            return;
        }
        if (email == '') {
            showError('Введите E-mail');
            return;
        }
        if (city == 0) {
            showError('Введите Город');
            return;
        }

        if ($(this).attr('req') != '1') {
            showError('Подтвердите номер телефона!');
            return;
        }

        if (passs == '') {
            showError('Введите пароль');
            return;
        }
        if (!agree) {
            showError('Вы должны согласится с Правилами Сервиса');
            return;
        }

        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/do-eventor-register/',
            data: {email: email, pass: passs, name: name, agree: agree ? 1 : 0, city: city},
            success: function(data, textStatus){
                if(typeof(data.ok) != 'undefined'){
                    showNotification(data.result);
                    hideUserPopup();
					_gaq.push(['_trackEvent', 'Registration', 'Eventor']);

                }else{
                    showError(data.result);
                }

            },
            dataType: 'json'
        });
    });


    uname.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    login.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    pass2.keydown(function(e){
        if (e.keyCode == 13) {
            loginb.trigger('click');
        }
    });

    auth.append($(document.createElement('div')).addClass('clear'));

    var line5 = $(document.createElement('div')).css({
        position: 'absolute',
        top: '20px',
        right: '134px'
    });
    line5.append(loginb);

    $('.relpos', popup).append(line5);

    $('.user_popup_c', popup).append(auth);

    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}

function showRegisterMain() {
    var popup = getUserPopup('Выберите тип аккаунта для регистрации.');
    $('.savvve1', popup).remove();
    var auth = $(document.createElement('div')).addClass('auth');
    var line0 = $(document.createElement('div')).addClass('input');
    var line4 = $(document.createElement('div')).html('<map  name="mapuserarea2"><area href onclick="hideUserPopup(); showRegisterPopup(); return false;" title="Пользователь" shape="rect" coords="0,0,250,66" /><area href="" onclick="hideUserPopup(); showRegisterEventorPopup(); return false;" title="Ивентор" shape="rect" coords="251,0,500,66" /></map><img id="imagepop" src="http://masterholiday.net/images/maket_main_choose_registration.png" width="500" height="66" alt="" usemap="#mapuserarea2" />');
    auth.append($(document.createElement('div')).addClass('clear').addClass('h7'));
    var line5 = $(document.createElement('div')).addClass('foriventdisc').html('Более подробная информация на странице<br />');
    var foriv = $(document.createElement('a')).html('”Как это работает“').attr('href', HTTP_HOST + '/static/how-this-work-client/');
    line5.append(foriv);
    $('.relpos', popup).append(line5);
	$('.user_popup_c', popup).append(line4);
    $(document.body).append(getShadow());
    $(document.body).append(popup);
    popup.css('top', $(window).scrollTop() + 100 + 'px');
}

