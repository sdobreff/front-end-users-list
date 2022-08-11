<?php
/**
 * The FE (shortcode) functionality of the plugin.
 *
 * @since 1.0.0
 *
 * @package front-end-users-list
 * @subpackage front-end-users-list/front-end
 */

namespace FEUL\Short_Code;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front_End_Users_List_Short_Code - Class for the front end part of the plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( '\FEUL\Short_Code\Front_End_Users_List_Short_Code' ) ) {

	/**
	 * Responsible for short code parsing
	 *
	 * @since 1.0.0
	 */
	class Front_End_Users_List_Short_Code {

		/**
		 * The main query order
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $order = 'asc';

		/**
		 * The main query order by
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $order_by = 'user_name';

		/**
		 * The users total pages option
		 *
		 * @var integer
		 *
		 * @since 1.0.0
		 */
		public static $total_pages = 0;

		/**
		 * The users page option
		 *
		 * @var integer
		 *
		 * @since 1.0.0
		 */
		public static $page = 1;

		/**
		 * The users per page option
		 *
		 * @var integer
		 *
		 * @since 1.0.0
		 */
		public static $per_page = 10;

		/**
		 * The main query fields which can be ordered
		 *
		 * @var array
		 *
		 * @since 1.0.0
		 */
		public static $columns_order = array( 'user_name', 'display_name' );

		/**
		 * Users elements
		 *
		 * @var array
		 *
		 * @since 1.0.0
		 */
		public static $user_elements = array();

		/**
		 * All user elements
		 *
		 * @var integer
		 *
		 * @since 1.0.0
		 */
		public static $all_user_elements = 0;

