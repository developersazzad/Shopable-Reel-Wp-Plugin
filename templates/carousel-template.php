<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( empty( $items ) ) {
	echo '<div class="sr-devsazzad-empty">No reels available yet.</div>';
	return;
}

// $settings arrives pre-built (global settings + any shortcode/Elementor overrides
// already merged and sanitized) from Shopable_Reel_Shortcode::render().
if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = wp_parse_args( get_option( 'sr_devsazzad_settings', array() ), Shopable_Reel_Settings::defaults() );
	$settings['badge_text'] = Shopable_Reel_Helpers::clean_badge_suffix( $settings['badge_text'] );
}

$uid = 'sr-reel-' . wp_unique_id();

$data_json = wp_json_encode( array_map( function ( $item ) {
	return array(
		'id'            => $item['id'],
		'title'         => $item['title'],
		'permalink'     => $item['permalink'],
		'price_html'    => $item['price_html'],
		'discount'      => $item['discount'],
		'video_id'      => $item['video_id'],
		'video_url'     => $item['video_url'],
		'thumbnail'     => $item['thumbnail'],
		'product_thumb' => $item['product_thumb'],
		'views'         => $item['views'],
		'views_formatted' => $item['views_formatted'],
	);
}, $items ) );

$mobile_limit    = (int) $settings['reel_count_mobile'];
$show_go_button  = ! empty( $settings['show_go_button'] );
$go_button_text  = $settings['go_button_text'];
$hover_expand_on = ! empty( $settings['card_hover_expand_enabled'] );
?>
<?php if ( $mobile_limit > 0 && $mobile_limit < count( $items ) ) : ?>
<style>
@media (max-width: 600px) {
	#<?php echo esc_attr( $uid ); ?> .sr-reel-card:nth-child(n+<?php echo (int) ( $mobile_limit + 1 ); ?>) { display: none; }
}
</style>
<?php endif; ?>
<div class="sr-devsazzad-wrapper" id="<?php echo esc_attr( $uid ); ?>"
	style="--sr-accent: <?php echo esc_attr( $settings['primary_color'] ); ?>;
	       --sr-card-w: <?php echo esc_attr( $settings['card_width'] ); ?>px;
	       --sr-card-h: <?php echo esc_attr( $settings['card_height'] ); ?>px;
	       --sr-text-color: <?php echo esc_attr( $settings['text_color'] ); ?>;
	       --sr-badge-text-color: <?php echo esc_attr( $settings['badge_text_color'] ); ?>;
	       --sr-nav-bg: <?php echo esc_attr( $settings['nav_button_color'] ); ?>;
	       --sr-nav-icon: <?php echo esc_attr( $settings['nav_button_icon_color'] ); ?>;
	       --sr-nav-size: <?php echo esc_attr( $settings['nav_button_size'] ); ?>px;
	       --sr-modal-btn-bg: <?php echo esc_attr( $settings['modal_button_color'] ); ?>;
	       --sr-modal-btn-icon: <?php echo esc_attr( $settings['modal_button_icon_color'] ); ?>;
	       --sr-modal-btn-size: <?php echo esc_attr( $settings['modal_button_size'] ); ?>px;
	       --sr-side-overlay: <?php echo esc_attr( Shopable_Reel_Helpers::hex_to_rgba( '#000000', $settings['side_reel_overlay_opacity'] ) ); ?>;
	       --sr-side-blur: <?php echo esc_attr( $settings['side_reel_blur'] ); ?>px;"
	data-reels="<?php echo esc_attr( $data_json ); ?>"
	data-autoplay="<?php echo esc_attr( $settings['autoplay'] ); ?>"
	data-muted="<?php echo esc_attr( $settings['muted'] ); ?>"
	data-badge-suffix="<?php echo esc_attr( $settings['badge_text'] ); ?>"
	data-scroll-speed="<?php echo esc_attr( $settings['scroll_speed'] ); ?>"
	data-show-go-button="<?php echo esc_attr( $show_go_button ? '1' : '0' ); ?>"
	data-go-button-text="<?php echo esc_attr( $go_button_text ); ?>"
	data-modal-bottom-overlay="<?php echo esc_attr( ! empty( $settings['modal_bottom_overlay_visible'] ) ? '1' : '0' ); ?>">

	<button type="button" class="sr-nav sr-nav-prev" aria-label="Scroll left">&#10094;</button>

	<div class="sr-reels-track">
		<?php foreach ( $items as $i => $item ) : ?>
		<div class="sr-reel-card" data-index="<?php echo (int) $i; ?>" tabindex="0" role="button" aria-label="Open reel: <?php echo esc_attr( $item['title'] ); ?>">
			<div class="sr-reel-media">
				<img src="<?php echo esc_url( $item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy">
			</div>
			<?php if ( $item['views'] > 0 ) : ?>
			<div class="sr-views-badge">
				<svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="2"/></svg>
				<?php echo esc_html( $item['views_formatted'] ); ?>
			</div>
			<?php endif; ?>
			<?php if ( $hover_expand_on ) : ?>
			<button type="button" class="sr-card-expand-btn" aria-label="Open reel: <?php echo esc_attr( $item['title'] ); ?>" tabindex="-1">
				<span class="sr-card-expand-glyph" aria-hidden="true">⤢</span>
			</button>
			<?php endif; ?>
			<div class="sr-reel-info">
				<div class="sr-reel-info-row">
					<img class="sr-reel-info-avatar" src="<?php echo esc_url( $item['product_thumb'] ?: $item['thumbnail'] ); ?>" alt="">
					<h4><?php echo esc_html( $item['title'] ); ?></h4>
				</div>
				<p>
					<?php echo wp_kses_post( $item['price_html'] ); ?>
					<?php if ( $item['discount'] > 0 ) : ?>
						<span class="sr-off-badge"><?php echo (int) $item['discount']; ?><?php echo esc_html( $settings['badge_text'] ); ?></span>
					<?php endif; ?>
				</p>
				<?php if ( $show_go_button ) : ?>
					<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="sr-card-go-btn" aria-label="<?php echo esc_attr( $go_button_text ); ?>" title="<?php echo esc_attr( $go_button_text ); ?>" onclick="event.stopPropagation();">
						<svg class="sr-card-go-glyph" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>

	<button type="button" class="sr-nav sr-nav-next" aria-label="Scroll right">&#10095;</button>
</div>

<div class="sr-devsazzad-modal" id="<?php echo esc_attr( $uid ); ?>-modal" aria-hidden="true">
	<button type="button" class="sr-modal-close" aria-label="Close">&times;</button>
	<button type="button" class="sr-modal-nav sr-modal-prev" aria-label="Previous reel">&#10094;</button>
	<div class="sr-modal-stage"></div>
	<button type="button" class="sr-modal-nav sr-modal-next" aria-label="Next reel">&#10095;</button>
</div>

