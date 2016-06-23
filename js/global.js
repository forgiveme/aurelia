/***************************
Author: IT-Serve Web Design, Fife, Scotland
Author URL: http://www.itserve.co.uk
Client URL: http://aureliaskincare.com
Jquery Plugins & Global Script Settings

Includes: 	
			BoxGrid - Products and Aurelia TV Image hover slide effect
			Drop down Subnav
			bxSlider (video carousel) Settings 			
			JQuery Placeholder Fix
			
****************************/


jQuery(function($) { 
				
//Boxgrid - Products
				$('.boxgrid.captionfull').hover(function(){
					$(".cover", this).stop().animate({top:'95px'},{queue:false,duration:150});
				}, function() {
					$(".cover", this).stop().animate({top:'130px'},{queue:false,duration:100});			
					
				});
				
			
//Boxgrid - Aurelia TV
			$('.boxgrid-tv.captionfull-tv').hover(function(){
					$(".cover-tv", this).stop().animate({top:'138px'},{queue:false,duration:150});
				}, function() {
					$(".cover-tv", this).stop().animate({top:'155px'},{queue:false,duration:100});
				});


//Drop down Subnav
$(".subnav", this).css({height: "0px", opacity:"0"});	

$(".nav-dropdown").hover(function(){
        	$(".subnav", this).stop().animate({height: "30px", opacity:"0.65"}, 'fast');
			},
			function() {	         
		    	$(".subnav", this).stop().animate({height: "0px", opacity:"0"}, 'fast');
				});


// bxSlider for Product Image Carousel
  $('#product-image-slider').bxSlider({
    displaySlideQty: 4,
    moveSlideQty: 1,
	easing: 'easeOutQuart'
  });
// bxSlider for Aurelia TV Carousel
  $('#aurelia-tv-slider').bxSlider({
    displaySlideQty: 4,
    moveSlideQty: 1,
	easing: 'easeOutQuart'
  });
// bxSlider for Footer Video Gallery Carousel
  $('#video-slider').bxSlider({
    displaySlideQty: 3,
    moveSlideQty: 1,
	easing: 'easeOutQuart'
  });
  
//A-Z Tabs	
$('.nav-tabs > li > a').click(function(event){
	event.preventDefault();//stop browser to take action for clicked anchor
	
	//get displaying tab content jQuery selector
	var active_tab_selector = $('.nav-tabs > li.active > a').attr('href');

	
	//find actived navigation and remove 'active' css
	var actived_nav = $('.nav-tabs > li.active');
	actived_nav.removeClass('active');
	
	//add 'active' css into clicked navigation
	$(this).parents('li').addClass('active');
	
	//hide tab content	
	$(active_tab_selector).css('display','none');
	
	//show target tab content with fade
	var target_tab_selector = $(this).attr('href');
	$(target_tab_selector).fadeIn('fast');
	});
});

/*
 * jQuery Placeholder Plugin
 */
(function($){$.fn.placeHolder=function(options){var eo=this;var settings={'text':'Placeholder','placeholder':'#999','active':'#000'};return this.each(function(){if(options){$.extend(settings,options);}
eo.val(settings.text);eo.css("color",settings.placeholder);eo.focus(function(){if(eo.val()==settings.text){eo.css("color",settings.active);eo.val("");}});eo.focusout(function(){$("#search_box img").css("display","none");if(eo.val()==""||eo.val()==settings.text){eo.val(settings.text);eo.css("color",settings.placeholder);}});});};})(jQuery);






