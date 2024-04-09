    <style>
        .tp-vote-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            justify-content: center;
        }

        .vote-item{
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);  
            overflow: hidden;
        }

        .vote-item-title{
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 25px;
            font-weight: 600;
            line-height: 32.78px;
            color: #191d23;
            text-transform: capitalize;
            display: block;
            text-align: center;
            width: 100%;
        }

        .vote-item a{
            padding-bottom:20px;
            background-color: #F5D429;
            padding-top:10px;
            width:100%;
            text-decoration: none;
            text-align: center;
            display: flex;
            flex-direction: column;
            color: white;
            font-size: 24px;
            font-weight: 600;
            cursor: pointer;   
        }   
         
        @media(max-width:600px){
         .tp-vote-container{grid-template-columns:repeat(1, 1fr);}
        }
        
        @media(min-width:768px){
         .tp-vote-container{grid-template-columns:repeat(3, 1fr);}
        }
        
        @media(min-width:928px){
         .tp-vote-container{grid-template-columns:repeat(4, 1fr);}
        }

        .vote-item a:hover{
            background-color: #F5D429;
        }

        .vote-item-img {
            height: 300px;
            overflow: hidden;
        }

        .vote-item-img img {
            height: 100%;
            width: auto;
            object-fit: cover;
        }

        .vote-item-count {
            font-size: 20px;
            line-height: 20px;
            font-weight: 600;
            color: #4A4948;
            margin-bottom: 16px;
            display: block;
            text-align: center;
            width: 100%;
        }

        section.tp-search-bar {
            margin-top:30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        section.tp-search-bar input{
            width:50%;
            padding: 15px;
            padding-left:10px;
            border-radius: 5px;
            border:1px solid grey;
        }

        section.tp-search-bar button{
            
            padding: 15px;
            margin-left: 10px;
            color: white;
            background-color: rgb(133, 209, 18);
            border: 1px solid grey;
            border-radius: 5px;
        }

        section.tp-search-bar button:hover{
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
            z-index: 99;
        }

        .venom-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 1rem 1.5rem;
            width: 50%;
            border-radius: 0.5rem;
        }

        .venom-close-button {
            float: right;
            width: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            cursor: pointer;
            border-radius: 0.25rem;
        }

        .venom-show-modal {
            opacity: 1;
            visibility: visible;
            transform: scale(1.0);
            transition: visibility 0s linear 0s, opacity 0.25s 0s, transform 0.25s;
        }

        .venom-modal-content div{
            display:flex;
            flex-direction: column;
            padding-top: 35px;
            padding-bottom: 25px;

        }

        
        .venom-modal-content div input{
            width: 100%;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ECEBE6;
            margin-bottom: 10px;
        }

        .venom-modal-submit {
            background-color: #F6D941;
            color: white;
            font-size: 20px;
            font-weight: 600;
            border: none;
        }


    </style>


    <!--<section class="tp-search-bar">
        <input type="text" placeholder="Search For a Participant...">
        <button>Search</button>
    </section>-->

    <section class="tp-vote-container">
    

    <?php 
        while ( $loop->have_posts() ) : $loop->the_post();
        $nickname = get_post_meta(get_the_ID(),"_venom_nickname_value_key",true);
        $age = get_post_meta(get_the_ID(),"_venom_age_value_key",true);
        $state = get_post_meta(get_the_ID(),"_venom_state_value_key",true);
        $vote = get_post_meta(get_the_ID(),"_venom_vote_value_key",true);
        $profile_image = get_post_meta(get_the_ID(), "_venom_profile_image_value_key", true); // Added profile image retrieval
    ?>

    

        <div class="vote-item">
            <?php the_post_thumbnail(); ?>
            <div class="vote-item-img">
                <img src="<?php echo esc_url($profile_image); ?>" alt="<?php the_title(); ?>">
            </div>
            <h4 class="vote-item-title"><?php the_title(); ?></h4>
            <?php if(get_option('venom_display_state') == 1): ?>
            <p>State: <?php echo $state; ?></p>
            <?php endif; ?>
            <div class="vote-item-count">
                <?php if(get_option('venom_display_vote') == 1): ?>
                <span>Votes: <?php echo $vote; ?></span>
                <?php endif; ?>
            </div>
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
                    <input type="submit" name="vote" value="Vote" class="venom-modal-submit">
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

        function venomWVWPMForm(id){
            toggleModal();
            document.getElementById("vote-id").value = id;
        }


        function venomWVWPMFormSubmit(event){
            event.preventDefault();
            var id = document.getElementById("vote-id").value;
            var quantity = document.getElementById("venom-number-of-vote").value;
            var amount = document.getElementById("venom-amount-of-vote").value;
            var email = document.getElementById("venom-email").value;
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

            if (email == "" || quantity == "" ) {

                alert("Fill the necessary details");

                return;
            }
            
            var handler = PaystackPop.setup({
                key: '<?php echo get_option( 'venom_paystack_public_key' ); ?>', // Replace with your public key
                email: email,
                amount: amount * 100, // the amount value is multiplied by 100 to convert to the lowest currency unit
                currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars
                reference: 'Easy Wp Voting With Payment', // Replace with a reference you generated
                callback: function(response) {
                //this happens after the payment is completed successfully
                var reference = response.reference;
                console.log(reference);
                jQuery.ajax({
                    url : ajaxurl,
                    type : 'post',
                    dataType: 'json',
                    data : {

                        quantity : quantity,
                        userID : id,
                        reference: reference,
                        email: email,
                        action: 'venom_form_ajax'

                    },
                    success : function( response ){
                            
                        if(response.success == true){
                            document.getElementById("venom-theme-2-form").reset();
                            alert(response.message);
                            setTimeout(window.location.reload(), 500);
                        } else {
                            //console.log(response.message);
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

        function updateAmount(event){
            var quantity = event.target.value;

            var total = quantity * <?php echo get_option('venom_min_amount'); ?>;
            document.getElementById("venom-amount-of-vote").value = total;
        }
        //trigger.addEventListener("click", toggleModal);
        closeButton.addEventListener("click", toggleModal);
        window.addEventListener("click", windowOnClick);

    </script>
<?php
wp_reset_postdata(); 

?>
