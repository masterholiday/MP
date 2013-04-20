var searchParams = {
    city: {
        id: 0,
        country: 0,
        name: ''
    },
    date: false,
    eventname: '',
    servises: [],
    authorized: false,
    canSearch: false,

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
};

var step2cityautocmplete, topCategories, secondCategories = {"0": []}, prevCountry, step3AfterLoginHandler = false;

function prepareForm() {
    if (searchParams.city.name != '') $('.search-box .step1 .city input').val(searchParams.city.name);
}

$(document).ready(function(){
    prepareForm();
    if ($('.search-box .step1 .city').length > 0) {
        $('.search-box .step1 .city input').autocomplete2({
            serviceUrl: HTTP_HOST + '/index/autocomplite/',
            minChars: 1,
            delimiter: /(,|;)\s*/,
            maxHeight: 216,
            width: 374,
            zIndex: 9999,
            deferRequestBy: 0,
            noCache: false,
            onSelect: function(value, data){
                var d = data.split("|");
                searchParams.city.id = parseInt(d[0]);
                searchParams.city.country = parseInt(d[1]);
                searchParams.city.name = value;
            }
        });
        $('.search-box .step1 .city input').change(function(){
            searchParams.city.id = 0;
        }).focus(function(){
            if ($(this).val() == 'Город') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Город');
        });
    }
    if ($('.search-box .step1 .date').length > 0) {
        $('.search-box .step1 .date a').click(function(){
            step2();

        });

        if (searchParams.date) {
            $('.search-box .step1 .date input').val(jQuery.fn.simpleDatepicker.formatOutput(searchParams.date));
            $('.search-box .step1 .date input').simpleDatepicker({chosendate:searchParams.date});
        }
        else {
            $('.search-box .step1 .date input').simpleDatepicker();
        }
        $('.search-box .step1 .date input').change(function(){
            if ($(this).val() != 'Дата') {
                var d = $(this).data('simpleDatepicker').getDate();
                searchParams.date = d;
            }
            else {
                searchParams.date = false;
            }

        }).focus(function(){
            if ($(this).val() == 'Дата') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Дата');
        });
    }






    //step2();
});


function step3() {
    if (searchParams.eventname == '') {
        showError('Введите название мероприятия');
        return;
    }

    if (searchParams.servises.length < 1) {
        showError('Добавьте услугу')
            return;
    }
    $("html, body").animate({ scrollTop: 180 }, "slow");
    $('.search-box .steps li:eq(0)').removeClass().addClass('green');
    $('.search-box .steps li:eq(2)').removeClass().addClass('green');
    $('.search-box .steps li:eq(4)').removeClass().addClass('yellow');

    $('.search-box div.step2').hide();

    $('.search-box div.step3').show();

    $('.search-box .step2-title1').hide();
    $('.search-box .step2-next').hide();
    if (searchParams.authorized) {
        $('.search-box .step3-next').show().unbind('click').click(function(){
            $.ajax({
                type: 'POST',
                url: HTTP_HOST + '/auth/check-phone-number/',
                data: {},
                success: function(data) {
                    if (parseInt(data.exists) == 1) {
                        var params = {};
                        for (var p in searchParams) {
                            if (typeof(searchParams[p]) != 'function') params[p] = searchParams[p];
                        }
                        params.date = {y: searchParams.date.getFullYear(), m: searchParams.date.getMonth() + 1, d: searchParams.date.getDate()};
                        $.ajax({
                            type: 'POST',
                            url: HTTP_HOST + '/activity/search-form/',
                            data: {params: params},
                            success: function(data) {
                                if (typeof(data.error) != 'undefined') {
                                    showError(data.error);
                                    return;
                                }
                                window.location.href = HTTP_HOST + '/client/index/show/1';
								_gaq.push(['_trackEvent', 'Zapros', 'Search']);
                            },
                            dataType: 'json'
                        });
                        return;
                    }
                    else {
                        showPhoneNumberPopup();
                    }
                },
                dataType: 'json'
            });

        });
        $('.search-box .step3-login').hide();
        $('.search-box .step3-register').hide();
    }
    else {
        $('.search-box .step3-next').hide();
        $('.search-box .step3-login').show().unbind('click').click(function(){
            step3AfterLoginHandler = function(){step3();};
            showLoginPopup();
        });
        $('.search-box .step3-register').show().unbind('click').click(function(){
            step3AfterLoginHandler = function(){step3();};
            showRegisterPopup();
        });
    }

    $('.search-box .step3-title1').show();


    $('.search-box div.step3 .summary .info span:eq(0)').html(searchParams.eventname);
    $('.search-box div.step3 .summary .info span:eq(1)').html([searchParams.date.getDate(), searchParams.date.getMonth() + 1, searchParams.date.getFullYear()].join('.'));
    $('.search-box div.step3 .summary .info span:eq(2)').html(searchParams.city.name);
    $('.search-box div.step3 .summary .button a').unbind('click').click(function(){
        step2();
    });

    $('#servlist').html('');
    for (var i in searchParams.servises) {
        viewService(searchParams.servises[i], parseInt(i) + 1);
    }
}

