<!-- mupr filter start -->
<div class="mupr-filter-wrap">
	<ul>
		<li class="mupr-role-label"><?php _e( 'User Role', 'mass-users-password-reset' ); ?></li>
		<li>
			<div class="mupr-select-box">
				<form action="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" method="GET" id="role_filter">
					<input type="hidden" name="page" value="mass_users_password_reset_options">
						<select class="mupr-selectpicker" name="role_filter">
							<option value="all"><?php _e( 'All', 'mass-users-password-reset' ); ?></option>
							<?php
								echo $this->mupr_user_role_filter();
							?>
						</select>
				</form>
			</div>
		</li>
	</ul>
</div>
<!-- mupr filter end -->
<!-- mupr table start -->
<div class="mupr-user-list">
	<?php $users = $this->mupr_user_lists(); ?>
	<table class="wp-list-table widefat mupr-table fixed table-wrap users">
		<thead>
			<tr class="post_header">
				<th class="column-primary"><?php _e( 'Username', 'mass-users-password-reset' ); ?></th>
				<th><?php _e( 'Name', 'mass-users-password-reset' ); ?></th>
				<th><?php _e( 'Email', 'mass-users-password-reset' ); ?></th>
			</tr>
		</thead>
		<tbody id="the-list" data-wp-lists="list:user">
			<?php
			if ( $users['list']->results ) :
				foreach ( $users['list']->results as $user ) :
					$username = ! empty( $user->first_name ) && ! empty( $user->last_name ) ? wp_sprintf( '%s %s', $user->first_name, $user->last_name ) : '—';
					?>
					<tr>
						<td class="column-primary username column-username">
							<a href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>" target="_blank"><?php echo $user->user_login; ?></a>
							<button type="button" class="toggle-row"> <span class="screen-reader-text"><?php echo _e( 'Show more details', 'mass-users-password-reset' ); ?></span> </button>
						</td>
						<td class="name column-name" data-colname="Name"><?php echo $username; ?></td>
						<td class="email column-email" data-colname="Email"><a href="mailto:<?php echo $user->user_email; ?>"><?php echo $user->user_email; ?></a></td>
					</tr>
			<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td class="column-primary username column-username">
						<p><?php _e( 'No users yet', 'mass-users-password-reset' ); ?></p>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<!-- mupr table end -->
<div class="tablenav mupr-tablenav">
	<div class="mupr-action">
		<?php
			$disabled = array();
		if ( empty( $users['list']->results ) ) {
			$disabled['disabled'] = 'disabled';
		}
			submit_button(
				__( 'Reset Password', 'mass-users-password-reset' ),
				'button mupr-btn',
				'reset',
				false,
				$disabled
			);
			/**
			 * Add new custom action
			 *
			 * @var $user object
			 */
			do_action( 'mupr_custom_action', $users );
			?>
	</div>
	<?php
		// Pagination
		// Count total item
		$total_items = count( $users['pagination']->results );
	if ( $total_items > 0 ) {
		// Set pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $this->user_per_page,
			)
		);
		echo $this->pagination( 'bottom' );
	}
	?>
</div>
