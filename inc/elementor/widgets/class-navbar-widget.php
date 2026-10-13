<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Navbar_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-navbar';
	}

	public function get_title() {
		return esc_html__( 'Navbar', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'navbar', 'menu', 'header', 'navigation' );
	}

	/**
	 * Helper: a color control that writes a CSS variable on .ms-navbar.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-navbar' => $var . ': {{VALUE}};',
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
		 * CONTENT: Logo
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'Logo', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => esc_html__( 'Logo Type', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'  => esc_html__( 'Text', 'my-store' ),
					'image' => esc_html__( 'Image', 'my-store' ),
				),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'     => esc_html__( 'Logo Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'MY STORE',
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => esc_html__( 'Logo Image', 'my-store' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'logo_type' => 'image' ),
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'   => esc_html__( 'Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/' ) ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Menu items
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_menu',
			array(
				'label' => esc_html__( 'Menu Items', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$menu = new Repeater();

		$menu->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Label', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Menu item', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$menu->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'menu_items',
			array(
				'label'       => esc_html__( 'Items', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $menu->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => 'Home',
						'link'  => array( 'url' => home_url( '/' ) ),
					),
					array(
						'title' => 'Shop',
						'link'  => array( 'url' => home_url( '/shop/' ) ),
					),
					array(
						'title' => 'Contact',
						'link'  => array( 'url' => home_url( '/contact/' ) ),
					),
				),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Icons
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_icons',
			array(
				'label' => esc_html__( 'Icons', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$icons = new Repeater();

		$icons->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Type', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'link',
				'options' => array(
					'link'   => esc_html__( 'Link', 'my-store' ),
					'search' => esc_html__( 'Search (opens search bar)', 'my-store' ),
					'cart'   => esc_html__( 'Cart (link + item count)', 'my-store' ),
				),
			)
		);

		$icons->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Accessible Label', 'my-store' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Link', 'my-store' ),
				'description' => esc_html__( 'Read by screen readers.', 'my-store' ),
			)
		);

		$icons->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'my-store' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-circle',
					'library' => 'fa-solid',
				),
			)
		);

		$icons->add_control(
			'link',
			array(
				'label'     => esc_html__( 'Link', 'my-store' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '#' ),
				'condition' => array( 'type!' => 'search' ),
			)
		);

		$this->add_control(
			'icons',
			array(
				'label'       => esc_html__( 'Icons', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $icons->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'type'  => 'search',
						'label' => 'Search',
						'icon'  => array(
							'value'   => 'fas fa-search',
							'library' => 'fa-solid',
						),
					),
					array(
						'type'  => 'link',
						'label' => 'Account',
						'icon'  => array(
							'value'   => 'far fa-user',
							'library' => 'fa-regular',
						),
						'link'  => array( 'url' => home_url( '/my-account/' ) ),
					),
					array(
						'type'  => 'link',
						'label' => 'Shop',
						'icon'  => array(
							'value'   => 'fas fa-store',
							'library' => 'fa-solid',
						),
						'link'  => array( 'url' => home_url( '/shop/' ) ),
					),
					array(
						'type'  => 'cart',
						'label' => 'Cart',
						'icon'  => array(
							'value'   => 'fas fa-shopping-bag',
							'library' => 'fa-solid',
						),
						'link'  => array( 'url' => home_url( '/cart/' ) ),
					),
				),
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => esc_html__( 'Search Placeholder', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Search products…', 'my-store' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Bar
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_bar',
			array(
				'label' => esc_html__( 'Bar', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'bar_bg', esc_html__( 'Background', 'my-store' ), '--ms-nav-bg', '', '#F6F2EC' );
		$this->add_var_color( 'bar_border', esc_html__( 'Bottom Border', 'my-store' ), '--ms-nav-border', Global_Colors::COLOR_SECONDARY );

		$this->add_responsive_control(
			'bar_height',
			array(
				'label'         => esc_html__( 'Height', 'my-store' ),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => array( 'px' ),
				'range'         => array( 'px' => array( 'min' => 48, 'max' => 160 ) ),
				'default'       => array( 'size' => 80, 'unit' => 'px' ),
				'mobile_default' => array( 'size' => 64, 'unit' => 'px' ),
				'selectors'     => array(
					'{{WRAPPER}} .ms-navbar' => '--ms-nav-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Logo
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_logo',
			array(
				'label' => esc_html__( 'Logo', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Image Width', 'my-store' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 300 ) ),
				'default'    => array( 'size' => 120, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-navbar' => '--ms-nav-logo-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'logo_type' => 'image' ),
			)
		);

		$this->add_control(
			'logo_color',
			array(
				'label'     => esc_html__( 'Text Color', 'my-store' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'selectors' => array(
					'{{WRAPPER}} .ms-navbar__logo-text' => 'color: {{VALUE}};',
				),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'logo_typography',
				'selector'  => '{{WRAPPER}} .ms-navbar__logo-text',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'condition' => array( 'logo_type' => 'text' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Menu
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_menu',
			array(
				'label' => esc_html__( 'Menu', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .ms-navbar__link',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->add_var_color( 'menu_color', esc_html__( 'Color', 'my-store' ), '--ms-nav-link', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'menu_hover_color', esc_html__( 'Hover / Active Color', 'my-store' ), '--ms-nav-link-hover', Global_Colors::COLOR_ACCENT );

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'Hover Effect', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => array(
					'none'      => esc_html__( 'None', 'my-store' ),
					'color'     => esc_html__( 'Color change', 'my-store' ),
					'underline' => esc_html__( 'Color + sliding underline', 'my-store' ),
					'fade'      => esc_html__( 'Fade other items', 'my-store' ),
				),
			)
		);

		$this->add_control(
			'menu_gap',
			array(
				'label'      => esc_html__( 'Space Between Items', 'my-store' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 100 ) ),
				'default'    => array( 'size' => 40, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-navbar' => '--ms-nav-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Icons
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_icons',
			array(
				'label' => esc_html__( 'Icons', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Size', 'my-store' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 40 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-navbar' => '--ms-nav-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_gap',
			array(
				'label'      => esc_html__( 'Space Between Icons', 'my-store' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 50 ) ),
				'default'    => array( 'size' => 22, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-navbar' => '--ms-nav-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_var_color( 'icon_color', esc_html__( 'Color', 'my-store' ), '--ms-nav-icon', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'icon_hover_color', esc_html__( 'Hover Color', 'my-store' ), '--ms-nav-icon-hover', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'badge_bg', esc_html__( 'Cart Badge Background', 'my-store' ), '--ms-nav-badge-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'badge_color', esc_html__( 'Cart Badge Text', 'my-store' ), '--ms-nav-badge-text', '', '#FFFFFF' );

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Mobile
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_mobile',
			array(
				'label' => esc_html__( 'Mobile & Search Panel', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'toggle_color', esc_html__( 'Hamburger Color', 'my-store' ), '--ms-nav-toggle', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'panel_bg', esc_html__( 'Panel Background (empty = same as bar)', 'my-store' ), '--ms-nav-panel-bg' );

		$this->end_controls_section();
	}

	protected function render() {
		global $wp;

		$s         = $this->get_settings_for_display();
		$id        = $this->get_id();
		$nav_id    = 'ms-nav-' . $id;
		$search_id = 'ms-search-' . $id;
		$effect    = ! empty( $s['hover_effect'] ) ? $s['hover_effect'] : 'underline';
		$current   = trailingslashit( home_url( empty( $wp->request ) ? '' : $wp->request ) );
		$icons     = ! empty( $s['icons'] ) ? $s['icons'] : array();
		$has_search = false;

		foreach ( $icons as $icon ) {
			if ( 'search' === $icon['type'] ) {
				$has_search = true;
			}
		}

		$this->add_link_attributes( 'logo_link', $s['logo_link'] );
		$this->add_render_attribute( 'logo_link', 'class', 'ms-navbar__logo' );
		?>
		<div class="ms-navbar ms-navbar--hover-<?php echo esc_attr( $effect ); ?>">
			<div class="ms-container ms-navbar__inner">

				<a <?php $this->print_render_attribute_string( 'logo_link' ); ?>>
					<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
						<?php if ( ! empty( $s['logo_image']['id'] ) ) : ?>
							<?php echo wp_get_attachment_image( $s['logo_image']['id'], 'full', false, array( 'class' => 'ms-navbar__logo-img' ) ); ?>
						<?php else : ?>
							<img class="ms-navbar__logo-img" src="<?php echo esc_url( $s['logo_image']['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
						<?php endif; ?>
					<?php else : ?>
						<span class="ms-navbar__logo-text"><?php echo esc_html( $s['logo_text'] ); ?></span>
					<?php endif; ?>
				</a>

				<nav class="ms-navbar__nav" id="<?php echo esc_attr( $nav_id ); ?>" aria-label="<?php esc_attr_e( 'Primary', 'my-store' ); ?>">
					<ul class="ms-navbar__menu">
						<?php foreach ( $s['menu_items'] as $index => $item ) : ?>
							<?php
							$key = 'menu_link_' . $index;
							$this->add_render_attribute( $key, 'class', 'ms-navbar__link' );

							if ( ! empty( $item['link']['url'] ) ) {
								$this->add_link_attributes( $key, $item['link'] );

								if ( trailingslashit( $item['link']['url'] ) === $current ) {
									$this->add_render_attribute( $key, 'class', 'is-active' );
								}
							}
							?>
							<li class="ms-navbar__item">
								<a <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $item['title'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<div class="ms-navbar__actions">
					<?php foreach ( $icons as $index => $icon ) : ?>
						<?php
						$key  = 'icon_' . $index;
						$type = $icon['type'];

						$this->add_render_attribute(
							$key,
							array(
								'class'      => array( 'ms-navbar__icon', 'ms-navbar__icon--' . $type ),
								'aria-label' => $icon['label'],
							)
						);

						if ( 'search' === $type ) {
							$this->add_render_attribute(
								$key,
								array(
									'type'                 => 'button',
									'data-ms-search-toggle' => '',
									'aria-expanded'        => 'false',
									'aria-controls'        => $search_id,
								)
							);
						} elseif ( ! empty( $icon['link']['url'] ) ) {
							$this->add_link_attributes( $key, $icon['link'] );
						}
						?>
						<?php if ( 'search' === $type ) : ?>
							<button <?php $this->print_render_attribute_string( $key ); ?>>
								<?php Icons_Manager::render_icon( $icon['icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</button>
						<?php else : ?>
							<a <?php $this->print_render_attribute_string( $key ); ?>>
								<?php Icons_Manager::render_icon( $icon['icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<?php
								if ( 'cart' === $type ) {
									echo my_store_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput
								}
								?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>

					<button type="button" class="ms-navbar__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $nav_id ); ?>">
						<span class="ms-navbar__burger"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'my-store' ); ?></span>
					</button>
				</div>
			</div>

			<?php if ( $has_search ) : ?>
				<div class="ms-navbar__search" id="<?php echo esc_attr( $search_id ); ?>" hidden>
					<div class="ms-container">
						<form class="ms-navbar__search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>-input"><?php esc_html_e( 'Search', 'my-store' ); ?></label>
							<input type="search" id="<?php echo esc_attr( $search_id ); ?>-input" name="s" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>">
							<input type="hidden" name="post_type" value="product">
							<button type="submit"><?php esc_html_e( 'Search', 'my-store' ); ?></button>
						</form>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}