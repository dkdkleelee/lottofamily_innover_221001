
$(document).ready(function() {	

	$('#gnb_1dul > .gnb_1dli .gnb_1da').removeClass('gnb_1da').addClass('gnb-1da');
	$('#gnb_1dul > .gnb_1dli > .gnb_2dul').removeClass('gnb_2dul');
	$('#gnb_1dul > .gnb_1dli').removeClass('gnb_1dli');
	$('#gnb_1dul').removeAttr('id').addClass('cateist');

	var cateist_height = $('.cateist').height() - 20;
	$('#wrapper').attr('style', 'min-height:'+cateist_height+'px;');
	
});