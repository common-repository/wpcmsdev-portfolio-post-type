<?php
/*
Plugin Name: wpCMSdev Portfolio Post Type
Plugin URI:  http://wpcmsdev.com/plugins/portfolio-post-type/
Description: Registers a "Portfolio" custom post type.
Author:      wpCMSdev
Author URI:  http://wpcmsdev.com
Version:     1.0
Text Domain: wpcmsdev-portfolio-post-type
Domain Path: /languages
License:     GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Copyright (C) 2014  wpCMSdev

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Registers the "portfolio_item" post type.
 */
function wpcmsdev_portfolio_post_type_register() {

	$labels = array(
		'name'               => __( 'Portfolio Items',                    'wpcmsdev-portfolio-post-type' ),
		'menu_name'          => __( 'Portfolio',                          'wpcmsdev-portfolio-post-type' ),
		'singular_name'      => __( 'Portfolio Item',                     'wpcmsdev-portfolio-post-type' ),
		'all_items'          => __( 'All Portfolio Items',                'wpcmsdev-portfolio-post-type' ),
		'add_new'            => _x( 'Add New', 'portfolio item',          'wpcmsdev-portfolio-post-type' ),
		'add_new_item'       => __( 'Add New Portfolio Item',             'wpcmsdev-portfolio-post-type' ),
		'edit_item'          => __( 'Edit Portfolio Item',                'wpcmsdev-portfolio-post-type' ),
		'new_item'           => __( 'New Portfolio Item',                 'wpcmsdev-portfolio-post-type' ),
		'view_item'          => __( 'View Portfolio Item',                'wpcmsdev-portfolio-post-type' ),
		'search_items'       => __( 'Search Portfolio Items',             'wpcmsdev-portfolio-post-type' ),
		'not_found'          => __( 'No portfolio items found.',          'wpcmsdev-portfolio-post-type' ),
		'not_found_in_trash' => __( 'No portfolio items found in Trash.', 'wpcmsdev-portfolio-post-type' ),
	);

	$args = array(
		'labels'        => $labels,
		'menu_icon'     => 'dashicons-portfolio',
		'menu_position' => 5,
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => _x( 'portfolio', 'portfolio single post url slug', 'wpcmsdev-portfolio-post-type' ) ),
		'supports'      => array(
			'author',
			'comments',
			'custom-fields',
			'editor',
			'excerpt',
			'page-attributes',
			'revisions',
			'trackbacks',
			'thumbnail',
			'title',
		),
	);

	$args = apply_filters( 'wpcmsdev_portfolio_post_type_args', $args );

	register_post_type( 'portfolio_item', $args );

}
add_action( 'init', 'wpcmsdev_portfolio_post_type_register' );


/**
 * Registers the "portfolio_category" taxonomy.
 */
function wpcmsdev_portfolio_category_taxonomy_register() {

	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name',  'wpcmsdev-portfolio-post-type' ),
		'singular_name'     => _x( 'Category',   'taxonomy singular name', 'wpcmsdev-portfolio-post-type' ),
		'all_items'         => __( 'All Categories',                       'wpcmsdev-portfolio-post-type' ),
		'edit_item'         => __( 'Edit Categories',                      'wpcmsdev-portfolio-post-type' ),
		'view_item'         => __( 'View Category',                        'wpcmsdev-portfolio-post-type' ),
		'update_item'       => __( 'Update Category',                      'wpcmsdev-portfolio-post-type' ),
		'add_new_item'      => __( 'Add New Category',                     'wpcmsdev-portfolio-post-type' ),
		'new_item_name'     => __( 'New Category Name',                    'wpcmsdev-portfolio-post-type' ),
		'parent_item'       => __( 'Parent Category',                      'wpcmsdev-portfolio-post-type' ),
		'parent_item_colon' => __( 'Parent Category:',                     'wpcmsdev-portfolio-post-type' ),
		'search_items'      => __( 'Search Categories',                    'wpcmsdev-portfolio-post-type' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'rewrite'           => array( 'slug' => _x( 'portfolio-category', 'portfolio category taxonomy url slug', 'wpcmsdev-portfolio-post-type' ) ),
		'show_admin_column' => true,
	);

	$args = apply_filters( 'wpcmsdev_portfolio_category_taxonomy_args', $args );

	register_taxonomy( 'portfolio_category', 'portfolio_item', $args );

}
add_action( 'init', 'wpcmsdev_portfolio_category_taxonomy_register' );


