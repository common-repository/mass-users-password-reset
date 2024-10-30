<?php
/**
 * Plugin Name: MASS Users Password Reset
 * Plugin URI: https://wordpress.org/plugins/mass-users-password-reset/
 * Description: MASS Users Password Reset is a WordPress Plugin that lets you resets the password of all users. It can group the users according to their role and resets password of that group.
 * Version: 1.9
 * Author: KrishaWeb
 * Author URI: https://www.krishaweb.com
 * Text Domain: mass-users-password-reset
 * Domain Path: /languages
 * License: GPL2
 *
 * @package         Mass_users_password_reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'includes/class-mupr.php';

define( 'MASS_USERS_PASSWORD_RESET_VERSION', '1.9' );
define( 'MASS_USERS_PASSWORD_RESET_REQUIRED_WP_VERSION', '4.3' );
define( 'MASS_USERS_PASSWORD_RESET', __FILE__ );
define( 'MASS_USERS_PASSWORD_RESET_BASENAME', plugin_basename( MASS_USERS_PASSWORD_RESET ) );
define( 'MASS_USERS_PASSWORD_RESET_PLUGIN_DIR', plugin_dir_path( MASS_USERS_PASSWORD_RESET ) );
define( 'MASS_USERS_PASSWORD_RESET_PLUGIN_URL', plugin_dir_url( MASS_USERS_PASSWORD_RESET ) );

/**
 * Activation hook
 */
function mass_users_password_reset_activate() {
	// Code here.
	if ( defined( 'MASS_USERS_PASSWORD_RESET_PRO' ) ) {
		wp_die( wp_sprintf( '<strong>Error: </strong>%s', __( 'Mass Users Password Reset Pro is active, please deactivate it and try again.', 'mass-users-password-reset' ) ) );
		exit;
	}
}
register_activation_hook( __FILE__, 'mass_users_password_reset_activate' );

/**
 * Deactivation hook
 */
function mass_users_password_reset_deactivate() {
	// Code here.
}
register_deactivation_hook( __FILE__, 'mass_users_password_reset_deactivate' );

/**
 * Init
 */
function mass_users_password_reset_init() {
	$mass_users_password_reset_obj = new Mass_users_password_reset();
	load_plugin_textdomain( 'mass-users-password-reset', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'mass_users_password_reset_init' );
