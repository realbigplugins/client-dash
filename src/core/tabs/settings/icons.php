<?php

/**
 * Class ClientDash_Page_Settings_Tab_Icons
 *
 * Adds the core content section for Settings -> Icons.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Icons
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Icons extends ClientDash {

	public static $icons = array(
		'dashicons-menu',
		'dashicons-dashboard',
		'dashicons-admin-site',
		'dashicons-admin-media',
		'dashicons-admin-page',
		'dashicons-admin-comments',
		'dashicons-admin-appearance',
		'dashicons-admin-plugins',
		'dashicons-admin-users',
		'dashicons-admin-tools',
		'dashicons-admin-settings',
		'dashicons-admin-network',
		'dashicons-admin-generic',
		'dashicons-admin-home',
		'dashicons-admin-collapse',
		'dashicons-admin-links',
		'dashicons-admin-post',
		'dashicons-format-standard',
		'dashicons-format-image',
		'dashicons-format-gallery',
		'dashicons-format-audio',
		'dashicons-format-video',
		'dashicons-format-links',
		'dashicons-format-chat',
		'dashicons-format-status',
		'dashicons-format-aside',
		'dashicons-format-quote',
		'dashicons-welcome-write-blog',
		'dashicons-welcome-edit-page',
		'dashicons-welcome-add-page',
		'dashicons-welcome-view-site',
		'dashicons-welcome-widgets-menus',
		'dashicons-welcome-comments',
		'dashicons-welcome-learn-more',
		'dashicons-image-crop',
		'dashicons-image-rotate-left',
		'dashicons-image-rotate-right',
		'dashicons-image-flip-vertical',
		'dashicons-image-flip-horizontal',
		'dashicons-undo',
		'dashicons-redo',
		'dashicons-editor-bold',
		'dashicons-editor-italic',
		'dashicons-editor-ul',
		'dashicons-editor-ol',
		'dashicons-editor-quote',
		'dashicons-editor-alignleft',
		'dashicons-editor-aligncenter',
		'dashicons-editor-alignright',
		'dashicons-editor-insertmore',
		'dashicons-editor-spellcheck',
		'dashicons-editor-distractionfree',
		'dashicons-editor-expand',
		'dashicons-editor-contract',
		'dashicons-editor-kitchensink',
		'dashicons-editor-underline',
		'dashicons-editor-justify',
		'dashicons-editor-textcolor',
		'dashicons-editor-paste-word',
		'dashicons-editor-paste-text',
		'dashicons-editor-removeformatting',
		'dashicons-editor-video',
		'dashicons-editor-customchar',
		'dashicons-editor-outdent',
		'dashicons-editor-indent',
		'dashicons-editor-help',
		'dashicons-editor-strikethrough',
		'dashicons-editor-unlink',
		'dashicons-editor-rtl',
		'dashicons-editor-break',
		'dashicons-editor-code',
		'dashicons-editor-paragraph',
		'dashicons-align-left',
		'dashicons-align-right',
		'dashicons-align-center',
		'dashicons-align-none',
		'dashicons-lock',
		'dashicons-calendar',
		'dashicons-visibility',
		'dashicons-post-status',
		'dashicons-edit',
		'dashicons-post-trash',
		'dashicons-trash',
		'dashicons-external',
		'dashicons-arrow-up',
		'dashicons-arrow-down',
		'dashicons-arrow-left',
		'dashicons-arrow-right',
		'dashicons-arrow-up-alt',
		'dashicons-arrow-down-alt',
		'dashicons-arrow-left-alt',
		'dashicons-arrow-right-alt',
		'dashicons-arrow-up-alt2',
		'dashicons-arrow-down-alt2',
		'dashicons-arrow-left-alt2',
		'dashicons-arrow-right-alt2',
		'dashicons-leftright',
		'dashicons-sort',
		'dashicons-randomize',
		'dashicons-list-view',
		'dashicons-exerpt-view',
		'dashicons-hammer',
		'dashicons-art',
		'dashicons-migrate',
		'dashicons-performance',
		'dashicons-universal-access',
		'dashicons-universal-access-alt',
		'dashicons-tickets',
		'dashicons-nametag',
		'dashicons-clipboard',
		'dashicons-heart',
		'dashicons-megaphone',
		'dashicons-schedule',
		'dashicons-wordpress',
		'dashicons-wordpress-alt',
		'dashicons-pressthis',
		'dashicons-update',
		'dashicons-screenoptions',
		'dashicons-info',
		'dashicons-cart',
		'dashicons-feedback',
		'dashicons-cloud',
		'dashicons-translation',
		'dashicons-tag',
		'dashicons-category',
		'dashicons-archive',
		'dashicons-tagcloud',
		'dashicons-text',
		'dashicons-media-archive',
		'dashicons-media-audio',
		'dashicons-media-code',
		'dashicons-media-default',
		'dashicons-media-document',
		'dashicons-media-interactive',
		'dashicons-media-spreadsheet',
		'dashicons-media-text',
		'dashicons-media-video',
		'dashicons-playlist-audio',
		'dashicons-playlist-video',
		'dashicons-yes',
		'dashicons-no',
		'dashicons-no-alt',
		'dashicons-plus',
		'dashicons-plus-alt',
		'dashicons-minus',
		'dashicons-dismiss',
		'dashicons-marker',
		'dashicons-star-filled',
		'dashicons-star-half',
		'dashicons-star-empty',
		'dashicons-flag',
		'dashicons-share',
		'dashicons-share1',
		'dashicons-share-alt',
		'dashicons-share-alt2',
		'dashicons-twitter',
		'dashicons-rss',
		'dashicons-email',
		'dashicons-email-alt',
		'dashicons-facebook',
		'dashicons-facebook-alt',
		'dashicons-networking',
		'dashicons-googleplus',
		'dashicons-location',
		'dashicons-location-alt',
		'dashicons-camera',
		'dashicons-images-alt',
		'dashicons-images-alt2',
		'dashicons-video-alt',
		'dashicons-video-alt2',
		'dashicons-video-alt3',
		'dashicons-vault',
		'dashicons-shield',
		'dashicons-shield-alt',
		'dashicons-sos',
		'dashicons-search',
		'dashicons-slides',
		'dashicons-analytics',
		'dashicons-chart-pie',
		'dashicons-chart-bar',
		'dashicons-chart-line',
		'dashicons-chart-area',
		'dashicons-groups',
		'dashicons-businessman',
		'dashicons-id',
		'dashicons-id-alt',
		'dashicons-products',
		'dashicons-awards',
		'dashicons-forms',
		'dashicons-testimonial',
		'dashicons-portfolio',
		'dashicons-book',
		'dashicons-book-alt',
		'dashicons-download',
		'dashicons-upload',
		'dashicons-backup',
		'dashicons-clock',
		'dashicons-lightbulb',
		'dashicons-microphone',
		'dashicons-desktop',
		'dashicons-tablet',
		'dashicons-smartphone',
		'dashicons-smiley'
	);

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => 'Core Settings Icons',
			'page'     => 'Settings',
			'tab'      => 'Icons',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		global $wp_version;

		$account_dashicon   = get_option( 'cd_dashicon_account', $this->option_defaults['dashicon_account'] );
		$reports_dashicon   = get_option( 'cd_dashicon_reports', $this->option_defaults['dashicon_reports'] );
		$help_dashicon      = get_option( 'cd_dashicon_help', $this->option_defaults['dashicon_help'] );
		$webmaster_dashicon = get_option( 'cd_dashicon_webmaster', $this->option_defaults['dashicon_webmaster'] );

		// Show nag if not up to date, because not all icons exist in WP pre 1.9
		if ( floatval( $wp_version ) < 3.9 ) {
			self::error_nag( 'You are currently running a version of WordPress below 1.9. Not all icons exist before 1.9 and will not show here properly. Please update WordPress as soon as you can.' );
		}
		?>

		<input type="hidden" id="cd_dashicon_account" name="cd_dashicon_account"
		       value="<?php echo $account_dashicon; ?>"/>
		<input type="hidden" id="cd_dashicon_reports" name="cd_dashicon_reports"
		       value="<?php echo $reports_dashicon; ?>"/>
		<input type="hidden" id="cd_dashicon_help" name="cd_dashicon_help" value="<?php echo $help_dashicon; ?>"/>
		<input type="hidden" id="cd_dashicon_webmaster" name="cd_dashicon_webmaster"
		       value="<?php echo $webmaster_dashicon; ?>"/>

		<div id="cd-dashicons-selections">
			<div class="cd-col-four cd-account" onclick="cdMain.dashicons_selected('account');">
				<p class="dashicons <?php echo $account_dashicon; ?> active"
				   data-dashicon="<?php echo $account_dashicon; ?>"
				   data-widget="account"></p>

				<p class="cd-dashicons-title">Account</p>
			</div>

			<div class="cd-col-four cd-reports" onclick="cdMain.dashicons_selected('reports');">
				<p class="dashicons  <?php echo $reports_dashicon; ?>"
				   data-dashicon="<?php echo $reports_dashicon; ?>"
				   data-widget="reports"></p>

				<p class="cd-dashicons-title">Reports</p>
			</div>

			<div class="cd-col-four cd-help" onclick="cdMain.dashicons_selected('help');">
				<p class="dashicons  <?php echo $help_dashicon; ?>"
				   data-dashicon="<?php echo $help_dashicon; ?>"
				   data-widget="help"></p>

				<p class="cd-dashicons-title">Help</p>
			</div>

			<div class="cd-col-four cd-webmaster" onclick="cdMain.dashicons_selected('webmaster');">
				<p class="dashicons  <?php echo $webmaster_dashicon; ?>"
				   data-dashicon="<?php echo $webmaster_dashicon; ?>"
				   data-widget="webmaster"></p>

				<p class="cd-dashicons-title">
					<?php echo get_option( 'cd_webmaster_name', $this->option_defaults['webmaster_name'] ); ?>
				</p>
			</div>
		</div>

		<p class="submit" style="text-align:center;">
			<?php submit_button( null, 'primary', null, false ); ?>
		</p>

		<?php
		// Begin grid
		echo '<ul id="cd-dashicons-grid">';

		foreach ( self::$icons as $dashicon ) {
			?>
			<li class="cd-dashicons-grid-item <?php echo $account_dashicon == $dashicon ? 'active' : ''; ?>">
				<div class="container" onclick="cdMain.dashicons_change('<?php echo $dashicon; ?>', this)">
					<span class="dashicons <?php echo $dashicon; ?>"></span>
				</div>
			</li>
		<?php
		}

		// Close grid
		echo '</ul>';
	}
}