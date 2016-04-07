<?php defined( 'ABSPATH' ) or exit;
/**
 * List table
 *
 * @package    WebMan Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 */





// Requirements check

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}





/**
 * List table class
 *
 * @see WP_List_Table
 *
 * @since    1.0
 * @version	 1.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Prepare items
 * 20) Columns
 */
class WebMan_Rename_Taxonomies_List_table extends WP_List_Table {





	/**
	 * 0) Init
	 */

		/**
		 * Constructor
		 *
		 * @see WP_List_Table::__construct() for more information on default arguments.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public function __construct() {

			// Processing

				parent::__construct();

				$this->prepare_items();

		} // /__construct





	/**
	 * 10) Prepare items
	 */

		/**
		 * Prepares the list of items for displaying.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function prepare_items() {

			// Helper variables

				$taxonomy_objects = get_taxonomies( null, 'objects' );

				$new_labels = get_option( WebMan_Rename_Taxonomies::$option_name );

				if (
						isset( $new_labels['taxonomies'] )
						&& is_array( $new_labels['taxonomies'] )
						&& ! empty( $new_labels['taxonomies'] )
					) {
					$new_labels = $new_labels['taxonomies'];
				} else {
					$new_labels = array();
				}

				// Skip these taxonomies

					$skipped_keys = (array) apply_filters( 'rename_taxonomies_skipped_keys', array(
							'nav_menu',
							'link_category',
							'post_format',
						) );


			// Processing

				foreach ( $taxonomy_objects as $taxonomy => $taxonomy_object ) {

					if ( in_array( $taxonomy, $skipped_keys ) ) {
						continue;
					}

					$count_new_labels = 0;

					if (
							isset( $new_labels[ $taxonomy ] )
							&& ! empty( $new_labels[ $taxonomy ] )
						) {
						$count_new_labels = count( $new_labels[ $taxonomy ] );
					}

					$this->items[] = array(
							'title'     => $taxonomy_object->label,
							'post_type' => $taxonomy_object->object_type,
							'status'    => absint( $count_new_labels ),
							'key'       => $taxonomy,
						);

				} // /foreach

				$columns = $this->get_columns();

				$this->_column_headers = array( $columns, array(), array() );

		} // /prepare_items





	/**
	 * 20) Columns
	 */

		/**
		 * Get a list of columns. The format is:
		 * 'internal-name' => 'Title'
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function get_columns() {

			// Processing

				$columns = array(
						'status'    => esc_html__( 'Status', 'rename-taxonomies' ),
						'title'     => esc_html__( 'Taxonomy Title', 'rename-taxonomies' ),
						'key'       => esc_html_x( 'Taxonomy Key', 'The same as "taxonomy registration ID".', 'rename-taxonomies' ),
						'post_type' => esc_html__( 'Related Post Types', 'rename-taxonomies' ),
					);


			// Output

				return $columns;

		} // /get_columns



		/**
		 * Column content: title
		 *
		 * Use `column_COLUMNID` naming convention.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function column_title( $item ) {

			// Helper variables

				$output = '';

				$edit_url = add_query_arg( array(
						'action'   => 'edit',
						'taxonomy' => $item['key']
					) );


			// Processing

				// Build row actions

					$actions = array(
							'edit' => '<a href="' . esc_url( $edit_url ) . '">' . esc_html__( 'Edit taxonomy labels', 'rename-taxonomies' ) . '</a>',
						);

				// Setting output HTML

					$output = sprintf(
							'<strong><a href="%1$s">%2$s</a></strong> %3$s',
							esc_url( $edit_url ), // %1$s
							esc_html( $item['title'] ), // %2$s
							$this->row_actions( $actions ) // $3%s
						);


			// Output

				return $output;

		} // /column_title



		/**
		 * Column content: status
		 *
		 * Use `column_COLUMNID` naming convention.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function column_status( $item ) {

			// Helper variables

				$output = '';

				$edit_url = add_query_arg( array(
						'action'   => 'edit',
						'taxonomy' => $item['key']
					) );


			// Processing

				if ( $item['status'] > 0 ) {

					$output .= '<a href="' . esc_url( $edit_url ) . '" class="dashicons dashicons-edit" title="' . esc_attr__( 'Uses customized labels', 'rename-taxonomies' ) . '">';
					$output .= '<span class="screen-reader-text">';
					$output .= esc_html__( 'Uses customized labels', 'rename-taxonomies' );
					$output .= '</span>';
					$output .= '</a>';

				} else {

					$output .= '<a href="' . esc_url( $edit_url ) . '" class="dashicons dashicons-admin-generic" title="' . esc_attr__( 'Uses default labels', 'rename-taxonomies' ) . '">';
					$output .= '<span class="screen-reader-text">';
					$output .= esc_html__( 'Uses default labels', 'rename-taxonomies' );
					$output .= '</span>';
					$output .= '</a>';

				}



			// Output

				return $output;

		} // /column_status



		/**
		 * Column content: key
		 *
		 * Use `column_COLUMNID` naming convention.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function column_key( $item ) {

			// Helper variables

				$output = '';

				$edit_url = add_query_arg( array(
						'action'   => 'edit',
						'taxonomy' => $item['key']
					) );


			// Output

				return '<a href="' . esc_url( $edit_url ) . '"><code>' . esc_html( $item['key'] ) . '</code></a>';

		} // /column_key



		/**
		 * Column content: post_type
		 *
		 * Use `column_COLUMNID` naming convention.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function column_post_type( $item ) {

			// Helper variables

				$output = array();


			// Processing

				if (
						is_array( $item['post_type'] )
						&& ! empty( $item['post_type'] )
					) {

					foreach ( $item['post_type'] as $post_type ) {

						$post_type_object = get_post_type_object( $post_type );

						if ( ! empty( $post_type_object ) ) {
							$output[] = $post_type_object->labels->name;
						}

					} // /foreach

				}


			// Output

				return esc_html( implode( ', ', (array) $output ) );

		} // /column_post_type





} // /WebMan_Rename_Taxonomies_List_table
