<?php
/*
Plugin Name: Login reCAPTCHA
Plugin URI: http://www.xrvel.com/336/programming/wordpress-login-recaptcha-plugin
Description: Add reCAPTCHA to login page.
Author: Xrvel
Version: 2.0.0
Author URI: http://www.xrvel.com/
*/

/*  Copyright 2014 xrvel.com

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

if (!function_exists('xrvel_login_recaptcha_add_pages')) {
	function xrvel_login_recaptcha_add_pages() {
		add_options_page('WP Login reCAPTCHA', 'WP Login reCAPTCHA', 'manage_options', 'xwplr', 'xrvel_login_recaptcha_page');
	}
}

// Display reCAPTCHA on login form
if (!function_exists('xrvel_login_recaptcha_form')) {
	function xrvel_login_recaptcha_form() {
		global $recaptcha;
		$ropt = get_option('recaptcha_options');
		$login_recaptcha_err = 0;

		if (isset($_GET['login_recaptcha_err'])) {
			$login_recaptcha_err = intval($_GET['login_recaptcha_err']);
		}

		$opt = get_option('xrvel_login_recaptcha_options');
		if (!isset($opt['theme'])) {
			$opt['theme'] = 'light';
		} else {
			if ('light' != $opt['theme'] && 'dark' != $opt['theme']) {
				$opt['theme'] = 'light';
			}
		}

		$x_s = '';
		if ('' != $opt['site_key'] && '' != $opt['secret_key']) {
			$x_s .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<div class="g-recaptcha" data-sitekey="'.htmlentities($opt['site_key']).'" data-theme="'.$opt['theme'].'"></div>';
			if (1 == $login_recaptcha_err) {
				$x_s .= '<div style="color:#F00;font-weight:900;padding:0.5em">Please pass reCAPTCHA verification</div>';
			}
		}
		echo $x_s;
	}
}

if (!function_exists('xrvel_login_recaptcha_get_ip')) {
	function xrvel_login_recaptcha_get_ip() {
		return $_SERVER['REMOTE_ADDR'];
	}
}

if (!function_exists('xrvel_login_recaptcha_get_post')) {
	function xrvel_login_recaptcha_get_post($var_name) {
		if (isset($_POST[$var_name])) {
			return $_POST[$var_name];
		} else {
			return '';
		}
	}
}

if (!function_exists('xrvel_login_recaptcha_page')) {
	function xrvel_login_recaptcha_page() {
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		if (isset($_POST['go'])) {
			update_option('xrvel_login_recaptcha_options', $_POST['xrvel_login_recaptcha_options']);
			_e('<div id="message" class="updated fade"><p>Options updated.</p></div>');
		}
		$opt = get_option('xrvel_login_recaptcha_options');
		echo '<div class="wrap">';
		?>
		<h2>WP Login reCAPTCHA</h2>
		<p><a href="https://www.google.com/recaptcha/admin" target="_blank">Get the reCAPTCHA keys here</a>.</p>
		<p>Both keys must be filled to enable WP reCAPTCHA in login page.</p>
		<form name="form1" method="post" action="">
		<input type="hidden" name="go" value="1" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Site Key (Public)</th>
				<td>
					<input type="text" name="xrvel_login_recaptcha_options[site_key]" size="40" value="<?php echo trim($opt['site_key']); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Secret Key (Private)</th>
				<td>
					<input type="text" name="xrvel_login_recaptcha_options[secret_key]" size="40" value="<?php echo trim($opt['secret_key']); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">reCAPTCHA theme</th>
				<td>
					<select name="xrvel_login_recaptcha_options[theme]">
					<option value="light">Light</option>
					<option value="dark"<?php if ('dark' == $opt['theme']) : ?> selected="selected"<?php endif; ?>>Dark</option>
					</select>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button-primary" title="Save Options" value="Save Options" /></p>
		<?php
		echo '</div>';
	}
}

// reCAPTCHA process
if (!function_exists('xrvel_login_recaptcha_process')) {
	function xrvel_login_recaptcha_process() {
		if (array() == $_POST) {
			return true;
		}

		$opt = get_option('xrvel_login_recaptcha_options');
		$parameters = array(
			'secret' => $opt['secret_key'],
			'response' => xrvel_login_recaptcha_get_post('g-recaptcha-response'),
			'remoteip' => xrvel_login_recaptcha_get_ip()
		);
		$url = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query($parameters);

		$response = xrvel_login_recaptcha_open_url($url);
		$json_response = json_decode($response, true);

		if (isset($json_response['success']) && true !== $json_response['success']) {
			header('Location: wp-login.php?login_recaptcha_err=1');
			exit();
		}
	}
}

// reCAPTCHA open url
if (!function_exists('xrvel_login_recaptcha_open_url')) {
	function xrvel_login_recaptcha_open_url($url) {
		if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			curl_close($ch);
		} else {
			$response = file_get_contents($url);
		}
		return trim($response);
	}
}

function xrvel_login_recaptcha_uninstall() {
	delete_option('xrvel_login_recaptcha_options');
}

$plugin = plugin_basename(__FILE__);

if (XRVEL_LOGIN_RECAPTCHA_ENABLED == true) {
	add_action('login_form','xrvel_login_recaptcha_form');
	add_action('wp_authenticate', 'xrvel_login_recaptcha_process', 1);
}

add_action('admin_menu', 'xrvel_login_recaptcha_add_pages');

register_uninstall_hook(ABSPATH.PLUGINDIR.'/wp-login-recaptcha/wp-login-recaptcha.php', 'xrvel_login_recaptcha_uninstall');
?>