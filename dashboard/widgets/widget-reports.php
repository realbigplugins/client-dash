<?php
function cd_reports_widget_content() {
$widget = '<a href="'.cd_get_reports_url().'" class="cd cd-reports">
    <span data-code="f239" class="wp-menu-image cd-icon cd-title-icon"></span>
  </a>';
echo apply_filters('cd_reports_widget', $widget);
}