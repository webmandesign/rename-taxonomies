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

				parent::__construct( array(
						'singular' => esc_html__( 'Taxonomy', 'rename-taxonomies' ),
						'plural'   => esc_html__( 'Taxonomies', 'rename-taxonomies' ),
					) );

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

				$items = array();

				$taxonomy_objects = get_taxonomies( null, 'objects' );
				$labels_default   = WebMan_Rename_Taxonomies::get_default_labels();
				$labels_new       = get_option( WebMan_Rename_Taxonomies::$option_name );

				// Get only labels from stored plugin option

					if (
							isset( $labels_new['taxonomies'] )
							&& is_array( $labels_new['taxonomies'] )
							&& ! empty( $labels_new['taxonomies'] )
						) {
						$labels_new = $labels_new['taxonomies'];
					} else {
						$labels_new = array();
					}

				// Skip these taxonomies

					$skipped_keys = (array) apply_filters( 'rename_taxonomies_skipped_keys', array(
							'nav_menu',
							'link_category',
							'post_format',
						) );

				// Pagination setup

					$per_page     = $this->get_items_per_page( 'taxonomies_per_page', WebMan_Rename_Taxonomies::$per_page );
					$current_page = $this->get_pagenum();


			// Processing

				// Get all list items

					foreach ( $taxonomy_objects as $taxonomy => $taxonomy_object ) {

						// Skip taxonomies

							if (
									! in_array( $taxonomy, array( 'category', 'post_tag' ) )
									&& empty( $taxonomy_object->label )
								) {
								$skipped_keys[] = $taxonomy;
							}

							if ( in_array( $taxonomy, $skipped_keys ) ) {
								continue;
							}

						// Set cycle helper variables

							$count_labels_new = 0;
							$title_default    = '';

							// Count customized labels

								if (
										isset( $labels_new[ $taxonomy ] )
										&& ! empty( $labels_new[ $taxonomy ] )
									) {
									$count_labels_new = count( $labels_new[ $taxonomy ] );
								}

							// Default taxonomy title

								// WordPress don't set this for Categories and Tags so we have to it manually.

								if ( isset( $labels_default[ $taxonomy ]['name'] ) ) {
									$title_default = $labels_default[ $taxonomy ]['name'];
								} else if ( 'category' == $taxonomy ) {
									$title_default = esc_html__( 'Categories', 'rename-taxonomies' );
								} else if ( 'post_tag' == $taxonomy ) {
									$title_default = esc_html__( 'Tags', 'rename-taxonomies' );
								}

								/*
								SPOILER

								Let's continue...
								Took money from richmen, basically a robber :)

								J _ _ o _ _ _

								SPOILER
								*/

						// Set item data

							$items[] = array(
									'title'         => $taxonomy_object->label,
									'title_default' => $title_default,
									'post_type'     => $taxonomy_object->object_type,
									'status'        => absint( $count_labels_new ),
									'key'           => $taxonomy,
								);

							// Sort items alphabetically (by customized label)

								usort( $items, array( $this, 'sort_by_title' ) );

					} // /foreach

				// Set column headers

					$this->_column_headers = array(
							$this->get_columns(), // all columns
							array(), // hidden columns
							array(), // sortable columns
						);

				// Set pagination and split items into chunks

					$items       = array_chunk( $items, absint( $per_page ) );
					$total_items = count( $taxonomy_objects ) - count( $skipped_keys );

					$this->items = $items[ $current_page - 1 ];

					$this->set_pagination_args( array(
							'total_items' => absint( $total_items ),
							'per_page'    => absint( $per_page ),
						) );

		} // /prepare_items



		/**
		 * Sort by taxonomy title, alphabetically
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		function sort_by_title( $a, $b ) {

			// Output

				if ( $a['title'] == $b['title'] ) {
					return strcmp( $a['key'], $b['key'] );
				} else {
					return strcmp( $a['title'], $b['title'] );
				}

		} // /sort_by_title





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
		 * Columns contents
		 */

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
			 * Column content: title_default
			 *
			 * Use `column_COLUMNID` naming convention.
			 *
			 * @since    1.0
			 * @version  1.0
			 */
			function column_title_default( $item ) {

				// Helper variables

					$output = '';

					$class = ( $item['status'] > 0 ) ? ( '' ) : ( ' class="screen-reader-text"' );

					$edit_url = add_query_arg( array(
							'action'   => 'edit',
							'taxonomy' => $item['key']
						) );


				// Output

					return '<a href="' . esc_url( $edit_url ) . '"' . $class . '><em>' . esc_html( $item['title_default'] ) . '</em></a>';

			} // /column_title_default



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

					/*
					SPOILER

					Let's continue...
					Well, surely he was Slovak, just like I am.

					J _ _ o _ _ k

					SPOILER
					*/

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

								$class = 'dashicons-admin-post';

								if ( $post_type_object->menu_icon ) {
									$class = $post_type_object->menu_icon;
								}

								$output_single  = '<span class="dashicons-before ' . esc_attr( $class ) . '">';
								$output_single .= $post_type_object->labels->name;
								$output_single .= '</span>';

								$output[] = $output_single;

							}

						} // /foreach

					}


				// Output

					return implode( ' ', (array) $output );

			} // /column_post_type





} // /WebMan_Rename_Taxonomies_List_table
