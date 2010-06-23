<?php
/*
Plugin Name: Login reCAPTCHA
Plugin URI: http://www.xrvel.com/336/programming/wordpress-login-recaptcha-plugin
Description: Add reCAPTCHA to login page. <a href="http://wordpress.org/extend/plugins/wp-recaptcha/" target="_blank">WP-reCAPTCHA</a> plugin must be installed first. Why use reCAPTCHA instead of other CAPTCHA? Because reCAPTCHA is a powerful CAPTCHA.
Author: Xrvel
Version: 0.1.2
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

global $xrvel_login_recaptcha;
global $xrvel_login_recaptcha_error;

function xrvel_login_recaptcha_install() {
}

// Display reCAPTCHA on login form
if (!function_exists('xrvel_login_recaptcha_form')) {
	function xrvel_login_recaptcha_form() {
		global $recaptcha_opt, $errors, $xrvel_login_recaptcha_error;
		$login_recaptcha_err = 0;
		if (isset($_GET['login_recaptcha_err'])) {
			$login_recaptcha_err = intval($_GET['login_recaptcha_err']);
		}
		$x_s = '';
		if (xrvel_login_recaptcha_recaptcha_installed()) {
			if ($login_recaptcha_err != 0) {
				if ($login_recaptcha_err == 1) {
					$xrvel_login_recaptcha_error = 'incorrect-captcha-sol';
				} else if (isset($_COOKIE['wp_login_recaptcha_error'])) {
					$xrvel_login_recaptcha_error = $_COOKIE['wp_login_recaptcha_error'];
				}
			}
			$use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");
			$x_s .= '<script type="text/javascript">var RecaptchaOptions = { theme : \'white\', tabindex : 30 };</script>';
			$x_s .= recaptcha_get_html($recaptcha_opt['pubkey'], $xrvel_login_recaptcha_error, $use_ssl);
		} else {
			$x_s .= '<p style="color:#FF0000;font-weight:900"><u>Latest</u> <a href="http://wordpress.org/extend/plugins/wp-recaptcha/" rel="nofollow">reCAPTCHA plugin</a> must be installed and activated first.</p>';
		}
		$x_s .= '<div style="padding:0.5em">&nbsp;</div>';
		echo $x_s;
	}
}

// reCAPTCHA process
if (!function_exists('xrvel_login_recaptcha_process')) {
	function xrvel_login_recaptcha_process() {
		global $recaptcha_opt, $errors, $xrvel_login_recaptcha_error;
		if (xrvel_login_recaptcha_recaptcha_installed() == false) {
			return true;
		}
		if ($_POST == array()) {
			return true;
		}

		if (!isset($_POST['recaptcha_response_field']) || $_POST['recaptcha_response_field'] == '') {
			header('Location: wp-login.php?login_recaptcha_err=1');
			exit();
		}

		$recaptcha_response = recaptcha_check_answer($recaptcha_opt['privkey'],
			$_SERVER['REMOTE_ADDR'],
			$_POST['recaptcha_challenge_field'],
			$_POST['recaptcha_response_field']);

		if (!$recaptcha_response->is_valid) {
			setcookie('wp_login_recaptcha_error', $recaptcha_response->error, time() + (3600 * 24 * 7));
			header('Location: wp-login.php?login_recaptcha_err=2');
			exit();
		}
		setcookie('wp_login_recaptcha_error', '', time() + (3600 * 24 * 7));
	}
}

//
// Check if wp-reCAPTCHA is already installed
//
if (!function_exists('xrvel_login_recaptcha_recaptcha_installed')) {
	function xrvel_login_recaptcha_recaptcha_installed() {
		return (defined('RECAPTCHA_WP_HASH_SALT') && function_exists('display_recaptcha'));
	}
}

$plugin = plugin_basename(__FILE__);

if (XRVEL_LOGIN_RECAPTCHA_ENABLED == true) {
	add_action('login_form','xrvel_login_recaptcha_form');
	add_action('wp_authenticate', 'xrvel_login_recaptcha_process', 1);
}
?>