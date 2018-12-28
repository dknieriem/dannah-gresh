<?php

/**
 * Toolset Page Builder Modules factory.
 *
 * @since 3.0.5
 */
class Toolset_Page_Builder_Modules_Factory {
	/**
	 * Get the Toolset Page Builder with modules.
	 *
	 * @param string $page_builder The page builder name.
	 *
	 * @return bool|Toolset_Page_Builder_Modules_Elementor
	 */
	public function get_page_builder( $page_builder ) {
		$return_page_builder = null;

		$views_active = new Toolset_Condition_Plugin_Views_Active();

		switch ( $page_builder ) {
			case Toolset_Page_Builder_Modules_Elementor::PAGE_BUILDER_NAME:
				if ( $views_active->is_met() ) {
					$return_page_builder = new Toolset_Page_Builder_Modules_Elementor();
				} else {
					$return_page_builder = null;
				}
				break;
		}

		return $return_page_builder;
	}
}