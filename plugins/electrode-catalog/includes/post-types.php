<?php
/**
 * Post types and taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'elc_register_post_types' );

/**
 * Register electrode CPT, its taxonomies, and the private inquiry CPT.
 */
function elc_register_post_types() {
	register_post_type(
		'electrode',
		array(
			'labels'       => array(
				'name'               => 'الکترودها',
				'singular_name'      => 'الکترود',
				'menu_name'          => 'الکترودها',
				'add_new'            => 'افزودن الکترود',
				'add_new_item'       => 'افزودن الکترود جدید',
				'edit_item'          => 'ویرایش الکترود',
				'new_item'           => 'الکترود جدید',
				'view_item'          => 'مشاهده الکترود',
				'search_items'       => 'جستجوی الکترود',
				'not_found'          => 'الکترودی یافت نشد',
				'all_items'          => 'همه الکترودها',
				'featured_image'     => 'تصویر محصول',
				'set_featured_image' => 'تنظیم تصویر محصول',
			),
			'public'       => true,
			'has_archive'  => 'products',
			'rewrite'      => array( 'slug' => 'electrode', 'with_front' => false ),
			'menu_icon'    => 'dashicons-hammer',
			'menu_position' => 5,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'electrode_type',
		'electrode',
		array(
			'labels'            => array(
				'name'          => 'دسته‌بندی الکترود',
				'singular_name' => 'دسته‌بندی',
				'add_new_item'  => 'افزودن دسته‌بندی',
				'edit_item'     => 'ویرایش دسته‌بندی',
				'all_items'     => 'همه دسته‌ها',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'electrode-type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'electrode_brand',
		'electrode',
		array(
			'labels'            => array(
				'name'          => 'برندها',
				'singular_name' => 'برند',
				'add_new_item'  => 'افزودن برند',
				'edit_item'     => 'ویرایش برند',
				'all_items'     => 'همه برندها',
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'brand', 'with_front' => false ),
		)
	);

	register_post_type(
		'elc_inquiry',
		array(
			'labels'          => array(
				'name'          => 'استعلام‌های قیمت',
				'singular_name' => 'استعلام',
				'menu_name'     => 'استعلام‌ها',
				'all_items'     => 'همه استعلام‌ها',
				'edit_item'     => 'جزئیات استعلام',
				'not_found'     => 'هنوز استعلامی ثبت نشده است',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 6,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

/**
 * Products archive: sort by menu order then title, allow filters via query vars.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( 'electrode' ) || $query->is_tax( array( 'electrode_type', 'electrode_brand' ) ) ) {
			$query->set( 'posts_per_page', 12 );
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );

			$diameter = isset( $_GET['d'] ) ? sanitize_text_field( wp_unslash( $_GET['d'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( '' !== $diameter && preg_match( '/^[0-9.]+$/', $diameter ) ) {
				$query->set(
					'meta_query',
					array(
						array(
							'key'     => '_elc_diameters',
							'value'   => '(^|[ ,])' . str_replace( '.', '[.]', $diameter ) . '($|[ ,])',
							'compare' => 'REGEXP',
						),
					)
				);
			}
			$stock = isset( $_GET['stock'] ) ? sanitize_key( wp_unslash( $_GET['stock'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'in' === $stock ) {
				$mq   = (array) $query->get( 'meta_query' );
				$mq[] = array( 'key' => '_elc_stock', 'value' => 'in' );
				$query->set( 'meta_query', $mq );
			}
		}
	}
);
