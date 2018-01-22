<?php
/**
 * The Helper Pages page.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @param array $pages Helper pages.
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <h1 class="clientdash-page-title">
		<?php echo get_admin_page_title(); ?>
		<?php
		cd_field_tip( __( 'Helper pages are special admin pages that Client Dash provides. You can enable or disable them for different roles.', 'client-dash' ) );
		?>
    </h1>

	<?php settings_errors(); ?>

    <section class="clientdash-page-wrap">

        <form method="post" action="options.php" id="clientdash-helper-pages-form">

			<?php settings_fields( 'clientdash_helper_pages' ); ?>

			<?php foreach ( $pages as $page_ID => $page ) : ?>
                <div class="clientdash-helper-page-wrap">
                    <div class="clientdash-helper-page-title">
                        <div class="clientdash-helper-page-icon-input">
							<?php
							cd_dashicon_selector( array(
								'name'     => "cd_helper_pages[$page_ID][icon]",
								'selected' => $page['icon'] ? $page['icon'] : $page['original_icon'],
							) );
							?>
                        </div>

                        <div class="clientdash-helper-page-title-input">
                            <input type="text" name="<?php echo "cd_helper_pages[$page_ID][title]"; ?>"
                                   id="<?php echo "cd_helper_pages[$page_ID][title]"; ?>"
                                   class="cd-title-input widefat"
                                   placeholder="<?php _e( 'Helper Page Title', 'client-dash' ); ?>"
                                   value="<?php echo esc_attr( $page['title'] ? $page['title'] : $page['original_title'] ); ?>"/>
                        </div>
                    </div>

                    <div class="clientdash-helper-page-description">
						<?php echo $page['description']; ?>
                    </div>

					<?php if ( isset( $page['tabs'] ) ) : ?>
						<?php foreach ( $page['tabs'] as $tab_ID => $tab ) : ?>
                            <div class="clientdash-helper-page-tab-wrap">
                                <div class="clientdash-helper-page-tab-title">
                                    <div class="clientdash-helper-page-tab-title-input">
                                        <input type="text"
                                               name="<?php echo "cd_helper_pages[$page_ID][tabs][$tab_ID][title]"; ?>"
                                               id="<?php echo "cd_helper_pages[$page_ID][tabs][$tab_ID][title]"; ?>"
                                               class="cd-title-input widefat"
                                               placeholder="<?php _e( 'Helper Page Tab Title', 'client-dash' ); ?>"
                                               value="<?php echo esc_attr( $tab['title'] ? $tab['title'] : $tab['original_title'] ); ?>"/>
                                    </div>

                                    <div class="clientdash-helper-page-tab-roles-input">
                                        <select name="<?php echo "cd_helper_pages[$page_ID][tabs][$tab_ID][roles][]"; ?>"
                                                id="<?php echo "cd_helper_pages[$page_ID][tabs][$tab_ID][roles][]"; ?>"
                                                class="clientdash-select2" multiple
                                                data-minimum-results-for-search="Infinity"
                                                data-dropdown-css-class="clientdash-helper-pages"
                                                data-close-on-select="false">
											<?php foreach ( get_editable_roles() as $role_ID => $role ) : ?>
												<?php
												// Skip administrators. They should always be able to see.
												if ( $role_ID === 'administrator' ) {

													continue;
												}
												?>
                                                <option value="<?php echo esc_attr( $role_ID ); ?>"
													<?php echo in_array( $role_ID, $tab['roles'] ) ? 'selected' : ''; ?>>
													<?php echo $role['name']; ?>
                                                </option>
											<?php endforeach; ?>
                                        </select>

                                        <button type="button" class="button" data-select-toggle>
											<?php _e( 'Select Roles', 'client-dash' ); ?>
                                        </button>
                                    </div>
                                </div>

                                <div class="clientdash-helper-page-tab-description">
									<?php echo isset( $tab['description'] ) ? $tab['description'] : ''; ?>
                                </div>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
			<?php endforeach; ?>

        </form>

    </section>

	<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

</div>
