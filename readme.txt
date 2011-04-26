=== WP Login reCAPTCHA ===
Contributors: Xrvel
Donate link: http://www.xrvel.com/donate/
Tags: login, captcha, recaptcha, admin, security
Requires at least: 2.9
Tested up to: 3.1.1
Stable tag: 0.1.4

Add reCAPTCHA to your WordPress login form

== Description ==

This plugin simply adds reCAPTCHA to your login form. By adding reCAPTCHA to your login form, you can prevent bot / script from trying to login to your WordPress website.

Requirement

* You must have reCAPTCHA account. It is free. You can login on http://www.google.com/recaptcha
* [WP-reCAPTCHA plugin](http://wordpress.org/extend/plugins/wp-recaptcha/) should be installed first.

== Installation ==

The installation process.

1. Upload  to the `/wp-content/plugins/` directory. Or directly upload from your Plugin management page.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Why should i use reCAPTCHA and not (put_other_captcha_here) ? =

Read the answer here :
http://www.google.com/recaptcha/learnmore

And here :
http://www.google.com/recaptcha/digitizing

= What if somehow i entered wrong reCAPTCHA setting (for example wrong Public Key, etc), i can not login to my Wordpress site =

Open your wp-login-recaptcha.php file. You can find it under your `/wp-content/plugins/wp-login-recaptcha` directory.
Change this line

`define('XRVEL_LOGIN_RECAPTCHA_ENABLED', true);`

And modify it into

`define('XRVEL_LOGIN_RECAPTCHA_ENABLED', false);`

Refresh your login form, and reCAPTCHA will dissapear on your login form.
You can login now and fix your reCAPTCHA setting from admin page.

== Changelog ==

= 0.1.4 =
* Bug fix for latest WP ReCAPTCHA plugin update.

= 0.1.3 =
* Add option to change reCAPTCHA color (theme).

= 0.1.2 =
* Change `XRVEL_LOGIN_RECAPTCHA_DISABLED` to `XRVEL_LOGIN_RECAPTCHA_ENABLED`
* Readme update.

= 0.1.1 =
* Small fix.

= 0.1 =
* First release.

== Upgrade Notice ==
* Not available yet.

== Screenshots ==
* Not available.