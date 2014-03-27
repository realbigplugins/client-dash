<?php
/*
* Basic universal, core plugin functions
*/

// Include tab based on page and tab-name
function cd_get_tab($cd_tab_name, $cd_page_name) {
  ?>
  <table class="form-table cd-<?php echo $cd_tab_name; ?>">
    <?php
    $cd_tab_path = 'layout/'.$cd_page_name.'/'.$cd_tab_name.'-tab.php';
    // Include file based on local url with page name and tab name
    // Or allow for the path to be filtered by other plugins
    include_once(apply_filters('cd_tab_path', $cd_tab_path));
    ?>
  </table>
  <?php
}
?>