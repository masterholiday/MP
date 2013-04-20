function getUserPopup(title) {
    var popup = $(document.createElement('div')).addClass('user_popup');
    var popup_rel = $(document.createElement('div')).addClass('user_popup_rel');
    var popup_top = $(document.createElement('div')).addClass('user_popup_top');
    var changepasstitle = $(document.createElement('div')).addClass('changepasstitle').html(title);
    popup_top.append(changepasstitle);
	/*popup_top.append($(document.createElement('div')).addClass('relpos'));*/
    popup_rel.append(popup_top);
    popup_rel.append($(document.createElement('div')).addClass('user_popup_c'));
    var popup_bottom = $(document.createElement('div')).addClass('user_popup_bottom');
    popup_bottom.append($(document.createElement('div')).addClass('relpos'));
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
    if ($('.user_popup_shadow').length > 0) return $(document.createTextNode(''));
    return $(document.createElement('div')).addClass('user_popup_shadow');
}

function hideUserPopup() {
    if ($('.user_popup').length > 0) $('.user_popup').remove();
    if ($('.user_popup_shadow').length > 0) $('.user_popup_shadow').remove();
}

