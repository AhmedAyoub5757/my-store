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

/**
 * Product card, shared by the Featured Products widget and (later) the shop.
 *
 * @param WC_Product $product Product.
 * @param array      $args    show_category, show_add, add_text, index.
 */
function my_store_product_card( $product, $args = array() ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'show_category' => true,
			'show_add'      => true,
			'add_text'      => __( 'Add to cart', 'my-store' ),
			'index'         => 0,
		)
	);

	$id      = $product->get_id();
	$name    = $product->get_name();
	$link    = $product->get_permalink();
	$image   = (int) $product->get_image_id();
	$gallery = $product->get_gallery_image_ids();
	$hover   = ! empty( $gallery ) ? (int) $gallery[0] : 0;
	$can_buy = $product->is_purchasable() && $product->is_in_stock();
	$ajax    = $can_buy && $product->supports( 'ajax_add_to_cart' );

	// Category (first one that isn't "Uncategorized").
	$cat = '';
	if ( $args['show_category'] ) {
		$terms = get_the_terms( $id, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( 'uncategorized' !== $term->slug ) {
					$cat = $term->name;
					break;
				}
			}
		}
	}

	// Badge.
	$badge       = '';
	$badge_class = 'ms-card__badge';
	if ( ! $product->is_in_stock() ) {
		$badge        = __( 'Sold out', 'my-store' );
		$badge_class .= ' ms-card__badge--out';
	} elseif ( $product->is_on_sale() ) {
		$badge   = __( 'Sale', 'my-store' );
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $product->is_type( 'simple' ) && $regular > 0 && $sale > 0 && $sale < $regular ) {
			$badge = '-' . round( ( 1 - $sale / $regular ) * 100 ) . '%';
		}
	}

	// Add to cart button.
	$btn_class = 'ms-card__add add_to_cart_button' . ( $ajax ? ' ajax_add_to_cart' : '' );
	$btn_text  = $ajax ? $args['add_text'] : $product->add_to_cart_text();
	?>
	<article class="ms-card<?php echo $hover ? ' ms-card--swap' : ''; ?>" data-ms-reveal style="--i:<?php echo (int) ( $args['index'] % 4 ); ?>;">

		<div class="ms-card__media">
			<a class="ms-card__link" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $name ); ?>" tabindex="-1">
				<?php
				if ( $image ) {
					echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'ms-card__img ms-card__img--main', 'alt' => $name, 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo wc_placeholder_img( 'large', array( 'class' => 'ms-card__img ms-card__img--main' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				}

				if ( $hover ) {
					echo wp_get_attachment_image( $hover, 'large', false, array( 'class' => 'ms-card__img ms-card__img--hover', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
			</a>

			<?php if ( $badge ) : ?>
				<span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>

			<?php if ( $args['show_add'] ) : ?>
				<a
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					class="<?php echo esc_attr( $btn_class ); ?>"
					data-product_id="<?php echo esc_attr( $id ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					data-quantity="1"
					aria-label="<?php echo esc_attr( wp_strip_all_tags( $product->add_to_cart_description() ) ); ?>"
					rel="nofollow"
				><span><?php echo esc_html( $btn_text ); ?></span></a>
			<?php endif; ?>
		</div>

		<div class="ms-card__info">
			<?php if ( $cat ) : ?>
				<p class="ms-card__cat"><?php echo esc_html( $cat ); ?></p>
			<?php endif; ?>

			<h3 class="ms-card__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a></h3>

			<div class="ms-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		</div>

	</article>
	<?php
}

/**
 * Products per page on the shop and category pages.
 */
function my_store_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'my_store_products_per_page', 20 );