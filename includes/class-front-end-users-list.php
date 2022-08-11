<?php
/**
 * The file that defines the core plugin class
 *
 * @since 1.0.0
 *
 * @package front-end-users-list
 */

namespace FEUL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front_End_Users_List - Main Class of the part of the plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( '\FEUL\Front_End_Users_List' ) ) {

	/**
	 * Responsible for all the plugin functionalities
	 *
	 * @since 1.0.0
	 */
	class Front_End_Users_List {

		/**
		 * Inits the class.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			if ( \is_admin() ) {
				self::define_admin_hooks();
			}
			self::define_public_hooks();
		}

		/**
		 * Register all of the hooks related to the admin functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private static function define_admin_hooks() {
			add_action( 'admin_menu', array( '\FEUL\Admin\Front_End_Users_List_Admin', 'setup_plugin_menu' ) );
		}

		/**
		 * Register all of the hooks related to the front-end functionality
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private static function define_public_hooks() {
			add_action( 'wp_enqueue_scripts', array( '\FEUL\Short_Code\Front_End_Users_List_Short_Code', 'enqueue_styles' ), 20 );
			add_action( 'wp_enqueue_scripts', array( '\FEUL\Short_Code\Front_End_Users_List_Short_Code', 'enqueue_scripts' ) );
			add_action( 'wp_head', array( '\FEUL\Short_Code\Front_End_Users_List_Short_Code', 'register_shortcode_init' ) );
			add_action( 'wp_ajax_load_front_end_users_list', array( '\FEUL\Short_Code\Front_End_Users_List_Short_Code', 'load_front_end_users_list' ) );
		}
	}
}
