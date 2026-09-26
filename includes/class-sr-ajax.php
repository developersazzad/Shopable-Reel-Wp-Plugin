<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_sr_devsazzad_track_view', array( $this, 'track_view' ) );
		add_action( 'wp_ajax_nopriv_sr_devsazzad_track_view', array( $this, 'track_view' ) );
	}

	public function track_view() {
		check_ajax_referer( 'sr_devsazzad_nonce', 'nonce' );

		$product_id = intval( $_POST['product_id'] ?? 0 );
		if ( $product_id && get_post_type( $product_id ) === 'product' ) {
			$count = Shopable_Reel_Helpers::increment_view_count( $product_id );
			wp_send_json_success( array(
				'views'     => $count,
				'formatted' => Shopable_Reel_Helpers::format_views( $count ),
			) );
		}

		wp_send_json_error();
	}
}
