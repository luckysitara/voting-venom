<?php
/**
 * Plugin Name: Voting Venom
 * Version: 2.1.0
 */

// Error handling and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/venom_error.log');

// Register custom post type and taxonomies
add_action('init', 'venom_custom_post_type');
add_action('init', 'tr_create_my_taxonomy');

// Add custom columns to admin panel
add_filter('manage_venom_posts_columns', 'venom_set_columns_name');
add_filter('manage_edit-venom-category_columns', 'venom_taxonomies_columns');

// Customize column content
add_action('manage_venom_posts_custom_column', 'venom_custom_columns', 10, 2);
add_action('manage_venom-category_custom_column', 'venom_manage_taxonomies_columns', 10, 3);

// Add meta boxes for contestant details
add_action('add_meta_boxes', 'venom_add_meta_box');

// Save meta box data
add_action('save_post', 'venom_save_nickname_data');
add_action('save_post', 'venom_save_age_data');
add_action('save_post', 'venom_save_state_data');
add_action('save_post', 'venom_save_occupation_data');
add_action('save_post', 'venom_save_vote_data');
add_action('save_post', 'venom_save_profile_picture_data');

// Modify post updated messages
add_filter('post_updated_messages', 'venom_updated_messages');

function venom_custom_post_type() {
    $labels = array(
        'name' => 'Voting With Payment',
        'singular_name' => 'Voting-Payment',
        'menu_name' => 'Voting-Payment',
        'name_admin_bar' => 'Voting-Payment'
    );

    $args = array(
        'labels' => $labels,
        'show_ui' => true,
        'show_ui_menu' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 200,
        'publicly_queryable' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'thumbnail')
    );

    register_post_type('venom', $args);
}

function tr_create_my_taxonomy() {
    $labels = array(
        'name' => __('Contest Categories'),
        'singular_name' => __('Contest Category'),
        'search_items' => __('Search Contests'),
        'all_items' => __('All Contests'),
        'parent_item' => __('Parent Contest'),
        'parent_item_colon' => __('Parent Contest:'),
        'edit_item' => __('Edit Contest'),
        'update_item' => __('Update Contest'),
        'add_new_item' => __('Add New Contest'),
        'new_item_name' => __('New Contest Name'),
        'menu_name' => __('Contest Categories'),
    );
    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'venom-category'),
    );
    register_taxonomy('venom-category', array('venom'), $args);
}

function venom_set_columns_name($columns) {
    $columns['nickname'] = 'Nick Name';
    $columns['state'] = 'State';
    $columns['age'] = 'Age';
    $columns['occupation'] = 'Occupation';
    $columns['votes'] = 'Number of Votes';
    $columns['taxonomy'] = 'Contest Category';
    return $columns;
}

