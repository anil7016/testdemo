jQuery(document).ready(function($){

	/*	Accordion Shortcode */	
	$(".hts-accordion__title").click(function () {
		$(this).parents('.hts-accordion').children('.hts-accordion__section').removeClass('hts-accordion__section--active');
		$(this).parent().toggleClass('hts-accordion__section--active');	  
	});

});