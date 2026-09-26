<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles the single-product floating reel video.
 *
 * This is a separate, additive feature from the main reels carousel. It
 * renders a small floating, draggable, expandable video mapped into the
 * single-product gallery image — using the SAME video URL already stored
 * via Shopable Reel → Manage Reels (no ACF required).
 *
 * Two ways to place it:
 * 1. Automatic (default, no setup needed): as soon as a product has a video
 *    connected in Manage Reels, it just appears on that product's page.
 * 2. Manual: the [shopable_reel_single_video] shortcode, for anyone who
 *    wants to control exactly where it sits via Elementor/a template.
 *
 * A per-request "already rendered" guard prevents it from appearing twice if
 * both the automatic hook and a manually-placed shortcode are active on the
 * same page.
 */
class Shopable_Reel_Single_Shortcode {

	private static $rendered_ids = array();

	public function __construct() {
		add_shortcode( 'shopable_reel_single_video', array( $this, 'render_shortcode' ) );
		add_action( 'wp_footer', array( $this, 'maybe_auto_render' ) );
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'product_id' => '',
		), $atts, 'shopable_reel_single_video' );

		$product_id = $atts['product_id'] !== '' ? intval( $atts['product_id'] ) : get_the_ID();

		return $this->render_for_product( $product_id );
	}

	/**
	 * Automatically outputs the floating video on any single product page,
	 * with zero shortcode/template setup, as long as the product has a video
	 * connected and the "Automatically show" setting is on.
	 */
	public function maybe_auto_render() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) return;

		$settings = wp_parse_args( get_option( 'sr_devsazzad_settings', array() ), Shopable_Reel_Settings::defaults() );
		if ( empty( $settings['auto_floating_video'] ) ) return;

		$product_id = get_queried_object_id();
		echo $this->render_for_product( $product_id ); // phpcs:ignore -- already escaped inside the template
	}

	private function render_for_product( $product_id ) {
		if ( ! class_exists( 'WooCommerce' ) ) return '';
		if ( ! $product_id || get_post_type( $product_id ) !== 'product' ) return '';

		// Prevent a duplicate render if both the automatic hook and a
		// manually-placed shortcode are active for the same product/page.
		if ( in_array( $product_id, self::$rendered_ids, true ) ) return '';

		$video_url = Shopable_Reel_Helpers::get_product_video_url( $product_id );
		$video_id  = Shopable_Reel_Helpers::extract_youtube_id( $video_url );

		if ( ! $video_id ) {
			return ''; // No video set for this product — render nothing.
		}

		$settings = wp_parse_args( get_option( 'sr_devsazzad_settings', array() ), Shopable_Reel_Settings::defaults() );

		wp_enqueue_style( 'sr-devsazzad-floating' );
		wp_enqueue_script( 'sr-devsazzad-floating' );

		self::$rendered_ids[] = $product_id;

		ob_start();
		include SR_DEVSAZZAD_PATH . 'templates/floating-video-template.php';
		return ob_get_clean();
	}
}
