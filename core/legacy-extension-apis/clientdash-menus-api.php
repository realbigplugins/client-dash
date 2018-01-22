<?php
/**
 * Class ClientDash_Menus_API
 */

/**
 * Class ClientDash_Menus_API
 *
 * Exists only for the legacy extension API.
 *
 * @deprecated
 */
abstract class ClientDash_Menus_API {

	/**
	 * Adds another drop down to the menu group on the left of Settings -> Menus.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $name The name of the drop down.
	 * @param string|array $callback The callback function that provides HTML to the dropdown.
	 */
	public static function add_menu_group( $name, $callback ) {
	}

	/**
	 * This is a pseudo function of CD_AdminMenu_AvailableItems_Callbacks::loop().
	 *
	 * This function is well documented there in the file /core/tabs/settings/menus/availableitems-callbacks.php.
	 *
	 * @since 1.6.0
	 * @deprecated
	 */
	public static function loop( $i, $label, $options_args ) {
	}

	/**
	 * Handles all HTML output of menu items for the side sortables area.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $name The unique name of this menu group.
	 * @param array $items Array of tabs and items.
	 */
	public static function group_output( $name, $items ) {
	}

	/**
	 * Adds the menu items from the initial $items array provided above.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param array $items An array of all tabs and items.
	 */
	private static function _add_items( $items ) {
	}
}