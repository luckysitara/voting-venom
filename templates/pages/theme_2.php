<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System</title>
    <style>
        /* Updated CSS Styles for Grid Layout */
        body .is-layout-constrained > :where(:not(.alignleft):not(.alignright):not(.alignfull)) {
            max-width: 100% !important;
        }

        .tp-vote-container {
            margin: 20px;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 1rem;
            justify-content: center;
        }

        .vote-item {
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1px solid rgb(115, 187, 8);
            padding: 10px;
        }

        .vote-item img {
            max-width: 320px;
            padding: 20px;
            border-radius: 5px;
        }

        .vote-item span {
            padding-bottom: 10px;
            font-size: 25px;
        }

        .vote-item a {
            padding-bottom: 20px;
            background-color: rgb(129, 204, 18);
            padding-top: 10px;
            width: 100%;
            text-decoration: none;
            text-align: center;
            display: flex;
            flex-direction: column;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .vote-item a:hover {
            background-color: rgb(104, 170, 4);
        }

        .tp-search-bar {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tp-search-bar input {
            width: 50%;
            padding: 15px;
            padding-left: 10px;
            border-radius: 5px;
            border: 1px solid grey;
        }

        .tp-search-bar button {
            padding: 15px;
            margin-left: 10px;
            color: white;
            background-color: rgb(133, 209, 18);
            border: 1px solid grey;
            border-radius: 5px;
        }

        .tp-search-bar button:hover {
            cursor: pointer;
            background-color: rgb(104, 170, 4);
        }

        .venom-modal {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transform: scale(1.1);
            transition: visibility 0s linear 0.25s, opacity 0.25s 0s, transform 0.25s;
        }

        .venom-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 1rem 1.5rem;
            width: 24rem;
            border-radius: 0.5rem;
        }

        .venom-close-button {
            float: right;
            width: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            cursor: pointer;
            border-radius: 0.25rem;
            background-color: rgb(206, 235, 197);
        }

        .venom-close-button:hover {
            background-color: rgb(244, 247, 243);
        }

        .venom-show-modal {
            opacity: 1;
            visibility: visible;
            transform: scale(1.0);
            transition: visibility 0s linear 0s, opacity 0.25s 0s, transform 0.25s;
        }

        .venom-modal-content div {
            display: flex;
            flex-direction: column;
            padding-top: 35px;
            padding-bottom: 25px;
        }

        .venom-modal-content div input {
            width: 100%;
            padding: 15px;
            padding-left: 10px;
            border-radius: 5px;
            border: 1px solid grey;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <!--<section class="tp-search-bar">
        <input type="text" placeholder="Search For a Participant...">
        <button>Search</button>
    </section>-->

    <section class="tp-vote-container">

        <?php
        while ($loop->have_posts()) : $loop->the_post();
            $nickname = get_post_meta(get_the_ID(), "_venom_nickname_value_key", true);
            $age = get_post_meta(get_the_ID(), "_venom_age_value_key", true);
            $state = get_post_meta(get_the_ID(), "_venom_state_value_key", true);
            $vote = get_post_meta(get_the_ID(), "_venom_vote_value_key", true);
        ?>

            <div class="vote-item">
                <?php the_post_thumbnail(); ?> <!-- Display profile picture -->
                <span><?php the_title(); ?></span>
                <?php if (get_option('venom_display_state') == 1) : ?>
                    <span>State: <?php echo $state; ?></span>
                <?php endif; ?>
                <?php if (get_option('venom_display_vote') == 1) : ?>
                    <span>Votes: <?php echo $vote; ?></span>
                <?php endif; ?>
                <a class="venom-trigger" id="vote-<?php print get_the_ID(); ?>" onclick="return easyVenomMForm(<?php print get_the_ID(); ?>)">Vote Now</a>
            </div>

        <?php endwhile; ?>
    </section>
    <div class="venom-modal">
        <div class="venom-modal-content">
            <span class="venom-close-button">&times;</span>
            <div>
                <form method="post" action="#" id="venom-theme-2-form" onsubmit="return easyVenomMFormSubmit(event)">
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
        var closeButton = document.querySelector(".venom-close-button");

        function toggleModal() {
            modal.classList.toggle("venom-show-modal");
        }

        function easyVenomMForm(id) {
            toggleModal();
            document.getElementById("vote-id").value = id;
        }

        function easyVenomMFormSubmit(event) {
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
                key: '<?php echo get_option('venom_paystack_public_key'); ?>',
                email: email,
                amount: amount * 100,
                currency: 'NGN',
                reference: 'Easy Wp Voting With Payment',
                callback: function(response) {
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
                                toggleModal();
                                window.location.reload(true); // Reload the page after successful payment to update the vote count
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
        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                toggleModal();
            }
        });
    </script>
    <?php wp_reset_postdata(); ?>
</body>

</html>
