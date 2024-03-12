<?php
/*
Plugin Name: Voting Venom
Description: A WordPress plugin for voting on contestants.
Version: 1.0
Author: Bughacker's Venom
*/

// Activation hook
register_activation_hook(__FILE__, 'voting_plugin_activation');

// Function to create database table on plugin activation
function voting_plugin_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contestants';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        votes int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Admin settings
function voting_plugin_settings_init() {
    register_setting('voting_plugin_settings', 'voting_plugin_amount_per_vote');
    register_setting('voting_plugin_settings', 'voting_plugin_paystack_test_secret_key');
    add_settings_section('voting_plugin_settings_section', 'Voting Settings', 'voting_plugin_settings_section_callback', 'voting-plugin-settings');
    add_settings_field('voting_plugin_amount_per_vote', 'Amount Per Vote', 'voting_plugin_amount_per_vote_callback', 'voting-plugin-settings', 'voting_plugin_settings_section');
    add_settings_field('voting_plugin_paystack_test_secret_key', 'Paystack Test Secret Key', 'voting_plugin_paystack_test_secret_key_callback', 'voting-plugin-settings', 'voting_plugin_settings_section');
}
add_action('admin_init', 'voting_plugin_settings_init');

function voting_plugin_settings_section_callback() {
    echo '<p>Configure the settings for voting plugin.</p>';
}

function voting_plugin_amount_per_vote_callback() {
    $amount_per_vote = get_option('voting_plugin_amount_per_vote');
    echo "<input type='number' name='voting_plugin_amount_per_vote' value='$amount_per_vote' />";
}

function voting_plugin_paystack_test_secret_key_callback() {
    $test_secret_key = get_option('voting_plugin_paystack_test_secret_key');
    echo "<input type='text' name='voting_plugin_paystack_test_secret_key' value='$test_secret_key' />";
}

// Admin menu
function voting_plugin_menu() {
    add_menu_page('Voting Settings', 'Voting Settings', 'manage_options', 'voting-plugin-settings', 'voting_plugin_settings_page');
}
add_action('admin_menu', 'voting_plugin_menu');

// Voting Settings Page
function voting_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Voting Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('voting_plugin_settings'); ?>
            <?php do_settings_sections('voting-plugin-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Frontend interface
function voting_form_shortcode() {
    ob_start();
    ?>
    <form id="voting-form" action="" method="post">
        <?php
        $contestants = get_contestants();
        foreach ($contestants as $contestant) {
            echo "<label>{$contestant->name}</label>";
            echo "<input type='number' name='votes[{$contestant->id}]' min='1' /><br>";
        }
        ?>
        <input type="submit" value="Vote">
    </form>

    <!-- Payment modal -->
    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirm Payment</h2>
            <p>Please confirm your payment details:</p>
            <form id="payment-form" method="post">
                <label for="vote-amount">Number of Votes:</label>
                <input type="text" id="vote-amount" name="vote-amount" readonly><br>
                <label for="payment-amount">Amount:</label>
                <input type="text" id="payment-amount" name="payment-amount" readonly><br>
                <input type="hidden" id="contestant-id" name="contestant-id">
                <input type="hidden" id="transaction-amount" name="transaction-amount">
                <input type="submit" value="Confirm Payment">
            </form>
        </div>
    </div>

    <script>
    // Get the payment modal
    var paymentModal = document.getElementById('payment-modal');

    // Open modal when form is submitted
    var votingForm = document.getElementById('voting-form');
    votingForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var selectedContestant = document.querySelector('input[name="votes"]:checked');
        var voteAmount = selectedContestant.value;
        var contestantId = selectedContestant.dataset.contestantId;
        var amountPerVote = <?php echo get_option('voting_plugin_amount_per_vote'); ?>;
        var paymentAmount = voteAmount * amountPerVote;
        document.getElementById('vote-amount').value = voteAmount;
        document.getElementById('payment-amount').value = paymentAmount;
        document.getElementById('contestant-id').value = contestantId;
        document.getElementById('transaction-amount').value = paymentAmount;
        paymentModal.style.display = 'block';
    });

    // Close modal function
    function closeModal(modal) {
        modal.style.display = 'none';
    }

    // Close modal when close button is clicked
    var closeButtons = document.getElementsByClassName('close');
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener('click', function() {
            closeModal(this.parentElement.parentElement);
        });
    }

    // AJAX handler for processing payment
    var paymentForm = document.getElementById('payment-form');
    paymentForm.addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(paymentForm);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Process response from server
                alert(xhr.responseText);
                // Close the payment modal
                closeModal(paymentModal);
            } else {
                // Handle error
                alert('Error processing payment. Please try again.');
            }
        };
        xhr.onerror = function() {
            // Handle network errors
            alert('Network error. Please try again later.');
        };
        xhr.send(formData);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('voting_form', 'voting_form_shortcode');

