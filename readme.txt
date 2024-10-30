=== MASS Users Password Reset ===
Plugin Name: MASS Users Password Reset
Plugin URI: https://wordpress.org/plugins/mass-users-password-reset
Author: krishaweb
Author URI: https://krishaweb.com
Contributors: manishamakhija, vijaybaria, krishaweb 
Tags: password reset, reset password, password, bulk reset password
Requires at least: 4.3
Tested up to: 6.2
Stable tag: 1.9
Copyright: (c) 2012-2023 KrishaWeb (info@krishaweb.com)
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

MASS Users Password Reset lets you easily reset the password of all users.

== Description ==

MASS Users Password Reset is a WordPress Plugin that lets you resets the password of all users. It can group the users according to their role and resets password of that group. It sends notification email to users about their new randomly generated password.

<a href="https://krishaweb.com/docs/mass-users-password-reset/?utm_source=readme&utm_medium=wporg&utm_campaign=MUPR" target="_blank">DOCUMENTATION</a>

Features
•   Easy installation
•   Role wise bifurcation of users 
•	Sends Notifications to selected role users
•   Multilingual Translation Enabled
•   Free support
•   You can update upto 100 passwords

> **Awesome plugin**
> "it’s very useful and great plugin to reset all the users password." ~[@ashkanram](https://wordpress.org/support/topic/awesome-plugin-3939/)

[Get Schedule Password Reset Add On](https://store.krishaweb.com/schedule-password-reset-mupr-add-on/?utm_source=readme&utm_medium=wporg&utm_campaign=MUPR)
• Pre-defined password reset schedule
• Unlimited password reset
• Role based schedule option

[Get Password Reset Log Add On](https://store.krishaweb.com/product/password-reset-log/?utm_source=readme&utm_medium=wporg&utm_campaign=MUPR)
• Maintain the password reset log reset by MUPR plugin
• Accurate user password reset log
• Available for MUPR and MUPR Pro

> **Does a really good job**
> "Seems to do a really good job of sending out password resets for multiple users. The pro version is definitely worth paying for the extra features." ~[@lightwavin](https://wordpress.org/support/topic/does-a-really-good-job-2/)

== Checkout the advanced features of Mass Users Password Reset Pro: ==

•   Individual user’s password reset option in users page.
•   Bulk action of Reset password for multiple selected users in users page.
•   Customized password reset mail template.
•   Apart from user role filter, you can filter users by using custom field filters of your choice.
•   The option of sending Reset Password Link to users instead of plain text password.
•   Compatible with very large number of users in the system.
•   You can update upto unlimited passwords.

<iframe width="560" height="315" src="https://www.youtube.com/embed/JI-mOB-dosM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

> **Very nice**
> "This is for the Pro version, which is a very nice plugin!" ~[@kostas45](https://wordpress.org/support/topic/very-nice-1679/)

<a href="https://codecanyon.net/item/mass-users-password-reset-pro/20809350" target="_blank">Download the Mass Users Password Reset Pro</a>

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Install the plugin via WordPress or download and upload the plugin to the /wp-content/plugins/
2. Activate the plugin through the `Plugins` menu in WordPress.
3. You can see the `Mass Users Password Reset` submenu inside the `Users` menu.

== Frequently Asked Questions ==

= What is the length of generated password? =

The length of randomly generated password is 8 characters, but by applying filter `mupr_password_length` you can customize it. For Example: Write this code in function file

`add_filter( 'mupr_password_length', 'callback_function' );
function callback_function() {
	return 6;
}`

= When notification mail will be send? =

When user will choose to generate new password, an email with the new random password will be sent to users.

= I have an idea for a great way to improve this plugin. =

Great! I’d love to hear from you at <a href="mailto:support@krishaweb.com">support@krishaweb.com</a>

= An email is handeled by the plugin? =

No, the plugin uses wp_mail function to send the email. If an email is not sent from your site, you can use any SMTP plugin.

== Screenshots ==

1. It shows the list of users and options.
2. It shows Reset password Email format

== Changelog ==

= 1.9 =
* Compatibility with latest version

= 1.8 =
* Added mupr add-on link

= 1.7 =
* Added help link

= 1.6 =
* UI improve

= 1.5 =
* Modified hooks

= 1.4 =
* Added hooks for developer

= 1.3 =
* UI update

= 1.2 =
* Include Administrator role in users list
 
= 1.1 =
* Made Translation ready
