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
$myplugin_cpt_args = array('post_type' => 'venom', 'posts_per_page' => -1);
$myplugin_cpt_posts = get_posts($myplugin_cpt_args);
foreach ($myplugin_cpt_posts as $post) {
	wp_delete_post($post->ID, false);
	delete_post_meta($post->ID, '_venom_vote_value_key');
	delete_post_meta($post->ID, '_venom_age_value_key');
	delete_post_meta($post->ID, '_venom_occupation_value_key');
	delete_post_meta($post->ID, '_venom_state_value_key');
	delete_post_meta($post->ID, '_venom_nickname_value_key');
}


//remove shortcode
remove_shortcode( 'venom_plugin' );


//delete register options
delete_option( 'venom_paystack_public_key' );
delete_option( 'venom_paystack_secret_key' );
delete_option( 'venom_min_amount' );
