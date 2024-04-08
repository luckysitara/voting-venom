<?php

/**
 * @package Venom
 * @version 2.1.0
 */

$taxonomy_id = $contest;

if($contest == "all"){
	$args = array(
		'post_type' => 'venom',
	    'post_status' => 'publish',
	    'posts_per_page' => get_option( 'venom_no_of_candidate_per_page' ) ? get_option( 'venom_no_of_candidate_per_page' ) : 10, 
	    'orderby' => 'title', 
	    'order' => 'ASC', 
	);

} else {
	$args = array(  
	    'post_type' => 'venom',
	    'post_status' => 'publish',
	    'posts_per_page' => get_option( 'venom_no_of_candidate_per_page' ) ? get_option( 'venom_no_of_candidate_per_page' ) : 10, 
	    'orderby' => 'title', 
	    'order' => 'ASC', 
	    'tax_query' => array(
		    array(
		    'taxonomy' => 'venom-category',
		    'field' => 'term_id',
		    'terms' => $contest,
		     ),
		),
	);
}

$loop = new WP_Query( $args );

if(!empty(get_option('venom_template'))){
	$template = get_option('venom_template');
} else {
	$template = 1;
}

//echo $taxonomy_id;
$term_name = get_term( $contest )->name;

if ($contest != "all") {
	echo '<h1>'.$term_name.'</h1>';
}
include 'pages/theme_'.$template.'.php';

?>