/**
 * Flushes the site's rewrite rules.
 */
function wpcmsdev_portfolio_rewrite_flush() {

	wpcmsdev_portfolio_post_type_register();
	wpcmsdev_portfolio_category_taxonomy_register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpcmsdev_portfolio_rewrite_flush' );


/**
 * Loads the translation files.
 */
function wpcmsdev_portfolio_load_translations() {

	load_plugin_textdomain( 'wpcmsdev-portfolio-post-type', false, dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
}
add_action( 'plugins_loaded', 'wpcmsdev_portfolio_load_translations' );


/**
 * Initializes additional functionality when used with a theme that declares support for the plugin.
 */
function wpmcsdev_portfolio_additional_functionality_init() {

	if ( current_theme_supports( 'wpcmsdev-portfolio-post-type' ) ) {
		add_action( 'admin_enqueue_scripts',                     'wpcmsdev_portfolio_manage_posts_css' );
		add_action( 'manage_portfolio_item_posts_custom_column', 'wpcmsdev_portfolio_manage_posts_columm_content' );
		add_filter( 'cmb2_meta_boxes',                           'wpcmsdev_portfolio_meta_box' );
		add_filter( 'manage_edit-portfolio_item_columns',        'wpcmsdev_portfolio_manage_posts_columns' );
	}
}
add_action( 'after_setup_theme', 'wpmcsdev_portfolio_additional_functionality_init', 11 );


/**
 * Registers custom columns for the Manage Portfolio Items admin page.
 */
function wpcmsdev_portfolio_manage_posts_columns( $columns ) {

	$column_order     = array( 'order'       => __( 'Order', 'wpcmsdev-portfolio-post-type' ) );
	$column_thumbnail = array( 'thumbnail'   => __( 'Image', 'wpcmsdev-portfolio-post-type' ) );
	$column_url       = array( 'project_url' => __( 'Project URL', 'wpcmsdev-portfolio-post-type' ) );

	$columns = array_slice( $columns, 0, 2, true ) + $column_thumbnail + $column_url + $column_order + array_slice( $columns, 2, null, true );

	return $columns;
}


/**
 * Outputs the custom column content for the Manage Portfolio Items admin page.
 */
function wpcmsdev_portfolio_manage_posts_columm_content( $column ) {

	global $post;

	switch( $column ) {

		case 'order':
			$order = $post->menu_order;
			if ( 0 === $order ) {
				echo '<span class="default-value">' . $order . '</span>';
			} else {
				echo $order;
			}
			break;

		case 'project_url':
			$url = get_post_meta( $post->ID, 'project_url', true );
			if ( $url ) {
				printf( '<a href="%1$s">%1$s</a>', esc_url( $url ) );
			} else {
				echo '&#8212;';
			}
			break;

		case 'thumbnail':
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			} else {
				echo '&#8212;';
			}
			break;
	}
}


/**
 * Outputs the custom columns CSS used on the Manage Portfolio Items admin page.
 */
function wpcmsdev_portfolio_manage_posts_css() {

	global $pagenow, $typenow;
	if ( ! ( 'edit.php' == $pagenow && 'portfolio_item' == $typenow ) ) {
		return;
	}

?>
<style>
	.edit-php .posts .column-order,
	.edit-php .posts .column-thumbnail {
		width: 10%;
	}
	.edit-php .posts .column-thumbnail img {
		width: 50px;
		height: auto;
	}
	.edit-php .posts .column-order .default-value {
		color: #bbb;
	}
</style>
<?php
}


/**
 * Creates the Portfolio Settings meta box and fields.
 */
function wpcmsdev_portfolio_meta_box( $meta_boxes ) {

	$meta_boxes['portfolio-settings'] = array(
		'id'           => 'portfolio-settings',
		'title'        => __( 'Portfolio Settings', 'wpcmsdev-portfolio-post-type' ),
		'object_types' => array( 'portfolio_item' ),
		'fields'       => array(
			array(
				'name' => __( 'Project URL', 'wpcmsdev-portfolio-post-type' ),
				'id'   => 'project_url',
				'type' => 'text_url',
			),
		),
	);

	return $meta_boxes;
}
