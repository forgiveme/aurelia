<div class="wmle_container <?php echo $shortcodeData['wmlo_responsive'] == 'yes'?'responsive':''; ?>" id="wmle_container" data-load-status="ready" data-seed="<?php echo rand(); ?>">
	<div class="wmle_item_holder <?php echo $shortcodeData['wmlo_columns'] ?>"></div> <!-- FILLER DIV FOR WIDTH REFERENCE -->
	<!-- POSTS LOADS HERE -->
</div>

<?php
	$link 				= admin_url('admin-ajax.php?action=wmlp_load_posts&shortcodeId='.$shortcodeId);
	$containerDivId		= 'wmle_container';
?>

<div class="wmle_loadmore" class="wmle_loadmore_<?php echo $shortcodeId; ?>">
	<?php if ($shortcodeData['wmlo_pagination_style'] == 'infinity_scroll'): ?>
    	<img src="<?php echo plugins_url('wp-masonry-layout-pro/images/loader/wmli_loading_04.gif'); ?>" class="loading_icon" /><br/>
		<a href="<?php echo $link; ?>" class="wmle_loadmore_btn" rel="<?php echo $containerDivId; ?>" style="display:none;">Load More</a>	
    <?php else: ?>
    	<a href="<?php echo $link; ?>" class="wmle_loadmore_btn" rel="<?php echo $containerDivId; ?>">Load More</a>	
    <?php endif; ?>    
</div>



<script> // AUTO TRIGGER FOR 1st Load
jQuery(document).ready( function() {
	jQuery('.wmle_loadmore_btn').trigger('click'); 
	<?php if ($shortcodeData['wmlo_pagination_style'] == 'infinity_scroll'): ?>
	jQuery(window).bind('scroll', function() {
        if (jQuery('#<?php echo $containerDivId; ?>').attr('data-load-status') == 'ready'){
			if(jQuery(window).scrollTop() >= jQuery('.wmle_container').offset().top + jQuery('.wmle_container').outerHeight() - window.innerHeight) 			{
			  jQuery('.wmle_loadmore_btn').trigger('click');
			}
		}
	});
	<?php endif; ?>
});
</script>