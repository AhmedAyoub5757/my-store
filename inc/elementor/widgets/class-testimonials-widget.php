<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Testimonials_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-testimonials';
	}

	public function get_title() {
		return esc_html__( 'Testimonials', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'testimonials', 'reviews', 'quotes', 'customers' );
	}

	/**
	 * Color control that writes a CSS variable on .ms-testi.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-testi' => $var . ': {{VALUE}};',
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
				'default'   => esc_html__( 'Kind Words', 'my-store' ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'     => esc_html__( 'Title', 'my-store' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Loved by our customers', 'my-store' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_heading' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Reviews
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_reviews',
			array(
				'label' => esc_html__( 'Reviews', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$review = new Repeater();

		$review->add_control(
			'quote',
			array(
				'label'   => esc_html__( 'Quote', 'my-store' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Beautiful fabric and a perfect fit. I get compliments every time I wear it.', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$review->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Name', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Customer Name', 'my-store' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$review->add_control(
			'role',
			array(
				'label'   => esc_html__( 'Role / Location', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Verified buyer', 'my-store' ),
			)
		);

		$review->add_control(
			'rating',
			array(
				'label'   => esc_html__( 'Rating', 'my-store' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'5' => esc_html__( '5 stars', 'my-store' ),
					'4' => esc_html__( '4 stars', 'my-store' ),
					'3' => esc_html__( '3 stars', 'my-store' ),
					'2' => esc_html__( '2 stars', 'my-store' ),
					'1' => esc_html__( '1 star', 'my-store' ),
				),
			)
		);

		$review->add_control(
			'photo',
			array(
				'label'       => esc_html__( 'Photo (optional, square)', 'my-store' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'Leave empty to show the first letter of the name.', 'my-store' ),
			)
		);

		$defaults = array(
			array( 'The linen shirt is unbelievably soft and fits exactly like the size guide said. Already ordered a second colour.', 'Hamza R.', 'Lahore' ),
			array( 'Packaging was lovely and the dress arrived two days early. The fabric feels far more expensive than the price.', 'Ayesha K.', 'Islamabad' ),
			array( 'Bought matching outfits for my kids. They survived a full week of school and still look new.', 'Sana M.', 'Karachi' ),
			array( 'The kurta is beautifully stitched. The embroidery detail is just as delicate as in the photos.', 'Usman T.', 'Rawalpindi' ),
			array( 'Great denim, honest sizing and easy returns. This is my new go-to store.', 'Bilal A.', 'Faisalabad' ),
		);

		$default_reviews = array();
		foreach ( $defaults as $d ) {
			$default_reviews[] = array(
				'quote'  => $d[0],
				'name'   => $d[1],
				'role'   => $d[2],
				'rating' => '5',
			);
		}

		$this->add_control(
			'reviews',
			array(
				'label'       => esc_html__( 'Reviews', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $review->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => $default_reviews,
			)
		);

		$this->add_control(
			'show_stars',
			array(
				'label'        => esc_html__( 'Show Stars', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_nav',
			array(
				'label'        => esc_html__( 'Show Prev / Next Buttons', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$this->add_var_color( 'section_bg', esc_html__( 'Background (empty = page background)', 'my-store' ), '--ms-testi-bg' );
		$this->add_var_color( 'eyebrow_color', esc_html__( 'Eyebrow Color', 'my-store' ), '--ms-testi-eyebrow', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'heading_color', esc_html__( 'Title Color', 'my-store' ), '--ms-testi-heading', Global_Colors::COLOR_PRIMARY );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'eyebrow_typography',
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-testi__eyebrow',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Title', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-testi__heading',
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

		$this->add_var_color( 'card_bg', esc_html__( 'Card Background', 'my-store' ), '--ms-testi-card-bg', Global_Colors::COLOR_SECONDARY );
		$this->add_var_color( 'quote_mark', esc_html__( 'Quote Mark Color', 'my-store' ), '--ms-testi-mark', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'star_color', esc_html__( 'Star Color', 'my-store' ), '--ms-testi-star', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'quote_color', esc_html__( 'Quote Text Color', 'my-store' ), '--ms-testi-quote', Global_Colors::COLOR_TEXT );
		$this->add_var_color( 'name_color', esc_html__( 'Name Color', 'my-store' ), '--ms-testi-name', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'role_color', esc_html__( 'Role Color', 'my-store' ), '--ms-testi-role', '', '#6B6560' );
		$this->add_var_color( 'initial_bg', esc_html__( 'Initial Circle Background', 'my-store' ), '--ms-testi-initial-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'initial_text', esc_html__( 'Initial Circle Text', 'my-store' ), '--ms-testi-initial-text', '', '#FFFFFF' );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'quote_typography',
				'label'     => esc_html__( 'Quote', 'my-store' ),
				'selector'  => '{{WRAPPER}} .ms-testi__quote',
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'label'    => esc_html__( 'Name', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-testi__name',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'label'    => esc_html__( 'Role', 'my-store' ),
				'selector' => '{{WRAPPER}} .ms-testi__role',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Navigation
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_nav',
			array(
				'label' => esc_html__( 'Buttons & Progress', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'nav_color', esc_html__( 'Button Color', 'my-store' ), '--ms-testi-nav', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'nav_hover_bg', esc_html__( 'Button Hover Background', 'my-store' ), '--ms-testi-nav-hover-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'nav_hover_text', esc_html__( 'Button Hover Icon', 'my-store' ), '--ms-testi-nav-hover-text', '', '#FFFFFF' );
		$this->add_var_color( 'progress_color', esc_html__( 'Progress Line Color', 'my-store' ), '--ms-testi-progress', Global_Colors::COLOR_ACCENT );

		$this->end_controls_section();
	}

	/**
	 * Avatar or initial.
	 */
	private function render_avatar( $review ) {
		if ( ! empty( $review['photo']['url'] ) ) {
			if ( ! empty( $review['photo']['id'] ) ) {
				echo wp_get_attachment_image(
					$review['photo']['id'],
					'thumbnail',
					false,
					array(
						'class'   => 'ms-testi__avatar',
						'alt'     => $review['name'],
						'loading' => 'lazy',
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				printf( '<img class="ms-testi__avatar" src="%s" alt="%s" loading="lazy">', esc_url( $review['photo']['url'] ), esc_attr( $review['name'] ) );
			}
			return;
		}

		$initial = $review['name'] ? mb_strtoupper( mb_substr( $review['name'], 0, 1 ) ) : '?';
		echo '<span class="ms-testi__avatar ms-testi__avatar--initial" aria-hidden="true">' . esc_html( $initial ) . '</span>';
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$reviews = ! empty( $s['reviews'] ) ? $s['reviews'] : array();

		if ( empty( $reviews ) ) {
			return;
		}
		?>
		<section class="ms-testi">
			<div class="ms-container">

				<?php if ( 'yes' === $s['show_heading'] || 'yes' === $s['show_nav'] ) : ?>
					<div class="ms-testi__head" data-ms-reveal>
						<div>
							<?php if ( 'yes' === $s['show_heading'] && ! empty( $s['eyebrow'] ) ) : ?>
								<p class="ms-testi__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
							<?php endif; ?>
							<?php if ( 'yes' === $s['show_heading'] && ! empty( $s['heading'] ) ) : ?>
								<h2 class="ms-testi__heading"><?php echo nl2br( esc_html( $s['heading'] ) ); ?></h2>
							<?php endif; ?>
						</div>

						<?php if ( 'yes' === $s['show_nav'] ) : ?>
							<div class="ms-testi__nav">
								<button type="button" class="ms-testi__btn" data-ms-testi-prev aria-label="<?php esc_attr_e( 'Previous reviews', 'my-store' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
								</button>
								<button type="button" class="ms-testi__btn" data-ms-testi-next aria-label="<?php esc_attr_e( 'Next reviews', 'my-store' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
								</button>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="ms-testi__viewport">
					<div class="ms-testi__track" data-ms-testi-track tabindex="0" aria-label="<?php esc_attr_e( 'Customer reviews', 'my-store' ); ?>">
						<?php foreach ( $reviews as $i => $review ) : ?>
							<?php $rating = max( 1, min( 5, (int) $review['rating'] ) ); ?>
							<figure class="ms-testi__card" data-ms-reveal style="--i:<?php echo (int) min( $i, 4 ); ?>;">

								<?php if ( 'yes' === $s['show_stars'] ) : ?>
									<div class="ms-testi__stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating */ __( 'Rated %d out of 5', 'my-store' ), $rating ) ); ?>">
										<?php for ( $n = 1; $n <= 5; $n++ ) : ?>
											<span class="ms-testi__star<?php echo ( $n <= $rating ) ? ' is-on' : ''; ?>" style="--n:<?php echo (int) $n; ?>;"></span>
										<?php endfor; ?>
									</div>
								<?php endif; ?>

								<blockquote class="ms-testi__quote"><?php echo esc_html( $review['quote'] ); ?></blockquote>

								<figcaption class="ms-testi__author">
									<?php $this->render_avatar( $review ); ?>
									<span class="ms-testi__who">
										<span class="ms-testi__name"><?php echo esc_html( $review['name'] ); ?></span>
										<?php if ( ! empty( $review['role'] ) ) : ?>
											<span class="ms-testi__role"><?php echo esc_html( $review['role'] ); ?></span>
										<?php endif; ?>
									</span>
								</figcaption>

							</figure>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="ms-testi__progress" aria-hidden="true">
					<span class="ms-testi__progress-bar" data-ms-testi-bar></span>
				</div>

			</div>
		</section>
		<?php
	}
}