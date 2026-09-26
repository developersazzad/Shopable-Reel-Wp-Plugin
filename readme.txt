=== Shopable Reel ===
Contributors: developersazzad
Donate link: https://sazzad.wedevspro.com/
Tags: woocommerce, video-reels, shoppable-video, youtube, elementor, product-video, reels-carousel, shoppable-video-commerce
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Turn YouTube reels into shoppable video carousels, a full-screen reel viewer and a floating product-page video for WooCommerce. Free, AJAX-powered, Elementor-ready.

== Description ==

**Shopable Reel** brings TikTok/Instagram-style shoppable video reels to your WooCommerce store.
Paste one YouTube reel link per product — the plugin assembles the whole experience:

* **Reel Carousel** — horizontal scroll-snap row with view-count badge, automatic discount badge (price math, e.g. "-15%"), product thumb and price. Place it anywhere with the shortcode or the Elementor widget.
* **Full-Screen Reel Viewer** — tap a reel and it opens centered in 9:16 while neighbouring reels sit dimmed (40%) and blurred (4px) beside it. Mute, copy-link, prev/next and a "Go" button straight to the product.
* **Floating Product Video** — on the product page a draggable picture-in-picture reel hovers over the gallery image: minimized into a configurable corner, expandable to a centered 9:16 YouTube player. No video attached? Nothing renders.
* **Manage Reels Dashboard** — stats strip (reels added, total views, products without a reel, most-viewed reel) above a connected-reels table. Every action runs over AJAX, no page reloads.
* **Settings Design System** — 20+ merchant-facing controls: query rules, category limit, order, badge suffix, accent/text/arrow colours, card size, scroll speed, viewer dim & blur, mobile reel count, floating-video corners and theme gallery selectors.
* **Elementor Widget** — "Shopable Reel Carousel" exposes every control in the Elementor panel with live canvas preview.
* **Power Shortcode** — `[shopable_reel_devsazzad]` with 15+ optional attributes; omitted attributes fall back to Settings.
* **ACF Auto-Sync** — create a URL field named `reel_video_url` and reels sync automatically, no double entry.
* **View Analytics** — eye-badge counts formatted like social apps (1700 becomes 1.7K), counted once per session on real plays via the YouTube IFrame API.
* **In-Admin Documentation** — the "Shortcode & Help" screen documents every attribute inside your dashboard.

= Why not the paid apps? =

ReelUp lives on Shopify and charges monthly; Ecomm Reels covers WordPress but with limited design and per-product control. Shopable Reel is free, self-owned, and adds an Elementor widget, a floating product-page video, mobile reel-count rules and 20+ design controls that neither alternative offers.

= Performance & security =

* Assets enqueue only on pages that render reels; YouTube iframes are created lazily on first open.
* Autoplay starts muted per browser policy; the carousel is server-rendered for a zero-JS first paint.
* All admin endpoints are nonce-secured and capability-checked; input/output escaped with `esc_*`.
* Storage uses post meta + one options row — no custom tables, upgrade- and backup-safe.

= Developer hooks =

`sr_carousel_args` (filter query args), `sr_reel_card_html` (filter card markup), `sr_viewer_overlays` (filter dim/blur values).

Full interactive case study, live demo & architecture walkthrough: [https://ai.khatifoodbazar.com/wa/portfolio/](https://ai.khatifoodbazar.com/wa/portfolio/)

== Installation ==

1. Upload the `shopable-reel` folder to `/wp-content/plugins/` (or upload the zip via Plugins → Add New).
2. Activate the plugin through the "Plugins" screen.
3. Go to **Shopable Reel → Manage Reels**, pick a product, paste its YouTube reel URL and click **Save Reel**.
4. Place the carousel: paste `[shopable_reel_devsazzad]` in any post/page/HTML block, or drag the **Shopable Reel Carousel** widget in Elementor.
5. Optional: tune colours, sizes, speeds and corners under **Shopable Reel → Settings**.

= Requirements =

* WordPress 5.8 or newer (tested to 7.1)
* PHP 7.4 or newer
* WooCommerce 6.0 or newer
* Elementor / ACF optional (features auto-register when present)

== Frequently Asked Questions ==

= Is Shopable Reel free? =

Yes. Free and self-owned under GPL-2.0+. No subscription, no feature gating.

= Do I need to know code to use it? =

No. Pick product, paste YouTube link, save. The shortcode and Elementor widget both work with zero attributes — Settings defaults apply.

= Will videos slow my store down? =

No. Scripts and styles load only on pages that render reels, iframes are lazy-created on first open, autoplay starts muted, and the carousel first paint is server-rendered HTML.

= Which themes are supported? =

Any WooCommerce theme. The floating video injects into `#main_images_product` (WoodMart default) or falls back to `.woocommerce-product-gallery`; both selectors are editable in Settings.

= How are views counted? =

Once per session per reel, fired by the YouTube player's PLAYING state through an AJAX endpoint — real plays only, no inflate-on-scroll.

= Can I place the floating video manually? =

Yes. Turn off "Show Automatically" in Settings and paste `[shopable_reel_single_video]` in any spot or Elementor slot.

== Screenshots ==

1. Manage Reels dashboard — stats strip and connected reels table
2. Product selector — bind any WooCommerce product to a reel
3. Settings — query rules, colours, card size, player & viewer controls
4. Settings inside WP admin
5. Shortcode & Help — in-admin documentation
6. Elementor — Shopable Reel Carousel widget on the canvas
7. Full-screen reel viewer — active reel with dimmed, blurred neighbours
8. Viewer transport controls — play, prev/next, mute, brand header
9. Floating video minimized over the product gallery
10. Floating video controls — close and expand icons
11. Floating video expanded — centered 9:16 YouTube player
12. Reel carousel on the storefront home page

== Changelog ==

= 1.5.0 =
* New: floating product-page video — draggable PiP, minimize corners, expand to centered 9:16 player
* New: theme gallery selector + fallback class for the floating video
* New: side-reel dim & blur controls, bottom info background toggle
* New: mobile reel-count setting and "Shortcode & Help" admin docs screen
* Improved: view badge K-formatting and per-session de-duplication

= 1.4.0 =
* New: native Elementor widget with full live controls
* New: view analytics — eye badges, stats strip, most-viewed reel
* New: ACF `reel_video_url` auto-sync
* Improved: AJAX admin — save/remove without reloads

= 1.3.0 =
* New: full-screen reel viewer with dimmed + blurred side reels
* New: "Go to product" button with custom label
* Improved: discount badge auto percentage math

= 1.0.0 =
* Initial release: Manage Reels, Settings, `[shopable_reel_devsazzad]` carousel

== Upgrade Notice ==

= 1.5.0 =
Adds the floating product-page video and in-admin documentation. No data migration required — upgrade in place.
