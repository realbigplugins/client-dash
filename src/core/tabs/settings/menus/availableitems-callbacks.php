<?php

/**
 * Class CD_AdminMenu_AvailableItems_Callbacks
 *
 * Contains all callbacks for the admin menu available items.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Menus
 *
 * @since Client Dash 1.6
 */
class CD_AdminMenu_AvailableItems_Callbacks extends ClientDash_Core_Page_Settings_Tab_Menus {

	/**
	 * Outputs the checkbox, its label, and all of the hidden inputs.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param int $i The current iteration (in negative).
	 * @param string $label The label that will show next to the checkbox.
	 * @param array $options_args Options to be explicitly set.
	 */
	public static function loop( $i = - 1, $label = '', $options_args = array() ) {

		// Set up the defaults and the inputs to output
		$options = wp_parse_args( $options_args, self::_get( 'menu_item_defaults' ) );

		echo '<li>';

		// The checkbox and label
		?>
		<label class="menu-item-title">
			<input type="checkbox" class="menu-item-checkbox"
			       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
			       value="0"/>
			<?php echo $label; ?>
		</label>
		<?php

		// Iterations of options to skip over
		$skip = array(
			'db-id',
			'parent-id',
			'position',
			'original-title',
			'cd-page-title',
			'cd-params',
		);

		// Cycle through all the options and output the hidden inputs
		foreach ( $options as $option_name => $option_value ) {

			// Skip if in $skip array
			if ( in_array( $option_name, $skip ) ) {
				continue;
			}
			?>
			<input type="hidden"
			       class="menu-item-<?php echo $option_name; ?>"
			       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
			       value="<?php echo $option_value; ?>"/>
		<?php
		}

		echo '</li>';
	}

