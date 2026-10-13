<?php

/**
 * My Store theme functions.
 */

if (! defined('ABSPATH')) {
	exit;
}

define('MY_STORE_VERSION', '1.0.0');
define('MY_STORE_DIR', get_template_directory());
define('MY_STORE_URI', get_template_directory_uri());

require_once MY_STORE_DIR . '/inc/site-parts.php';
require_once MY_STORE_DIR . '/inc/woocommerce.php';
require_once MY_STORE_DIR . '/inc/elementor/init.php';

/**
 * Theme setup.
 */
function my_store_setup()
{
	load_theme_textdomain('my-store', MY_STORE_DIR . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
	add_theme_support('custom-logo');
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'my_store_setup');

/**
 * Enqueue styles and scripts.
 */
function my_store_assets()
{
	wp_enqueue_style(
		'my-store-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Jost:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'my-store-style',
		get_stylesheet_uri(),
		array('my-store-fonts'),
		MY_STORE_VERSION
	);

	wp_enqueue_style(
		'my-store-navbar',
		MY_STORE_URI . '/assets/css/navbar.css',
		array('my-store-style'),
		MY_STORE_VERSION
	);

	wp_enqueue_script(
		'my-store-navbar',
		MY_STORE_URI . '/assets/js/navbar.js',
		array(),
		MY_STORE_VERSION,
		true
	);

	wp_enqueue_style(
		'my-store-hero',
		MY_STORE_URI . '/assets/css/hero.css',
		array('my-store-style'),
		MY_STORE_VERSION
	);

	wp_enqueue_script(
		'my-store-hero',
		MY_STORE_URI . '/assets/js/hero.js',
		array(),
		MY_STORE_VERSION,
		true
	);

	wp_enqueue_style(
		'my-store-categories',
		MY_STORE_URI . '/assets/css/categories.css',
		array('my-store-style'),
		MY_STORE_VERSION
	);

	wp_enqueue_style(
		'my-store-product-card',
		MY_STORE_URI . '/assets/css/product-card.css',
		array('my-store-style'),
		MY_STORE_VERSION
	);

	wp_enqueue_style(
		'my-store-products',
		MY_STORE_URI . '/assets/css/products.css',
		array('my-store-product-card'),
		MY_STORE_VERSION
	);

	wp_enqueue_script(
		'my-store-reveal',
		MY_STORE_URI . '/assets/js/reveal.js',
		array(),
		MY_STORE_VERSION,
		true
	);

	wp_enqueue_style(
		'my-store-promo',
		MY_STORE_URI . '/assets/css/promo.css',
		array('my-store-style'),
		MY_STORE_VERSION
	);
}
add_action('wp_enqueue_scripts', 'my_store_assets');

/**
 * Flag JS-enabled browsers (used by the scroll reveal animation).
 */
function my_store_js_flag()
{
	echo "<script>document.documentElement.classList.add('ms-js');</script>\n";
}
add_action('wp_head', 'my_store_js_flag', 1);
