<?php
/**
 * Plugin Name: WP REST API Yoast SEO
 * Description: Adds Yoast fields to page and post metadata to WP REST API responses
 * Author: Charlie Francis
 * Author URI: https://github.com/ChazUK
 * Version: 1.2.0
 * Plugin URI: https://github.com/ChazUK/wp-api-yoast-seo
 */

class WPAPIYoastMeta {

  function __construct() {
    add_action('rest_api_init', array($this, 'add_yoast_data'));
	}

  function add_yoast_data() {
	   // Posts
		register_rest_field( 'post',
	        'yoast',
	        array(
	            'get_callback'    => array( $this, 'wp_api_encode_yoast_post' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

		// Pages
		register_rest_field( 'page',
	        'yoast',
	        array(
	            'get_callback'    => array( $this, 'wp_api_encode_yoast_post' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

      // Category
  		register_rest_field( 'category',
  	        'yoast',
  	        array(
  	            'get_callback'    => array( $this, 'wp_api_encode_yoast_taxonomy' ),
  	            'update_callback' => null,
  	            'schema'          => null,
  	        )
  	    );

        // Tag
        register_rest_field( 'post_tag',
              'yoast',
              array(
                  'get_callback'    => array( $this, 'wp_api_encode_yoast_taxonomy' ),
                  'update_callback' => null,
                  'schema'          => null,
              )
          );

		// Public custom post types
		$types = get_post_types(array(
			'public' => true,
			'_builtin' => false
		));
		foreach($types as $key => $type) {
			register_rest_field( $type,
		        'yoast',
		        array(
		            'get_callback'    => array( $this, 'wp_api_encode_yoast_post' ),
		            'update_callback' => null,
		            'schema'          => null,
		        )
		    );
		}
	}

  function wp_api_encode_yoast_taxonomy() {
    include __DIR__ . '/classes/class-frontend.php';

    $wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();

    $yoastMeta = array(
      'metas' => array(
        'title' => $wpseo_frontend->get_taxonomy_title()
      ),
      'title' => $wpseo_frontend->title(wp_get_document_title()),
      'canonical' => $wpseo_frontend->canonical(false),
      'metatags' => array(
          'description' => $wpseo_frontend->metadesc(false),
          'keywords' => $wpseo_frontend->metakeywords(),
          'robots' => $wpseo_frontend->robots(),
      )
    );

    return (array) $yoastMeta;
  }

  function wp_api_encode_yoast_post($post, $field_name, $request) {

    include __DIR__ . '/classes/class-frontend.php';
    include __DIR__ . '/classes/class-opengraph.php';
    include __DIR__ . '/classes/class-twitter.php';

    wp_reset_query();
    query_posts([
        'p' => $post['id'],
        'post_type' => $post['type']
    ]);

    $wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();
    $wpseo_opengraph = new WPAPI_WPSEO_OpenGraph();
    $wpseo_twitter = new WPAPI_WPSEO_Twitter();

    $yoastMeta = array(
        'metas' => array(
            'focuskw' => get_post_meta($post['id'],'_yoast_wpseo_focuskw', true),
            'title' => get_post_meta($post['id'], '_yoast_wpseo_title', true),
            'metadesc' => get_post_meta($post['id'], '_yoast_wpseo_metadesc', true),
            'linkdex' => get_post_meta($post['id'], '_yoast_wpseo_linkdex', true),
            'metakeywords' => get_post_meta($post['id'], '_yoast_wpseo_metakeywords', true),
            'meta-robots-noindex' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-noindex', true),
            'meta-robots-nofollow' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-nofollow', true),
            'meta-robots-adv' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-adv', true),
            'canonical' => get_post_meta($post['id'], '_yoast_wpseo_canonical', true),
            'redirect' => get_post_meta($post['id'], '_yoast_wpseo_redirect', true),
            'opengraph-title' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-title', true),
            'opengraph-description' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-description', true),
            'opengraph-image' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-image', true),
            'twitter-title' => get_post_meta($post['id'], '_yoast_wpseo_twitter-title', true),
            'twitter-description' => get_post_meta($post['id'], '_yoast_wpseo_twitter-description', true),
            'twitter-image' => get_post_meta($post['id'], '_yoast_wpseo_twitter-image', true)
        ),
        'title' => $wpseo_frontend->title(wp_get_document_title()),
        'canonical' => $wpseo_frontend->canonical(false),
        'metatags' => array(
            'description' => $wpseo_frontend->metadesc(false),
            'keywords' => $wpseo_frontend->metakeywords(),
            'robots' => $wpseo_frontend->robots(),
        )
    );

    return (array) $yoastMeta;
  }
}

$WPAPIYoastMeta = new WPAPIYoastMeta();
