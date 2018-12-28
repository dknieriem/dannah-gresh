<?php

/**
 * Facade for caching association query results, using the WP Object Cache API.
 *
 * @since 3.0.3
 */
class Toolset_Association_Query_Cache {


	const CACHE_GROUP = 'toolset_association_query';


	private static $instance;


	public static function get_instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function push( $key, $value ) {
		wp_cache_set( $key, $value, self::CACHE_GROUP );
	}


	/**
	 * @param string $key
	 * @param null|&bool $found
	 *
	 * @return mixed
	 */
	public function get( $key, &$found = null ) {
		return wp_cache_get( $key, self::CACHE_GROUP, false, $found );
	}


}
