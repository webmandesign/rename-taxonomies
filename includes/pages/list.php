<?php defined( 'ABSPATH' ) or exit;
/**
 * List taxonomies
 *
 * @package    WebMan Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 */





// Required files

	require_once( self::$plugin_dir . 'includes/classes/class-list-table.php' );



// Helper variables

	$list = new WebMan_Rename_Taxonomies_List_table();

?>

<h1>
	<?php esc_html_e( 'Rename Taxonomies', 'rename-taxonomies' ); ?>
</h1>

<p>
	<strong><?php esc_html_e( 'List of registered taxonomies:', 'rename-taxonomies' ); ?></strong><br>
	<em><?php esc_html_e( '(Click the taxonomy name to edit its details)', 'rename-taxonomies' ); ?></em>
</p>

<?php

// Display the list of taxonomies

	$list->display();
