<?php
/**
 * Adds Helper Pages.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/helper-pages
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_Helper_Pages
 *
 * Adds Helper Pages.
 *
 * @since 2.0.0
 */
class ClientDash_Helper_Pages {

	/**
	 * Pages only viewable by the current user.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $user_pages;

	/**
	 * ClientDash_Helper_Pages constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		if ( is_admin() ) {

			add_action( 'init', array( $this, 'load_pages' ) );
		}

		add_action( 'admin_menu', array( $this, 'add_pages' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_widgets' ) );
		add_shortcode( 'cd_feed', array( $this, 'shortcode_feed' ) );
	}

	/**
	 * Returns the pages the current user can view.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_user_pages() {

		return $this->user_pages;
	}

	/**
	 * Loads the custom pages.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function load_pages() {

		$this->user_pages = self::load_user_pages( self::get_pages() );
	}

	/**
	 * Loads current user viewable pages only.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pages All pages.
	 *
	 * @return array $pages
	 */
	public static function load_user_pages( $pages ) {

		$current_user = wp_get_current_user();

		// Admins can always view them all
		if ( is_super_admin() || in_array( 'administrator', $current_user->roles ) ) {

			return $pages;
		}

		foreach ( $pages as $page_ID => $page ) {

			foreach ( $page['tabs'] as $tab_ID => $tab ) {

				if ( ! array_intersect( $tab['roles'], $current_user->roles ) ) {

					unset( $pages[ $page_ID ]['tabs'][ $tab_ID ] );
				}
			}

			if ( empty( $pages[ $page_ID ]['tabs'] ) ) {

				unset( $pages[ $page_ID ] );
			}
		}

		return $pages;
	}

	/**
	 * Gets custom CD pages.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static public function get_pages() {

		static $page_modifications;

		if ( $page_modifications === null ) {

			$page_modifications = get_option( 'cd_helper_pages', array() );
		}

		/**
		 * Client Dash helper pages modifications (straight from the DB).
		 *
		 * @since 2.0.0
		 */
		$pages = apply_filters( 'cd_helper_pages_modifications', $page_modifications );

