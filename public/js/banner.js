function hide_banner() {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/index/hide-banner/',
        data: {},
        success: function(data){
            $('.mt_banner').remove();
        },
        dataType: 'json'
    });
}