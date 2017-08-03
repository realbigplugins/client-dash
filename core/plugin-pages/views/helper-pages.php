<?php
/**
 * The Helper Pages page.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @param array $pages Helper pages.
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <form method="post" action="options.php" id="clientdash-admin-page-form">

		<?php settings_fields( 'clientdash_helper_pages' ); ?>

        <h1 class="clientdash-page-title">
			<?php echo get_admin_page_title(); ?>
        </h1>

        <section class="clientdash-page-wrap">

            <div class="clientdash-page-description">
				<?php
				_e( 'Helper pages are special admin pages that Client Dash provides. You can enable or disable them' .
				    ' for different roles.', 'client-dash' );
				?>
            </div>

			<?php foreach ( $pages as $page ) : ?>
                <div class="clientdash-helper-page-wrap">
                    <div class="clientdash-helper-page-title">
                        <div class="clientdash-helper-page-icon-input">
							<?php
							cd_dashicon_selector( array(
								'name'     => "cd_helper_pages[$page[id]][icon]",
								'selected' => $page['icon'] ? $page['icon'] : $page['original_icon'],
							) );
							?>
                        </div>

                        <div class="clientdash-helper-page-title-input">
                            <input type="text" name="<?php echo "cd_helper_pages[$page[id]][title]"; ?>"
                                   id="<?php echo "cd_helper_pages[$page[id]][title]"; ?>"
                                   class="cd-title-input widefat"
                                   placeholder="<?php _e( 'Helper Page Title', 'client-dash' ); ?>"
                                   value="<?php echo esc_attr( $page['title'] ? $page['title'] : $page['original_title'] ); ?>"/>
                        </div>

                        <div class="clientdash-helper-page-disabled-notice">
                            <span class="clientdash-helper-page-visible-icon dashicons dashicons-visibility"
                                  aria-label="<?php _e( 'Visible', 'client-dash' ); ?>"
                                  title="<?php _e( 'Visible', 'client-dash' ); ?>"></span>
                            <span class="clientdash-helper-page-hidden-icon dashicons dashicons-hidden"
                                  aria-label="<?php _e( 'Hidden', 'client-dash' ); ?>"
                                  title="<?php _e( 'Hidden', 'client-dash' ); ?>"></span>
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
                                        <input type="text" name="<?php echo "cd_helper_pages[$page[id]][tabs][$tab_ID][title]"; ?>"
                                               id="<?php echo "cd_helper_pages[$page[id]][tabs][$tab_ID][title]"; ?>"
                                               class="cd-title-input widefat"
                                               placeholder="<?php _e( 'Helper Page Tab Title', 'client-dash' ); ?>"
                                               value="<?php echo esc_attr( $tab['title'] ? $tab['title'] : $tab['original_title'] ); ?>"/>
                                    </div>

                                    <div class="clientdash-helper-page-tab-roles-input">
                                        <select name="<?php echo "cd_helper_pages[$page[id]][tabs][$tab_ID][roles][]"; ?>"
                                                id="<?php echo "cd_helper_pages[$page[id]][tabs][$tab_ID][roles][]"; ?>"
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

                                    <div class="clientdash-helper-page-disabled-notice">
                                        <span class="clientdash-helper-page-visible-icon dashicons dashicons-visibility"
                                              aria-label="<?php _e( 'Visible', 'client-dash' ); ?>"></span>
                                        <span class="clientdash-helper-page-hidden-icon dashicons dashicons-hidden"
                                              aria-label="<?php _e( 'Hidden', 'client-dash' ); ?>"></span>
                                    </div>
                                </div>

                                <div class="clientdash-helper-page-tab-description">
									<?php echo $tab['description']; ?>
                                </div>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>
			<?php endforeach; ?>

        </section>

		<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

    </form>
</div>
