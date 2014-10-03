<?php

/**
 * Class ClientDash_Widget_API
 *
 * This class provides static functions to use within widget extensions.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Extensions
 *
 * @since Client Dash 1.6
 */
abstract class ClientDash_Menus_API extends ClientDash_Functions {

	/**
	 * Adds another drop down to the menu group on the left of Settings -> Menus.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $name The name of the drop down.
	 * @param string|array $callback The callback function that provides HTML to the dropdown.
	 */
	public static function add_menu_group( $name, $callback ) {

		global $ClientDash_Core_Page_Settings_Tab_Menus;

		$ClientDash_Core_Page_Settings_Tab_Menus->side_sortables[ self::translate_name_to_id( $name ) ] = array(
			'id'       => self::translate_name_to_id( $name ),
			'title'    => $name,
			'callback' => $callback,
		);
	}

	/**
	 * This is a pseudo function of CD_AdminMenu_AvailableItems_Callbacks::loop().
	 *
	 * This function is well documented there in the file /core/tabs/settings/menus/availableitems-callbacks.php.
	 *
	 * @since Client Dash 1.6
	 */
	public static function loop( $i, $label, $options_args ) {

		CD_AdminMenu_AvailableItems_Callbacks::loop( $i, $label, $options_args );
	}

	/**
	 * Handles all HTML output of menu items for the side sortables area.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $name The unique name of this menu group.
	 * @param array $items Array of tabs and items.
	 */
	public static function group_output( $name, $items ) {

		$ID = self::translate_name_to_id( $name );
		?>
		<div id="<?php echo $ID; ?>" class="posttypediv">

			<?php
			if ( ! empty( $items ) && count( $items ) > 1 ) {

				echo "<ul id='$ID-tabs' class='posttype-tabs add-menu-item-tabs'>";

				foreach ( $items as $tab_name => $all_items ) {

					$tab_ID = self::translate_name_to_id( $tab_name );
					?>
					<li class="tabs">
						<a class="nav-tab-link" data-type="tabs-panel-<?php echo "$ID-$tab_ID"; ?>"
						   href="/wp-admin/nav-menus.php?page-tab=most-recent#tabs-panel-<?php echo "$ID-$tab_ID"; ?>">
							<?php echo $tab_name; ?>
						</a>
					</li>
				<?php
				}

				echo '</ul>';
			}

			$i = 0;
			foreach ( $items as $tab_name => $all_items ) {
				$i++;

				$active = $i == 1 ? 'active': 'inactive';

				$tab_ID = self::translate_name_to_id( $tab_name );
				?>
				<div id="tabs-panel-<?php echo "$ID-$tab_ID"; ?>" class="tabs-panel tabs-panel-<?php echo $active; ?>">
					<ul id="posttypechecklist-<?php echo $ID; ?>" class="categorychecklist form-no-clear">
						<?php

						self::_add_items( $all_items );
						?>

					</ul>
				</div>
				<!-- /.tabs-panel -->
				<?php
			}
			?>

			<p class="button-controls">
			<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#<?php echo $ID; ?>" class="select-all">Select
					All</a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu"
				       name="add-post-type-menu-item" id="submit-<?php echo $ID; ?>">
				<span class="spinner"></span>
			</span>
			</p>

		</div>
	<?php
	}

	/**
	 * Adds the menu items from the initial $items array provided above.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $items An array of all tabs and items.
	 */
	private static function _add_items( $items ) {

		// If no items, bail
		if ( empty( $items ) ) {
			self::error_nag( 'ERROR: You did not enter any items.' );

			return;
		}

		$i = 0;
		foreach ( $items as $item_label => $item ) {

			// Separator
			if ( is_string( $item) && strtolower( $item ) == 'separator' ) {
				echo '<li class="cd-availableitems-separator">' . $item_label . '</li>';
				continue;
			}

			$i --;

			$options = array(
				'title'   => $item_label,
				'url'     => $item['url'],
				'cd-icon' => isset( $item['icon'] ) ? $item['icon'] : 'dashicons-admin-generic',
				'cd-type' => isset( $item['type'] ) ? $item['type'] : 'plugin',
			);

			// Output the checkbox and inputs HTML
			self::loop( $i, $options['title'], $options );
		}
	}
}