<?php
/**
 * Class Toolset_Elementor_View_Widget
 *
 * Handles the Toolset View Elementor Widget.
 *
 * @since 3.0.5
 */
class Toolset_Elementor_View_Widget extends \Elementor\Widget_Base {

	const FRONTEND_WIDGET_SCRIPT = 'toolset-pageBuilder-elementor-widget-view-editor-js';

	const FRONTEND_WIDGET_STYLE = 'toolset-pageBuilder-elementor-widget-view-editor-frontend-css';

	const BACKEND_WIDGET_STYLE = 'toolset-pageBuilder-elementor-widget-view-editor-css';

	const FRONTEND_WIDGET_SCRIPT_LOCALIZATION_OBJECT_NAME = 'toolsetPageBuilderElementorWidgetViewStrings';

	private $constants;

	private $tc_bootstrap;

	private $toolset_renderer;

	public $initial_settings;

	public function __construct(
		array $data = array(),
		array $args = null,
		Toolset_Common_Bootstrap $tc_bootstrap = null,
		Toolset_Constants $constants = null,
		Toolset_Renderer $toolset_renderer = null
	) {
		$this->initial_settings = toolset_getarr( $data, 'settings', array() );

		parent::__construct( $data, $args );

		$this->constants = $constants
			? $constants
			: new Toolset_Constants();

		$this->tc_bootstrap = $tc_bootstrap
			? $tc_bootstrap
			: Toolset_Common_Bootstrap::get_instance();

		$this->toolset_renderer = $toolset_renderer
			? $toolset_renderer
			: Toolset_Renderer::get_instance();
	}

	/**
	 * Initiliazes the Toolset View ELementor widget.
	 */
	public function init() {
		$this->register_styles();

		add_filter( 'elementor/widgets/black_list', array( $this, 'blacklist_views_widgets' ) );
	}

	/**
	 * The name of the Toolset Elementor widget.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'toolset-view';
	}

	/**
	 * The title of the Toolset Elementor widget.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Toolset View', 'wpv-view' );
	}

	/**
	 * The icon of the Toolset Elementor widget.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'icon-views-logo';
	}

	/**
	 * The category of the Toolset Elementor widget.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Registers the Toolset View Elementor widget controls.
	 */
	protected function _register_controls() {
		$widget_controls = new Toolset_Elementor_View_Widget_Controls( $this );
		$widget_controls->register_controls();

	}

	/**
	 * Renders the Toolset View Elementor widget output on the frontend.
	 */
	protected function render() {
		if ( $this->tc_bootstrap->get_request_mode() === $this->constants->constant( 'Toolset_Common_Bootstrap::MODE_FRONTEND' ) ) {
			$this->render_frontend_widget( true );
		} else {
			$this->render_backend_widget_preview( true );
		}
	}

	/**
	 * Renders the Toolset View Elementor widget output for the frontend.
	 *
	 * @param bool $echo Determines if the output will be echo-ed or returned (mainly for unit-testing purposes).
	 *
	 * @return bool|string The markup for the frontend widget rendering echo-ed or returned.
	 */
	public function render_frontend_widget( $echo = true ) {
		$settings = $this->get_settings_for_display();
		$view_id = toolset_getarr( $settings, 'view', '0' );
		$view = WPV_View_Base::get_instance( $view_id );

		if ( null !== $view ) {
			$html = do_shortcode( '[wpv-view name="' . $view->slug . '"]' );
		} else {
			$html = $this->render_no_view_selected_message( false );
		}

		if ( false !== $echo ) {
			echo $html;
			return true;
		} else {
			return $html;
		}
	}

	/**
	 * Renders the Toolset View Elementor widget output for the widget preview on the editor.
	 *
	 * @param bool $echo Determines if the output will be echo-ed or returned (mainly for unit-testing purposes).
	 *
	 * @return bool|string The markup for the editor widget rendering echo-ed or returned.
	 */
	public function render_backend_widget_preview( $echo = true ) {
		$settings = $this->get_settings_for_display();

		$view_id = toolset_getarr( $settings, 'view', '0' );
		$view = WPV_View_Base::get_instance( $view_id );

		if ( null !== $view ) {
//			$limit_value = (int) toolset_getnest( $settings, array( 'limit', 'size' ), '0' );
//			$limit =  $limit_value > 0 ? ' limit="' . $limit_value . '"' : '';
//
//			$offset_value = (int) toolset_getnest( $settings, array( 'offset', 'size' ), '0' );
//			$offset = $offset_value > 0 ? ' offset="' . $offset_value . '"' : '';
//
//			$orderby_value = toolset_getarr( $settings, 'orderby', '' );
//			$orderby = '' !== $orderby_value ? ' orderby="' . $orderby_value . '"' : '';
//
//			$order_value = toolset_getarr( $settings, 'order', '' );
//			$order = '' !== $order_value ? ' order="' . $order_value . '"' : '';
//
//			$secondary_order_by_value = toolset_getarr( $settings, 'secondaryOrderby', '' );
//			$secondary_order_by = '' !== $secondary_order_by_value ? ' orderby_second="' . $secondary_order_by_value . '"' : '';
//
//			$secondary_order_value = toolset_getarr( $settings, 'secondaryOrder', '' );
//			$secondary_order = '' !== $secondary_order_value ? ' order_second="' . $secondary_order_value . '"' : '';
//
//			$html = do_shortcode( '[wpv-view name="' . $view->slug . '" ' . $limit . $offset . $orderby . $order . $secondary_order_by . $secondary_order . ']' );
			// TODO: Re-enable the Toolset View Elementor widget output customization. :show-up:.
			$html = do_shortcode( '[wpv-view name="' . $view->slug . '" ]' );

			$html .= $this->render_module_overlay( $view->title, false );
		} else {
			$html = $this->render_no_view_selected_message( false );
		}

		if ( false !== $echo ) {
			echo $html;
			return true;
		} else {
			return $html;
		}
	}

