<?php

/**
 * This code adds the OpenGraph output.
 */
class WPAPI_WPSEO_OpenGraph extends WPSEO_OpenGraph {

	/**
	 * Create new WPSEO_OpenGraph_Image class and get the images to set the og:image
	 *
	 * @param string|boolean $image Optional image URL.
	 */
	public function image( $image = false ) {
		$opengraph_images = new WPSEO_OpenGraph_Image( $this->options, $image );

		$tags = [];

		foreach ( $opengraph_images->get_images() as $img ) {
			$tags[] = [ 'name' => 'og:image', 'value' => esc_url( $img ) ];

			if ( 0 === strpos( $img, 'https://' ) ) {
                $tags[] = [ 'name' => 'og:image:secure_url', 'value' => esc_url( $img ) ];
			}
			break; // return only the first one ...
		}

		$dimensions = $opengraph_images->get_dimensions();

		if ( ! empty( $dimensions['width'] ) ) {
            $tags[] = [ 'name' => 'og:image:width', 'value' => absint( $dimensions['width'] ) ];
		}

		if ( ! empty( $dimensions['height'] ) ) {
            $tags[] = [ 'name' => 'og:image:height', 'value' => absint( $dimensions['height'] ) ];
		}
	}


} /* End of class */