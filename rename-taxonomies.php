<?php defined( 'ABSPATH' ) or exit;
/**
 * WebMan Rename Taxonomies
 *
 * @package    WebMan Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  http://www.webmandesign.eu
 *
 * Plugin Name:        Rename Taxonomies by WebMan
 * Plugin URI:         http://www.webmandesign.eu/
 * Description:        Customize the text labels or menu names for any registered taxonomy using a simple interface.
 * Version:            1.0
 * Author:             WebMan Design, Oliver Juhas
 * Author URI:         http://www.webmandesign.eu/
 * Text Domain:        rename-taxonomies
 * Domain Path:        /languages
 * License:            GNU General Public License v3
 * License URI:        http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least:  4.2
 * Tested up to:       4.5
 *
 * This plugin was inspired by "Custom Post Type Editor" plugin
 * Copyright (c) 2012-2015 OM4, https://om4.com.au
 * Distributed under the terms of the GNU GPL
 * https://om4.com.au/plugins/custom-post-type-editor/
 * https://wordpress.org/plugins/cpt-editor/
 */





/**
 * Main class
 *
 * @since    1.0
 * @version	 1.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Functionality
 * 20) Admin page
 * 30) Tools
 * 40) Localization
 */
class WebMan_Rename_Taxonomies {





	/**
	 * 0) Init
	 */

		private static $instance;

		public static $plugin_dir;

		public static $admin_page_slug;

		public static $option_name;

		private static $default_tax_labels;

		private static $label_keys;

		private static $capability;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		private function __construct() {

			// Processing

				// Set variables

					self::set_variables();

				// Hooks

					// Actions

						// Localization

							add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

						// Admin menu

							add_action( 'admin_menu', array( $this, 'admin_menu' ) );

						// Admin styles

							add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );

					// Filters

						// Rename taxonomy labels

							add_filter( 'register_taxonomy_args', array( $this, 'taxonomy_labels' ), 10, 2 );

						// Plugin action links

							add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Functionality
	 */

		/**
		 * Taxonomy labels
		 *
		 * First store the default predefined taxonomy labels in `self::$default_tax_labels`.
		 * Then, if we have new labels for taxonomy, apply those.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
     * @param array  $args     Array of arguments for registering a taxonomy.
     * @param string $taxonomy Taxonomy key.
		 */
		public static function taxonomy_labels( $args, $taxonomy ) {

			// Helper variables

				// Get saved taxonomy labels

					$taxonomy_labels = get_option( self::$option_name );

				// Get default labels

					if ( is_admin() ) {
						self::$default_tax_labels[ $taxonomy ] = ( isset( $args['labels'] ) ) ? ( (array) $args['labels'] ) : ( array() );
					}


			// Requirements check

				if (
						isset( $taxonomy_labels['taxonomies'] )
						&& is_array( $taxonomy_labels['taxonomies'] )
						&& ! empty( $taxonomy_labels['taxonomies'] )
					) {

					$taxonomy_labels = $taxonomy_labels['taxonomies'];

				} else {

					return $args;

				}


			// Processing

				// Set new labels

					foreach ( $taxonomy_labels as $taxonomy_key => $new_labels ) {

						$new_labels = array_filter( (array) $new_labels );

						if (
								$taxonomy_key == $taxonomy
								&& ! empty( $new_labels )
							) {

							if ( ! isset( $args['labels'] ) ) {
								$args['labels'] = array();
							}

							$args['labels'] = array_merge( $args['labels'], $new_labels );

						}

					} // /foreach


			// Output

				return $args;

		} // /taxonomy_labels



		/**
		 * Get default taxonomies labels
		 *
		 * This only works in admin. There is no need for it on front-end.
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function get_default_labels() {

			// Output

				return self::$default_tax_labels;

		} // /get_default_labels





	/**
	 * 20) Admin page
	 */

		/**
		 * Admin menu
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function admin_menu() {

			// Processing

				// Adding admin menu item under "Tools" menu

					add_submenu_page(
							'tools.php',
							esc_html__( 'Rename Taxonomies', 'rename-taxonomies' ),
							esc_html__( 'Rename Taxonomies', 'rename-taxonomies' ),
							self::$capability,
							self::$admin_page_slug,
							'WebMan_Rename_Taxonomies::admin_page'
						);

		} // /admin_menu



		/**
		 * Render admin page
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function admin_page() {

			// Helper variables

				$action = ( isset( $_GET['action'] ) ) ? ( $_GET['action'] ) : ( '' );


			// Processing

				// Wrapper open

					echo '<div class="wrap wrap-rename-taxonomies">';

				// Load the page content

					switch ( $action ) {

						case 'edit':
							 include( 'includes/pages/edit.php' );
							break;

						default:
							 include( 'includes/pages/list.php' );
							break;

					} // /switch

				// Wrapper close

					echo '</div>';

		} // /admin_page



		/**
		 * Admin styles
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $hook
		 */
		public static function admin_styles( $hook ) {

			// Requirements check

				if ( 'tools_page_' . self::$admin_page_slug !== $hook ) {
					return;
				}


			// Processing

				// Styles

					wp_enqueue_style(
						self::$admin_page_slug,
						plugin_dir_url( __FILE__ ) . 'assets/css/style.css'
					);

		} // /admin_styles



