<?php
/**
 * Handles the creation and initialization of the all the Page Builder modules.
 *
 * @since 3.0.5
 */
class Toolset_Page_Builder_Modules {
	/**
	 * Initializes the Toolset Page Builder Modules Integration.
	 */
	public function load_modules() {
		if ( ! $this->maybe_should_load_page_builder_modules() ) {
			return;
		}

		$toolset_page_builder_with_modules = array(
			Toolset_Page_Builder_Modules_Elementor::PAGE_BUILDER_NAME,
		);

		$factory = new Toolset_Page_Builder_Modules_Factory();

		foreach ( $toolset_page_builder_with_modules as $page_builder_name ) {
			$page_builder_name = $factory->get_page_builder( $page_builder_name );
			if ( $page_builder_name ) {
				$page_builder_name->initialize();
			};
		}
	}

	/**
	 * Checks whether Toolset should load the Toolset Page Builder modules.
	 *
	 * @return bool
	 */
	public function maybe_should_load_page_builder_modules() {
		$views_version_to_support_page_builder_modules = '2.6.2';

		$views_is_active_and_in_proper_version = new Toolset_Condition_Plugin_Views_Version_Greater_Or_Equal( $views_version_to_support_page_builder_modules );

		if ( $views_is_active_and_in_proper_version->is_met() ) {
			return true;
		}

		return false;
	}
}
