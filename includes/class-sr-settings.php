<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_sr_save_reel_video', array( $this, 'save_reel_video' ) );
		add_action( 'admin_post_sr_delete_reel_video', array( $this, 'delete_reel_video' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
	}

	public static function defaults() {
		return array(
			'acf_field'                  => 'reel_video_url',
			'reel_count'                 => 10,
			'category'                   => '',
			'orderby'                    => 'date',
			'only_with_video'            => 1,
			'badge_text'                 => '% Off',
			'primary_color'              => '#e74c3c',
			'card_width'                 => 200,
			'card_height'                => 350,
			'autoplay'                   => 1,
			'muted'                      => 1,
			// Carousel & reel-viewer appearance
			'text_color'                 => '#ffffff',
			'badge_text_color'           => '#ffffff',
			'nav_button_color'           => '#000000',
			'nav_button_icon_color'      => '#ffffff',
			'nav_button_size'            => 36,
			'modal_button_color'         => '#000000',
			'modal_button_icon_color'    => '#ffffff',
			'modal_button_size'          => 34,

			// Carousel behavior & extra features
			'reel_count_mobile'          => 0,       // 0 = same as desktop
			'scroll_speed'               => 450,     // ms, custom eased smooth-scroll
			'card_hover_expand_enabled'  => 1,
			'show_go_button'             => 1,
			'go_button_text'             => 'Go',

			// Reel viewer (modal) extras
			'side_reel_overlay_opacity'  => 40,       // 0-100
			'side_reel_blur'             => 2,        // px
			'modal_bottom_overlay_visible' => 1,

			// Single-product floating reel video
			'auto_floating_video'       => 1,
			'floating_gallery_selector'  => '#main_images_product',
			'floating_fallback_selector' => '.woocommerce-product-gallery',
			'floating_position'         => 'bottom-right',
			'floating_desktop_width'    => 160,
			'floating_mobile_width'     => 120,
			'floating_autoplay'         => 1,
			'floating_icon_bg_color'    => '#141414',
			'floating_icon_color'       => '#ffffff',
			'floating_icon_size'        => 13,
			'floating_expand_position'  => 'bottom-right',
			'floating_close_position'   => 'top-right',
			'floating_icon_style'       => 'minimal', // minimal (no circle) or circle
		);
	}

	public function enqueue_admin( $hook ) {
		if ( strpos( $hook, 'shopable-reel' ) === false ) return;
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'sr-devsazzad-admin', SR_DEVSAZZAD_URL . 'assets/css/sr-admin.css', array(), SR_DEVSAZZAD_VERSION );
		wp_enqueue_script( 'sr-devsazzad-admin', SR_DEVSAZZAD_URL . 'assets/js/sr-admin.js', array( 'jquery', 'wp-color-picker' ), SR_DEVSAZZAD_VERSION, true );
	}

	public function add_menu() {
		add_menu_page(
			'Shopable Reel',
			'Shopable Reel',
			'manage_options',
			'shopable-reel-devsazzad',
			array( $this, 'render_reels_page' ),
			'dashicons-video-alt3',
			56
		);
		add_submenu_page( 'shopable-reel-devsazzad', 'Manage Reels', 'Manage Reels', 'manage_options', 'shopable-reel-devsazzad', array( $this, 'render_reels_page' ) );
		add_submenu_page( 'shopable-reel-devsazzad', 'Settings', 'Settings', 'manage_options', 'shopable-reel-devsazzad-settings', array( $this, 'render_settings_page' ) );
		add_submenu_page( 'shopable-reel-devsazzad', 'Shortcode & Help', 'Shortcode & Help', 'manage_options', 'shopable-reel-devsazzad-help', array( $this, 'render_help_page' ) );
	}

	public function register_settings() {
		register_setting( 'sr_devsazzad_settings_group', 'sr_devsazzad_settings', array( $this, 'sanitize_settings' ) );
	}

	public function sanitize_settings( $input ) {
		$out = array();
		$out['acf_field']       = sanitize_key( $input['acf_field'] ?? 'reel_video_url' );
		$out['reel_count']      = max( 1, intval( $input['reel_count'] ?? 10 ) );
		$out['category']        = sanitize_text_field( $input['category'] ?? '' );
		$allowed_orderby        = array( 'date', 'popularity', 'price', 'title', 'rand' );
		$out['orderby']         = in_array( $input['orderby'] ?? 'date', $allowed_orderby, true ) ? $input['orderby'] : 'date';
		$out['only_with_video'] = ! empty( $input['only_with_video'] ) ? 1 : 0;
		$out['badge_text']      = Shopable_Reel_Helpers::clean_badge_suffix( sanitize_text_field( $input['badge_text'] ?? '% Off' ) );
		$out['primary_color']   = sanitize_hex_color( $input['primary_color'] ?? '#e74c3c' );
		$out['card_width']      = max( 120, intval( $input['card_width'] ?? 200 ) );
		$out['card_height']     = max( 200, intval( $input['card_height'] ?? 350 ) );
		$out['autoplay']        = ! empty( $input['autoplay'] ) ? 1 : 0;
		$out['muted']           = ! empty( $input['muted'] ) ? 1 : 0;

		// Carousel & reel-viewer appearance
		$out['text_color']              = sanitize_hex_color( $input['text_color'] ?? '#ffffff' ) ?: '#ffffff';
		$out['badge_text_color']        = sanitize_hex_color( $input['badge_text_color'] ?? '#ffffff' ) ?: '#ffffff';
		$out['nav_button_color']        = sanitize_hex_color( $input['nav_button_color'] ?? '#000000' ) ?: '#000000';
		$out['nav_button_icon_color']   = sanitize_hex_color( $input['nav_button_icon_color'] ?? '#ffffff' ) ?: '#ffffff';
		$out['nav_button_size']         = max( 20, intval( $input['nav_button_size'] ?? 36 ) );
		$out['modal_button_color']      = sanitize_hex_color( $input['modal_button_color'] ?? '#000000' ) ?: '#000000';
		$out['modal_button_icon_color'] = sanitize_hex_color( $input['modal_button_icon_color'] ?? '#ffffff' ) ?: '#ffffff';
		$out['modal_button_size']       = max( 20, intval( $input['modal_button_size'] ?? 34 ) );

		// Carousel behavior & extra features
		$out['reel_count_mobile']         = max( 0, intval( $input['reel_count_mobile'] ?? 0 ) );
		$out['scroll_speed']              = max( 100, min( 2000, intval( $input['scroll_speed'] ?? 450 ) ) );
		$out['card_hover_expand_enabled'] = ! empty( $input['card_hover_expand_enabled'] ) ? 1 : 0;
		$out['show_go_button']           = ! empty( $input['show_go_button'] ) ? 1 : 0;
		$out['go_button_text']           = sanitize_text_field( $input['go_button_text'] ?? 'Go' ) ?: 'Go';

		// Reel viewer (modal) extras
		$out['side_reel_overlay_opacity']    = max( 0, min( 100, intval( $input['side_reel_overlay_opacity'] ?? 40 ) ) );
		$out['side_reel_blur']               = max( 0, min( 20, intval( $input['side_reel_blur'] ?? 2 ) ) );
		$out['modal_bottom_overlay_visible'] = ! empty( $input['modal_bottom_overlay_visible'] ) ? 1 : 0;

		// Single-product floating reel video
		$out['auto_floating_video']        = ! empty( $input['auto_floating_video'] ) ? 1 : 0;
		$out['floating_gallery_selector']  = sanitize_text_field( $input['floating_gallery_selector'] ?? '#main_images_product' );
		$out['floating_fallback_selector'] = sanitize_text_field( $input['floating_fallback_selector'] ?? '.woocommerce-product-gallery' );
		$allowed_positions                 = array( 'bottom-right', 'bottom-left', 'top-right', 'top-left' );
		$out['floating_position']          = in_array( $input['floating_position'] ?? 'bottom-right', $allowed_positions, true ) ? $input['floating_position'] : 'bottom-right';
		$out['floating_desktop_width']     = max( 60, intval( $input['floating_desktop_width'] ?? 160 ) );
		$out['floating_mobile_width']      = max( 60, intval( $input['floating_mobile_width'] ?? 120 ) );
		$out['floating_autoplay']          = ! empty( $input['floating_autoplay'] ) ? 1 : 0;
		$out['floating_icon_bg_color']     = sanitize_hex_color( $input['floating_icon_bg_color'] ?? '#141414' ) ?: '#141414';
		$out['floating_icon_color']        = sanitize_hex_color( $input['floating_icon_color'] ?? '#ffffff' ) ?: '#ffffff';
		$out['floating_icon_size']         = max( 10, intval( $input['floating_icon_size'] ?? 13 ) );
		$allowed_corners                   = array( 'top-left', 'top-right', 'bottom-left', 'bottom-right' );
		$out['floating_expand_position']   = in_array( $input['floating_expand_position'] ?? 'bottom-right', $allowed_corners, true ) ? $input['floating_expand_position'] : 'bottom-right';
		$out['floating_close_position']    = in_array( $input['floating_close_position'] ?? 'top-right', $allowed_corners, true ) ? $input['floating_close_position'] : 'top-right';
		$allowed_icon_styles                = array( 'minimal', 'circle' );
		$out['floating_icon_style']         = in_array( $input['floating_icon_style'] ?? 'minimal', $allowed_icon_styles, true ) ? $input['floating_icon_style'] : 'minimal';

		return $out;
	}

	public function save_reel_video() {
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		check_admin_referer( 'sr_save_reel_video_nonce' );

		$product_id = intval( $_POST['product_id'] ?? 0 );
		$video_url  = esc_url_raw( $_POST['video_url'] ?? '' );
		$view_count = intval( $_POST['view_count'] ?? 0 );

		if ( $product_id ) {
			$field = Shopable_Reel_Helpers::get_video_field_name();
			update_post_meta( $product_id, $field, $video_url );
			update_post_meta( $product_id, '_sr_view_count', max( 0, $view_count ) );
			wp_cache_delete( 'sr_devsazzad_dashboard_stats', 'shopable_reel' );
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'shopable-reel-devsazzad', 'sr_msg' => 'saved' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function delete_reel_video() {
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		check_admin_referer( 'sr_delete_reel_video_nonce' );

		$product_id = intval( $_POST['product_id'] ?? 0 );
		if ( $product_id ) {
			$field = Shopable_Reel_Helpers::get_video_field_name();
			delete_post_meta( $product_id, $field );
			wp_cache_delete( 'sr_devsazzad_dashboard_stats', 'shopable_reel' );
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'shopable-reel-devsazzad', 'sr_msg' => 'deleted' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function render_reels_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;
		require SR_DEVSAZZAD_PATH . 'templates/admin-reels-page.php';
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;
		require SR_DEVSAZZAD_PATH . 'templates/admin-settings-page.php';
	}

	public function render_help_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;
		require SR_DEVSAZZAD_PATH . 'templates/admin-help-page.php';
	}
}
