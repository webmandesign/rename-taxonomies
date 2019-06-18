<?php defined( 'ABSPATH' ) or exit;
/**
 * Plugin Name:        Rename Taxonomies by WebMan
 * Plugin URI:         https://www.webmandesign.eu/
 * Description:        Customizes text and menu labels for any registered taxonomy using a simple interface.
 * Version:            1.1.0
 * Author:             WebMan Design, Oliver Juhas
 * Author URI:         https://www.webmandesign.eu/
 * Text Domain:        rename-taxonomies
 * Domain Path:        /languages
 * License:            GNU General Public License v3
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least:  4.3
 * Tested up to:       5.2
 *
 * This plugin was inspired by "Custom Post Type Editor" plugin
 * Copyright (c) 2012-2015 OM4, https://om4.com.au
 * Distributed under the terms of the GNU GPL
 * https://wordpress.org/plugins/cpt-editor/
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://www.webmandesign.eu
 *
 * Contents:
 *
 *  0) Init
 * 10) Setup
 * 20) Getters
 */
class WebMan_Rename_Taxonomies {





	/**
	 * 0) Init
	 */

		public static $plugin_slug = 'rename-taxonomies';
		public static $option_name = 'webman_rename_taxonomies';
		public static $screen_option_name = 'taxonomies_per_page';

		public static $capability = 'manage_options';
		public static $per_page = 10;

		public static $default_tax_labels = array();



		/**
		 * Initialization.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function init() {

			// Variables

				define( 'WMRT_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );


			// Processing

				// Hooks

					// Actions

						// Load after the plugin class is loaded (see below).
						add_action( 'plugins_loaded', __CLASS__ . '::load_textdomain', 25 );

						add_action( 'admin_menu', __CLASS__ . '::admin_menu' );
						add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_styles' );

					// Filters

						add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __CLASS__ . '::get_action_links' );

						add_filter( 'set-screen-option', __CLASS__ . '::set_screen_option', 10, 3 );

						add_filter( 'register_taxonomy_args', __CLASS__ . '::get_taxonomy_args', 10, 2 );

		} // /init





	/**
	 * 20) Setup
	 */

		/**
		 * Load textdomain.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function load_textdomain() {

			// Processing

				load_plugin_textdomain(
					self::$plugin_slug,
					false,
					WMRT_PLUGIN_PATH . 'languages'
				);

		} // /load_textdomain



		/**
		 * Admin menu.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function admin_menu() {

			// Processing

				$hook = add_submenu_page(
					'tools.php',
					esc_html__( 'Rename Taxonomies', 'rename-taxonomies' ),
					esc_html__( 'Rename Taxonomies', 'rename-taxonomies' ),
					self::$capability,
					self::$plugin_slug,
					__CLASS__ . '::admin_page'
				);

				add_action( 'load-' . $hook, __CLASS__ . '::screen_options' );

		} // /admin_menu



		/**
		 * Render admin page.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function admin_page() {

			// Variables

				$action = ( isset( $_GET['action'] ) ) ? ( $_GET['action'] ) : ( '' );


			// Processing

				echo '<div class="wrap wrap-rename-taxonomies">';

				switch ( $action ) {

					case 'edit':
						require_once WMRT_PLUGIN_PATH . 'templates/edit.php';
						break;

					default:
						require_once WMRT_PLUGIN_PATH . 'templates/list.php';
						break;

				}

				echo '</div>';

		} // /admin_page



		/**
		 * Admin styles.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 *
		 * @param  string $hook
		 */
		public static function admin_styles( $hook ) {

			// Requirements check

				if ( 'tools_page_' . self::$plugin_slug !== $hook ) {
					return;
				}


			// Processing

				wp_enqueue_style(
					self::$plugin_slug,
					plugin_dir_url( __FILE__ ) . 'assets/css/style.css'
				);

		} // /admin_styles



