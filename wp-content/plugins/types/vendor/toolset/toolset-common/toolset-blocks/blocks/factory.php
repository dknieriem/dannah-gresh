<?php

/**
 * Toolset Gutenberg Blocks factory class.
 *
 * @since 2.6.0
 */
class Toolset_Gutenberg_Block_Factory {

	public function __construct() {
		add_action( 'init', array( $this, 'register_common_block_editor_assets' ) );
	}

	/**
	 * Get the Toolset Gutenberg Block.
	 *
	 * @param string $block The name of the block.
	 *
	 * @return bool|Toolset_Blocks_Content_Template|Toolset_Blocks_CRED_Form|Toolset_Blocks_View
	 */
	public function get_block( $block ) {
		$return_block = null;
		$types_active = new Toolset_Condition_Plugin_Types_Active();
		$views_active = new Toolset_Condition_Plugin_Views_Active();
		$cred_active = new Toolset_Condition_Plugin_Cred_Active();

		switch ( $block ) {
			case Toolset_Blocks_View::BLOCK_NAME:
				if ( $views_active->is_met() ) {
					$return_block = new Toolset_Blocks_View();
				} else {
					$return_block = null;
				}
				break;
			case Toolset_Blocks_Content_Template::BLOCK_NAME:
				if ( $views_active->is_met() ) {
					$return_block = new Toolset_Blocks_Content_Template();
				} else {
					$return_block = null;
				}
				break;
			case Toolset_Blocks_CRED_Form::BLOCK_NAME:
				if ( $cred_active->is_met() ) {
					$return_block = new Toolset_Blocks_CRED_Form();
				} else {
					$return_block = null;
				}
				break;
			case Toolset_Blocks_Custom_HTML::BLOCK_NAME:
				if ( $views_active->is_met() ) {
					$return_block = new Toolset_Blocks_Custom_HTML();
				} else {
					$return_block = null;
				}
				break;
		}

		return $return_block;
	}

	/**
	 * Register the needed assets for the Toolset Gutenberg blocks on the editor.
	 *
	 * @since 2.6.0
	 */
	public function register_common_block_editor_assets() {
		$toolset_assets_manager = Toolset_Assets_Manager::getInstance();
		$toolset_assets_manager->register_style(
			'toolset-blocks-react-select-css',
			TOOLSET_COMMON_URL . '/toolset-blocks/assets/css/third-party/react-select.css',
			array(),
			TOOLSET_COMMON_VERSION
		);
	}
}