		/**
		 * Users role for sorting
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $role = '';

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_styles() {

			\wp_enqueue_style(
				'font-awesome-feul',
				plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
				array(),
				FE_USERS_LIST_VERSION,
				'all'
			);

			\wp_enqueue_style(
				FE_USERS_LIST_PLUGIN_NAME,
				plugin_dir_url( __FILE__ ) . 'css/fe-users-list.css',
				array(),
				FE_USERS_LIST_VERSION,
				'all'
			);
		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_scripts() {

			\wp_enqueue_script(
				FE_USERS_LIST_PLUGIN_NAME,
				plugin_dir_url( __FILE__ ) . 'js/fe-users-list.js',
				array( 'jquery' ),
				FE_USERS_LIST_VERSION,
				false
			);
			\wp_localize_script(
				FE_USERS_LIST_PLUGIN_NAME,
				'front_end_users_list',
				array(
					'ajaxurl' => \admin_url( 'admin-ajax.php' ),
					'nonce'   => \wp_create_nonce( FE_USERS_LIST_PLUGIN_NAME ),
				)
			);
		}

		/**
		 * Registers the short code of the plugin
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function register_shortcode_init() {
			\add_shortcode(
				FE_USERS_LIST_SHORT_CODE,
				array( __CLASS__, 'users_list_shortcode' )
			);
		}

		/**
		 * Outputs users table
		 *
		 * @return string The shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function users_list_shortcode() {
			return self::users_list_page_content();
		}

		/**
		 * Outputs users list page HTML.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function users_list_page_content() {

			if ( ! \current_user_can( 'list_users' ) ) {
				return;
			}

			self::users_list_query();

			?>
			<div class="feul-wrapper users-list">

				<div class="tablenav top">
					<div class="users-roles-div">
						<ul class="users-roles-nav">
							<?php echo self::users_role_filter(); // phpcs:ignore ?>
						</ul>
					</div>
					<div class="users-list-pagination">
						<?php echo self::users_lists_pagination(); // phpcs:ignore ?>
					</div>
				</div>

				<div>
					<table class="users-list-table">
						<thead>
							<tr>
								<th scope="col" id="username" class="column-username column-primary <?php echo \esc_attr( self::add_sortable_classes( 'user_name' ) ); ?>">
									<a href="#" data-sort-order="<?php echo \esc_attr( self::prepare_order_direction( 'user_name' ) ); ?>" data-sort-orderby="user_name">
										<span><?php \esc_html_e( 'Username', 'front-end-users-list' ); ?> <i class="fa fa-sort"></i>
									</a>
								</th>
								<th scope="col" id="name" class="column-name <?php echo \esc_attr( self::add_sortable_classes( 'display_name' ) ); ?>">
									<a href="#" data-sort-order="<?php echo \esc_attr( self::prepare_order_direction( 'display_name' ) ); ?>" data-sort-orderby="display_name">
										<span><?php \esc_html_e( 'Name', 'front-end-users-list' ); ?></span> <i class="fa fa-sort"></i>
								</th>
								<th scope="col" id="email" class="column-email"><?php \esc_html_e( 'Email', 'front-end-users-list' ); ?></th>
								<th scope="col" id="role" class="column-role"><?php \esc_html_e( 'Role', 'front-end-users-list' ); ?></th>
							</tr>
						</thead>

						<template id="user_table_row" style="display: none;">
							<tr>
								<td><strong><a id="user_name_link"></a></strong></td>
								<td id="display_name"></td>
								<td id="user_email"></td>
								<td id="user_roles"></td>
							</tr>
						</template>

						<template id="user_table_noresults" style="display: none;">
							<tr class="no-items">
								<td class="colspanchange" colspan="4"><?php \esc_html_e( 'No Users found', 'front-end-users-list' ); ?></td>
							</tr>
						</template>

						<tbody id="list_table_body">

							<?php
							if ( self::$user_elements ) {
								foreach ( self::$user_elements as $user ) {
									?>
							<tr>
								<td><strong><a href="<?php echo \esc_url( \get_edit_user_link( $user->ID ) ); ?>"><?php echo \esc_html( $user->user_login ); ?></a></strong></td>
								<td><?php echo \esc_html( $user->display_name ); ?></td>
								<td><?php echo \esc_html( $user->user_email ); ?></td>
								<td><?php echo \esc_html( self::format_roles( $user->roles ) ); ?></td>
							</tr>
									<?php
								}
							} else {
								?>
							<tr class="no-items">
								<td class="colspanchange" colspan="4"><?php esc_html_e( 'No Users found', 'front-end-users-list' ); ?></td>
							</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>            
			</div>
			<?php
		}

		/**
		 * Outputs the users list in a JSON format.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public static function load_front_end_users_list() {

			if ( ! \current_user_can( 'list_users' ) ) {
				return;
			}

			$users_data = array();

			self::users_list_query();

			if ( self::$user_elements ) {
				foreach ( self::$user_elements as $user ) {
					$users_data[] = array(
						'user_name'    => $user->user_login,
						'user_link'    => \get_edit_user_link( $user->ID ),
						'display_name' => $user->display_name,
						'user_email'   => $user->user_email,
						'user_roles'   => self::format_roles( $user->roles ),
					);
				}
			}

			$result['user_elements']         = $users_data;
			$result['all_user_elements']     = self::$all_user_elements;
			$result['total_pages']           = self::$total_pages;
			$result['total_pages_formatted'] = self::$total_pages;

			\wp_send_json( $result );
			die();
		}

		/**
		 * Prepare the roles
		 *
		 * @return array
		 *
		 * @since 1.0.0
		 */
		public static function prepare_users_list_roles() {

			$roles         = array();
			$all_roles     = \count_users();
			$wp_roles      = \wp_roles();
			$wp_roles_name = $wp_roles->role_names;

			foreach ( $wp_roles_name as $role_name => $role_title ) {
				if ( array_key_exists( $role_name, $all_roles['avail_roles'] ) && (int) $all_roles['avail_roles'][ $role_name ] ) {

					$roles[ $role_name ] = array(
						'name'  => $role_name,
						'title' => \translate_user_role( $role_title ),
						'count' => (int) $all_roles['avail_roles'][ $role_name ],
					);
				}
			}
			return $roles;
		}

