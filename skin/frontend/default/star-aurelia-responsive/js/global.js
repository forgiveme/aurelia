(function() {
	var $ = jQuery.noConflict();
$('document').ready(function(){
//Boxgrid - Products
	$('.yotpo-label.yotpo-small-box').css('display','block !important');
	$('.yotpo-menu-mobile-collapse').show(300); 
// hide things function overrides yotpo external stuff

function hidethings(){
	// $('.yotpo-thankyou-content > span').text('Please share on social media');
	$('.yotpo-icon-profile-social').css('display','none');
	
	var htmlString = $('#fieldEmail').val();
	// alert ("string is " + htmlString);
	
	if (htmlString  != '')
	{
		
		$('#submit').attr("disabled", false);
	}
	else
	{
		$('#submit').attr("disabled", true);
	}
		
	
	 x = setTimeout(function(){hidethings()}, 500);
	 }
	 hidethings();
	 
	$('.boxgrid.captionfull').hover(
		function(){
			$(".cover", this).stop().animate({top:'95px'},{queue:false,duration:150});
		}, 
		function() {
			$(".cover", this).stop().animate({top:'130px'},{queue:false,duration:100});
		}
	);

//Boxgrid - Aurelia TV
	$('.boxgrid-tv.captionfull-tv').hover(
		function(){
			$(".cover-tv", this).stop().animate({top:'138px'},{queue:false,duration:150});
		},
		function() {
			$(".cover-tv", this).stop().animate({top:'155px'},{queue:false,duration:100});
		}
	);

//Drop down Subnav
$(".subnav", this).css({height: "0px", opacity:"0"});

$(".nav-dropdown").hover(
	function(){
		$(".subnav", this).stop().animate({height: "30px", opacity:"1"}, 'fast');
	},
	function() {
		$(".subnav", this).stop().animate({height: "0px", opacity:"0"}, 'fast');
	}
);

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
minSlides: 3,
 maxSlides: 3,
  pager: false,
   slideWidth: 330,
    slideMargin: 10,
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

// product attributes accordion
if($("#accordion, #accordion1, #accordion2").length > 0) {
$("#accordion, #accordion1, #accordion2").accordion({
	    header: "> h3",
	    collapsible: true,
	    autoHeight: false,
	    navigation: true 
	});
}

$("#accordion, #accordion1, #accordion2").on('accordionchange',function(e,ui){
	e.stopPropagation();
	if(ui.newHeader.length > 0){
	var myid='last-heading';
	if(ui.newHeader.attr('id')==myid){
		$('body, html').animate({ scrollTop: (ui.newHeader.offset().top)-8 }, 200);	
		}
	
	}
	
});

if(jQuery.flexslider) {
	
	// main product page slider
	$('.js-product-img__main').flexslider({
		controlNav: false,
		directionNav: false
	});


	// product page thumbnail navigation
	$('.js-product-img__thumbs').flexslider({
			animation: 'slide',
			animationLoop: false,
			slideshow: false,
			controlNav: false,
			directionNav: true,
			useCss: false,
			minItems: 2,
			maxItems: 3,
			itemWidth: 75,
			itemMargin: 0,
			move: 1,
			controlsContainer: '.more-views',
			asNavFor: '.js-product-img__main'
		});
}

// <summary>
// Cart Page Gift Wrap
// </summary>
if($('.checkout-cart-index').length > 0) {
	var $giftWrap = $('#gift_wrap_check'),
		$giftWrapChecked = $('#gift_wrap_checked'),
		$giftMessage = $('.gift-message-holder');

	// gift wrap isn't checked on the cart page
	if($giftWrap.length > 0){
		$giftMessage.hide();
	}

	if($giftWrapChecked.length > 0 || quoteHasGiftCard == true) {
		$giftMessage.show();
	}	
}



// <summary>
// Checkout Gift Wrap
// </summary>
if($('.checkout-cart-index').length > 0) {
	var $giftWrap = $('#id_extra_product_7'),
		$giftMessage = $('#onestepcheckout-giftmessages');

	// gift wrap isn't checked on the cart page
	if($giftWrap.length > 0){
		if(!$giftWrap.is(':checked')){
			$giftMessage.hide();
		}

		if(quoteHasGiftCard == true) {
			$giftMessage.show();
		}

		$giftWrap.on('change',function(e){
			if(this.checked || quoteHasGiftCard == true) {
				$giftMessage.show();
			} else {
				$giftMessage.hide();
			}
		});
		
	}
}




}); // end document ready

})();
/*
* jQuery Placeholder Plugin
*/
(function($){$.fn.placeHolder=function(options){var eo=this;var settings={'text':'Placeholder','placeholder':'#999','active':'#000'};return this.each(function(){if(options){$.extend(settings,options);}
eo.val(settings.text);eo.css("color",settings.placeholder);eo.focus(function(){if(eo.val()==settings.text){eo.css("color",settings.active);eo.val("");}});eo.focusout(function(){$("#search_box img").css("display","none");if(eo.val()==""||eo.val()==settings.text){eo.val(settings.text);eo.css("color",settings.placeholder);}});});};})(jQuery);



