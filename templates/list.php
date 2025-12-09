<?php
/**
 * Admin page: List taxonomies.
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<h1><?php esc_html_e( 'Rename Taxonomies', 'rename-taxonomies' ); ?></h1>

<p>
	<strong><?php esc_html_e( 'List of registered taxonomies:', 'rename-taxonomies' ); ?></strong><br>
	<em><?php esc_html_e( '(Click the taxonomy name to edit its details)', 'rename-taxonomies' ); ?></em>
</p>

<p class="description dashicons-before dashicons-editor-help"><?php
	esc_html_e( '(*) Items in the list are sorted alphabetically by (customized) "Taxonomy Title" field.', 'rename-taxonomies' );
?></p>

<?php

require_once WMRT_PLUGIN_PATH . 'includes/class-list-table.php';

if ( class_exists( 'WebMan_Rename_Taxonomies_List_table' ) ) {

	$table = new WebMan_Rename_Taxonomies_List_table;

	$table->display();
}
