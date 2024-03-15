<?php

function ewvwp_add_admin_page() {

    //Admin page

	add_submenu_page( 'edit.php?post_type=ewvwp', 'Voting Settings', 'Settings', 'manage_options', 'ewvwp_plugin', 'ewvwp_setting_page');

    //Activate Custom Setting

    add_action( 'admin_init', 'ewvwp_custom_setting' );


}
add_action( 'admin_menu', 'ewvwp_add_admin_page' );


function ewvwp_custom_setting() {

	register_setting( 'ewvwp-group', 'ewvwp_display_vote' );
	register_setting( 'ewvwp-group', 'ewvwp_display_state' );
	register_setting( 'ewvwp-group', 'ewvwp_paystack_public_key' );
	register_setting( 'ewvwp-group', 'ewvwp_paystack_secret_key' );
	register_setting( 'ewvwp-group', 'ewvwp_min_amount' );
	register_setting( 'ewvwp-group', 'ewvwp_template' );
	register_setting( 'ewvwp-group', 'ewvwp_no_of_candidate_per_page' );


	add_settings_field( 'ewvwp-display-vote', 'Display Vote Counts', 'ewvwp_display_vote_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

	add_settings_field( 'ewvwp-display-state', 'Display Candidate State', 'ewvwp_display_state_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

	add_settings_field( 'ewvwp-template', 'Select Template', 'ewvwp_template_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

	add_settings_field( 'ewvwp-min-amount', 'Amount for one vote', 'ewvwp_min_amount_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

	add_settings_field( 'ewvwp-no-of-cand-per-page', 'Number of Candidate Per Page', 'ewvwp_no_of_cand_per_page_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

	add_settings_section( 'ewvwp-form-plugin' , 'Settings' , 'ewvwp_plugin_settings' , 'ewvwp_plugin' );
	add_settings_field( 'ewvwp-public-key', 'Paystack Public Key', 'ewvwp_paystack_public_key_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );
	add_settings_field( 'ewvwp-secret-key', 'Paystack Secret Key', 'ewvwp_paystack_secret_key_input', 'ewvwp_plugin', 'ewvwp-form-plugin' );

}


function ewvwp_setting_page() {
	include( plugin_dir_path(__FILE__) . 'templates/admin.php');
}

function ewvwp_plugin_settings(){
	//echo "Paystack Public Key";
}

function ewvwp_paystack_public_key_input() {
	$option = get_option( 'ewvwp_paystack_public_key' );
	echo '<input type="text" name="ewvwp_paystack_public_key" value="'.$option.'" id="ewvwp_paystack_public_key"/>';
}

function ewvwp_display_vote_input() {
	$option = get_option( 'ewvwp_display_vote' );
	$checked = ( @$option == 1 ? 'checked' : '' );
	echo '<label><input type="checkbox" name="ewvwp_display_vote" value="1" id="ewvwp_display_vote" '.$checked.' /></label>';
}

function ewvwp_display_state_input() {
	$option = get_option( 'ewvwp_display_state' );
	$checked = ( @$option == 1 ? 'checked' : '' );
	echo '<label><input type="checkbox" name="ewvwp_display_state" value="1" id="ewvwp_display_state" '.$checked.' /></label>';
}


function ewvwp_template_input() {
	$option = get_option( 'ewvwp_template' );
	echo '<select name="ewvwp_template" id="ewvwp_template">
			<option value="1"'; ?> <?php if ($option == 1) { echo "selected"; } ?> <?php echo '>Default</option>
			<option value="2"'; ?> <?php if ($option == 2) { echo "selected"; } ?> <?php echo '>Theme 1</option>
		 </select>';
}


function ewvwp_paystack_secret_key_input() {
	$option = get_option( 'ewvwp_paystack_secret_key' );
	echo '<input type="text" name="ewvwp_paystack_secret_key" value="'.$option.'" id="ewvwp_paystack_secret_key"/>';
}


function ewvwp_min_amount_input() {
	$option = get_option( 'ewvwp_min_amount' );
	echo '<input type="number" name="ewvwp_min_amount" value="'.$option.'" id="ewvwp_min_amount"/><p class="description">Note: Amount is in NGN</p>';
}

function ewvwp_no_of_cand_per_page_input() {
	$option = get_option( 'ewvwp_no_of_candidate_per_page' ) ? get_option( 'ewvwp_no_of_candidate_per_page' ) : 10;
	echo '<input type="number" name="ewvwp_no_of_candidate_per_page" value="'.$option.'" id="ewvwp_no_of_candidate_per_page"/><p class="description">Note: This is going to be the number of Candidate per page</p>';
}