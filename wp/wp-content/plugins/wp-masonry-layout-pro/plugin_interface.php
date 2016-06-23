<?php
add_action('admin_menu', 'wmlp_create_menu');
add_action("admin_print_scripts", 'wmlp_adminjslibs');
add_action("admin_print_styles", 'wmlp_adminCsslibs');
add_action('wp_enqueue_scripts', 'wmlp_client_js_css');
add_action('plugins_loaded', 'wmlp_update_check');

function wmlp_create_menu() { // Create menu for the plugin
	add_menu_page( 'WP Masonry Posts', 'WP Masonry', 'manage_options', 'wmlp_shortcodes', 'wmlp_shortcodes', '', 81 );
	add_submenu_page( 'wmlp_shortcodes', 'Shortcodes', 'Shortcodes', 'manage_options', 'wmlp_shortcodes', 'wmlp_shortcodes');
	add_submenu_page( 'wmlp_shortcodes', 'Layout Themes', 'Layout Themes', 'manage_options', 'wmlp_layout_themes', 'wmlp_layout_themes');
	add_submenu_page( 'wmlp_shortcodes', 'Documentation', 'Documentation', 'manage_options', 'wmlp_documentation', 'wmlp_documentation');
	add_submenu_page( 'wmlp_shortcodes', 'License', 'License', 'manage_options', 'wmlp_license', 'wmlp_license');
	add_submenu_page( '', 'Layout Settings', 'Layout Settings', 'manage_options', 'wmlp_layout_settings', 'wmlp_layout_settings');
}

function wmlp_adminjslibs(){ // Load needed js 
	wp_register_script('wmlp_validate_js',plugins_url("wp-masonry-layout-pro/js/jquery.validate.min.js"));
	wp_enqueue_script('wmlp_validate_js');
	
	wp_register_script('wmlp_color_js',plugins_url("wp-masonry-layout-pro/js/colorpicker/jscolor.js"));		
	wp_enqueue_script('wmlp_color_js');
}

function wmlp_adminCsslibs(){ // Load needed css
	wp_register_style('wmlp_admin_style', plugins_url('wp-masonry-layout-pro/css/wmlc_admin.css'));
    wp_enqueue_style('wmlp_admin_style');	
}

function wmlp_client_js_css(){
	wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'masonry' );
	wp_register_script('wmljs',plugins_url("wp-masonry-layout-pro/js/wmljs.js"));		
	wp_enqueue_script('wmljs');
	
	
	/* FOR PRO VERSION ONLY */
	wp_register_script('wmlp_infinity_scroll',plugins_url("wp-masonry-layout-pro/js/jquery.infinitescroll.min.js"));
	wp_enqueue_script('wmlp_infinity_scroll');
	/* EOF FOR PRO VERSION ONLY */
	
	wp_register_style('wmlp_client_style', plugins_url('wp-masonry-layout-pro/css/wmlc_client.css'));
    wp_enqueue_style('wmlp_client_style');
	
	wp_register_style('wmlp_icon_style', plugins_url('wp-masonry-layout-pro/icons/style.css'));
    wp_enqueue_style('wmlp_icon_style');
}

function wmlp_activate(){ // When plugin gets activated or updated.
	$primaryKey = get_option('wmlo_primary_key');
	if (empty($primaryKey)){
		update_option('wmlo_primary_key','1');
	}
	wmlp_write_theme_settings();
}

function wmlp_fill_up_form($fillUpData, $fieldkey, $selectOptionValue = ''){
	if (!empty($fillUpData)){
		if (array_key_exists($fieldkey,$fillUpData)){
			if (empty($selectOptionValue)){
				return $fillUpData[$fieldkey];
			} else {
				if ($fillUpData[$fieldkey] == $selectOptionValue){
					return 'selected="selected"';
				} else {
					return '';
				}
			}
		}
	}
	return '';
}

function wmlp_shortcodes(){ // Main page
	include('includes/admin/common/wmlp_header.php');
	include('includes/admin/shortcode/wmlp_shortcodes.php');
	include('includes/admin/shortcode/wmlp_add_edit_shortcode.php');
	include('includes/admin/common/wmlp_footer.php');
}

function wmlp_documentation(){ // Main page
	include('includes/admin/common/wmlp_header.php');
	include('includes/admin/documentation/wmlp_documentation.php');
	include('includes/admin/common/wmlp_footer.php');
}

// SHORTCODE
add_shortcode( 'wmls', 'wmlp_shortcode');
function wmlp_shortcode($atts){
	if (array_key_exists('id',$atts)){ // Check if shortcode ID is passed
		$shortcodeId = $atts['id'];
		if (!empty($shortcodeId)){ // Check if Id is not empty
			$shortcodesRawData 	= get_option('wmlo_shortcodes_data');
			$shortcodesData		= json_decode($shortcodesRawData, true);
			if (array_key_exists($shortcodeId, $shortcodesData)){ // Check if requested shortcode is in our record.
				$shortcodeData	= $shortcodesData[$shortcodeId];
				$themeLayout	= wmlp_check_layout_moved($shortcodeData['wmlo_layout_theme'], 'themes/');
				
				// GET THEME SETTINGS
				$layoutSlug 	= $shortcodeData['wmlo_layout_theme'];
				$layoutSettings	= get_option('wmlo_theme_settings_'.$layoutSlug);
				$layoutSettings	= json_decode($layoutSettings, true);
				
				ob_start();
				include($themeLayout.'/style.php');
				include('includes/client/wmlp_container.php');
				$masonryContainerOutput = ob_get_clean();
				return $masonryContainerOutput;
	
			} else {
				echo "WP Masonry Posts : Coudln't find shortcode in our record.";
			}
		} else {
			return 'WP Masonry Posts : Shortcode ID Empty.';	
		}
	} else {
		return 'WP Masonry Posts : Shortcode ID Undefined.';
	}
}

