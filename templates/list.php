<?php defined( 'ABSPATH' ) or exit;
/**
 * Admin page: List taxonomies.
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.1.0
 */

require_once WMRT_PLUGIN_PATH . 'includes/class-list-table.php';

$list = new WebMan_Rename_Taxonomies_List_table();

?>

<h1><?php esc_html_e( 'Rename Taxonomies', 'rename-taxonomies' ); ?></h1>

	<p>
		<strong><?php esc_html_e( 'List of registered taxonomies:', 'rename-taxonomies' ); ?></strong><br>
		<em><?php esc_html_e( '(Click the taxonomy name to edit its details)', 'rename-taxonomies' ); ?></em>
	</p>

	<p class="description dashicons-before dashicons-editor-help">
		<?php esc_html_e( '(*) Items in the list are sorted alphabetically by (customized) "Taxonomy Title" field.', 'rename-taxonomies' ); ?>
	</p>

	<?php

	$list->display();
