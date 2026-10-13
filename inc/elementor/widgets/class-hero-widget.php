<?php
if (! defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class My_Store_Hero_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'ms-hero';
    }

    public function get_title()
    {
        return esc_html__('Hero Slider', 'my-store');
    }

    public function get_icon()
    {
        return 'eicon-slider-push';
    }

    public function get_categories()
    {
        return array('my-store');
    }

    public function get_keywords()
    {
        return array('hero', 'slider', 'banner', 'products');
    }

    /**
     * Color control that writes a CSS variable on .ms-hero.
     */
    private function add_var_color($id, $label, $var, $default = '')
    {
        $args = array(
            'label'     => $label,
            'type'      => Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .ms-hero' => $var . ': {{VALUE}};',
            ),
        );

        if ($default) {
            $args['default'] = $default;
        }

        $this->add_control($id, $args);
    }

    /**
     * Image helper.
     */
    private function render_image($media, $size, $class, $eager = false)
    {
        if (empty($media['url'])) {
            return;
        }

        if (! empty($media['id'])) {
            echo wp_get_attachment_image(
                $media['id'],
                $size,
                false,
                array(
                    'class'   => $class,
                    'loading' => $eager ? 'eager' : 'lazy',
                )
            ); // phpcs:ignore WordPress.Security.EscapeOutput
        } else {
            printf('<img class="%s" src="%s" alt="">', esc_attr($class), esc_url($media['url']));
        }
    }

    protected function register_controls()
    {

        $placeholder = array('url' => Utils::get_placeholder_image_src());

        /* ------------------------------------------------------------------
		 * CONTENT: Slides
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'section_slides',
            array(
                'label' => esc_html__('Slides', 'my-store'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $slide = new Repeater();

        $slide->add_control(
            'image',
            array(
                'label'   => esc_html__('Product Image (transparent PNG/WebP)', 'my-store'),
                'type'    => Controls_Manager::MEDIA,
                'default' => $placeholder,
            )
        );

        $slide->add_control(
            'thumb',
            array(
                'label'       => esc_html__('Thumbnail Icon (optional)', 'my-store'),
                'type'        => Controls_Manager::MEDIA,
                'description' => esc_html__('Leave empty to reuse the product image.', 'my-store'),
            )
        );

        $slide->add_control(
            'label',
            array(
                'label'   => esc_html__('Category Label', 'my-store'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Men', 'my-store'),
                'dynamic' => array('active' => true),
            )
        );

        $slide->add_control(
            'word',
            array(
                'label'       => esc_html__('Big Background Word', 'my-store'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__('Leave empty to use the category label.', 'my-store'),
            )
        );

        $slide->add_control(
            'title',
            array(
                'label'   => esc_html__('Title', 'my-store'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Tailored for Him', 'my-store'),
                'dynamic' => array('active' => true),
            )
        );

        $slide->add_control(
            'description',
            array(
                'label'   => esc_html__('Description', 'my-store'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Clean cuts and honest fabrics for every day.', 'my-store'),
                'dynamic' => array('active' => true),
            )
        );

        $slide->add_control(
            'button_text',
            array(
                'label'   => esc_html__('Button Text', 'my-store'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Shop Men', 'my-store'),
            )
        );

        $slide->add_control(
            'button_link',
            array(
                'label'   => esc_html__('Button Link', 'my-store'),
                'type'    => Controls_Manager::URL,
                'default' => array('url' => home_url('/shop/')),
                'dynamic' => array('active' => true),
            )
        );

        $slide->add_control(
            'bg_color',
            array(
                'label'     => esc_html__('Slide Background', 'my-store'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#E7DFD3',
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => '--ms-hero-bg: {{VALUE}};',
                ),
            )
        );

        $slide->add_control(
            'text_color',
            array(
                'label'     => esc_html__('Slide Text Color', 'my-store'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1A1A1A',
                'selectors' => array(
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => '--ms-hero-text: {{VALUE}};',
                ),
            )
        );

        $defaults = array(
            array('Men', 'Tailored for Him', 'Clean cuts and honest fabrics for every day.', 'Shop Men', '#E7DFD3', '#1A1A1A'),
            array('Women', 'Made to Move', 'Soft silhouettes designed to flow with you.', 'Shop Women', '#EBD3CA', '#1A1A1A'),
            array('Kids', 'Little Legends', 'Playful, durable and ready for adventure.', 'Shop Kids', '#F2E2B0', '#1A1A1A'),
            array('Western', 'The Wild Edit', 'Denim, leather and a touch of frontier spirit.', 'Shop Western', '#D8C3A5', '#1A1A1A'),
            array('Traditional', 'Rooted in Heritage', 'Timeless craft for celebrations and family moments.', 'Shop Traditional', '#1F2A24', '#F6F2EC'),
        );

        $default_slides = array();
        foreach ($defaults as $d) {
            $default_slides[] = array(
                'image'       => $placeholder,
                'label'       => $d[0],
                'title'       => $d[1],
                'description' => $d[2],
                'button_text' => $d[3],
                'button_link' => array('url' => home_url('/shop/')),
                'bg_color'    => $d[4],
                'text_color'  => $d[5],
            );
        }

        $this->add_control(
            'slides',
            array(
                'label'       => esc_html__('Slides', 'my-store'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $slide->get_controls(),
                'title_field' => '{{{ label }}}',
                'default'     => $default_slides,
            )
        );

        $this->end_controls_section();

        /* ------------------------------------------------------------------
		 * CONTENT: Behavior
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'section_behavior',
            array(
                'label' => esc_html__('Behavior & Controls', 'my-store'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'autoplay',
            array(
                'label'        => esc_html__('Autoplay', 'my-store'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'description'  => esc_html__('Autoplay is paused inside the Elementor editor.', 'my-store'),
            )
        );

        $this->add_control(
            'autoplay_speed',
            array(
                'label'     => esc_html__('Autoplay Delay (ms)', 'my-store'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 6000,
                'min'       => 2000,
                'step'      => 500,
                'condition' => array('autoplay' => 'yes'),
            )
        );

        $this->add_control(
            'pause_hover',
            array(
                'label'        => esc_html__('Pause on Hover', 'my-store'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array('autoplay' => 'yes'),
            )
        );

        $this->add_control(
            'show_thumbs',
            array(
                'label'        => esc_html__('Show Thumbnails', 'my-store'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            )
        );

        // $this->add_control(
        // 	'show_arrows',
        // 	array(
        // 		'label'        => esc_html__( 'Show Arrows', 'my-store' ),
        // 		'type'         => Controls_Manager::SWITCHER,
        // 		'return_value' => 'yes',
        // 		'default'      => 'yes',
        // 	)
        // );

        $this->add_control(
            'show_counter',
            array(
                'label'        => esc_html__('Show Counter', 'my-store'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();

        /* ------------------------------------------------------------------
		 * STYLE: Layout
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'style_layout',
            array(
                'label' => esc_html__('Layout & Image', 'my-store'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'hero_height',
            array(
                'label'          => esc_html__('Height', 'my-store'),
                'type'           => Controls_Manager::SLIDER,
                'size_units'     => array('px', 'vh'),
                'range'          => array(
                    'px' => array('min' => 400, 'max' => 1100),
                    'vh' => array('min' => 40, 'max' => 100),
                ),
                'default'        => array('size' => 80, 'unit' => 'vh'),
                'tablet_default' => array('size' => 760, 'unit' => 'px'),
                'mobile_default' => array('size' => 700, 'unit' => 'px'),
                'selectors'      => array(
                    '{{WRAPPER}} .ms-hero' => '--ms-hero-height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'image_width',
            array(
                'label'      => esc_html__('Product Max Width', 'my-store'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array('px' => array('min' => 200, 'max' => 900)),
                'default'    => array('size' => 560, 'unit' => 'px'),
                'selectors'  => array(
                    '{{WRAPPER}} .ms-hero' => '--ms-hero-img-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'word_opacity',
            array(
                'label'      => esc_html__('Background Word Opacity', 'my-store'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => array('px' => array('min' => 0, 'max' => 0.4, 'step' => 0.01)),
                'default'    => array('size' => 0.08),
                'size_units' => array('px'),
                'selectors'  => array(
                    '{{WRAPPER}} .ms-hero' => '--ms-hero-word-opacity: {{SIZE}};',
                ),
            )
        );

        $this->end_controls_section();

        /* ------------------------------------------------------------------
		 * STYLE: Text
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'style_text',
            array(
                'label' => esc_html__('Text', 'my-store'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'label_typography',
                'label'    => esc_html__('Category Label', 'my-store'),
                'selector' => '{{WRAPPER}} .ms-hero__eyebrow',
                'global'   => array('default' => Global_Typography::TYPOGRAPHY_ACCENT),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__('Title', 'my-store'),
                'selector' => '{{WRAPPER}} .ms-hero__title',
                'global'   => array('default' => Global_Typography::TYPOGRAPHY_PRIMARY),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'label'    => esc_html__('Description', 'my-store'),
                'selector' => '{{WRAPPER}} .ms-hero__desc',
                'global'   => array('default' => Global_Typography::TYPOGRAPHY_TEXT),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'word_typography',
                'label'    => esc_html__('Background Word', 'my-store'),
                'selector' => '{{WRAPPER}} .ms-hero__word',
                'global'   => array('default' => Global_Typography::TYPOGRAPHY_PRIMARY),
            )
        );

        $this->end_controls_section();

        /* ------------------------------------------------------------------
		 * STYLE: Button
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'style_button',
            array(
                'label' => esc_html__('Button', 'my-store'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'button_typography',
                'selector' => '{{WRAPPER}} .ms-hero__button',
                'global'   => array('default' => Global_Typography::TYPOGRAPHY_ACCENT),
            )
        );

        $this->add_control(
            'button_note',
            array(
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Leave colors empty to auto-match each slide (button uses the slide text color as background).', 'my-store'),
                'content_classes' => 'elementor-descriptor',
            )
        );

        $this->add_var_color('btn_bg', esc_html__('Background', 'my-store'), '--ms-hero-btn-bg');
        $this->add_var_color('btn_text', esc_html__('Text', 'my-store'), '--ms-hero-btn-text');
        $this->add_var_color('btn_hover_bg', esc_html__('Hover Background', 'my-store'), '--ms-hero-btn-hover-bg');
        $this->add_var_color('btn_hover_text', esc_html__('Hover Text', 'my-store'), '--ms-hero-btn-hover-text');

        $this->end_controls_section();

        /* ------------------------------------------------------------------
		 * STYLE: Thumbnails & arrows
		 * ---------------------------------------------------------------- */
        /* ------------------------------------------------------------------
		 * STYLE: Thumbnails
		 * ---------------------------------------------------------------- */
        $this->start_controls_section(
            'style_thumbs',
            array(
                'label' => esc_html__('Thumbnails & Counter', 'my-store'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_var_color('ui_color', esc_html__('Thumbnails / Counter Color (empty = slide text color)', 'my-store'), '--ms-hero-ui-override');
        $this->add_var_color('ring_color', esc_html__('Active Thumbnail Border (empty = slide text color)', 'my-store'), '--ms-hero-ring');

        $this->end_controls_section();
    }

    protected function render()
    {
        $s      = $this->get_settings_for_display();
        $slides = ! empty($s['slides']) ? $s['slides'] : array();

        if (empty($slides)) {
            return;
        }

        $total = count($slides);
        // $shape = ! empty($s['thumb_shape']) ? $s['thumb_shape'] : 'circle';

        $this->add_render_attribute(
            'root',
            array(
                // 'class'         => array('ms-hero', 'ms-hero--thumbs-' . $shape),
                'class'         => array('ms-hero'),
                'data-autoplay' => ('yes' === $s['autoplay']) ? 'yes' : 'no',
                'data-speed'    => ! empty($s['autoplay_speed']) ? absint($s['autoplay_speed']) : 6000,
                'data-pause'    => ('yes' === $s['pause_hover']) ? 'yes' : 'no',
            )
        );
?>
        <section <?php $this->print_render_attribute_string('root'); ?>>

            <div class="ms-hero__slides">
                <?php foreach ($slides as $i => $slide) : ?>
                    <?php
                    $is_first = (0 === $i);
                    $word     = ! empty($slide['word']) ? $slide['word'] : $slide['label'];
                    $btn_key  = 'hero_btn_' . $i;

                    $this->add_render_attribute($btn_key, 'class', 'ms-hero__button');
                    if (! empty($slide['button_link']['url'])) {
                        $this->add_link_attributes($btn_key, $slide['button_link']);
                    }
                    ?>
                    <article class="ms-hero__slide elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?><?php echo $is_first ? ' is-active' : ' is-after'; ?>" data-index="<?php echo esc_attr($i); ?>">

                        <span class="ms-hero__word" aria-hidden="true"><?php echo esc_html($word); ?></span>

                        <div class="ms-hero__media">
                            <?php $this->render_image($slide['image'], 'full', 'ms-hero__image', $is_first); ?>
                        </div>

                        <div class="ms-hero__content ms-container">
                            <div class="ms-hero__text">
                                <?php if (! empty($slide['label'])) : ?>
                                    <p class="ms-hero__eyebrow"><?php echo esc_html($slide['label']); ?></p>
                                <?php endif; ?>

                                <?php if (! empty($slide['title'])) : ?>
                                    <h2 class="ms-hero__title"><?php echo nl2br(esc_html($slide['title'])); ?></h2>
                                <?php endif; ?>

                                <?php if (! empty($slide['description'])) : ?>
                                    <p class="ms-hero__desc"><?php echo esc_html($slide['description']); ?></p>
                                <?php endif; ?>

                                <?php if (! empty($slide['button_text'])) : ?>
                                    <a <?php $this->print_render_attribute_string($btn_key); ?>><?php echo esc_html($slide['button_text']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ('yes' === $s['show_counter']) : ?>
                <div class="ms-hero__counter" aria-hidden="true">
                    <span data-ms-hero-current>01</span>
                    <span class="ms-hero__counter-sep">/</span>
                    <span><?php echo esc_html(str_pad($total, 2, '0', STR_PAD_LEFT)); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($total > 1 && 'yes' === $s['show_thumbs']) : ?>
                <div class="ms-hero__controls">
                    <div class="ms-hero__thumbs">
                        <?php foreach ($slides as $i => $slide) : ?>
                            <?php $thumb = ! empty($slide['thumb']['url']) ? $slide['thumb'] : $slide['image']; ?>
                            <button type="button" class="ms-hero__thumb<?php echo (0 === $i) ? ' is-active' : ''; ?>" style="--i:<?php echo esc_attr($i); ?>" data-index="<?php echo esc_attr($i); ?>" aria-label="<?php echo esc_attr($slide['label']); ?>">
                                <?php $this->render_image($thumb, 'medium', 'ms-hero__thumb-img'); ?>
                                <span class="ms-hero__bar"></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </section>
<?php
    }
}
