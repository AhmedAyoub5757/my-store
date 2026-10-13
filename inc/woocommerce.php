<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart count badge markup.
 */
function my_store_cart_count_html() {
	$count   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	$editing = class_exists( '\Elementor\Plugin' )
		&& \Elementor\Plugin::$instance->editor
		&& \Elementor\Plugin::$instance->editor->is_edit_mode();

	$class = 'ms-navbar__cart-count';
	if ( 0 === $count && ! $editing ) {
		$class .= ' is-empty';
	}

	return '<span class="' . esc_attr( $class ) . '">' . esc_html( $count ) . '</span>';
}

/**
 * Refresh the badge after AJAX add-to-cart.
 */
function my_store_cart_count_fragment( $fragments ) {
	$fragments['span.ms-navbar__cart-count'] = my_store_cart_count_html();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'my_store_cart_count_fragment' );