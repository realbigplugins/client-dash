<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Option 1</th>
			<td><input type="text" name="option_one" value="<?php echo get_option('option_one'); ?>" /></td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>