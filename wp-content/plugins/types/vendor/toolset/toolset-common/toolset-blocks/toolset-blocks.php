<?php
/**
 * Handles the creation and initialization of the all the Gutenberg integration stuff.
 *
 * @since 2.6.0
 */

class Toolset_Blocks {
	public function load_blocks() {
		if (
			! $this->is_gutenberg_active()
			|| ! $this->is_toolset_ready_for_gutenberg()
		) {
			return;
		}

		$toolset_blocks = array(
			Toolset_Blocks_View::BLOCK_NAME,
			Toolset_Blocks_Content_Template::BLOCK_NAME,
			Toolset_Blocks_CRED_Form::BLOCK_NAME,
			Toolset_Blocks_Custom_HTML::BLOCK_NAME,
		);

		$factory = new Toolset_Gutenberg_Block_Factory();
		$helper = new Toolset_Gutenberg_Block_REST_Helper();

		foreach ( $toolset_blocks as $toolset_block_name ) {
			$block = $factory->get_block( $toolset_block_name );
			if ( $block ) {
				$block->init_hooks();
			};
		}
	}

	public function is_gutenberg_active() {
		// return defined( 'GUTENBERG_VERSION' ) || defined( 'GUTENBERG_DEVELOPMENT_MODE' );
		return function_exists( 'register_block_type' );
	}

	public function is_toolset_ready_for_gutenberg() {
		$views_compatibility = false;
		$cred_compatibility = false;

		if (
			defined( 'WPV_VERSION' ) &&
			version_compare( WPV_VERSION, '2.6', '>=' )
		) {
			$views_compatibility = true;
		}

		if (
			defined( 'CRED_FE_VERSION' ) &&
			version_compare( CRED_FE_VERSION, '2.0', '>=' )
		) {
			$cred_compatibility = true;
		}

		return $views_compatibility || $cred_compatibility;
	}
}
