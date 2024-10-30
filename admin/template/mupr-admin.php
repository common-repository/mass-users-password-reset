<?php
$spinner = '';
if ( isset( $_GET['tab'] ) && 'user-list' !== $_GET['tab'] ) {
	$active_tab = $_GET['tab'];
} else {
	$active_tab = 'user-list';
	$spinner    = '<div class="spinner"></div>';
}
?>
<div class="mupr-wrap<?php echo $active_tab == 'mupr-pro' ? ' mupr-pro-wrap' : ''; ?>">
	<?php echo $spinner; ?>
	<div class="wrap">
		<!-- mupr header start -->
		<div class="mupr-header">
			<div class="mupr-header-right">
				<div class="logo">
					<a href="<?php echo esc_url( 'https://codecanyon.net/item/mass-users-password-reset/20809350' ); ?>" target="_blank"><img src="<?php echo MASS_USERS_PASSWORD_RESET_PLUGIN_URL; ?>assets/images/mupr-logo.png" alt="Mass User Password Reset"></a>
				</div>
			</div>
			<div class="mupr-header-left">
				<div class="nav-tab-wrapper mupr-tab">
					<?php
						$steps = apply_filters( 'mupr_steps', $this->steps );
					foreach ( $steps as $step ) :
						$tab_name = isset( $step['key'] ) ? $step['key'] : '';
						$tab_icon = isset( $step['icon'] ) ? ' ' . $step['icon'] : '';
						$template = isset( $step['template'] ) ? ' ' . $step['template'] : '';
						?>
						<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'page' => 'mass_users_password_reset_options',
									'tab'  => $tab_name,
								),
								admin_url( 'users.php' )
							)
						);
						?>
									" class="nav-tab<?php echo esc_attr( $active_tab == $tab_name ? ' active' : '' ); ?>"><span class="dashicons<?php echo esc_attr( $tab_icon ); ?>"></span> <?php _e( isset( $step['label'] ) ? $step['label'] : '' ); ?></a>
					<?php endforeach; ?>
					<?php if ( ! class_exists( 'Schedule_Password_Reset\Includes\Schedule_Password_Reset' ) ) : ?>
						<a href="<?php echo esc_url( 'https://store.krishaweb.com/schedule-password-reset-mupr-add-on/' ); ?>" target="_blank" class="nav-tab"><span class="dashicons dashicons-calendar-alt"></span>&nbsp;<?php _e( 'Schedule Password Reset', 'mass-users-password-reset-pro' ); ?></a>
					<?php endif; ?>
					<?php if ( ! function_exists( 'mupr_log_file' ) ) : ?>
						<a href="<?php echo esc_url( 'https://store.krishaweb.com/password-reset-log/' ); ?>" target="_blank" class="nav-tab"><span class="dashicons dashicons-clock"></span>&nbsp;<?php _e( 'Password Reset Log', 'mass-users-password-reset-pro' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!-- mupr header end -->
		<!-- mupr body start -->
		<div class="mupr-body<?php echo $active_tab == 'mupr-pro' ? ' mupr-body-landing' : ''; ?>">
			<div class="notice notice-success is-dismissible mupr-hidden">
				<p><strong><?php echo esc_html( 'Settings saved.' ); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'mass-users-password-reset' ); ?></span>
				</button>
			</div>
			<div class="notice notice-error is-dismissible mupr-hidden">
				<p><strong><?php echo esc_html( 'Something went wrong please try again.' ); ?></strong></p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'mass-users-password-reset' ); ?></span>
				</button>
			</div>
			<?php
				// Require files.
				$current      = array_search( $active_tab, array_column( $steps, 'key' ) );
				$current      = $current ? $current : 0;
				$current_step = $steps[ $current ];
			if ( isset( $current_step['template'] ) && file_exists( $current_step['template'] ) ) {
				require_once $current_step['template'];
			}
			?>
		</div>
		<!-- mupr body end -->
	</div>
</div>
<div class="mupr-need-help">
	<a href="<?php echo esc_url( 'https://krishaweb.com/docs/mass-users-password-reset/' ); ?>" target="_blank"><?php _e( 'Need Help?', 'mass-users-password-reset-pro' ); ?></a>
</div>