function step2() {
    var waserror = false;
    if (!searchParams.canSearch) {
        showError("Извините, но вы не можете осуществлять поиск");
        return;
    }

    if (searchParams.city.id == 0) {
        showError("Выберите город");
        waserror = true;
    }
    if (!searchParams.date) {
        showError("Выберите дату");
        waserror = true;
    }
    if (waserror) return;
    $("html, body").animate({ scrollTop: 180 }, "slow");
    $('.search-box div.step2 .addservicebutton .save').hide();
    $('.catalog-short-list').remove();
    $('.search-box .steps li:eq(0)').removeClass().addClass('green');
    $('.search-box .steps li:eq(2)').removeClass().addClass('yellow');
    $('.search-box .steps li:eq(4)').removeClass().addClass('grey');

    $('.search-box div.step1').hide();

    $('.search-box .step1-title1').hide();
    $('.search-box .step1-title2').hide();
    $('.search-box .step3-title1').hide();

    $('.search-box div.step3').hide();
    $('.search-box div.step2').show();
    $('.search-box .step2-title1').show();
    $('.search-box .step3-next').hide();
    $('.search-box .step3-login').hide();
    $('.search-box .step3-register').hide();

    $('.search-box .step2-next').show().unbind('click').click(function(){
        searchParams.eventname = $.trim($('.search-box .step2 .nameline input').val());
        step3();

    });
    $('.search-box .step2 .nameline input').val(searchParams.eventname);
    var dbox = $('.search-box div.step2 .addressline .date .inbox input');
    dbox.val(jQuery.fn.simpleDatepicker.formatOutput(searchParams.date));
    if (typeof(dbox.data('simpleDatepicker')) == 'undefined') {
        dbox.simpleDatepicker({chosendate:searchParams.date, dx: -15, dy: -15});
        dbox.change(function(){
            if ($(this).val() != 'Дата') {
                var d = $(this).data('simpleDatepicker').getDate();
                searchParams.date = d;
            }
            else {
                searchParams.date = false;
            }

        }).focus(function(){
            if ($(this).val() == 'Дата') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Дата');
        });
    }

    var cbox = $('.search-box div.step2 .addressline .city .inbox input');
    cbox.val(searchParams.city.name);

    if (typeof(step2cityautocmplete) == 'undefined') {
        //first initialization
        step2cityautocmplete = cbox.autocomplete2({
            serviceUrl: HTTP_HOST + '/index/autocomplite/',
            minChars: 1,
            delimiter: /(,|;)\s*/,
            maxHeight: 216,
            width: 325,
            zIndex: 9999,
            deferRequestBy: 0,
            noCache: false,
            onSelect: function(value, data){
                var d = data.split("|");

                searchParams.city.id = parseInt(d[0]);
                searchParams.city.country = parseInt(d[1]);
                searchParams.city.name = value;
            }
        });
        cbox.change(function(){
            searchParams.city.id = 0;
        }).focus(function(){
            if ($(this).val() == 'Город') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Город');
        });

        $('.search-box div.step2 .addressline .button a').click(function(){
            if ($(this).hasClass('edit')) {
                $(this).removeClass().addClass('save');
                $('.search-box .step2 .date .inbox').addClass('inbox-editable');
                $('.search-box .step2 .date .inbox input').attr('disabled', false);

                $('.search-box .step2 .city .inbox').addClass('inbox-editable');
                $('.search-box .step2 .city .inbox input').attr('disabled', false);
                prevCountry = searchParams.city.country
            }
            else {
                var werror = false;
                if (searchParams.city.id == 0) {
                    showError("Выберите город");
                    werror = true;
                }
                if (!searchParams.date) {
                    showError("Выберите дату");
                    werror = true;
                }
                if (werror) return;

                $(this).removeClass().addClass('edit');
                $('.search-box .step2 .date .inbox').removeClass('inbox-editable');
                $('.search-box .step2 .date .inbox input').attr('disabled', 'disabled').val(jQuery.fn.simpleDatepicker.formatOutput(searchParams.date));

                $('.search-box .step2 .city .inbox').removeClass('inbox-editable');
                $('.search-box .step2 .city .inbox input').attr('disabled', 'disabled');
                if (prevCountry != searchParams.city.country) {
                    if (prevCountry == 9908) {
                        for (var _i_ in searchParams.servises) {
                            searchParams.servises[_i_].minprice = parseInt(searchParams.servises[_i_].minprice*5);
                            searchParams.servises[_i_].maxprice = parseInt(searchParams.servises[_i_].maxprice*5);
                        }
                    }
                    else {
                        for (var _i_ in searchParams.servises) {
                            searchParams.servises[_i_].minprice = parseInt(searchParams.servises[_i_].minprice/5);
                            searchParams.servises[_i_].maxprice = parseInt(searchParams.servises[_i_].maxprice/5);
                        }
                    }
                    showServices(searchParams.servises);
                    clearAddServiceForm();
                }
            }
        });
        var topsel = $('.search-box div.step2 .addservice .selectcategoryh select');
        for (var i in topCategories) {
            var el = $(document.createElement('option'));
            el.val(topCategories[i].Id);
            el.html(topCategories[i].CategoryName);
            topsel.append(el);
        }

        topsel.change(function(){loadSubcategories($(this).val())});
        selectSmal('selectcategory');
        selectSmal('selectsubcategory');

        initSlider($('.search-box div.step2 .pricesliderh'));

        var tdesc = $('.search-box div.step2 .servdescription textarea');
        tdesc.focus(function(){
            if ($(this).val() == 'Укажите детали вашего мероприятия') $(this).val('');
        }).blur(function(){
            if ($(this).val() == '') $(this).val('Укажите детали вашего мероприятия');
        }).trigger('blur');
        tdesc.keyup(function(e){
            if ($(this).val().length > 370) {
                $(this).val($(this).val().substr(0, 370));
                showError('Информация не должна превышать 370 символов!');
                e.preventDefault();
                return false;
            }
        });
        var addbut = $('.search-box div.step2 .addservicebutton a.add');
        addbut.click(function(){
            var pvalues = $('.search-box div.step2 .pricesliderh .priceslider').slider("option", "values");
            var service = {
                id: 'new' + (new Date().getTime()),
                catid: topsel.val(),
                subcatid: $('.search-box div.step2 .addservice .selectsubcategoryh select').val(),
                minprice: pvalues[0],
                maxprice: pvalues[1],
                description: $.trim(tdesc.val()),
                catname: '',
                subcatname: ''
            };

            for (var i in searchParams.servises) {
                if (typeof searchParams.servises[i].subcatid != 'undefined' && searchParams.servises[i].subcatid == service.subcatid) {
                    showError('Можно только 1 раз добавить одну и ту же услугу в Комплексном поиске на 2-м этапе');
                    return;
                }
            }
            service = showService(service, searchParams.servises.length + 1);
            if (service) {
                searchParams.servises.push(service);
                clearAddServiceForm();
                $('.search-box div.step2 .listservices').show();
            }
        });
        var savebut = $('.search-box div.step2 .addservicebutton a.save');
        savebut.click(function(){
            var serv, id = $(this).attr('sid');
            for (var i in searchParams.servises) {
                if (searchParams.servises[i].id == id) {
                    serv = searchParams.servises[i];
                    var pvalues = $('.search-box div.step2 .pricesliderh .priceslider').slider("option", "values");
                    serv.catid = topsel.val();
                    serv.subcatid = $('.search-box div.step2 .addservice .selectsubcategoryh select').val();
                    serv.minprice = pvalues[0];
                    serv.maxprice = pvalues[1];
                    serv.description = $.trim(tdesc.val());
                    serv.subcatname = '';
                    serv.catname = '';
                    if (editService(serv, id)) clearAddServiceForm();
                    $('.search-box div.step2 .listservices').show();
                }
            }
        });
    }
    else {
        refreshSlider($('.search-box div.step2 .pricesliderh'));
        clearAddServiceForm();
    }
    showServices(searchParams.servises);

}

