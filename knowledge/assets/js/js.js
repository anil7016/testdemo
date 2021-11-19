// Start Wrapper
jQuery(document).ready(function($) {

	// Smooth Scroll
	$(function() {
	  $('a[href*="#"]:not([href="#"])').click(function() {
	    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
	      var target = $(this.hash);
	      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
	      if (target.length) {
	        $('html,body').animate({
	          scrollTop: target.offset().top
	        }, 1000);
	        return false;
	      }
	    }
	  });
	});

	// Mobile Nav Toggle
	$('#ht-navtoggle').on('click', function(e) {
		$('#ht-navtoggle, .ht-global-overlay').toggleClass("active");
		e.preventDefault();
    });

	// HKB searching event
	$('.hkb-site-search__field').keypress(function() {
		jQuery('body').addClass( "htevent-hkb-searching" );
	});
	$( ".hkb-site-search__field" ).blur( function() {
   		jQuery('body').removeClass( "htevent-hkb-searching" );
	});

	function setSiteHeaderVariables() {
		var headerHeight = $('.site-header__banner').outerHeight();
	  	var adminBarHeight = $('#wpadminbar').outerHeight();

		jQuery('.ht-global-overlay').css({ "top": headerHeight + adminBarHeight });
		jQuery('.nav-header__menuwrapper').css({ "top": headerHeight });
	}

	function sideBarVariables(){
		$( '.sidebar--sticky' ).each(function() {
		  var sidebar = $( this );
		  
		  //do we need to add the admin bar height?
		  var adminBarHeight = ( $('#wpadminbar').length > 0 ) ? 28 : 0 ;


		  //compute height from top
		  var sidebarFromTop = sidebar.offset().top + adminBarHeight ;

		  //add to inner container
		  var innerContainer = $(sidebar).find('.sidebar__innercontainer');
		  innerContainer.attr('data-offset-top', sidebarFromTop);

		  //calculte sidebar width and add it as a style attribut to the sidebar
		  //var sidebarWidth = sidebar.css('width');
		  var sidebarWidth = sidebar.width();
		  innerContainer.attr('style', 'width: ' + sidebarWidth + 'px');

		});

	}
	
	//inital load
	jQuery(window).load(function () {
	  setSiteHeaderVariables();
	  sideBarVariables();
	  kbarchiveheights();
	}); 


	//window resize
	jQuery( window ).resize(function() {
	  setSiteHeaderVariables();
	  sideBarVariables();
	  kbarchiveheights();
	});
 

	// Attachments Toggle
	jQuery(".hkb-article-attachments").click(function () {
		jQuery(this).toggleClass('hkb-article-attachments--active');	  
	});

	function kbarchiveheights(){
		// archive category height equalling
		if ( $( ".hkb-archive--style1 .hkb-archive--style3 .hkb-archive--style4 .hkb-archive--style5 .hkb-archive--style6" ).length ) {
	 
	    	var maxHeight = -1;

			$('.hkb-archive li').each(function() {
			 maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
			});

			$('.hkb-archive li').each(function() {
			 //$(this).height(maxHeight);
			 $('.hkb-archive .hkb-category.hkb-category--hasdesc a').css('min-height', maxHeight);
			});

		}
	}

// End Wrapper
});