		/**
		 * Registering screen options.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function screen_options() {

			// Processing

				add_screen_option( 'per_page', array(
					'label'   => esc_html__( 'Number of items per page:', 'rename-taxonomies' ),
					'default' => self::$per_page,
					'option'  => self::$screen_option_name,
				) );

		} // /screen_options



		/**
		 * Saving screen options.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 *
		 * @param  bool   $keep    Whether to save or skip saving the screen option value. Default false.
		 * @param  string $option  The option name.
		 * @param  int    $value   The number of rows to use.
		 */
		public static function set_screen_option( $keep, $option, $value ) {

			// Output

				if ( self::$screen_option_name === $option ) {
					return absint( $value );
				} else {
					return $keep;
				}

		} // /set_screen_option





	/**
	 * 20) Getters
	 */

		/**
		 * Get taxonomy args with new labels.
		 *
		 * First store the default predefined taxonomy labels in `self::$default_tax_labels`.
		 * Then, if we have new labels for taxonomy, apply those.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 *
     * @param array  $args     Array of arguments for registering a taxonomy.
     * @param string $taxonomy Taxonomy key.
		 */
		public static function get_taxonomy_args( $args, $taxonomy ) {

			// Variables

				if ( is_admin() ) {
					self::$default_tax_labels[ $taxonomy ] = ( isset( $args['labels'] ) ) ? ( (array) $args['labels'] ) : ( array() );
				}

				$taxonomy_labels = get_option( self::$option_name );


			// Requirements check

				if (
					! isset( $taxonomy_labels['taxonomies'] )
					|| ! is_array( $taxonomy_labels['taxonomies'] )
					|| empty( $taxonomy_labels['taxonomies'] )
				) {
					return $args;
				}


			// Processing

				foreach ( $taxonomy_labels['taxonomies'] as $taxonomy_key => $new_labels ) {
					$new_labels = array_filter( (array) $new_labels );

					if (
						$taxonomy_key == $taxonomy
						&& ! empty( $new_labels )
					) {

						// Multilingual plugin compatibility (WPML, Polylang).
						if ( function_exists( 'icl_t' ) ) {
							foreach ( $new_labels as $label_key => $label_text ) {
								$new_labels[ $label_key ] = icl_t(
									self::$plugin_slug,
									$taxonomy . '[' . $label_key . ']',
									$label_text
								);
							}
						}

						if ( ! isset( $args['labels'] ) ) {
							$args['labels'] = array();
						}

						if ( ! isset( $new_labels['menu_name'] ) ) {
							$new_labels['menu_name'] = $new_labels['name'];
						}

						$args['labels'] = array_merge(
							$args['labels'],
							$new_labels
						);

					}
				}


			// Output

				return $args;

		} // /get_taxonomy_args



		/**
		 * Get plugin action links.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 *
		 * @param  array $links
		 */
		public static function get_action_links( $links ) {

			// Variables

				$plugin_settings_url = add_query_arg(
					'page',
					self::$plugin_slug,
					get_admin_url( null, 'tools.php' )
				);


			// Processing

				$links[] = '<a href="' . esc_url( $plugin_settings_url ) . '">'
					. esc_html_x( 'Settings', 'Plugin action link.', 'rename-taxonomies' )
					. '</a>';


			// Output

				return $links;

		} // /get_action_links



		/**
		 * Get admin notice html.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 *
		 * @param  string $text  Notice text.
		 * @param  string $class Notice appearance class.
		 */
		public static function get_admin_notice_html( $text, $class = 'updated' ) {

			// Variables

				$text = trim( $text );


			// Requirements check

				if ( empty( $text ) ) {
					return;
				}


			// Output

				return '<div class="' . esc_attr( $class ) . ' notice is-dismissible"><p>' . $text . '</p></div>';

		} // /admin_notice



