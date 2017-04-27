<?php

/**
 * Class ClientDash_Page_Account_Tab_About
 *
 * Adds the core content section for Account -> About.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Account_Tab_About extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => __( 'Basic Information', 'client-dash' ),
			'page'     => __( 'Account', 'client-dash' ),
			'tab'      => __( 'About You', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get the current user object
		global $current_user;

		// Get the user information
		$cd_username       = $current_user->user_login;
		$cd_firstname      = $current_user->first_name;
		$cd_lastname       = $current_user->last_name;
		$cd_useremail      = $current_user->user_email;
		$cd_url            = $current_user->user_url;
		$cd_userregistered = $current_user->user_registered;

		// Get current user role
		global $wp_roles;
		$cd_userrole = $wp_roles->role_names[ $current_user->roles[0] ];
		$cd_usercaps = $current_user->allcaps;
		?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Your username', 'client-dash' ); ?></th>
                <td><?php echo $cd_username; ?></td>
            </tr>

			<?php if ( $cd_firstname || $cd_lastname ): { ?>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Your name', 'client-dash' ); ?></th>
                    <td><?php echo $cd_firstname . ' ' . $cd_lastname; ?></td>
                </tr>
			<?php } endif; ?>

            <tr valign="top">
                <th scope="row"><?php _e( 'Your e-mail address', 'client-dash' ); ?></th>
                <td><?php echo $cd_useremail; ?></td>
            </tr>

			<?php if ( $cd_url ): { ?>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Your URL', 'client-dash' ); ?></th>
                    <td><a href="<?php echo $cd_url; ?>" target="_blank"><?php echo $cd_url; ?></a></td>
                </tr>
			<?php } endif; ?>

            <tr valign="top">
                <th scope="row"><?php _e( 'When you first joined this site', 'client-dash' ); ?></th>
                <td><?php echo $cd_userregistered; ?></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e( 'Your role', 'client-dash' ); ?></th>
                <td><?php echo $cd_userrole; ?>
                    <span class="cd-caps cd-click dashicons dashicons-info"
                          onclick="cdMain.updown('cd-caps');"
                          style="color:<?php echo $this->get_color_scheme( 'secondary' ); ?>"></span>
                    <span id="cd-caps" style="display: none;">
		          <h4><?php echo $cd_userrole; ?>s are able to:</h4>
						<?php
						if ( ! empty( $cd_usercaps ) ) {
							unset( $cd_usercaps['level_0'], $cd_usercaps['level_1'], $cd_usercaps['level_2'], $cd_usercaps['level_3'], $cd_usercaps['level_4'], $cd_usercaps['level_5'], $cd_usercaps['level_6'], $cd_usercaps['level_7'], $cd_usercaps['level_8'], $cd_usercaps['level_9'], $cd_usercaps['level_10'] );
							echo '<ul>';
							foreach ( $cd_usercaps as $key => $value ) {
								echo '<li>' . $key . '</li>';
							}
							echo '</ul>';
						} ?>
		        </span>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <a href="<?php site_url(); ?>/wp-admin/profile.php" class="button-primary">
						<?php _e( 'Edit Your Profile', 'client-dash' ); ?>
                    </a>
                </th>
                <td></td>
            </tr>
        </table>
		<?php
	}
}