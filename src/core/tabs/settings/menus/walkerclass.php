<?php

/**
 * Create HTML list of nav menu input items (MODIFIED FOR CD).
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @category Menus
 *
 * @uses Walker_Nav_Menu
 *
 * @since Client Dash1.6.0
 */
class Walker_Nav_Menu_Edit_CD extends Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @since 1.6.0
	 *
	 * @param string $output Passed by reference.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker_Nav_Menu::end_lvl()
	 *
	 * @since 1.6.0
	 *
	 * @param string $output Passed by reference.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 * @since 1.6.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 * @param int $id Not used.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		global $errors, $ClientDash, $cd_current_menu_role;

		// Add disabled visual cue for CD Core pages that are currently disabled for current role
		// If Administrator menu, skip because all items are visible always
		$visible = true;
		if ( array_key_exists( strtolower( $item->original_title ), ClientDash::$core_files )
		     && $cd_current_menu_role != 'administrator'
		) {

			// Get role options for the current menu item (or default settings)
			$role_options = get_option( 'cd_content_sections_roles', $ClientDash->option_defaults['content_sections_roles'] );
			$role_options = $role_options[ strtolower( $item->original_title ) ];

			// Assume not visible until proven otherwise (if even one content section is visible, then
			// the current page will also be visible)
			$visible = false;
			foreach ( $role_options as $content_sections ) {
				foreach ( $content_sections as $roles ) {
					foreach ( $roles as $role => $visibility ) {
						if ( $role == $cd_current_menu_role && $visibility == 'visible' ) {
							$visible = true;
						}
					}
				}
			}
		}

		// Args to remove from the links for no-js actions
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		// Decide what the type label is (because all cd items are technically "custom")
		switch ( $item->cd_type ) {
			case 'post_type':
				$type_label = 'Post Type';
				break;
			case 'taxonomy':
				$type_label = 'Post Type (Taxonomy)';
				break;
			case 'separator':
				$type_label = 'Separator';
				break;
			case 'wp_core':
				$type_label = 'WordPress';
				break;
			case 'cd_core':
				$type_label = 'Client Dash';
				break;
			case 'plugin':
				$type_label = 'Plugin / Theme';
				break;
			default:
				$type_label = 'Link';
		}

		// Create the item classes
		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item->ID == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		// A few extra conditional classes
		if ( $item->cd_type == 'separator' ) {
			$classes[] = 'menu-item-separator';
		}

		if ( ! $visible ) {
			$classes[] = 'menu-item-cd-disabled';
		}

		// Item errors
		$item_errors = array();

		if ( ! $visible ) {
			$item_errors[] = $ClientDash->error_nag( 'This page has no content visible for the current menu\'s role. To correct this, please visit <a href="' . $ClientDash->get_settings_url( 'display' ) . '">Display Settings</a>', 'read', false );
		}

		// Decide of the "submenu" text should show or not
		$submenu_text = '';
		if ( 0 == $depth ) {
			$submenu_text = 'style="display: none;"';
		}

		// Webmaster title
		$cd_webmaster = false;
		if ( $item->original_title == 'Webmaster' && $item->url == 'cd_webmaster' ) {
			$item->title  = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );
			$cd_webmaster = true;
		}

		// Deal with the icon being an icon, an image, or a data image (taken from WP core)
		// wp-admin/menu-header.php:~89
		$img = '<img src="' . $item->cd_icon . '" alt="" />';
		$img_style = '';
		$img_class = '';
		if ( 'none' === $item->cd_icon || 'div' === $item->cd_icon ) {
			$img = '';
		} elseif ( 0 === strpos( $item->cd_icon, 'data:image/svg+xml;base64,' ) ) {
			$img = '';
			$img_style = ' style="background-image:url(\'' . esc_attr( $item->cd_icon ) . '\')"';
			$img_class = ' svg';
		} elseif ( ! empty( $item->cd_icon ) ) {
			$img = '';
			$img_class = ' dashicons ' . sanitize_html_class( $item->cd_icon );
		} elseif ( empty( $item->cd_icon ) ) {
			$img = '';
			$img_class = ' dashicons dashicons-admin-generic';
		}

		ob_start();
		?>
	<li id="menu-item-<?php echo $item->ID; ?>" class="<?php echo implode( ' ', $classes ); ?>">
	<dl class="menu-item-bar">
		<dt class="menu-item-handle">
				<span class="item-title">
					<?php if ( $item->cd_type != 'separator' ) : ?>
						<span class='menu-item-icon<?php echo $img_class; echo $depth != 0 ? ' hidden' : ''; ?>'<?php echo $img_style; ?>>
							<?php echo $img; ?>
						</span>
					<?php endif; ?>
					<span class="menu-item-title">
						<?php echo ! empty( $item->title ) ? esc_html( strip_tags( $item->title ) ) : '(no title)'; ?>
					</span>
					<span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span>
				</span>
					<span class="item-controls">
						<span class="item-type"><?php echo $type_label; ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-up-menu-item',
										'menu-item' => $item->ID,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e( 'Move up' ); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-down-menu-item',
										'menu-item' => $item->ID,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down' ); ?>">&#8595;</abbr></a>
						</span>

						<a class="item-edit" id="edit-<?php echo $item->ID; ?>"
						   title="<?php esc_attr_e( 'Edit Menu Item' ); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item->ID == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item->ID, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item->ID ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
		</dt>
	</dl>

	<div class="menu-item-settings" id="menu-item-settings-<?php echo $item->ID; ?>">

		<?php
		// Print out all set error messages
		if ( ! empty( $item_errors ) ) {
			foreach ( $item_errors as $error ) {
				echo $error;
			}
		}
		?>

		<div <?php echo $item->cd_type == 'separator' ? 'style="display: none;"' : ''; ?> >

			<?php if ( $item->cd_type == 'link' ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item->ID; ?>">
						<?php _e( 'URL' ); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo $item->ID; ?>"
						       class="widefat code edit-menu-item-url"
						       name="menu-item-url[<?php echo $item->ID; ?>]"
						       value="<?php echo esc_attr( $item->url ); ?>"/>
					</label>
				</p>
			<?php else: ?>
				<input type="hidden" name="menu-item-url[<?php echo $item->ID; ?>]"
				       value="<?php echo esc_attr( $item->url ); ?>"/>
			<?php endif; ?>

			<?php if ( $item->cd_type != 'separator' ) : ?>
				<p class="description description-thin <?php echo $item->cd_type == 'separator' ? 'hidden' : ''; ?>">
					<label for="edit-menu-item-title-<?php echo $item->ID; ?>">
						<?php _e( 'Navigation Label' ); ?><br/>

						<?php
						// Don't allow title modification with Webmaster menu item
						if ( $cd_webmaster == true ) :
							?>
							<input type="text" value="<?php echo esc_html( $item->title ); ?>"
							       title="Change in Webmaster settings" disabled/>
						<?php else: ?>
							<input type="text" id="edit-menu-item-title-<?php echo $item->ID; ?>"
							       class="widefat edit-menu-item-title"
							       name="menu-item-title[<?php echo $item->ID; ?>]"
							       value="<?php echo esc_html( $item->title ); ?>"/>
						<?php endif; ?>

					</label>
				</p>

				<div class="cd-menu-icon-field">
					<p class="description description-thin">
						<label for="edit-menu-item-cd-icon-<?php echo $item->ID; ?>">
							<?php _e( 'Menu Icon' ); ?><br/>
							<input type="text" id="edit-menu-item-cd-icon-<?php echo $item->ID; ?>"
							       class="widefat edit-menu-item-cd-icon"
							       name="menu-item-cd-icon[<?php echo $item->ID; ?>]"
							       value="<?php echo esc_html( ! empty( $item->cd_icon ) ? $item->cd_icon : '' ); ?>"
							       placeholder="dashicons-admin-generic" />
						</label>
					</p>
					<ul class="cd-menu-icon-selector">
						<?php foreach ( ClientDash_Core_Page_Settings_Tab_Icons::$icons as $icon ) : ?>
							<li data-icon="<?php echo $icon; ?>">
								<span class="dashicons <?php echo $icon; ?>"></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<p class="field-css-classes description description-wide">
					<label for="edit-menu-item-classes-<?php echo $item->ID; ?>">
							<span class="cd-extra-params"
							      onclick="cdMain.updown('edit-menu-item-classes-<?php echo $item->ID; ?>')">
						<?php _e( 'CSS Classes (only applies to top-level menu items)' ); ?>
								</span>
						<input type="text" id="edit-menu-item-classes-<?php echo $item->ID; ?>"
						       class="widefat code edit-menu-item-classes"
						       name="menu-item-classes[<?php echo $item->ID; ?>]"
						       value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"
						       style="display: none;"/>
					</label>
				</p>

				<p class="description description-wide">
					<label for="edit-menu-item-cd-params-<?php echo $item->ID; ?>">
							<span class="cd-extra-params"
							      onclick="cdMain.updown('edit-menu-item-cd-params-<?php echo $item->ID; ?>')">
								<?php _e( 'Extra Parameters' ); ?>
							</span>
						<input type="text" id="edit-menu-item-cd-params-<?php echo $item->ID; ?>"
						       class="widefat code edit-menu-item-cd-params"
						       name="menu-item-cd-params[<?php echo $item->ID; ?>]"
						       value="<?php echo $item->cd_params; ?>"
						       style="display: none;"/>
					</label>
				</p>
			<?php else: ?>
				<input type="hidden"
				       name="menu-item-title[<?php echo $item->ID; ?>]"
				       value="Separator"/>
			<?php endif; ?>

			<p class="field-move hide-if-no-js description description-wide">
				<label>
					<span><?php _e( 'Move' ); ?></span>
					<a href="#" class="menus-move-up"><?php _e( 'Up one' ); ?></a>
					<a href="#" class="menus-move-down"><?php _e( 'Down one' ); ?></a>
					<a href="#" class="menus-move-left"></a>
					<a href="#" class="menus-move-right"></a>
					<a href="#" class="menus-move-top"><?php _e( 'To the top' ); ?></a>
				</label>
			</p>
		</div>

		<div class="menu-item-actions description-wide submitbox">

			<?php if ( $item->original_title != $item->title && $item->original_title != 'Client Dash ORIG' && $item->cd_type != 'separator' ) : ?>
				<p class="link-to-original">
					Original Title: <b style="font-style: normal;"><?php echo $item->original_title; ?></b>
				</p>
			<?php endif; ?>

			<?php if ( $item->original_title != 'Client Dash ORIG' ) : ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo $item->ID; ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item->ID,
						),
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item->ID
				); ?>"><?php _e( 'Remove' ); ?></a>
				<span class="meta-sep hide-if-no-js"> | </span>
			<?php endif; ?>

			<a
				class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item->ID; ?>"
				href="<?php echo esc_url( add_query_arg( array(
					'edit-menu-item' => $item->ID,
					'cancel'         => time()
				), admin_url( 'nav-menus.php' ) ) );
				?>#menu-item-settings-<?php echo $item->ID; ?>"><?php _e( 'Cancel' ); ?></a>
		</div>

		<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item->ID; ?>]"
		       value="<?php echo $item->ID; ?>"/>
		<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item->ID; ?>]"
		       value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
		<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item->ID; ?>]"
		       value="<?php echo esc_attr( $item->menu_order ); ?>"/>
	</div>
	<!-- .menu-item-settings-->
	<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}