		/**
		 * Adds class names for column header depending on current query sorting options.
		 *
		 * @param string $order_by - Order by field name.
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function add_sortable_classes( $order_by ) {

			if ( $order_by === self::$order_by ) {
				$class[] = 'sorted';

				if ( 'desc' === self::$order ) {
					$class[] = 'desc';
				} else {
					$class[] = 'asc';
				}
			} else {
				$class[] = 'sortable desc';
			}

			return implode( ' ', $class );
		}

		/**
		 * ASC to DESC, DESC to ASC
		 *
		 * @param string $order_by - The order by string.
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function prepare_order_direction( $order_by ) {

			if ( $order_by === self::$order_by ) {
				if ( 'desc' === self::$order ) {
					$order = 'asc';
				} else {
					$order = 'desc';
				}
			} else {
				$order = 'asc';
			}

			return $order;
		}

		/**
		 * Generates select depending on current query total page number.
		 *
		 * @return null|string
		 *
		 * @since 1.0.0
		 */
		public static function prepare_pagination_select_options() {

			if ( ! self::$total_pages ) {
				return null;
			}

			$select = '';

			for ( $i = 1; $i <= self::$total_pages; $i++ ) {

				if ( $i === self::$page ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$select .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
			}

			return $select;
		}

		/**
		 * Return role link with current sorting options.
		 *
		 * @param string $role - Role ID.
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function role_link( $role = '' ) {

			$query_string = '';
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$query_string = \remove_query_arg( 'paged', \sanitize_text_field( \wp_unslash( $_SERVER['QUERY_STRING'] ) ) );
			}

			if ( ! empty( $role ) ) {
				$query_string = \add_query_arg( 'role', $role, $query_string );
			} else {
				$query_string = \remove_query_arg( 'role', $query_string );
			}

			return rtrim( \get_permalink(), '/' ) . $query_string;
		}

		/**
		 * Generates roles filter navigation.
		 *
		 * @return null|string
		 *
		 * @since 1.0.0
		 */
		public static function users_role_filter() {

			$roles = self::prepare_users_list_roles();

			if ( ! $roles ) {
				return null;
			}

			$current_role = self::request( 'role' );

			$result = '<li><a href="' . \esc_url( self::role_link() ) . '" class="' . ( ! $current_role ? 'current' : '' ) . '" data-filter-role="">' . \esc_attr__( 'All', 'front-end-users-list' ) . ' <span class="count">(' . self::$all_user_elements . ')</span></a> |</li> ';

			foreach ( $roles as $role ) {

				if ( $role['name'] === $current_role ) {
					$current_class = 'current';
				} else {
					$current_class = '';
				}
				$result .= '<li><a href="' . \esc_url( self::role_link( $role['name'] ) ) . '" class="' . $current_class . '" data-filter-role="' . $role['name'] . '">' . $role['title'] . ' <span class="count">(' . $role['count'] . ')</span></a> <span class="separator">|</span></li> ';
			}

			return $result;
		}

		/**
		 * Generates pagination content.
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public static function users_lists_pagination() {

			if ( ! self::$all_user_elements ) {
				return null;
			}

			$query_string = '';
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$query_string = \remove_query_arg( 'paged', \sanitize_text_field( \wp_unslash( $_SERVER['QUERY_STRING'] ) ) );
			}

			$base_url = rtrim( \get_permalink(), '/' );

			if ( 1 === (int) self::$total_pages ) {
				$paginator_class = 'one-page';
			} else {
				$paginator_class = '';
			}

			$result = '<div class="tablenav-pages ' . $paginator_class . '">';

			$result .= '<span class="pagination-links">';

			// Disable & Enable previous page link.
			if ( self::$page <= 1 ) {
				$disabled = 'disabled="disabled"';
			} else {
				$disabled = '';
			}

			// Previous Page.
			$result .= sprintf(
				'<a class="prev-page" %s href="%s"><span aria-hidden="true">%s</span></a> ',
				$disabled,
				\esc_url( $base_url . \add_query_arg( 'paged', self::$page - 1, $query_string ) ),
				'<i class="fa fa-chevron-left"></i>'
			);

			$result .= '<span class="paging-input">';

			// Current page selector.
			$result .= '<select name="paged" class="current-page-selector">' . self::prepare_pagination_select_options() . '</select>';

			// Total pages label.
			$html_total_pages = sprintf( '<span class="total-pages">%s</span>', \number_format_i18n( self::$total_pages ) );
			$result          .= sprintf(
				/* translators: number of pages */
				\esc_html__( '%1$s of %2$s pages', 'front-end-users-list' ),
				'<span class="tablenav-paging-text">',
				$html_total_pages
			) . '</span></span> ';

