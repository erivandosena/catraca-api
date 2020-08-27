$(function(){

	$('img').width($('.tres colunas').width()).height($(window).height());
	$(window).resize(function(event) {
		$('img').width($(window).width()).height($(window).height());
	});
});