		/**
		 * Get default taxonomies labels.
		 *
		 * This only works in admin. There is no need for it on front-end.
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function get_default_labels() {

			// Output

				return self::$default_tax_labels;

		} // /get_default_labels



		/**
		 * Get taxonomy label keys, names and descriptions.
		 *
		 * @since    1.0.0
		 * @version  1.1.0
		 */
		public static function get_label_keys() {

			// Output

				return array(
					// From @link  https://developer.wordpress.org/reference/functions/get_taxonomy_labels/

					'name' => array(
						'label'       => esc_html_x( 'Name', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'General name for the taxonomy, usually plural.', 'rename-taxonomies' ),
					),

						'menu_name' => array(
							'label'       => esc_html_x( 'Menu Name', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'Defaults to "Name" value.', 'rename-taxonomies' ),
						),

					'singular_name' => array(
						'label'       => esc_html_x( 'Singular Name', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Name for one item of this taxonomy.', 'rename-taxonomies' ),
					),

					'search_items' => array(
						'label'       => esc_html_x( 'Search Items', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for taxonomy search form button.', 'rename-taxonomies' ),
					),

					'all_items' => array(
						'label'       => esc_html_x( 'All Items', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for all taxonomy items.', 'rename-taxonomies' ),
					),

					'edit_item' => array(
						'label'       => esc_html_x( 'Edit Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for editing the taxonomy item.', 'rename-taxonomies' ),
					),

					'view_item' => array(
						'label'       => esc_html_x( 'View Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for viewing the taxonomy item archive page.', 'rename-taxonomies' ),
					),

					'update_item' => array(
						'label'       => esc_html_x( 'Update Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for updating the taxonomy item.', 'rename-taxonomies' ),
					),

					'add_new_item' => array(
						'label'       => esc_html_x( 'Add New Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for adding a new taxonomy item.', 'rename-taxonomies' ),
					),

					'new_item_name' => array(
						'label'       => esc_html_x( 'New Item Name', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for new taxonomy item name field.', 'rename-taxonomies' ),
					),

					'not_found' => array(
						'label'       => esc_html_x( 'Not Found', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Used in the meta box and taxonomy list table.', 'rename-taxonomies' ),
					),

					'no_terms' => array(
						'label'       => esc_html_x( 'No Terms', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Used in the posts and media list tables.', 'rename-taxonomies' ),
					),

					'items_list_navigation' => array(
						'label'       => esc_html_x( 'Items List Navigation', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for the table pagination hidden heading.', 'rename-taxonomies' ),
					),

					'items_list' => array(
						'label'       => esc_html_x( 'Items List', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label for the table hidden heading.', 'rename-taxonomies' ),
					),

					'most_used' => array(
						'label'       => esc_html_x( 'Most Used', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Title for the Most Used tab.', 'rename-taxonomies' ),
					),

					'back_to_items' => array(
						'label'       => esc_html_x( 'Back to Items', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
						'description' => esc_html__( 'Label displayed after a term has been updated.', 'rename-taxonomies' ),
					),

					// Hierarchical only

						'parent_item' => array(
							'label'       => esc_html_x( 'Parent Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'Parent taxonomy label.', 'rename-taxonomies' ),
							'condition'   => 'is_hierarchical',
						),

						'parent_item_colon' => array(
							'label'       => esc_html_x( 'Parent Item Colon', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'The same as parent_item, but with colon (:) in the end.', 'rename-taxonomies' ),
							'condition'   => 'is_hierarchical',
						),

					// Non-hierarchical only

						'popular_items' => array(
							'label'       => esc_html_x( 'Popular Items', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'Popular items label.', 'rename-taxonomies' ),
							'condition'   => 'is_not_hierarchical',
						),

						'separate_items_with_commas' => array(
							'label'       => esc_html_x( 'Separate Items With Commas', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'This is used in the meta box.', 'rename-taxonomies' ),
							'condition'   => 'is_not_hierarchical',
						),

						'add_or_remove_items' => array(
							'label'       => esc_html_x( 'Add or Remove Items', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'Used in the meta box when JavaScript is disabled.', 'rename-taxonomies' ),
							'condition'   => 'is_not_hierarchical',
						),

						'choose_from_most_used' => array(
							'label'       => esc_html_x( 'Choose From Most Used', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
							'description' => esc_html__( 'Used in the meta box.', 'rename-taxonomies' ),
							'condition'   => 'is_not_hierarchical',
						),

				);

		} // /get_label_keys





} // /WebMan_Rename_Taxonomies

// Loading with higher priority for multilingual plugins compatibility.
add_action( 'plugins_loaded', 'WebMan_Rename_Taxonomies::init', 20 );
