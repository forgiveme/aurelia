jQuery(window).load(function(){
	jQuery(document).ready(function () {
		jQuery('#btn_popup1').click(function() {
			jQuery('html').addClass('overlay');
			jQuery('.cms-aureliaboutiquedemo .header-container .desktop').css('z-index','0');
			var activePopup = jQuery('#boutique-popup2-popup-sub');
			jQuery(activePopup).addClass('visible');
			window.scrollTo(0,0);
		});
		jQuery('#btn_popup2').click(function() {
			jQuery('html').addClass('overlay');
			jQuery('.cms-aureliaboutiquedemo .header-container .desktop').css('z-index','0');
			var activePopup = jQuery('#boutique-popup1-popup-sub');
			jQuery(activePopup).addClass('visible');
			window.scrollTo(0,0);
		});
		jQuery('.popup-exit-sub').click(function() {
			clearPopup('popup-sub');
		});
	});
	function clearPopup(elem) {
		jQuery('.'+elem+'.visible').addClass('transitioning').removeClass('visible');
		jQuery('html').removeClass('overlay');

		setTimeout(function () {
			jQuery('.'+elem).removeClass('transitioning');
		}, 200);
		jQuery('.cms-index-index .header-container .desktop').css('z-index','10000');
	}
});
