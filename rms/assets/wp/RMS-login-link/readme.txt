=== Passwordless Login ===

Contributors: sareiodata, cozmoslabs
Donate link: http://www.cozmoslabs.com/wordpress-profile-builder/
Tags: passwordless login, passwordless, front-end login, login shortcode, custom login form, login without password, passwordless authentication
Requires at least: 3.9
Tested up to: 4.7.3
Stable tag: 1.0.5


Passwordless login form via a simple to use shortcode: [passwordless-login]


== Description ==

Passwordless Login is a modern way of loggin into your WordPress site without the use of a password.

Join the discussion here: http://www.cozmoslabs.com/31550-wordpress-passwordless-login/

This is how it works:

* Instead of asking users for a password when they try to log in to your website, we simply ask them for their username or email
* The plugin creates a temporary authorization token and saves it in a WordPress transient that expires after 10 minutes
* Then we send the user an email with a link and the token
* The user clicks the link and sends the authorization code to your server
* The plugin then checks if the code is valid and creates the log in WordPress cookie, successfuly authenticating the user.

You can use the shortcode [passwordless-login] in a page or widget.

NOTE:

Passwordless Authentication dose not replace the default login functionality in WordPress.


== Installation ==

1. Upload the passwordless-login folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use the shortcode available

== Frequently Asked Questions ==

= Is this secure? =

	Yes. The token is created using wp_hash and it's based on the user id, the current time and the salt in wp-config.php

= Couldn't anyone login if they have that link? =

	The token expires after 10 minutes and can only be used once. If people have access to that link it's supposed they have access to your email, in which case it's as safe as the default login, since they could reset their passwords.

= Isn't it more complicated they just entering a password? =

	Weak passwords are used every day by users. There are also people who use the same password across various services and websites. By using the Passwordless Login plugin your users will have one less password to worry about.

= But what if my users don't want to login every time via their email?  =

	You can extend the auth cookie expiration to something like 1 month or 3 months (this can be changed by using the wpa_change_link_expiration filter). Also, you can offer Passwordless Login as an alternative login system and enforce stronger passwords on registration using <a href="http://wordpress.org/plugins/profile-builder/">Profile Builder plugin.</a>

= I can't find a question similar to my issue; Where can I find support? =

	For more information please visit http://www.cozmoslabs.com or via the support tab.


== Screenshots ==

1. Front End Passwordless Login Form
2. Received Email with the token link
3. Backend Info page for the plugin


== Changelog ==

= 1.0.5 =
* Fix: Fixed an issue with the Email Content Type. Now we are using the wp_mail_content_type filter to set this.
* Plugin security improvements.

= 1.0.4 =
* Fix: Remove email 'from' filter. Should use wp_mail_from filter.
* Added support for HTML inside the e-mail that gets sent.
* Added the wpa_change_link_expiration filter to be able to change the lifespan of the token.
* Added the wpa_change_form_label to be able to change the label for the login form. The label also changes automatically now based on the value of the Allow Users to * Login With option set in Profile Builder -> Manage Fields.
* Fix: Generating the url using add_query_args() function.

= 1.0.3 =
Fix: Minor readme change

= 1.0.2 =
Fix: Added require_once for the PasswordHash class

= 1.0.1 =
* Security fix: tokens are now hashed in the database.
* Security fix: sanitized the input fields data.
* Fix: no longer using transients. Now using user_meta with an expiration meta since transients are not to be trusted.
* Change: removed a br tag.

= 1.0 =
Initial version. Added a passwordless login form as a shortcode.