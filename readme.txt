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

Returns Yoast post or page metadata in a normal post or page request. Stores the metadata in the `yoast_meta` field of the returned data.

```
{
  id: 123,
  ...
  yoast_meta: {
    yoast_wpseo_title: "Testy Test | My WordPress site",
    yoast_wpseo_metadesc: "My description",
    yoast_wpseo_canonical: "http://my-wordpress-site.test/testy-test"
  }
}
```

Supports pages, posts, categories, tags and any *public* custom post types

Currently fetching:

- `yoast_wpseo_title`
- `yoast_wpseo_metadesc`
- `yoast_wpseo_canonical`

Currently updating:

- `yoast_wpseo_focuskw`
- `yoast_wpseo_title`
- `yoast_wpseo_metadesc`
- `yoast_wpseo_linkdex`
- `yoast_wpseo_metakeywords`
- `yoast_wpseo_meta-robots-noindex`
- `yoast_wpseo_meta-robots-nofollow`
- `yoast_wpseo_meta-robots-adv`
- `yoast_wpseo_canonical`
- `yoast_wpseo_redirect`
- `yoast_wpseo_opengraph-title`
- `yoast_wpseo_opengraph-description`
- `yoast_wpseo_opengraph-image`
- `yoast_wpseo_twitter-title`
- `yoast_wpseo_twitter-description`
- `yoast_wpseo_twitter-image`

Thanks to Pablo Postigo, Tedy Warsitha and Charlie Francis for amazing contributions!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/yoast-to-rest-api` directory, or install the plugin through the 'Plugins' menu in WordPress
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.4.1 =

- Documentation

= 1.4.0 =

- Fixed broken meta descriptions in REST collections
- PHP Coding Standards (https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#php)

= 1.4.0-alpha =

- Fixed retrieval of the meta description
- Generalized Worona PWA dependencies

= 1.3 =

- Adapted to the needs of Worona PWA

= 1.2 =

- Changed `register_api_field` to `register_rest_field` as it's depracated
- Changed the metadata name to `yoast` rather than `yoast_meta`
- Removed the `yoast_wpseo_` prefix from the returned meta as it seems undeed

= 1.1 =

- Using Class instead of plain function
- Added output to public custom post types

= 1.0 =

- Launched!
