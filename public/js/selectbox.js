/*
** (c) Dimox | http://dimox.name/styling-select-boxes-using-jquery-css/
*/
function selectSmal(_classname){
(function($) {
$(function() {

  $(document).bind('click', function(e) {
		var clicked = $(e.target);
		if (!clicked.parents().hasClass('dropdown')) {
			$('span.selectbox-' + _classname + ' ul.dropdown').hide().find('li.sel').addClass('selected');
			$('span.selectbox-' + _classname).removeClass('focused');
		}
	});

	$('select.' + _classname).each(function() {
        var selecttag = $(this);
		var option = $(this).find('option');
		var optionSelected = $(this).find('option:selected');
		var dropdown = '';
		var selectText = $(this).find('option:first').html();
		if (optionSelected.length) selectText = optionSelected.text();

		for (i = 0; i < option.length; i++) {
			var selected = '';
			var disabled = ' class="disabled"';
			if ( option.eq(i).is(':selected') ) selected = ' class="selected sel"';
			if ( option.eq(i).is(':disabled') ) selected = disabled;
			dropdown += '<li' + selected + '>'+ option.eq(i).text() +'</li>';
		}

		$(this).before(
			'<span class="selectbox-' + _classname + '" style="display: inline-block; position: relative">'+
				'<span class="select" style="float: left; position: relative;"><span class="text">' + selectText + '</span>'+
					'<b class="trigger"><i class="arrow"></i></b>'+
				'</span>'+
				'<ul class="dropdown" style="position: absolute; z-index: 999999; overflow: auto; overflow-x: hidden; list-style: none">' + dropdown + '</ul>'+
			'</span>'
		).css({position: 'absolute', left: -9999});

		var ul = $(this).prev().find('ul');
		var selectHeight = $(this).prev().outerHeight();
		if ( ul.css('left') == 'auto' ) ul.css({left: 0});
		if ( ul.css('top') == 'auto' ) ul.css({top: selectHeight});
		var liHeight = ul.find('li').outerHeight();
		var position = ul.css('top');
		ul.hide();

		/* при клике на псевдоселекте */
		$(this).prev().find('span.select').click(function() {

			/* умное позиционирование */
			var topOffset = $(this).parent().offset().top;
			var bottomOffset = $(window).height() - selectHeight - (topOffset - $(window).scrollTop());
			
			/*if (bottomOffset < 0 || bottomOffset < liHeight * 6)	{
				ul.height('auto').css({top: 'auto', bottom: '39px', 'border-top': 'solid 1px #ffffff', 'border-bottom': 'none'});
				if (ul.outerHeight() > topOffset - $(window).scrollTop() - 20 ) {
					ul.height(Math.floor((topOffset - $(window).scrollTop() - 20) / liHeight) * liHeight);
				}
			} else*/ 
			if (bottomOffset > liHeight * 6) {
				ul.height('auto').css({bottom: 'auto', top: position, 'border-bottom': 'solid 1px #ffffff', 'border-top': 'none'});
				if (false && ul.outerHeight() > bottomOffset - 20 ) {
					ul.height(Math.floor((bottomOffset - 20) / liHeight) * liHeight);
				}
			}

			/*$('span.selectbox-' + _classname).css({zIndex: 1}).removeClass('focused');*/
			$('span.selectbox-' + _classname).removeClass('focused');
			if ( $(this).next('ul').is(':hidden') ) {
				$('ul.dropdown:visible').hide();
				$(this).next('ul').show();
			} else {
				$(this).next('ul').hide();
			}
			//$(this).parent().css({zIndex: 2});
			return false;
		});

		/* при наведении курсора на пункт списка */
		$(this).prev().find('li:not(.disabled)').hover(function() {
			$(this).siblings().removeClass('selected');
		})
		/* при клике на пункт списка */
		.click(function() {
			$(this).siblings().removeClass('selected sel').end()
				.addClass('selected sel').parent().hide()
				.prev('span.select').find('span.text').text($(this).text())
			;
			option.removeAttr('selected').eq($(this).index()).attr({selected: 'selected'});
			$(this).parents('span.selectbox-' + _classname).next().change();
		});

		/* фокус на селекте при нажатии на Tab */
		$(this).focus(function() {
			$('span.selectbox-' + _classname).removeClass('focused');
			$(this).prev().addClass('focused');
		})
		/* меняем селект с клавиатуры */
		.keyup(function() {
			$(this).prev().find('span.text').text($(this).find('option:selected').text()).end()
				.find('li').removeClass('selected sel').eq($(this).find('option:selected').index()).addClass('selected sel')
			;
		});

	});

})
})(jQuery)
}