<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Products_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-products';
	}

	public function get_title() {
		return esc_html__( 'Product Grid', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'products', 'featured', 'shop', 'woocommerce', 'grid' );
	}

	/**
	 * Color control that writes a CSS variable on .ms-products.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-products' => $var . ': {{VALUE}};',
			),
		);

		if ( $global ) {
			$args['global'] = array( 'default' => $global );
		} elseif ( $default ) {
			$args['default'] = $default;
		}

		$this->add_control( $id, $args );
	}

	private function get_category_options() {
		$options = array();
		$terms   = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	private function get_product_options() {
		$options = array();

		if ( ! post_type_exists( 'product' ) ) {
			return $options;
		}

		$ids = get_posts(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'numberposts' => 200,
				'fields'      => 'ids',
			)
		);

		foreach ( $ids as $id ) {
			$options[ $id ] = get_the_title( $id ) . ' (#' . $id . ')';
		}

		return $options;
	}

	protected function register_controls() {

		/* ------------------------------------------------------------------
		 * CONTENT: Heading
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_heading',
			array(
				'label' => esc_html__( 'Heading', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_heading',
			array(
				'label'        => esc_html__( 'Show Heading', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Handpicked', 'my-store' ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'     => esc_html__( 'Title', 'my-store' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Featured Products', 'my-store' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'head_link_text',
			array(
				'label'     => esc_html__( 'Link Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'View all', 'my-store' ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'head_link',
			array(
				'label'     => esc_html__( 'Link', 'my-store' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => home_url( '/shop/' ) ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Products
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_products',
			array(
				'label' => esc_html__( 'Products', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Show', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => array(
					'latest'   => esc_html__( 'Latest products', 'my-store' ),
					'featured' => esc_html__( 'Featured products (starred)', 'my-store' ),
					'sale'     => esc_html__( 'On sale', 'my-store' ),
					'best'     => esc_html__( 'Best sellers', 'my-store' ),
					'category' => esc_html__( 'From categories', 'my-store' ),
					'manual'   => esc_html__( 'Hand-picked products', 'my-store' ),
				),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'       => esc_html__( 'Categories', 'my-store' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_category_options(),
				'label_block' => true,
				'condition'   => array( 'source' => 'category' ),
			)
		);

		$this->add_control(
			'products',
			array(
				'label'       => esc_html__( 'Products', 'my-store' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_product_options(),
				'label_block' => true,
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'Number of Products', 'my-store' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 24,
			)
		);

		$this->add_control(
			'show_category',
			array(
				'label'        => esc_html__( 'Show Category Label', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_add',
			array(
				'label'        => esc_html__( 'Show Quick Add Button', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'add_text',
			array(
				'label'     => esc_html__( 'Button Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Add to cart', 'my-store' ),
				'condition' => array( 'show_add' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Section
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Section', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'section_bg', esc_html__( 'Background (empty = page background)', 'my-store' ), '--ms-products-bg' );
		$this->add_var_color( 'eyebrow_color', esc_html__( 'Eyebrow Color', 'my-store' ), '--ms-products-eyebrow', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'heading_color', esc_html__( 'Title Color', 'my-store' ), '--ms-products-heading', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'link_color', esc_html__( 'Link Color', 'my-store' ), '--ms-products-link', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'link_hover_color', esc_html__( 'Link Hover Color', 'my-store' ), '--ms-products-link-hover', Global_Colors::COLOR_ACCENT );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eyebrow_typography',
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-products__eyebrow',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Title', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-products__heading',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Product cards
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_cards',
			array(
				'label' => esc_html__( 'Product Cards', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'card_title', esc_html__( 'Name Color', 'my-store' ), '--ms-card-title', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'card_title_hover', esc_html__( 'Name Hover Color', 'my-store' ), '--ms-card-title-hover', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'card_cat', esc_html__( 'Category / Old Price Color', 'my-store' ), '--ms-card-muted', '', '#6B6560' );
		$this->add_var_color( 'card_price', esc_html__( 'Price Color', 'my-store' ), '--ms-card-price', Global_Colors::COLOR_PRIMARY );

		$this->add_var_color( 'badge_bg', esc_html__( 'Badge Background', 'my-store' ), '--ms-card-badge-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'badge_text', esc_html__( 'Badge Text', 'my-store' ), '--ms-card-badge-text', '', '#FFFFFF' );

		$this->add_var_color( 'btn_bg', esc_html__( 'Button Background', 'my-store' ), '--ms-card-btn-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'btn_text', esc_html__( 'Button Text', 'my-store' ), '--ms-card-btn-text', '', '#FFFFFF' );
		$this->add_var_color( 'btn_hover_bg', esc_html__( 'Button Hover Background', 'my-store' ), '--ms-card-btn-hover-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'btn_hover_text', esc_html__( 'Button Hover Text', 'my-store' ), '--ms-card-btn-hover-text', '', '#FFFFFF' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_name_typography',
				'label'     => esc_html__( 'Name', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-card__title',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_cat_typography',
				'label'    => esc_html__( 'Category', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-card__cat',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_price_typography',
				'label'    => esc_html__( 'Price', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-card__price',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_btn_typography',
				'label'    => esc_html__( 'Button & Badge', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-card__add, {{WRAPPER}} .ms-card__badge',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build the product query from the settings.
	 */
	private function get_query( $s ) {
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, min( 24, absint( $s['count'] ) ) ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				),
			),
		);

		switch ( $s['source'] ) {
			case 'featured':
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'featured' ),
				);
				break;

			case 'sale':
				$ids              = wc_get_product_ids_on_sale();
				$args['post__in'] = $ids ? $ids : array( 0 );
				break;

			case 'best':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['orderby']  = 'meta_value_num';
				break;

			case 'category':
				if ( ! empty( $s['categories'] ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => (array) $s['categories'],
					);
				}
				break;

			case 'manual':
				$ids              = array_filter( array_map( 'absint', (array) $s['products'] ) );
				$args['post__in'] = $ids ? $ids : array( 0 );
				$args['orderby']  = 'post__in';
				break;
		}

		return new WP_Query( $args );
	}

	private function notice( $message ) {
		echo '<div class="ms-products__notice">' . esc_html( $message ) . '</div>';
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$editing = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( $editing ) {
				$this->notice( __( 'WooCommerce is not active.', 'my-store' ) );
			}
			return;
		}

		// WooCommerce only loads these on its own pages, so we request them here.
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );

		$query = $this->get_query( $s );

		if ( ! $query->have_posts() ) {
			if ( $editing ) {
				$this->notice( __( 'No products found. Add products in WooCommerce → Products, or change the "Show" option.', 'my-store' ) );
			}
			return;
		}

		if ( 'yes' === $s['show_heading'] ) {
			$this->add_render_attribute( 'head_link', 'class', 'ms-products__more' );
			if ( ! empty( $s['head_link']['url'] ) ) {
				$this->add_link_attributes( 'head_link', $s['head_link'] );
			}
		}
		?>
		<section class="ms-products">
			<div class="ms-container">

				<?php if ( 'yes' === $s['show_heading'] ) : ?>
					<div class="ms-products__head">
						<div>
							<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
								<p class="ms-products__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $s['heading'] ) ) : ?>
								<h2 class="ms-products__heading"><?php echo nl2br( esc_html( $s['heading'] ) ); ?></h2>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $s['head_link_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'head_link' ); ?>><?php echo esc_html( $s['head_link_text'] ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="ms-products__grid">
					<?php
					foreach ( $query->posts as $i => $post ) {
						my_store_product_card(
							wc_get_product( $post ),
							array(
								'show_category' => 'yes' === $s['show_category'],
								'show_add'      => 'yes' === $s['show_add'],
								'add_text'      => $s['add_text'],
								'index'         => $i,
							)
						);
					}
					?>
				</div>

			</div>
		</section>
		<?php
	}
}