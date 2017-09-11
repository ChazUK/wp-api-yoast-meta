<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * Main frontend class for Yoast SEO, responsible for the SEO output as well as removing
 * default WordPress output.
 */
class WPAPI_WPSEO_Frontend extends WPSEO_Frontend {

    /**
     * Get the singleton instance of this class
     *
     * @return WPSEO_Frontend
     */
    public static function get_instance() {
        if ( ! ( self::$instance instanceof self ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Outputs the meta keywords element.
     *
     * @return void
     */
    public function metakeywords() {
        global $wp_query, $post;

        if ( $this->options['usemetakeywords'] === false ) {
            return '';
        }

        $keywords = '';

        if ( is_singular() ) {
            $keywords = WPSEO_Meta::get_value( 'metakeywords' );
            if ( $keywords === '' && ( is_object( $post ) && ( ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) ) {
                $keywords = wpseo_replace_vars( $this->options[ 'metakey-' . $post->post_type ], $post );
            }
        }
        else {
            if ( $this->is_home_posts_page() && $this->options['metakey-home-wpseo'] !== '' ) {
                $keywords = wpseo_replace_vars( $this->options['metakey-home-wpseo'], array() );
            }
            elseif ( $this->is_home_static_page() ) {
                $keywords = WPSEO_Meta::get_value( 'metakeywords' );
                if ( $keywords === '' && ( is_object( $post ) && ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) {
                    $keywords = wpseo_replace_vars( $this->options[ 'metakey-' . $post->post_type ], $post );
                }
            }
            elseif ( $this->is_posts_page() ) {
                $keywords = $this->get_keywords( get_post( get_option( 'page_for_posts' ) ) );
            }
            elseif ( is_category() || is_tag() || is_tax() ) {
                $term = $wp_query->get_queried_object();

                if ( is_object( $term ) ) {
                    $keywords = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'metakey' );
                    if ( ( ! is_string( $keywords ) || $keywords === '' ) && ( isset( $this->options[ 'metakey-tax-' . $term->taxonomy ] ) && $this->options[ 'metakey-tax-' . $term->taxonomy ] !== '' ) ) {
                        $keywords = wpseo_replace_vars( $this->options[ 'metakey-tax-' . $term->taxonomy ], $term );
                    }
                }
            }
            elseif ( is_author() ) {
                $author_id = get_query_var( 'author' );
                $keywords  = get_the_author_meta( 'metakey', $author_id );
                if ( ! $keywords && $this->options['metakey-author-wpseo'] !== '' ) {
                    $keywords = wpseo_replace_vars( $this->options['metakey-author-wpseo'], $wp_query->get_queried_object() );
                }
            }
            elseif ( is_post_type_archive() ) {
                $post_type = get_query_var( 'post_type' );
                if ( is_array( $post_type ) ) {
                    $post_type = reset( $post_type );
                }
                if ( isset( $this->options[ 'metakey-ptarchive-' . $post_type ] ) && $this->options[ 'metakey-ptarchive-' . $post_type ] !== '' ) {
                    $keywords = wpseo_replace_vars( $this->options[ 'metakey-ptarchive-' . $post_type ], $wp_query->get_queried_object() );
                }
            }
        }

        $keywords = apply_filters( 'wpseo_metakey', trim( $keywords ) ); // TODO Make deprecated.

        /**
         * Filter: 'wpseo_metakeywords' - Allow changing the Yoast SEO meta keywords
         *
         * @api string $keywords The meta keywords to be echoed.
         */
        $keywords = apply_filters( 'wpseo_metakeywords', trim( $keywords ) ); // More appropriately named.

        return $keywords;
    }

    /**
     * Getting the keywords
     *
     * @param WP_Post $post The post object with the values.
     *
     * @return string
     */
    private function get_keywords( $post ) {
        $keywords        = WPSEO_Meta::get_value( 'metakeywords', $post->ID );
        $option_meta_key = 'metakey-' . $post->post_type;

        if ( $keywords === '' && ( is_object( $post ) && ( isset( $this->options[ $option_meta_key ] ) && $this->options[ $option_meta_key ] !== '' ) ) ) {
            $keywords = wpseo_replace_vars( $this->options[ $option_meta_key ], $post );
        }

        return $keywords;
    }

    /**
     * Output the meta robots value.
     *
     * @return string
     */
    public function robots() {
        global $wp_query, $post;

        $robots           = array();
        $robots['index']  = 'index';
        $robots['follow'] = 'follow';
        $robots['other']  = array();

        if ( is_singular() && is_object( $post ) ) {

            $option_name = 'noindex-' . $post->post_type;
            $noindex     = isset( $this->options[ $option_name ] ) && $this->options[ $option_name ] === true;
            $private     = 'private' === $post->post_status;

            if ( $noindex || $private ) {
                $robots['index'] = 'noindex';
            }

            $robots = $this->robots_for_single_post( $robots );

        }
        else {
            if ( is_search() || is_404() ) {
                $robots['index'] = 'noindex';
            }
            elseif ( is_tax() || is_tag() || is_category() ) {
                $term = $wp_query->get_queried_object();
                if ( is_object( $term ) && ( isset( $this->options[ 'noindex-tax-' . $term->taxonomy ] ) && $this->options[ 'noindex-tax-' . $term->taxonomy ] === true ) ) {
                    $robots['index'] = 'noindex';
                }

                // Three possible values, index, noindex and default, do nothing for default.
                $term_meta = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'noindex' );
                if ( is_string( $term_meta ) && 'default' !== $term_meta ) {
                    $robots['index'] = $term_meta;
                }

                if ( $this->is_multiple_terms_query() ) {
                    $robots['index'] = 'noindex';
                }
            }
            elseif (
                ( is_author() && $this->options['noindex-author-wpseo'] === true ) ||
                ( is_date() && $this->options['noindex-archive-wpseo'] === true )
            ) {
                $robots['index'] = 'noindex';
            }
            elseif ( is_home() ) {
                if ( get_query_var( 'paged' ) > 1 && $this->options['noindex-subpages-wpseo'] === true ) {
                    $robots['index'] = 'noindex';
                }

                $page_for_posts = get_option( 'page_for_posts' );
                if ( $page_for_posts ) {
                    $robots = $this->robots_for_single_post( $robots, $page_for_posts );
                }
                unset( $page_for_posts );

            }
            elseif ( is_post_type_archive() ) {
                $post_type = get_query_var( 'post_type' );

                if ( is_array( $post_type ) ) {
                    $post_type = reset( $post_type );
                }

                if ( isset( $this->options[ 'noindex-ptarchive-' . $post_type ] ) && $this->options[ 'noindex-ptarchive-' . $post_type ] === true ) {
                    $robots['index'] = 'noindex';
                }
            }

            $is_paged         = isset( $wp_query->query_vars['paged'] ) && ( $wp_query->query_vars['paged'] && $wp_query->query_vars['paged'] > 1 );
            $noindex_subpages = $this->options['noindex-subpages-wpseo'] === true;
            if ( $is_paged && $noindex_subpages ) {
                $robots['index'] = 'noindex';
            }
            unset( $robot );
        }

        // Force override to respect the WP settings.
        if ( '0' == get_option( 'blog_public' ) || isset( $_GET['replytocom'] ) ) {
            $robots['index'] = 'noindex';
        }


        $robotsstr = $robots['index'] . ',' . $robots['follow'];

        if ( $robots['other'] !== array() ) {
            $robots['other'] = array_unique( $robots['other'] ); // TODO Most likely no longer needed, needs testing.
            $robotsstr .= ',' . implode( ',', $robots['other'] );
        }

        $robotsstr = preg_replace( '`^index,follow,?`', '', $robotsstr );
        $robotsstr = str_replace( array( 'noodp,', 'noodp' ), '', $robotsstr );

        /**
         * Filter: 'wpseo_robots' - Allows filtering of the meta robots output of Yoast SEO
         *
         * @api string $robotsstr The meta robots directives to be echoed.
         */
        $robotsstr = apply_filters( 'wpseo_robots', $robotsstr );

        return $robotsstr;
    }
}
