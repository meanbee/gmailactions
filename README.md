#Meanbee Gmailactions

Increase user-engagement in your Magento stores transactional emails. Adding schema supported by Gmail adds quick go-to buttons to your customers inboxes. For more info see: [Gmail Actions](https://developers.google.com/gmail/actions/)

![Go-to Actions Module Example](http://cl.ly/image/3d170G1c3E1x/Screenshot%202014-01-29%2015.58.14.png)

## Requirements:

- Emails must be [authenticated via DKIM or SPF](https://support.google.com/mail/answer/180707?hl=en)
	- If you are unable to modify your DNS records to support this, one way of achieving is through the use of a SMTP module
- Your domain of the DKIM or SPF signatures must match the domain of your From: email address exactly. eg for From: foo@bar.com the DKIM must be for the bar.com domain and not a subdomain such as email.bar.com.
- Emails must come from a static email address, eg foo@bar.com
- Emails must follow [Google's general email guidelines](https://support.google.com/mail/answer/81126?hl=en)


Once you have met these basic requirements, you can then [register with Google](https://developers.google.com/gmail/actions/registering-with-google) so your customers can start seeing the new Go-to Actions in their inboxes.


[Full list of requirements from Google](https://developers.google.com/gmail/actions/registering-with-google)


## Installation

You can install via [Modman](https://github.com/colinmollenhour/modman):

	cd /to/magento/root/
	modman init
	modman clone https://github.com/meanbee/gmailactions.git

Or by copying the files across manually (using the modman file as a reference)

Once installed, clear your cache and sessions.

## Configuration

We have added a few configurable options, which allows you to customise the `Action Text` which appears on the button. Plus `Description` which describes the contents of the email. These options are located at `System > Configuration > Gmail Actions > Gmail Actions Configurationn`. There is an `Action Name` and `Description` for each type of transactional email with the exception of Invoices.

## Testing

To test that the emails are sending correctly, you will require a gmail account (Google Apps accounts also work). You will then need to change the Sales Representative Contact Email in `System > Configuration > Store Email Addresses`. Changing it to your gmail account. When you place a test order, you will also need to use that same email address.
