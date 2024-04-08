<?php

/**
 * @package Venom
 * @version 1.0
 */

@ob_start();
add_action( 'init', 'venom_custom_post_type' );
add_action( 'init', 'tr_create_my_taxonomy' );
add_filter( 'manage_venom_posts_columns', 'venom_set_columns_name' );
add_filter("manage_edit-venom-category_columns", 'venom_taxonomies_columns'); 
add_action( 'manage_venom_posts_custom_column', 'venom_custom_columns', 10, 2 );
add_filter("manage_venom-category_custom_column", 'venom_manage_taxonomies_columns', 10, 3);
add_action( 'add_meta_boxes', 'venom_add_meta_box' );
add_action( 'save_post', 'venom_save_nickname_data' );
add_action( 'save_post', 'venom_save_age_data' );
add_action( 'save_post', 'venom_save_state_data' );
add_action( 'save_post', 'venom_save_occupation_data' );
add_action( 'save_post', 'venom_save_vote_data' );

add_filter('gettext','custom_enter_title');

add_action( 'wp_loaded', 'venom_wpse_19240_change_place_labels', 20 );

add_filter('post_updated_messages', 'venom_updated_messages');


function venom_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['venom'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Candidate updated.') ),
    //1 => sprintf( __('Candidate updated. <a href="%s">View Candidate</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Candidate updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Candidate restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Candidate published.') ),
    //6 => sprintf( __('Candidate published. <a href="%s">View Candidate</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Candidate saved.'),
    8 => sprintf( __('Candidate submitted.') ),
    //8 => sprintf( __('Candidate submitted. <a target="_blank" href="%s">Preview Candidate</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Candidate scheduled for: <strong>%1$s</strong>. '),
      // translators: Publish box date format, see http://php.net/date
    	date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
    //9 => sprintf( __('Candidate scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Candidate</a>'),
      // translators: Publish box date format, see http://php.net/date
      //date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Candidate draft updated.') ),
    //10 => sprintf( __('Candidate draft updated. <a target="_blank" href="%s">Preview Candidate</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);

	return $messages;
}

function tr_create_my_taxonomy() {
	$labels = array(
		'name'              => __( 'Contest Categories'),
		'singular_name'     => __( 'Contest Category'),
		'search_items'      => __( 'Search Contests' ),
		'all_items'         => __( 'All Contests' ),
		'parent_item'       => __( 'Parent Contest' ),
		'parent_item_colon' => __( 'Parent Contest:' ),
		'edit_item'         => __( 'Edit Contest' ),
		'update_item'       => __( 'Update Contest' ),
		'add_new_item'      => __( 'Add New Contest' ),
		'new_item_name'     => __( 'New Contest Name' ),
		'menu_name'         => __( 'Contest Categories' ),
	);
	$args   = array(
         'hierarchical'      => true, // make it hierarchical (like categories)
         'labels'            => $labels,
         'show_ui'           => true,
         'show_admin_column' => true,
         'query_var'         => true,
         'rewrite'           => [ 'slug' => 'venom-category' ],
     );
	register_taxonomy( 'venom-category', [ 'venom' ], $args );
}


function venom_taxonomies_columns($theme_columns) {
	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Contest'),
		'shortcode' => __('Shortcode'),
		'description' => __('Description'),
		'posts' => __('Candidates')
	);
	return $new_columns;
}


function venom_manage_taxonomies_columns($out, $column_name, $theme_id) {
	switch ($column_name) {
		case 'shortcode':
		$out .= '[venom_plugin contest="'.$theme_id.'"]'; 
		break;

		default:
		break;
	}
	return $out;    
}

function custom_enter_title( $input ) {

	global $post_type;

	if( is_admin() && 'Add title' == $input && 'venom' == $post_type )
		return 'Enter Fullname';

	return $input;
}


function venom_wpse_19240_change_place_labels()
{
	$p_object = get_post_type_object( 'venom' );

	if ( ! $p_object )
		return FALSE;

    // see get_post_type_labels()
	$p_object->labels->add_new            = 'Add Candidate';
	$p_object->labels->add_new_item       = 'Add New Candidate';
	$p_object->labels->all_items          = 'All Candidate';
	$p_object->labels->edit_item          = 'Edit Candidate';
	$p_object->labels->new_item           = 'New Candidate';
	$p_object->labels->not_found          = 'No Candidates found';
	$p_object->labels->not_found_in_trash = 'No Candidates found in trash';
	$p_object->labels->search_items       = 'Search Candidates';
	$p_object->labels->view_item          = 'View Candidate';

	return TRUE;
}


function venom_custom_post_type(){
	$labels = array(
		'taxonomies' => 'venom-category',
		'name'				=>	'Easy WP Voting With Payment',
		'singular_name'		=>	'Easy WP Voting With Payment',
		'menu_name'			=>	'Easy WP Voting With Payments',
		'name_admin_bar'	=>	'Easy WP Voting With Payment'
	);

	$args = array(
		'labels'				=>	$labels,
		'show_ui'		=>	true,
		'show_ui_menu'			=>	true,
		'capability_type'	=>	'post',
		'hierarchical'	=>	false,
		'menu_position'	=>	200,
		'publicly_queryable' => true,
		'menu_icon'	=>	'dashicons-groups',
		'supports'	=>	array('title', 'thumbnail')
	);

	register_post_type( 'venom', $args );
}

