<?php
// If check class exists 'WP_List_Table'
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
// If check class not exists 'Mass_users_password_reset'
if ( ! class_exists( 'Mass_users_password_reset' ) ) {
	class Mass_users_password_reset extends WP_List_Table {

		/**
		 * Show user per page
		 *
		 * @param $user_per_page int
		 */
		public $user_per_page;

		/**
		 * Exclude user
		 *
		 * @param $exclude array
		 */
		public $exclude = array();

		/**
		 * Set default steps
		 *
		 * @var $steps
		 */
		public $steps = array(
			array(
				'label'    => 'User List',
				'key'      => 'user-list',
				'icon'     => 'dashicons-admin-users',
				'template' => MASS_USERS_PASSWORD_RESET_PLUGIN_DIR . 'admin/template/user-list.php',
			),
			array(
				'label'    => 'Advanced Features',
				'key'      => 'mupr-pro',
				'icon'     => 'dashicons-unlock',
				'template' => MASS_USERS_PASSWORD_RESET_PLUGIN_DIR . 'admin/template/mupr-pro.php',
			),
		);

		// Class construct
		function __construct() {
			// Set per page
			$this->user_per_page = get_option( 'posts_per_page', 10 );
			// Store currrent user ID
			$this->exclude[] = get_current_user_id();
			// include in admin menu
			add_action( 'admin_menu', array( $this, 'mass_users_password_reset_menu' ) );
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'mass_users_password_reset_options' ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'include_scripts' ) );
				add_action( 'admin_init', array( $this, 'mupr_remove_admin_notice' ) );
			}
			add_action( 'wp_ajax_send_reset_password_mail_action', array( $this, 'send_reset_password_mail_action' ) );
			add_action( 'wp_ajax_nopriv_send_reset_password_mail_action', array( $this, 'send_reset_password_mail_action' ) );
			add_action( 'wp_ajax_mupr_plugin_data', array( $this, 'mupr_plugin_data' ) );
			add_action( 'wp_ajax_nopriv_mupr_plugin_data', array( $this, 'mupr_plugin_data' ) );
			// Add upgrade link on plugin action.
			add_filter( 'plugin_action_links_' . basename( dirname( MASS_USERS_PASSWORD_RESET ) ) . '/' . basename( MASS_USERS_PASSWORD_RESET ), array( $this, 'mass_users_password_reset_add_upgrade_link' ) );
			// Plugin meta row.
			add_filter( 'plugin_row_meta', array( $this, 'mass_users_password_reset_plugin_row_meta' ), 10, 4 );
		}

		function mass_users_password_reset_add_upgrade_link( $links ) {
			$links[] = wp_sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'https://codecanyon.net/item/mass-users-password-reset-pro/20809350' ), __( 'Upgrade', 'mass-users-password-reset' ) );
			return $links;
		}

		/**
		 * Remove all admin notices.
		 */
		function mupr_remove_admin_notice() {
			remove_all_actions( 'admin_notices' );
		}

		/**
		 * Add sub menu
		 */
		function mass_users_password_reset_menu() {
			add_submenu_page( 'users.php', 'Mass Users Password Reset', 'Mass Users Password Reset ', 'activate_plugins', 'mass_users_password_reset_options', array( $this, 'users_list_display' ) );
		}

		/**
		 * Enqueue scripts.
		 */
		function include_scripts() {
			// CSS
			wp_enqueue_style( 'google-montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700' );
			wp_enqueue_style( 'bootstrap-select', MASS_USERS_PASSWORD_RESET_PLUGIN_URL . 'assets/css/bootstrap-select.css' );
			wp_enqueue_style( 'mupr', MASS_USERS_PASSWORD_RESET_PLUGIN_URL . 'assets/css/mupr.css' );
			// JS
			// enqueue these scripts and styles before admin_head
			wp_enqueue_script( 'jquery-ui-dialog' );
			// jquery and jquery-ui should be dependencies, didn't check though.
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			// Plugin JS
			wp_enqueue_script( 'mupr', MASS_USERS_PASSWORD_RESET_PLUGIN_URL . 'assets/js/mupr.js', array(), false, true );
			wp_enqueue_script( 'bootstrap-select', MASS_USERS_PASSWORD_RESET_PLUGIN_URL . 'assets/js/bootstrap-select.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'jquery-dropdown', MASS_USERS_PASSWORD_RESET_PLUGIN_URL . 'assets/js/dropdown.js', array( 'jquery' ), false, true );
			wp_localize_script(
				'mupr',
				'MUPR_FREE',
				array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'nonce_error'  => __( 'Something went wrong please try again', 'mass-users-password-reset' ),
					'reset_nonce'  => wp_create_nonce( 'mupr_free_reset_' . get_current_user_id() ),
					'per_page'     => $this->user_per_page,
					'plugin_nonce' => wp_create_nonce( 'mupr_get_details_' . get_current_user_id() ),
				)
			);
		}

		/**
		 * Display admin page
		 */
		function users_list_display() {
			require_once plugin_dir_path( __FILE__ ) . '../admin/template/mupr-admin.php';
		}

		/**
		 * User role filter
		 */
		function mupr_user_role_filter() {
			$filter       = '';
			$current_role = isset( $_REQUEST['role_filter'] ) ? $_REQUEST['role_filter'] : '';
			foreach ( get_editable_roles() as $role_name => $role_info ) {
				$filter .= '<option value="' . $role_name . '" ' . selected( $role_name, $current_role, false ) . '>' . $role_info['name'] . '</option>';
			}
			return $filter;
		}

		/**
		 * Show user list
		 *
		 * @return array all user's
		 */
		function mupr_user_lists() {
			$data = array();
			// Per page
			$users_per_page = $this->user_per_page;
			$paged          = ( isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1 );

			$all_users_query = array(
				'exclude' => $this->exclude,
			);

			// User query
			$user_query = array(
				'exclude' => $this->exclude,
				'number'  => $users_per_page,
				'offset'  => ( $paged - 1 ) * $users_per_page,
			);
			// If check user role filter exists OR not
			if ( isset( $_REQUEST['role_filter'] ) ) {
				$role = sanitize_text_field( $_REQUEST['role_filter'] );
				if ( $role && $role != 'all' ) {
					$user_query['role'] = array(
						'role' => $role,
					);
					// Pagination
					$all_users_query['role'] = array(
						'role' => $role,
					);
				}
			}
			$data['list']       = new WP_User_Query( $user_query );
			$data['pagination'] = new WP_User_Query( $all_users_query );
			return $data;
		}

		/**
		 * Override pagination function
		 *
		 * @param string $which  The which
		 *
		 * @return pagination
		 */
		function pagination( $which ) {
			if ( empty( $this->_pagination_args ) ) {
				return;
			}

			$total_items     = $this->_pagination_args['total_items'];
			$total_pages     = $this->_pagination_args['total_pages'];
			$infinite_scroll = false;
			if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
				$infinite_scroll = $this->_pagination_args['infinite_scroll'];
			}

			if ( 'top' === $which && $total_pages > 1 ) {
				$this->screen->render_screen_reader_content( 'heading_pagination' );
			}

			$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

			$current              = $this->get_pagenum();
			$removable_query_args = wp_removable_query_args();
			$current_protocol     = ( stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://' );
			$current_url          = set_url_scheme( $current_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$str_pos              = strpos( $current_url, 'admin-ajax.php' );

			$current_url = remove_query_arg( $removable_query_args, $current_url );

			$page_links = array();

			$total_pages_before = '<span class="paging-input">';
			$total_pages_after  = '</span></span>';

			$disable_first = $disable_last = $disable_prev = $disable_next = false;

			if ( $current == 1 ) {
				$disable_first = true;
				$disable_prev  = true;
			}
			if ( $current == 2 ) {
				$disable_first = true;
			}
			if ( $current == $total_pages ) {
				$disable_last = true;
				$disable_next = true;
			}
			if ( $current == $total_pages - 1 ) {
				$disable_last = true;
			}

			if ( $disable_first ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( remove_query_arg( 'paged', $current_url ) ),
					__( 'First page' ),
					'&laquo;'
				);
			}

			if ( $disable_prev ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
					__( 'Previous page' ),
					'&lsaquo;'
				);
			}

			if ( 'bottom' === $which ) {
				$html_current_page  = $current;
				$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
			} else {
				$html_current_page = sprintf(
					"%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
					'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
					$current,
					strlen( $total_pages )
				);
			}
			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

			if ( $disable_next ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
					__( 'Next page' ),
					'&rsaquo;'
				);
			}

			if ( $disable_last ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
					__( 'Last page' ),
					'&raquo;'
				);
			}

			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) ) {
				$pagination_links_class = ' hide-if-js';
			}
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

			if ( $total_pages ) {
				$page_class = $total_pages < 2 ? ' one-page' : '';
			} else {
				$page_class = ' no-pages';
			}
			$this->_pagination = "<div class='tablenav-pages mupr-pagination{$page_class}'>$output</div>";

			return $this->_pagination;
		}

		/**
		 * Send reset password mail
		 */
		function send_reset_password_mail_action() {
			// Check ajax nonce
			check_ajax_referer( 'mupr_free_reset_' . get_current_user_id(), 'nonce' );
			// If check role exists OR not
			if ( isset( $_POST['role'] ) ) {
				$role   = sanitize_text_field( $_POST['role'] );
				$offset = isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : '';
				$number = $this->user_per_page;
				// Default query
				$user_query = array(
					'exclude' => $this->exclude,
					'offset'  => $offset,
					'number'  => $number,
				);
				// If check role empty OR not
				if ( $role != '' && $role != 'all' ) {
					$user_query['role'] = array( 'role' => $role );
				}
				// Get all users
				$all_users_list = new WP_User_Query( $user_query );
				$user_ids       = $all_users_list->get_results();
				$count_users    = count( $user_ids );
				$get_mupr_users = get_option( 'mupr_users' ) ? get_option( 'mupr_users' ) : 0;
				if ( $get_mupr_users < 100 ) {
					$mail_not_send = false;
					if ( ! empty( $user_ids ) ) {
						$sent_count = 0;
						foreach ( $user_ids as $user_id ) {
							$result = $this->send_email_format( $user_id->ID );
							if ( $result == 1 ) {
								$sent_count++;
								update_option( 'mupr_users', $count_users + $get_mupr_users );
							} else {
								$mail_not_send = true;
							}
						}
						$users_count_till_now = $offset + $sent_count;
						$successful_msg       = sprintf( _n( '%s user password has been reset successfully', '%s users password have been reset successfully', $users_count_till_now, 'mass-users-password-reset-pro' ), $users_count_till_now );

						$no_mails_send_msg = __( 'There is an error in sending mail, Please check your server configuration.', 'mass-users-password-reset-pro' );

						if ( $result == 1 ) {
							$message = array(
								'result'  => 1,
								'status'  => 'continue',
								'message' => $successful_msg,
							);
						}
						if ( $mail_not_send == true ) {
							$message = array(
								'result'  => 0,
								'message' => $no_mails_send_msg,
							);
						}
					} else {
						if ( $offset == 0 ) {
							$message = array(
								'result'  => 0,
								'message' => __( 'No users.', 'mass-users-password-reset-pro' ),
							);
						} else {
							$message = array(
								'result'  => 1,
								'status'  => 'end',
								'message' => __( 'All users password have been reset successfully.', 'mass-users-password-reset-pro' ),
							);
						}
					}
				} else {
					$message = array(
						'result'  => 2,
						'status'  => 'end',
						'message' => wp_sprintf( __( 'You have reached the maximum user limit. Upgrade to pro to get more features <a href="%1$s" target="_blank" class="button button-primary">%2$s</a>', 'mass-users-password-reset' ), esc_url( 'https://codecanyon.net/item/mass-users-password-reset/20809350' ), __( 'Upgrade Now', 'mass-users-password-reset' ) ),
					);
				}
			} else {
				$message = array(
					'result'  => 0,
					'message' => __( 'Unauthorized Access', 'mass-users-password-reset' ),
				);
			}
			echo wp_json_encode( $message );
			exit;
		}

		/**
		 * Send email format and reset user password
		 *
		 * @param  int $user_id User ID.
		 * @return bool ( return wp_mail response );
		 */
		function send_email_format( $user_id ) {
			// Get user data
			$user_info = get_userdata( $user_id );
			// Generate new password
			$new_password = wp_generate_password( apply_filters( 'mupr_password_length', '8' ), true, false );
			// Set user password
			wp_set_password( $new_password, $user_info->ID );
			ob_start(); ?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?php echo get_bloginfo( 'name' ); ?></title>
			</head>
			<body topmargin="0" leftmargin="0" style="padding:0; margin:0;">
				<p><?php printf( esc_html__( 'Dear %s', 'mass-users-password-reset' ), $user_info->display_name ); ?>,</p>
				<p><?php _e( 'Your Password has been changed.', 'mass-users-password-reset' ); ?> </p>
				<p><?php printf( esc_html__( 'Your new password is : %s', 'mass-users-password-reset' ), $new_password ); ?></p>
				<p><?php printf( esc_html__( 'To reset your password, log in to %s', 'mass-users-password-reset' ), get_site_url() . '/wp-admin/profile.php' ); ?></p>
			</body>
			</html> 
			<?php
			$to      = $user_info->user_email;
			$subject = sprintf( __( 'Reset Password of %s', 'mass-users-password-reset' ), get_bloginfo( 'name' ) );
			$body    = ob_get_clean();
			// To send HTML mail, the Content-type header must be set
			// Additional headers
			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' );
			// Send mail
			$result = wp_mail( $to, $subject, $body, $headers );

			/**
			 * After reset action
			 *
			 * @var $user_id object
			 */
			do_action( 'mupr_after_reset', $user_info );
			// return wp_mail response.
			return $result;
		}

		/**
		 * Get plugin data
		 */
		public function mupr_plugin_data() {
			// If check ajax nonce
			check_ajax_referer( 'mupr_get_details_' . get_current_user_id(), 'nonce' );
			include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
			// Get active installtion
			$data       = array( 'result' => 0 );
			$plugin_api = plugins_api(
				'plugin_information',
				array(
					'slug'   => 'mass-users-password-reset',
					'fields' => array(
						'active_installs' => true,
						'downloaded'      => true,
					),
				)
			);
			if ( ! is_wp_error( $plugin_api ) ) {
				$data['result']     = 1;
				$data['active']     = $plugin_api->active_installs;
				$data['downloaded'] = $plugin_api->downloaded;

			}
			echo wp_json_encode( $data );
			die();
		}

		/**
		 * Plugin row meta.
		 *
		 * @param  array  $links       Row items.
		 * @param  string $plugin_file File path.
		 * @param  array  $plugin_data Plugin data.
		 * @param  string $status Plugin status.
		 * @return array Plugin row action links.
		 */
		public function mass_users_password_reset_plugin_row_meta( $links, $plugin_file, $plugin_data, $status ) {
			// Add documentation link in plugin meta.
			if ( ( isset( $plugin_data['slug'] ) && 'mass-users-password-reset' === $plugin_data['slug'] ) ) {
				$links[] = wp_sprintf( '<a href="%1$s" target="_blank"><span class="dashicons dashicons-search"></span> %2$s</a>', esc_url( 'https://krishaweb.com/docs/mass-users-password-reset/' ), __( 'Documentation', 'mass-users-password-reset-pro' ) );
				$links[] = wp_sprintf( '<a href="mailto:%1$s"><span class="dashicons dashicons-admin-users"></span> %2$s</a>', sanitize_email( 'support@krishaweb.com' ), __( 'Support', 'mass-users-password-reset-pro' ) );
				$links[] = wp_sprintf( '<a href="%1$s"><span class="dashicons dashicons-cart"></span> %2$s</a>', esc_url( 'https://codecanyon.net/item/mass-users-password-reset-pro/20809350' ), __( 'Premium', 'mass-users-password-reset-pro' ) );
			}
			return $links;
		}
	} // end of class
}
