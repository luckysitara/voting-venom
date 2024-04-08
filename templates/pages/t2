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

    section.tp-search-bar {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    section.tp-search-bar input {
        width: 50%;
        padding: 15px;
        padding-left: 10px;
        border-radius: 5px;
        border: 1px solid grey;
    }

    section.tp-search-bar button {
        padding: 15px;
        margin-left: 10px;
        color: white;
        background-color: rgb(133, 209, 18);
        border: 1px solid grey;
        border-radius: 5px;
    }

    section.tp-search-bar button:hover {
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

<section class="tp-vote-container">
    <?php
    while ($loop->have_posts()) : $loop->the_post();
        $nickname = get_post_meta(get_the_ID(), "_venom_nickname_value_key", true);
        $age = get_post_meta(get_the_ID(), "_venom_age_value_key", true);
        $state = get_post_meta(get_the_ID(), "_venom_state_value_key", true);
        $vote = get_post_meta(get_the_ID(), "_venom_vote_value_key", true);
        $profile_picture = get_post_meta(get_the_ID(), '_venom_profile_picture_key', true);
        ?>

        <div class="vote-item">
            <?php
            if (!empty($profile_picture)) {
                echo '<img src="' . esc_url($profile_picture) . '" style="max-width: 320px; padding: 20px; border-radius: 5px;" />';
            } else {
                echo '<img src="YOUR_DEFAULT_IMAGE_URL" style="max-width: 320px; padding: 20px; border-radius: 5px;" />'; // Replace YOUR_DEFAULT_IMAGE_URL with the URL of your default image
            }
            ?>
            <span><?php the_title(); ?></span>
            <?php if (get_option('venom_display_state') == 1) : ?>
                <span>State: <?php echo $state; ?></span>
            <?php endif; ?>
            <?php if (get_option('venom_display_vote') == 1) : ?>
                <span>Votes: <?php echo $vote; ?></span>
            <?php endif; ?>
            <a class="venom-trigger" id="vote-<?php print get_the_ID(); ?>" onclick="return venomWVWPMForm(<?php print get_the_ID(); ?>)">Vote Now</a>
        </div>

    <?php endwhile; ?>
</section>

<script>
    // Add your JavaScript code here for modal and form handling
</script>

