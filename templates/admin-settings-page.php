<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$settings = wp_parse_args( get_option( 'sr_devsazzad_settings', array() ), Shopable_Reel_Settings::defaults() );
?>
<div class="wrap sr-devsazzad-wrap">
	<h1>Shopable Reel — Settings</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'sr_devsazzad_settings_group' ); ?>
		<div class="sr-admin-card" style="max-width:800px;">
		<table class="form-table">
			<tr>
				<th><label for="acf_field">ACF / Meta Field Name</label></th>
				<td>
					<input type="text" name="sr_devsazzad_settings[acf_field]" id="acf_field" value="<?php echo esc_attr( $settings['acf_field'] ); ?>" class="regular-text">
					<p class="description">The ACF (or custom) field on each product that stores the reel video URL. Default: <code>reel_video_url</code></p>
				</td>
			</tr>
			<tr>
				<th><label for="reel_count">Number of Reels</label></th>
				<td><input type="number" min="1" name="sr_devsazzad_settings[reel_count]" id="reel_count" value="<?php echo esc_attr( $settings['reel_count'] ); ?>"></td>
			</tr>
			<tr>
				<th><label for="category">Limit to Category</label></th>
				<td>
					<input type="text" name="sr_devsazzad_settings[category]" id="category" value="<?php echo esc_attr( $settings['category'] ); ?>" class="regular-text" placeholder="e.g. lawn, bridal">
					<p class="description">Comma separated category slugs. Leave empty to show all categories.</p>
				</td>
			</tr>
			<tr>
				<th><label for="orderby">Order By</label></th>
				<td>
					<select name="sr_devsazzad_settings[orderby]" id="orderby">
						<?php foreach ( array( 'date' => 'Newest', 'popularity' => 'Popularity', 'price' => 'Price', 'title' => 'Title', 'rand' => 'Random' ) as $k => $label ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['orderby'], $k ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="only_with_video">Only Show Products With a Reel</label></th>
				<td><label><input type="checkbox" name="sr_devsazzad_settings[only_with_video]" id="only_with_video" value="1" <?php checked( $settings['only_with_video'], 1 ); ?>> Recommended — hides products without a video</label></td>
			</tr>
			<tr>
				<th><label for="badge_text">Discount Badge Suffix</label></th>
				<td>
					<input type="text" name="sr_devsazzad_settings[badge_text]" id="badge_text" value="<?php echo esc_attr( $settings['badge_text'] ); ?>">
					<p class="description">
						Only enter the label text that comes <strong>after</strong> the number — e.g. <code>% Off</code>.
						The discount percentage is calculated automatically from each product's price, so with this suffix a 20%-off product shows <strong>"20<?php echo esc_html( $settings['badge_text'] ); ?>"</strong>.
						<br><strong>Do not type a number here</strong> (e.g. don't enter <code>10% Off</code>) — that would show as "20<?php echo esc_html( $settings['badge_text'] ); ?>10% Off"-style duplicated text.
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="primary_color">Accent Color</label></th>
				<td><input type="text" class="sr-color-field" name="sr_devsazzad_settings[primary_color]" id="primary_color" value="<?php echo esc_attr( $settings['primary_color'] ); ?>"></td>
			</tr>
			<tr>
				<th>Card Size (px)</th>
				<td>
					Width <input type="number" name="sr_devsazzad_settings[card_width]" value="<?php echo esc_attr( $settings['card_width'] ); ?>" style="width:80px;">
					&nbsp; Height <input type="number" name="sr_devsazzad_settings[card_height]" value="<?php echo esc_attr( $settings['card_height'] ); ?>" style="width:80px;">
					<p class="description">Cards automatically scale down for mobile regardless of this setting.</p>
				</td>
			</tr>
			<tr>
				<th>Reel Player</th>
				<td>
					<label><input type="checkbox" name="sr_devsazzad_settings[autoplay]" value="1" <?php checked( $settings['autoplay'], 1 ); ?>> Autoplay video in the full-screen viewer</label><br>
					<label><input type="checkbox" name="sr_devsazzad_settings[muted]" value="1" <?php checked( $settings['muted'], 1 ); ?>> Start muted (recommended — browsers block unmuted autoplay)</label>
				</td>
			</tr>
		</table>
		</div>

		<div class="sr-admin-card" style="max-width:800px; margin-top:20px;">
			<h2>Carousel &amp; Reel Viewer Appearance</h2>
			<p class="description">Fine-tune the colors and sizes of text, badges, and every button in the carousel and the full-screen reel viewer.</p>
			<table class="form-table">
				<tr>
					<th><label for="text_color">Text Color</label></th>
					<td><input type="text" class="sr-color-field" name="sr_devsazzad_settings[text_color]" id="text_color" value="<?php echo esc_attr( $settings['text_color'] ); ?>">
						<p class="description">Product title, price, and reel-viewer caption text.</p>
					</td>
				</tr>
				<tr>
					<th><label for="badge_text_color">Discount Badge Text Color</label></th>
					<td><input type="text" class="sr-color-field" name="sr_devsazzad_settings[badge_text_color]" id="badge_text_color" value="<?php echo esc_attr( $settings['badge_text_color'] ); ?>"></td>
				</tr>
				<tr>
					<th>Carousel Arrow Buttons</th>
					<td>
						Color <input type="text" class="sr-color-field" name="sr_devsazzad_settings[nav_button_color]" id="nav_button_color" value="<?php echo esc_attr( $settings['nav_button_color'] ); ?>">
						&nbsp; Icon Color <input type="text" class="sr-color-field" name="sr_devsazzad_settings[nav_button_icon_color]" id="nav_button_icon_color" value="<?php echo esc_attr( $settings['nav_button_icon_color'] ); ?>">
						&nbsp; Size (px) <input type="number" name="sr_devsazzad_settings[nav_button_size]" value="<?php echo esc_attr( $settings['nav_button_size'] ); ?>" style="width:70px;">
						<p class="description">The left/right scroll arrows on the carousel.</p>
					</td>
				</tr>
				<tr>
					<th>Reel Viewer Buttons</th>
					<td>
						Color <input type="text" class="sr-color-field" name="sr_devsazzad_settings[modal_button_color]" id="modal_button_color" value="<?php echo esc_attr( $settings['modal_button_color'] ); ?>">
						&nbsp; Icon Color <input type="text" class="sr-color-field" name="sr_devsazzad_settings[modal_button_icon_color]" id="modal_button_icon_color" value="<?php echo esc_attr( $settings['modal_button_icon_color'] ); ?>">
						&nbsp; Size (px) <input type="number" name="sr_devsazzad_settings[modal_button_size]" value="<?php echo esc_attr( $settings['modal_button_size'] ); ?>" style="width:70px;">
						<p class="description">Close, previous/next, mute, and share buttons inside the full-screen reel viewer.</p>
					</td>
				</tr>
			</table>
		</div>

		<div class="sr-admin-card" style="max-width:800px; margin-top:20px;">
			<h2>Carousel Behavior</h2>
			<table class="form-table">
				<tr>
					<th><label for="reel_count_mobile">Reel Count on Mobile</label></th>
					<td>
						<input type="number" min="0" name="sr_devsazzad_settings[reel_count_mobile]" id="reel_count_mobile" value="<?php echo esc_attr( $settings['reel_count_mobile'] ); ?>" style="width:80px;">
						<p class="description">How many reels show on mobile screens (≤600px). Leave as <code>0</code> to show the same number as desktop.</p>
					</td>
				</tr>
				<tr>
					<th><label for="scroll_speed">Scroll Animation Speed</label></th>
					<td>
						<input type="number" min="100" max="2000" step="50" name="sr_devsazzad_settings[scroll_speed]" id="scroll_speed" value="<?php echo esc_attr( $settings['scroll_speed'] ); ?>" style="width:90px;"> ms
						<p class="description">How long the smooth-scroll animation takes when the arrows are clicked or the carousel is dragged. Lower = faster.</p>
					</td>
				</tr>
				<tr>
					<th><label for="card_hover_expand_enabled">Hover Expand Icon</label></th>
					<td><label><input type="checkbox" name="sr_devsazzad_settings[card_hover_expand_enabled]" id="card_hover_expand_enabled" value="1" <?php checked( $settings['card_hover_expand_enabled'], 1 ); ?>> Show a blurred expand button in the center of a reel card on hover</label></td>
				</tr>
				<tr>
					<th><label for="show_go_button">"Go to Product" Button</label></th>
					<td>
						<label><input type="checkbox" name="sr_devsazzad_settings[show_go_button]" id="show_go_button" value="1" <?php checked( $settings['show_go_button'], 1 ); ?>> Show a button on each card (and in the reel viewer) that links straight to the product</label><br>
						Button text <input type="text" name="sr_devsazzad_settings[go_button_text]" value="<?php echo esc_attr( $settings['go_button_text'] ); ?>" style="width:100px;">
					</td>
				</tr>
			</table>
		</div>

		<div class="sr-admin-card" style="max-width:800px; margin-top:20px;">
			<h2>Reel Viewer Extras</h2>
			<table class="form-table">
				<tr>
					<th><label for="side_reel_overlay_opacity">Side Reels — Dark Overlay</label></th>
					<td>
						<input type="number" min="0" max="100" name="sr_devsazzad_settings[side_reel_overlay_opacity]" id="side_reel_overlay_opacity" value="<?php echo esc_attr( $settings['side_reel_overlay_opacity'] ); ?>" style="width:80px;">%
						<p class="description">Darkness of the overlay on the dimmed reels beside the active one in the full-screen viewer.</p>
					</td>
				</tr>
				<tr>
					<th><label for="side_reel_blur">Side Reels — Blur</label></th>
					<td>
						<input type="number" min="0" max="20" name="sr_devsazzad_settings[side_reel_blur]" id="side_reel_blur" value="<?php echo esc_attr( $settings['side_reel_blur'] ); ?>" style="width:80px;">px
					</td>
				</tr>
				<tr>
					<th><label for="modal_bottom_overlay_visible">Bottom Info Background</label></th>
					<td><label><input type="checkbox" name="sr_devsazzad_settings[modal_bottom_overlay_visible]" id="modal_bottom_overlay_visible" value="1" <?php checked( $settings['modal_bottom_overlay_visible'], 1 ); ?>> Show the dark gradient behind the product name/price at the bottom of the viewer (turn off for plain text directly over the video)</label></td>
				</tr>
			</table>
		</div>

		<div class="sr-admin-card" style="max-width:800px; margin-top:20px;">
			<h2>Single-Product Floating Video</h2>
			<p class="description">Automatically shows on any product's page once that product has a reel video connected in <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad' ) ); ?>">Manage Reels</a> — no shortcode needed. The <code>[shopable_reel_single_video]</code> shortcode is still available if you want manual placement instead (e.g. via Elementor).</p>
			<table class="form-table">
				<tr>
					<th><label for="auto_floating_video">Show Automatically</label></th>
					<td>
						<label><input type="checkbox" name="sr_devsazzad_settings[auto_floating_video]" id="auto_floating_video" value="1" <?php checked( $settings['auto_floating_video'], 1 ); ?>> Automatically add the floating video to every product page that has a reel connected</label>
						<p class="description">Turn this off if you'd rather place the <code>[shopable_reel_single_video]</code> shortcode manually (e.g. in a specific spot via Elementor) instead.</p>
					</td>
				</tr>
				<tr>
					<th><label for="floating_gallery_selector">Product Gallery Selector</label></th>
					<td>
						<input type="text" name="sr_devsazzad_settings[floating_gallery_selector]" id="floating_gallery_selector" value="<?php echo esc_attr( $settings['floating_gallery_selector'] ); ?>" class="regular-text">
						<p class="description">CSS selector (ID or class) of the element that wraps your main product image. The floating video is placed inside this element so it scrolls with the gallery. Default matches WoodMart's gallery wrapper: <code>#main_images_product</code></p>
					</td>
				</tr>
				<tr>
					<th><label for="floating_fallback_selector">Fallback Selector</label></th>
					<td>
						<input type="text" name="sr_devsazzad_settings[floating_fallback_selector]" id="floating_fallback_selector" value="<?php echo esc_attr( $settings['floating_fallback_selector'] ); ?>" class="regular-text">
						<p class="description">Used if the selector above isn't found on the page (e.g. default WooCommerce gallery <code>.woocommerce-product-gallery</code>). If neither is found, the video floats fixed on the screen instead.</p>
					</td>
				</tr>
				<tr>
					<th><label for="floating_position">Minimized Corner Position</label></th>
					<td>
						<select name="sr_devsazzad_settings[floating_position]" id="floating_position">
							<?php foreach ( array( 'bottom-right' => 'Bottom Right', 'bottom-left' => 'Bottom Left', 'top-right' => 'Top Right', 'top-left' => 'Top Left' ) as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['floating_position'], $k ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">Where the small minimized video sits within the product gallery.</p>
					</td>
				</tr>
				<tr>
					<th>Minimized Width (px)</th>
					<td>
						Desktop <input type="number" name="sr_devsazzad_settings[floating_desktop_width]" value="<?php echo esc_attr( $settings['floating_desktop_width'] ); ?>" style="width:80px;">
						&nbsp; Mobile <input type="number" name="sr_devsazzad_settings[floating_mobile_width]" value="<?php echo esc_attr( $settings['floating_mobile_width'] ); ?>" style="width:80px;">
						<p class="description">Video keeps a 9:16 ratio, so height is calculated automatically.</p>
					</td>
				</tr>
				<tr>
					<th>Autoplay</th>
					<td><label><input type="checkbox" name="sr_devsazzad_settings[floating_autoplay]" value="1" <?php checked( $settings['floating_autoplay'], 1 ); ?>> Autoplay the floating video (muted) when the page loads</label></td>
				</tr>
				<tr>
					<th><label for="floating_expand_position">Expand Icon Corner</label></th>
					<td>
						<select name="sr_devsazzad_settings[floating_expand_position]" id="floating_expand_position">
							<?php foreach ( array( 'top-left' => 'Top Left', 'top-right' => 'Top Right', 'bottom-left' => 'Bottom Left', 'bottom-right' => 'Bottom Right' ) as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['floating_expand_position'], $k ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">Where the expand (⤢) icon sits on the video.</p>
					</td>
				</tr>
				<tr>
					<th><label for="floating_close_position">Close Icon Corner</label></th>
					<td>
						<select name="sr_devsazzad_settings[floating_close_position]" id="floating_close_position">
							<?php foreach ( array( 'top-left' => 'Top Left', 'top-right' => 'Top Right', 'bottom-left' => 'Bottom Left', 'bottom-right' => 'Bottom Right' ) as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['floating_close_position'], $k ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">Where the close (✕) icon sits on the video. Set this to a different corner than the expand icon to avoid overlap.</p>
					</td>
				</tr>
				<tr>
					<th>Expand/Close Icon Style</th>
					<td>
						Background <input type="text" class="sr-color-field" name="sr_devsazzad_settings[floating_icon_bg_color]" value="<?php echo esc_attr( $settings['floating_icon_bg_color'] ); ?>">
						&nbsp; Icon Color <input type="text" class="sr-color-field" name="sr_devsazzad_settings[floating_icon_color]" value="<?php echo esc_attr( $settings['floating_icon_color'] ); ?>">
						&nbsp; Size (px) <input type="number" name="sr_devsazzad_settings[floating_icon_size]" value="<?php echo esc_attr( $settings['floating_icon_size'] ); ?>" style="width:70px;">
						<p class="description">Increase the size or use a higher-contrast icon color if the buttons are hard to spot on top of your video.</p>
					</td>
				</tr>
				<tr>
					<th><label for="floating_icon_style">Icon Background Style</label></th>
					<td>
						<select name="sr_devsazzad_settings[floating_icon_style]" id="floating_icon_style">
							<option value="minimal" <?php selected( $settings['floating_icon_style'], 'minimal' ); ?>>Minimal — icon only, no circle</option>
							<option value="circle" <?php selected( $settings['floating_icon_style'], 'circle' ); ?>>Circle background behind the icon</option>
						</select>
						<p class="description">Minimal removes the round button background entirely, showing just the plain icon glyph.</p>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Save Settings' ); ?>
		</div>
	</form>
</div>