			// Disable/enable next page link.
			if ( self::$page + 1 > self::$total_pages ) {
				$disabled = 'disabled="disabled"';
			} else {
				$disabled = '';
			}

			$result .= sprintf(
				'<a class="next-page" %s href="%s"><span aria-hidden="true">%s</span></a> ',
				$disabled,
				\esc_url( $base_url . \add_query_arg( 'paged', self::$page + 1, $query_string ) ),
				' <i class="fa fa-chevron-right"></i>'
			);

			$result .= '</div>';

			return $result;
		}

		/**
		 * Formats user roles array for displaying on front-end.
		 *
		 * @param array $role_array - Array of user roles.
		 *
		 * @return null|string
		 *
		 * @since 1.0.0
		 */
		public static function format_roles( $role_array ) {

			$roles = self::prepare_users_list_roles();

			if ( ! $role_array || ! is_array( $role_array ) ) {
				return null;
			}
			$roles_array = array();

			foreach ( $role_array as $role_name ) {
				$roles_array[] = $roles[ $role_name ]['title'];
			}

			if ( $roles_array ) {
				return implode( ', ', $roles_array );
			} else {
				return \esc_html__( 'No Roles', 'front-end-users-list' );
			}
		}

		/**
		 * Checks if value exists in the request.
		 *
		 * @param string $key - The key to check for.
		 *
		 * @return mixed|null
		 *
		 * @since 1.0.0
		 */
		private static function request( $key ) {
			if ( ! isset( $_POST['nonce'] ) || ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), FE_USERS_LIST_PLUGIN_NAME ) ) {
				return null;
			}

			if ( isset( $_REQUEST[ $key ] ) ) {
				return \sanitize_text_field( \wp_unslash( $_REQUEST[ $key ] ) );
			} else {
				return null;
			}
		}

		/**
		 * Create users query.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		private static function users_list_query() {

			// The Role Query.
			$role = self::request( 'role' );
			if ( ! $role ) {
				$role__in = array();
			} else {
				$role__in = (array) $role;
			}
			self::$role = $role;

			// The Order Type Query.
			$order = self::request( 'order' );
			if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
				$order = self::$order;
			}
			self::$order = $order;

			// The Order Query.
			$order_by = self::request( 'orderby' );
			if ( ! in_array( $order_by, self::$columns_order, true ) ) {
				$order_by = self::$order_by;
			}
			self::$order_by = $order_by;

			// Retrieves page number query argument.
			$current_page = self::request( 'paged' );
			if ( ! (int) $current_page ) {
				$current_page = 1;
			}
			self::$page = (int) $current_page;

			// Get the page offset.
			$offset = self::$per_page * $current_page - self::$per_page;

			/**
			 * Prepare the WP_User_Query
			 */
			$args  = array(
				'count_total' => true,
				'orderby'     => $order_by,
				'order'       => $order,
				'offset'      => $offset,
				'number'      => self::$per_page,
				'role__in'    => $role__in,
			);
			$query = new \WP_User_Query( $args );

			self::$user_elements     = $query->get_results();
			self::$all_user_elements = $query->get_total();
			self::$total_pages       = ceil( self::$all_user_elements / self::$per_page );
		}
	}
}
