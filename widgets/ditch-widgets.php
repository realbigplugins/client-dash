<?php
function cd_ditch_default_widgets() {
	if (current_user_can( 'publish_posts' )) {
	remove_meta_box('dashboard_browser_nag', 'dashboard', 'advanced');
	remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
	remove_meta_box('dashboard_activity', 'dashboard', 'normal');
	remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
	remove_meta_box('dashboard_primary', 'dashboard', 'side');
}
}
add_action('wp_dashboard_setup', 'cd_ditch_default_widgets');
?>