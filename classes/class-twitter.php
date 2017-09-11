<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * This class handles the Twitter card functionality.
 *
 * @link https://dev.twitter.com/docs/cards
 */
class WPAPI_WPSEO_Twitter {

	/**
	 * Displays the description for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	public function description() {
		if ( is_singular() ) {
			$meta_desc = $this->single_description();
		}
		elseif ( WPSEO_Frontend::get_instance()->is_posts_page() ) {
			$meta_desc = $this->single_description( get_option( 'page_for_posts' ) );
		}
		elseif ( is_category() || is_tax() || is_tag() ) {
			$meta_desc = $this->taxonomy_description();
		}
		else {
			$meta_desc = $this->fallback_description();
		}

		/**
		 * Filter: 'wpseo_twitter_description' - Allow changing the Twitter description as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The description string
		 */
		$meta_desc = apply_filters( 'wpseo_twitter_description', $meta_desc );
		return $meta_desc;
	}

	/**
	 * Returns the description for a singular page
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	private function single_description( $post_id = 0 ) {
		$meta_desc = trim( WPSEO_Meta::get_value( 'twitter-description', $post_id ) );

		if ( is_string( $meta_desc ) && '' !== $meta_desc ) {
			return $meta_desc;
		}

		$meta_desc = $this->fallback_description();
		if ( is_string( $meta_desc ) && '' !== $meta_desc ) {
			return $meta_desc;
		}

		return strip_tags( get_the_excerpt() );
	}


	/**
	 * Getting the description for the taxonomy
	 *
	 * @return bool|mixed|string
	 */
	private function taxonomy_description() {
		$meta_desc = WPSEO_Taxonomy_Meta::get_meta_without_term( 'twitter-description' );

		if ( ! is_string( $meta_desc ) || $meta_desc === '' ) {
			$meta_desc = $this->fallback_description();
		}

		if ( is_string( $meta_desc ) || $meta_desc !== '' ) {
			return $meta_desc;
		}

		return trim( strip_tags( term_description() ) );

	}

	/**
	 * Returns a fallback description
	 *
	 * @return string
	 */
	private function fallback_description() {
		return trim( WPSEO_Frontend::get_instance()->metadesc( false ) );
	}

	/**
	 * Displays the title for Twitter.
	 *
	 * Only used when OpenGraph is inactive.
	 */
	public function title() {
		if ( is_singular() ) {
			$title = $this->single_title();
		}
		elseif ( WPSEO_Frontend::get_instance()->is_posts_page() ) {
			$title = $this->single_title( get_option( 'page_for_posts' ) );
		}
		elseif ( is_category() || is_tax() || is_tag() ) {
			$title = $this->taxonomy_title();
		}
		else {
			$title = $this->fallback_title();
		}

		/**
		 * Filter: 'wpseo_twitter_title' - Allow changing the Twitter title as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The title string
		 */
		$title = apply_filters( 'wpseo_twitter_title', $title );
		return $title;
	}

	/**
	 * Returns the Twitter title for a single post
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	private function single_title( $post_id = 0 ) {
		$title = WPSEO_Meta::get_value( 'twitter-title', $post_id );
		if ( ! is_string( $title ) || $title === '' ) {
			return $this->fallback_title();
		}

		return $title;
	}

	/**
	 * Getting the title for the taxonomy
	 *
	 * @return bool|mixed|string
	 */
	private function taxonomy_title() {
		$title = WPSEO_Taxonomy_Meta::get_meta_without_term( 'twitter-title' );

		if ( ! is_string( $title ) || $title === '' ) {
			return $this->fallback_title();
		}

		return $title;
	}

	/**
	 * Returns the Twitter title for any page
	 *
	 * @return string
	 */
	private function fallback_title() {
		return WPSEO_Frontend::get_instance()->title( '' );
	}

	/**
	 * Displays the image for Twitter
	 *
	 * Only used when OpenGraph is inactive or Summary Large Image card is chosen.
	 */
	public function image() {

		if ( is_category() || is_tax() || is_tag() ) {
			return $this->taxonomy_image_output();
		}
		else {
			return $this->single_image_output();
		}
	}

	/**
	 * Outputs the first image of a gallery.
	 */
	private function gallery_images_output() {

		return reset( $this->images );
	}

	/**
	 * @return bool
	 */
	private function taxonomy_image_output() {
		foreach ( array( 'twitter-image', 'opengraph-image' ) as $tag ) {
			$img = WPSEO_Taxonomy_Meta::get_meta_without_term( $tag );
			if ( $img !== '' ) {
				$this->image_output( $img );

				return true;
			}
		}

		return false;
	}

	/**
	 * Takes care of image output when we only need to display a single image.
	 */
	private function single_image_output() {
		if ( $img = $this->homepage_image_output() ) {
			return $img;
		}
		elseif ( $img = $this->posts_page_image_output() ) { // Posts page, which won't be caught by is_singular() below.
			return $img;
		}

		if ( is_singular() ) {
			if ( $img = $this->image_from_meta_values_output() ) {
				return $img;
			}

			$post_id = get_the_ID();

			if ( $img = $this->image_of_attachment_page_output( $post_id ) ) {
				return $img;
			}
			if ( $img = $this->image_thumbnail_output() ) {
				return $img;
			}
			if ( !empty($this->images) ) {
				return $this->gallery_images_output();
			}
			if ( $img = $this->image_from_content_output() ) {
				return $img;
			}
		}

		return '';
	}

	/**
	 * Show the front page image
	 *
	 * @return bool
	 */
	private function homepage_image_output() {
		if ( is_front_page() ) {
			if ( $this->options['og_frontpage_image'] !== '' ) {
				return $this->options['og_frontpage_image'];
			}
		}

		return false;
	}

	/**
	 * Show the posts page image.
	 *
	 * @return bool
	 */
	private function posts_page_image_output() {

		if ( is_front_page() || ! is_home() ) {
			return false;
		}

		$post_id = get_option( 'page_for_posts' );

		if ( $img = $this->image_from_meta_values_output( $post_id ) ) {
			return $img;
		}

		if ( $img = $this->image_thumbnail_output( $post_id ) ) {
			return $img;
		}

		return false;
	}

	/**
	 * Outputs a Twitter image tag for a given image
	 *
	 * @param string  $img The source URL to the image.
	 * @param boolean $tag Deprecated argument, previously used for gallery images.
	 *
	 * @return bool
	 */
	protected function image_output( $img, $tag = false ) {

		if ( $tag ) {
			_deprecated_argument( __METHOD__, 'WPSEO 2.4' );
		}

		/**
		 * Filter: 'wpseo_twitter_image' - Allow changing the Twitter Card image
		 *
		 * @api string $img Image URL string
		 */
		$img = apply_filters( 'wpseo_twitter_image', $img );

		if ( WPSEO_Utils::is_url_relative( $img ) === true && $img[0] === '/' ) {
			$parsed_url = wp_parse_url( home_url() );
			$img        = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $img;
		}

		$escaped_img = esc_url( $img );

		if ( in_array( $escaped_img, $this->shown_images ) ) {
			return false;
		}

		if ( is_string( $escaped_img ) && $escaped_img !== '' ) {
			$this->output_metatag( 'image', $escaped_img, true );
			array_push( $this->shown_images, $escaped_img );

			return true;
		}

		return false;
	}

	/**
	 * Retrieve images from the post meta values
	 *
	 * @param int $post_id Optional post ID to use.
	 *
	 * @return bool
	 */
	private function image_from_meta_values_output( $post_id = 0 ) {
		foreach ( array( 'twitter-image', 'opengraph-image' ) as $tag ) {
			$img = WPSEO_Meta::get_value( $tag, $post_id );
			if ( $img !== '' ) {
				return $img;
			}
		}

		return false;
	}

	/**
	 * Retrieve an attachment page's attachment
	 *
	 * @param string $attachment_id The ID of the attachment for which to retrieve the image.
	 *
	 * @return bool
	 */
	private function image_of_attachment_page_output( $attachment_id ) {
		if ( get_post_type( $attachment_id ) === 'attachment' ) {
			$mime_type = get_post_mime_type( $attachment_id );
			switch ( $mime_type ) {
				case 'image/jpeg':
				case 'image/png':
				case 'image/gif':
					return wp_get_attachment_url( $attachment_id );
			}
		}

		return false;
	}

	/**
	 * Retrieve the featured image
	 *
	 * @param int $post_id Optional post ID to use.
	 *
	 * @return bool
	 */
	private function image_thumbnail_output( $post_id = 0 ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_id ) ) {
			/**
			 * Filter: 'wpseo_twitter_image_size' - Allow changing the Twitter Card image size
			 *
			 * @api string $featured_img Image size string
			 */
			$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), apply_filters( 'wpseo_twitter_image_size', 'full' ) );

			if ( $featured_img ) {
				return $featured_img[0];
			}
		}

		return false;
	}

	/**
	 * Retrieve the image from the content
	 *
	 * @return bool
	 */
	private function image_from_content_output() {
		/**
		 * Filter: 'wpseo_pre_analysis_post_content' - Allow filtering the content before analysis
		 *
		 * @api string $post_content The Post content string
		 *
		 * @param object $post - The post object.
		 */
		global $post;
		$content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );

		if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
			foreach ( $matches[0] as $img ) {
				if ( preg_match( '`src=(["\'])(.*?)\1`', $img, $match ) ) {
					return $match[2];
				}
			}
		}

		return false;
	}

	/**
	 * Displays the authors Twitter account.
	 */
	protected function author() {
		$twitter = ltrim( trim( get_the_author_meta( 'twitter', get_post()->post_author ) ), '@' );
		/**
		 * Filter: 'wpseo_twitter_creator_account' - Allow changing the Twitter account as output in the Twitter card by Yoast SEO
		 *
		 * @api string $twitter The twitter account name string
		 */
		$twitter = apply_filters( 'wpseo_twitter_creator_account', $twitter );
		$twitter = $this->get_twitter_id( $twitter );

		if ( is_string( $twitter ) && $twitter !== '' ) {
			$this->output_metatag( 'creator', '@' . $twitter );
		}
		elseif ( $this->options['twitter_site'] !== '' ) {
			if ( is_string( $this->options['twitter_site'] ) && $this->options['twitter_site'] !== '' ) {
				$this->output_metatag( 'creator', '@' . $this->options['twitter_site'] );
			}
		}
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Displays the domain tag for the site.
	 *
	 * @deprecated 3.0
	 *
	 * @codeCoverageIgnore
	 */
	protected function site_domain() {
		_deprecated_function( __METHOD__, 'WPSEO 3.0' );
	}
} /* End of class */
