<?php
defined( 'ABSPATH' ) || exit;

// Keep the real catalog query safe from anything Elementor switches while rendering.
$GLOBALS['ms_shop_query'] = $GLOBALS['wp_query'];

get_header();

$shop_id = wc_get_page_id( 'shop' );
$built   = false;

if ( is_shop() && $shop_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
	$document = \Elementor\Plugin::$instance->documents->get( $shop_id );

	if ( $document && $document->is_built_with_elementor() ) {
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $shop_id ); // phpcs:ignore WordPress.Security.EscapeOutput
		$built = true;
	}
}

if ( ! $built ) {
	my_store_render_shop( $GLOBALS['wp_query'], my_store_shop_defaults() );
}

get_footer();