	/**
	 * Renders the Toolset View Elementor widget output for the widget preview on the editor when no View is selected.
	 *
	 * @param bool $echo Determines if the output will be echo-ed or returned (mainly for unit-testing purposes).
	 *
	 * @return bool|string The markup for the editor widget rendering, when no view is selected, echo-ed or returned.
	 */
	public function render_no_view_selected_message( $echo = true ) {
		$renderer = $this->toolset_renderer;
		$template_repository = Toolset_Output_Template_Repository::get_instance();
		$context = array();
		$html = $renderer->render(
			$template_repository->get( $this->constants->constant( 'Toolset_Output_Template_Repository::PAGE_BUILDER_MODULES_ELEMENTOR_NO_VIEW_SELECTED' ) ),
			$context,
			false
		);

		if ( false !== $echo ) {
			echo $html;
			return true;
		} else {
			return $html;
		}
	}

	/**
	 * Renders the Toolset View Elementor widget overlay for the widget preview on the editor.
	 *
	 * @param string $view_title The title of the selected View.
	 * @param bool $echo Determines if the output will be echo-ed or returned (mainly for unit-testing purposes).
	 *
	 * @return bool|string The markup for the editor widget overlay rendering echo-ed or returned.
	 */
	public function render_module_overlay( $view_title, $echo = true ) {
		$renderer = $this->toolset_renderer;
		$template_repository = Toolset_Output_Template_Repository::get_instance();
		$context = array(
			'module_title' => $view_title,
			'module_type' => __( 'widget', 'wpv-views' ),
		);
		$html = $renderer->render(
			$template_repository->get( $this->constants->constant( 'Toolset_Output_Template_Repository::PAGE_BUILDER_MODULES_OVERLAY' ) ),
			$context,
			false
		);

		if ( false !== $echo ) {
			echo $html;
			return true;
		} else {
			return $html;
		}
	}

	/**
	 * Registers the frontend scripts required for each widget.
	 */
	public function register_frontend_widget_scripts() {
		wp_register_script(
			self::FRONTEND_WIDGET_SCRIPT,
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/toolset-page-builder-modules/modules/elementor/widgets/view/assets/js/view.editor.js',
			array(),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);

		wp_localize_script(
			self::FRONTEND_WIDGET_SCRIPT,
			self::FRONTEND_WIDGET_SCRIPT_LOCALIZATION_OBJECT_NAME,
			array(
				'editViewURL' => admin_url( 'admin.php?page=views-editor&view_id=' ),
				'selectViewFirstMessage' => __( 'Please, select a View first!', 'wpv-views' ),
				'isPreviewMode' => \Elementor\Plugin::$instance->preview->is_preview_mode()
			)
		);
	}

	/**
	 * Registers styles for the widget.
	 */
	public function register_styles() {
		if ( ! wp_style_is( $this->constants->constant( 'Toolset_Assets_Manager::STYLE_ONTHEGOSYSTEMS_ICONS' ), 'registered' ) ) {
			wp_register_style(
				$this->constants->constant( 'Toolset_Assets_Manager::STYLE_ONTHEGOSYSTEMS_ICONS' ),
				$this->constants->constant( 'ON_THE_GO_SYSTEMS_BRANDING_REL_PATH' ) . 'onthegosystems-icons/css/onthegosystems-icons.css',
				array(),
				$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
			);
		}

		wp_register_style(
			self::FRONTEND_WIDGET_STYLE,
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/toolset-page-builder-modules/modules/elementor/widgets/view/assets/css/view.editor.frontend.css',
			array(
				$this->constants->constant( 'Toolset_Assets_Manager::STYLE_ONTHEGOSYSTEMS_ICONS' ),
			),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);

		wp_register_style(
			self::BACKEND_WIDGET_STYLE,
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/toolset-page-builder-modules/modules/elementor/widgets/view/assets/css/view.editor.css',
			array(),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);
	}

	/**
	 * Enqueues the frontend styles required for each widget.
	 */
	public function enqueue_frontend_widget_styles() {
		wp_enqueue_style( self::FRONTEND_WIDGET_STYLE );
	}

	/**
	 * Enqueues the styles required for each widget.
	 */
	public function enqueue_editor_widget_styles() {
		if ( ! wp_style_is( $this->constants->constant( 'Toolset_Assets_Manager::STYLE_ONTHEGOSYSTEMS_ICONS' ), 'registered' ) ) {
			$this->register_styles();
		}

		wp_enqueue_style( $this->constants->constant( 'Toolset_Assets_Manager::STYLE_ONTHEGOSYSTEMS_ICONS' ) );

		wp_enqueue_style( self::BACKEND_WIDGET_STYLE );
	}

	/**
	 * Enqueues the frontend scripts required for each widget.
	 */
	public function enqueue_frontend_widget_scripts() {
		wp_enqueue_script( self::FRONTEND_WIDGET_SCRIPT );
	}

	/**
	 * Filter callback that blacklists the Views widget so that they are not offered through the Elementor sidebar.
	 *
	 * @param array $blacklisted_widgets The array with the widgets to be blacklisted.
	 *
	 * @return array The array with the widgets to be blacklisted.
	 */
	public function blacklist_views_widgets( $blacklisted_widgets ) {
		$blacklisted_widgets[] = 'WPV_Widget';
		$blacklisted_widgets[] = 'WPV_Widget_filter';

		return $blacklisted_widgets;
	}
}
