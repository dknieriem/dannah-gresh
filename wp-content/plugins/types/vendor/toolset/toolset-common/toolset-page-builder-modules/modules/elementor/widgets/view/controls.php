<?php

class Toolset_Elementor_View_Widget_Controls {
	private $widget;

	public function __construct( Toolset_Elementor_View_Widget $widget ) {
		$this->widget = $widget;
	}

	public function register_controls() {

		$this->register_view_selection_section();

		// TODO: Re-register the search, query filters and secondary sorting after initial release.
		//$this->register_custom_search_settings_section();

		//$this->register_query_filters_settings_section();

		//$this->register_override_basic_settings_section();

		//$this->register_secondary_sorting_settings_section();
	}

	public function register_view_selection_section() {
		$this->widget->start_controls_section(
			'view_selection_section',
			array(
				'label' => __( 'View selection', 'wpv-views' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->widget->add_control(
			'view',
			array(
				'label' => __( 'View', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'groups' => $this->create_view_select_control_options(),
				'default' => '0',
				'description' => __( 'Select a View to render its preview inside the editor.', 'wpv-views' ),
			)
		);

		$this->widget->add_control(
			'hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->widget->add_control(
			'edit_view_btn',
			array(
				'label' => __( 'Edit selected View in Toolset', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'separator' => 'default',
				'button_type' => 'default',
				'text' => __( 'Edit View', 'wpv-views' ),
				'event' => 'toolset:pageBuilderWidgets:elementor:editor:editView',
				'description' => __( 'Use this button to edit the View in the Views Toolset editor.', 'wpv-views' ),
			)
		);

		$this->widget->end_controls_section();
	}

	public function register_override_basic_settings_section() {
		$this->widget->start_controls_section(
			'override_basic_settings_section',
			array(
				'label' => __( 'Override View basic settings', 'wpv-views' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->widget->add_control(
			'limit',
			array(
				'label' => __( 'Limit', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'description' => __( 'Get only some results. 0 means no limit.', 'wpv-views' ),
				'size_units' => array( 'px' ), // This widget control is meant for sizes. We are using it here by ignoring the unit.
				'range' => array(
					'value' => array(
						'min' => 0,
						'max' => 999,
					),
				),
				'default' => array(
					'unit' => 'px', // This widget control is meant for sizes. We are using it here by ignoring the unit.
					'size' => 0,
				),
			)
		);

		$this->widget->add_control(
			'offset',
			array(
				'label' => __( 'Offset', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'description' => __( 'Skip some results. 0 means skip nothing.', 'wpv-views' ),
				'size_units' => array( 'px' ), // This widget control is meant for sizes. We are using it here by ignoring the unit.
				'range' => array(
					'value' => array(
						'min' => 0,
						'max' => 999,
					),
				),
				'default' => array(
					'unit' => 'px', // This widget control is meant for sizes. We are using it here by ignoring the unit.
					'size' => 0,
				),
			)
		);

		$this->widget->add_control(
			'orderby',
			array(
				'label' => __( 'Order by', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'ID, date, author, title, post_type or field-slug', 'wpv-views' ),
				'description' => __( 'Change how the results will be ordered.', 'wpv-views' ) . ' ' . __( 'You can sort by a custom field simply using the value field-xxx where xxx is the custom field slug.', 'wpv-views' ),
			)
		);

		$this->widget->add_control(
			'order',
			array(
				'label' => __( 'Order', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default setting', 'wpv-views' ),
					'asc' => __( 'Ascending', 'wpv-views' ),
					'desc' => __( 'Descending', 'wpv-views' ),
				),
				'default' => 'default',
				'description' => __( 'Change the order of the results.', 'wpv-views' ),
			)
		);

		$this->widget->end_controls_section();
	}

	public function register_secondary_sorting_settings_section() {
		$this->widget->start_controls_section(
			'secondary_sorting_settings_section',
			array(
				'label' => __( 'Secondary sorting', 'wpv-views' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->widget->add_control(
			'secondaryOrderby',
			array(
				'label' => __( 'Secondary order by', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'No secondary sorting', 'wpv-views' ),
					'post_date' => __( 'Post date', 'wpv-views' ),
					'post_title' => __( 'Post title', 'wpv-views' ),
					'ID' => __( 'ID', 'wpv-views' ),
					'post_author' => __( 'Post author', 'wpv-views' ),
					'post_type' => __( 'Post type', 'wpv-views' ),
				),
				'default' => 'default',
				'description' => __( 'Change how the results that share the same value on the orderby setting will be ordered.', 'wpv-views' ),
			)
		);

		$this->widget->add_control(
			'secondaryOrder',
			array(
				'label' => __( 'Order', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default setting', 'wpv-views' ),
					'asc' => __( 'Ascending', 'wpv-views' ),
					'desc' => __( 'Descending', 'wpv-views' ),
				),
				'default' => 'default',
				'description' => __( 'Change the secondary order of the results.', 'wpv-views' ),
			)
		);

		$this->widget->end_controls_section();
	}

	public function register_custom_search_settings_section() {
		$this->widget->start_controls_section(
			'custom_search_settings_section',
			array(
				'label' => __( 'Custom Search', 'wpv-views' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

//		global $WP_Views;
//		$view_id = toolset_getarr( $this->widget->initial_settings, 'view', '0' );
//		$has_parametric_search = $WP_Views->does_view_have_form_controls( $view_id );
//
//		$this->widget->add_control(
//			'has_parametric_search',
//			[
//				'label' => __( 'Has parametric search', 'plugin-domain' ),
//				'type' => \Elementor\Controls_Manager::HIDDEN,
//				'default' => ( $has_parametric_search ) ? 'true' : 'false',
//			]
//		);

		$this->widget->add_control(
			'custom_search_include',
			array(
				'label' => __( 'What do you want to include here?', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'full' => __( 'Both the search form and results', 'wpv-views' ),
					'form' => __( 'Only the search form', 'wpv-views' ),
					'results' => __( 'Only the search results', 'wpv-views' ),
				),
				'default' => 'full',
				'description' => __( 'The first option will display the full View.', 'wpv-views' ) . ' ' .
				                 __( 'The second option will display just the form, you can then select where to display the results.', 'wpv-views' ) . ' ' .
				                 __( 'Finally, the third option will display just the results, you need to add the form elsewhere targeting this page.', 'wpv-views' ),
			)
		);

		$this->widget->add_control(
			'where_to_display_results',
			array(
				'label' => __( 'Where do you want to display the results?', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'samePage' => __( 'In other place on this same page', 'wpv-views' ),
					'otherPage' => __( 'On another page', 'wpv-views' ),
				),
				'default' => 'samePage',
				'condition' => [
					'custom_search_include' => 'form',
				],
			)
		);

		$this->widget->add_control(
			'display_results_in',
			array(
				'label' => __( 'Where do you want to display the results?', 'wpv-views' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => array(
					'page1' => __( 'Page1', 'wpv-views' ),
					'page2' => __( 'Page2', 'wpv-views' ),
					'page3' => __( 'Page3', 'wpv-views' ),
				),
				'default' => '',
				'condition' => [
					'custom_search_include' => 'form',
				],
			)
		);

		$this->widget->add_control(
			'no_submit_button_warning',
			array(
				'label' => __( 'Warning', 'wpv-views' ),
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'The form in this View does not have a submit button, so you can only display the results on this same page.', 'wpv-views' ),
				'content_classes' => 'elementor-widget-toolset-view-warning',
				'condition' => array(
					'custom_search_include' => array( 'form' ),
				),
			)
		);

		$this->widget->add_control(
			'only_search_results_warning',
			array(
				'label' => __( 'Warning', 'wpv-views' ),
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					         __( 'You are only displaying the %1s in this widget.', 'wpv-views' ) . ' ',
					         sprintf(
						         '<strong>%1s</strong>',
						         __( 'search results', 'wpv-views' )
					         )
				         ) .
				         sprintf(
					         __( 'A custom search should have the %1s and %2s.', 'wpv-views' ) . ' ',
					         sprintf(
						         '<strong>%1s</strong>',
						         __( 'search results', 'wpv-views' )
					         ),
					         sprintf(
						         '<strong>%1s</strong>',
						         __( 'search form', 'wpv-views' )
					         )
				         ) .
				         sprintf(
					         __( 'To display the %1s you need to:', 'wpv-views' ) . ' ',
					         sprintf(
						         '<strong>%1s</strong>',
						         __( 'search form', 'wpv-views' )
					         )
				         ) .
				         '<ul>' .
				         '<li>' . __( 'Create a different View block and display this View.', 'wpv-views' ) . '</li>' .
				         '<li>' . sprintf(
					         __( 'Choose to display the %1s.', 'wpv-views' ) . ' ',
					         sprintf(
						         '<strong>%1s</strong>',
						         __( 'search form', 'wpv-views' )
					         )
				         ) . '</li>' .
				         '</ul>',
				'content_classes' => 'elementor-widget-toolset-view-warning',
				'condition' => array(
					'custom_search_include' => array( 'results' ),
				),
			)
		);

		$this->widget->end_controls_section();
	}

	public function register_query_filters_settings_section() {
		$this->widget->start_controls_section(
			'query_filters_settings_section',
			array(
				'label' => __( 'Query filters', 'wpv-views' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->widget->end_controls_section();
	}

	public function create_view_select_control_options() {
		$view_select_control_options = array(
			'0' => __( 'Select a View', 'wpv-views' ),
		);

		$published_views = apply_filters( 'wpv_get_available_views', array() );

		$available_view_types = array(
			'posts',
			'taxonomy',
			'users',
		);

		foreach ( $available_view_types as $view_type ) {
			if ( count( $published_views[ $view_type ] ) > 0 ) {
				$group = array(
					'label' => __( ucfirst( $view_type ), 'wpv-views' ),
					'options' => array(),
				);

				foreach ( $published_views[ $view_type ] as $view ) {
					$group['options'][ $view->ID ] = $view->post_title;
				}

				$view_select_control_options[ $view_type ] = $group;
			}
		}

		return $view_select_control_options;
	}
}
