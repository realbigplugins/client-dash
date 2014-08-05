<?php

/**
 * Class ClientDash_Page_Settings_Tab_Widgets
 *
 * Adds the core content section for Settings -> Widgets.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Widgets extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		$this->add_content_section( array(
			'name' => 'Core Settings Widgets',
			'page' => 'Settings',
			'tab' => 'Widgets',
			'callback' => array( $this, 'block_output' )
		));
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {
		global $ClientDash;
		?>
		<div id="cd-dash-widgets-left">
			<h3>Available Dashboard Widgets</h3>
			<p class="description">
				To activate a dashboard widget, simply drag it into the dashboard area. To delete, click on the "X".
			</p>

			<ul class="cd-dash-widgets-container">
				<?php foreach( $ClientDash->widgets as $widget ) { ?>
					<li class="cd-dash-widget">
						<h4 class="cd-dash-widget-title ui-draggable"><?php echo $widget['title']; ?></h4>
						<p><?php echo $widget['description']; ?></p>
					</li>
				<?php } ?>
			</ul>
		</div>

		<div id="cd-dash-widgets-right">
			<h3>Dashboard</h3>

			<ul id="cd-dash-widgets-droppable" class="cd-dash-widgets-container">
			</ul>
		</div>

		<div class="clear"></div>
	<?php
	}
}