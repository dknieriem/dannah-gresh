<?php

/**
 * Class Types_Page_Extension_Settings
 *
 * @since 2.1
 */
class Types_Page_Extension_Settings {

	/**
	 * @var null|Types_Helper_Twig
	 */
	private $twig = null;


	public function build() {
		// Custom content tab
		if( ! apply_filters( 'toolset_is_m2m_enabled', false ) ) {
			// Relationship Migration
			add_filter( 'toolset_filter_toolset_register_settings_custom-content_section',	array( $this, 'custom_content_tab_m2m_activation' ), 1, 2 );
		}

		// script
		add_action( 'admin_enqueue_scripts', array( $this, 'on_admin_enqueue_scripts' ) );
		add_action( 'admin_print_scripts', array( $this, 'print_admin_scripts' ) );

		$bootstrap = Toolset_Common_Bootstrap::get_instance();
		$bootstrap->register_gui_base();
		Toolset_Gui_Base::get_instance()->init();

		add_action( 'admin_print_scripts', array( $this, 'prepare_dialogs' ) );

		$m2m_migration_dialog = new Types_Page_Extension_M2m_Migration_Dialog();
		$m2m_migration_dialog->prepare();
	}


	/**
	 * Admin Scripts
	 */
	public function on_admin_enqueue_scripts() {

		$asset_manager = Toolset_Assets_Manager::get_instance();

		$asset_manager->enqueue_styles(
			array(
				'wp-admin',
				'common',
				'font-awesome',
				'wpcf-css-embedded',
				'wp-jquery-ui-dialog',
				Toolset_Gui_Base::STYLE_GUI_BASE
			)
		);

		wp_enqueue_style(
			'types-toolset-settings',
			TYPES_RELPATH . '/public/css/settings.css',
			array(),
			TYPES_VERSION
		);

		wp_enqueue_script(
			'types-toolset-settings',
			TYPES_RELPATH . '/public/js/settings.js',
			array(
				Toolset_Gui_Base::SCRIPT_GUI_ABSTRACT_PAGE_CONTROLLER,
				Toolset_Assets_Manager::SCRIPT_HEADJS,
				Toolset_Assets_Manager::SCRIPT_TOOLSET_EVENT_MANAGER
			),
			TYPES_VERSION,
			true
		);
	}

	public function print_admin_scripts() {
		echo '<script id="types_model_data" type="text/plain">'.base64_encode( wp_json_encode( $this->build_js_data() ) ).'</script>';
	}


	/**
	 * Build data to be passed to JavaScript.
	 *
	 * @return array
	 * @since m2m
	 */
	private function build_js_data() {

		$types_settings_action = Types_Ajax::get_instance()->get_action_js_name( Types_Ajax::CALLBACK_SETTINGS_ACTION );

		return array(
			'ajaxInfo' => array(
				'fieldAction' => array(
					'name' => $types_settings_action,
					'nonce' => wp_create_nonce( $types_settings_action )
				)
			),
		);
	}

	/**
	 * @param $sections
	 * @param $toolset_options
	 *
	 * @return array
	 * @since m2m
	 */
	public function custom_content_tab_m2m_activation( $sections, /** @noinspection PhpUnusedParameterInspection */ $toolset_options ) {
		$context = $this->m2m_activation_context();

		$sections['toolset_is_m2m_enabled'] = array(
			'slug' => 'toolset_is_m2m_enabled',
			'title' => __( 'Relationships', 'wpcf' ),
			'content' =>  $this->get_twig()->render(
				'/setting/m2m/activation.twig', $context
			)
		);

		return $sections;
	}

	/**
	 * Generate context for m2m activation setting
	 * @return array
	 * @since m2m
	 */
	private function m2m_activation_context() {

		$gui_base = Toolset_Gui_Base::get_instance();
		$base_context = $gui_base->get_twig_context_base( Toolset_Gui_Base::TEMPLATE_LISTING, $this->m2m_prepare_js_model_data() );

		$context = array(
			'description' => __( 'Migrate from legacy post relationships to many-to-many post relationships', 'wpcf' ),
			'sections' => array( 'm2mActivation' => array() ),
			'm2m_enabled' =>  apply_filters( 'toolset_is_m2m_enabled', false ) === false
		);

		$context = toolset_array_merge_recursive_distinct( $base_context, $context );
		return $context;
	}

	/**
	 * Prepare JS strings for dialog
	 *
	 * @return array
	 * @since m2m
	 */
	private function m2m_prepare_js_model_data() {

		$js_model_data = array(
			'sections' => array( 'm2mActivation' => array() ),
			'strings' => array(
				'confirmUnload' => __( 'There is an action in progress. Please do not leave or reload this page until it finishes.', 'wpcf' )
			)
		);

		return $js_model_data;
	}



	/**
	 * Retrieve a Twig environment initialized by the Toolset GUI base.
	 *
	 * @return Types_Helper_Twig
	 * @since m2m
	 */
	private function get_twig() {
		if( null == $this->twig ) {
			$this->twig = new Types_Helper_Twig(
				array( 'settings' => TYPES_ABSPATH . '/application/views/setting' )
			);
		}

		return $this->twig;
	}


	/**
	 * @since m2m
	 */
	public function prepare_dialogs() {

	}
}
