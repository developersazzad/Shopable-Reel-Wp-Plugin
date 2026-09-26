<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @var int    $product_id
 * @var string $video_id
 * @var array  $settings
 */

$embed_params = array(
	'autoplay'       => ! empty( $settings['floating_autoplay'] ) ? 1 : 0,
	'mute'           => 1, // Always start muted while minimized — required by browsers for autoplay.
	'loop'           => 1,
	'playlist'       => $video_id,
	'controls'       => 1,
	'playsinline'    => 1,
	'rel'            => 0,
	'modestbranding' => 1,
);
$embed_src = 'https://www.youtube.com/embed/' . $video_id . '?' . http_build_query( $embed_params );
$uid       = 'sr-floating-' . wp_unique_id();
?>
<div class="sr-floating-reel sr-floating-style-<?php echo esc_attr( $settings['floating_icon_style'] ); ?>" id="<?php echo esc_attr( $uid ); ?>"
	style="--sr-floating-desktop-w: <?php echo esc_attr( $settings['floating_desktop_width'] ); ?>px;
	       --sr-floating-mobile-w: <?php echo esc_attr( $settings['floating_mobile_width'] ); ?>px;
	       --sr-floating-icon-bg: <?php echo esc_attr( $settings['floating_icon_bg_color'] ); ?>;
	       --sr-floating-icon-color: <?php echo esc_attr( $settings['floating_icon_color'] ); ?>;
	       --sr-floating-icon-size: <?php echo esc_attr( $settings['floating_icon_size'] ); ?>px;"
	data-gallery-selector="<?php echo esc_attr( $settings['floating_gallery_selector'] ); ?>"
	data-fallback-selector="<?php echo esc_attr( $settings['floating_fallback_selector'] ); ?>"
	data-position="<?php echo esc_attr( $settings['floating_position'] ); ?>">

	<div class="sr-floating-reel-media">
		<iframe src="<?php echo esc_url( $embed_src ); ?>" allow="autoplay; encrypted-media" allowfullscreen title="Product reel video"></iframe>
	</div>

	<div class="sr-floating-drag-overlay"></div>

	<!-- Plain text glyphs, not SVG or icon fonts — themes that globally reset
	     SVG stroke/fill styles (a common issue on some page builders) cannot
	     hide these, since they are just characters. -->
	<button type="button" class="sr-floating-btn sr-floating-expand sr-pos-<?php echo esc_attr( $settings['floating_expand_position'] ); ?>" aria-label="Expand video">
		<span class="sr-floating-icon-glyph" aria-hidden="true">⤢</span>
	</button>

	<button type="button" class="sr-floating-btn sr-floating-close sr-pos-<?php echo esc_attr( $settings['floating_close_position'] ); ?>" aria-label="Close video">
		<span class="sr-floating-icon-glyph" aria-hidden="true">&times;</span>
	</button>
</div>
