<?php

// TODO Make it so that when you use a checkbox item, it becomes disabled until deleted from the menu

/**
 * class CD_AdminMenu_AvailableItems_Callbacks
 *
 * Contains all callbacks for the admin menu available items.
 *
 * @since 1.6
 *
 * @package WordPress
 * @subpackage ClientDash
 */
class CD_AdminMenu_AvailableItems_Callbacks extends ClientDash_Core_Page_Settings_Tab_Admin_Menu {

	public static function default_post_types() {
		// Default options
		$options = array(
			'db-id'       => 0,
			'data-object' => '',
			'parent-id'   => 0,
			'type'        => 'custom',
			'title'       => '',
			'url'         => '',
			'classes'     => ''
		);

		// Custom meta to send
		// This needs to match $custom_meta from "./core/inc/adminmenu-walkerclass.php:~61"
		$custom_meta = array(
			'cd-type'             => '',
			'cd-post-type'        => 'post',
			'cd-object-type'      => '',
			'cd-original-title'   => '',
			'cd-icon'             => 'dashicons-admin-generic',
			'cd-separator-height' => 5,
			'cd-url'              => ''
		);

		return apply_filters( 'cd_availableitems_callback_defaults_posttype', array( $options, $custom_meta ) );
	}

	public static function post_types() {
		// Get all of our post types
		$all_post_types = get_post_types( array(
			'public' => true
		) );

		$post_types = [ ];
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

		// TODO Tabs don't fit well (taxonomies hangs off)
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

					// Reset defaults on each iteration
					$defaults    = self::default_post_types();
					$options     = $defaults[0];
					$custom_meta = $defaults[1];

					echo '<li>';
					// Set specific options
					$options['title'] = $post_type['title_plural'];
					$options['url']   = $post_type['listall_link'];

					// Now for the custom meta
					$custom_meta['cd-object-type']    = 'toplevel';
					$custom_meta['cd-post-type']      = $post_type['title'];
					$custom_meta['cd-original-title'] = $options['title'];
					$custom_meta['cd-icon']           = $icon[ $post_type['id'] ];
					$custom_meta['cd-type']           = 'post_type';
					$custom_meta['cd-url']            = $options['url'];

					// Allow data to be filtered

					$options = apply_filters( 'cd_adminmenu_availableitems_options_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $options );

					$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $custom_meta );

					?>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox"
						       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
						       value="<?php echo $post_type['id']; ?>"/>
						<?php echo $options['title']; ?>
					</label>
					<?php
					// Cycle through all the options
					foreach ( $options as $option_name => $option_value ) {
						?>
						<input type="hidden"
						       class="menu-item-<?php echo $option_name; ?>"
						       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
						       value="<?php echo $option_value; ?>"/>
					<?php
					}
					foreach ( $custom_meta as $meta_name => $meta_value ) {
						?>
						<input type="hidden"
						       class="cd-custom-menu-item"
						       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
						       value="<?php echo $meta_value; ?>"/>
					<?php
					}

					echo '</li>';
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

					// Reset defaults on each iteration
					$defaults    = self::default_post_types();
					$options     = $defaults[0];
					$custom_meta = $defaults[1];

					echo '<li>';

					// Set specific options
					$options['title'] = 'Add New ' . $post_type['title'];
					$options['url']   = $post_type['addnew_link'];

					// Now for the custom meta
					$custom_meta['cd-object-type']    = 'addnew';
					$custom_meta['cd-post-type']      = $post_type['title'];
					$custom_meta['cd-original-title'] = $options['title'];
					$custom_meta['cd-icon']           = $icon[ $post_type['id'] ];
					$custom_meta['cd-type']           = 'post_type';
					$custom_meta['cd-url']            = $options['url'];

					// Allow data to be filtered
					$options = apply_filters( 'cd_adminmenu_availableitems_options_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $options );

					$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $custom_meta );
					?>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox"
						       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
						       value="<?php echo $post_type['id']; ?>"/>
						<?php echo $options['title']; ?>
					</label>
					<?php
					// Cycle through all the options
					foreach ( $options as $option_name => $option_value ) {
						?>
						<input type="hidden"
						       class="menu-item-<?php echo $option_name; ?>"
						       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
						       value="<?php echo $option_value; ?>"/>
					<?php
					}
					foreach ( $custom_meta as $meta_name => $meta_value ) {
						?>
						<input type="hidden"
						       class="cd-custom-menu-item"
						       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
						       value="<?php echo $meta_value; ?>"/>
					<?php
					}

					echo '</li>';
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

					// Reset defaults on each iteration
					$defaults    = self::default_post_types();
					$options     = $defaults[0];
					$custom_meta = $defaults[1];

					echo '<li>';

					// Set specific data
					$options['title'] = 'All ' . $post_type['title'] . 's';
					$options['url']   = $post_type['listall_link'];

					// Now for the custom meta
					$custom_meta['cd-object-type']    = 'listall';
					$custom_meta['cd-post-type']      = $post_type['title'];
					$custom_meta['cd-original-title'] = $options['title'];
					$custom_meta['cd-icon']           = $icon[ $post_type['id'] ];
					$custom_meta['cd-type']           = 'post_type';
					$custom_meta['cd-url']            = $options['url'];

					// Allow data to be filtered
					$options = apply_filters( 'cd_adminmenu_availableitems_options_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $options );

					$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $custom_meta );

					// Special case for "Media" title
					if ( $post_type['id'] == 'media' ) {
						$options['title'] = 'Library';
					}
					?>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox"
						       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
						       value="<?php echo $post_type['id']; ?>"/>
						<?php echo $options['title']; ?>
					</label>
					<?php
					// Cycle through all the options
					foreach ( $options as $option_name => $option_value ) {
						?>
						<input type="hidden"
						       class="menu-item-<?php echo $option_name; ?>"
						       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
						       value="<?php echo $option_value; ?>"/>
					<?php
					}
					foreach ( $custom_meta as $meta_name => $meta_value ) {
						?>
						<input type="hidden"
						       class="cd-custom-menu-item"
						       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
						       value="<?php echo $meta_value; ?>"/>
					<?php
					}

					echo '</li>';
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

					// Reset defaults on each iteration
					$defaults    = self::default_post_types();
					$options     = $defaults[0];
					$custom_meta = $defaults[1];

					// Get available taxonomies
					$taxonomies = get_object_taxonomies( $post_type['id'], 'object' );

					if ( ! empty( $taxonomies ) ) {

						echo '<li class="taxonomy-title">' . $post_type['title_plural'] . '</li>';

						$i = 0;
						foreach ( $taxonomies as $taxonomy ) {
							$i --;

							echo '<li>';

							// Set specific options
							$options['title'] = $taxonomy->labels->name;
							$options['url']   = "edit-tags.php?taxonomy={$taxonomy->name}";

							// Now for the custom meta
							$custom_meta['cd-object-type']    = 'listall';
							$custom_meta['cd-post-type']      = $post_type['title'];
							$custom_meta['cd-original-title'] = $options['title'];
							$custom_meta['cd-icon']           = $icon[ $post_type['id'] ];
							$custom_meta['cd-type']           = 'taxonomy';
							$custom_meta['cd-url']            = $options['url'];

							// Allow data to be filtered
							$options = apply_filters( 'cd_adminmenu_availableitems_options_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $options );

							$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_' . $post_type['id'] . '_' . $custom_meta['cd-object-type'], $custom_meta );
							?>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox"
								       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
								       value="<?php echo $taxonomy->labels->name; ?>"/>
								<?php echo $taxonomy->labels->name; ?>
							</label>
							<?php
							// Cycle through all the options
							foreach ( $options as $option_name => $option_value ) {
								?>
								<input type="hidden"
								       class="menu-item-<?php echo $option_name; ?>"
								       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
								       value="<?php echo $option_value; ?>"/>
							<?php
							}
							foreach ( $custom_meta as $meta_name => $meta_value ) {
								?>
								<input type="hidden"
								       class="cd-custom-menu-item"
								       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
								       value="<?php echo $meta_value; ?>"/>
							<?php
							}

							echo '</li>';
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

	public static function wp_core() {
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
					foreach ( ClientDash_Core_Page_Settings_Tab_Admin_Menu::$wp_core as $item_title => $item ) {
						$i --;

						// Reset defaults on each iteration
						$defaults    = self::default_post_types();
						$options     = $defaults[0];
						$custom_meta = $defaults[1];

						echo '<li>';
						// Set specific options
						$options['title'] = $item_title;
						$options['url']   = $item['url'];

						// Now for the custom meta
						$custom_meta['cd-type']           = 'wp_core';
						$custom_meta['cd-original-title'] = $options['title'];
						$custom_meta['cd-icon']           = $item['icon'];
						$custom_meta['cd-url']            = $item['url'];

						// Allow data to be filtered
						$options     = apply_filters( 'cd_adminmenu_availableitems_options_wp_core_' . $options['title'] . '_' . $options['url'], $options );
						$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_wp_core_' . $options['title'] . '_' . $options['url'], $custom_meta );

						?>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox"
							       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
							       value="<?php echo strtolower( $item_title ); ?>"/>
							<?php echo $options['title']; ?>
						</label>
						<?php
						// Cycle through all the options
						foreach ( $options as $option_name => $option_value ) {
							?>
							<input type="hidden"
							       class="menu-item-<?php echo $option_name; ?>"
							       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
							       value="<?php echo $option_value; ?>"/>
						<?php
						}
						foreach ( $custom_meta as $meta_name => $meta_value ) {
							?>
							<input type="hidden"
							       class="cd-custom-menu-item"
							       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
							       value="<?php echo $meta_value; ?>"/>
						<?php
						}

						echo '</li>';
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-wordpress-core-submenu" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-wordpress-core" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( ClientDash_Core_Page_Settings_Tab_Admin_Menu::$wp_core as $item_title => $item ) {
						$i --;

						if ( isset( $item['submenus'] ) ) {

							echo '<li class="wp-core-title">' . $item_title . '</li>';
							foreach ( $item['submenus'] as $submenu_item_title => $submenu_item_url ) {

								// Reset defaults on each iteration
								$defaults    = self::default_post_types();
								$options     = $defaults[0];
								$custom_meta = $defaults[1];

								echo '<li>';
								// Set specific options
								$options['title'] = $submenu_item_title;
								$options['url']   = $submenu_item_url;

								// Now for the custom meta
								$custom_meta['cd-type']           = 'wp_core';
								$custom_meta['cd-original-title'] = $options['title'];
								$custom_meta['cd-url']            = $options['url'];
								$custom_meta['cd-icon']           = 'dashicons-admin-generic';

								// Allow data to be filtered
								$options     = apply_filters( 'cd_adminmenu_availableitems_options_wp_core_' . $options['title'] . '_' . $options['url'], $options );
								$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_wp_core_' . $options['title'] . '_' . $options['url'], $custom_meta );

								?>
								<label class="menu-item-title">
									<input type="checkbox" class="menu-item-checkbox"
									       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
									       value="<?php echo strtolower( str_replace( ' ', '_', $options['title'] ) ); ?>"/>
									<?php echo $options['title']; ?>
								</label>
								<?php
								// Cycle through all the options
								foreach ( $options as $option_name => $option_value ) {
									?>
									<input type="hidden"
									       class="menu-item-<?php echo $option_name; ?>"
									       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
									       value="<?php echo $option_value; ?>"/>
								<?php
								}
								foreach ( $custom_meta as $meta_name => $meta_value ) {
									?>
									<input type="hidden"
									       class="cd-custom-menu-item"
									       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
									       value="<?php echo $meta_value; ?>"/>
								<?php
								}

								echo '</li>';
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

	public static function plugin() {

		// Get the original menu
		$orig_menu = ClientDash_Core_Page_Settings_Tab_Admin_Menu::return_orig_admin_menu();

		// Separate out only the items added by plugins
		$menu_items = [ ];
		foreach ( $orig_menu as $menu ) {

			$sorted = ClientDash_Core_Page_Settings_Tab_Admin_Menu::sort_original_admin_menu( $menu );

			if ( $sorted[1]['cd-type'] == 'plugin' ) {
				$menu_items[] = $menu;
			}
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

						// Reset defaults on each iteration
						$defaults    = self::default_post_types();
						$options     = $defaults[0];
						$custom_meta = $defaults[1];

						echo '<li>';
						// Set specific options
						$options['title'] = $item['menu_title'];
						$options['url']   = $item['menu_slug'];

						// Now for the custom meta
						$custom_meta['cd-type']           = 'plugin';
						$custom_meta['cd-original-title'] = $options['title'];
						$custom_meta['cd-icon']           = $item['icon_url'];
						$custom_meta['cd-url']            = $item['url'];

						// Allow data to be filtered
						$options     = apply_filters( 'cd_adminmenu_availableitems_options_plugin_' . $options['title'] . '_' . $options['url'], $options );
						$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_plugin_' . $options['title'] . '_' . $options['url'], $custom_meta );

						?>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox"
							       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
							       value="<?php echo strtolower( $options['title'] ); ?>"/>
							<?php echo $options['title']; ?>
						</label>
						<?php
						// Cycle through all the options
						foreach ( $options as $option_name => $option_value ) {
							?>
							<input type="hidden"
							       class="menu-item-<?php echo $option_name; ?>"
							       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
							       value="<?php echo $option_value; ?>"/>
						<?php
						}
						foreach ( $custom_meta as $meta_name => $meta_value ) {
							?>
							<input type="hidden"
							       class="cd-custom-menu-item"
							       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
							       value="<?php echo $meta_value; ?>"/>
						<?php
						}

						echo '</li>';
					}
					?>

				</ul>
			</div>
			<!-- /.tabs-panel -->

			<div id="tabs-panel-plugin-submenu" class="tabs-panel tabs-panel-inactive">
				<ul id="posttypechecklist-plugin" class="categorychecklist form-no-clear">
					<?php

					$i = 0;
					foreach ( $menu_items as $item ) {
						$i --;

						// Reset defaults on each iteration
						$defaults    = self::default_post_types();
						$options     = $defaults[0];
						$custom_meta = $defaults[1];

						if ( isset( $item['submenus'] ) ) {

							echo '<li class="plugin-title">' . $item['menu_title'] . '</li>';
							foreach ( $item['submenus'] as $submenu_item ) {

								// Reset defaults on each iteration
								$defaults    = self::default_post_types();
								$options     = $defaults[0];
								$custom_meta = $defaults[1];

								echo '<li>';
								// Set specific options
								$options['title'] = $submenu_item['menu_title'];
								$options['url']   = $submenu_item['menu_slug'];

								// Now for the custom meta
								$custom_meta['cd-type']           = 'plugin';
								$custom_meta['cd-original-title'] = $options['title'];
								$custom_meta['cd-url']            = $options['url'];
								$custom_meta['cd-icon']           = 'dashicons-admin-generic';

								// Allow data to be filtered
								$options     = apply_filters( 'cd_adminmenu_availableitems_options_wp_core_' . $options['title'] . '_' . $options['url'], $options );
								$custom_meta = apply_filters( 'cd_adminmenu_availableitems_custommeta_wp_core_' . $options['title'] . '_' . $options['url'], $custom_meta );

								?>
								<label class="menu-item-title">
									<input type="checkbox" class="menu-item-checkbox"
									       name="menu-item[<?php echo $i; ?>][menu-item-object-id]"
									       value="<?php echo strtolower( str_replace( ' ', '_', $options['title'] ) ); ?>"/>
									<?php echo $options['title']; ?>
								</label>
								<?php
								// Cycle through all the options
								foreach ( $options as $option_name => $option_value ) {
									?>
									<input type="hidden"
									       class="menu-item-<?php echo $option_name; ?>"
									       name="menu-item[<?php echo $i; ?>][menu-item-<?php echo $option_name; ?>]"
									       value="<?php echo $option_value; ?>"/>
								<?php
								}
								foreach ( $custom_meta as $meta_name => $meta_value ) {
									?>
									<input type="hidden"
									       class="cd-custom-menu-item"
									       name="menu-item[<?php echo $i; ?>][custom-meta-<?php echo $meta_name; ?>]"
									       value="<?php echo $meta_value; ?>"/>
								<?php
								}

								echo '</li>';
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
}