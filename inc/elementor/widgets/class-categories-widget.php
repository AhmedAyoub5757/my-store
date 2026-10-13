<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Categories_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-categories';
	}

	public function get_title() {
		return esc_html__( 'Category Cards', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'category', 'categories', 'cards', 'collections' );
	}

	/**
	 * Color control that writes a CSS variable on .ms-cats.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-cats' => $var . ': {{VALUE}};',
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

		$placeholder = array( 'url' => Utils::get_placeholder_image_src() );

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
				'default'   => esc_html__( 'Collections', 'my-store' ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'     => esc_html__( 'Title', 'my-store' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Shop by Category', 'my-store' ),
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
		 * CONTENT: Cards
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_cards',
			array(
				'label' => esc_html__( 'Cards', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$card = new Repeater();

		$card->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'my-store' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => $placeholder,
			)
		);

		$card->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Men', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$card->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Everyday essentials', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$card->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/shop/' ) ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$defaults = array(
			array( 'Men', 'Everyday essentials' ),
			array( 'Women', 'Soft, flowing silhouettes' ),
			array( 'Kids', 'Playful and durable' ),
			array( 'Western', 'Denim, leather, frontier' ),
			array( 'Traditional', 'Crafted for celebrations' ),
		);

		$default_cards = array();
		foreach ( $defaults as $d ) {
			$default_cards[] = array(
				'image'    => $placeholder,
				'title'    => $d[0],
				'subtitle' => $d[1],
				'link'     => array( 'url' => home_url( '/shop/' ) ),
			);
		}

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $default_cards,
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => esc_html__( 'Card Button Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Explore', 'my-store' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_numbers',
			array(
				'label'        => esc_html__( 'Show Numbers (01, 02…)', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'Hover Effect (desktop)', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'expand',
				'options' => array(
					'expand' => esc_html__( 'Expand card + zoom', 'my-store' ),
					'zoom'   => esc_html__( 'Zoom only', 'my-store' ),
				),
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

		$this->add_var_color( 'section_bg', esc_html__( 'Background (empty = page background)', 'my-store' ), '--ms-cats-bg' );
		$this->add_var_color( 'eyebrow_color', esc_html__( 'Eyebrow Color', 'my-store' ), '--ms-cats-eyebrow', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'heading_color', esc_html__( 'Title Color', 'my-store' ), '--ms-cats-heading', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'link_color', esc_html__( 'Link Color', 'my-store' ), '--ms-cats-link', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'link_hover_color', esc_html__( 'Link Hover Color', 'my-store' ), '--ms-cats-link-hover', Global_Colors::COLOR_ACCENT );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eyebrow_typography',
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-cats__eyebrow',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Title', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-cats__heading',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Cards
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_cards',
			array(
				'label' => esc_html__( 'Cards', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'overlay_color', esc_html__( 'Overlay Color', 'my-store' ), '--ms-cats-overlay', '', '#1A1A1A' );

		$this->add_control(
			'overlay_opacity',
			array(
				'label'      => esc_html__( 'Overlay Strength', 'my-store' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0.65 ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-cats' => '--ms-cats-overlay-opacity: {{SIZE}};',
				),
			)
		);

		$this->add_var_color( 'card_text', esc_html__( 'Text Color', 'my-store' ), '--ms-cats-text', '', '#FFFFFF' );
		$this->add_var_color( 'card_accent', esc_html__( 'Number / Underline Color', 'my-store' ), '--ms-cats-accent', Global_Colors::COLOR_ACCENT );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_title_typography',
				'label'     => esc_html__( 'Card Title', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-cats__title',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_sub_typography',
				'label'    => esc_html__( 'Card Subtitle', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-cats__subtitle',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_cta_typography',
				'label'    => esc_html__( 'Card Button', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-cats__cta',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Image helper.
	 */
	private function render_image( $media, $class, $eager = false ) {
		if ( empty( $media['url'] ) ) {
			return;
		}

		if ( ! empty( $media['id'] ) ) {
			echo wp_get_attachment_image(
				$media['id'],
				'large',
				false,
				array(
					'class'   => $class,
					'loading' => $eager ? 'eager' : 'lazy',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			printf( '<img class="%s" src="%s" alt="" loading="lazy">', esc_attr( $class ), esc_url( $media['url'] ) );
		}
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$cards = ! empty( $s['cards'] ) ? $s['cards'] : array();

		if ( empty( $cards ) ) {
			return;
		}

		$effect = ! empty( $s['hover_effect'] ) ? $s['hover_effect'] : 'expand';

		if ( 'yes' === $s['show_heading'] ) {
			$this->add_render_attribute( 'head_link', 'class', 'ms-cats__more' );
			if ( ! empty( $s['head_link']['url'] ) ) {
				$this->add_link_attributes( 'head_link', $s['head_link'] );
			}
		}
		?>
		<section class="ms-cats ms-cats--<?php echo esc_attr( $effect ); ?>">
			<div class="ms-container">

				<?php if ( 'yes' === $s['show_heading'] ) : ?>
					<div class="ms-cats__head">
						<div class="ms-cats__head-text">
							<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
								<p class="ms-cats__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $s['heading'] ) ) : ?>
								<h2 class="ms-cats__heading"><?php echo nl2br( esc_html( $s['heading'] ) ); ?></h2>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $s['head_link_text'] ) ) : ?>
							<a <?php $this->print_render_attribute_string( 'head_link' ); ?>><?php echo esc_html( $s['head_link_text'] ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="ms-cats__list">
					<?php foreach ( $cards as $i => $card ) : ?>
						<?php
						$key = 'card_' . $i;
						$this->add_render_attribute( $key, 'class', 'ms-cats__item' );
						$this->add_render_attribute( $key, 'style', '--i:' . (int) $i . ';' );
						if ( ! empty( $card['link']['url'] ) ) {
							$this->add_link_attributes( $key, $card['link'] );
						}
						?>
						<a <?php $this->print_render_attribute_string( $key ); ?>>
							<div class="ms-cats__media">
								<?php $this->render_image( $card['image'], 'ms-cats__img' ); ?>
							</div>

							<div class="ms-cats__body">
								<?php if ( 'yes' === $s['show_numbers'] ) : ?>
									<span class="ms-cats__num"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
								<?php endif; ?>

								<h3 class="ms-cats__title"><?php echo esc_html( $card['title'] ); ?></h3>

								<div class="ms-cats__reveal">
									<div class="ms-cats__reveal-inner">
										<?php if ( ! empty( $card['subtitle'] ) ) : ?>
											<p class="ms-cats__subtitle"><?php echo esc_html( $card['subtitle'] ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $s['cta_text'] ) ) : ?>
											<span class="ms-cats__cta"><?php echo esc_html( $s['cta_text'] ); ?></span>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

			</div>
		</section>
		<?php
	}
}