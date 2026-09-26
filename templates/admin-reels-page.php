<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap sr-devsazzad-wrap">
	<h1>Shopable Reel <span class="sr-version">v<?php echo esc_html( SR_DEVSAZZAD_VERSION ); ?></span></h1>
	<p class="description">Manage which products appear in your shoppable reels carousel.</p>

	<?php if ( isset( $_GET['sr_msg'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p>
			<?php echo $_GET['sr_msg'] === 'saved' ? 'Reel saved successfully.' : 'Reel removed successfully.'; ?>
		</p></div>
	<?php endif; ?>

	<?php $stats = Shopable_Reel_Helpers::get_dashboard_stats(); ?>
	<div class="sr-glass-dash">
		<div class="sr-glass-card">
			<div class="sr-glass-icon">🎬</div>
			<div class="sr-glass-number"><?php echo esc_html( number_format_i18n( $stats['total_reels'] ) ); ?></div>
			<div class="sr-glass-label">Reels Added</div>
		</div>
		<div class="sr-glass-card">
			<div class="sr-glass-icon">👁</div>
			<div class="sr-glass-number"><?php echo esc_html( Shopable_Reel_Helpers::format_views( $stats['total_views'] ) ); ?></div>
			<div class="sr-glass-label">Total Views</div>
		</div>
		<div class="sr-glass-card">
			<div class="sr-glass-icon">📦</div>
			<div class="sr-glass-number"><?php echo esc_html( number_format_i18n( $stats['products_missing'] ) ); ?></div>
			<div class="sr-glass-label">Products Without a Reel</div>
		</div>
		<div class="sr-glass-card sr-glass-card-wide">
			<div class="sr-glass-icon">🏆</div>
			<div class="sr-glass-number" style="font-size:16px; line-height:1.4;">
				<?php if ( $stats['top_product_id'] ) : ?>
					<a href="<?php echo esc_url( get_edit_post_link( $stats['top_product_id'] ) ); ?>"><?php echo esc_html( $stats['top_product_title'] ); ?></a>
					<div class="sr-glass-sublabel"><?php echo esc_html( Shopable_Reel_Helpers::format_views( $stats['top_product_views'] ) ); ?> views</div>
				<?php else : ?>
					—
				<?php endif; ?>
			</div>
			<div class="sr-glass-label">Most Viewed Reel</div>
		</div>
	</div>

	<div class="sr-admin-grid">
		<div class="sr-admin-card">
			<h2>Add / Update Reel Video</h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'sr_save_reel_video_nonce' ); ?>
				<input type="hidden" name="action" value="sr_save_reel_video">
				<table class="form-table">
					<tr>
						<th><label for="sr_product_id">Product</label></th>
						<td>
							<select name="product_id" id="sr_product_id" required style="min-width:320px;">
								<option value="">— Select a product —</option>
								<?php
								$products = get_posts( array(
									'post_type'      => 'product',
									'posts_per_page' => -1,
									'post_status'    => 'publish',
									'orderby'        => 'title',
									'order'          => 'ASC',
								) );
								foreach ( $products as $p ) : ?>
									<option value="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?> (#<?php echo (int) $p->ID; ?>)</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="sr_video_url">Reel Video URL (YouTube)</label></th>
						<td><input type="url" id="sr_video_url" name="video_url" class="regular-text" placeholder="https://www.youtube.com/watch?v=..."></td>
					</tr>
					<tr>
						<th><label for="sr_view_count">View Count (optional)</label></th>
						<td>
							<input type="number" id="sr_view_count" name="view_count" min="0" value="0">
							<p class="description">Shown as the eye-icon count on the reel card (e.g. 1700 displays as 1.7K).</p>
						</td>
					</tr>
				</table>
				<?php submit_button( 'Save Reel' ); ?>
			</form>
			<p class="description">Tip: if you use Advanced Custom Fields, create a URL field with the same name as set in <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad-settings' ) ); ?>">Settings</a> and it will sync automatically — no need to enter it twice.</p>
		</div>

		<div class="sr-admin-card">
			<h2>Connected Reels</h2>
			<?php
			$field = Shopable_Reel_Helpers::get_video_field_name();
			$all   = get_posts( array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
				'meta_query'     => array( array( 'key' => $field, 'value' => '', 'compare' => '!=' ) ),
			) );
			?>
			<?php if ( empty( $all ) ) : ?>
				<p>No products have a reel video yet. Add one using the form on the left.</p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr><th></th><th>Product</th><th>Video URL</th><th>Views</th><th>Actions</th></tr>
					</thead>
					<tbody>
					<?php foreach ( $all as $p ) :
						$video_url = get_post_meta( $p->ID, $field, true );
						$video_id  = Shopable_Reel_Helpers::extract_youtube_id( $video_url );
						$thumb     = $video_id ? Shopable_Reel_Helpers::get_youtube_thumbnail( $video_id ) : get_the_post_thumbnail_url( $p->ID, 'thumbnail' );
						$views     = Shopable_Reel_Helpers::get_view_count( $p->ID );
					?>
						<tr>
							<td><img src="<?php echo esc_url( $thumb ); ?>" style="width:46px;height:64px;object-fit:cover;border-radius:6px;" alt=""></td>
							<td><a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a></td>
							<td><a href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_trim_words( $video_url, 6, '...' ) ); ?></a></td>
							<td><?php echo esc_html( Shopable_Reel_Helpers::format_views( $views ) ); ?></td>
							<td>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;" onsubmit="return confirm('Remove this reel from the product?');">
									<?php wp_nonce_field( 'sr_delete_reel_video_nonce' ); ?>
									<input type="hidden" name="action" value="sr_delete_reel_video">
									<input type="hidden" name="product_id" value="<?php echo (int) $p->ID; ?>">
									<button type="submit" class="button button-small button-link-delete">Remove</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
