<?php
/*
* All core functions go here
*/

function cd_get_active_widgets() {
  global $wp_meta_boxes, $cd_widgets;

  // Initialize
  $active_widgets = array();

  // This lovely, crazy loop is what gathers all of the widgets and organizes it into MY array
  foreach ($wp_meta_boxes['dashboard'] as $context => $widgets) {
    foreach ($widgets as $priority => $widgets) {
      foreach ($widgets as $id => $values) {
        $active_widgets[$id]['title']    = $values['title'];
        $active_widgets[$id]['context']  = $context;
        $active_widgets[$id]['priority'] = $priority;
      }
    }
  }

  // Unset OUR widgets
  foreach ($cd_widgets as $widget) {
    unset($active_widgets[$widget]);
  }

  update_option('cd_active_widgets', $active_widgets);
}

add_action('wp_dashboard_setup', 'cd_get_active_widgets', 100);

function cd_the_page_title() {
  echo '<h2 class="cd-title"><span class="cd-title-icon cd-icon"></span> ' . get_admin_page_title() . '</h2>';
}

function cd_create_tab_page() {
  global $cd_existing_pages;

  $cd_existing_pages = apply_filters('cd_tabs', $cd_existing_pages);

  // Declare static variable
  $first_tab = '';

  /* Create Tab Menu */

  // Get the page for building url
  $current_page = str_replace('cd_', '', $_GET['page']);

  // If a tab is open, get it
  if (isset($_GET['tab']))
    $active_tab = $_GET['tab'];
  else {
    $active_tab = null;
  }
  ?>

  <h2 class="nav-tab-wrapper">
    <?php $i = 0;
    foreach ($cd_existing_pages as $page => $data) {

      // If not current page, bail
      if ($current_page != $page)
        continue;

      $i = 0;
      foreach ($data as $name => $ID) {
        $i++;

        if ($i == 1)
          $first_tab = $ID;

        // If active tab, set class
        if ($active_tab == $ID || !$active_tab && $i == 1)
          $active = 'nav-tab-active';
        else
          $active = '';

        echo '<a href="?page=cd_' . $current_page . '&tab=' . $ID . '" class="nav-tab ' . $active . '">' . $name . '</a>';
      }
    } ?>
  </h2>
  <?php

  /* Output Tab Content */

  if (!$active_tab)
    $active_tab = $first_tab;

  // Add content via actions
  do_action('cd_' . $current_page . '_' . $active_tab . '_tab');
}

function cd_settings_header($options) {
  extract($options);

  global $cd_fields;

  if (isset($_POST["update_settings"])) {
    foreach ($fields as $field) {
      $var = esc_attr($_POST[$field]);
      update_option($field, $var);
    }
    ?>
    <div id="cd-message" class="cd-updated cd-message-closed">Settings saved!</div>
  <?php
  }

  foreach ($fields as $field) {
    $cd_fields[$field] = get_option($field);
  }

  echo '<form method="POST" action=""><table class="form-table">';
}

function cd_settings_footer() {
  echo '
  </table>
  <p>
    <input type="submit" value="Save cd_settings" class="button-primary"/>
  </p>
  <input type="hidden" name="update_settings" value="Y" />
  </form>
  ';
}

function cd_get_color_scheme($which_color) {
  global $admin_colors;
  $current_color = get_user_option('admin_color');
  $colors        = $admin_colors[$current_color];

  $output = array(
    'primary'      => $colors->colors[1],
    'primary-dark' => $colors->colors[0],
    'secondary'    => $colors->colors[2],
    'tertiary'     => $colors->colors[3]
  );

  if (!$which_color)
    return $output;
  elseif ($which_color == 'primary')
    return $output['primary'];
  elseif ($which_color == 'primary-dark')
    return $output['primary-dark'];
  elseif ($which_color == 'secondary')
    return $output['secondary'];
  elseif ($which_color == 'tertiary')
    return $output['tertiary'];
}

// Functions for getting directory information (by pradeep)
function cd_get_dir_size($path) {
  $totalsize  = 0;
  $totalcount = 0;
  $dircount   = 0;
  if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link($nextpath)) {
        if (is_dir($nextpath)) {
          $dircount++;
          $result = cd_get_dir_size($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        }
        elseif (is_file($nextpath)) {
          $totalsize += filesize($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir($handle);
  $total['size']     = $totalsize;
  $total['count']    = $totalcount;
  $total['dircount'] = $dircount;

  return $total;
}

function cd_format_dir_size($size) {
  if ($size < 1024) {
    return $size . " bytes";
  }
  else if ($size < (1024 * 1024)) {
    $size = round($size / 1024, 1);

    return $size . " KB";
  }
  else if ($size < (1024 * 1024 * 1024)) {
    $size = round($size / (1024 * 1024), 1);

    return $size . " MB";
  }
  else {
    $size = round($size / (1024 * 1024 * 1024), 1);

    return $size . " GB";
  }
}

// Help functions
function cd_get_settings_url() {
  return get_admin_url() . 'options-general.php?page=cd_settings';
}

function cd_get_account_url(){
  return get_admin_url() . 'index.php?page=cd_account';
}

function cd_get_help_url(){
  return get_admin_url() . 'index.php?page=cd_help';
}

function cd_get_reports_url(){
  return get_admin_url() . 'index.php?page=cd_reports';
}

function cd_get_webmaster_url(){
  return get_admin_url() . 'index.php?page=cd_webmaster';
}