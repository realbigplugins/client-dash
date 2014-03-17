<?php
add_action('admin_enqueue_scripts', 'cd_styles');
function cd_styles() {
//Now we actually register the stylesheet
wp_enqueue_style("client-dash", plugins_url("/css/style.css", __FILE__), FALSE); 
}
?>