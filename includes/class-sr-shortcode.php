<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Shortcode {

	public function __construct() {
		add_shortcode( 'shopable_reel_devsazzad', array( $this, 'render' ) );
		add_shortcode( 'shopable_reel', array( $this, 'render' ) ); // convenient alias
	}

	public function render( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '<p>WooCommerce is required for Shopable Reel by DevSazzad.</p>';
		}

		$atts = shortcode_atts( array(
			'limit'            => '',
			'limit_mobile'     => '',
			'category'         => '',
			'orderby'          => '',
			'scroll_speed'     => '',
			'show_go_button'   => '',
			'go_button_text'   => '',
			'text_color'       => '',
			'accent_color'     => '',
			'badge_text_color' => '',
			'arrow_bg_color'   => '',
			'arrow_icon_color' => '',
			'arrow_size'       => '',
			'card_width'       => '',
			'card_height'      => '',
		), $atts, 'shopable_reel_devsazzad' );

		$query_args = array();
		if ( $atts['limit'] !== '' )    $query_args['limit']    = intval( $atts['limit'] );
		if ( $atts['category'] !== '' ) $query_args['category'] = sanitize_text_field( $atts['category'] );
		if ( $atts['orderby'] !== '' )  $query_args['orderby']  = sanitize_text_field( $atts['orderby'] );

		$items = Shopable_Reel_Helpers::get_reel_products( $query_args );

		// Start from the global settings, then layer any explicit shortcode /
		// Elementor-widget overrides on top — this is what lets an Elementor
		// instance customize colors/behavior without touching global Settings.
		$settings = wp_parse_args( get_option( 'sr_devsazzad_settings', array() ), Shopable_Reel_Settings::defaults() );

		if ( $atts['limit_mobile'] !== '' )     $settings['reel_count_mobile']  = max( 0, intval( $atts['limit_mobile'] ) );
		if ( $atts['scroll_speed'] !== '' )     $settings['scroll_speed']       = max( 100, min( 2000, intval( $atts['scroll_speed'] ) ) );
		if ( $atts['show_go_button'] !== '' )   $settings['show_go_button']     = in_array( strtolower( $atts['show_go_button'] ), array( '1', 'yes', 'true' ), true ) ? 1 : 0;
		if ( $atts['go_button_text'] !== '' )   $settings['go_button_text']     = sanitize_text_field( $atts['go_button_text'] );
		if ( $atts['text_color'] !== '' )       $settings['text_color']         = sanitize_hex_color( $atts['text_color'] ) ?: $settings['text_color'];
		if ( $atts['accent_color'] !== '' )     $settings['primary_color']      = sanitize_hex_color( $atts['accent_color'] ) ?: $settings['primary_color'];
		if ( $atts['badge_text_color'] !== '' ) $settings['badge_text_color']   = sanitize_hex_color( $atts['badge_text_color'] ) ?: $settings['badge_text_color'];
		if ( $atts['arrow_bg_color'] !== '' )   $settings['nav_button_color']   = sanitize_hex_color( $atts['arrow_bg_color'] ) ?: $settings['nav_button_color'];
		if ( $atts['arrow_icon_color'] !== '' ) $settings['nav_button_icon_color'] = sanitize_hex_color( $atts['arrow_icon_color'] ) ?: $settings['nav_button_icon_color'];
		if ( $atts['arrow_size'] !== '' )       $settings['nav_button_size']    = max( 20, intval( $atts['arrow_size'] ) );
		if ( $atts['card_width'] !== '' )       $settings['card_width']         = max( 100, intval( $atts['card_width'] ) );
		if ( $atts['card_height'] !== '' )      $settings['card_height']        = max( 150, intval( $atts['card_height'] ) );

		$settings['badge_text'] = Shopable_Reel_Helpers::clean_badge_suffix( $settings['badge_text'] );

		wp_enqueue_style( 'sr-devsazzad-frontend' );
		wp_enqueue_script( 'sr-devsazzad-frontend' );

		ob_start();
		include SR_DEVSAZZAD_PATH . 'templates/carousel-template.php';
		return ob_get_clean();
	}
}
