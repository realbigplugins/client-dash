<?php
/*
* All core functions go here
*/

function cd_the_page_title(){
  echo '<h2 class="cd-title"><span class="cd-title-icon cd-icon"></span> '.get_admin_page_title().'</h2>';
}

function cd_create_tab_page($options){
  extract($options);

  global $cd_existing_pages;

  // Declare static variable
  $first_tab = '';

  /* Create Tab Menu */

  // Get the page for building url
  $current_page = $_GET['page'];

  // If a tab is open, get it
  if (isset($_GET['tab']))
    $active_tab = $_GET['tab'];
  else
    $active_tab = null;

  // Allow tabs to be added
  $tabs = apply_filters('cd_add_tabs', $tabs);
  ?>

  <h2 class="nav-tab-wrapper">
    <?php $i=0; foreach ($tabs as $name => $link): $i++;
      // Don't skip by default
      $skip = false;

      // See if this tab belongs
      foreach ($cd_existing_pages as $page => $tabs):
        if (in_array($link, $tabs) && $page != $current_page)
          $skip = true;
      endforeach;

      // Skip if necessary
      if ($skip)
        continue;

      // If first tab and none set, or if active tab is this tab, activate
      if ($i == 1 && !$active_tab || $link == $active_tab)
        $active = 'nav-tab-active';
      else
        $active = '';

      // Save first tab for later
      if ($i == 1)
        $first_tab = $link;

      echo '<a href="?page='.$current_page.'&tab='.$link.'" class="nav-tab '.$active.'">'.$name.'</a>';
    endforeach; ?>
  </h2>
  <?php

  /* Output Tab Content */

  if (!$active_tab)
    $active_tab = $first_tab;

  // Add content via actions
  do_action('cd_add_to_'.$active_tab.'_tab');
}

function cd_settings_header($options){
  extract($options);

  global $cd_fields;

  if (isset($_POST["update_settings"])) {
    foreach ($fields as $field):
      $var = esc_attr($_POST[$field]);
      update_option($field, $var);
    endforeach;
    ?>
    <div id="cd-message" class="cd-updated cd-message-closed">Settings saved!</div>
    <?php
  } 

  foreach ($fields as $field):
    $cd_fields[$field] = get_option($field);
  endforeach;

  echo '<form method="POST" action=""><table class="form-table">';
}

function cd_settings_footer(){
  echo '
  </table>
  <p>
    <input type="submit" value="Save settings" class="button-primary"/>
  </p>
  <input type="hidden" name="update_settings" value="Y" />
  </form>
  ';
}

function cd_get_color_scheme($which_color){
  global $admin_colors;
  $current_color = get_user_option( 'admin_color' );
  $colors = $admin_colors[$current_color];

  $output = array(
    'primary' => $colors->colors[1],
    'primary-dark' => $colors->colors[0],
    'secondary' => $colors->colors[2],
    'tertiary' => $colors->colors[3]
  );
  
  if (!$which_color):
    return $output;
  elseif ($which_color == 'primary'):
    return $output['primary'];
  elseif ($which_color == 'primary-dark'):
    return $output['primary-dark'];
  elseif ($which_color == 'secondary'):
    return $output['secondary'];
  elseif ($which_color == 'tertiary'):
    return $output['tertiary'];
  endif;
}

// Functions for getting directory information (by pradeep)
function cd_get_dir_size($path){ 
  $totalsize = 0; 
  $totalcount = 0; 
  $dircount = 0; 
  if ($handle = opendir ($path)){ 
    while (false !== ($file = readdir($handle))){ 
      $nextpath = $path . '/' . $file; 
      if ($file != '.' && $file != '..' && !is_link ($nextpath)){ 
        if (is_dir ($nextpath)){ 
          $dircount++; 
          $result = cd_get_dir_size($nextpath); 
          $totalsize += $result['size']; 
          $totalcount += $result['count']; 
          $dircount += $result['dircount']; 
        } 
        elseif (is_file ($nextpath)){ 
          $totalsize += filesize ($nextpath); 
          $totalcount++; 
        } 
      } 
    } 
  } 
  closedir ($handle); 
  $total['size'] = $totalsize; 
  $total['count'] = $totalcount; 
  $total['dircount'] = $dircount; 
  return $total; 
} 

function cd_format_dir_size($size){ 
  if($size<1024){ 
    return $size." bytes"; 
  } 
  else if($size<(1024*1024)){ 
    $size=round($size/1024,1); 
    return $size." KB"; 
  } 
  else if($size<(1024*1024*1024)){ 
    $size=round($size/(1024*1024),1); 
    return $size." MB"; 
  } 
  else{ 
    $size=round($size/(1024*1024*1024),1); 
    return $size." GB"; 
  } 
}
?>