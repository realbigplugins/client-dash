<?php
function cd_account_widget_content() {
$widget = '<a href="'.cd_get_account_url().'" class="cd cd-account">
      <span data-code="f337" class="wp-menu-image cd-icon cd-title-icon"></span>
    </a>';
echo apply_filters('cd_account_widget', $widget);
}