<?php
/**
 * Class Toolset_Page_Builder_Modules_Elementor
 *
 * Handles the registration of all the Toolset Elementor Widgets.
 *
 * @since 3.0.5
 */
class Toolset_Page_Builder_Modules_Elementor {
	/**
	 * Minimum Elementor Version
	 *
	 * @var string Minimum Elementor version required to run the widget.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	const PAGE_BUILDER_NAME = 'elementor';

	private $toolset_elementor_widgets;

	/**
	 * Initializes the Toolset Page Builder Module integration for the Elementor page builder.
	 */
	public function initialize() {
		// Check if Elementor installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Check for required Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'init_hooks' ) );
	}

	/**
	 * Checks that Elementor is loaded and is on the proper version to register the Toolset Elementor widgets.
	 */
	public function init_hooks() {
		// Register Widget Scripts
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_widget_scripts' ) );

		// Enqueue Widget Scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_frontend_widget_scripts' ) );

		// Εnqueue Widget Styles
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_widget_styles' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_frontend_widget_styles' ) );

		// Register widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

		// Initialize widgets.
		$this->initialize_widgets();
	}

	/**
	 * Initilizes the Toolset Elementor page builder widgets.
	 */
	public function initialize_widgets() {
		$toolset_view_elementor_widget = new \Toolset_Elementor_View_Widget();
		$toolset_view_elementor_widget->init();
		$this->toolset_elementor_widgets['view'] = $toolset_view_elementor_widget;
	}

	/**
	 * Register the Toolset Elementor widgets.
	 *
	 * @throws Exception
	 */
	public function register_widgets() {
		foreach ( $this->toolset_elementor_widgets as $widget ) {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( $widget );
		}
	}

	/**
	 * Registers the frontend scripts required for each widget.
	 */
	public function register_frontend_widget_scripts() {
		foreach ( $this->toolset_elementor_widgets as $widget ) {
			if (
				method_exists( $widget, 'register_frontend_widget_scripts' ) &&
				is_callable( array( $widget, 'register_frontend_widget_scripts' ) )
			) {
				$widget->register_frontend_widget_scripts();
			}
		}
	}

	/**
	 * Enqueues the styles required for each widget.
	 */
	public function enqueue_editor_widget_styles() {
		foreach ( $this->toolset_elementor_widgets as $widget ) {
			if (
				method_exists( $widget, 'enqueue_editor_widget_styles' ) &&
				is_callable( array( $widget, 'enqueue_editor_widget_styles' ) )
			) {
				$widget->enqueue_editor_widget_styles();
			}
		}
	}

	/**
	 * Enqueues the frontend styles required for each widget.
	 */
	public function enqueue_frontend_widget_styles() {
		foreach ( $this->toolset_elementor_widgets as $widget ) {
			if (
				method_exists( $widget, 'enqueue_frontend_widget_styles' ) &&
				is_callable( array( $widget, 'enqueue_frontend_widget_styles' ) )
			) {
				$widget->enqueue_frontend_widget_styles();
			}
		}
	}

	/**
	 * Enqueues the frontend scripts required for each widget.
	 */
	public function enqueue_frontend_widget_scripts() {
		foreach ( $this->toolset_elementor_widgets as $widget ) {
			if ( method_exists( $widget, 'enqueue_frontend_widget_scripts' ) ) {
				$widget->enqueue_frontend_widget_scripts();
			}
		}
	}
}