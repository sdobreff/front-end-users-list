<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since 1.0.0
 *
 * @package front-end-users-list
 * @subpackage front-end-users-list/admin
 */

namespace FEUL\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front_End_Users_List_Admin - Class for the admin part of the plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( '\FEUL\Admin\Front_End_Users_List_Admin' ) ) {

	/**
	 * Responsible for the admin part of the plugin.
	 *
	 * @since 1.0.0
	 */
	class Front_End_Users_List_Admin {

		/**
		 * Function to add the plugin under users menu
		 *
		 * @since 1.0.0
		 */
		public static function setup_plugin_menu() {

			// Add the menu to the Users set of menu items.
			\add_users_page(
				'Front End Users List',
				'Front End Users List',
				'manage_options',
				FE_USERS_LIST_PLUGIN_NAME,
				array( __CLASS__, 'plugin_admin_info' )
			);
		}

		/**
		 * Redirects the admin to the plugin info page on plugin activate
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function front_end_users_list_plugin_activation_redirect() {
			if ( \get_option( FE_USERS_LIST_PLUGIN_NAME . '_redirect', false ) ) {
				\delete_option( FE_USERS_LIST_PLUGIN_NAME . '_redirect' );
				// Redirect to users list page.
				\wp_safe_redirect( \menu_page_url( FE_USERS_LIST_PLUGIN_NAME ) );
				exit;
			}
		}

		/**
		 * Shows the plugin info to the admin
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function plugin_admin_info() {
			?>
			<div class="wrapper users-list-admin">

				<h1><?php \esc_html_e( 'Front End Users List', 'front-end-users-list' ); ?></h1>
				<div class="home-content">

					<div class='users-list-way'>
						<ul class="users-list how">
							<li>
							<?php
							printf(
								/* translators: %s: Shortcode string. */
								\esc_html__(
									'You can list the users in frontend by putting [%s] shortcode.',
									'front-end-users-list'
								),
								'<b>' . \esc_html( FE_USERS_LIST_SHORT_CODE ) . '</b>'
							);
							?>
								</i></li>
						</ul>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
