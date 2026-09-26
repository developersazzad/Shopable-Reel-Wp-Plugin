<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'shopable_reel_devsazzad'; }
	public function get_title() { return 'Shopable Reel Carousel'; }
	public function get_icon() { return 'eicon-video-camera'; }
	public function get_categories() { return array( 'shopable-reel-devsazzad' ); }
	public function get_keywords() { return array( 'reel', 'video', 'woocommerce', 'carousel', 'shoppable', 'shorts' ); }

	protected function register_controls() {

		// ---------- CONTENT TAB ----------
		$this->start_controls_section( 'sr_section_content', array(
			'label' => 'Reel Settings',
		) );

		$this->add_control( 'limit', array(
			'label'   => 'Number of Reels (Desktop)',
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 10,
			'min'     => 1,
		) );

		$this->add_control( 'limit_mobile', array(
			'label'       => 'Number of Reels (Mobile)',
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => '',
			'min'         => 0,
			'description' => 'Leave empty to show the same number as desktop.',
		) );

		$this->add_control( 'category', array(
			'label'       => 'Category Slug(s)',
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. lawn, bridal',
			'description' => 'Comma separated product category slugs. Leave empty for all categories.',
		) );

		$this->add_control( 'orderby', array(
			'label'   => 'Order By',
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'date',
			'options' => array(
				'date'       => 'Newest',
				'popularity' => 'Popularity',
				'price'      => 'Price',
				'title'      => 'Title',
				'rand'       => 'Random',
			),
		) );

		$this->add_control( 'scroll_speed', array(
			'label'       => 'Scroll Animation Speed (ms)',
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => '',
			'min'         => 100,
			'max'         => 2000,
			'description' => 'Leave empty to use the global Settings value.',
		) );

		$this->add_control( 'show_go_button', array(
			'label'        => 'Show "Go to Product" Button',
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'Use global setting',
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'go_button_text', array(
			'label'     => 'Button Text',
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => '',
			'condition' => array( 'show_go_button' => 'yes' ),
		) );

		$this->end_controls_section();

		// ---------- STYLE TAB ----------
		$this->start_controls_section( 'sr_section_style', array(
			'label' => 'Colors & Sizing',
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'text_color', array(
			'label'   => 'Text Color',
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
		) );

		$this->add_control( 'accent_color', array(
			'label'   => 'Accent / Badge Background Color',
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
		) );

		$this->add_control( 'badge_text_color', array(
			'label'   => 'Badge Text Color',
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
		) );

		$this->add_control( 'arrow_bg_color', array(
			'label'   => 'Arrow Background Color',
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
		) );

		$this->add_control( 'arrow_icon_color', array(
			'label'   => 'Arrow Icon Color',
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => '',
		) );

		$this->add_control( 'arrow_size', array(
			'label'   => 'Arrow Size (px)',
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
			'min'     => 20,
			'max'     => 80,
		) );

		$this->add_control( 'card_width', array(
			'label'   => 'Card Width (px)',
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
			'min'     => 100,
		) );

		$this->add_control( 'card_height', array(
			'label'   => 'Card Height (px)',
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
			'min'     => 150,
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display(); 

		$atts = array(
			'limit'            => intval( $settings['limit'] ),
			'limit_mobile'     => $settings['limit_mobile'] !== '' ? intval( $settings['limit_mobile'] ) : '',
			'category'         => $settings['category'],
			'orderby'          => $settings['orderby'],
			'scroll_speed'     => $settings['scroll_speed'] !== '' ? intval( $settings['scroll_speed'] ) : '',
			'show_go_button'   => $settings['show_go_button'] === 'yes' ? 'yes' : '',
			'go_button_text'   => $settings['go_button_text'],
			'text_color'       => is_array( $settings['text_color'] ) ? '' : $settings['text_color'],
			'accent_color'     => is_array( $settings['accent_color'] ) ? '' : $settings['accent_color'],
			'badge_text_color' => is_array( $settings['badge_text_color'] ) ? '' : $settings['badge_text_color'],
			'arrow_bg_color'   => is_array( $settings['arrow_bg_color'] ) ? '' : $settings['arrow_bg_color'],
			'arrow_icon_color' => is_array( $settings['arrow_icon_color'] ) ? '' : $settings['arrow_icon_color'],
			'arrow_size'       => $settings['arrow_size'] !== '' ? intval( $settings['arrow_size'] ) : '',
			'card_width'       => $settings['card_width'] !== '' ? intval( $settings['card_width'] ) : '',
			'card_height'      => $settings['card_height'] !== '' ? intval( $settings['card_height'] ) : '',
		);

		$shortcode = '[shopable_reel_devsazzad';
		foreach ( $atts as $key => $value ) {
			if ( $value === '' || $value === null ) continue;
			$shortcode .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
		$shortcode .= ']';

		echo do_shortcode( $shortcode );
	}
}
