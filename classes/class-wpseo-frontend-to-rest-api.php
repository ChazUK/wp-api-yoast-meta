<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * Main frontend class for Yoast SEO, responsible for the SEO output as well as removing
 * default WordPress output.
 */
class WPSEO_Frontend_To_REST_API extends WPSEO_Frontend {
	/**
	 * Get the singleton instance of this class.
	 *
	 * @return WPSEO_Frontend
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
