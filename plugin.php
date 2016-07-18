<?php
/**
 * Plugin Name: WP REST API Yoast SEO
 * Description: Adds Yoast fields to page and post metadata to WP REST API responses
 * Author: Charlie Francis
 * Author URI: https://github.com/ChazUK
 * Version: 0.1.1
 * Plugin URI: https://github.com/ChazUK/wp-api-yoast-seo
 */

function wp_api_encode_yoast($post, $field_name, $request) {
    $yoastMeta = array(
        'yoast_wpseo_focuskw' => get_post_meta($post['id'], '_yoast_wpseo_focuskw', true),
        'yoast_wpseo_title' => get_post_meta($post['id'], '_yoast_wpseo_title', true),
        'yoast_wpseo_metadesc' => get_post_meta($post['id'], '_yoast_wpseo_metadesc', true),
        'yoast_wpseo_linkdex' => get_post_meta($post['id'], '_yoast_wpseo_linkdex', true),
        'yoast_wpseo_metakeywords' => get_post_meta($post['id'], '_yoast_wpseo_metakeywords', true),
        'yoast_wpseo_meta-robots-noindex' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-noindex', true),
        'yoast_wpseo_meta-robots-nofollow' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-nofollow', true),
        'yoast_wpseo_meta-robots-adv' => get_post_meta($post['id'], '_yoast_wpseo_meta-robots-adv', true),
        'yoast_wpseo_canonical' => get_post_meta($post['id'], '_yoast_wpseo_canonical', true),
        'yoast_wpseo_redirect' => get_post_meta($post['id'], '_yoast_wpseo_redirect', true),
        'yoast_wpseo_opengraph-title' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-title', true),
        'yoast_wpseo_opengraph-description' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-description', true),
        'yoast_wpseo_opengraph-image' => get_post_meta($post['id'], '_yoast_wpseo_opengraph-image', true),
        'yoast_wpseo_twitter-title' => get_post_meta($post['id'], '_yoast_wpseo_twitter-title', true),
        'yoast_wpseo_twitter-description' => get_post_meta($post['id'], '_yoast_wpseo_twitter-description', true),
        'yoast_wpseo_twitter-image' => get_post_meta($post['id'], '_yoast_wpseo_twitter-image', true)
    );

    return (array) $yoastMeta;
}

add_action('rest_api_init', function() {
    register_api_field('page', 'yoast_meta', array(
        'get_callback'    => 'wp_api_encode_yoast',
        'update_callback' => null,
        'schema'          => null,
    ));
});

add_action('rest_api_init', function() {
    register_api_field('post', 'yoast_meta', array(
        'get_callback'    => 'wp_api_encode_yoast',
        'update_callback' => null,
        'schema'          => null,
    ));
});
