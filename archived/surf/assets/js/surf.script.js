/**
 * @file
 * Custom scripts for theme.
 */
(function ($) {

	// Hello World.
/*
	Drupal.behaviors.helloWorld = {
	    attach: function (context) {
	      console.log('Hello World');
	    }
	}
*/
	
	// Append chevron icon to primary nav items
	$('.navbar-nav > li a').append('<i class="fa fa-chevron-down"></i>');
	
	// Hide page content type eyebrow
	$('div.content-type:contains("page")').addClass('hidden');
	
	
	// Toggle Search
	$('.search-block-form').click(function () {
		$(this).addClass('open');
		$( "#edit-keys" ).focus();
	});
	
	// Toggle Contract Body
	$('.action-toggle.contract').click(function () {
		$(this).addClass('open');
		$( ".field.body" ).fadeToggle();
	});
	
	
	// Check window size to determine whether or not to use megamenu toggles
    function resizeForm(){
        var width = (window.innerWidth > 0) ? window.innerWidth : document.documentElement.clientWidth;
        if(width > 767){	        
			// Disable primary nav buttons to allow for megamenu toggles
			$('.navbar-nav > li > a').click(function(e){
			     e.preventDefault();
			});	
        } else {
			// Enable primary nav buttons to disable megamenu toggles
			$('.navbar-nav > li > a').unbind('click')
        }    
    }
    window.onresize = resizeForm;
    resizeForm();
    
    
    // Megamenu hide function

	$(document).click(function(){
	 	$('.mega-menu .visible').hide(); 
	 	$('.mega-menu .visible').removeClass('visible'); 
	 	$('.navbar-primary .open').toggleClass('open'); 	
	});
	
	$('.mega-menu, .navbar-primary').click(function(e){
	  	e.stopPropagation(); 
	});	
	

	
	// Megamenu Toggles
	$('.science-and-discovery').click(function(){
		if ($(this).hasClass('open')) {
			$('.block--sciencemegamenu').slideToggle('fast');
			$(this).toggleClass('open');
		} else {
			$('.visible').hide();
			$('.block--sciencemegamenu').slideToggle('fast');
			$('.block--sciencemegamenu').addClass('visible');
			$('li.open').removeClass('open');
			$(this).toggleClass('open');
		}
	});
	$('.impact-and-history').click(function(){
		if ($(this).hasClass('open')) {
			$('.block--impactmegamenu').slideToggle('fast');
			$(this).toggleClass('open');
		} else {
			$('.visible').hide();
			$('.block--impactmegamenu').slideToggle('fast');
			$('.block--impactmegamenu').addClass('visible');
			$('li.open').removeClass('open');
			$(this).toggleClass('open');
		}
	});
	$('.education-and-outreach').click(function(){
		if ($(this).hasClass('open')) {
			$('.block--educationmegamenu').slideToggle('fast');
			$(this).toggleClass('open');
		} else {
			$('.visible').hide();
			$('.block--educationmegamenu').slideToggle('fast');
			$('.block--educationmegamenu').addClass('visible');
			$('li.open').removeClass('open');
			$(this).toggleClass('open');
		}
	});
	
	
	$(function(){
	
	    var url = window.location.pathname, 
	        urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
	        // now grab every link from the navigation
	        $('.subnav-item a').each(function(){
	            // and test its normalized href against the url pathname regexp
	            if(urlRegExp.test(this.href.replace(/\/$/,''))){
	                $(this).addClass('is-active');
	            }
	        });
	
	});
	

	$(function() {
	  // Find all YouTube and Vimeo videos
	  var $allVideos = $("iframe[src*='www.youtube.com'], iframe[src*='player.vimeo.com']");
	
	  // Figure out and save aspect ratio for each video
	  $allVideos.each(function() {
	    $(this)
	      .data('aspectRatio', this.height / this.width)
	      // and remove the hard coded width/height
	      .removeAttr('height')
	      .removeAttr('width');
	  });
	
	  // When the window is resized
	  $(window).resize(function() {
	    // Resize all videos according to their own aspect ratio
	    $allVideos.each(function() {
	      var $el = $(this);
	      // Get parent width of this video
	      var newWidth = $el.parent().width();
	      $el
	        .width(newWidth)
	        .height(newWidth * $el.data('aspectRatio'));
	    });
	
	  // Kick off one resize to fix all videos on page load
	  }).resize();
	});

	
	

  
	// Add affix class to header upon scroll
	//$('.navbar').affix({
	   // offset: {top: 150}
	//});

})(jQuery);
