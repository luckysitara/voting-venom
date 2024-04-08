<?php
/**
 * @package Venom
 * @version 1.0
 */


function venom_add_admin_page() {

    //Admin page

	add_submenu_page( 'edit.php?post_type=venom', 'Easy Wp Voting Settings', 'Settings', 'manage_options', 'venom_plugin', 'venom_setting_page');

    //Activate Custom Setting

    add_action( 'admin_init', 'venom_custom_setting' );


}
add_action( 'admin_menu', 'venom_add_admin_page' );


function venom_custom_setting() {

	register_setting( 'venom-group', 'venom_display_vote' );
	register_setting( 'venom-group', 'venom_display_state' );
	register_setting( 'venom-group', 'venom_paystack_public_key' );
	register_setting( 'venom-group', 'venom_paystack_secret_key' );
	register_setting( 'venom-group', 'venom_min_amount' );
	register_setting( 'venom-group', 'venom_template' );
	register_setting( 'venom-group', 'venom_no_of_candidate_per_page' );


	add_settings_field( 'venom-display-vote', 'Display Vote Counts', 'venom_display_vote_input', 'venom_plugin', 'venom-form-plugin' );

	add_settings_field( 'venom-display-state', 'Display Candidate State', 'venom_display_state_input', 'venom_plugin', 'venom-form-plugin' );

	add_settings_field( 'venom-template', 'Select Template', 'venom_template_input', 'venom_plugin', 'venom-form-plugin' );

	add_settings_field( 'venom-min-amount', 'Amount for one vote', 'venom_min_amount_input', 'venom_plugin', 'venom-form-plugin' );

	add_settings_field( 'venom-no-of-cand-per-page', 'Number of Candidate Per Page', 'venom_no_of_cand_per_page_input', 'venom_plugin', 'venom-form-plugin' );

	add_settings_section( 'venom-form-plugin' , 'Settings' , 'venom_plugin_settings' , 'venom_plugin' );
	add_settings_field( 'venom-public-key', 'Paystack Public Key', 'venom_paystack_public_key_input', 'venom_plugin', 'venom-form-plugin' );
	add_settings_field( 'venom-secret-key', 'Paystack Secret Key', 'venom_paystack_secret_key_input', 'venom_plugin', 'venom-form-plugin' );

}


function venom_setting_page() {
	include( plugin_dir_path(__FILE__) . 'templates/admin.php');
}

function venom_plugin_settings(){
	//echo "Paystack Public Key";
}

function venom_paystack_public_key_input() {
	$option = get_option( 'venom_paystack_public_key' );
	echo '<input type="text" name="venom_paystack_public_key" value="'.$option.'" id="venom_paystack_public_key"/>';
}

function venom_display_vote_input() {
	$option = get_option( 'venom_display_vote' );
	$checked = ( @$option == 1 ? 'checked' : '' );
	echo '<label><input type="checkbox" name="venom_display_vote" value="1" id="venom_display_vote" '.$checked.' /></label>';
}

function venom_display_state_input() {
	$option = get_option( 'venom_display_state' );
	$checked = ( @$option == 1 ? 'checked' : '' );
	echo '<label><input type="checkbox" name="venom_display_state" value="1" id="venom_display_state" '.$checked.' /></label>';
}


function venom_template_input() {
	$option = get_option( 'venom_template' );
	echo '<select name="venom_template" id="venom_template">
			<option value="1"'; ?> <?php if ($option == 1) { echo "selected"; } ?> <?php echo '>Default</option>
			<option value="2"'; ?> <?php if ($option == 2) { echo "selected"; } ?> <?php echo '>Theme 1</option>
		 </select>';
}


function venom_paystack_secret_key_input() {
	$option = get_option( 'venom_paystack_secret_key' );
	echo '<input type="text" name="venom_paystack_secret_key" value="'.$option.'" id="venom_paystack_secret_key"/>';
}


function venom_min_amount_input() {
	$option = get_option( 'venom_min_amount' );
	echo '<input type="number" name="venom_min_amount" value="'.$option.'" id="venom_min_amount"/><p class="description">Note: Amount is in NGN</p>';
}

function venom_no_of_cand_per_page_input() {
	$option = get_option( 'venom_no_of_candidate_per_page' ) ? get_option( 'venom_no_of_candidate_per_page' ) : 10;
	echo '<input type="number" name="venom_no_of_candidate_per_page" value="'.$option.'" id="venom_no_of_candidate_per_page"/><p class="description">Note: This is going to be the number of Candidate per page</p>';
}
