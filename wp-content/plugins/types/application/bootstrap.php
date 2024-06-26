<?php

/*
 * Autoloader
 */
require_once( TYPES_ABSPATH . '/vendor/toolset/autoloader/autoloader.php' );

$autoloader = Types_Autoloader::get_instance();

$autoloader->add_path( 'Toolset', TYPES_ABSPATH . '/vendor/toolset' );


/*
 * Load old Types
 */
if( ! defined( 'WPCF_RELPATH' ) ) {
	define( 'WPCF_RELPATH', TYPES_RELPATH . '/vendor/toolset/types' );
}

if( ! defined( 'WPCF_EMBEDDED_TOOLSET_ABSPATH' ) ) {
	define( 'WPCF_EMBEDDED_TOOLSET_ABSPATH', TYPES_ABSPATH . '/vendor/toolset' );
}

if( ! defined( 'WPCF_EMBEDDED_TOOLSET_RELPATH') ) {
	define( 'WPCF_EMBEDDED_TOOLSET_RELPATH', TYPES_RELPATH . '/vendor/toolset' );
}

if( ! defined( 'WPTOOLSET_COMMON_PATH' ) ) {
	define( 'WPTOOLSET_COMMON_PATH', TYPES_ABSPATH . '/vendor/toolset/toolset-common' );
}

if ( !defined( 'EDITOR_ADDON_RELPATH' ) ) {
	define( 'EDITOR_ADDON_RELPATH', WPCF_EMBEDDED_TOOLSET_RELPATH . '/toolset-common/visual-editor' );
}

// installer
$installer = TYPES_ABSPATH . '/vendor/otgs/installer/loader.php';
if ( file_exists( $installer ) ) {

	// This is a required dependency for Installer, but we cannot use neither the composer autoloader
	// (because of how Toolset Common is being loaded) nor our own autoloader (because this file doesn't contain
	// a class).
	require_once TYPES_ABSPATH . '/vendor/jakeasmith/http_build_url/src/http_build_url.php';

	/** @noinspection PhpIncludeInspection */
	include_once $installer;
	if ( function_exists( 'WP_Installer_Setup' ) ) {
		WP_Installer_Setup(
			$wp_installer_instance,
			array(
				'plugins_install_tab' => '1',
				'repositories_include' => array('toolset', 'wpml'),
			)
		);
	}

	// This is a temporary workaround since Types removed Twig from its composer dependencies,
    // until the Installer handles this dependency properly. Minor performance drawback, it should
    // be removed as soon as possible.
	add_action( 'init', function() {
	    Toolset_Common_Bootstrap::get_instance()->register_gui_base();
	    Toolset_Gui_Base::get_instance()->init();
    }, 20 );
}



// Get new functions.php
require_once( dirname( __FILE__ ) . '/functions.php' );

// Initialize legacy code
require_once( dirname( __FILE__ ) . '/../vendor/toolset/types/wpcf.php' );

// Public API
require_once( dirname( __FILE__ ) . '/controllers/main.php' );

// Public Types functions
require_once( dirname( __FILE__ ) . '/functions_public.php' );

// Jumpstart new Types
Types_Main::initialize();

// Screen Options Controller
add_action( 'init', function() {
	if( ! is_admin() ) {
		// no screen options on front-end
		return;
	}

	new \OTGS\Toolset\Types\Controller\ScreenOptions();
} );

// YOAST Comptability
add_action( 'init', function() {
	if( ! TOOLSET_TYPES_YOAST || ! is_admin() ) {
		// this check is not required, but saves resources
		return;
	}

	// load DIC
	$dic = apply_filters( 'toolset_dic', false );

	// load fields to YOAST Analysis on post edit screen
	add_action( 'load-post.php', function() use ( $dic ) {
		$yoast = $dic->make( '\OTGS\Toolset\Types\Controller\Compatibility\Yoast' );

		if( $yoast->dependenciesLoaded() ) {
			$dic->execute( array( $yoast, 'postEditScreen' ) );
		}
	} );

	// function to add YOAST options to Fields creation GUI
	$field_group_edit_page = function() use ( $dic ) {
		$yoast = $dic->make( '\OTGS\Toolset\Types\Controller\Compatibility\Yoast' );

		if( $yoast->dependenciesLoaded() ) {
			$dic->execute( array( $yoast, 'groupEditScreen' ) );
		}
	};

	// run GUI extension on field group edit page
	add_action( 'load-toolset_page_wpcf-edit', function() use ( $field_group_edit_page ) { $field_group_edit_page(); } );

	// run GUI extension when adding a new field via AJAX
	if( defined( 'DOING_AJAX' )
	    && DOING_AJAX
	    && isset( $_REQUEST['action'] )
		&& $_REQUEST['action'] == 'wpcf_edit_field_insert'
	) {
		$field_group_edit_page();
	}
} );

/**
 * Gutenberg Compatiblity
 *
 * @since 3.2
 */
function types_bootstrap_gutenberg_compatibility() {
	// load DIC
	$dic = apply_filters( 'toolset_dic', false );

	/** @var \OTGS\Toolset\Types\Controller\Compatibility\Gutenberg $gutenberg */
	$gutenberg = $dic->make( '\OTGS\Toolset\Types\Controller\Compatibility\Gutenberg' );
	$gutenberg->initialize();

	if ( $gutenberg->is_active_for_current_post_type() ) {
		$dic->execute( array( $gutenberg, 'post_edit_screen' ) );
	}
}

// load compatibililty on new post and edit post screen
add_action( 'load-post.php', 'types_bootstrap_gutenberg_compatibility' );
add_action( 'load-post-new.php', 'types_bootstrap_gutenberg_compatibility' );
