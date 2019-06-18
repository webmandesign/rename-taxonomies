<?php defined( 'ABSPATH' ) or exit;
/**
 * Admin page: Edit and save taxonomy.
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.1.0
 */

$taxonomy   = ( isset( $_GET['taxonomy'] ) ) ? ( sanitize_key( $_GET['taxonomy'] ) ) : ( '' );
$list_url   = add_query_arg( 'page', WebMan_Rename_Taxonomies::$plugin_slug, get_admin_url( null, 'tools.php' ) );
$nonce      = esc_attr( WebMan_Rename_Taxonomies::$option_name . '_edit_taxonomy_' . $taxonomy );
$label_keys = WebMan_Rename_Taxonomies::get_label_keys();

?>

<p>
	<a href="<?php echo esc_url( $list_url ); ?>" class="button button-hero">
		<span class="dashicons dashicons-undo" aria-hidden="true" style="vertical-align: -.2em;"></span>
		<?php esc_html_e( 'Back to taxonomies list without saving', 'rename-taxonomies' ); ?>
	</a>
</p>

<h1><?php

	printf(
		esc_html_x( 'Renaming taxonomy %s', '%s: taxonomy key/ID.', 'rename-taxonomies' ),
		'<code>' . $taxonomy . '</code>'
	);

?></h1>

	<?php

	// Requirements check

		// Capability check.
		if ( ! current_user_can( WebMan_Rename_Taxonomies::$capability ) ) {
			wp_die( esc_html__( 'Insufficient privileges!', 'rename-taxonomies' ) );
		}

		// Action check.
		if (
			! isset( $_GET['action'] )
			|| 'edit' !== $_GET['action']
		) {
			echo WebMan_Rename_Taxonomies::get_admin_notice_html(
				esc_html__( 'Sorry, there is nothing to see here.', 'rename-taxonomies' ),
				'notice-error'
			);
			return;
		}

		// Taxonomy check.
		if (
			empty( $taxonomy )
			|| ! taxonomy_exists( $taxonomy )
		) {
			echo WebMan_Rename_Taxonomies::get_admin_notice_html(
				esc_html__( 'Error: Invalid taxonomy!', 'rename-taxonomies' ),
				'notice-error'
			);
			return;
		}

	?>

	<p><?php

		esc_html_e( 'In form below you can only edit taxonomy labels.', 'rename-taxonomies' );
		echo '<br>';
		esc_html_e( 'The labels will change naming of the taxonomy, but not its functionality (you will not loose any registered taxonomy).', 'rename-taxonomies' );
		echo '<br>';
		esc_html_e( 'Please note that this will not affect any taxonomy URL slugs.', 'rename-taxonomies' );

	?></p>

	<?php

	// Saving action

		if (
			isset( $_POST['action'] )
			&& 'save' == $_POST['action']
			&& isset( $_POST['labels'] )
			&& is_array( $_POST['labels'] )
			&& ! empty( $_POST['labels'] )
		) {

			// Nonce check.
			check_admin_referer( $nonce );

			// Process new labels.

				$labels_new     = get_option( WebMan_Rename_Taxonomies::$option_name );
				$labels_to_save = (array) $_POST['labels'];

				// Multilingual plugin compatibility.
				if ( function_exists( 'icl_register_string' ) ) {
					foreach ( $labels_to_save as $label_key => $label_text ) {
						if ( empty( $label_text ) ) {
							icl_unregister_string( WebMan_Rename_Taxonomies::$plugin_slug, $taxonomy . '[' . $label_key . ']' );
						} else {
							icl_register_string( WebMan_Rename_Taxonomies::$plugin_slug, $taxonomy . '[' . $label_key . ']', $label_text );
						}
					}
				}

				$labels_new['taxonomies'][ $taxonomy ] = array_filter( $labels_to_save );

			// Save new labels.

				if ( update_option( WebMan_Rename_Taxonomies::$option_name, $labels_new ) ) {
					echo WebMan_Rename_Taxonomies::get_admin_notice_html(
						esc_html__( 'New taxonomy labels were saved successfully!', 'rename-taxonomies' )
						. ' <strong>'
						. esc_html__( 'Please, refresh the page to preview the changes.', 'rename-taxonomies' )
						. '</strong>'
					);
				} else {
					echo WebMan_Rename_Taxonomies::get_admin_notice_html(
						esc_html__( 'No changes were saved!', 'rename-taxonomies' ),
						'notice-error'
					);
				}

		}

	?>

	<form action="" method="post" class="form-edit-rename-taxonomies" id="form-edit-rename-taxonomies">

		<table class="form-table">
			<tbody><?php

			foreach ( $label_keys as $label => $args ) :

				// Display only relevant labels upon taxonomy `hierarchical` setting.
				if ( isset( $args['condition'] ) ) {
					if ( 'is_not_hierarchical' == $args['condition'] && is_taxonomy_hierarchical( $taxonomy ) ) {
						continue;
					} else if ( 'is_hierarchical' == $args['condition'] && ! is_taxonomy_hierarchical( $taxonomy ) ) {
						continue;
					}
				}

				$value_default_description = $value = $class ='';

				// Get default taxonomy label value.
				$value_default = esc_html__( '- Uses WordPress default value -', 'rename-taxonomies' );
				if ( isset( WebMan_Rename_Taxonomies::$default_tax_labels[ $taxonomy ][ $label ] ) ) {
					$value_default = $value_default_description = trim( WebMan_Rename_Taxonomies::$default_tax_labels[ $taxonomy ][ $label ] );
				}

				// Get saved taxonomy label value.
				$saved_labels = get_option( WebMan_Rename_Taxonomies::$option_name );
				if ( isset( $saved_labels['taxonomies'][ $taxonomy ][ $label ] ) ) {
					$value = trim( $saved_labels['taxonomies'][ $taxonomy ][ $label ] );
				}

				// Row CSS class.
				if ( $value_default_description ) {
					$class = ' custom-default-value';
				}

				?>

				<tr class="form-field<?php esc_attr_e( $class ); ?>">

					<th scope="row">
						<label for="<?php esc_attr_e( $label ); ?>">
							<?php esc_html_e( $args['label'] ); ?>
							<br>
							<code><?php esc_html_e( $label ); ?></code>
						</label>
					</th>

					<td>
						<input
							name="labels[<?php esc_attr_e( $label ); ?>]"
							type="text"
							id="<?php esc_attr_e( $label ); ?>"
							value="<?php esc_attr_e( $value ); ?>"
							placeholder="<?php esc_attr_e( $value_default ); ?>"
							class="input-rename-taxonomy-label"
							>

						<a
							class="button-reset dashicons dashicons-dismiss"
							title="<?php esc_attr_e( 'Reset default value', 'rename-taxonomies' ); ?>"
							onClick="document.getElementById( '<?php esc_attr_e( $label ); ?>' ).value = ''; document.getElementById( '<?php esc_attr_e( $label ); ?>' ).focus();"
							tabindex="-1"
							>
							<span class="screen-reader-text"><?php esc_html_e( 'Reset default value', 'rename-taxonomies' ); ?></span>
						</a>

						<input
							type="submit"
							name="submit"
							class="button button-primary button-hidden"
							value="<?php esc_attr_e( 'Save All Changes', 'rename-taxonomies' ); ?>"
							tabindex="-1"
							>

						<span class="description"><?php

							esc_html_e( $args['description'] );

							if ( $value_default_description ) {
								printf(
									' ' . esc_html__( 'Default value: "%s"', 'rename-taxonomies' ),
									$value_default_description
								);
							}

						?></span>
					</td>

				</tr>

				<?php

			endforeach;

			?></tbody>
		</table>

		<p class="submit">
			<?php wp_nonce_field( $nonce ); ?>

			<input
				type="hidden"
				name="action"
				value="save"
				>
			<input
				type="submit"
				name="submit"
				class="button button-primary button-hero"
				value="<?php esc_attr_e( 'Save All Changes', 'rename-taxonomies' ); ?>"
				>

			<a href="<?php echo esc_url( $list_url ); ?>" class="button button-hero">
				<span class="dashicons dashicons-undo" aria-hidden="true" style="vertical-align: -.2em;"></span>
				<?php esc_html_e( 'Back to taxonomies list without saving', 'rename-taxonomies' ); ?>
			</a>

			<button
				type="reset"
				class="button button-hero"
				onClick="var inputFields = document.getElementsByClassName( 'input-rename-taxonomy-label' ); for ( var i = 0; i < inputFields.length; i++ ) { inputFields[ i ].value = ''; inputFields[ i ].setAttribute( 'value', '' ); };"
				>
				<span class="dashicons dashicons-dismiss" aria-hidden="true" style="vertical-align: -.25em;"></span>
				<?php esc_attr_e( 'Reset all taxonomy labels to default values', 'rename-taxonomies' ); ?>
			</button>
		</p>

	</form>