	/**
	 * All menu / sub-menu pages created by added post types: Add New, View All, Taxonomies.
	 *
	 * @since Client Dash 1.6
	 */
	public static function post_types() {

		global $cd_current_menu_role;

		$role = get_role( $cd_current_menu_role ? $cd_current_menu_role : 'administrator' );

		if ( ! array_key_exists( 'edit_posts', $role->capabilities ) ) {

			echo '<p class="description">No items present</p>';

			return;
		}

		// Get all of our post types
		$all_post_types = get_post_types( array(
			'show_in_menu' => true,
		) );

		$post_types = array();
		// Construct an array for later use
		foreach ( $all_post_types as $post_type ) {

			// Attachment should be media
			if ( $post_type == 'attachment' ) {
				$post_type = 'media';
			}

			// Generate link
			switch ( $post_type ) {
				case 'post':
					$listall_link = 'edit.php';
					$addnew_link  = 'post-new.php';
					break;
				case 'media':
					$listall_link = 'upload.php';
					$addnew_link  = 'media-new.php';
					break;
				default:
					$listall_link = "edit.php?post_type=$post_type";
					$addnew_link  = "post-new.php?post_type=$post_type";
			}

			// Determine the plural title
			switch ( $post_type ) {
				case 'media':
					$title_plural = 'media';
					break;
				default:
					$title_plural = "{$post_type}s";
			}

			$post_types[] = array(
				'id'           => $post_type,
				'title'        => ucwords( str_replace( array( '_', '-' ), ' ', $post_type ) ),
				'title_plural' => ucwords( str_replace( array( '_', '-' ), ' ', $title_plural ) ),
				'listall_link' => $listall_link,
				'addnew_link'  => $addnew_link
			);
		}

		// Default icons
		$icon = array(
			'post'  => 'dashicons-admin-post',
			'media' => 'dashicons-admin-media',
			'page'  => 'dashicons-admin-page'
		);

		// If no post types (uh never?)
		if ( empty( $post_types ) ) {
			echo '<p class="description">No items present</p>';

			return;
		}
		?>

		<div id="posttype-page" class="posttypediv">

			<ul id="posttype-page-tabs" class="posttype-tabs add-menu-item-tabs">
				<li class="tabs">
					<a class="nav-tab-link" data-type="tabs-panel-posttype-toplevel"
					   href="/wp-admin/nav-menus.php?page-tab=most-recent#tabs-panel-posttype-toplevel">
						Top-level
					</a>
				</li>
				<li>
					<a class="nav-tab-link" data-type="tabs-panel-posttype-addnew"
					   href="/wp-admin/nav-menus.php?page-tab=all#tabs-panel-posttype-addnew">
						Add New
					</a>
				</li>
				<li>
					<a class="nav-tab-link" data-type="tabs-panel-posttype-listall"
					   href="/wp-admin/nav-menus.php?page-tab=search#tabs-panel-posttype-listall">
						List All
					</a>
				</li>
				<li>
					<a class="nav-tab-link" data-type="tabs-panel-posttype-taxonomies"
					   href="/wp-admin/nav-menus.php?page-tab=search#tabs-panel-posttype-taxonomies">
						Taxonomies
					</a>
				</li>
			</ul>

			<div id="tabs-panel-posttype-toplevel" class="tabs-panel tabs-panel-active">
				<ul id="posttypechecklist-toplevel" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( $post_types as $post_type ) {
						$i --;

						$options = array(
							'title'   => $post_type['title_plural'],
							'url'     => $post_type['listall_link'],
							'cd-icon' => isset( $icon[ $post_type['id'] ] ) ? $icon[ $post_type['id'] ] : 'dashicons-admin-post',
							'cd-type' => 'post_type',
						);

						// Output the checkbox and inputs HTML
						self::loop( $i, $options['title'], $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-posttype-addnew" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-addnew" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( $post_types as $post_type ) {
						$i --;

						$options = array(
							'title'   => 'Add New ' . $post_type['title'],
							'url'     => $post_type['addnew_link'],
							'cd-icon' => isset( $icon[ $post_type['id'] ] ) ? $icon[ $post_type['id'] ] : 'dashicons-admin-post',
							'cd-type' => 'post_type',
						);

						// Output the checkbox and inputs HTML
						self::loop( $i, $options['title'], $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-posttype-listall" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-listall" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( $post_types as $post_type ) {
						$i --;

						$options = array(
							'title'   => 'All ' . $post_type['title'] . 's',
							'url'     => $post_type['listall_link'],
							'cd-icon' => isset( $icon[ $post_type['id'] ] ) ? $icon[ $post_type['id'] ] : 'dashicons-admin-post',
							'cd-type' => 'post_type',
						);

						// Special case for "Media" title
						if ( $post_type['id'] == 'media' ) {
							$options['title'] = 'Library';
						}

						// Output the checkbox and inputs HTML
						self::loop( $i, $options['title'], $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->


			<div id="tabs-panel-posttype-taxonomies" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-taxonomies" class="categorychecklist form-no-clear">
					<?php

					// Cycle through post types
					foreach ( $post_types as $post_type ) {

						// Get available taxonomies
						$taxonomies = get_object_taxonomies( $post_type['id'], 'object' );

						if ( ! empty( $taxonomies ) ) {

							echo '<li class="taxonomy-title">' . $post_type['title_plural'] . '</li>';

							$i = 0;
							foreach ( $taxonomies as $taxonomy ) {
								$i --;

								$options = wp_parse_args( array(
									'title'   => $taxonomy->labels->name,
									'url'     => "edit-tags.php?taxonomy={$taxonomy->name}",
									'cd-icon' => isset( $icon[ $post_type['id'] ] ) ? $icon[ $post_type['id'] ] : 'dashicons-admin-post',
									'cd-type' => 'taxonomy',
								), self::_get( 'menu_item_defaults' ) );

								// Output the checkbox and inputs HTML
								self::loop( $i, $taxonomy->labels->name, $options );
							}
							echo '</li>'; // .taxonomy-title
						}
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<p class="button-controls">
			<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-page" class="select-all">Select
					All</a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-posttype-page">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * All default admin menu items added by core (or potentially added).
	 *
	 * @since Client Dash 1.6
	 */
	public static function wp_core() {

		global $cd_current_menu_role;

		$role = get_role( $cd_current_menu_role ? $cd_current_menu_role : 'administrator' );
		?>

		<div id="wordpress-core" class="posttypediv">

			<ul id="wordpress-core-tabs" class="wordpress-core-tabs add-menu-item-tabs">
				<li class="tabs">
					<a class="nav-tab-link" data-type="tabs-panel-wordpress-core-toplevel"
					   href="/wp-admin/nav-menus.php?page-tab=most-recent#tabs-panel-wordpress-core-toplevel">
						Top-level
					</a>
				</li>
				<li>
					<a class="nav-tab-link" data-type="tabs-panel-wordpress-core-submenu"
					   href="/wp-admin/nav-menus.php?page-tab=all#tabs-panel-wordpress-core-submenu">
						Sub-menu
					</a>
				</li>
			</ul>

			<div id="tabs-panel-wordpress-core-toplevel" class="tabs-panel tabs-panel-active">
				<ul id="posttypechecklist-wordpress-core" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( self::$wp_core as $item_title => $item ) {
						$i --;

						// Skip if no cap
						if ( ! array_key_exists( $item['capability'], $role->capabilities ) ) {
							continue;
						}

						// Set specific options
						$options = array(
							'title'   => $item_title,
							'url'     => $item['url'],
							'cd-icon' => $item['icon'],
							'cd-type' => 'wp_core',
						);

						// Output the checkbox and inputs HTML
						self::loop( $i, $options['title'], $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-wordpress-core-submenu" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-wordpress-core" class="categorychecklist form-no-clear">
					<?php
					$core_items = self::$wp_core;

					foreach ( self::$wp_core as $item_title => $item ) {

						if ( isset( $item['submenus'] ) ) {

							foreach ( $item['submenus'] as $submenu_item_title => $submenu_item ) {
								// Skip if no cap
								if ( ! array_key_exists( $submenu_item['capability'], $role->capabilities ) ) {
									unset( $core_items[ $item_title ]['submenus'][ $submenu_item_title ] );
								}

								if ( empty( $core_items[ $item_title ]['submenus'] ) ) {
									unset( $core_items[ $item_title ]['submenus'] );
								}
							}
						}

						if ( empty( $core_items[ $item_title ]['submenus'] ) ) {
							unset( $core_items[ $item_title ] );
						}
					}

					foreach ( $core_items as $item_title => $item ) {

						if ( isset( $item['submenus'] ) ) {

							echo '<li class="cd-availableitems-separator">' . $item_title . '</li>';

							$i = 0;
							foreach ( $item['submenus'] as $submenu_item_title => $submenu_item ) {
								$i --;

								// Set specific options
								$options = array(
									'title'             => $submenu_item_title,
									'url'               => $submenu_item['url'],
									'cd-icon'           => 'dashicons-admin-generic',
									'cd-type'           => 'wp_core',
									'cd-submenu-parent' => $item['url'],
								);

								// Output the checkbox and inputs HTML
								self::loop( $i, $options['title'], $options );
							}
						}
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<p class="button-controls">
			<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#wordpress-core" class="select-all">Select
					All</a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-wordpress-core">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * All other items present in the admin menu (from plugins or themes).
	 *
	 * @since Client Dash 1.6
	 */
	public static function plugin() {

		// Globalize the "parent" class object for access of public properties
		global $ClientDash_Core_Page_Settings_Tab_Menus, $cd_current_menu_role;

		$role = get_role( $cd_current_menu_role ? $cd_current_menu_role : 'administrator' );

		// Get core items
		$wp_core = self::$wp_core;

		// Separate out only the items added by plugins
		$menu_items = array();
		$i          = 0;
		foreach ( $ClientDash_Core_Page_Settings_Tab_Menus->original_admin_menu as $menu ) {
			$i ++;

			// Pass over if current role (for menu) doesn't have the capability
			if ( ! array_key_exists( $menu['capability'], $role->capabilities ) ) {
				$menu_item['disabled'] = true;
			}

			$menu_item = $menu;

			// Plugins has white space :|
			$menu_item['menu_title'] = trim( $menu_item['menu_title'] );

			// Deal with Comments title
			if ( strpos( $menu_item['menu_title'], 'Comments' ) !== false ) {
				$menu_item['menu_title'] = 'Comments';
			}

			// Deal with Plugins title
			if ( strpos( $menu_item['menu_title'], 'Plugins' ) !== false ) {
				$menu_item['menu_title'] = 'Plugins';
			}

			// Skip if a separator
			if ( $menu_item['menu_title'] == 'Separator' || strpos( $menu_item['menu_slug'], 'separator' ) !== false ) {
				continue;
			}

			// If icon is using "none" or "div", set accordingly
			if ( isset( $menu_item['icon_url'] ) && ( $menu_item['icon_url'] == 'none' || $menu_item['icon_url'] == 'div' ) ) {
				unset( $menu_item['icon_url'] );
			}

			// If in the WP Core list, this isn't a plugin page, so set it to disabled
			if ( array_key_exists( $menu_item['menu_title'], $wp_core ) ) {
				$menu_item['disabled'] = true;
			}

			// Submenus
			if ( isset( $menu['submenus'] ) ) {

				unset( $menu_item['submenus'] );

				foreach ( $menu['submenus'] as $submenu_item ) {

					// Pass over if current role (for menu) doesn't have the capability
					if ( ! array_key_exists( $submenu_item['capability'], $role->capabilities ) ) {
						continue;
					}

					// Skip separators
					if ( $submenu_item['menu_title'] == 'Separator' || strpos( $submenu_item['menu_slug'], 'separator' ) !== false ) {
						continue;
					}

					// Only add if not a WP Core or CD Core item
					// Variable webmaster title
					$title = $submenu_item['menu_slug'] == 'cd_webmaster' ? 'webmaster' : strtolower( $submenu_item['menu_title'] );
					if ( isset( $wp_core[ $menu_item['menu_title'] ]['submenus'] )
					     && ! array_key_exists( $submenu_item['menu_title'], $wp_core[ $menu_item['menu_title'] ]['submenus'] )
					     && ! array_key_exists( $title, ClientDash::$core_files ) && $title != 'client dash'
					) {
						$menu_item['submenus'][] = $submenu_item;
					}
				}
			}

			if ( isset( $menu_item['submenus'] ) || ! isset( $menu_item['disabled'] ) ) {
				$menu_items[ $i ] = $menu_item;
			}
		}

		if ( empty( $menu_items ) ) {

			echo '<p class="description">No items</p>';

			return;
		}
		?>

		<div id="plugin" class="posttypediv">

			<ul id="plugin-tabs" class="plugin-tabs add-menu-item-tabs">
				<li class="tabs">
					<a class="nav-tab-link" data-type="tabs-panel-plugin-toplevel"
					   href="/wp-admin/nav-menus.php?page-tab=most-recent#tabs-panel-plugin-toplevel">
						Top-level
					</a>
				</li>
				<li>
					<a class="nav-tab-link" data-type="tabs-panel-plugin-submenu"
					   href="/wp-admin/nav-menus.php?page-tab=all#tabs-panel-plugin-submenu">
						Sub-menu
					</a>
				</li>
			</ul>

			<div id="tabs-panel-plugin-toplevel" class="tabs-panel tabs-panel-active">
				<ul id="posttypechecklist-plugin" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( $menu_items as $item ) {
						$i --;

						if ( isset( $item['disabled'] ) && $item['disabled'] ) {
							continue;
						}

						// Set specific options
						$options = array(
							'title'   => $item['menu_title'],
							'url'     => $item['menu_slug'],
							'cd-icon' => isset( $item['icon_url'] ) ? $item['icon_url'] : '',
							'cd-type' => 'plugin',
						);

						// Output the checkbox and inputs HTML
						self::loop( $i, $options['title'], $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-plugin-submenu" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-plugin" class="categorychecklist form-no-clear">
					<?php

					foreach ( $menu_items as $item ) {

						if ( isset( $item['submenus'] ) ) {

							echo '<li class="cd-availableitems-separator">' . $item['menu_title'] . '</li>';

							$i = 0;
							foreach ( $item['submenus'] as $submenu_item ) {
								$i --;

								// Set specific options
								$options = array(
									'title'             => $submenu_item['menu_title'],
									'url'               => $submenu_item['menu_slug'],
									'cd-icon'           => 'dashicons-admin-generic',
									'cd-type'           => 'plugin',
									'cd-submenu-parent' => $item['menu_slug'],
								);

								// Output the checkbox and inputs HTML
								self::loop( $i, $options['title'], $options );
							}
						}
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<p class="button-controls">
			<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#plugin" class="select-all">Select
					All</a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-plugin">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * Client Dash core pages: Account, Help, Reports, Webmaster, Settings.
	 *
	 * @since Client Dash 1.6
	 */
	public static function cd_core() {
		global $ClientDash, $cd_current_menu_role;

		$role = get_role( $cd_current_menu_role ? $cd_current_menu_role : 'administrator' );
		?>

		<div id="clientdash-core" class="posttypediv">

			<div id="tabs-panel-clientdash-core-submenu" class="tabs-panel tabs-panel-active">
				<ul id="posttypechecklist-clientdash-core" class="categorychecklist form-no-clear">
					<?php
					$i = 0;
					foreach ( ClientDash::$core_files as $item_title => $submenus ) {
						$i --;

						// If not admin, don't show settings
						if ( ! array_key_exists( 'manage_options', $role->capabilities ) && $item_title == 'settings' ) {
							continue;
						}

						// Set specific options
						$options = array(
							'title'             => $item_title != 'settings' ? ucfirst( $item_title ) : 'Client Dash',
							'url'               => "cd_$item_title",
							'cd-icon'           => $ClientDash->option_defaults["dashicon_$item_title"],
							'cd-type'           => 'cd_core',
							'cd-submenu-parent' => $item_title != 'settings' ? 'index.php' : 'options-general.php',
						);

						// Output the checkbox and inputs HTML
						self::loop( $i, self::translate_id_to_name( $item_title ), $options );
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<p class="button-controls">
			<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#clientdash-core" class="select-all">Select
					All</a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-clientdash-core">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * The separator admin menu item.
	 *
	 * @since Client Dash 1.6
	 */
	public static function separator() {
		?>
		<div id="separator" class="posttypediv">

			<p class="description">Adds some vertical space between items.</p>

			<div id="tabs-panel-separator-submenu" class="tabs-panel tabs-panel-active" style="display: none;">
				<ul id="posttypechecklist-separator" class="categorychecklist form-no-clear">
					<li>
						<input type="checkbox" class="menu-item-checkbox" id="separator-checkbox"
						       name="menu-item[-1][menu-item-object-id]"
						       value="0" checked/>

						<input type="hidden" name="menu-item[-1][menu-item-title]" class="menu-item-title"
						       value="Separator"/>
						<input type="hidden" name="menu-item[-1][menu-item-cd-type]" class="cd-custom-menu-item"
						       value="separator"/>
					</li>
				</ul>
			</div>
			<!-- /.tabs-panel -->

			<p class="button-controls">

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-separator">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * Custom absolute uri's.
	 *
	 * @since Client Dash 1.6
	 */
	public static function custom_link() {		?>
		<div id="custom-link" class="posttypediv">

			<div id="tabs-panel-custom-link" class="tabs-panel-active categorychecklist form-no-clear">
				<ul id="posttypechecklist-custom-link" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<p id="menu-item-url-wrap">
								<label class="howto" for="menu-item-url">
									<span>URL</span>
									<input type="text" class="menu-item-url code"
									       placeholder="Link"
									       name="menu-item[-1][menu-item-url]" value="">
								</label>
							</p>

							<p id="menu-item-name-wrap">
								<label class="howto" for="custom-menu-item-name">
									<span>Link Text</span>
									<input type="text" class="menu-item-title" name="menu-item[-1][menu-item-title]"
									       value="" placeholder="Menu Item">
									<input type="checkbox" id="custom-checkbox" style="display: none;"
									       class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]"
									       value="0" checked>
								</label>
							</p>
						</label>

						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-cd-type" name="menu-item[-1][menu-item-cd-type]"
						       value="link">
						<input type="hidden" class="menu-item-cd-icon" name="menu-item[-1][menu-item-cd-icon]"
						       value="dashicons-admin-links">
					</li>
				</ul>
			</div>

			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
					       name="add-post-type-menu-item" id="submit-custom-link">
					<span class="spinner"></span>
				</span>
			</p>

		</div>
	<?php
	}
}