function venom_set_columns_name( $columns ) {
	$clientColumns = array();
	$clientColumns['cb'] = "<input type=\"checkbox\" />";
	$clientColumns['title'] = 'Full Name';
	$clientColumns['nickname'] = 'Nick Name';
	$clientColumns['state'] = 'State';
	$clientColumns['age'] = 'Age';
	$clientColumns['occupation'] = 'Occupation';
	$clientColumns['votes'] = 'Number of votes';
	$clientColumns['taxonomy'] = 'Contest Category';
	return $clientColumns;

}


function venom_custom_columns( $columns, $post_id ) {

	switch ( $columns ) {
		case 'nickname':
		$value = get_post_meta( $post_id, '_venom_nickname_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'state':
		$value = get_post_meta( $post_id, '_venom_state_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'age':
		$value = get_post_meta( $post_id, '_venom_age_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'votes':
		$value = get_post_meta( $post_id, '_venom_vote_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'occupation':
		$value = get_post_meta( $post_id, '_venom_occupation_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'taxonomy':
		$terms = get_the_terms( $post_id, 'venom-category' );
		$draught_links = array();
		foreach ( $terms as $term ) {
			$draught_links[] = $term->name;
		}                  
		$on_draught = join( ", ", $draught_links );
		printf($on_draught);
		break;
	}

}

function venom_add_meta_box(){
	add_meta_box( 'venom_nickname', 'Nickname', 'venom_nickname_callback', 'venom', 'normal' );
	add_meta_box( 'venom_age', 'Age', 'venom_age_callback', 'venom', 'normal' );
	add_meta_box( 'venom_votes', 'Number of Votes', 'venom_vote_callback', 'venom', 'normal' );
	add_meta_box( 'venom_state', 'State', 'venom_state_callback', 'venom', 'normal' );
	add_meta_box( 'venom_occupation', 'Occupation', 'venom_occupation_callback', 'venom', 'normal' );
}


function venom_nickname_callback( $post ){
	wp_nonce_field( 'venom_save_nickname_data', 'venom_nickname_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_venom_nickname_value_key', true );

	echo '<label for="venom_nickname_field"> Nick Name </label><br><br> ';
	echo '<input type="text" name="venom_nickname_field" id="venom_nickname_field" value="'. esc_attr( $value ).'" size="25"/>';
}

function venom_vote_callback( $post ){
	wp_nonce_field( 'venom_save_vote_data', 'venom_vote_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_venom_vote_value_key', true );

	$final_value = (!empty($value)) ? $value : 0;

	echo '<label for="venom_vote_field"> Number of Votes </label><br><br> ';
	echo '<input type="number" name="venom_vote_field" id="venom_vote_field" value="'. esc_attr( $final_value ).'" size="25"/>';
}

function venom_age_callback( $post ){
	wp_nonce_field( 'venom_save_age_data', 'venom_age_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_venom_age_value_key', true );

	echo '<label for="venom_age_field"> Ages </label><br><br> ';
	echo '<input type="number" name="venom_age_field" id="venom_age_field" value="'. esc_attr( $value ).'" size="25"/>';
}

function venom_state_callback( $post ){
	wp_nonce_field( 'venom_save_state_data', 'venom_state_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_venom_state_value_key', true );

	echo '<label for="venom_state_field"> Name of State </label><br><br> ';
	echo '<input type="text" name="venom_state_field" id="venom_state_field" value="'. esc_attr( $value ).'" size="25"/>';
}

function venom_occupation_callback( $post ){
	wp_nonce_field( 'venom_save_occupation_data', 'venom_occupation_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_venom_occupation_value_key', true );

	echo '<label for="venom_occupation_field"> Occupation </label><br><br> ';
	echo '<input type="text" name="venom_occupation_field" id="venom_occupation_field" value="'. esc_attr( $value ).'" size="25"/>';
}



function venom_save_nickname_data( $post_id ){

	if (! isset( $_POST['venom_nickname_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['venom_nickname_meta_box_nonce'], 'venom_save_nickname_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['venom_nickname_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['venom_nickname_field'] );

	update_post_meta( $post_id , '_venom_nickname_value_key' , $my_data );

}

function venom_save_age_data( $post_id ){

	if (! isset( $_POST['venom_age_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['venom_age_meta_box_nonce'], 'venom_save_age_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['venom_age_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['venom_age_field'] );

	update_post_meta( $post_id , '_venom_age_value_key' , $my_data );

}

function venom_save_state_data( $post_id ){

	if (! isset( $_POST['venom_state_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['venom_state_meta_box_nonce'], 'venom_save_state_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['venom_state_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['venom_state_field'] );

	update_post_meta( $post_id , '_venom_state_value_key' , $my_data );

}

function venom_save_occupation_data( $post_id ){

	if (! isset( $_POST['venom_occupation_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['venom_occupation_meta_box_nonce'], 'venom_save_occupation_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['venom_occupation_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['venom_occupation_field'] );

	update_post_meta( $post_id , '_venom_occupation_value_key' , $my_data );

}

function venom_save_vote_data( $post_id ){

	if (! isset( $_POST['venom_vote_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['venom_vote_meta_box_nonce'], 'venom_save_vote_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['venom_vote_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['venom_vote_field'] );

	update_post_meta( $post_id , '_venom_vote_value_key' , $my_data );

}

