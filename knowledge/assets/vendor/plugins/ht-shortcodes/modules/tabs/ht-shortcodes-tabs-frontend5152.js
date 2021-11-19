jQuery(document).ready(function($){

	$('.hts-tabs .hts-tabs__nav .hts-tabs__tab').click(function(){
		var tab_id = $(this).attr('data-tab');

		$(this).parents('.hts-tabs__nav').children('.hts-tabs__tab').removeClass('hts-tabs__tab--active').delay(300).queue(function(nxt) {
	      	$(this).parents('.hts-tabs__nav').find('[data-tab="' + tab_id + '"]').toggleClass('hts-tabs__tab--active').colo;
	      nxt();
		});
		$(this).parents('.hts-tabs').children('.hts-tabs__content').removeClass('hts-tabs__content--active').delay(300).queue(function(nxt) {
			$(this).parents('.hts-tabs').find(tab_id).toggleClass('hts-tabs__content--active');	
	      nxt();
		});		

	})

});