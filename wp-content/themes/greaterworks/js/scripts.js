
//...........I straight up stole this juicy script and footer idea from http://www.ikreativ.com
jQuery(function($) {
    var slide = false;
    var height = $('#footer-bar').height();
    $('#footer-button span').click(function() {
        var docHeight = $(document).height();
        var windowHeight = $(window).height();
        var scrollPos = docHeight - windowHeight + height;
    $('#footer-bar').animate({ height: "toggle"}, 1000);

        if(slide == false) {
            if($.browser.opera) {
                $('html').animate({scrollTop: scrollPos+'px'}, 1000);
            		} else {
                $('html, body').animate({scrollTop: scrollPos+'px'}, 1000);
            	}
               	slide = true;
        		} else {
                slide = false;
      		}
    });
});