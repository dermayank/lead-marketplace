Background: At [EduGorilla Directory Listing](https://directory.edugorilla.com) Portal, we receive 50+ queries every day. We have more than 40,000 Coaching centers, which are increasing at a rate of 1 Institute per 40secs, listed on our website.

We have to come with a transparent platform of all the leads on our website. There are two aspects of this project:

1. Promotional Lead: These leads will be forwarded to all the people in that category for free
2. Sellable leads: These leads can be sold in the market. People can buy them.

## Promotional Lead:

 We would get the leads accumulated in our database and send it in email to the interested parties.
   
## Sellable leads:
  The marketing manager/Owner of the institute will be the user of this portal and they use the credits present in their account to get the details of leads(email/phone of people interested). 

### Requirements (Step 1):

Create a ready to deploy Wordpress Plugin.

Create a form to add following details

1. A)Name of lead:
2. B)Contact Number of lead:
3. C)Email ID of lead:
4. D)Query Link or query of lead:
5. E)User Location
6. F)Query Category
7. G)Keyword

### Requirements (Step 2):

Show the details of all the fetched Leads. They should be properly hyperlinked.

1. Users should be able to purchase definite number of EduCash from their account. 
2. We should be able to modify the EduCash credited in User&#39;s account from backend from the admin UI.
3. User should be able to see the current points and history in his user panel.
4. User should be able to sort, filter and find leads before purchasing.
5. Overview of the leads should be given to the user before he makes the purchase (like which city the lead is from and what service the lead is looking for).
5. User can see contact details of the lead once he is ready to trade his EduCash and those corresponding number of EduCash should be deducted for the user.
6. The proportion between how many leads you can buy with one EduCash should be configurable. By default,one EduCash can buy one lead. UI to unlock leads from using EduCash. 

# Technology:

Wordpress Plugin.

Please follow the coding standards from :  https://make.wordpress.org/core/handbook/best-practices/coding-standards/

## Getting Started:

First you would need to get started with running a local wordpress instance, use this to start a LAMP server: https://www.digitalocean.com/community/tutorials/how-to-install-wordpress-with-lamp-on-ubuntu-16-04
Check this tutorial if you face any issues in creating a new post : https://www.digitalocean.com/community/tutorials/how-to-set-up-mod_rewrite-for-apache-on-ubuntu-14-04

We are using Superlist theme to display content on our website.

Step 1: Download Superlist theme from [https://edugorilla.com/superlist.zip](https://edugorilla.com/superlist.zip)

Step 2: Install superlist plugin and check that all its dependent plugins are correctly installed.

Step 3 : Go to Tools->One Click Installation and run the demo installer.(Pragmatic Mates One Click plugin should be active)

Step 4 : (Optional) Enable automatic plugin upgrade : https://wordpress.org/plugins/easy-theme-and-plugin-upgrades/

Step 5: Download the zip file of this plugin from git and install it on the dashboard using Plugins->Add new -> Upload Plugin

### Shortcodes Used:

The following shortcodes are currently supported by this project : 

1. [edugorilla_leads] - This is the frontend UI for marketing managers to see the some details of possible leads and unlock the contact details by spending EduCash
1. [educash_payment] - This page shows you the Payment gateway through which the user can buy EduCash
1. [transaction_history]- This page can be used to view the current client's EduCash Trasncation history
1. [client_preference_form] - This is to be used by the clients to enter their details and subscribe for email notifications.
1. [manage_leads] - This will create a UI containing the above four short codes as tabs.

### Admin Menus:

The following menus will be available in the admin page after the plugin is installed : 

1. Lead capture form - This is the main form that is used to add new Leads to our system
1. Sent Leads - This form shows you Promotional/Bought leads that are sent to the customers
1. OTP- This is used for debugging purposes to get the OTP to mobile number
1. Email Templates - This is to be used by the operations team to edit the template of the email sent to customers
1. SMS Templates - This is to be used by the operations team to edit the template of the SMS sent to customers
1. Allocate EduCash - This can be used to allocate new EduCash to our customers
1. Transaction History - An UI to see the transaction history for any customer
1. Client Preferences - This is the backend UI for the client preferences table that can be edited by user from [client_preference_form] UI
1. Conversion Tables - An UI to modify the conversion tables which are used to convert between EduCash/Leads and actual money.
1. Third Party Settings - Misc Admin settings that are used to communicate with third party APIs.


### Database Tables Used:

The following tables will be added to the database after the plugin is installed : 

1. edugorilla_lead_details - The table containing all the leads.
1. edugorilla_lead_contact_log - Logs when the leads were contacted.
1. edugorilla_lead_educash_transactions - Transaction history for EduCash.
1. edugorilla_lead_client_mapping - Mapping between client id and lead id.
1. edugorilla_educash_conversion_ratio - Mapping between educash and other currencies.
1. edugorilla_client_preferences - Contains the details of the client's email/sms preferences

# Scope of project:

We will contribute this project back to Open Source Repository i.e. a Wordpress Plugin or even host in CodeCanyon.

# Glossary:

Lead: Contact details of students who are interested in a particular educational service.

EduCash : The credit/points assiciated with each account that the user can utilize to purchase leads.
