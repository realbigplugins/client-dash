<?php
function cd_help_widget_content() {
$widget = '<a href="'.cd_get_help_url().'" class="cd cd-help">
    <span data-code="f223" class="wp-menu-image cd-icon cd-title-icon"></span>
  </a>';
echo apply_filters('cd_help_widget', $widget);
}