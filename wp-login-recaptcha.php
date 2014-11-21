<?php
/*
Plugin Name: Login reCAPTCHA
Plugin URI: http://www.xrvel.com/336/programming/wordpress-login-recaptcha-plugin
Description: Add reCAPTCHA to login page. <a href="https://wordpress.org/plugins/wp-recaptcha/" target="_blank">WP-reCAPTCHA</a> plugin must be installed first. Why use reCAPTCHA instead of other CAPTCHA? Because reCAPTCHA is a powerful CAPTCHA.
Author: Xrvel
Version: 1.0.0
Author URI: http://www.xrvel.com/
*/

/*  Copyright 2010 xrvel.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Enable this plugin?
define('XRVEL_LOGIN_RECAPTCHA_ENABLED', true);

if (!class_exists('ReCAPTCHAPlugin')) {
	require_once(dirname(__FILE__).'/../wp-recaptcha/recaptcha.php');
}

if (!isset($recaptcha)) {
	$recaptcha = new ReCAPTCHAPlugin('recaptcha_options');
}

function xrvel_login_recaptcha_install() {
}

// Display reCAPTCHA on login form
if (!function_exists('xrvel_login_recaptcha_form')) {
	function xrvel_login_recaptcha_form() {
		global $errors, $recaptcha;
		$ropt = get_option('recaptcha_options');
		$login_recaptcha_err = 0;
		if (isset($_GET['login_recaptcha_err'])) {
			$login_recaptcha_err = intval($_GET['login_recaptcha_err']);
		}
		$x_s = '';
		if (xrvel_login_recaptcha_recaptcha_installed()) {
			$x_s .= $recaptcha->get_recaptcha_html();
		} else {
			$x_s .= '<p style="color:#FF0000;font-weight:900"><u>Latest</u> <a href="https://wordpress.org/plugins/wp-recaptcha/" rel="external nofollow">reCAPTCHA plugin</a> must be installed and activated first.</p>';
		}
		$x_s .= '<div style="padding:0.5em">&nbsp;</div>';
		echo $x_s;
	}
}

// reCAPTCHA process
if (!function_exists('xrvel_login_recaptcha_process')) {
	function xrvel_login_recaptcha_process() {
		global $errors, $recaptcha;

		if (xrvel_login_recaptcha_recaptcha_installed() == false) {
			return true;
		}
		if ($_POST == array()) {
			return true;
		}
		$error = new WP_Error();
		$recaptcha_response = $recaptcha->validate_recaptcha_response($error);

		if (count($recaptcha_response->errors) > 0) {
			header('Location: wp-login.php?login_recaptcha_err=1');
			exit();
		}
	}
}

//
// Check if wp-reCAPTCHA is already installed
//
if (!function_exists('xrvel_login_recaptcha_recaptcha_installed')) {
	function xrvel_login_recaptcha_recaptcha_installed() {
		return (class_exists('ReCAPTCHAPlugin'));
	}
}

function xrvel_login_recaptcha_uninstall() {
	delete_option('xrvel_login_recaptcha_option');
}

$plugin = plugin_basename(__FILE__);

if (XRVEL_LOGIN_RECAPTCHA_ENABLED == true) {
	add_action('login_form','xrvel_login_recaptcha_form');
	add_action('wp_authenticate', 'xrvel_login_recaptcha_process', 1);
}

register_uninstall_hook(ABSPATH.PLUGINDIR.'/wp-login-recaptcha/wp-login-recaptcha.php', 'xrvel_login_recaptcha_uninstall');
?>