<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default options (used on category pages and as widget fallbacks).
 */
function my_store_shop_defaults() {
	return array(
		'eyebrow'       => __( 'Collection', 'my-store' ),
		'title'         => '',
		'desc'          => '',
		'show_crumbs'   => true,
		'show_cats'     => true,
		'show_price'    => true,
		'attrs'         => array( 'size', 'color' ),
		'all_label'     => __( 'All products', 'my-store' ),
		'filters_label' => __( 'Filters', 'my-store' ),
		'empty_text'    => __( 'No products match your filters.', 'my-store' ),
	);
}

/**
 * Current URL without pagination or query string.
 */
function my_store_shop_base_url() {
	global $wp;
	$path = preg_replace( '#/page/\d+/?$#', '', (string) $wp->request );
	return home_url( user_trailingslashit( $path ) );
}

/**
 * The query parameters we keep when building filter links.
 */
function my_store_shop_params() {
	$params  = array();
	$allowed = array( 'orderby', 'min_price', 'max_price', 's', 'post_type' );

	foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification
		$key = sanitize_key( $key );

		if ( ! is_scalar( $value ) ) {
			continue;
		}

		if ( in_array( $key, $allowed, true ) || 0 === strpos( $key, 'filter_' ) || 0 === strpos( $key, 'query_type_' ) ) {
			$params[ $key ] = sanitize_text_field( wp_unslash( $value ) );
		}
	}

	return $params;
}

/**
 * Page title.
 */
function my_store_shop_title( $static = false ) {
	if ( ! $static && is_search() ) {
		/* translators: %s: search term */
		return sprintf( __( 'Search results for “%s”', 'my-store' ), get_search_query() );
	}

	if ( ! $static && is_product_taxonomy() ) {
		return single_term_title( '', false );
	}

	$shop_id = wc_get_page_id( 'shop' );
	return $shop_id > 0 ? get_the_title( $shop_id ) : __( 'Shop', 'my-store' );
}

/**
 * Render the whole shop layout.
 *
 * @param WP_Query $query  Products query.
 * @param array    $o      Options (see my_store_shop_defaults()).
 * @param bool     $static True in the editor or off-shop pages: no live filtering.
 */
