<?php
/**
 * List table.
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	return;
}

class WebMan_Rename_Taxonomies_List_table extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @see   WP_List_Table::__construct()
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/
	 *
	 * @since    1.0.0
	 * @version  1.1.0
	 *
	 * @return  void
	 */
	public function __construct() {

		// Processing

			parent::__construct( array(
				'singular' => esc_html__( 'Taxonomy', 'rename-taxonomies' ),
				'plural'   => esc_html__( 'Taxonomies', 'rename-taxonomies' ),
			) );

			$this->prepare_items();

	} // /__construct

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public function prepare_items() {

		// Variables

			/**
			 * Skip some taxonomies.
			 *
			 * @since    1.0.0
			 * @version  1.3.0
			 *
			 * @param  array $taxonomies
			 */
			$skipped_keys = (array) apply_filters( 'rename_taxonomies_skipped_keys', array_combine(
				WebMan_Rename_Taxonomies::$skipped_taxonomies,
				WebMan_Rename_Taxonomies::$skipped_taxonomies
			) );

			/**
			 * Arguments passed to `get_taxonomies()`.
			 *
			 * @link  https://developer.wordpress.org/reference/functions/get_taxonomies/
			 *
			 * @since  1.3.0
			 *
			 * @param  array $args
			 */
			$get_taxonomies_args = (array) apply_filters( 'rename_taxonomies_get_taxonomies_args', array(
				'show_ui' => true,
			) );

			$items = array();

			$taxonomy_objects = (array) get_taxonomies( $get_taxonomies_args, 'objects' );
			$taxonomy_count   = 0;
			$labels_default   = (array) WebMan_Rename_Taxonomies::get_default_labels();
			$labels_new       = (array) get_option( WebMan_Rename_Taxonomies::$option_name );

			// Get new labels only from stored plugin option.
			if ( ! empty( $labels_new['taxonomies'] ) ) {
				$labels_new = (array) $labels_new['taxonomies'];
			} else {
				$labels_new = array();
			}

			// Pagination setup.
			$per_page     = absint( $this->get_items_per_page( 'taxonomies_per_page', WebMan_Rename_Taxonomies::$per_page ) );
			$current_page = absint( $this->get_pagenum() );


		// Processing

			// Get all list items.
			foreach ( $taxonomy_objects as $taxonomy => $taxonomy_object ) {

				// Skip some taxonomies.
				if ( in_array( $taxonomy, $skipped_keys ) ) {
					continue;
				}

				$count_labels_new = ( ! empty( $labels_new[ $taxonomy ] ) ) ? ( count( (array) $labels_new[ $taxonomy ] ) ) : ( 0 );

				if ( isset( $labels_default[ $taxonomy ]['name'] ) ) {
					$title_default = $labels_default[ $taxonomy ]['name'];
				} elseif ( 'category' == $taxonomy ) {
					// WordPress doesn't set title for Categories, so we have to do it manually.
					$title_default = esc_html__( 'Categories', 'rename-taxonomies' );
				} elseif ( 'post_tag' == $taxonomy ) {
					// WordPress doesn't set title for Tags, so we have to do it manually.
					$title_default = esc_html__( 'Tags', 'rename-taxonomies' );
				} else {
					$title_default = $taxonomy;
				}

				// Set row item data.
				$items[] = array(
					'title'         => $taxonomy_object->label,
					'title_default' => $title_default,
					'post_type'     => $taxonomy_object->object_type,
					'status'        => absint( $count_labels_new ),
					'key'           => $taxonomy,
				);

				// Sort items alphabetically (by customized label).
				usort( $items, function( $a, $b ) {

					// Sort by title alphabetically.
					if ( $a['title'] == $b['title'] ) {
						return strcmp( $a['key'], $b['key'] );
					} else {
						return strcmp( $a['title'], $b['title'] );
					}
				} );

				$taxonomy_count++;
			}

			// Set column headers.
			$this->_column_headers = array(
				$this->get_columns(), // All columns.
				array(),              // Hidden columns.
				array(),              // Sortable columns.
			);

			// Split items into chunks.
			$items       = array_chunk( $items, $per_page );
			$this->items = $items[ $current_page - 1 ];

			// Set pagination.
			$this->set_pagination_args( array(
				'total_items' => absint( $taxonomy_count ),
				'per_page'    => $per_page,
			) );

	} // /prepare_items

	/**
	 * Gets a list of columns.
	 *
	 * The format is:
	 * - `'internal-name' => 'Title'`
	 *
	 * @since    1.0.0
	 * @version  1.1.0
	 *
	 * @return array
	 */
	public function get_columns() {

		// Processing

			$columns = array(
				'status'        => esc_html__( 'Status', 'rename-taxonomies' ),
				'title'         => esc_html_x( 'Taxonomy Title (*)', 'The (*) is for info note below the items list.', 'rename-taxonomies' ),
				'title_default' => esc_html__( 'Default Taxonomy Title', 'rename-taxonomies' ),
				'key'           => esc_html_x( 'Taxonomy Registration Key', 'The same as "taxonomy registration ID".', 'rename-taxonomies' ),
				'post_type'     => esc_html__( 'Related Post Types', 'rename-taxonomies' ),
			);


		// Output

			return $columns;

	} // /get_columns

	/**
	 * Custom column content: status.
	 *
	 * Use `column_COLUMNID` naming convention.
	 * Get `COLUMNID` from `self::get_columns()`.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $item  Table row item (column values).
	 *
	 * @return  string
	 */
	public function column_status( array $item ): string {

		// Variables

			$edit_url = add_query_arg( array(
				'action'   => 'edit',
				'taxonomy' => $item['key']
			) );


		// Processing

			if ( $item['status'] > 0 ) {

				$output =
					'<a
						href="' . esc_url( $edit_url ) . '"
						class="dashicons dashicons-edit"
						title="' . esc_attr__( 'Custom labels', 'rename-taxonomies' ) . '"
						>'
					. '<span class="screen-reader-text">'
					. esc_html__( 'Custom labels', 'rename-taxonomies' )
					. '</span>'
					. '</a>';

			} else {

				$output =
					'<a
						href="' . esc_url( $edit_url ) . '"
						class="dashicons dashicons-editor-code"
						title="' . esc_attr__( 'Default labels from code', 'rename-taxonomies' ) . '"
						>'
					.'<span class="screen-reader-text">'
					.esc_html__( 'Default labels from code', 'rename-taxonomies' )
					.'</span>'
					.'</a>';
			}


		// Output

			return $output;

	} // /column_status

	/**
	 * Custom column content: title.
	 *
	 * Use `column_COLUMNID` naming convention.
	 * Get `COLUMNID` from `self::get_columns()`.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
	 *
	 * @since    1.0.0
	 * @version  1.1.0
	 *
	 * @param  array $item  Table row item (column values).
	 *
	 * @return  string
	 */
	public function column_title( array $item ): string {

		// Variables

			$edit_url = add_query_arg( array(
				'action'   => 'edit',
				'taxonomy' => $item['key']
			) );

			$actions = array(
				'edit' => '<a href="' . esc_url( $edit_url ) . '">' . esc_html__( 'Edit taxonomy labels', 'rename-taxonomies' ) . '</a>',
			);


		// Output

			return sprintf(
				'<strong><a href="%1$s">%2$s</a></strong> %3$s',
				esc_url( $edit_url ),
				esc_html( $item['title'] ),
				$this->row_actions( $actions )
			);

	} // /column_title

	/**
	 * Custom column content: title_default.
	 *
	 * Use `column_COLUMNID` naming convention.
	 * Get `COLUMNID` from `self::get_columns()`.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $item  Table row item (column values).
	 *
	 * @return  string
	 */
	public function column_title_default( array $item ): string {

		// Variables

			$atts = '';

			if ( $item['status'] === 0 ) {
				$atts .= ' class="not-changed"';
				$atts .= ' title="' . esc_attr__( 'This taxonomy is not modified.', 'rename-taxonomies' ) . '"';
			}


		// Output

			return
				'<a href="' . esc_url(
					add_query_arg( array(
						'action'   => 'edit',
						'taxonomy' => $item['key']
					) )
				) . '"'
				. $atts
				. '>'
				. esc_html( $item['title_default'] )
				. '</a>';

	} // /column_title_default

	/**
	 * Custom column content: key.
	 *
	 * Use `column_COLUMNID` naming convention.
	 * Get `COLUMNID` from `self::get_columns()`.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $item  Table row item (column values).
	 *
	 * @return  string
	 */
	public function column_key( array $item ): string {

		// Output

			return
				'<a href="' . esc_url(
					add_query_arg( array(
						'action'   => 'edit',
						'taxonomy' => $item['key']
					) )
				) . '">'
				. '<code>'
				. esc_html( $item['key'] )
				. '</code>'
				. '</a>';

	} // /column_key

	/**
	 * Custom column content: post_type.
	 *
	 * Use `column_COLUMNID` naming convention.
	 * Get `COLUMNID` from `self::get_columns()`.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $item  Table row item (column values).
	 *
	 * @return  string
	 */
	public function column_post_type( array $item ): string {

		// Variables

			$output = array();


		// Processing

			if ( ! empty( $item['post_type'] ) ) {
				foreach ( (array) $item['post_type'] as $post_type ) {

					$post_type_object = get_post_type_object( $post_type );

					if ( ! empty( $post_type_object ) ) {

						$output[] =
							'<span class="dashicons-before ' . esc_attr( ( $post_type_object->menu_icon ?? 'dashicons-admin-post' ) ) . '">'
							. esc_html( $post_type_object->labels->name )
							. '</span>';
					}
				}
			}


		// Output

			return implode( ' ', (array) $output );

	} // /column_post_type

}
