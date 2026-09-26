<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Elementor {

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category( 'shopable-reel-devsazzad', array(
			'title' => 'Shopable Reel',
			'icon'  => 'eicon-video-camera',
		) );
	}

	public function register_widget( $widgets_manager ) {
		require_once SR_DEVSAZZAD_PATH . 'includes/class-sr-elementor-widget.php';
		$widgets_manager->register( new Shopable_Reel_Elementor_Widget() );
	}
}
