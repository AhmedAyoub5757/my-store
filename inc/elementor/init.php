<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget category shown in the Elementor panel.
 */
function my_store_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'my-store',
		array(
			'title' => __( 'My Store', 'my-store' ),
			'icon'  => 'eicon-font',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'my_store_elementor_category' );

/**
 * Register our widgets.
 */
function my_store_register_widgets( $widgets_manager ) {
	require_once MY_STORE_DIR . '/inc/elementor/widgets/class-navbar-widget.php';
	require_once MY_STORE_DIR . '/inc/elementor/widgets/class-hero-widget.php';
	require_once MY_STORE_DIR . '/inc/elementor/widgets/class-categories-widget.php';
	require_once MY_STORE_DIR . '/inc/elementor/widgets/class-products-widget.php';

	
	$widgets_manager->register( new My_Store_Navbar_Widget() );
	$widgets_manager->register( new My_Store_Hero_Widget() );
	$widgets_manager->register( new My_Store_Categories_Widget() );
	$widgets_manager->register( new My_Store_Products_Widget() );
}
add_action( 'elementor/widgets/register', 'my_store_register_widgets' );