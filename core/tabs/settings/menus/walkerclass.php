<?php

// TODO When adding post type sub-menu items (add new, list all, tax, etc.), display what post type each item belongs to somewhere in the title

/**
 * Create HTML list of nav menu input items (MODIFIED FOR CD).
 *
 * @package WordPress
 * @subpackage Client Dash
 * @since 1.6.0
 * @uses Walker_Nav_Menu
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

		// TODO Remove excess

		global $_wp_nav_menu_max_depth, $cd_current_menu_id, $errors;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		// Remove the transient so it resets
		if ( get_transient( "cd_adminmenu_output_$cd_current_menu_id" ) ) {
			delete_transient( "cd_adminmenu_output_$cd_current_menu_id" );
		}

		// Get the action
		$action = isset( $_POST['action'] ) ? $_POST['action'] : null;

		// This needs to match $custom_meta keys from "./core/inc/adminmenu-availableitems-callbacks.php:~31"
		$custom_meta = array(
			'cd-type',
			'cd-post-type',
			'cd-object-type',
			'cd-original-title',
			'cd-icon',
			'cd-separator-height',
			'cd-url',
			'cd-page-title'
		);

		// Save any cd custom meta
		if ( $action == 'add-menu-item' ) {
			$menu_item = array_shift( $_POST['menu-item'] );
			foreach ( $custom_meta as $meta ) {
				if ( isset( $menu_item["custom-meta-$meta"] ) ) {
					update_post_meta( $item->ID, $meta, $menu_item["custom-meta-$meta"] );
					$item_meta[ str_replace( '-', '_', str_replace( 'cd-', '', $meta ) ) ] = $menu_item["custom-meta-$meta"];
				}
			}
		} elseif ( $action == 'update' ) {
			foreach ( $custom_meta as $meta ) {
				// The meta inside of POST
				if ( isset( $_POST["menu-item-$meta"] ) ) {
					update_post_meta( $item->ID, $meta, $_POST["menu-item-$meta"][ $item->ID ] );
					$item_meta[ str_replace( '-', '_', str_replace( 'cd-', '', $meta ) ) ] = $_POST["menu-item-$meta"][ $item->ID ];
				}
			}

			// Determine if there is a parent item with a duplicate slug
			$duplicate_parent_slug = false;
			foreach ( $_POST['menu-item-cd-url'] as $menu_item_ID => $menu_item_slug ) {

				// If the current slug equals this items slug
				// and this item's ID is NOT the current ID
				// and the this item is a not a child
				// and the current item is not a child
				if ( $menu_item_slug == $_POST['menu-item-cd-url'][ $item->ID ]
				     && $item->ID != $menu_item_ID
				     && $_POST['menu-item-parent-id'][ $menu_item_ID ] == '0'
				     && $_POST['menu-item-parent-id'][ $item->ID ] == '0'
				) {
					$duplicate_parent_slug = true;
					break;
				}
			}

			update_post_meta( $item->ID, 'cd-duplicate-parent-slug', $duplicate_parent_slug );
		}

		foreach ( $custom_meta as $meta ) {
			if ( ! isset( $item_meta[ str_replace( '-', '_', str_replace( 'cd-', '', $meta ) ) ] ) ) {
				$item_meta[ str_replace( '-', '_', str_replace( 'cd-', '', $meta ) ) ] = get_post_meta( $item->ID, $meta, true );
			}
		}

		if ( ! isset( $item_meta['duplicate_parent_slug'] ) ) {
			$item_meta['duplicate_parent_slug'] = get_post_meta( $item->ID, 'cd-duplicate-parent-slug', true );
		}

		// Get all custom meta
		extract( $item_meta );

		ob_start();
		$item_id      = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		// Decide what the type label is (because all cd items are technically "custom")
		if ( ! isset( $type ) || empty( $type ) ) {
			$type = 'custom';
		}
		switch ( $type ) {
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
			case 'plugin':
				$type_label = 'Plugin / Theme';
				break;
			default:
				$type_label = 'Custom';
		}

		// Create the item classes
		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' )
		);

		// A few extra conditional classes
		if ( $type == 'separator' ) {
			$classes[] = 'menu-item-separator';
		}

		// Add an error message for duplicate PARENT slugs
		if ( $duplicate_parent_slug ) {
			$error_msg = 'There are multiple top-level menu items that go to the same place. This can cause issues with the menu output. Please correct and save';

			if ( ! in_array( $error_msg, $errors ) ) {
				$errors[] = $error_msg;
			}

			// Also add a class to the menu item for highlighting
			$classes[]                 = 'duplicate-parent-slug';
		}

		// Decide of the "submenu" text should show or not
		$submenu_text = '';
		if ( 0 == $depth ) {
			$submenu_text = 'style="display: none;"';
		}
		?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title">
					<?php if ( $type != 'separator' ) : ?>
						<span class="dashicons <?php echo ! empty( $icon ) ? $icon : 'dashicons-admin-generic';
						echo $depth != 0 ? ' hidden' : '' ?>"></span>
					<?php endif; ?>
					<span class="menu-item-title"><?php echo esc_html( strip_tags( $item->title ) ); ?></span>
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
										'menu-item' => $item_id,
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
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down' ); ?>">&#8595;</abbr></a>
						</span>

						<a class="item-edit" id="edit-<?php echo $item_id; ?>"
						   title="<?php esc_attr_e( 'Edit Menu Item' ); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">

			<div <?php echo $type == 'separator' ? 'style="display: none;"' : ''; ?> >

				<?php if ( $type == 'custom' ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL' ); ?><br/>
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
							       class="widefat code edit-menu-item-cd-url"
							       name="menu-item-cd-url[<?php echo $item_id; ?>]"
							       value="<?php echo esc_attr( $url ); ?>"/>
						</label>
					</p>
				<?php else: ?>
					<input type="hidden" name="menu-item-cd-url[<?php echo $item_id; ?>]"
					       value="<?php echo esc_attr( $url ); ?>"/>
				<?php endif; ?>

				<?php
				// Unfortunately, I can't just not have this field exist for separators, as it's required
				// So I've hidden it from view instead
				?>
				<p class="description description-thin <?php echo $type == 'separator' ? 'hidden' : ''; ?>">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label' ); ?><br/>
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>"
						       class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]"
						       value="<?php echo esc_html( $item->title ); ?>"/>
					</label>
				</p>

				<?php if ( $type != 'separator' ) : ?>
					<p class="description description-thin">
						<label for="edit-menu-item-icon-<?php echo $item_id; ?>">
							<?php _e( 'Menu Icon' ); ?><br/>
							<input type="text" id="edit-menu-item-cd-icon-<?php echo $item_id; ?>"
							       class="widefat edit-menu-item-cd-icon" name="menu-item-cd-icon[<?php echo $item_id; ?>]"
							       value="<?php echo esc_html( ! empty( $icon ) ? $icon : 'dashicons-admin-generic' ); ?>"/>
						</label>
					</p>
				<?php endif; ?>

				<?php // TODO Enable this, because I do believe it's possible ?>
				<?php // Not using for now because I can't figure out how to attach classes to the menu items ?>
				<!--			<p class="field-css-classes description description-thin">-->
				<!--				<label for="edit-menu-item-classes---><?php //echo $item_id; ?><!--">-->
				<!--					--><?php //_e( 'CSS Classes (optional)' ); ?><!--<br/>-->
				<!--					<input type="text" id="edit-menu-item-classes---><?php //echo $item_id; ?><!--"-->
				<!--					       class="widefat code edit-menu-item-classes"-->
				<!--					       name="menu-item-classes[--><?php //echo $item_id; ?><!--]"-->
				<!--					       value="-->
				<?php //echo esc_attr( implode( ' ', $item->classes ) ); ?><!--"/>-->
				<!--				</label>-->
				<!--			</p>-->

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

				<?php if ( $original_title != $item->title && $type != 'separator' ) : ?>
					<p class="link-to-original">
						Original Title: <b style="font-style: normal;"><?php echo $original_title; ?></b>
					</p>
				<?php endif; ?>

				<?php if ( $original_title != 'Client Dash' ) : ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action'    => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e( 'Remove' ); ?></a>
					<span class="meta-sep hide-if-no-js"> | </span>
				<?php endif; ?>

				<a
					class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>"
					href="<?php echo esc_url( add_query_arg( array(
						'edit-menu-item' => $item_id,
						'cancel'         => time()
					), admin_url( 'nav-menus.php' ) ) );
					?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e( 'Cancel' ); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
			       value="<?php echo $item_id; ?>"/>
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->object_id ); ?>"/>
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->object ); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->menu_order ); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->type ); ?>"/>
		</div>
		<!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}