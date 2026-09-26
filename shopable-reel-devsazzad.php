<?php
/**
 * Plugin Name: Shopable Reel by DevSazzad
 * Plugin URI:  https://devsazzad.com
 * Description: Turn your WooCommerce products into an Instagram/TikTok style shoppable video reels carousel, complete with a full-screen reel viewer. Works via Shortcode and Elementor widget.
 * Version:     1.5.0
 * Author:      DevSazzad
 * Text Domain: shopable-reel-devsazzad
 * Requires PHP: 7.4
 * Requires at least: 5.8
 * WC requires at least: 5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SR_DEVSAZZAD_VERSION', '1.5.0' );
define( 'SR_DEVSAZZAD_FILE', __FILE__ );
define( 'SR_DEVSAZZAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'SR_DEVSAZZAD_URL', plugin_dir_url( __FILE__ ) );

final class Shopable_Reel_DevSazzad {

	private static $instance = null;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( SR_DEVSAZZAD_FILE, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function activate() {
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-settings.php';
		if ( ! get_option( 'sr_devsazzad_settings' ) ) {
			update_option( 'sr_devsazzad_settings', Shopable_Reel_Settings::defaults() );
		}
	}

	public function init() {

		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-helpers.php';
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-settings.php';
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-shortcode.php';
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-single-shortcode.php';
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-ajax.php';

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'wc_missing_notice' ) );
		}

		new Shopable_Reel_Settings();
		new Shopable_Reel_Shortcode();
		new Shopable_Reel_Single_Shortcode();
		new Shopable_Reel_Ajax();

		if ( did_action( 'elementor/loaded' ) ) {
			$this->load_elementor();
		} else {
			add_action( 'elementor/loaded', array( $this, 'load_elementor' ) );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		// Elementor's editor preview iframe fires its own enqueue hooks rather
		// than always guaranteeing wp_enqueue_scripts has fully applied by the
		// time widgets render, which is why the carousel could look unstyled
		// inside the editor even though the live page was fine. Hooking these
		// directly guarantees our CSS/JS is present in that preview frame too.
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_frontend' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_frontend' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
	}

	public function load_elementor() {
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-elementor.php';
		new Shopable_Reel_Elementor();
	}

	public function wc_missing_notice() {
		echo '<div class="notice notice-warning"><p><strong>Shopable Reel by DevSazzad</strong> requires WooCommerce to be installed and active.</p></div>';
	}

	public function enqueue_frontend() {
		// Enqueued (not just registered) unconditionally on every relevant
		// hook — small files, and this sidesteps the classic "wp_enqueue_style
		// called from inside a shortcode/widget render callback after
		// wp_head already printed" issue that left the Elementor editor
		// preview without our CSS even though the shortcode itself rendered.
		wp_enqueue_style( 'sr-devsazzad-frontend', SR_DEVSAZZAD_URL . 'assets/css/sr-frontend.css', array(), SR_DEVSAZZAD_VERSION );
		wp_enqueue_script( 'sr-devsazzad-frontend', SR_DEVSAZZAD_URL . 'assets/js/sr-frontend.js', array(), SR_DEVSAZZAD_VERSION, true );
		wp_localize_script( 'sr-devsazzad-frontend', 'SR_DEVSAZZAD', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'sr_devsazzad_nonce' ),
		) );

		// Single-product floating reel video (separate, additive feature).
		wp_enqueue_style( 'sr-devsazzad-floating', SR_DEVSAZZAD_URL . 'assets/css/sr-floating.css', array(), SR_DEVSAZZAD_VERSION );
		wp_enqueue_script( 'sr-devsazzad-floating', SR_DEVSAZZAD_URL . 'assets/js/sr-floating.js', array(), SR_DEVSAZZAD_VERSION, true );
	}
}

Shopable_Reel_DevSazzad::instance();
