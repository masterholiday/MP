$('.plusminus a').live('click', function(){
    var p = $(this).parent().parent().parent();
    if ($(this).hasClass('opened')) {
        $(this).removeClass('opened');
        $('ul li', p).hide();
        if ($('.one', p).length > 0) $('.one', p).parent().show();
    }
    else {
        $(this).addClass('opened');
        showUL($('ul', p));
        $('ul li', p).show();
        if ($('.one', p).length > 0) $('.one', p).parent().hide();
    }
});

function showUL(ul) {
    $('.gwl', ul).remove();
    $('.gwl2', ul).remove();
    $('li.empty', ul).remove();
    var t = $('li', ul).length;
    $('li', ul).each(function(i){
        if ($(this).hasClass('oneone')) return;
        if (i % 2 == 0) {
            $(this).append($('<div class="gwl"><div></div></div>'));
            $(this).append($('<div class="gwl2"><div></div></div>'));
        }
        else {
            $(this).addClass('reddi3');
            $(this).after($('<li class="empty"><div class="gwl"><div></div></div><div class="gwl2"><div></div></div></li>'));
        }
    });
}



$('.delsearch a').live('mouseover', function(){
    $('div', $(this).parent()).show();
});
$('.delsearch a').live('mouseout', function(){
    $('div', $(this).parent()).hide();
});
$('.delsearch a').live('click', function(){
    var sid = $(this).attr('sid').split('|');
    noty({
        text: 'Удалить запрос?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/client/delete-search-service/',
                        data: {sid:sid[1], cid:sid[0]},
                        success: function(data) {
                            if (typeof(data.error) != 'undefined') {
                                showError(data.error);
                                return;
                            }
                            var p = $('#s' + sid[2]).parent();
                            $('#s' + sid[2]).remove();
                            if ($('.request', p).length < 1) window.location.href = currentAddress + '/delete/' + sid[1];
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
$('.delsearch2 a').live('mouseover', function(){
    $('div', $(this).parent()).show();
});
$('.delsearch2 a').live('mouseout', function(){
    $('div', $(this).parent()).hide();
});
$('.delsearch2 a').live('click', function(){
    var id = $(this).attr('scatid');
    noty({
        text: 'Удалить запрос?',
        layout: 'center',
        buttons: [
            {
                addClass: 'btn btn-primary',
                text: 'Да',
                onClick: function($noty) {
                    $noty.close();
                    $.ajax({
                        type: 'POST',
                        url: HTTP_HOST + '/client/delete-search-catalog/',
                        data: {id:id},
                        success: function(data) {
                            if (typeof(data.error) != 'undefined') {
                                showError(data.error);
                                return;
                            }
                            window.location.href = currentAddress;
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
