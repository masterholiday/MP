$('.catalog-short-list .main-list ul li').live('click', function(){
    var catID = $(this).attr('id').replace('sh-cat', '');
    var p = $(this).parent().parent();
    $('.catalog-short-list .dark span').html($(this).html().replace(/<div>(.)*<\/div>/, ""));
    $('.subcat ul', p.parent()).hide();
    $('#sh-pcat' + catID, p.parent()).show();
    $('#sh-pcat' + catID + ' li', p.parent()).each(function(i){
        var k = i + 1;
        if (k % 3 == 0 && i > 0) $(this).css('margin-right', '0px');
    });
    p.fadeOut(250, function(){$('.subcat', p.parent()).fadeIn(250)});
});


$('.catalog-short-list a.back').live('click', function(){
    $('.catalog-short-list .subcat').fadeOut(250, function(){$('.catalog-short-list .main-list').fadeIn(250)});
});


var secCategories = {"0": []},
    scatParams = {
        city: {
            id: 0,
            country: 0,
            name: '',
            prevcountry: 0
        },
        min: 0, max: 0,
        category: 0,
        currency: function(full) {
            if (typeof(full) == 'undefined') return this.city.country == 9908 ? "грн" : "руб";
            else return this.city.country == 9908 ? "гривен" : "рублей";
        },
        getMinPrice: function() {
            return this.city.country == 9908 ? 100 : 500;
        },
        getMaxPrice: function() {
            return this.city.country == 9908 ? 10000 : 50000;
        },
        getSliderStep: function() {
            return this.city.country == 9908 ? 100 : 500;
        }
    }
    requestedPhoneEventor = 0,
    requestedPhoneService = 0;

