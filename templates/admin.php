<?php settings_errors(); ?>
<p>Use this <strong>shortcode</strong> to display your venom wp voting  inside a page or a post</p>
<p>Note: Payment are accept in <strong>NGN</strong></p>
<p><code>[venom_plugin]</code></p>
<p>Note: To display candidate per contest, click here to copy the <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=venom-category&post_type=venom' ); ?>">Shortcode</a> of each contest</p>
<form method="post" action="options.php" class="mujhtech-general-form">
	<?php settings_fields( 'venom-group' ); ?>
	<?php do_settings_sections( 'venom_plugin' ); ?>
	<?php submit_button( 'Save Changes', 'primary', 'btnSubmit' ); ?>
</form>
