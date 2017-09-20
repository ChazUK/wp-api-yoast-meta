<?php

include __DIR__ . '/classes/class-wpseo-replace-vars.php';
include __DIR__ . '/classes/class-frontend.php';

/**
 * Plugin Name: WP REST API Yoast SEO
 * Description: Adds Yoast fields to page and post metadata to WP REST API responses
 * Author: Charlie Francis, Tedy Warsitha, Pablo Postigo
 * Author URI: https://github.com/ChazUK
 * Version: 1.1.0
 * Plugin URI: https://github.com/ChazUK/wp-api-yoast-seo
 */
class WPAPIYoastMeta {

	protected $keys = array(
		'yoast_wpseo_focuskw',
		'yoast_wpseo_title',
		'yoast_wpseo_metadesc',
		'yoast_wpseo_linkdex',
		'yoast_wpseo_metakeywords',
		'yoast_wpseo_meta-robots-noindex',
		'yoast_wpseo_meta-robots-nofollow',
		'yoast_wpseo_meta-robots-adv',
		'yoast_wpseo_canonical',
		'yoast_wpseo_redirect',
		'yoast_wpseo_opengraph-title',
		'yoast_wpseo_opengraph-description',
		'yoast_wpseo_opengraph-image',
		'yoast_wpseo_twitter-title',
		'yoast_wpseo_twitter-description',
		'yoast_wpseo_twitter-image'
	);

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'add_yoast_data' ) );
	}

	function add_yoast_data() {
		// Posts
		register_rest_field( 'post',
			'yoast_meta',
			array(
				'get_callback'    => array( $this, 'wp_api_encode_yoast' ),
				'update_callback' => array( $this, 'wp_api_update_yoast' ),
				'schema'          => null,
			)
		);

		// Pages
		register_rest_field( 'page',
			'yoast_meta',
			array(
				'get_callback'    => array( $this, 'wp_api_encode_yoast' ),
				'update_callback' => array( $this, 'wp_api_update_yoast' ),
				'schema'          => null,
			)
		);

    // Category
		register_rest_field( 'category',
        'yoast',
        array(
            'get_callback'    => array( $this, 'wp_api_encode_yoast_category' ),
            'update_callback' => null,
            'schema'          => null,
        )
    );

  	// Tag
  	register_rest_field( 'tag',
        'yoast',
        array(
            'get_callback'    => array( $this, 'wp_api_encode_yoast_tag' ),
            'update_callback' => null,
            'schema'          => null,
        )
    );

		// Public custom post types
		$types = get_post_types( array(
			'public'   => true,
			'_builtin' => false
		) );
		foreach ( $types as $key => $type ) {
			register_rest_field( $type,
				'yoast_meta',
				array(
					'get_callback'    => array( $this, 'wp_api_encode_yoast' ),
					'update_callback' => array( $this, 'wp_api_update_yoast' ),
					'schema'          => null,
				)
			);
		}
	}

	/**
	 * Updates post meta with values from post/put request.
	 * @param array $value
	 * @param object $data
	 * @param string $field_name
	 *
	 * @return array
	 */
	function wp_api_update_yoast( $value, $data, $field_name ) {

		foreach ( $value as $k => $v ) {

			if ( in_array( $k, $this->keys ) ) {
				! empty( $k ) ? update_post_meta( $data->ID, '_' . $k, $v ) : null;
			}
		}

		return $this->wp_api_encode_yoast( $data->ID, null, null );
	}

	function get_title_home_wpseo() {
		$wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();

		return $wpseo_frontend->get_title_from_options( 'title-home-wpseo' );
	}

	function get_metadesc_home_wpseo() {
		$wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();
		$wpseo_replace_vars = new WPAPI_WPSEO_Replace_Vars();

		//Generate metadesc-home-wpseo
		$metadesc = $wpseo_frontend->options['metadesc-home-wpseo'];
		$post_data     = array();
		$omit = array();

		if ( empty( $metadesc ) ) {
			$metadesc = get_bloginfo( 'description' );
		}

		return $wpseo_replace_vars->replace( $metadesc, $post_data, $omit );
	}

	function wp_api_encode_yoast( $p, $field_name, $request ) {
		$wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();

		$args = array(
  		'p'         => $p['id'], // ID of a page, post, or custom type
  		'post_type' => 'any'
		);

		$GLOBALS['wp_query'] = new WP_Query( $args );

		$yoastMeta = array(
			'yoast_wpseo_title'                 => $wpseo_frontend->get_content_title(),
			'yoast_wpseo_metadesc'              => $wpseo_frontend->metadesc(false),
			'yoast_wpseo_focuskw'               => get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true ),
			'yoast_wpseo_linkdex'               => get_post_meta( $p['id'], '_yoast_wpseo_linkdex', true ),
			'yoast_wpseo_metakeywords'          => get_post_meta( $p['id'], '_yoast_wpseo_metakeywords', true ),
			'yoast_wpseo_meta-robots-noindex'   => get_post_meta( $p['id'], '_yoast_wpseo_meta-robots-noindex', true ),
			'yoast_wpseo_meta-robots-nofollow'  => get_post_meta( $p['id'], '_yoast_wpseo_meta-robots-nofollow', true ),
			'yoast_wpseo_meta-robots-adv'       => get_post_meta( $p['id'], '_yoast_wpseo_meta-robots-adv', true ),
			'yoast_wpseo_canonical'             => get_post_meta( $p['id'], '_yoast_wpseo_canonical', true ),
			'yoast_wpseo_redirect'              => get_post_meta( $p['id'], '_yoast_wpseo_redirect', true ),
			'yoast_wpseo_opengraph-title'       => get_post_meta( $p['id'], '_yoast_wpseo_opengraph-title', true ),
			'yoast_wpseo_opengraph-description' => get_post_meta( $p['id'], '_yoast_wpseo_opengraph-description', true ),
			'yoast_wpseo_opengraph-image'       => get_post_meta( $p['id'], '_yoast_wpseo_opengraph-image', true ),
			'yoast_wpseo_twitter-title'         => get_post_meta( $p['id'], '_yoast_wpseo_twitter-title', true ),
			'yoast_wpseo_twitter-description'   => get_post_meta( $p['id'], '_yoast_wpseo_twitter-description', true ),
			'yoast_wpseo_twitter-image'         => get_post_meta( $p['id'], '_yoast_wpseo_twitter-image', true ),
			'yoast_wpseo_homepage_title'        => $this->get_title_home_wpseo(),
			'yoast_wpseo_homepage_metadesc'     => $this->get_metadesc_home_wpseo(),
		);

		return (array) $yoastMeta;
	}

	function wp_api_encode_taxonomy (){
		$wpseo_frontend = WPAPI_WPSEO_Frontend::get_instance();

		$yoastMeta = array(
			'yoast_wpseo_title'          => $wpseo_frontend->get_taxonomy_title(),
			'yoast_wpseo_metadesc'       => $wpseo_frontend->metadesc(false),
			'yoast_wpseo_homepage_title' => $this->get_title_home_wpseo(),
			'yoast_wpseo_homepage_metadesc'  => $this->get_metadesc_home_wpseo(),
		);

		return (array) $yoastMeta;
	}

	function wp_api_encode_yoast_category($category) {
		$args=array(
  		'cat' => $category['id'],
		);
		$GLOBALS['wp_query'] = new WP_Query( $args );

		return $this->wp_api_encode_taxonomy();
	}

	function wp_api_encode_yoast_tag($tag){
		$args=array(
			'tag_id' => $tag['id'],
		);
		$GLOBALS['wp_query'] = new WP_Query( $args );

		return $this->wp_api_encode_taxonomy();
	}

}

$WPAPIYoastMeta = new WPAPIYoastMeta();
