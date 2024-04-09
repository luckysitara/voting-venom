<?php
/*
Template Name: Theme 2 Template
*/
get_header();
?>

<style>
    .tp-vote-container {
        margin: 20px;
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        justify-content: center;
    }

    .vote-item {
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 1px solid #F5D429;
    }

    .vote-item img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin-bottom: 10px; /* Added margin for spacing */
    }

    .vote-item span {
        font-size: 25px;
        margin-bottom: 5px; /* Added margin for spacing */
    }

    .vote-item a {
        background-color: rgb(129, 204, 18);
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        font-size: 24px;
        font-weight: bold;
        margin-top: 10px; /* Added margin for spacing */
    }

    .vote-item a:hover {
        background-color: rgb(104, 170, 4);
    }
</style>

<section class="tp-vote-container">
    <?php
    $loop = new WP_Query(array(
        'post_type' => 'venom',
        'posts_per_page' => -1,
        'order' => 'ASC',
    ));

    while ($loop->have_posts()) : $loop->the_post();
        $nickname = get_post_meta(get_the_ID(), "_venom_nickname_value_key", true);
        $age = get_post_meta(get_the_ID(), "_venom_age_value_key", true);
        $state = get_post_meta(get_the_ID(), "_venom_state_value_key", true);
        $vote = get_post_meta(get_the_ID(), "_venom_vote_value_key", true);
        $profile_image = get_post_meta(get_the_ID(), "_venom_profile_image_value_key", true); // Get the profile image URL
    ?>
        <div class="vote-item">
            <img src="<?php echo esc_attr($profile_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            <span><?php the_title(); ?></span>
            <?php if (get_option("venom_display_state") == 1) : ?>
                <span>State: <?php echo $state; ?></span>
            <?php endif; ?>
            <?php if (get_option("venom_display_vote") == 1) : ?>
                <span>Votes: <?php echo $vote; ?></span>
            <?php endif; ?>
            <a class="venom-trigger" id="vote-<?php print get_the_ID(); ?>" onclick="return venomWVWPMForm(<?php print get_the_ID(); ?>)">Vote Now</a>
        </div>
    <?php endwhile; ?>
</section>

<div class="venom-modal">
    <div class="venom-modal-content">
        <span class="venom-close-button">&times;</span>
        <div>
            <form method="post" action="#" id="venom-theme-2-form" onsubmit="return venomWVWPMFormSubmit(event)">
                <input type="hidden" name="vote-id" value="" id="vote-id">
                <input placeholder="Enter your Email" id="venom-email" type="text">
                <input type="number" id="venom-number-of-vote" onkeyup="return updateAmount(event)" placeholder="Number of Votes">
                <input type="number" id="venom-amount-of-vote" readonly placeholder="Amount">
                <input type="submit" name="vote" value="Vote">
            </form>
        </div>
    </div>
</div>

<script>
    // MODAL BOX JS
    var modal = document.querySelector(".venom-modal");
    var trigger = document.querySelector(".venom-trigger");
    var closeButton = document.querySelector(".venom-close-button");
    var numberOfVote = document.getElementById("venom-number-of-vote");

    function toggleModal() {
        modal.classList.toggle("venom-show-modal");
    }

    function windowOnClick(event) {
        if (event.target === modal) {
            toggleModal();
        }
    }

    function venomWVWPMForm(id) {
        toggleModal();
        document.getElementById("vote-id").value = id;
    }


    function venomWVWPMFormSubmit(event) {
        event.preventDefault();
        var id = document.getElementById("vote-id").value;
        var quantity = document.getElementById("venom-number-of-vote").value;
        var amount = document.getElementById("venom-amount-of-vote").value;
        var email = document.getElementById("venom-email").value;
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

        if (email == "" || quantity == "") {
            alert("Fill the necessary details");
            return;
        }

        var handler = PaystackPop.setup({
            key: '<?php echo get_option('venom_paystack_public_key'); ?>', // Replace with your public key
            email: email,
            amount: amount * 100, // the amount value is multiplied by 100 to convert to the lowest currency unit
            currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars
            reference: 'Easy Wp Voting With Payment', // Replace with a reference you generated
            callback: function(response) {
                //this happens after the payment is completed successfully
                var reference = response.reference;
                console.log(reference);
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        quantity: quantity,
                        userID: id,
                        reference: reference,
                        email: email,
                        action: 'venom_form_ajax'
                    },
                    success: function(response) {
                        if (response.success == true) {
                            document.getElementById("venom-theme-2-form").reset();
                            alert(response.message);
                            setTimeout(window.location.reload(), 500);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            },
            onClose: function() {
                alert('Transaction was not completed, window closed.');
            },
        });
        handler.openIframe();
    }

    function updateAmount(event) {
        var quantity = event.target.value;
        var total = quantity * <?php echo get_option('venom_min_amount'); ?>;
        document.getElementById("venom-amount-of-vote").value = total;
    }

    closeButton.addEventListener("click", toggleModal);
    window.addEventListener("click", windowOnClick);
</script>

<?php
wp_reset_postdata();
get_footer();
?>