function my_store_render_shop( $query, $o = array(), $static = false ) {
	$o      = wp_parse_args( $o, my_store_shop_defaults() );
	$params = $static ? array() : my_store_shop_params();
	$base   = $static ? '#' : my_store_shop_base_url();

	wp_enqueue_script( 'wc-add-to-cart' );
	wp_enqueue_script( 'wc-cart-fragments' );

	/* Build a link from the current URL: set / unset parameters. */
	$link = function ( $set = array(), $unset = array() ) use ( $params, $base ) {
		if ( '#' === $base ) {
			return '#';
		}
		$p = array_merge( array_diff_key( $params, array_flip( $unset ) ), $set );
		return $p ? add_query_arg( urlencode_deep( $p ), $base ) : $base;
	};

	/* Hidden inputs that keep the other parameters when a form is submitted. */
	$hidden = function ( $skip = array() ) use ( $params ) {
		foreach ( $params as $key => $value ) {
			if ( ! in_array( $key, $skip, true ) ) {
				printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $key ), esc_attr( $value ) );
			}
		}
	};

	/* Header text. */
	$title     = $o['title'] ? $o['title'] : my_store_shop_title( $static );
	$desc_html = '';

	if ( ! $static && is_product_taxonomy() && term_description() ) {
		$desc_html = wp_kses_post( term_description() );
	} elseif ( ! empty( $o['desc'] ) && ( $static || ! is_search() ) ) {
		$desc_html = '<p>' . esc_html( $o['desc'] ) . '</p>';
	}

	/* Counts. */
	$found = (int) $query->found_posts;
	$per   = max( 1, (int) $query->get( 'posts_per_page' ) );
	$paged = max( 1, (int) $query->get( 'paged' ) );
	$from  = $found ? ( $paged - 1 ) * $per + 1 : 0;
	$to    = min( $found, $paged * $per );

	/* Categories. */
	$cats = array();
	if ( $o['show_cats'] ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => ! $static,
				'parent'     => 0,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		$cats  = is_array( $terms ) ? $terms : array();
	}
	$current_cat = ( ! $static && is_product_category() ) ? get_queried_object_id() : 0;
	$shop_url    = $static ? '#' : get_permalink( wc_get_page_id( 'shop' ) );

	/* Attribute groups (Size, Color…). */
	$groups = array();
	foreach ( (array) $o['attrs'] as $slug ) {
		$slug = sanitize_title( $slug );
		$tax  = wc_attribute_taxonomy_name( $slug );

		if ( ! $slug || ! taxonomy_exists( $tax ) ) {
			continue;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => ! $static,
			)
		);

		if ( is_array( $terms ) && $terms ) {
			$selected = isset( $params[ 'filter_' . $slug ] ) ? array_filter( explode( ',', $params[ 'filter_' . $slug ] ) ) : array();
			$groups[] = array(
				'slug'     => $slug,
				'label'    => wc_attribute_label( $tax ),
				'terms'    => $terms,
				'selected' => $selected,
			);
		}
	}

	/* Active filters. */
	$clear_keys = array( 'min_price', 'max_price' );
	foreach ( array_keys( $params ) as $key ) {
		if ( 0 === strpos( $key, 'filter_' ) || 0 === strpos( $key, 'query_type_' ) ) {
			$clear_keys[] = $key;
		}
	}
	$has_active = (bool) array_intersect( $clear_keys, array_keys( $params ) );

	/* Sorting. */
	$sorts = array(
		'menu_order' => __( 'Featured', 'my-store' ),
		'popularity' => __( 'Best selling', 'my-store' ),
		'rating'     => __( 'Top rated', 'my-store' ),
		'date'       => __( 'Newest', 'my-store' ),
		'price'      => __( 'Price: low to high', 'my-store' ),
		'price-desc' => __( 'Price: high to low', 'my-store' ),
	);
	if ( ! $static && is_search() ) {
		$sorts = array( 'relevance' => __( 'Relevance', 'my-store' ) ) + $sorts;
	}
	$orderby = isset( $params['orderby'] ) ? $params['orderby'] : key( $sorts );
	$crumb   = $title;
	?>
	<div class="ms-shop">

		<header class="ms-shop__head">
			<div class="ms-container">
				<?php if ( $o['show_crumbs'] ) : ?>
					<nav class="ms-shop__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'my-store' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'my-store' ); ?></a>
						<span aria-hidden="true">/</span>
						<?php if ( $static || is_shop() ) : ?>
							<span><?php echo esc_html( $crumb ); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( my_store_shop_title( true ) ); ?></a>
							<span aria-hidden="true">/</span>
							<span><?php echo esc_html( $crumb ); ?></span>
						<?php endif; ?>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $o['eyebrow'] ) ) : ?>
					<p class="ms-shop__eyebrow"><?php echo esc_html( $o['eyebrow'] ); ?></p>
				<?php endif; ?>

				<h1 class="ms-shop__title"><?php echo esc_html( $title ); ?></h1>

				<?php if ( $desc_html ) : ?>
					<div class="ms-shop__desc"><?php echo $desc_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
		</header>

		<div class="ms-container ms-shop__layout">

			<div class="ms-shop__overlay" data-ms-filters-close></div>

			<aside class="ms-shop__filters" id="ms-shop-filters" aria-label="<?php esc_attr_e( 'Filters', 'my-store' ); ?>">
				<div class="ms-shop__filters-top">
					<span class="ms-shop__filters-title"><?php echo esc_html( $o['filters_label'] ); ?></span>
					<button type="button" class="ms-shop__close" data-ms-filters-close aria-label="<?php esc_attr_e( 'Close filters', 'my-store' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
					</button>
				</div>

				<?php if ( $has_active ) : ?>
					<a class="ms-shop__clear" href="<?php echo esc_url( $link( array(), $clear_keys ) ); ?>"><?php esc_html_e( 'Clear all filters', 'my-store' ); ?></a>
				<?php endif; ?>

				<?php if ( $cats ) : ?>
					<section class="ms-shop__group">
						<h2 class="ms-shop__group-title"><?php esc_html_e( 'Categories', 'my-store' ); ?></h2>
						<ul class="ms-shop__cats">
							<li>
								<a href="<?php echo esc_url( $shop_url ); ?>" class="<?php echo ( ! $current_cat && ! $static ) ? 'is-active' : ''; ?>"><?php echo esc_html( $o['all_label'] ); ?></a>
							</li>
							<?php foreach ( $cats as $cat ) : ?>
								<li>
									<a href="<?php echo esc_url( $static ? '#' : get_term_link( $cat ) ); ?>" class="<?php echo ( $current_cat === (int) $cat->term_id ) ? 'is-active' : ''; ?>">
										<?php echo esc_html( $cat->name ); ?>
										<span class="ms-shop__count"><?php echo (int) $cat->count; ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $o['show_price'] ) : ?>
					<section class="ms-shop__group">
						<h2 class="ms-shop__group-title"><?php esc_html_e( 'Price', 'my-store' ); ?></h2>
						<form class="ms-shop__price" method="get" action="<?php echo esc_url( '#' === $base ? '' : $base ); ?>">
							<?php $hidden( array( 'min_price', 'max_price' ) ); ?>
							<label class="screen-reader-text" for="ms-min"><?php esc_html_e( 'Minimum price', 'my-store' ); ?></label>
							<input id="ms-min" type="number" min="0" step="1" name="min_price" placeholder="<?php echo esc_attr( __( 'Min', 'my-store' ) ); ?>" value="<?php echo esc_attr( isset( $params['min_price'] ) ? $params['min_price'] : '' ); ?>">
							<span aria-hidden="true">–</span>
							<label class="screen-reader-text" for="ms-max"><?php esc_html_e( 'Maximum price', 'my-store' ); ?></label>
							<input id="ms-max" type="number" min="0" step="1" name="max_price" placeholder="<?php echo esc_attr( __( 'Max', 'my-store' ) ); ?>" value="<?php echo esc_attr( isset( $params['max_price'] ) ? $params['max_price'] : '' ); ?>">
							<button type="submit"><?php esc_html_e( 'Apply', 'my-store' ); ?></button>
						</form>
					</section>
				<?php endif; ?>

				<?php foreach ( $groups as $group ) : ?>
					<section class="ms-shop__group">
						<h2 class="ms-shop__group-title"><?php echo esc_html( $group['label'] ); ?></h2>
						<ul class="ms-shop__chips">
							<?php
							foreach ( $group['terms'] as $term ) :
								$sel = $group['selected'];
								$pos = array_search( $term->slug, $sel, true );
								$on  = ( false !== $pos );

								if ( $on ) {
									unset( $sel[ $pos ] );
								} else {
									$sel[] = $term->slug;
								}

								$url = $sel
									? $link(
										array(
											'filter_' . $group['slug']     => implode( ',', $sel ),
											'query_type_' . $group['slug'] => 'or',
										)
									)
									: $link( array(), array( 'filter_' . $group['slug'], 'query_type_' . $group['slug'] ) );
								?>
								<li>
									<a class="ms-shop__chip<?php echo $on ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>" rel="nofollow"><?php echo esc_html( $term->name ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>
			</aside>

			<div class="ms-shop__results">

				<div class="ms-shop__toolbar">
					<button type="button" class="ms-shop__filter-btn" data-ms-filters-open aria-controls="ms-shop-filters">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/></svg>
						<span><?php echo esc_html( $o['filters_label'] ); ?></span>
					</button>

					<p class="ms-shop__result-count">
						<?php
						if ( $found ) {
							/* translators: 1: first, 2: last, 3: total */
							printf( esc_html__( 'Showing %1$d–%2$d of %3$d products', 'my-store' ), (int) $from, (int) $to, (int) $found );
						} else {
							esc_html_e( 'No products', 'my-store' );
						}
						?>
					</p>

					<form class="ms-shop__sort" method="get" action="<?php echo esc_url( '#' === $base ? '' : $base ); ?>">
						<?php $hidden( array( 'orderby' ) ); ?>
						<label for="ms-orderby"><?php esc_html_e( 'Sort by', 'my-store' ); ?></label>
						<select id="ms-orderby" name="orderby" data-ms-sort>
							<?php foreach ( $sorts as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $orderby, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<noscript><button type="submit"><?php esc_html_e( 'Sort', 'my-store' ); ?></button></noscript>
					</form>
				</div>

				<?php if ( $query->have_posts() ) : ?>
					<div class="ms-shop__grid">
						<?php
						foreach ( $query->posts as $i => $post ) {
							my_store_product_card( wc_get_product( $post ), array( 'index' => $i ) );
						}
						?>
					</div>

					<?php if ( ! $static && $query->max_num_pages > 1 ) : ?>
						<nav class="ms-shop__pagination" aria-label="<?php esc_attr_e( 'Pagination', 'my-store' ); ?>">
							<?php
							echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
								array(
									'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
									'format'    => '',
									'current'   => $paged,
									'total'     => (int) $query->max_num_pages,
									'prev_text' => '←',
									'next_text' => '→',
									'type'      => 'list',
									'end_size'  => 1,
									'mid_size'  => 1,
								)
							);
							?>
						</nav>
					<?php endif; ?>

				<?php else : ?>
					<div class="ms-shop__empty">
						<p><?php echo esc_html( $o['empty_text'] ); ?></p>
						<?php if ( $has_active ) : ?>
							<a class="ms-shop__clear" href="<?php echo esc_url( $link( array(), $clear_keys ) ); ?>"><?php esc_html_e( 'Clear all filters', 'my-store' ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
	<?php
}