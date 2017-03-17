<?php
/**
 * @link              http://www.wordpluginpress.com/
 * @since             1.0.0
 * @package           wc_hide_categories_on_shop_page
 *
 * @wordpress-plugin
 * Plugin Name:       WC Hide Categories On Shop Page
 * Plugin URI:        http://www.wordpluginpress.com/
 * Description:       WC Hide Categories On Shop Page allows removing specific categories products from WooCommerce Shop Page.
 * Version:           1.0
 * Author:            WordPluginPress
 * Author URI:        http://www.wordpluginpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wchcosp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

// Exit if accessed directly
defined( 'ABSPATH' ) || die( 'Wordpress Error! Opening plugin file directly' );
define( 'PLUGIN_PATH', plugins_url( __FILE__ ) ); 

/**
 * Check if WooCommerce is active
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
    add_action( 'admin_notices', 'wchcosp_install_admin_notice' );
    
}else{
    
    /**
    * Create the section beneath the products tab
    **/
//  Add WC Hide Categories On Shop Page Setting Section

add_filter( 'woocommerce_get_sections_products', 'wchcosp_hide_category_setting_section' );
   function wchcosp_hide_category_setting_section( $sections ) {

           $sections['wchcosp'] = __( 'Hide Categories On Shop Page', 'wchcosp' );
           return $sections;

   }

// Add WooCommerce  Setting For excluding Category From Shop Page
 add_filter( 'woocommerce_get_settings_products', 'wchcosp_add_wc_exclude_setting', 10, 2 );
 
   function wchcosp_add_wc_exclude_setting( $settings ,  $current_section) {
	   $settings_url = array();
           
                   // Add Title to the Settings
                   if( $current_section == 'wchcosp'){
                   $settings_url[] = array( 'name' => __( 'Hide categories on shop page', 'wchcosp' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Hide Categories on shop Page', 'wchcosp' ), 'id' => 'wchcosp' );
                   
                   // Add  text option
                   
                   $settings_url[] = array(
                           'name'     => __( 'Hide Categories', 'wchcosp' ),
                           'desc_tip' => __( 'This will Hide Categories From Shop Page', 'wchcosp' ),
                           'id'       => 'wchcosp_global',
                           'type'     => 'text',
                           'css'      => 'min-width:300px;',
                           'desc'     => __( 'Put the categories  which are to be excluded  eg : abc,xyz', 'wchcosp' ),
                   );
				   return $settings_url;
				}
				else{
					
					return $settings;
				}
   }

 // Exclude Categories From Shop Page
 
add_action( 'pre_get_posts', 'wchcosp_custom_pre_get_posts_query' );

function wchcosp_custom_pre_get_posts_query( $q ) {
	$data = array();
		 $opt_terms = get_option('wchcosp_global');
		 $data = explode(",",$opt_terms);
	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() && !current_user_can('administrator') ) {
		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => $data , // Don't display products in these categories on the shop page
			'operator' => 'NOT IN'
		)));
	
	}
 
	remove_action( 'pre_get_posts', 'wchcosp_custom_pre_get_posts_query' );
 
}
   
}

/* Admin notice if WooCommerce is not installed or active */
function wchcosp_install_admin_notice(){
    echo '<div class="notice notice-error">';
    echo     '<p>'. _e( 'WC Hide Categories On Shop Page requires active WooCommerce Installation!', 'wchcosp' ).'</p>';
    echo '</div>';
}   

