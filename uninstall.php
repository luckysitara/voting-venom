<?php

/*

    ========================
        UNINSTALL FUNCTIONS
    ========================
*/

if (! defined('WP_UNINSTALL_PLUGIN') ) {
	exit;
}


//delete all custom post type with metadata
$myplugin_cpt_args = array('post_type' => 'ewvwp', 'posts_per_page' => -1);
$myplugin_cpt_posts = get_posts($myplugin_cpt_args);
foreach ($myplugin_cpt_posts as $post) {
	wp_delete_post($post->ID, false);
	delete_post_meta($post->ID, '_ewvwp_vote_value_key');
	delete_post_meta($post->ID, '_ewvwp_age_value_key');
	delete_post_meta($post->ID, '_ewvwp_occupation_value_key');
	delete_post_meta($post->ID, '_ewvwp_state_value_key');
	delete_post_meta($post->ID, '_ewvwp_nickname_value_key');
}


//remove shortcode
remove_shortcode( 'ewvwp_plugin' );


//delete register options
delete_option( 'ewvwp_paystack_public_key' );
delete_option( 'ewvwp_paystack_secret_key' );
delete_option( 'ewvwp_min_amount' );
