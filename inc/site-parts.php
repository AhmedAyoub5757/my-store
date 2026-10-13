<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site Parts: global pieces (header, footer) edited with Elementor.
 */
function my_store_register_site_parts() {
	register_post_type(
		'ms_part',
		array(
			'labels'              => array(
				'name'          => __( 'Site Parts', 'my-store' ),
				'singular_name' => __( 'Site Part', 'my-store' ),
				'add_new_item'  => __( 'Add New Site Part', 'my-store' ),
				'edit_item'     => __( 'Edit Site Part', 'my-store' ),
			),
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'site-part' ),
			'menu_icon'           => 'dashicons-layout',
			'supports'            => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'my_store_register_site_parts' );

/**
 * Enable Elementor on Site Parts without relying on the settings screen.
 */
// function my_store_site_parts_elementor_support() {
// 	add_post_type_support( 'ms_part', 'elementor' );
// }
// add_action( 'init', 'my_store_site_parts_elementor_support', 20 );

/**
 * Keep site parts hidden from visitors.
 */
function my_store_protect_site_parts() {
	if ( is_singular( 'ms_part' ) && ! current_user_can( 'edit_posts' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'my_store_protect_site_parts' );

/**
 * Output a site part (by slug) built with Elementor.
 */
function my_store_render_part( $slug ) {
	$part = get_page_by_path( $slug, OBJECT, 'ms_part' );

	if ( ! $part || 'publish' !== $part->post_status || ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $part->ID ); // phpcs:ignore WordPress.Security.EscapeOutput
}