<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * Initialize the Auryn dependency injector and offer it through a toolset_dic filter and functions.
 *
 * @since 3.0.6
 */

namespace {

	/**
	 * @return \OTGS\Toolset\Common\Auryn\Injector
	 */
	function toolset_dic() {
		static $dic;

		if ( null === $dic ) {
			$dic = new \OTGS\Toolset\Common\Auryn\Injector();
		}

		return $dic;
	}

	/** @noinspection PhpDocMissingThrowsInspection */
	/**
	 * @param $class_name
	 * @param array $args
	 *
	 * @return mixed
	 */
	function toolset_dic_make( $class_name, $args = array() ) {
		return toolset_dic()->make( $class_name, $args );
	}


	add_filter( 'toolset_dic', function ( /** @noinspection PhpUnusedParameterInspection */ $ignored ) {
		return toolset_dic();
	} );

}


/**
 * Initialize the DIC for usage of Toolset Common classes.
 */
namespace OTGS\Toolset\Common\DicSetup {

	$dic = toolset_dic();

	// To expose existing singleton classes, use delegate callbacks. These callbacks will
	// be invoked only when the instance is actually needed, thus save performance.
	// Only after a delegate is used, we'll use the $injector->share() method to
	// provide the singleton instance directly and to improve performance a bit further.
	$singleton_delegates = array(
		'\Toolset_Post_Type_Repository' => function() {
			return \Toolset_Post_Type_Repository::get_instance();
		},
		'\Toolset_Relationship_Definition_Repository' => function() {
			return \Toolset_Relationship_Definition_Repository::get_instance();
		},
		'\Toolset_WPML_Compatibility' => function() {
			return \Toolset_WPML_Compatibility::get_instance();
		}
	);

	foreach( $singleton_delegates as $class_name => $callback ) {
		$dic->delegate( $class_name, function() use( $callback, $dic ) {
			$instance = $callback();
			$dic->share( $instance );
			return $instance;
		});
	}
}