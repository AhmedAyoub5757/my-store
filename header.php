<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'my-store' ); ?></a>

<?php if ( ! is_singular( 'ms_part' ) ) : ?>
<header class="ms-site-header" id="site-header">
	<?php my_store_render_part( 'header' ); ?>
</header>
<?php endif; ?>

<main id="main">