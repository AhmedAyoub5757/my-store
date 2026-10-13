<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Footer_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'ms-footer';
	}

	public function get_title() {
		return esc_html__( 'Newsletter + Footer', 'my-store' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return array( 'my-store' );
	}

	public function get_keywords() {
		return array( 'footer', 'newsletter', 'subscribe', 'links', 'social' );
	}

	/**
	 * Color control that writes a CSS variable on .ms-foot.
	 */
	private function add_var_color( $id, $label, $var, $global = '', $default = '' ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .ms-foot' => $var . ': {{VALUE}};',
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
	 * Lists from MailPoet (empty if it isn't active).
	 */
	private function get_list_options() {
		$options = array();

		if ( class_exists( \MailPoet\API\API::class ) ) {
			try {
				foreach ( \MailPoet\API\API::MP( 'v1' )->getLists() as $list ) {
					$options[ $list['id'] ] = $list['name'];
				}
			} catch ( \Exception $e ) {
				// Ignore: the select just stays empty.
			}
		}

		return $options;
	}

	protected function register_controls() {

		/* ------------------------------------------------------------------
		 * CONTENT: Newsletter
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_newsletter',
			array(
				'label' => esc_html__( 'Newsletter', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_newsletter',
			array(
				'label'        => esc_html__( 'Show Newsletter', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		if ( ! class_exists( \MailPoet\API\API::class ) ) {
			$this->add_control(
				'mailpoet_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'MailPoet is not active. Install and activate it so signups are saved.', 'my-store' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array( 'show_newsletter' => 'yes' ),
				)
			);
		}

		$this->add_control(
			'nl_list',
			array(
				'label'       => esc_html__( 'MailPoet List', 'my-store' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array( '' => esc_html__( 'First list (default)', 'my-store' ) ) + $this->get_list_options(),
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_eyebrow',
			array(
				'label'     => esc_html__( 'Eyebrow', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Stay in the loop', 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_title',
			array(
				'label'     => esc_html__( 'Title', 'my-store' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Join the list, get 10% off your first order', 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_desc',
			array(
				'label'     => esc_html__( 'Description', 'my-store' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'New arrivals, restocks and private sales, straight to your inbox.', 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_placeholder',
			array(
				'label'     => esc_html__( 'Email Placeholder', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Your email address', 'my-store' ),
				'separator' => 'before',
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_button',
			array(
				'label'     => esc_html__( 'Button Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Subscribe', 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_success',
			array(
				'label'     => esc_html__( 'Success Message', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( "Thank you! You're on the list.", 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->add_control(
			'nl_note',
			array(
				'label'     => esc_html__( 'Small Print', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'No spam. Unsubscribe anytime.', 'my-store' ),
				'condition' => array( 'show_newsletter' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Brand
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_brand',
			array(
				'label' => esc_html__( 'Brand & Social', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'       => esc_html__( 'Logo Image (optional)', 'my-store' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'Leave empty to show the brand name as text.', 'my-store' ),
			)
		);

		$this->add_control(
			'brand_name',
			array(
				'label'   => esc_html__( 'Brand Name', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'MY STORE',
			)
		);

		$this->add_control(
			'brand_desc',
			array(
				'label'   => esc_html__( 'Description', 'my-store' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Thoughtfully made clothing for the whole family, from everyday essentials to heritage pieces.', 'my-store' ),
			)
		);

		$social = new Repeater();

		$social->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Accessible Label', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Instagram',
			)
		);

		$social->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'my-store' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fab fa-instagram',
					'library' => 'fa-brands',
				),
			)
		);

		$social->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'socials',
			array(
				'label'       => esc_html__( 'Social Icons', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $social->get_controls(),
				'title_field' => '{{{ label }}}',
				'separator'   => 'before',
				'default'     => array(
					array(
						'label' => 'Instagram',
						'icon'  => array( 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => 'Facebook',
						'icon'  => array( 'value' => 'fab fa-facebook-f', 'library' => 'fa-brands' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => 'Pinterest',
						'icon'  => array( 'value' => 'fab fa-pinterest-p', 'library' => 'fa-brands' ),
						'link'  => array( 'url' => '#' ),
					),
				),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * CONTENT: Link columns (3 fixed columns, any number of links each)
		 * ---------------------------------------------------------------- */
		$col_defaults = array(
			1 => array(
				'Shop',
				array(
					array( 'Men', '/product-category/men/' ),
					array( 'Women', '/product-category/women/' ),
					array( 'Kids', '/product-category/kids/' ),
					array( 'Western', '/product-category/western/' ),
					array( 'Traditional', '/product-category/traditional/' ),
				),
			),
			2 => array(
				'Help',
				array(
					array( 'My Account', '/my-account/' ),
					array( 'Cart', '/cart/' ),
					array( 'Contact', '/contact/' ),
					array( 'Shipping & Returns', '/shipping-returns/' ),
				),
			),
			3 => array(
				'Get in Touch',
				array(
					array( 'hello@example.com', 'mailto:hello@example.com' ),
					array( '+92 300 0000000', 'tel:+923000000000' ),
					array( 'Rawalpindi, Pakistan', '' ),
				),
			),
		);

		foreach ( $col_defaults as $c => $def ) {
			$this->start_controls_section(
				'section_col_' . $c,
				array(
					/* translators: %d: column number */
					'label' => sprintf( esc_html__( 'Link Column %d', 'my-store' ), $c ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'col_' . $c . '_title',
				array(
					'label'       => esc_html__( 'Column Title', 'my-store' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => $def[0],
					'description' => esc_html__( 'Leave the title and links empty to hide this column.', 'my-store' ),
				)
			);

			$links = new Repeater();

			$links->add_control(
				'label',
				array(
					'label'   => esc_html__( 'Label', 'my-store' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'Link', 'my-store' ),
				)
			);

			$links->add_control(
				'link',
				array(
					'label'       => esc_html__( 'Link (empty = plain text)', 'my-store' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'https://',
				)
			);

			$default_links = array();
			foreach ( $def[1] as $l ) {
				$default_links[] = array(
					'label' => $l[0],
					'link'  => array( 'url' => $l[1] ? ( 0 === strpos( $l[1], '/' ) ? home_url( $l[1] ) : $l[1] ) : '' ),
				);
			}

			$this->add_control(
				'col_' . $c . '_links',
				array(
					'label'       => esc_html__( 'Links', 'my-store' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $links->get_controls(),
					'title_field' => '{{{ label }}}',
					'default'     => $default_links,
				)
			);

			$this->end_controls_section();
		}

		/* ------------------------------------------------------------------
		 * CONTENT: Bottom bar
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_bottom',
			array(
				'label' => esc_html__( 'Bottom Bar', 'my-store' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'copyright',
			array(
				'label'       => esc_html__( 'Copyright', 'my-store' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '© {year} My Store. All rights reserved.',
				'description' => esc_html__( '{year} becomes the current year automatically.', 'my-store' ),
			)
		);

		$legal = new Repeater();

		$legal->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'my-store' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Privacy Policy', 'my-store' ),
			)
		);

		$legal->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'my-store' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'legal',
			array(
				'label'       => esc_html__( 'Legal Links', 'my-store' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $legal->get_controls(),
				'title_field' => '{{{ label }}}',
				'separator'   => 'before',
				'default'     => array(
					array( 'label' => 'Privacy Policy', 'link' => array( 'url' => home_url( '/privacy-policy/' ) ) ),
					array( 'label' => 'Terms & Conditions', 'link' => array( 'url' => home_url( '/terms-and-conditions/' ) ) ),
					array( 'label' => 'Refund Policy', 'link' => array( 'url' => home_url( '/refund-policy/' ) ) ),
				),
			)
		);

		$this->add_control(
			'show_wordmark',
			array(
				'label'        => esc_html__( 'Show Giant Wordmark', 'my-store' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'wordmark',
			array(
				'label'     => esc_html__( 'Wordmark Text', 'my-store' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'MY STORE',
				'condition' => array( 'show_wordmark' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Colors
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_colors',
			array(
				'label' => esc_html__( 'Colors', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_var_color( 'bg', esc_html__( 'Background', 'my-store' ), '--ms-foot-bg', Global_Colors::COLOR_PRIMARY );
		$this->add_var_color( 'text', esc_html__( 'Text (muted tones are derived from it)', 'my-store' ), '--ms-foot-text', '', '#F6F2EC' );
		$this->add_var_color( 'accent', esc_html__( 'Accent (eyebrow, focus line)', 'my-store' ), '--ms-foot-accent', Global_Colors::COLOR_ACCENT );

		$this->add_var_color( 'btn_bg', esc_html__( 'Button Background', 'my-store' ), '--ms-foot-btn-bg', '', '#F6F2EC' );
		$this->add_var_color( 'btn_text', esc_html__( 'Button Text', 'my-store' ), '--ms-foot-btn-text', '', '#1A1A1A' );
		$this->add_var_color( 'btn_hover_bg', esc_html__( 'Button Hover Background', 'my-store' ), '--ms-foot-btn-hover-bg', Global_Colors::COLOR_ACCENT );
		$this->add_var_color( 'btn_hover_text', esc_html__( 'Button Hover Text', 'my-store' ), '--ms-foot-btn-hover-text', '', '#FFFFFF' );

		$this->end_controls_section();

		/* ------------------------------------------------------------------
		 * STYLE: Typography
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'style_typography',
			array(
				'label' => esc_html__( 'Typography', 'my-store' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$typo = array(
			array( 'nl_eyebrow_typo', 'Newsletter Eyebrow', '.ms-foot__eyebrow', Global_Typography::TYPOGRAPHY_ACCENT ),
			array( 'nl_title_typo', 'Newsletter Title', '.ms-foot__nl-title', Global_Typography::TYPOGRAPHY_PRIMARY ),
			array( 'body_typo', 'Descriptions & Input', '.ms-foot__nl-desc, .ms-foot__desc, .ms-foot__input, .ms-foot__note, .ms-foot__msg', Global_Typography::TYPOGRAPHY_TEXT ),
			array( 'col_title_typo', 'Column Titles', '.ms-foot__col-title', Global_Typography::TYPOGRAPHY_ACCENT ),
			array( 'link_typo', 'Links', '.ms-foot__link, .ms-foot__text', Global_Typography::TYPOGRAPHY_TEXT ),
			array( 'bottom_typo', 'Bottom Bar', '.ms-foot__copy, .ms-foot__legal a', Global_Typography::TYPOGRAPHY_TEXT ),
			array( 'button_typo', 'Button', '.ms-foot__submit', Global_Typography::TYPOGRAPHY_ACCENT ),
		);

		foreach ( $typo as $t ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $t[0],
					'label'    => esc_html( $t[1] ),
					'selector' => implode(
						', ',
						array_map(
							function ( $sel ) {
								return '{{WRAPPER}} ' . trim( $sel );
							},
							explode( ',', $t[2] )
						)
					),
					'global'   => array( 'default' => $t[3] ),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Render one link (anchor if it has a URL, plain text otherwise).
	 */
	private function render_link( $key, $item ) {
		if ( ! empty( $item['link']['url'] ) ) {
			$this->add_render_attribute( $key, 'class', 'ms-foot__link' );
			$this->add_link_attributes( $key, $item['link'] );
			echo '<a ' . $this->get_render_attribute_string( $key ) . '>' . esc_html( $item['label'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<span class="ms-foot__text">' . esc_html( $item['label'] ) . '</span>';
		}
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$copy = str_replace( '{year}', gmdate( 'Y' ), (string) $s['copyright'] );
		?>
		<footer class="ms-foot">

			<?php if ( 'yes' === $s['show_newsletter'] ) : ?>
				<div class="ms-foot__newsletter">
					<div class="ms-container ms-foot__nl-inner">

						<div class="ms-foot__nl-text" data-ms-reveal>
							<?php if ( ! empty( $s['nl_eyebrow'] ) ) : ?>
								<p class="ms-foot__eyebrow"><?php echo esc_html( $s['nl_eyebrow'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $s['nl_title'] ) ) : ?>
								<h2 class="ms-foot__nl-title"><?php echo nl2br( esc_html( $s['nl_title'] ) ); ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( $s['nl_desc'] ) ) : ?>
								<p class="ms-foot__nl-desc"><?php echo esc_html( $s['nl_desc'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="ms-foot__nl-form" data-ms-reveal style="--i:2;">
							<form
								class="ms-foot__form"
								data-ms-subscribe
								novalidate
								data-success="<?php echo esc_attr( $s['nl_success'] ); ?>"
								data-error="<?php esc_attr_e( 'Something went wrong. Please try again.', 'my-store' ); ?>"
								data-invalid="<?php esc_attr_e( 'Please enter a valid email address.', 'my-store' ); ?>"
							>
								<div class="ms-foot__row">
									<div class="ms-foot__field">
										<label class="screen-reader-text" for="<?php echo esc_attr( 'ms-foot-email-' . $this->get_id() ); ?>"><?php esc_html_e( 'Email address', 'my-store' ); ?></label>
										<input
											class="ms-foot__input"
											type="email"
											name="email"
											id="<?php echo esc_attr( 'ms-foot-email-' . $this->get_id() ); ?>"
											placeholder="<?php echo esc_attr( $s['nl_placeholder'] ); ?>"
											autocomplete="email"
											required
										>
										<span class="ms-foot__field-line" aria-hidden="true"></span>
									</div>

									<input class="ms-foot__hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
									<input type="hidden" name="list" value="<?php echo esc_attr( $s['nl_list'] ); ?>">

									<button type="submit" class="ms-foot__submit"><span><?php echo esc_html( $s['nl_button'] ); ?></span></button>
								</div>

								<p class="ms-foot__msg" role="status" aria-live="polite"></p>
							</form>

							<?php if ( ! empty( $s['nl_note'] ) ) : ?>
								<p class="ms-foot__note"><?php echo esc_html( $s['nl_note'] ); ?></p>
							<?php endif; ?>
						</div>

					</div>
				</div>
			<?php endif; ?>

			<div class="ms-foot__main">
				<div class="ms-container ms-foot__grid">

					<div class="ms-foot__brand" data-ms-reveal>
						<?php if ( ! empty( $s['logo_image']['url'] ) ) : ?>
							<?php if ( ! empty( $s['logo_image']['id'] ) ) : ?>
								<?php echo wp_get_attachment_image( $s['logo_image']['id'], 'medium', false, array( 'class' => 'ms-foot__logo-img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php else : ?>
								<img class="ms-foot__logo-img" src="<?php echo esc_url( $s['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( $s['brand_name'] ); ?>" loading="lazy">
							<?php endif; ?>
						<?php elseif ( ! empty( $s['brand_name'] ) ) : ?>
							<p class="ms-foot__logo-text"><?php echo esc_html( $s['brand_name'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $s['brand_desc'] ) ) : ?>
							<p class="ms-foot__desc"><?php echo esc_html( $s['brand_desc'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $s['socials'] ) ) : ?>
							<ul class="ms-foot__social">
								<?php foreach ( $s['socials'] as $n => $item ) : ?>
									<?php
									$key = 'social_' . $n;
									$this->add_render_attribute( $key, 'class', 'ms-foot__social-link' );
									$this->add_render_attribute( $key, 'aria-label', $item['label'] );
									if ( ! empty( $item['link']['url'] ) ) {
										$this->add_link_attributes( $key, $item['link'] );
									}
									?>
									<li>
										<a <?php $this->print_render_attribute_string( $key ); ?>>
											<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>

					<?php
					$shown = 0;
					for ( $c = 1; $c <= 3; $c++ ) :
						$title = $s[ 'col_' . $c . '_title' ];
						$links = $s[ 'col_' . $c . '_links' ];

						if ( empty( $title ) && empty( $links ) ) {
							continue;
						}
						$shown++;
						?>
						<nav class="ms-foot__col" data-ms-reveal style="--i:<?php echo (int) $shown; ?>;" aria-label="<?php echo esc_attr( $title ); ?>">
							<?php if ( ! empty( $title ) ) : ?>
								<h3 class="ms-foot__col-title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $links ) ) : ?>
								<ul class="ms-foot__links">
									<?php foreach ( $links as $n => $item ) : ?>
										<li><?php $this->render_link( 'col' . $c . '_link_' . $n, $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</nav>
					<?php endfor; ?>

				</div>
			</div>

			<div class="ms-foot__bottom">
				<div class="ms-container ms-foot__bottom-inner" data-ms-reveal>
					<p class="ms-foot__copy"><?php echo esc_html( $copy ); ?></p>

					<?php if ( ! empty( $s['legal'] ) ) : ?>
						<ul class="ms-foot__legal">
							<?php foreach ( $s['legal'] as $n => $item ) : ?>
								<?php
								$key = 'legal_' . $n;
								if ( ! empty( $item['link']['url'] ) ) {
									$this->add_link_attributes( $key, $item['link'] );
								}
								?>
								<li><a <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $item['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( 'yes' === $s['show_wordmark'] && ! empty( $s['wordmark'] ) ) : ?>
				<div class="ms-foot__wordmark" data-ms-reveal aria-hidden="true"><?php echo esc_html( $s['wordmark'] ); ?></div>
			<?php endif; ?>

		</footer>
		<?php
	}
}