function initCatalogForm() {

    //console.log(scatParams.city.name);

    var topsel = $('.search-box .selectcategoryh select');
    for (var i in topCategories) {
        var el = $(document.createElement('option'));
        el.val(topCategories[i].Id);
        el.html(topCategories[i].CategoryName);
        if (topCategories[i].Id == scatParams.category) {
            $('option', topsel).attr('selected', false);
            el.attr('selected', 'selected');
        }
        topsel.append(el);
    }

    topsel.change(function(){loadSubcategories2($(this).val())});
    selectSmal('selectcategory');
    showSubcategoriesC(secCategories[scatParams.category], scatParams.category2)
    $('.search-box .city input').change(function(){
        scatParams.city.id = 0;
        scatParams.city.country = 0;
    }).focus(function(){
            if ($(this).val() == 'Город') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Город');
        });
    $('.search-box .city input').val(scatParams.city.name).trigger('blur');
    $('.search-box .city input').autocomplete2({
        serviceUrl: HTTP_HOST + '/index/autocomplite/',
        minChars: 1,
        delimiter: /(,|;)\s*/,
        maxHeight: 216,
        width: 272,
        zIndex: 9999,
        deferRequestBy: 0,
        noCache: false,
        onSelect: function(value, data){
            var d = data.split("|");

            scatParams.city.id = parseInt(d[0]);
            scatParams.city.country = parseInt(d[1]);
            scatParams.city.name = value;
            if (scatParams.city.prevcountry != scatParams.city.country) {
                refreshSlider2($('.search-box .pricesliderh'));
            }
        }
    });
    $('.autocomplete').css({
        'margin-left': '-15px',
        'margin-top': '-8px'
    });
    initSlider2($('.search-box .pricesliderh'));
	
	function getMinValue(){
	var pvalues = $('.search-box .pricesliderh .priceslider').slider("option", "values");
	return pvalues[0];
	}
	
	function getMaxValue(){
	var pvalues = $('.search-box .pricesliderh .priceslider').slider("option", "values");
	return pvalues[1];
	}
	
	function getCityValue(){
	return scatParams.city.id;
	}
	
	function getCategory(){
	return parseInt($('.search-box .selectsubcategoryh select').val());
	}
	
	function fillForm(){
	var pvalues = $('.search-box .pricesliderh .priceslider').slider("option", "values");
	$("#hcity").val(parseInt(scatParams.city.id));
	$("#hcat").val(parseInt($('.search-box .selectsubcategoryh select').val()));
	$("#hmin").val(parseInt(pvalues[0]));
	$("#hmax").val(parseInt(pvalues[1]));
	
	}
	
    $('.catalog-box a.searchbutton').click(function(){
	var category = parseInt($('.search-box .selectsubcategoryh select').val());
	var city = scatParams.city.id;
	var catname = null;
	var cityname = null;
	$.ajax({
				   url: '/iventor/translitcat',
        			type: 'post',
					dataType: 'json',
					async: false,
				   data: {
	
						catid: category,
					},
				   success: function(html, msg){
				  
					    catname = html;
						
				   },
				   error: function (xhr, ajaxOptions, thrownError) {
						
					  },
				 });
				 
	     $.ajax({
				   url: '/iventor/translitcity',
        			type: 'post',
					dataType: 'json',
					async: false,
				   data: {
	
						city: city,
					},
				   success: function(html, msg){
						
					    cityname = html;
						
				   },
				   error: function (xhr, ajaxOptions, thrownError) {

						
					  },
				 });
				 
	var action = "/catalog/"+catname+"/"+cityname+"/";
	$("#hcityname").val(cityname);
	$("#hcatname").val(catname);
	$('#hform').get(0).setAttribute('action', action); //this works
	
	

        if (isNaN(category) || category < 1) {
            showError('Выберите категорию!');
            return;
        }
        if (isNaN(city) || city < 1) {
            showError('Выберите город!');
            return;
        }
		fillForm();
		$("#hform").submit();
    });

    $('.iphone').live('mouseover', function(){
        $('.myhint', $(this).parent()).hide();
        if ($(this).hasClass('iphoned')) $('.callwait', $(this).parent()).show();
        $('.callme', $(this).parent()).show();
    });
    $('.iphone').live('mouseout', function(){
        $('.myhint', $(this).parent()).hide();
    });
    $('.iphone').live('click', function(){
        $('.myhint', $(this).parent()).hide();
        if ($(this).hasClass('iphoned')) {

        }
        else {
            var eid = $(this).attr('eid');
            var sid = $(this).attr('sid');
            requestedPhoneEventor = eid;
            requestedPhoneService = sid;
            $.ajax({
                type: 'POST',
				async: false,
                url: HTTP_HOST + '/iventor/request-call/',
                data: {id:eid, sid:sid},
                success: function(data){
                    if (typeof(data.error) == 'undefined') {
                        $('a.iphone[eid=' + eid + ']').addClass('iphoned');
                        showNotification('Запрос выслан. Вам перезвонят на телефон ' + data.phone);
						_gaq.push(['_trackEvent', 'Zapros', 'Catalog']);
                    }
                    else {
                        if (data.error == 'login') {
                            showLoginPopup();
                            return;
                        }
                        if (data.error == 'phone') {
                            showPhoneNumberPopupC();
                            return;
                        }
                        showError(data.error);
                    }
                },
                dataType: 'json'
            });
        }

    });

    $('.istar').live('mouseover', function(){
        $('.myhint', $(this).parent()).hide();
        if ($(this).hasClass('istard')) $('.delstar', $(this).parent()).show();
        $('.addstar', $(this).parent()).show();
    });
    $('.istar').live('mouseout', function(){
        $('.myhint', $(this).parent()).hide();
    });
    $('.istar').live('click', function(){
        $('.myhint', $(this).parent()).hide();
        var eid = $(this).attr('eid');
        var sid = $(this).attr('sid');
        var link = $(this);
        if ($(this).hasClass('istard')) {
            $.ajax({
                type: 'POST',
				async: false,
                url: HTTP_HOST + '/iventor/remove-star/',
                data: {id:eid},
                success: function(data){
                    if (typeof(data.error) == 'undefined') {
                        $('a.istar[eid=' + eid + ']').removeClass('istard');
                        showNotification("Ивентор удален из избранного");
                    }
                    else {
                        showError(data.error);
                    }
                },
                dataType: 'json'
            });
        }
        else {
            $.ajax({
                type: 'POST',
				async: false,
                url: HTTP_HOST + '/iventor/add-star/',
                data: {id:eid, sid:sid},
                success: function(data){
					
                    if (typeof(data.error) == 'undefined') {
					
                        $('a.istar[eid=' + eid + ']').addClass('istard');
                        showNotification("Ивентор добавлен в избранное");
						_gaq.push(['_trackEvent', 'Izbrannoe', 'VCataloge']);
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
				error: function (xhr, ajaxOptions, thrownError) {
					
					  },
                dataType: 'json'
            });

        }

    });

}

function initSlider2(div) {
    var cmin = scatParams.min > scatParams.getMinPrice() ? scatParams.min : scatParams.getMinPrice();
    var cmax = scatParams.max < scatParams.getMaxPrice() ? scatParams.max : scatParams.getMaxPrice();

    var min = scatParams.getMinPrice();
    var max = scatParams.getMaxPrice();
    if (scatParams.min == 0 && scatParams.max == 0) {
        cmin = min;
        cmax = max;
    }
    $('.lowprice', div).html(cmin).append($(document.createElement('span')).html(scatParams.currency()));
    $('.highprice', div).html(cmax + (cmax == scatParams.getMaxPrice() ? '+' : '')).append($(document.createElement('span')).html(scatParams.currency()));

    $('.priceslider', div).slider({
        range: true,
        min: min,
        max: max,
        step: scatParams.getSliderStep(),
        animate: true,
        values: [cmin, cmax],
        slide: function( event, ui ) {
            $('.lowprice', div).html(ui.values[0]).append($(document.createElement('span')).html(scatParams.currency()));
            $('.highprice', div).html(ui.values[1] == scatParams.getMaxPrice() ? ui.values[1] + '+' : ui.values[1]).append($(document.createElement('span')).html(scatParams.currency()));
        }
    });
    if (scatParams.city.country == 0) {
        $('.priceslider', div).slider("disable");
        $('.lowprice', div).html('');
        $('.highprice', div).html('');
    }
    else {
        $('.priceslider', div).slider("enable");
    }
    scatParams.city.prevcountry = scatParams.city.country;

}

function refreshSlider2(div, curmin, curmax) {
    var min = scatParams.getMinPrice();
    var max = scatParams.getMaxPrice();

    if (typeof(curmin) == 'undefined') curmin = min;
    if (typeof(curmax) == 'undefined') curmax = max;

    $('.priceslider', div).slider("option", "min", min);
    $('.priceslider', div).slider("option", "max", max);
    $('.priceslider', div).slider("option", "step", scatParams.getSliderStep());
    $('.priceslider', div).slider("option", "values", [curmin, curmax]);
    $('.priceslider', div).slider("values", $('.priceslider', div).slider("values"));

    $('.lowprice', div).html(curmin).append($(document.createElement('span')).html(scatParams.currency()));
    $('.highprice', div).html(curmax + (curmax == scatParams.getMaxPrice() ? "+" : '')).append($(document.createElement('span')).html(scatParams.currency()));

    if (scatParams.city.country == 0) {
        $('.priceslider', div).slider("disable");
        $('.lowprice', div).html('');
        $('.highprice', div).html('');
    }
    else {
        $('.priceslider', div).slider("enable");
    }
    scatParams.city.prevcountry = scatParams.city.country;
}



function loadSubcategories2(id, current) {
    if (typeof(secCategories[id]) == 'undefined') {
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/get-subcategories/',
            data: {id:id},
            success: function(data){
                secCategories[id] = data;
                showSubcategoriesC(secCategories[id], current);
            },
            dataType: 'json'
        });
    }
    else {
        showSubcategoriesC(secCategories[id], current);
    }
}


