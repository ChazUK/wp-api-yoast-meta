=== WP API Yoast SEO ===
Contributors: ChazUK
Tags: yoast, wp-api, rest, seo
Requires at least: 4.4
Tested up to: 4.5.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Returns Yoast post or page metadata in a normal post or page request.

== Description ==

Returns Yoast post or page metadata in a normal post or page request. Stores the metadata in the yoast_meta field of the returned data.

== Installation ==

Upload the plugin files to the /wp-content/plugins/wp-api-yoast-meta directory, or install the plugin through the WordPress plugins screen directly.
Activate the plugin through the 'Plugins' screen in WordPress
...
Profit
== Changelog ==

= 1.2 =

Changed `register_api_field` to `register_rest_field` as it's depracated
Changed the metadata name to `yoast` rather than `yoast_meta`
Removed the `yoast_wpseo_` prefix from the returned meta as it seems undeed

= 1.1 =

Using Class instead of plain function
Added output to public custom post types

= 1.0 =

Launched!
