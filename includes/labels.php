<?php
/**
 * Array of taxonomy label keys, names and descriptions.
 *
 * @link  https://developer.wordpress.org/reference/classes/wp_taxonomy/get_default_labels/
 * @link  https://developer.wordpress.org/reference/functions/get_taxonomy_labels/
 *
 * @package    Rename Taxonomies
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.3.0
 *
 * @param  array $labels
 */

$labels = array(

	'name' => array(
		'label'       => esc_html_x( 'Name', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'General name for the taxonomy, usually plural.', 'rename-taxonomies' ),
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

	'item_link' => array(
		'label'       => esc_html_x( 'Item Link', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'Title for a navigation link block variation in block editor.', 'rename-taxonomies' ),
	),

	'item_link_description' => array(
		'label'       => esc_html_x( 'Item Link Description', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'Description for a navigation link block variation in block editor.', 'rename-taxonomies' ),
	),

	'name_field_description' => array(
		'label'       => esc_html_x( '"Name" Field Description', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'Description for the "Name" field on edit item screen.', 'rename-taxonomies' ),
	),

	'slug_field_description' => array(
		'label'       => esc_html_x( '"Slug" Field Description', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'Description for the "Slug" field on edit item screen.', 'rename-taxonomies' ),
	),

	'desc_field_description' => array(
		'label'       => esc_html_x( '"Description" Field Description', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
		'description' => esc_html__( 'Description for the "Description" field on edit item screen.', 'rename-taxonomies' ),
	),

	// Hierarchical only.

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

		'filter_by_item' => array(
			'label'       => esc_html_x( 'Filter by Item', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
			'description' => esc_html__( 'Used in the posts list table.', 'rename-taxonomies' ),
			'condition'   => 'is_hierarchical',
		),

		'parent_field_description' => array(
			'label'       => esc_html_x( 'Parent Field Description', 'Form field label. Taxonomy label name.', 'rename-taxonomies' ),
			'description' => esc_html__( 'Description for the parent field on edit item screen.', 'rename-taxonomies' ),
			'condition'   => 'is_hierarchical',
		),

	// Non-hierarchical only.

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
