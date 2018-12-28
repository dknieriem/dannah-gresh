<?php

/**
 * Repository for templates in Toolset Common.
 *
 * See Toolset_Renderer for a detailed usage instructions.
 *
 * @since 2.5.9
 */
class Toolset_Output_Template_Repository extends Toolset_Output_Template_Repository_Abstract {

	// Names of the templates go here and to $templates
	//
	//

	const FAUX_TEMPLATE = 'faux_template.twig';
	const MAINTENANCE_FILE = 'maintenance.twig';

	// Toolset_Shortcode_Generator templates

	const SHORTCODE_GUI_DIALOG = 'shortcodes_gui/dialog.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_GROUP_WRAPPER = 'shortcodes_gui/wrapper_attribute_group.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_WRAPPER = 'shortcodes_gui/wrapper_attribute.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_INFORMATION = 'shortcodes_gui/attribute_information.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_TEXT = 'shortcodes_gui/attribute_text.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_TEXTAREA = 'shortcodes_gui/attribute_textarea.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_RADIO = 'shortcodes_gui/attribute_radio.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_SELECT = 'shortcodes_gui/attribute_select.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_SELECT2 = 'shortcodes_gui/attribute_select2.phtml';
	const SHORTCODE_GUI_ATTRIBUTE_AJAXSELECT2 = 'shortcodes_gui/attribute_ajaxselect2.phtml';
	const SHORTCODE_GUI_CONTENT = 'shortcodes_gui/content.phtml';

	//Toolset Page Builder Modules Templates.
	const PAGE_BUILDER_MODULES_OVERLAY = '/admin/toolset-page-builder-modules/module-overlay.phtml';
	const PAGE_BUILDER_MODULES_ELEMENTOR_NO_VIEW_SELECTED = '/admin/toolset-page-builder-modules/elementor/widgets/view/no-view-selected.phtml';


	/**
	 * @var array Template definitions.
	 */
	private $templates = array();


	/** @var Toolset_Output_Template_Repository */
	private static $instance;


	/**
	 * @return Toolset_Output_Template_Repository
	 */
	public static function get_instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	public function __construct(
		Toolset_Output_Template_Factory $template_factory_di = null,
		Toolset_Constants $constants_di = null
	) {
		parent::__construct( $template_factory_di, $constants_di );

		$this->templates = array(
			self::FAUX_TEMPLATE => array(
				'base_path' => null,
				'namespaces' => array()
			),
			self::MAINTENANCE_FILE => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_DIALOG => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_GROUP_WRAPPER => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_WRAPPER => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_INFORMATION => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_TEXT => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_TEXTAREA => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_RADIO => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_SELECT => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_SELECT2 => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_ATTRIBUTE_AJAXSELECT2 => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::SHORTCODE_GUI_CONTENT => array(
				'base_path' => $this->get_templates_dir_base_path()
			),
			self::PAGE_BUILDER_MODULES_ELEMENTOR_NO_VIEW_SELECTED => array(
				'base_path' => $this->get_templates_dir_base_path(),
				'namespaces' => array(),
			),
			self::PAGE_BUILDER_MODULES_OVERLAY => array(
				'base_path' => $this->get_templates_dir_base_path(),
				'namespaces' => array(),
			),
		);
	}


	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function get_default_base_path() {
		return $this->constants->constant( 'TOOLSET_COMMON_PATH' ) . '/utility/gui-base/twig-templates';
	}


	private function get_templates_dir_base_path() {
		return $this->constants->constant( 'TOOLSET_COMMON_PATH' ) . '/templates';
	}



	/**
	 * Get the array with template definitions.
	 *
	 * @return array
	 */
	protected function get_templates() {
		return $this->templates;
	}
}