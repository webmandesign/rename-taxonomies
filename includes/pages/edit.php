<?php defined( 'ABSPATH' ) or exit;
/**
 * Edit and save taxonomy
 *
 * @package    WebMan Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 */





// Helper variables

	$taxonomy = ( isset( $_GET['taxonomy'] ) ) ? ( sanitize_key( $_GET['taxonomy'] ) ) : ( '' );
	$list_url = add_query_arg( 'page', self::$admin_page_slug, get_admin_url( null, 'tools.php' ) );
	$nonce    = esc_attr( self::$option_name . '_edit_taxonomy_' . $taxonomy );



?>

<h1><?php esc_html_e( 'Rename Taxonomies', 'rename-taxonomies' ); ?></h1>

	<p>
		<a href="<?php echo esc_url( $list_url ); ?>" class="button" title="<?php esc_attr_e( 'No changes will be saved!', 'rename-taxonomies' ); ?>">
			<?php esc_html_e( '&laquo; Go back to taxonomies list', 'rename-taxonomies' ); ?>
		</a>
	</p>

	<?php

	// Requirements check

		// Capability check

				if ( ! current_user_can( self::$capability ) ) {

					wp_die( esc_html__( 'Insufficient privileges!', 'rename-taxonomies' ) );

				}

		// Action check

			if ( ! isset( $_GET['action'] ) || 'edit' !== $_GET['action'] ) {

				echo self::admin_notice(
						esc_html__( 'Sorry, there is nothing to see here.', 'rename-taxonomies' ),
						'notice-error'
					);

				return;

			}

		// Taxonomy check

			if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {

				echo self::admin_notice(
						esc_html__( 'Error: Invalid taxonomy!', 'rename-taxonomies' ),
						'notice-error'
					);

				return;

			}

	?>



	<h2><?php printf( esc_html__( 'Editing %s taxonomy', '%s: taxonomy ID.', 'rename-taxonomies' ), '<code>' . $taxonomy . '</code>' ); ?></h2>

		<p>
			<?php esc_html_e( 'You can only edit taxonomy labels in form below.', 'rename-taxonomies' ); ?><br>
			<?php esc_html_e( 'The labels will change naming of the taxonomy, but not its functionality (you will not loose any registered taxonomy).', 'rename-taxonomies' ); ?><br>
			<?php esc_html_e( 'This will not affect taxonomy URL slugs, however.', 'rename-taxonomies' ); ?>
		</p>

		<?php

		// Saving action

			if (
					isset( $_POST['action'] )
					&& 'save' == $_POST['action']
					&& isset( $_POST['labels'] )
					&& is_array( $_POST['labels'] )
					&& ! empty( $_POST['labels'] )
				) {

				// Nonce check

					check_admin_referer( $nonce );

				// Process new labels

					$labels_new = get_option( self::$option_name );

					$labels_new['taxonomies'][ $taxonomy ] = array_filter( (array) $_POST['labels'] );

				// Save new labels

					if ( update_option( self::$option_name, $labels_new ) ) {
						echo self::admin_notice(
								esc_html__( 'New taxonomy labels were saved successfully!', 'rename-taxonomies' )
								. ' <strong>'
								. esc_html__( 'Please, refresh the page to preview the changes.', 'rename-taxonomies' )
								. '</strong>'
							);
					}

			}

		?>

		<form action="" method="post" class="edit-rename-taxonomies" id="edit-rename-taxonomies">

			<table class="form-table">

				<tbody>
				<?php foreach ( self::$label_keys as $label => $args ) : ?>

					<?php

					// Display only relevant labels upon taxonomy `hierarchical` setting

						if ( isset( $args['condition'] ) ) {

							if ( 'is_not_hierarchical' == $args['condition'] && is_taxonomy_hierarchical( $taxonomy ) ) {
								continue;
							} else if ( 'is_hierarchical' == $args['condition'] && ! is_taxonomy_hierarchical( $taxonomy ) ) {
								continue;
							}

						}

					// Set cycle helper variables

						$value_default_description = $value = $class ='';

						// Get default taxonomy label value

							$value_default = esc_html__( '- Using WordPress default value -', 'rename-taxonomies' );

							if ( isset( self::$default_tax_labels[ $taxonomy ][ $label ] ) ) {
								$value_default = $value_default_description = trim( self::$default_tax_labels[ $taxonomy ][ $label ] );
							}

						// Get saved taxonomy label value

							$saved_labels = get_option( self::$option_name );

							if ( isset( $saved_labels['taxonomies'][ $taxonomy ][ $label ] ) ) {
								$value = trim( $saved_labels['taxonomies'][ $taxonomy ][ $label ] );
							}

						// Row CSS class

							if ( $value_default_description ) {
								$class = ' custom-default-value';
							}

					?>

					<tr class="form-field<?php esc_attr_e( $class ); ?>">

						<th scope="row">
							<label for="<?php esc_attr_e( $label ); ?>">
								<?php esc_html_e( $args['label'] ); ?><br>
								<code><?php esc_html_e( $label ); ?></code>
							</label>
						</th>

						<td>
							<!-- Taxonomy label text field -->
							<input name="labels[<?php esc_attr_e( $label ); ?>]" type="text" id="<?php esc_attr_e( $label ); ?>" value="<?php esc_attr_e( $value ); ?>" placeholder="<?php esc_attr_e( $value_default ); ?>" />

							<!-- Reset the default taxonomy label (empty the text field) -->
							<a class="button button-reset dashicons dashicons-undo" tabindex="-1" onClick="jQuery( '#<?php esc_attr_e( $label ); ?>' ).attr( 'value', '' );" title="<?php esc_attr_e( 'Reset default value', 'rename-taxonomies' ); ?>">
								<span class="screen-reader-text">
									<?php esc_html_e( 'Reset default value', 'rename-taxonomies' ); ?>
								</span>
							</a>

							<!-- Convenient hidden submit button displayed on row hover -->
							<input type="submit" name="submit" class="button button-primary button-hidden" value="<?php esc_attr_e( 'Save All Changes', 'rename-taxonomies' ); ?>" tabindex="-1">

							<!-- Taxonomy label description -->
							<span class="description">
								<?php

								esc_html_e( $args['description'] );

								if ( $value_default_description ) {
									printf( ' ' . esc_html__( 'Default value: "%s"', 'rename-taxonomies' ), $value_default_description );
								}

								?>
							</span>
						</td>

					</tr>

				<?php endforeach; ?>
				</tbody>

			</table>

			<p class="submit">
				<!-- Form submit button -->
				<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save All Changes', 'rename-taxonomies' ); ?>">

				<!-- Back to taxonomies list button -->
				<a href="<?php echo esc_url( $list_url ); ?>" class="button" title="<?php esc_attr_e( 'No changes will be saved!', 'rename-taxonomies' ); ?>">
					<?php esc_html_e( '&laquo; Go back to taxonomies list', 'rename-taxonomies' ); ?>
				</a>

				<!-- Form reset button - empties all text fields in the form -->
				<input type="reset" class="button" value="<?php esc_attr_e( 'Reset all taxonomy labels to default values', 'rename-taxonomies' ); ?>" onClick="jQuery( '#edit-rename-taxonomies input[type=text]' ).attr( 'value', '' );">
			</p>

			<input type="hidden" name="action" value="save" />

			<?php wp_nonce_field( $nonce ); ?>

		</form>
