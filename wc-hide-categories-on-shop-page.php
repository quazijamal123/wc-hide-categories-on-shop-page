<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.wordpluginpress.com/
 * @since             1.0.0
 * @package           Wc_Hide_Categories_On_Shop_Page
 *
 * @wordpress-plugin
 * Plugin Name:       WC Hide Categories On Shop Page
 * Plugin URI:        http://www.wordpluginpress.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
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

// Add WooCommerce  Setting For excluding Category From Shop Page
 add_filter( 'woocommerce_get_settings_products', 'wchcosp_add_wc_exclude_setting', 10, 1 );
   function wchcosp_add_wc_exclude_setting( $settings) {
	   
	   $settings_url = array();
                   
                   // Add Title to the Settings
                   
                   $settings_url[] = array( 'name' => __( 'Hide Categories on shop Page', 'wchcosp' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Hide Categories on shop Page', 'wchcosp' ), 'id' => 'wctr' );
                   
                   // Add  text option
                   
                   $settings_url[] = array(
                           'name'     => __( 'Hide Categories From Shop Page', 'wchcosp' ),
                           'desc_tip' => __( 'This will Hide Categories From Shop Page', 'wchcosp' ),
                           'id'       => 'wctr_global',
                           'type'     => 'text',
                           'css'      => 'min-width:300px;',
                           'desc'     => __( 'Put the categories to which to be excluded', 'wchcosp' ),
                   );
				   return $settings_url;
   }

 // Exclude Categories From Shop Page
 
add_action( 'pre_get_posts', 'wchcosp_custom_pre_get_posts_query' );

function wchcosp_custom_pre_get_posts_query( $q ) {
	$data = array();
		 $opt_terms = get_option('wctr_global');
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

