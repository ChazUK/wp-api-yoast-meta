=== Yoast to REST API ===
Contributors: Niels Garve, Pablo Postigo, Tedy Warsitha, Charlie Francis
Tags: yoast, wp-api, rest, seo
Requires at least: 4.4
Tested up to: 4.9.1
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

= 1.4.0 =

Bugfix: now resetting WPSEO_Frontend Singleton everytime before calculations are made

= 1.4.0-alpha =

Fixed retrieval of meta description
Generalized/Removed Worona PWA dependencies

= 1.3 =

Adapted to the needs of Worona PWA.

= 1.2 =

Changed `register_api_field` to `register_rest_field` as it's depracated
Changed the metadata name to `yoast` rather than `yoast_meta`
Removed the `yoast_wpseo_` prefix from the returned meta as it seems undeed

= 1.1 =

Using Class instead of plain function
Added output to public custom post types

= 1.0 =

Launched!