		/**
		 * Set plugin action links
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  array $links
		 */
		public static function action_links( $links ) {

			// Helper variables

				$plugin_settings_url = add_query_arg( 'page', self::$admin_page_slug, get_admin_url( null, 'tools.php' ) );


			// Processing

				$links[] = '<a href="' . esc_url( $plugin_settings_url ) . '">' . esc_html_x( 'Settings', 'Plugin action link.', 'rename-taxonomies' ) . '</a>';


			// Output

				return $links;

		} // /action_links





	/**
	 * 30) Tools
	 */

		/**
		 * Set class variables
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		private function set_variables() {

			// Processing

				$plugin_slug = basename( __FILE__, '.php' );

				// Set class variables

					self::$plugin_dir         = trailingslashit( plugin_dir_path( __FILE__ ) );
					self::$admin_page_slug    = $plugin_slug;
					self::$option_name        = 'webman_' . str_replace( '-', '_', $plugin_slug );
					self::$default_tax_labels = array();
					self::$capability         = 'manage_options';

					self::$label_keys = array(
							// From @link  https://developer.wordpress.org/reference/functions/get_taxonomy_labels/

							'name' => array(
								'label'       => esc_html_x( 'Name', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'General name for the taxonomy, usually plural.', 'rename_taxonomies' ),
							),
							'singular_name' => array(
								'label'       => esc_html_x( 'Singular Name', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Name for one item of the taxonomy.', 'rename_taxonomies' ),
							),
							'search_items' => array(
								'label'       => esc_html_x( 'Search Items', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for taxonomy search form button.', 'rename_taxonomies' ),
							),
							'all_items' => array(
								'label'       => esc_html_x( 'All Items', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for all taxonomy items.', 'rename_taxonomies' ),
							),
							'edit_item' => array(
								'label'       => esc_html_x( 'Edit Item', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for editing the taxonomy item.', 'rename_taxonomies' ),
							),
							'view_item' => array(
								'label'       => esc_html_x( 'View Item', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for viewing the taxonomy item.', 'rename_taxonomies' ),
							),
							'update_item' => array(
								'label'       => esc_html_x( 'Update Item', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for updating the taxonomy item.', 'rename_taxonomies' ),
							),
							'add_new_item' => array(
								'label'       => esc_html_x( 'Add New Item', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for adding a new taxonomy item.', 'rename_taxonomies' ),
							),
							'new_item_name' => array(
								'label'       => esc_html_x( 'New Item Name', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Label for new taxonomy item name field.', 'rename_taxonomies' ),
							),
							'not_found' => array(
								'label'       => esc_html_x( 'Not Found', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Used in the meta box and taxonomy list table.', 'rename_taxonomies' ),
							),
							'no_terms' => array(
								'label'       => esc_html_x( 'No Terms', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'Ued in the posts and media list tables.', 'rename_taxonomies' ),
							),
							'items_list_navigation' => array(
								'label'       => esc_html_x( 'Items List Navigation', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'String for the table pagination hidden heading.', 'rename_taxonomies' ),
							),
							'items_list' => array(
								'label'       => esc_html_x( 'Items List', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
								'description' => esc_html__( 'String for the table hidden heading.', 'rename_taxonomies' ),
							),

							// Hierarchical only

								'parent_item' => array(
									'label'       => esc_html_x( 'Parent Item', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'Parent taxonomy label.', 'rename_taxonomies' ),
									'condition'   => 'is_hierarchical',
								),
								'parent_item_colon' => array(
									'label'       => esc_html_x( 'Parent Item Colon', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'The same as parent_item, but with colon (:) in the end.', 'rename_taxonomies' ),
									'condition'   => 'is_hierarchical',
								),

							// Non-hierarchical only

								'popular_items' => array(
									'label'       => esc_html_x( 'Popular Items', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'Popular items label.', 'rename_taxonomies' ),
									'condition'   => 'is_not_hierarchical',
								),
								'separate_items_with_commas' => array(
									'label'       => esc_html_x( 'Separate Items With Commas', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'This is used in the meta box.', 'rename_taxonomies' ),
									'condition'   => 'is_not_hierarchical',
								),
								'add_or_remove_items' => array(
									'label'       => esc_html_x( 'Add or Remove Items', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'Used in the meta box when JavaScript is disabled.', 'rename_taxonomies' ),
									'condition'   => 'is_not_hierarchical',
								),
								'choose_from_most_used' => array(
									'label'       => esc_html_x( 'Choose From Most Used', 'Form field label. Taxonomy label name.', 'rename_taxonomies' ),
									'description' => esc_html__( 'Used in the meta box.', 'rename_taxonomies' ),
									'condition'   => 'is_not_hierarchical',
								),

						);

		} // /set_variables



		/**
		 * Admin notice
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $text  Notice text.
		 * @param  string $class Notice appearance class.
		 */
		public static function admin_notice( $text, $class = 'updated' ) {

			// Helper variables

				$output = '';

				$text = trim( $text );


			// Requirements check

				if ( empty( $text ) ) {
					return;
				}


			// Processing

				$output .= '<div class="' . esc_attr( $class ) . ' notice is-dismissible">';
				$output .= '<p>';
				$output .= $text;
				$output .= '</p>';
				$output .= '</div>';


			// Output

				return $output;

		} // /admin_notice





	/**
	 * 40) Localization
	 */

		/**
		 * Load textdomain
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function load_textdomain() {

			// Processing

				load_plugin_textdomain(
						'rename-taxonomies',
						false,
						dirname( plugin_basename( __FILE__ ) ) . '/languages/'
					);

		} // /load_textdomain





} // /WebMan_Rename_Taxonomies

add_action( 'plugins_loaded', 'WebMan_Rename_Taxonomies::init' );
