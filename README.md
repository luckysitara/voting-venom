# voting-venom
Voting-Venom Plugin Documentation
Overview
Voting-Venom is a WordPress plugin designed to facilitate voting on contestants. It allows users to vote for their favorite contestants by selecting the number of votes and processing the payment using the Paystack payment gateway.

Installation
Download the Voting-Venom plugin zip file.
Log in to your WordPress admin panel.
Navigate to Plugins > Add New.
Click on the Upload Plugin button.
Choose the zip file you downloaded and click Install Now.
Once installed, click Activate Plugin.
Configuration
After activating the plugin, you need to configure the settings:

Go to Settings > Voting Settings.
Set the amount per vote and enter your Paystack test secret key.
Save the changes.
Adding Contestants
To add contestants:

Go to Voting-Venom > Add Contestants.
Use the search bar to find users from the WordPress database.
Click on Add as Contestant next to the user you want to add.
Contestant details such as name, profile picture, and about me will be fetched from the user's profile and added to the contestant list.
Voting
To vote for contestants:

Use the [voting_form] shortcode to display the voting form on any post or page.
Select the number of votes for each contestant and click Vote.
A modal will appear to confirm the payment details.
Enter your payment details and click Confirm Payment.
Once the payment is processed, the votes will be added to the contestants.
Contestant Dashboard
You can view the contestant dashboard to see the list of contestants and their total votes:

Go to Voting-Venom > Contestant Dashboard.
The dashboard will display the list of contestants and their total votes.
Shortcodes
[voting_form]: Display the voting form on any post or page.
AJAX and Paystack Integration
The plugin utilizes AJAX for asynchronous processing of votes and Paystack payment gateway for secure payment processing.

Additional Notes
Ensure that you have a Paystack account and obtain the test secret key for testing purposes.
Make sure to test the plugin in a staging environment before deploying it to a live website.
Check Paystack documentation for information on setting up payment webhooks for handling payment verification.
AUTHOR: 
# BUGHACKER