function showSubcategoriesC(list, current) {
    var sel = $('.search-box .selectsubcategoryh select');
    $('option', sel).each(function(){
        if ($(this).val() != "0") $(this).remove();
        else {
            if (typeof(current) == 'undefined') $(this).attr("selected", "selected");
            else $(this).attr("selected",false);
        }
    });
    $('span', sel.parent()).remove();
    for (var i in list) {
        var el = $(document.createElement('option'));
        el.val(list[i].Id);
        el.html(list[i].CategoryName);
        if (typeof(current) != 'undefined' && list[i].Id == current) el.attr('selected', 'selected');
        sel.append(el);

    }
    selectSmal('selectsubcategory');
}



function showBlockedPhonePopupC(seconds) {

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
    clock.html(getClockTextC(seconds_cnt--));
    var timer_int = setInterval(function(){
        if (seconds_cnt <= 0) {
            clearInterval(timer_int);
            hideUserPopup();
            showPhoneNumberPopup();
            seconds_cnt = 0;
        }
        clock.html(getClockTextC(seconds_cnt));
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




function getClockTextC(seconds) {
    var seconds_cnt = parseInt(seconds);
    var mins = parseInt(seconds_cnt / 60);
    var secs = seconds_cnt % 60;
    mins = mins.toString();
    secs = secs.toString();
    if (mins.length < 2) mins = '0' + mins;
    if (secs.length < 2) secs = '0' + secs;
    return mins + ':' + secs;
}


function showPhoneNumberPopupC() {
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
        showBlockedPhonePopupC(blockedInfo.seconds);
        return;
    }

    var popup = getUserPopup('Подтверждение запроса.');
    $('.savvve1', popup).remove();

    var request = $(document.createElement('div')).addClass('phonereq');
    request.append($(document.createElement('div')).addClass('toptext').html('Для того чтобы ивенторы или ивент компании связались с Вами, введите и подтвердите свой контактный телефон.'));
    request.append($(document.createElement('div')).addClass('bw_line'));

    var notynoty = $(document.createElement('div')).addClass('notynoty').css('padding', '0px 150px 10px 25px');
    request.append(notynoty);

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
                        showBlockedPhonePopupC(data.seconds);
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
        if ($(this).attr('check') != '1') return;
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/sms-check/',
            data: {code:codeinp.val()},
            success: function(data) {
                if (typeof(data.error) != 'undefined') {
                    if (typeof(data.seconds) != 'undefined') {
                        hideUserPopup();
                        showBlockedPhonePopupC(data.seconds);
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
        if ($(this).attr('req') == '0') return;
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
                $('a.iphone[eid=' + requestedPhoneEventor + ']').addClass('iphoned');
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

