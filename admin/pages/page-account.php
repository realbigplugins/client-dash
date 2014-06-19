<?php

/**
 * Outputs Account page.
 */
function cd_account_page() {
	?>
	<div class="wrap cd-account">
		<?php
		cd_the_page_title( 'account' );
		cd_create_tab_page();
		?>
	</div><!--.wrap-->
<?php
}