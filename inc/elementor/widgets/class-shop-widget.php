<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Shop_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-shop';
	}

	public function get_title() {
		return esc_html__( 'Shop Archive', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-products-archive';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'shop', 'archive', 'catalog', 'filters', 'products' );
	}

	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-shop' => $var . ': {{VALUE}};',
			),
		);

		if ( $global ) {
			$args['global'] = array( 'default' => $global );
		} elseif ( $default ) {
			$args['default'] = $default;
		}

		$this->add_control( $id, $args );
	}

	protected function register_controls() {

		/* ------------------------------------------------------------------
		 * CONTENT: Header
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_head',
			array(
				'label' => esc_html__( 'Page Header', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => esc_html__( 'Eyebrow', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Collection', 'my-store' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'my-store' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to use the page title (or the category / search name).', 'my-store' ),
			)
		);

		$this->add_control(
			'desc',
			array(
				'label'   => esc_html__( 'Description', 'my-store' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Everyday essentials and heritage pieces, made to be worn and loved.', 'my-store' ),
			)
		);

		$this->add_control(
			'show_crumbs',
			array(
				'label'        => esc_html__( 'Show Breadcrumb', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Filters
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_filters',
			array(
				'label' => esc_html__( 'Filters', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_cats',
			array(
				'label'        => esc_html__( 'Category List', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'     => esc_html__( '"All" Link Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'All products', 'my-store' ),
				'condition' => array( 'show_cats' => 'yes' ),
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'        => esc_html__( 'Price Filter', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'attrs',
			array(
				'label'       => esc_html__( 'Attribute Filters', 'my-store' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'size, color',
				'description' => esc_html__( 'Attribute slugs from Products → Attributes, separated by commas. Order = display order. Empty = none.', 'my-store' ),
			)
		);

		$this->add_control(
			'filters_label',
			array(
				'label'     => esc_html__( 'Filters Button / Title', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Filters', 'my-store' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'   => esc_html__( 'Empty Message', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'No products match your filters.', 'my-store' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Header
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_head',
			array(
				'label' => esc_html__( 'Page Header', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'head_bg', esc_html__( 'Background', 'my-store' ), '--ms-shop-head-bg', Global_Colors::COLOR_SECONDARY );
		$this->add_var_color( 'eyebrow_color', esc_html__( 'Eyebrow Color', 'my-store' ), '--ms-shop-eyebrow', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'title_color', esc_html__( 'Title Color', 'my-store' ), '--ms-shop-title', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'desc_color', esc_html__( 'Text Color', 'my-store' ), '--ms-shop-text', Global_Colors::COLOR_TEXT );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eyebrow_typography',
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-shop__eyebrow',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-shop__title',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Filters & toolbar
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_filters',
			array(
				'label' => esc_html__( 'Filters & Toolbar', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'body_bg', esc_html__( 'Section Background (empty = page)', 'my-store' ), '--ms-shop-bg' );
		$this->add_var_color( 'group_color', esc_html__( 'Filter Titles', 'my-store' ), '--ms-shop-group', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'link_color', esc_html__( 'Links', 'my-store' ), '--ms-shop-link', Global_Colors::COLOR_TEXT );
		$this->add_var_color( 'link_active', esc_html__( 'Active / Hover Link', 'my-store' ), '--ms-shop-link-active', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'chip_active_bg', esc_html__( 'Active Chip Background', 'my-store' ), '--ms-shop-chip-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'chip_active_text', esc_html__( 'Active Chip Text', 'my-store' ), '--ms-shop-chip-text', '', '#FFFFFF' );
		$this->add_var_color( 'line_color', esc_html__( 'Lines / Borders', 'my-store' ), '--ms-shop-line', Global_Colors::COLOR_SECONDARY );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'group_typography',
				'label'     => esc_html__( 'Filter Titles', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-shop__group-title, {{WRAPPER}} .ms-shop__filters-title',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'Links & Toolbar', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-shop__cats a, {{WRAPPER}} .ms-shop__chip, {{WRAPPER}} .ms-shop__result-count, {{WRAPPER}} .ms-shop__sort',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Product cards (same variables as the product grid)
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_cards',
			array(
				'label' => esc_html__( 'Product Cards', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$card_vars = array(
			array( 'card_title', 'Name Color', '--ms-card-title', Global_Colors::COLOR_PRIMARY, '' ),
			array( 'card_title_hover', 'Name Hover Color', '--ms-card-title-hover', Global_Colors::COLOR_ACCENT, '' ),
			array( 'card_price', 'Price Color', '--ms-card-price', Global_Colors::COLOR_PRIMARY, '' ),
			array( 'badge_bg', 'Badge Background', '--ms-card-badge-bg', Global_Colors::COLOR_ACCENT, '' ),
			array( 'btn_bg', 'Button Background', '--ms-card-btn-bg', Global_Colors::COLOR_PRIMARY, '' ),
			array( 'btn_hover_bg', 'Button Hover Background', '--ms-card-btn-hover-bg', Global_Colors::COLOR_ACCENT, '' ),
		);

		foreach ( $card_vars as $v ) {
			$this->add_var_color( $v[0], esc_html( $v[1] ), $v[2], $v[3], $v[4] );
		}

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

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$s       = $this->get_settings_for_display();
		$editing = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$on_shop = is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' );
		$static  = $editing || ! $on_shop;

		if ( $static ) {
			// Editor or a page that isn't a catalog: show a sample of products.
			$query = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 6,
				)
			);
		} else {
			$query = isset( $GLOBALS['ms_shop_query'] ) ? $GLOBALS['ms_shop_query'] : $GLOBALS['wp_query'];
		}

		my_store_render_shop(
			$query,
			array(
				'eyebrow'       => $s['eyebrow'],
				'title'         => $s['title'],
				'desc'          => $s['desc'],
				'show_crumbs'   => 'yes' === $s['show_crumbs'],
				'show_cats'     => 'yes' === $s['show_cats'],
				'show_price'    => 'yes' === $s['show_price'],
				'attrs'         => array_filter( array_map( 'trim', explode( ',', (string) $s['attrs'] ) ) ),
				'all_label'     => $s['all_label'],
				'filters_label' => $s['filters_label'],
				'empty_text'    => $s['empty_text'],
			),
			$static
		);

		if ( $static ) {
			wp_reset_postdata();
		}
	}
}