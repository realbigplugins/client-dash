<?php
global $cd_fields;

cd_settings_header(array(
  'fields' => array(
    'cd_status_cake_option1',
    'cd_status_cake_option2'
  )
));
?>

<tr valign="top">
  <th scope="row">
    <label for="cd_status_cake_option1">Option 1</label> 
  </th>
  <td>
    <input type="text" name="cd_status_cake_option1" id="cd_status_cake_option1" value="<?php echo $cd_fields['cd_status_cake_option1'] ?>" />
  </td>
</tr>
<tr valign="top">
  <th scope="row">
    <label for="cd_status_cake_option2">Option 2</label> 
  </th>
  <td>
    <input type="text" name="cd_status_cake_option2" id="cd_status_cake_option2" value="<?php echo $cd_fields['cd_status_cake_option2'] ?>" />
  </td>
</tr>

<?php cd_settings_footer(); ?>