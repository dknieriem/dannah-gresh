<?php

/**
 * Handles the creation of the Toolset Toolset Form Gutenberg block to allow Toolset Form embedding inside the Gutenberg editor.
 *
 * @since 2.6.0
 */
class Toolset_Blocks_CRED_Form implements Toolset_Gutenberg_Block_Interface {

	const BLOCK_NAME = 'toolset/cred-form';

	public function init_hooks() {
		add_action( 'init', array( $this, 'register_block_editor_assets' ) );

		add_action( 'init', array( $this, 'register_block_type' ) );

		// Hook scripts function into block editor hook
		add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_editor_scripts' ) );
	}

	/**
	 * Register the needed assets for the Toolset Gutenberg blocks
	 *
	 * @since 2.6.0
	 */
	public function register_block_editor_assets() {
		$toolset_assets_manager = Toolset_Assets_Manager::getInstance();

		$toolset_assets_manager->register_script(
			'toolset-cred-form-block-js',
			TOOLSET_COMMON_URL . '/toolset-blocks/assets/js/cred.form.block.editor.js',
			array( 'wp-blocks', 'wp-element' ),
			TOOLSET_COMMON_VERSION
		);

		$toolset_ajax_controller = Toolset_Ajax::get_instance();
		wp_localize_script(
			'toolset-cred-form-block-js',
			'toolset_cred_form_block_strings',
			array(
				'block_name' => self::BLOCK_NAME,
				'published_forms' => array(
					'postForms' => apply_filters( 'cred_get_available_forms', array(), CRED_Form_Domain::POSTS ),
					'userForms' => apply_filters( 'cred_get_available_forms', array(), CRED_Form_Domain::USERS ),
				),
				'wpnonce' => wp_create_nonce( Toolset_Ajax::CALLBACK_GET_CRED_FORM_BLOCK_PREVIEW ),
				'actionName' => $toolset_ajax_controller->get_action_js_name( Toolset_Ajax::CALLBACK_GET_CRED_FORM_BLOCK_PREVIEW ),
			)
		);

		$toolset_assets_manager->register_style(
			'toolset-cred-form-block-editor-css',
			TOOLSET_COMMON_URL . '/toolset-blocks/assets/css/cred.form.block.editor.css',
			array(),
			TOOLSET_COMMON_VERSION
		);
	}

	/**
	 * Register block type. We can use this method to register the editor & frontend scripts as well as the render callback.
	 *
	 * @note For now the scripts registration is disabled as it creates console errors on the classic editor.
	 *
	 * @since 2.6.0
	 */
	public function register_block_type() {
		register_block_type(
			self::BLOCK_NAME,
			array(
				'attributes' => array(
					'form' => array(
						'type' => 'string',
						'default' => '',
					),
					'formType' => array(
						'type' => 'string',
						'default' => '',
					),
					'formAction' => array(
						'type' => 'string',
						'default' => '',
					),
					'postToEdit' => array(
						'type' => 'string',
						'default' => 'current',
					),
					'anotherPostToEdit' => array(
						'type' => 'object',
						'default' => '',
					),
					'userToEdit' => array(
						'type' => 'string',
						'default' => 'current',
					),
					'anotherUserToEdit' => array(
						'type' => 'object',
						'default' => '',
					),
				),
				'editor_script' => 'toolset-cred-form-block-js', // Editor script.
		        'editor_style' => 'toolset-cred-form-block-editor-css', // Editor style.
			)
		);
	}

	/**
	 * Enqueue assets, needed on the editor side, for the Toolset Gutenberg blocks
	 *
	 * @since 2.6.0
	 */
	public function blocks_editor_scripts() {
		do_action( 'toolset_enqueue_styles', array( 'toolset-blocks-react-select-css' ) );
	}
}
