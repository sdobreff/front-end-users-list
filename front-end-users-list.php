<?php
/**
 * The plugin main file
 *
 * @since             1.0.0
 * @package           front-end-users-list
 *
 * @wordpress-plugin
 * Plugin Name:       Front End Users List
 * Description:       Plugin that creates a custom HTML table with a list of users that can be filtered
 * Version:           1.0.0
 * Author:            Stoil Dobreff
 * Text Domain:       front-end-users-list
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'FE_USERS_LIST_VERSION' ) ) {
	define( 'FE_USERS_LIST_VERSION', '1.0.0' );
	define( 'FE_USERS_LIST_PATH', \plugin_dir_path( __FILE__ ) );
	define( 'FE_USERS_LIST_PLUGIN_NAME', 'front-end-users-list' );
	define( 'FE_USERS_LIST_SHORT_CODE', 'fe_users_list' );
}

if ( file_exists( FE_USERS_LIST_PATH . 'vendor/autoload.php' ) ) {
	require_once FE_USERS_LIST_PATH . 'vendor/autoload.php';
} else {
	throw new \Exception( 'Required autoloader is not presented' );
}

/**
 * The code that runs during plugin activation.
 */
function activate_users_list() {
	\add_option( FE_USERS_LIST_PLUGIN_NAME . '_redirect', true );
}

\register_activation_hook( __FILE__, 'activate_users_list' );
\add_action( 'admin_init', array( '\FEUL\Admin\Front_End_Users_List_Admin', 'front_end_users_list_plugin_activation_redirect' ) );

/**
 * Inits the main plugin class
 */
\FEUL\Front_End_Users_List::init();