// AJAX handler for processing payment with Paystack
function process_payment_ajax_handler() {
    $amount = $_POST['transaction_amount'];
    $secret_key = get_option('voting_plugin_paystack_test_secret_key'); // Use test secret key for now

    // Process payment with Paystack
    $transaction_reference = process_payment_with_paystack($amount, $secret_key);

    // Check if transaction reference was generated successfully
    if ($transaction_reference) {
        echo 'Payment processed successfully! Transaction Reference: ' . $transaction_reference;
    } else {
        echo 'Error processing payment. Please try again.';
    }
    wp_die(); // Terminate AJAX request
}
add_action('wp_ajax_process_payment', 'process_payment_ajax_handler');
add_action('wp_ajax_nopriv_process_payment', 'process_payment_ajax_handler');

// Payment callback function to handle payment verification after the user completes the payment on Paystack
function paystack_payment_callback() {
    // Verify payment with Paystack
    $reference = $_GET['reference'];
    $secret_key = get_option('voting_plugin_paystack_test_secret_key'); // Use test secret key for now

    // Prepare request to Paystack API
    $paystack_url = 'https://api.paystack.co/transaction/verify/' . $reference;
    $response = wp_remote_get($paystack_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $secret_key,
        ),
    ));

    // Check for errors in response
    if (is_wp_error($response)) {
        echo 'Error verifying payment. Please try again.';
        exit;
    }

    // Retrieve response body
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if payment was successful
    if ($data && isset($data['data']) && $data['data']['status'] === 'success') {
        // Update contestant votes in the database
        $votes = intval($data['data']['metadata']['votes']);
        $contestant_id = intval($data['data']['metadata']['contestant_id']);
        update_contestant_votes($contestant_id, $votes);

        // Output success message
        echo 'Payment verification successful! Votes have been added to the contestant.';
    } else {
        // Payment verification failed
        echo 'Payment verification failed. Please try again.';
    }

    // End script execution
    wp_die();
}
add_action('wp_ajax_paystack_payment_callback', 'paystack_payment_callback');
add_action('wp_ajax_nopriv_paystack_payment_callback', 'paystack_payment_callback');

// Function to process payment with Paystack
function process_payment_with_paystack($amount, $secret_key) {
    $transaction_url = 'https://api.paystack.co/transaction/initialize';

    $args = array(
        'amount' => $amount * 100, // Paystack accepts amount in kobo (smallest currency unit in Nigeria)
        'email' => 'customer@example.com', // Get customer's email from user session
        'callback_url' => admin_url('admin-ajax.php?action=paystack_payment_callback') // Callback URL for payment verification
    );

    $response = wp_remote_post($transaction_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $secret_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($args),
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($data && isset($data['data']['reference'])) {
        return $data['data']['reference'];
    } else {
        return false;
    }
}

// Database functions
// Function to create contestants table
function create_contestants_table() {
    global $wpdb;
    $contestants_table = $wpdb->prefix . 'contestants'; // Define the table name

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $contestants_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        votes int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register activation hook
register_activation_hook(__FILE__, 'voting_plugin_activation');

// Function to update contestant votes
function update_contestant_votes($contestant_id, $votes) {
    global $wpdb;
    $contestants_table = $wpdb->prefix . 'contestants'; // Define the table name

    $wpdb->update(
        $contestants_table,
        array('votes' => $votes),
        array('id' => $contestant_id),
        array('%d'),
        array('%d')
    );
}

// Function to get total votes for a contestant
function get_total_votes($contestant_id) {
    global $wpdb;
    $contestants_table = $wpdb->prefix . 'contestants'; // Define the table name

    $votes = $wpdb->get_var($wpdb->prepare("SELECT votes FROM $contestants_table WHERE id = %d", $contestant_id));
    return $votes ? $votes : 0;
}

// Fetch contestants from WordPress users
function get_wordpress_users_as_contestants() {
    $users = get_users();
    $contestants = array();
    foreach ($users as $user) {
        $contestant = new stdClass();
        $contestant->id = $user->ID;
        $contestant->name = $user->display_name;
        // Add other contestant details like profile photo, about me, etc. if needed
        $contestants[] = $contestant;
    }
    return $contestants;
}

// Function to get contestants
function get_contestants() {
    $contestants = get_wordpress_users_as_contestants();
    return $contestants;
}
