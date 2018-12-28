<?php

/**
 * Main backend controller for Types.
 *
 * @since 2.0
 */
final class Types_Admin {


	/**
	 * Initialize Types for backend.
	 *
	 * This is expected to be called during init.
	 *
	 * @since 2.0
	 */
	public static function initialize() {
		new self();
	}


	private function __construct() {
		

		$this->on_init();
	}


	private function __clone() { }


	private function on_init() {

		Types_Upgrade::initialize();

		// Load menu - won't be loaded in embedded version.
		if( apply_filters( 'types_register_pages', true ) ) {
			Types_Admin_Menu::initialize();
		}

		$this->init_page_extensions();

		$this->init_shortcodes();
	}


	/**
	 * Add hooks for loading page extensions.
	 *
	 * @since 2.1
	 */
	private function init_page_extensions() {
		// extensions for post edit page
		add_action( 'load-post.php', array( 'Types_Page_Extension_Edit_Post', 'get_instance' ) );

		// extension for post type edit page
		add_action( 'load-toolset_page_wpcf-edit-type', array( 'Types_Page_Extension_Edit_Post_Type', 'get_instance' ) );

		// extension for post fields edit page
		add_action( 'load-toolset_page_wpcf-edit', array( 'Types_Page_Extension_Edit_Post_Fields', 'get_instance' ) );

		// settings
		add_action( 'load-toolset_page_toolset-settings', array( $this, 'init_settings' ) );

		if( apply_filters( 'toolset_is_m2m_enabled', false ) ) {

			// Related posts in edit pages.
			add_action( 'add_meta_boxes', array( 'Types_Page_Extension_Meta_Box_Related_Content', 'initialize' ) );

		}

		// extension for cpt edit page
		add_action( 'load-toolset_page_wpcf-edit-type', function() {
			Toolset_Singleton_Factory::get( 'Types_Admin_Notices_Custom_Fields_For_New_Cpt' );
		} );
	}


	/**
	 * Initialize the extension for the Toolset Settings page.
	 *
	 * @since 2.1
	 */
	public function init_settings() {
		$settings = new Types_Page_Extension_Settings();
		$settings->build();
	}

	/**
	 * Maybe initialize the Types shortcodes in the backend.
	 *
	 * Type shortcodes do not need to be registered in the backend;
	 * however, some page builders with backend editors include previews,
	 * so we need to register them to avoid raw shortcodes to be printed.
	 *
	 * @since 3.0.x
	 */
	private function init_shortcodes() {
		// shortcode [types]
		$factory = new Types_Shortcode_Factory();
		if( $shortcode = $factory->get_shortcode( 'types' ) ) {
			add_shortcode( 'types', array( $shortcode, 'render' ) );
		};
	}

	/**
	 * Check whether the current admin page is the backend editor for a known page buildr.
	 *
	 * @return boolean
	 * @since 3.0.x
	 */
	private function is_page_builder_editor_with_preview() {
		// Elementor first pageload includes those GET parameters
		// Any further preview update happens on AJAX
		if (
			'elementor' == toolset_getget( 'action' )
			&& '' != toolset_getget( 'post' )
		) {
			return true;
		}
		return false;
	}

}
