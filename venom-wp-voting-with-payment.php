<?php
/**
 * @package Venom
 * @version 1.0
 */
/*
Plugin Name: Venom
Plugin URI: https://github.com/luckysitara/voting-venom
Description: Venom  allows you to create a simple voting system with payment method
Author: Bughacker
Version: 1.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Author URI: https://github.com/luckysitara/
*/
defined('ABSPATH') || die('Direct access is not allow');

register_activation_hook( __FILE__, 'venom_admin_notice_example_activation_hook' );


function venom_admin_notice_example_activation_hook() {

    set_transient( 'venom-admin-notice-example', true, 5 );

}


function venom_admin_success_notice() { 

	if( get_transient( 'venom-admin-notice-example' ) ){
       ?>

       <div class="updated notice is-dismissible">
        <p>Thank you for using this plugin! <strong>You are awesome</strong>.</p>
    </div>

    <?php
    delete_transient( 'venom-admin-notice-example' );
}
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'venom_add_action_links' );
function venom_add_action_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'edit.php?post_type=venom&page=venom_plugin' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
}

require plugin_dir_path(__FILE__) . 'functions.php';
require plugin_dir_path(__FILE__) . 'admin/custom-post-type.php';



function venom_shortcode( $atts, $content = null ){

	extract(shortcode_atts(
		array( 'contest' => 'all' ),
		$atts,
		'venom_plugin'
	));


	ob_start();
	include plugin_dir_path(__FILE__) . 'templates/venom-wp-voting.php';
	return ob_get_clean();

}
add_shortcode( 'venom_plugin', 'venom_shortcode' );


function venom_scripts(){

    wp_enqueue_style( 'venom-owl-carousel-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0', 'all' );

    wp_enqueue_style( 'venom-sweetalert-css', plugin_dir_url(__FILE__) . 'assets/css/sweetalert.css', array(), '1.0.0', 'all' );

    wp_enqueue_script( 'venom-paystack-js', 'https://js.paystack.co/v1/inline.js', array(), '1.0' );
    
    wp_enqueue_script( 'venom-jquery' , plugin_dir_url(__FILE__) . 'assets/js/jquery.min.js', false, '1.11.3', true );

    wp_enqueue_script( 'venom-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('venom-jquery'), '1.0.0', true );

    wp_enqueue_script( 'venom-sweetalert-js', plugin_dir_url(__FILE__) . 'assets/js/sweetalert.js', false, '1.0', true );

}

add_action( 'wp_enqueue_scripts', 'venom_scripts' );


require plugin_dir_path(__FILE__) . 'ajax.php';
