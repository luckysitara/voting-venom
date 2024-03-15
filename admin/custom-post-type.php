<?php


@ob_start();
add_action( 'init', 'ewvwp_custom_post_type' );
add_action( 'init', 'tr_create_my_taxonomy' );
add_filter( 'manage_ewvwp_posts_columns', 'ewvwp_set_columns_name' );
add_filter("manage_edit-ewvwp-category_columns", 'ewvwp_taxonomies_columns'); 
add_action( 'manage_ewvwp_posts_custom_column', 'ewvwp_custom_columns', 10, 2 );
add_filter("manage_ewvwp-category_custom_column", 'ewvwp_manage_taxonomies_columns', 10, 3);
add_action( 'add_meta_boxes', 'ewvwp_add_meta_box' );
add_action( 'save_post', 'ewvwp_save_nickname_data' );
add_action( 'save_post', 'ewvwp_save_age_data' );

add_action( 'save_post', 'ewvwp_save_vote_data' );

add_filter('gettext','custom_enter_title');

add_action( 'wp_loaded', 'ewvwp_wpse_19240_change_place_labels', 20 );

add_filter('post_updated_messages', 'ewvwp_updated_messages');


function ewvwp_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['ewvwp'] = array(
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
         'rewrite'           => [ 'slug' => 'ewvwp-category' ],
     );
	register_taxonomy( 'ewvwp-category', [ 'ewvwp' ], $args );
}


function ewvwp_taxonomies_columns($theme_columns) {
	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Contest'),
		'shortcode' => __('Shortcode'),
		'description' => __('Description'),
		'posts' => __('Candidates')
	);
	return $new_columns;
}


function ewvwp_manage_taxonomies_columns($out, $column_name, $theme_id) {
	switch ($column_name) {
		case 'shortcode':
		$out .= '[plugin contest="'.$theme_id.'"]'; 
		break;

		default:
		break;
	}
	return $out;    
}

function custom_enter_title( $input ) {

	global $post_type;

	if( is_admin() && 'Add title' == $input && 'ewvwp' == $post_type )
		return 'Enter Fullname';

	return $input;
}


function ewvwp_wpse_19240_change_place_labels()
{
	$p_object = get_post_type_object( 'ewvwp' );

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


function ewvwp_custom_post_type(){
	$labels = array(
		'taxonomies' => 'ewvwp-category',
		'name'				=>	'Voting-Payment',
		'singular_name'		=>	'Voting-Payment',
		'menu_name'			=>	'Voting-Payment',
		'name_admin_bar'	=>	'Voting-Payment'
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

	register_post_type( 'ewvwp', $args );
}

function ewvwp_set_columns_name( $columns ) {
	$clientColumns = array();
	$clientColumns['cb'] = "<input type=\"checkbox\" />";
	$clientColumns['title'] = 'Full Name';
	$clientColumns['nickname'] = 'Nick Name';
	$clientColumns['age'] = 'Age';
	$clientColumns['votes'] = 'Number of votes';
	$clientColumns['taxonomy'] = 'Contest Category';
	return $clientColumns;

}


function ewvwp_custom_columns( $columns, $post_id ) {

	switch ( $columns ) {
		case 'nickname':
		$value = get_post_meta( $post_id, '_ewvwp_nickname_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'age':
		$value = get_post_meta( $post_id, '_ewvwp_age_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'votes':
		$value = get_post_meta( $post_id, '_ewvwp_vote_value_key', true );
		echo '<strong>'.$value.'</strong>';
		break;

		case 'taxonomy':
		$terms = get_the_terms( $post_id, 'ewvwp-category' );
		$draught_links = array();
		foreach ( $terms as $term ) {
			$draught_links[] = $term->name;
		}                  
		$on_draught = join( ", ", $draught_links );
		printf($on_draught);
		break;
	}

}

function ewvwp_add_meta_box(){
	add_meta_box( 'ewvwp_nickname', 'Nickname', 'ewvwp_nickname_callback', 'ewvwp', 'normal' );
	add_meta_box( 'ewvwp_age', 'Age', 'ewvwp_age_callback', 'ewvwp', 'normal' );
	add_meta_box( 'ewvwp_votes', 'Number of Votes', 'ewvwp_vote_callback', 'ewvwp', 'normal' );
	
}


function ewvwp_nickname_callback( $post ){
	wp_nonce_field( 'ewvwp_save_nickname_data', 'ewvwp_nickname_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_ewvwp_nickname_value_key', true );

	echo '<label for="ewvwp_nickname_field"> Nick Name </label><br><br> ';
	echo '<input type="text" name="ewvwp_nickname_field" id="ewvwp_nickname_field" value="'. esc_attr( $value ).'" size="25"/>';
}

function ewvwp_vote_callback( $post ){
	wp_nonce_field( 'ewvwp_save_vote_data', 'ewvwp_vote_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_ewvwp_vote_value_key', true );

	$final_value = (!empty($value)) ? $value : 0;

	echo '<label for="ewvwp_vote_field"> Number of Votes </label><br><br> ';
	echo '<input type="number" name="ewvwp_vote_field" id="ewvwp_vote_field" value="'. esc_attr( $final_value ).'" size="25"/>';
}

function ewvwp_age_callback( $post ){
	wp_nonce_field( 'ewvwp_save_age_data', 'ewvwp_age_meta_box_nonce' );
	$value = get_post_meta( $post->ID, '_ewvwp_age_value_key', true );

	echo '<label for="ewvwp_age_field"> Ages </label><br><br> ';
	echo '<input type="number" name="ewvwp_age_field" id="ewvwp_age_field" value="'. esc_attr( $value ).'" size="25"/>';
}

function ewvwp_save_nickname_data( $post_id ){

	if (! isset( $_POST['ewvwp_nickname_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['ewvwp_nickname_meta_box_nonce'], 'ewvwp_save_nickname_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['ewvwp_nickname_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['ewvwp_nickname_field'] );

	update_post_meta( $post_id , '_ewvwp_nickname_value_key' , $my_data );

}

function ewvwp_save_age_data( $post_id ){

	if (! isset( $_POST['ewvwp_age_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['ewvwp_age_meta_box_nonce'], 'ewvwp_save_age_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['ewvwp_age_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['ewvwp_age_field'] );

	update_post_meta( $post_id , '_ewvwp_age_value_key' , $my_data );

}




function ewvwp_save_vote_data( $post_id ){

	if (! isset( $_POST['ewvwp_vote_meta_box_nonce'] ) ) {
		return;
	}
	if (! wp_verify_nonce( $_POST['ewvwp_vote_meta_box_nonce'], 'ewvwp_save_vote_data' ) ) {
		return;
	}
	if ( define('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	if (! current_user_can( 'edit_post', $post_id )) {
		return;
	}
	if (! isset( $_POST['ewvwp_vote_field'] )) {
		return;
	}

	$my_data = sanitize_text_field( $_POST['ewvwp_vote_field'] );

	update_post_meta( $post_id , '_ewvwp_vote_value_key' , $my_data );

}

