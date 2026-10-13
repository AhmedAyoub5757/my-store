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

class My_Store_Promo_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-promo';
	}

	public function get_title() {
		return esc_html__( 'Promo Banner', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'promo', 'banner', 'split', 'editorial', 'image text' );
	}

	/**
	 * Color control that writes a CSS variable on .ms-promo.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-promo' => $var . ': {{VALUE}};',
			),
		);

		if ( $global ) {
			$args['global'] = array( 'default' => $global );
		} elseif ( $default ) {
			$args['default'] = $default;
		}

		$this->add_control( $id, $args );
	}

	/**
	 * Image helper.
	 */
	private function render_image( $media, $class ) {
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
					'loading' => 'lazy',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			printf( '<img class="%s" src="%s" alt="" loading="lazy">', esc_attr( $class ), esc_url( $media['url'] ) );
		}
	}

	protected function register_controls() {

		$placeholder = array( 'url' => Utils::get_placeholder_image_src() );

		/* ------------------------------------------------------------------
		 * CONTENT: Images
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_images',
			array(
				'label' => esc_html__( 'Images', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Image Side', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'my-store' ),
					'right' => esc_html__( 'Right', 'my-store' ),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Main Image (portrait, about 900×1100)', 'my-store' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => $placeholder,
			)
		);

		$this->add_control(
			'image_small',
			array(
				'label'       => esc_html__( 'Small Overlapping Image (optional)', 'my-store' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'A detail shot. Leave empty to hide.', 'my-store' ),
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'       => esc_html__( 'Badge Text (optional)', 'my-store' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'New Season', 'my-store' ),
				'description' => esc_html__( 'Short text works best, for example "-30%". Leave empty to hide.', 'my-store' ),
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Text
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_text',
			array(
				'label' => esc_html__( 'Text', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => esc_html__( 'Eyebrow', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Autumn Edit', 'my-store' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'my-store' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Layers made for cooler days', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'my-store' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Soft knits, structured outerwear and earthy tones, designed to be worn together and loved for years.', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$points = new Repeater();

		$points->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Feature', 'my-store' ),
			)
		);

		$this->add_control(
			'points',
			array(
				'label'       => esc_html__( 'Checklist (optional)', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $points->get_controls(),
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
				'default'     => array(
					array( 'text' => 'Premium natural fabrics' ),
					array( 'text' => 'Free delivery over a set amount' ),
					array( 'text' => 'Easy 14-day returns' ),
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'Button Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Shop the Edit', 'my-store' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'   => esc_html__( 'Button Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => home_url( '/shop/' ) ),
				'dynamic' => array( 'active' => true ),
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

		$this->add_var_color( 'section_bg', esc_html__( 'Background', 'my-store' ), '--ms-promo-bg', Global_Colors::COLOR_SECONDARY );
		$this->add_var_color( 'eyebrow_color', esc_html__( 'Eyebrow Color', 'my-store' ), '--ms-promo-eyebrow', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'title_color', esc_html__( 'Title Color', 'my-store' ), '--ms-promo-title', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'text_color', esc_html__( 'Text Color', 'my-store' ), '--ms-promo-text', Global_Colors::COLOR_TEXT );
		$this->add_var_color( 'check_color', esc_html__( 'Checklist Tick Color', 'my-store' ), '--ms-promo-tick', Global_Colors::COLOR_ACCENT );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eyebrow_typography',
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-promo__eyebrow',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-promo__title',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Description & Checklist', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-promo__desc, {{WRAPPER}} .ms-promo__point',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Button & badge
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_button',
			array(
				'label' => esc_html__( 'Button & Badge', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'btn_bg', esc_html__( 'Button Background', 'my-store' ), '--ms-promo-btn-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'btn_text', esc_html__( 'Button Text', 'my-store' ), '--ms-promo-btn-text', '', '#FFFFFF' );
		$this->add_var_color( 'btn_hover_bg', esc_html__( 'Button Hover Background', 'my-store' ), '--ms-promo-btn-hover-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'btn_hover_text', esc_html__( 'Button Hover Text', 'my-store' ), '--ms-promo-btn-hover-text', '', '#FFFFFF' );

		$this->add_var_color( 'badge_bg', esc_html__( 'Badge Background', 'my-store' ), '--ms-promo-badge-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'badge_text', esc_html__( 'Badge Text', 'my-store' ), '--ms-promo-badge-text', '', '#FFFFFF' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'btn_typography',
				'label'     => esc_html__( 'Button & Badge', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-promo__button, {{WRAPPER}} .ms-promo__badge',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$layout = ( 'right' === $s['layout'] ) ? 'right' : 'left';
		$points = ! empty( $s['points'] ) ? $s['points'] : array();

		$this->add_render_attribute( 'button', 'class', 'ms-promo__button' );
		if ( ! empty( $s['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $s['button_link'] );
		}
		?>
		<section class="ms-promo ms-promo--image-<?php echo esc_attr( $layout ); ?>">
			<div class="ms-container ms-promo__inner">

				<div class="ms-promo__visual">
					<div class="ms-promo__frame" data-ms-reveal>
						<?php $this->render_image( $s['image'], 'ms-promo__img' ); ?>
					</div>

					<?php if ( ! empty( $s['image_small']['url'] ) ) : ?>
						<div class="ms-promo__small" data-ms-reveal style="--i:3;">
							<?php $this->render_image( $s['image_small'], 'ms-promo__small-img' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $s['badge'] ) ) : ?>
						<div class="ms-promo__badge-wrap" data-ms-reveal style="--i:5;">
							<span class="ms-promo__badge"><?php echo esc_html( $s['badge'] ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<div class="ms-promo__text">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="ms-promo__eyebrow" data-ms-reveal><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $s['title'] ) ) : ?>
						<h2 class="ms-promo__title" data-ms-reveal style="--i:1;"><?php echo nl2br( esc_html( $s['title'] ) ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $s['description'] ) ) : ?>
						<p class="ms-promo__desc" data-ms-reveal style="--i:2;"><?php echo esc_html( $s['description'] ); ?></p>
					<?php endif; ?>

					<?php if ( $points ) : ?>
						<ul class="ms-promo__points" data-ms-reveal style="--i:3;">
							<?php foreach ( $points as $n => $point ) : ?>
								<?php if ( empty( $point['text'] ) ) { continue; } ?>
								<li class="ms-promo__point" style="--n:<?php echo (int) $n; ?>;"><?php echo esc_html( $point['text'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $s['button_text'] ) ) : ?>
						<div class="ms-promo__action" data-ms-reveal style="--i:4;">
							<a <?php $this->print_render_attribute_string( 'button' ); ?>><span><?php echo esc_html( $s['button_text'] ); ?></span></a>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</section>
		<?php
	}
}