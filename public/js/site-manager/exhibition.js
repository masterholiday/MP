function deleteImage(id) {
    if(confirm('Вы действительно хотите удалить картинку?')){
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/site-manager/exhibition/delete-image',
            data: {id:id},
            success: function(data, textStatus){
                $('#imt' + id).remove();
            },
            dataType: 'json'
        });
    }
}

function deleteExhibition(id) {
    if(confirm('Вы действительно хотите удалить выставку?')){
        $.ajax({
            type: 'POST',
            url: HTTP_HOST + '/site-manager/exhibition/delete-exhibition',
            data: {id:id},
            success: function(data, textStatus){
                $('#imt' + id).remove();
            },
            dataType: 'json'
        });
    }
}

function changeActivity(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/exhibition/public-exhibition',
        data: {id:id},
        success: function(data, textStatus){
            $('#imt' + id + ' td:last a:eq(0)').removeClass().addClass(data.clas);
        },
        dataType: 'json'
    });
}

function sortUp(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/exhibition/sort-image',
        data: {id:id, direction: 'up'},
        success: function(data, textStatus){
            window.location.reload();
        },
        dataType: 'json'
    });
}

function sortDown(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/exhibition/sort-image',
        data: {id:id, direction: 'down'},
        success: function(data, textStatus){
            window.location.reload();
        },
        dataType: 'json'
    });
}

function sortExUp(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/exhibition/sort-exhibition',
        data: {id:id, direction: 'up'},
        success: function(data, textStatus){
            window.location.reload();
        },
        dataType: 'json'
    });
}

function sortExDown(id) {
    $.ajax({
        type: 'POST',
        url: HTTP_HOST + '/site-manager/exhibition/sort-exhibition',
        data: {id:id, direction: 'down'},
        success: function(data, textStatus){
            window.location.reload();
        },
        dataType: 'json'
    });
}