// AJAX HANDELR
add_action("wp_ajax_nopriv_wmlp_load_posts", "wmlp_ajax_load_posts");
add_action("wp_ajax_wmlp_load_posts", "wmlp_ajax_load_posts");
function wmlp_ajax_load_posts(){
	global $randSeed;
	$returnData			= array();
	$shortcodeId 		= $_GET['shortcodeId'];
	$pageNumber 		= $_GET['pageNumber'];
	$randSeed			= $_GET['randSeed'];
	$shortcodesRawData 	= get_option('wmlo_shortcodes_data');
	$shortcodesData		= json_decode($shortcodesRawData, true);
	if (array_key_exists($shortcodeId, $shortcodesData)){ // Check if requested shortcode is in our record.
		$shortcodeData	= $shortcodesData[$shortcodeId];
		$themeLayout	= wmlp_check_layout_moved($shortcodeData['wmlo_layout_theme']);
		
		//CREATE QUERY ARGUMENTS
		$query_arg = array(
				   'post_type' 		=> $shortcodeData['wmlo_post_type'],
				   'posts_per_page'	=> $shortcodeData['wmlo_post_count'],
				   'post_status'	=> 'publish'
				   );

		if (($shortcodeData['wmlo_post_type'] == 'post') && ($shortcodeData['wmlo_post_category'] > 0)){ // If post type is post and category is selected
			$query_arg['cat']	= $shortcodeData['wmlo_post_category'];
		}
		
		if (($shortcodeData['wmlo_post_type'] == 'product') && ($shortcodeData['wmlo_product_category'] > 0)){ // If post type is post and category is selected
			$product_cat_term = get_term( $shortcodeData['wmlo_product_category'], 'product_cat' );
			$product_cat_slug = $product_cat_term->slug;
			
			$query_arg['product_cat']		= $product_cat_slug;
		}
		
		if (($shortcodeData['wmlo_post_type'] == 'page') && ($shortcodeData['wmlo_page_parent'] > 0)){ // If post type is post and category is selected
			$query_arg['post_parent']	= $shortcodeData['wmlo_page_parent'];
		}
		
		if ($shortcodeData['wmlo_order_by'] != '0'){
			if ($shortcodeData['wmlo_order_by'] == 'rand'){
				add_filter('posts_orderby', 'wmlp_post_orderby');
			} else {
				$query_arg['orderby']	= $shortcodeData['wmlo_order_by'];
			}
		}
		
		if ($shortcodeData['wmlo_order'] != '0'){
			$query_arg['order']		= $shortcodeData['wmlo_order'];
		}
		
		if ($pageNumber > 0){
			$query_arg['paged']     = $pageNumber;
		}
		include('themes/loader.php');
	} else {
		$returnData['status']			= 'no_shortcode';
		$returnData['message'] 			= "WP Masonry Posts : Coudln't find shortcode in our record.";
	}
	echo json_encode($returnData);
	die();
}

function wmlp_check_layout_moved($themeLayout, $folderAddition = ''){
	// CHECK IF LAYOUT IS COPIED WORDPRESS ACTIVE THEME
	$currentWPTheme 		= wp_get_theme();
	$currentWPThemeSlug 	= $currentWPTheme->get( 'TextDomain' );
	$currentWPThemeDir 		= get_theme_root() . '/' . $currentWPThemeSlug;
	$mainfolderName 		= '/masonry-layout';
	$masonryLayoutName 		= '/'.$themeLayout;
	$masonryLayoutFullPath  = $currentWPThemeDir.$mainfolderName.$masonryLayoutName;
	
	if (file_exists($masonryLayoutFullPath.'/layout.php') && file_exists($masonryLayoutFullPath.'/style.php')){
		$themeLayout = $masonryLayoutFullPath;
	} else {
		$themeLayout = $folderAddition.$themeLayout;
	}
	
	return $themeLayout;
}

function wmlp_layout_url($filePath){
    $siteUrl              = content_url();
    $realpath             = str_replace('\\', '/', dirname($filePath));
    $explodedRealPath     = explode('wp-content',$realpath);
    $urlpath             = $siteUrl.$explodedRealPath[1];
    return $urlpath;
}

// RANDOM UNIQUE POST FILTER
function wmlp_post_orderby($orderby_statement) {
    global $randSeed;
	$orderby_statement = 'RAND('.$randSeed.')';
    return $orderby_statement;
}