function venom_custom_columns($column, $post_id) {
    switch ($column) {
        case 'nickname':
            echo get_post_meta($post_id, '_venom_nickname_value_key', true);
            break;
        case 'state':
            echo get_post_meta($post_id, '_venom_state_value_key', true);
            break;
        case 'age':
            echo get_post_meta($post_id, '_venom_age_value_key', true);
            break;
        case 'occupation':
            echo get_post_meta($post_id, '_venom_occupation_value_key', true);
            break;
        case 'votes':
            echo get_post_meta($post_id, '_venom_vote_value_key', true);
            break;
        case 'taxonomy':
            $terms = get_the_terms($post_id, 'venom-category');
            if (!empty($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo implode(', ', $term_names);
            }
            break;
    }
}

function venom_manage_taxonomies_columns($columns) {
    $columns['taxonomy'] = 'Contest Category';
    return $columns;
}

function venom_add_meta_box() {
    add_meta_box('venom_nickname', 'Nickname', 'venom_nickname_callback', 'venom', 'normal');
    add_meta_box('venom_age', 'Age', 'venom_age_callback', 'venom', 'normal');
    add_meta_box('venom_votes', 'Number of Votes', 'venom_vote_callback', 'venom', 'normal');
    add_meta_box('venom_state', 'State', 'venom_state_callback', 'venom', 'normal');
    add_meta_box('venom_occupation', 'Occupation', 'venom_occupation_callback', 'venom', 'normal');
    add_meta_box('venom_profile_picture', 'Profile Picture', 'venom_profile_picture_callback', 'venom', 'normal');
}

function venom_nickname_callback($post) {
    $value = get_post_meta($post->ID, '_venom_nickname_value_key', true);
    echo '<label for="venom_nickname_field"> Nick Name </label><br><br> ';
    echo '<input type="text" name="venom_nickname_field" id="venom_nickname_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_vote_callback($post) {
    $value = get_post_meta($post->ID, '_venom_vote_value_key', true);
    $final_value = (!empty($value)) ? $value : 0;
    echo '<label for="venom_vote_field"> Number of Votes </label><br><br> ';
    echo '<input type="number" name="venom_vote_field" id="venom_vote_field" value="' . esc_attr($final_value) . '" size="25"/>';
}

function venom_age_callback($post) {
    $value = get_post_meta($post->ID, '_venom_age_value_key', true);
    echo '<label for="venom_age_field"> Ages </label><br><br> ';
    echo '<input type="number" name="venom_age_field" id="venom_age_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_state_callback($post) {
    $value = get_post_meta($post->ID, '_venom_state_value_key', true);
    echo '<label for="venom_state_field"> State </label><br><br> ';
    echo '<input type="text" name="venom_state_field" id="venom_state_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_occupation_callback($post) {
    $value = get_post_meta($post->ID, '_venom_occupation_value_key', true);
    echo '<label for="venom_occupation_field"> Occupation </label><br><br> ';
    echo '<input type="text" name="venom_occupation_field" id="venom_occupation_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_profile_picture_callback($post) {
    wp_nonce_field(basename(__FILE__), 'venom_profile_picture_nonce');
    $value = get_post_meta($post->ID, '_venom_profile_picture_value_key', true);
    echo '<label for="venom_profile_picture_field"> Profile Picture </label><br><br> ';
    echo '<input type="file" name="venom_profile_picture_field" id="venom_profile_picture_field" accept="image/*">';
    if (!empty($value)) {
        echo '<br><img src="' . esc_attr($value) . '" style="max-width:200px;"/>';
    }
}

function venom_save_nickname_data($post_id) {
    if (!isset($_POST['venom_nickname_field'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['venom_profile_picture_nonce'], basename(__FILE__))) {
        return;
    }
    $nickname_data = sanitize_text_field($_POST['venom_nickname_field']);
    update_post_meta($post_id, '_venom_nickname_value_key', $nickname_data);
}

function venom_save_vote_data($post_id) {
    if (!isset($_POST['venom_vote_field'])) {
        return;
    }
    $vote_data = sanitize_text_field($_POST['venom_vote_field']);
    update_post_meta($post_id, '_venom_vote_value_key', $vote_data);
}

function venom_save_age_data($post_id) {
    if (!isset($_POST['venom_age_field'])) {
        return;
    }
    $age_data = sanitize_text_field($_POST['venom_age_field']);
    update_post_meta($post_id, '_venom_age_value_key', $age_data);
}

function venom_save_state_data($post_id) {
    if (!isset($_POST['venom_state_field'])) {
        return;
    }
    $state_data = sanitize_text_field($_POST['venom_state_field']);
    update_post_meta($post_id, '_venom_state_value_key', $state_data);
}

function venom_save_occupation_data($post_id) {
    if (!isset($_POST['venom_occupation_field'])) {
        return;
    }
    $occupation_data = sanitize_text_field($_POST['venom_occupation_field']);
    update_post_meta($post_id, '_venom_occupation_value_key', $occupation_data);
}

function venom_save_profile_picture_data($post_id) {
    if (!isset($_POST['venom_profile_picture_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['venom_profile_picture_nonce'], basename(__FILE__))) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (!empty($_FILES['venom_profile_picture_field']['name'])) {
        $file = $_FILES['venom_profile_picture_field'];
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'];
        $file_name = basename($file['name']);
        $file_path = $upload_path . '/' . $file_name;

        // Move uploaded file to the upload directory
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $file_url = $upload_dir['url'] . '/' . $file_name;
            update_post_meta($post_id, '_venom_profile_picture_value_key', $file_url);
        }
    }
}

function venom_updated_messages($messages) {
    global $post, $post_ID;
    $messages['venom'] = array(
        0 => '',
        1 => sprintf(__('Contest updated. <a href="%s">View Contest</a>'), esc_url(get_permalink($post_ID))),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Contest updated.'),
        5 => isset($_GET['revision']) ? sprintf(__('Contest restored to revision from %s'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
        6 => sprintf(__('Contest published. <a href="%s">View Contest</a>'), esc_url(get_permalink($post_ID))),
        7 => __('Contest saved.'),
        8 => sprintf(__('Contest submitted. <a target="_blank" href="%s">Preview Contest</a>'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
        9 => sprintf(__('Contest scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Contest</a>'), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
        10 => sprintf(__('Contest draft updated. <a target="_blank" href="%s">Preview Contest</a>'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
    );
    return $messages;
}

// Customizing labels for "places" taxonomy
add_action('init', 'venom_wpse_19240_change_place_labels');

function venom_wpse_19240_change_place_labels() {
    global $wp_taxonomies;

    if (isset($wp_taxonomies['venom-category'])) {
        $labels = &$wp_taxonomies['venom-category']->labels;
        $labels->name = 'Contest Categories';
        $labels->singular_name = 'Contest Category';
        $labels->add_new = 'Add New Contest Category';
        $labels->add_new_item = 'Add New Contest Category';
        $labels->edit_item = 'Edit Contest Category';
        $labels->new_item = 'New Contest Category';
        $labels->view_item = 'View Contest Category';
        $labels->search_items = 'Search Contest Categories';
        $labels->not_found = 'No Contest Categories found';
        $labels->not_found_in_trash = 'No Contest Categories found in Trash';
        $labels->all_items = 'All Contest Categories';
        $labels->menu_name = 'Contest Categories';
        $labels->name_admin_bar = 'Contest Category';
    }
}

// AJAX handler for form submission
add_action('wp_ajax_venom_form_ajax', 'venom_handle_form_submission');
add_action('wp_ajax_nopriv_venom_form_ajax', 'venom_handle_form_submission');

function venom_handle_form_submission() {
    $response = array();

    // Check if the necessary data is provided
    if (isset($_POST['quantity'], $_POST['userID'], $_POST['reference'], $_POST['email'])) {
        $quantity = sanitize_text_field($_POST['quantity']);
        $userID = intval($_POST['userID']);
        $reference = sanitize_text_field($_POST['reference']);
        $email = sanitize_email($_POST['email']);

        // Process payment and update vote count
        // Sample code, replace with your actual payment processing logic
        $success = true; // Assuming payment is successful
        if ($success) {
            // Update vote count
            $current_votes = get_post_meta($userID, '_venom_vote_value_key', true);
            $new_votes = intval($current_votes) + intval($quantity);
            update_post_meta($userID, '_venom_vote_value_key', $new_votes);

            // Prepare response
            $response['success'] = true;
            $response['message'] = 'Thank you for voting!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Payment failed. Please try again.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid request. Please provide all required data.';
    }

    // Send JSON response
    wp_send_json($response);
}

?>