function loadSubcategories(id, current) {
    if (typeof(secondCategories[id]) == 'undefined') {
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/index/get-subcategories/',
            data: {id:id},
            success: function(data){
                secondCategories[id] = data;
                showSubcategories(secondCategories[id], current);
            },
            dataType: 'json'
        });
    }
    else {
        showSubcategories(secondCategories[id], current);
    }
}

function showSubcategories(list, current) {
    var sel = $('.search-box div.step2 .addservice .selectsubcategoryh select');
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

function initSlider(div) {
    var min = searchParams.getMinPrice();
    var max = searchParams.getMaxPrice();
    $('.lowprice', div).html(min).append($(document.createElement('span')).html(searchParams.currency()));
    $('.highprice', div).html(max + '+').append($(document.createElement('span')).html(searchParams.currency()));

    $('.priceslider', div).slider({
        range: true,
        min: min,
        max: max,
        step: searchParams.getSliderStep(),
        animate: true,
        values: [min, max],
        slide: function( event, ui ) {
            $('.lowprice', div).html(ui.values[0]).append($(document.createElement('span')).html(searchParams.currency()));
            $('.highprice', div).html(ui.values[1] == searchParams.getMaxPrice() ? ui.values[1] + '+' : ui.values[1]).append($(document.createElement('span')).html(searchParams.currency()));
        }
    });
}

function refreshSlider(div, curmin, curmax) {
    var min = searchParams.getMinPrice();
    var max = searchParams.getMaxPrice();

    if (typeof(curmin) == 'undefined') curmin = min;
    if (typeof(curmax) == 'undefined') curmax = max;

    $('.priceslider', div).slider("option", "min", min);
    $('.priceslider', div).slider("option", "max", max);
    $('.priceslider', div).slider("option", "step", searchParams.getSliderStep());
    $('.priceslider', div).slider("option", "values", [curmin, curmax]);
    $('.priceslider', div).slider("values", $('.priceslider', div).slider("values"));

    $('.lowprice', div).html(curmin).append($(document.createElement('span')).html(searchParams.currency()));
    $('.highprice', div).html(curmax + (curmax == searchParams.getMaxPrice() ? "+" : '')).append($(document.createElement('span')).html(searchParams.currency()));

}

function showService(service, number) {
    var servstr = '<div class="service"><div class="relpos"><a class="delete"></a></div><div class="number"></div><div class="cats"><div>Услуга:</div><div>Бюджет:</div><div>Описание:</div></div><div class="ebutton"><a></a></div><div class="sdata"><div class="row1"><b><i></i></b> &gt; <b><i></i></b></div><div class="row2"><b></b></div><div class="row3"></div></div><div class="clear"></div></div>';
    var serv = $(servstr);

    for (var i in topCategories) {
        if (topCategories[i].Id == service.catid) {
            service.catname = topCategories[i].CategoryName;
        }
    }

    if (service.catname == '') {
        showError('Выберите категорию');
        return;
    }

    //console.log(secondCategories);
    for (var i in secondCategories[service.catid]) {
        if (secondCategories[service.catid][i].Id == service.subcatid) {
            service.subcatname = secondCategories[service.catid][i].CategoryName;
        }
    }

    if (service.subcatname == '') {
        showError('Выберите подкатегорию');
        return;
    }

    if (service.description == '' || service.description == 'Укажите детали вашего мероприятия') {
        showError('Укажите детали вашего мероприятия');
        return;
    }




    //searchParams.servises.push(service);

    $('.number', serv).html(number + '.');
    $('a.delete', serv).attr('rel', service.id).click(function(){
        if (confirm('Вы уверены?')) {
            var id = $(this).attr('rel');

            var k = -1;
            for (var j in searchParams.servises) {
                if (searchParams.servises[j].id == id) {
                    k = j;
                }
            }
            if (k >= 0) searchParams.servises.splice(k, 1);
            if (searchParams.servises.length == 0) {
                $('.search-box div.step2 .listservices').hide();
            }
            clearAddServiceForm();
            showServices(searchParams.servises);
        }
    });
    $('.ebutton a', serv).attr('sid', service.id).click(function(){
        var serv, id = $(this).attr('sid');
        for (var i in searchParams.servises) {
            if (searchParams.servises[i].id == id) serv = searchParams.servises[i];
        }
        if (serv) showEditForm(serv);
        else showError('error');
    });
    $('.row1 i:eq(0)', serv).html(service.catname);
    $('.row1 i:eq(1)', serv).html(service.subcatname);
    var maxpricetext = service.maxprice == searchParams.getMaxPrice() ? service.maxprice + '+' : service.maxprice;
    $('.row2 b', serv).html('от ' + service.minprice + ' до ' + maxpricetext + ' ' + searchParams.currency(true));
    $('.row3', serv).html(service.description);
    serv.attr('id', service.id);
    $('#addedlist').append(serv);
    return service;
}

function viewService(service, number) {
    var servstr = '<div class="listservices"><div class="service"><div class="number"></div><div class="cats"><div>Услуга:</div><div>Бюджет:</div><div>Описание:</div></div><div class="sdata"><div class="row1"><b><i></i></b> &gt; <b><i></i></b></div><div class="row2"><b></b></div><div class="row3"></div></div><div class="clear"></div></div></div>';
    var serv = $(servstr);

    $('.number', serv).html(number + '.');
    $('.row1 i:eq(0)', serv).html(service.catname);
    $('.row1 i:eq(1)', serv).html(service.subcatname);
    var maxpricetext = service.maxprice == searchParams.getMaxPrice() ? service.maxprice + '+' : service.maxprice;
    $('.row2 b', serv).html('от ' + service.minprice + ' до ' + maxpricetext + ' ' + searchParams.currency(true));
    $('.row3', serv).html(service.description);
    $('#servlist').append(serv);
}

function editService(service, id) {
    var serv = $('#' + id);
    for (var i in topCategories) {
        if (topCategories[i].Id == service.catid) {
            service.catname = topCategories[i].CategoryName;
        }
    }

    if (service.catname == '') {
        showError('Выберите категорию');
        return false;
    }

    for (var i in secondCategories[service.catid]) {
        if (secondCategories[service.catid][i].Id == service.subcatid) {
            service.subcatname = secondCategories[service.catid][i].CategoryName;
        }
    }

    if (service.subcatname == '') {
        showError('Выберите подкатегорию');

        return false;
    }

    if (service.description == '' || service.description == 'Укажите детали вашего мероприятия') {
        showError('Укажите детали вашего мероприятия');
        return false;
    }

    $('.row1 i:eq(0)', serv).html(service.catname);
    $('.row1 i:eq(1)', serv).html(service.subcatname);
    var maxpricetext = service.maxprice == searchParams.getMaxPrice() ? service.maxprice + '+' : service.maxprice;
    $('.row2 b', serv).html('от ' + service.minprice + ' до ' + maxpricetext + ' ' + searchParams.currency(true));
    $('.row3', serv).html(service.description);


    for (var i in searchParams.servises) {
        if (searchParams.servises[i].id == id) searchParams.servises[i] = service;
    }

    return true;
}

function showServices(list) {
    $('#addedlist').html('');
    for (var i in list) {
        showService(list[i], parseInt(i) + 1);
        $('.search-box div.step2 .listservices').show();
    }
}

function clearAddServiceForm() {
    $('.search-box div.step2 .servdescription textarea').val('').trigger('blur');
    refreshSlider($('.search-box div.step2 .pricesliderh'));
    loadSubcategories(0);
    var sel = $('.search-box div.step2 .addservice .selectcategoryh select');
    $('option', sel).each(function(){
        if ($(this).val() == "0") $(this).attr("selected", "selected");
        else $(this).attr("selected", false);
    });

    $('span', sel.parent()).remove();
    selectSmal('selectcategory');
    $('.search-box div.step2 .addservicebutton .add').show();
    $('.search-box div.step2 .addservicebutton .save').attr('sid', '').hide();
    $('.search-box div.step2 .addservice .title').html('Добавить услугу');
}

function showEditForm(service) {
    $('.search-box div.step2 .servdescription textarea').val(service.description).trigger('blur');
    refreshSlider($('.search-box div.step2 .pricesliderh'), service.minprice, service.maxprice);
    loadSubcategories(service.catid, service.subcatid);
    var sel = $('.search-box div.step2 .addservice .selectcategoryh select');
    $('option', sel).each(function(){
        if ($(this).val() == service.catid) $(this).attr("selected", "selected");
        else $(this).attr("selected", false);
    });

    $('span', sel.parent()).remove();
    selectSmal('selectcategory');
    $('.search-box div.step2 .addservice .title').html('Изменить услугу');
    $('.search-box div.step2 .addservicebutton .add').hide();
    $('.search-box div.step2 .addservicebutton .save').attr('sid', service.id).show();
}


function showBlockedPhonePopup(seconds) {

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
    clock.html(getClockText(seconds_cnt--));
    var timer_int = setInterval(function(){
        //if (seconds_cnt <= 0 || $('.clock').length < 1) {
        if (seconds_cnt <= 0) {
            clearInterval(timer_int);
            hideUserPopup();
            showPhoneNumberPopup();
            seconds_cnt = 0;
        }
        clock.html(getClockText(seconds_cnt));
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

function getClockText(seconds) {
    var seconds_cnt = parseInt(seconds);
    var mins = parseInt(seconds_cnt / 60);
    var secs = seconds_cnt % 60;
    mins = mins.toString();
    secs = secs.toString();
    if (mins.length < 2) mins = '0' + mins;
    if (secs.length < 2) secs = '0' + secs;
    return mins + ':' + secs;
}

function showPhoneNumberPopup() {
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
        showBlockedPhonePopup(blockedInfo.seconds);
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
    phoneinp.focus(function() {
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
                        showBlockedPhonePopup(data.seconds);
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
                        showBlockedPhonePopup(data.seconds);
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
            var params = {};
            for (var p in searchParams) {
                if (typeof(searchParams[p]) != 'function') params[p] = searchParams[p];
            }
            params.date = {y: searchParams.date.getFullYear(), m: searchParams.date.getMonth() + 1, d: searchParams.date.getDate()};
            params.saveNumber = remember.hasClass('save_number_on') ? 2 : 1;
            $.ajax({
                type: 'POST',
                url: HTTP_HOST + '/activity/search-form/',
                data: {params: params},
                success: function(data) {
                    if (typeof(data.error) != 'undefined') {
                        showError(data.error);
                        return;
                    }
                    window.location.href = HTTP_HOST + '/client/index/show/1';
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