		$pages = array(
			'account'    => array(
				'title'       => __( 'Account', 'clientdash' ),
				'icon'        => 'dashicons-id-alt',
				'description' => __( 'Provides some quick, helpful information on the user\'s account.', 'client-dash' ),
				'parent'      => 'index.php',
				'deleted'     => false,
				'position'    => 100,
				'tabs'        => array(
					'about' => array(
						'title'    => __( 'About', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_account_tab_about' ),
						'roles'    => array( 'editor', 'author', 'contributor', 'subscriber' ),
					),
					'sites' => array(
						'title'    => __( 'Sites', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_account_tab_sites' ),
						'roles'    => array( 'editor', 'author' ),
					),
				),
			),
			'help'       => array(
				'title'       => __( 'Help', 'clientdash' ),
				'icon'        => 'dashicons-editor-help',
				'description' => __( 'Provides information about the current website and setup.', 'client-dash' ),
				'parent'      => 'index.php',
				'deleted'     => false,
				'position'    => 100,
				'tabs'        => array(
					'info'   => array(
						'title'    => __( 'Info', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_help_tab_info' ),
						'roles'    => array( 'editor' ),
					),
					'domain' => array(
						'title'    => __( 'Domain', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_help_tab_domain' ),
						'roles'    => array( 'editor' ),
					),
				),
			),
			'reports'    => array(
				'title'       => __( 'Reports', 'clientdash' ),
				'icon'        => 'dashicons-chart-area',
				'description' => __( 'Provides quick reports on the website\'s content.', 'client-dash' ),
				'parent'      => 'index.php',
				'deleted'     => false,
				'position'    => 100,
				'tabs'        => array(
					'site' => array(
						'title'    => __( 'Site', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_reports_tab_site' ),
						'roles'    => array( 'editor', 'author' ),
					),
				),
			),
			'admin_page' => array(
				'title'       => __( 'Admin Page', 'clientdash' ),
				'icon'        => 'dashicons-admin-generic',
				'description' => sprintf(
					__( 'Customizable admin page to benefit your users. It can be edited further %shere%s.', 'client-dash' ),
					'<a href="' . admin_url( 'admin.php?page=clientdash_admin_page' ) . '">',
					'</a>'
				),
				'parent'      => 'index.php',
				'deleted'     => false,
				'position'    => 100,
				'tabs'        => array(
					'main' => array(
						'title'    => __( 'Main', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_admin_page_tab_main' ),
						'roles'    => array(),
					),
					'feed' => array(
						'title'    => __( 'Feed', 'client-dash' ),
						'callback' => array( __CLASS__, 'load_cd_page_admin_page_tab_feed' ),
						'roles'    => array(),
					),
				),
			),
		);

		// Override via saved options.
		foreach ( $pages as $page_ID => &$page ) {

			if ( isset( $page_modifications[ $page_ID ] ) ) {

				$modified_page = $page_modifications[ $page_ID ];

				if ( $modified_page['title'] ) {

					$page['title'] = $modified_page['title'];
				}

				if ( $modified_page['icon'] ) {

					$page['icon'] = $modified_page['icon'];
				}

				if ( $modified_page['tabs'] && $page['tabs'] ) {

					foreach ( $page['tabs'] as $tab_ID => &$tab ) {

						if ( isset( $modified_page['tabs'][ $tab_ID ] ) ) {

							$modified_tab = $modified_page['tabs'][ $tab_ID ];

							if ( isset( $modified_tab['title'] ) ) {

								$tab['title'] = $modified_tab['title'];
							}

							$tab['roles'] = isset( $modified_tab['roles'] ) ? $modified_tab['roles'] : array();
						}
					}
				}
			}
		}

		/**
		 * Client Dash helper pages.
		 *
		 * @since 2.0.0
		 */
		$pages = apply_filters( 'cd_helper_pages', $pages );

		return $pages;
	}

	/**
	 * Adds the custom pages.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function add_pages() {

		global $submenu;

		if ( ! $this->user_pages ) {

			return;
		}

		// Add toplevel
		foreach ( $this->user_pages as $page_ID => $page ) {

			if ( $page['parent'] != 'toplevel' ) {

				continue;
			}

			add_menu_page(
				$page['title'],
				$page['title'],
				'read',
				"cd_$page_ID",
				array( $this, 'load_page' ),
				$page['icon'] ? $page['icon'] : $page['icon'],
				$page['position'] ? $page['position'] : 100
			);
		}

		// Add submenu
		foreach ( $this->user_pages as $page_ID => $page ) {

			if ( $page['parent'] == 'toplevel' ) {

				continue;
			}

			add_submenu_page(
				$page['parent'],
				$page['title'],
				$page['title'],
				'read',
				"cd_$page_ID",
				array( $this, 'load_page' )
			);
		}
	}

	/**
	 * Adds dashboard widgets for the Core CD Pages.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function add_widgets() {

		foreach ( $this->user_pages as $page_ID => $page ) {

			if ( $page['deleted'] || ! $page['parent'] ) {

				continue;
			}

			wp_add_dashboard_widget(
				"cd_$page_ID",
				$page['title'],
				array( $this, 'load_widget' ),
				null,
				array(
					'icon' => $page['icon'],
					'link' => admin_url( ( $page['parent'] == 'toplevel' ? 'admin.php' : $page['parent'] ) .
					                     "?page=cd_{$page_ID}" ),
				)
			);
		}
	}

	/**
	 * Loads a custom page.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function load_page() {

		global $plugin_page;

		$cd_page_ID = substr( $plugin_page, 3 );

		$cd_page = $this->user_pages[ $cd_page_ID ];

		/**
		 * The template to use for helper CD pages.
		 *
		 * @since 2.0.0
		 */
		$page_template = apply_filters(
			'cd_helper_page_template',
			CLIENTDASH_DIR . 'core/helper-pages/views/page.php'
		);

		if ( ! file_exists( $page_template ) ) {

			return;
		}

		// Active tab
		if ( isset( $_GET['tab'] ) ) {

			$active_tab = $_GET['tab'];

		} else {

			reset( $cd_page['tabs'] );
			$active_tab = key( $cd_page['tabs'] );
		}

		include_once $page_template;
	}

	/**
	 * Loads Core CD Page widgets.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param mixed $object Who the heck knows...
	 * @param array $widget Widget args.
	 */
	function load_widget( $object, $widget ) {

		$icon = $widget['args']['icon'];
		$link = $widget['args']['link'];

		include CLIENTDASH_DIR . 'core/helper-pages/views/widget.php';
	}

	/**
	 * Loads the Account page tab About.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_account_tab_about() {

		global $wp_roles;

		$current_user = wp_get_current_user();
		$user_role    = $wp_roles->role_names[ $current_user->roles[0] ];

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/account/about.php';
	}

	/**
	 * Loads the Account page tab Sites.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_account_tab_sites() {

		$current_user = wp_get_current_user();

		$blogs = get_blogs_of_user( $current_user->ID );

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/account/sites.php';
	}

	/**
	 * Loads the Help page tab Domain.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_help_tab_domain() {

		$domain = str_replace( 'http://', '', get_site_url() );
		$ip     = gethostbyname( $domain );
		$dns    = dns_get_record( $domain );

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/help/domain.php';
	}

	/**
	 * Loads the Help page tab Info.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_help_tab_info() {

		global $wp_version;

		$current_theme     = wp_get_theme();
		$installed_plugins = get_plugins();
		$active_plugins    = get_option( 'active_plugins' );
		$theme_uri         = $current_theme->get( 'ThemeURI' );
		$author_uri        = $current_theme->get( 'AuthorURI' );

		foreach ( $active_plugins as $i => $active_plugin ) {

			$active_plugins[ $i ] = get_plugin_data( dirname( CLIENTDASH_DIR ) . "/$active_plugin" );
		}

		$child_theme = false;

		if ( is_child_theme() ) {

			$child_theme = wp_get_theme( $current_theme->get_template() );
		}

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/help/info.php';
	}

	/**
	 * Loads the Reports page tab Site.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_reports_tab_site() {

		$count_comments = wp_count_comments();
		$count_users    = count_users();
		$post_types     = get_post_types( array(
			'public' => true,
		), 'objects' );
		$upload_dir     = wp_upload_dir();
		$dir_info       = cd_get_dir_size( $upload_dir['basedir'] );
		$attachments    = wp_count_posts( 'attachment' );

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/reports/site.php';
	}

	/**
	 * Loads the Admin Page tab Main.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_admin_page_tab_main() {

		$content = get_option( 'cd_adminpage_content' );

		$content = apply_filters( 'the_content', $content );

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/admin-page/main.php';
	}

	/**
	 * Loads the Admin Page tab Feed.
	 *
	 * @since 2.0.0
	 */
	static public function load_cd_page_admin_page_tab_feed() {

		// Get the feed options
		$feed_url   = get_option( 'cd_adminpage_feed_url', null );
		$feed_count = get_option( 'cd_adminpage_feed_count', 5 );

		include_once CLIENTDASH_DIR . 'core/helper-pages/views/admin-page/feed.php';
	}

	/**
	 * Shortcode for RSS feed.
	 *
	 * @since 2.0.0
	 *
	 * @param array $atts
	 */
	static public function shortcode_feed( $atts = array() ) {

		$atts = shortcode_atts( array(
			'url'   => '',
			'count' => '5',
		), $atts, 'cd_feed' );

		if ( ! $atts['url'] ) {

			ob_start();
			include_once CLIENTDASH_DIR . 'core/helper-pages/views/shortcodes/feed-error.php';

			return ob_get_clean();
		}

		// Get the feed items
		$feed = fetch_feed( $atts['url'] );

		// Check for an error if there's no RSS feed
		if ( is_wp_error( $feed ) ) {

			ob_start();
			include_once CLIENTDASH_DIR . 'core/helper-pages/views/shortcodes/feed-error.php';

			return ob_get_clean();
		}

		$feed_items = $feed->get_items( 0, $atts['count'] );

		ob_start();
		include_once CLIENTDASH_DIR . 'core/helper-pages/views/shortcodes/feed.php';

		return ob_get_clean();
	}
}