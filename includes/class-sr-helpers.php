<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Shopable_Reel_Helpers {

	public static function get_video_field_name() {
		$settings = get_option( 'sr_devsazzad_settings', array() );
		return ! empty( $settings['acf_field'] ) ? $settings['acf_field'] : 'reel_video_url';
	}

	public static function get_product_video_url( $product_id ) {
		$field = self::get_video_field_name();
		$value = '';

		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $field, $product_id );
		}
		if ( empty( $value ) ) {
			$value = get_post_meta( $product_id, $field, true );
		}
		return $value ? esc_url_raw( $value ) : '';
	}

	public static function extract_youtube_id( $url ) {
		if ( empty( $url ) ) return '';
		$pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
		if ( preg_match( $pattern, $url, $m ) ) {
			return $m[1];
		}
		return '';
	}

	public static function get_youtube_thumbnail( $video_id ) {
		return $video_id ? 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg' : '';
	}

	public static function get_view_count( $product_id ) {
		return (int) get_post_meta( $product_id, '_sr_view_count', true );
	}

	public static function increment_view_count( $product_id ) {
		$count = self::get_view_count( $product_id ) + 1;
		update_post_meta( $product_id, '_sr_view_count', $count );
		return $count;
	}

	public static function format_views( $count ) {
		$count = (int) $count;
		if ( $count >= 1000000 ) {
			return rtrim( rtrim( number_format( $count / 1000000, 1 ), '0' ), '.' ) . 'M';
		} elseif ( $count >= 1000 ) {
			return rtrim( rtrim( number_format( $count / 1000, 1 ), '0' ), '.' ) . 'K';
		}
		return (string) $count;
	}

	public static function get_discount_percent( $product ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			return (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
		}
		return 0;
	}

	/**
	 * Convert a hex color + opacity percentage (0-100) into an rgba() string.
	 */
	public static function hex_to_rgba( $hex, $opacity_percent = 100 ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) {
			$hex = '000000';
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$a = max( 0, min( 100, (int) $opacity_percent ) ) / 100;

		return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $a . ')';
	}

	/**
	 * Self-heals the common "1010% OFF" mistake, where someone typed a full
	 * example (e.g. "10% Off") into the Badge Suffix field instead of just the
	 * unit text that belongs after the auto-calculated number. Runs both on
	 * save and on every render, so an already-saved bad value corrects itself
	 * immediately without the admin needing to notice and re-save it.
	 */
	public static function clean_badge_suffix( $value ) {
		$value = (string) $value;
		if ( preg_match( '/^\s*\d+\s*(%?)\s*(.*)$/', $value, $m ) ) {
			$percent = $m[1];
			$rest    = trim( $m[2] );
			$value   = trim( $percent . ( $rest !== '' ? ' ' . $rest : '' ) );
		}
		return $value !== '' ? $value : '% Off';
	}

	public static function get_reel_products( $args = array() ) {
		$settings = get_option( 'sr_devsazzad_settings', array() );

		$defaults = array(
			'limit'           => ! empty( $settings['reel_count'] ) ? (int) $settings['reel_count'] : 10,
			'category'        => ! empty( $settings['category'] ) ? $settings['category'] : '',
			'orderby'         => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'only_with_video' => isset( $settings['only_with_video'] ) ? (bool) $settings['only_with_video'] : true,
		);
		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $args['limit'] > 0 ? $args['limit'] : 10,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => $args['orderby'],
			'order'               => 'DESC',
		);

		if ( $args['orderby'] === 'popularity' ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'total_sales';
		} elseif ( $args['orderby'] === 'price' ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = '_price';
		} elseif ( $args['orderby'] === 'rand' ) {
			$query_args['orderby'] = 'rand';
		}

		if ( ! empty( $args['category'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $args['category'] ) ),
				),
			);
		}

		$field = self::get_video_field_name();
		if ( $args['only_with_video'] ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => $field,
					'value'   => '',
					'compare' => '!=',
				),
			);
		}

		$query = new WP_Query( $query_args );
		$items = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				$product = wc_get_product( $post_id );
				if ( ! $product ) continue;

				$video_url = self::get_product_video_url( $post_id );
				$video_id  = self::extract_youtube_id( $video_url );
				$thumb     = $video_id ? self::get_youtube_thumbnail( $video_id ) : get_the_post_thumbnail_url( $post_id, 'medium' );
				if ( ! $thumb ) {
					$thumb = wc_placeholder_img_src( 'medium' );
				}

				$views = self::get_view_count( $post_id );

				$items[] = array(
					'id'              => $post_id,
					'title'           => get_the_title(),
					'permalink'       => get_permalink(),
					'price_html'      => $product->get_price_html(),
					'discount'        => self::get_discount_percent( $product ),
					'video_url'       => $video_url,
					'video_id'        => $video_id,
					'thumbnail'       => $thumb,
					'product_thumb'   => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
					'views'           => $views,
					'views_formatted' => self::format_views( $views ),
				);
			}
			wp_reset_postdata();
		}

		return $items;
	}

	/**
	 * Dashboard summary numbers: how many products have a reel attached, total
	 * views across all reels, how many published products still have no reel,
	 * and which reel is the most-viewed. Cached briefly since it scans all
	 * products with a video meta query.
	 */
	public static function get_dashboard_stats() {
		$cached = wp_cache_get( 'sr_devsazzad_dashboard_stats', 'shopable_reel' );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$field = self::get_video_field_name();

		$with_video = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array( array( 'key' => $field, 'value' => '', 'compare' => '!=' ) ),
		) );

		$total_products = (int) wp_count_posts( 'product' )->publish;
		$total_reels    = count( $with_video );
		$total_views    = 0;
		$top_id         = 0;
		$top_views      = -1;

		foreach ( $with_video as $product_id ) {
			$views = self::get_view_count( $product_id );
			$total_views += $views;
			if ( $views > $top_views ) {
				$top_views = $views;
				$top_id    = $product_id;
			}
		}

		$stats = array(
			'total_reels'        => $total_reels,
			'total_views'        => $total_views,
			'products_missing'   => max( 0, $total_products - $total_reels ),
			'top_product_id'     => $top_id,
			'top_product_title'  => $top_id ? get_the_title( $top_id ) : '',
			'top_product_views'  => $top_id ? $top_views : 0,
		);

		wp_cache_set( 'sr_devsazzad_dashboard_stats', $stats, 'shopable_reel', 5 * MINUTE_IN_SECONDS );

		return $stats;
	}
}
