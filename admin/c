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

// Change post type labels
add_action('wp_loaded', 'venom_wpse_19240_change_place_labels', 20);

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

function venom_add_meta_box() {
    add_meta_box('venom_nickname', 'Nickname', 'venom_nickname_callback', 'venom', 'normal');
    add_meta_box('venom_age', 'Age', 'venom_age_callback', 'venom', 'normal');
    add_meta_box('venom_votes', 'Number of Votes', 'venom_vote_callback', 'venom', 'normal');
    add_meta_box('venom_state', 'State', 'venom_state_callback', 'venom', 'normal');
    add_meta_box('venom_occupation', 'Occupation', 'venom_occupation_callback', 'venom', 'normal');
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
    echo '<label for="venom_state_field"> Name of State </label><br><br> ';
    echo '<input type="text" name="venom_state_field" id="venom_state_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_occupation_callback($post) {
    $value = get_post_meta($post->ID, '_venom_occupation_value_key', true);
    echo '<label for="venom_occupation_field"> Occupation </label><br><br> ';
    echo '<input type="text" name="venom_occupation_field" id="venom_occupation_field" value="' . esc_attr($value) . '" size="25"/>';
}

function venom_save_nickname_data($post_id) {
    if (isset($_POST['venom_nickname_field'])) {
        update_post_meta($post_id, '_venom_nickname_value_key', sanitize_text_field($_POST['venom_nickname_field']));
    }
}

function venom_save_age_data($post_id) {
    if (isset($_POST['venom_age_field'])) {
        update_post_meta($post_id, '_venom_age_value_key', sanitize_text_field($_POST['venom_age_field']));
    }
}

function venom_save_state_data($post_id) {
    if (isset($_POST['venom_state_field'])) {
        update_post_meta($post_id, '_venom_state_value_key', sanitize_text_field($_POST['venom_state_field']));
    }
}

function venom_save_occupation_data($post_id) {
    if (isset($_POST['venom_occupation_field'])) {
        update_post_meta($post_id, '_venom_occupation_value_key', sanitize_text_field($_POST['venom_occupation_field']));
    }
}

function venom_save_vote_data($post_id) {
    if (isset($_POST['venom_vote_field'])) {
        update_post_meta($post_id, '_venom_vote_value_key', sanitize_text_field($_POST['venom_vote_field']));
    }
}

function venom_wpse_19240_change_place_labels() {
    $p_object = get_post_type_object('venom');
    if ($p_object) {
        $p_object->labels->add_new = 'Add Candidate';
        $p_object->labels->add_new_item = 'Add New Candidate';
        $p_object->labels->all_items = 'All Candidate';
        $p_object->labels->edit_item = 'Edit Candidate';
        $p_object->labels->new_item = 'New Candidate';
        $p_object->labels->not_found = 'No Candidates found';
        $p_object->labels->not_found_in_trash = 'No Candidates found in trash';
        $p_object->labels->search_items = 'Search Candidates';
        $p_object->labels->view_item = 'View Candidate';
    }
}

function venom_updated_messages($messages) {
    global $post, $post_ID;
    $messages['venom'] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf(__('Candidate updated.')),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Candidate updated.'),
        5 => isset($_GET['revision']) ? sprintf(__('Candidate restored to revision from %s'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
        6 => sprintf(__('Candidate published.')),
        7 => __('Candidate saved.'),
        8 => sprintf(__('Candidate submitted.')),
        9 => sprintf(__('Candidate scheduled for: <strong>%1$s</strong>. '), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date))),
        10 => sprintf(__('Candidate draft updated.')),
    );
    return $messages;
}
