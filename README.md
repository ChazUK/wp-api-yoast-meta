
# Yoast to REST API - WordPress plugin

![Yoast](Yoast_Logo_Small_RGB.png)

For use with the new [WP REST API](http://v2.wp-api.org/)

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
- `yoast_wpseo_facebook_title` aka `yoast_wpseo_opengraph-title`
- `yoast_wpseo_facebook_description` aka `yoast_wpseo_opengraph-description`
- `yoast_wpseo_facebook_type`
- `yoast_wpseo_facebook_image` aka `yoast_wpseo_opengraph-image`
- `yoast_wpseo_twitter_title`
- `yoast_wpseo_twitter_description`
- `yoast_wpseo_twitter_image`
- `yoast_wpseo_social_url`
- `yoast_wpseo_website_name`
- `yoast_wpseo_company_or_person`
- `yoast_wpseo_person_name`
- `yoast_wpseo_company_name`
- `yoast_wpseo_company_logo`
- `yoast_wpseo_website_name`
- `yoast_wpseo_social_defaults`

Currently updating:

- `yoast_wpseo_focuskw`
- `yoast_wpseo_linkdex`
- `yoast_wpseo_metakeywords`
- `yoast_wpseo_meta-robots-noindex`
- `yoast_wpseo_meta-robots-nofollow`
- `yoast_wpseo_meta-robots-adv`
- `yoast_wpseo_canonical`
- `yoast_wpseo_redirect`

`yoast_wpseo_defaults` includes the following:
- `facebook_site`
- `instagram_url`
- `linkedin_url`
- `myspace_url`
- `og_default_image`
- `og_frontpage_title`
- `og_frontpage_desc`
- `og_frontpage_image`
- `opengraph`
- `pinterest_url`
- `pinterestverify`
- `plus-publisher`
- `twitter`
- `twitter_site`,
- `twitter_card_type`
- `youtube_url`
- `google_plus_url`
- `fbadminapp`

Thanks to Pablo Postigo, Tedy Warsitha and Charlie Francis for amazing contributions!
