<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap sr-devsazzad-wrap">
	<h1>Shopable Reel — Shortcode &amp; Help</h1>
	<div class="sr-admin-card" style="max-width:800px;">
		<h2>Reel Carousel <span style="font-weight:400;color:#777;font-size:13px;">(multiple products — e.g. your home page)</span></h2>
		<p>Paste this anywhere — a post, page, or an HTML/WPCode block. This is the horizontal-scrolling carousel of multiple products, each opening into the full-screen reel viewer when clicked:</p>
		<pre>[shopable_reel_devsazzad]</pre>
		<p>Optional attributes — every one is optional and falls back to the global <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad-settings' ) ); ?>">Settings page</a> value if omitted:</p>
		<pre>[shopable_reel_devsazzad limit="8" limit_mobile="4" category="lawn,bridal" orderby="popularity" scroll_speed="400" show_go_button="yes" go_button_text="Shop Now" text_color="#ffffff" accent_color="#e74c3c" arrow_size="40"]</pre>
		<table class="widefat" style="max-width:700px;">
			<thead><tr><th>Attribute</th><th>Description</th></tr></thead>
			<tbody>
				<tr><td><code>limit</code></td><td>Number of reels to display on desktop.</td></tr>
				<tr><td><code>limit_mobile</code></td><td>Number of reels to display on mobile (≤600px). Omit to match desktop.</td></tr>
				<tr><td><code>category</code></td><td>Comma separated product category slugs.</td></tr>
				<tr><td><code>orderby</code></td><td>date, popularity, price, title, or rand.</td></tr>
				<tr><td><code>scroll_speed</code></td><td>Smooth-scroll animation duration in milliseconds.</td></tr>
				<tr><td><code>show_go_button</code></td><td><code>yes</code> or <code>no</code> — a "Go to Product" button on each card and in the viewer.</td></tr>
				<tr><td><code>go_button_text</code></td><td>Text for that button, e.g. "Shop Now".</td></tr>
				<tr><td><code>text_color</code>, <code>accent_color</code>, <code>badge_text_color</code></td><td>Hex colors for text, badge background, and badge text.</td></tr>
				<tr><td><code>arrow_bg_color</code>, <code>arrow_icon_color</code>, <code>arrow_size</code></td><td>Nav arrow styling.</td></tr>
				<tr><td><code>card_width</code>, <code>card_height</code></td><td>Card size in pixels for this instance only.</td></tr>
			</tbody>
		</table>

		<h2>Elementor</h2>
		<p>Search for <strong>"Shopable Reel Carousel"</strong> in the Elementor widget panel (under the "Shopable Reel" category) and drag it onto your page. The widget's Content and Style tabs expose every attribute above — desktop/mobile reel counts, scroll speed, the Go button, and all colors/sizes — with no shortcode typing needed.</p>

		<h2>Adding Reel Videos to Products</h2>
		<p>Go to <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad' ) ); ?>">Shopable Reel → Manage Reels</a> and attach a YouTube video URL to any product directly — no extra plugin required.</p>
		<p>Already using Advanced Custom Fields? Create a URL field with the same field name set on the Settings page (default <code>reel_video_url</code>) and it will be picked up automatically.</p>
	</div>

	<div class="sr-admin-card" style="max-width:800px; margin-top:20px;">
		<h2>Single-Product Floating Reel Video <span style="font-weight:400;color:#777;font-size:13px;">(separate feature)</span></h2>
		<p><strong>Works automatically — no shortcode needed.</strong> As soon as a product has a video connected in <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad' ) ); ?>">Manage Reels</a>, a small draggable, expandable video appears floating on top of the main product image on that product's page. If a product has no video, nothing is added — completely safe to leave on for every product.</p>
		<p>Prefer to control the exact placement yourself (e.g. via a specific spot in Elementor)? Turn off "Show Automatically" on the <a href="<?php echo esc_url( admin_url( 'admin.php?page=shopable-reel-devsazzad-settings' ) ); ?>">Settings page</a> and paste this shortcode instead:</p>
		<pre>[shopable_reel_single_video]</pre>
		<p>How it behaves:</p>
		<ul style="list-style:disc;padding-left:20px;">
			<li>Automatically uses the video already attached to that product from <strong>Manage Reels</strong> — no ACF setup needed.</li>
			<li>If the product has no reel video, nothing is output.</li>
			<li>It maps itself inside your product gallery element (so it scrolls with the image), starts muted and minimized in a corner, and can be dragged anywhere.</li>
			<li>Clicking the expand icon opens it centered on screen at 9:16.</li>
			<li>The expand/close icons are plain glyphs (not SVG or an icon font), so no theme can ever hide them by resetting SVG styles.</li>
			<li>Gallery selector, corner position for each icon independently, size, icon style (minimal or circle background), and autoplay are all configurable on the Settings page under "Single-Product Floating Video".</li>
		</ul>
		<p class="description">Tip: if your theme's gallery wrapper has a different ID than <code>#main_images_product</code> (WoodMart's default), update the "Product Gallery Selector" setting to match your theme, or rely on the fallback field for the default WooCommerce gallery class.</p>
	</div>
</div>
