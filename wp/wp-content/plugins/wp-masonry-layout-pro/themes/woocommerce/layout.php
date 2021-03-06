<?php $mediaUrl = wmlp_layout_url(__FILE__); // Donot remove this ?>
<?php global $product; ?>
<div class="wmle_item_holder <?php echo $shortcodeData['wmlo_columns'] ?>" style="display:none;">
    <div class="wmle_item">
			<?php if ( has_post_thumbnail() ) :?>
                <div class="wpme_image">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($shortcodeData['wmlo_image_size']); ?></a>                </div>
            <?php endif; ?>
            
            <div class="wmle_woocommerce_box">
            	<div class="wlme_header">
                	<?php if ($layoutSettings['show_title'] == 'yes'): ?>
                    <div class="wlme_title">
                    	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="wlme_price">
                    	<?php echo $product->get_price_html(); ?>
                    </div>
                </div>
                
                <?php if ($layoutSettings['show_short_description'] == 'yes'): ?>
                    <div class="wlme_short_description">
                        <?php echo get_the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                
                <div class="wlme_add_to_cart">
                	<?php echo do_shortcode('[add_to_cart id="'.get_the_ID().'" style=""]'); ?>
                </div>
                
            </div>
            
    </div><!-- EOF wmle_item_holder -->
</div><!-- EOF wmle_item_holder -->



<?php /*?>
<?php if ($layoutSettings['show_social_share'] == 'yes'): ?>
	<div class="wmle_social_share">
		<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" class="fb">Facebook</a>
		<a target="_blank"  href="https://twitter.com/home?status=<?php the_permalink(); ?>" class="tw">Twitter</a>
		<a target="_blank"  href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>&summary=&source=" class="in">Linkedin</a>                   
		<?php if (has_post_thumbnail( get_the_ID()) ): // Don't show pinterest if no image
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' ); ?>
		<a target="_blank"  href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo $image[0]; ?>&description=<?php the_title(); ?>" class="pi">Pinterest</a>
		<?php endif; ?>
		<a target="_blank"  href="https://plus.google.com/share?url=<?php the_permalink(); ?>" class="gp">Google+</a>
	</div>
<?php endif; ?>


<?php */?>