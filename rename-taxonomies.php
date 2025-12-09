<?php defined( 'ABSPATH' ) or exit;
/**
 * Plugin Name:  Rename Taxonomies by WebMan
 * Plugin URI:   https://www.webmandesign.eu/portfolio/rename-taxonomies-wordpress-plugin/
 * Description:  Customizes text and menu labels for any registered taxonomy using a simple interface.
 * Version:      1.3.0
 * Author:       WebMan Design, Oliver Juhas
 * Author URI:   https://www.webmandesign.eu/
 * License:      GPL-3.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:  rename-taxonomies
 * Domain Path:  /languages
 *
 * Requires PHP:       7.0
 * Requires at least:  6.0
 *
 * This plugin was inspired by "Custom Post Type Editor" plugin
 * Copyright (c) 2012-2015 OM4, https://om4.com.au
 * Distributed under the terms of the GNU GPL
 * https://wordpress.org/plugins/cpt-editor/
 *
 * @package  Rename Taxonomies
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class WebMan_Rename_Taxonomies {

	public static $plugin_slug        = 'rename-taxonomies';
	public static $option_name        = 'webman_rename_taxonomies';
	public static $screen_option_name = 'taxonomies_per_page';
	public static $capability         = 'manage_options';
	public static $per_page           = 10;
	public static $default_tax_labels = array();
	public static $skipped_taxonomies = array(

		'nav_menu',
		'link_category',
		'post_format',

		'wp_theme',
		'wp_template_part_area',
		'wp_pattern_category',
	);

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.2.1
	 *
	 * @return  void
	 */
	public static function init() {

		// Variables

			define( 'WMRT_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );


		// Processing

			// Hooks

				// Actions

					add_action( 'admin_menu', __CLASS__ . '::admin_menu' );

					add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_styles' );

				// Filters

					add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __CLASS__ . '::get_action_links' );

					add_filter( 'set-screen-option', __CLASS__ . '::set_screen_option', 10, 3 );

					add_filter( 'register_taxonomy_args', __CLASS__ . '::get_taxonomy_args', 10, 2 );

	} // /init

	/**
	 * Admin menu.
	 *
	 * @since    1.0.0
	 * @version  1.1.0
	 *
	 * @return  void
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
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function admin_page() {

		// Variables

			$action = $_GET['action'] ?? '';


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
	 *
	 * @return  void
	 */
	public static function admin_styles( string $hook ) {

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
	 *
	 * @return  void
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
	 * @version  1.3.0
	 *
	 * @param  mixed  $screen_option  Whether to save or skip saving the value. Default false.
	 * @param  string $option         The option name.
	 * @param  int    $value          The number of rows to use.
	 *
	 * @return  mixed
	 */
	public static function set_screen_option( $screen_option, string $option, int $value ) {

		// Output

			if ( self::$screen_option_name === $option ) {
				return absint( $value );
			} else {
				return $screen_option;
			}

	} // /set_screen_option

	/**
	 * Get taxonomy args with new labels.
	 *
	 * First store the default predefined taxonomy labels in `self::$default_tax_labels`.
	 * Then, if we have new labels for taxonomy, apply those.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array  $args      Array of arguments for registering a taxonomy.
	 * @param  string $taxonomy  Taxonomy key.
	 *
	 * @return  array
	 */
	public static function get_taxonomy_args( array $args, string $taxonomy ): array {

		// Variables

			if ( is_admin() ) {
				self::$default_tax_labels[ $taxonomy ] = ( isset( $args['labels'] ) ) ? ( (array) $args['labels'] ) : ( array() );
			}

			$taxonomy_labels = (array) get_option( self::$option_name );


		// Requirements check

			if (
				empty( $taxonomy_labels['taxonomies'] )
				|| ! is_array( $taxonomy_labels['taxonomies'] )
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
						(array) $args['labels'], // We have to force the array here!
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
	 *
	 * @return  array
	 */
	public static function get_action_links( array $links ): array {

		// Variables

			$plugin_settings_url = add_query_arg(
				'page',
				self::$plugin_slug,
				get_admin_url( null, 'tools.php' )
			);


		// Processing

			$links[] =
				'<a href="' . esc_url( $plugin_settings_url ) . '">'
				. esc_html_x( 'Settings', 'Plugin action link.', 'rename-taxonomies' )
				. '</a>';


		// Output

			return $links;

	} // /get_action_links

	/**
	 * Get admin notice html.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  string $text   Notice text.
	 * @param  string $class  Notice appearance class.
	 *
	 * @return  string
	 */
	public static function get_admin_notice_html( string $text, string $class = 'updated' ): string {

		// Variables

			$text = trim( $text );


		// Requirements check

			if ( empty( $text ) ) {
				return '';
			}


		// Output

			return
				'<div class="' . esc_attr( $class ) . ' notice is-dismissible">'
				. '<p>' . $text . '</p>'
				. '</div>';

	} // /admin_notice

	/**
	 * Get default taxonomies labels.
	 *
	 * This only works in admin. There is no need for it on front-end.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  array
	 */
	public static function get_default_labels(): array {

		// Output

			return (array) self::$default_tax_labels; // Set in `self::get_taxonomy_args()`.

	} // /get_default_labels

	/**
	 * Get taxonomy label keys, names and descriptions.
	 *
	 * @link  https://developer.wordpress.org/reference/classes/wp_taxonomy/get_default_labels/
	 * @link  https://developer.wordpress.org/reference/functions/get_taxonomy_labels/
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  array
	 */
	public static function get_label_keys(): array {

		// Processing

			include WMRT_PLUGIN_PATH . 'includes/labels.php';

			if ( ! isset( $labels ) ) {
				$labels = array();
			}


		// Output

			return (array) $labels;

	} // /get_label_keys

}

// Loading with higher priority for multilingual plugins compatibility.
add_action( 'plugins_loaded', 'WebMan_Rename_Taxonomies::init', 20 );
