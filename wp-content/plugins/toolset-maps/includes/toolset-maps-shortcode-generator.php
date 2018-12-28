<?php
/**
 * Shortcode generator for Toolset Maps, using Toolset Common.
 * @since 1.5
 */
class Toolset_Maps_Shortcode_Generator extends Toolset_Shortcode_Generator {
	const SCRIPT_MAPS_SHORTCODE = 'maps-shortcode';

	protected $doing_ajax = false;
	/** @var Toolset_Addon_Maps_Views using some of the stuff from this class until everything is ported */
	protected $maps_views;

	public function __construct( Toolset_Addon_Maps_Views $maps_views ) {
		$this->maps_views = $maps_views;
	}

	/**
	 * Initialize the Maps shortcodes generator.
	 * 
	 * This is run at init, so most of the things can be done directly.
	 *
	 * @since 1.5
	 */
	public function initialize() {
		$this->doing_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );

		if ( ! $this->doing_ajax ) {
			add_action( 'wpv_action_collect_shortcode_groups', array( $this, 'register_maps_group' ), 10 );

			// Register shortcode dialogs assets
			// (on wp_loaded:20, because initialize() is called on init:10
			// and this needs to run both backend and frontend)
			add_action( 'wp_loaded',            array( $this, 'register_assets' ) );
			add_action( 'wp_enqueue_scripts',    array( $this, 'maybe_enqueue_assets' ), 99 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ), 99 );
		}

		add_action( 'wp_ajax_toolset_maps_select2_suggest_meta', array( $this, 'select2_suggest_meta' ) );
		add_action( 'wp_ajax_nopriv_toolset_maps_select2_suggest_meta', array( $this, 'select2_suggest_meta' ) );
	}

	public function register_maps_group() {
		$group_id = 'toolset-maps';
		$group_data = array(
			'name' => __( 'Toolset Maps', 'toolset-maps' ),
			'fields' => array(
				'marker' => array(
					'name' => __( 'Marker', 'toolset-maps' ),
					'shortcode' => 'wpv-map-marker',
					'callback' =>
						"Toolset.Maps.shortcodeManager.shortcodeDialogOpen({title:'Marker',shortcode:'wpv-map-marker'})"
				)
			)
		);

		do_action( 'wpv_action_register_shortcode_group', $group_id, $group_data );
	}

	// I assume that at some point we will deprecate views-addon-maps-dialogs-script
	// or most of it at least?
	public function register_assets() {
		$toolset_assets_manager = Toolset_Assets_Manager::get_instance();

		$toolset_assets_manager->register_script(
			self::SCRIPT_MAPS_SHORTCODE,
			TOOLSET_ADDON_MAPS_URL_JS . 'maps_shortcode.js',
			array(
				'jquery',
				'underscore',
				Toolset_Assets_Manager::SCRIPT_TOOLSET_SHORTCODE,
				'views-addon-maps-dialogs-script'
			),
			TOOLSET_ADDON_MAPS_VERSION,
			true
		);

		$maps_shortcode_i18n = array(
			'action'	   => array(
				'insert'   => __( 'Insert shortcode', 'toolset-maps' ),
				'create'   => __( 'Create shortcode', 'toolset-maps' ),
				'update'   => __( 'Update shortcode', 'toolset-maps' ),
				'close'    => __( 'Close', 'toolset-maps' ),
				'cancel'   => __( 'Cancel', 'toolset-maps' ),
				'back'     => __( 'Back', 'toolset-maps' ),
				'previous' => __( 'Previous step', 'toolset-maps' ),
				'next'     => __( 'Next step', 'toolset-maps' ),
				'save'     => __( 'Save settings', 'toolset-maps' ),
				'loading'  => __( 'Loading...', 'toolset-maps' ),
				'wizard'   => __( 'Show me how', 'toolset-maps' ),
				'got_it'   => __( 'Got it!', 'toolset-maps' )
			),
			'attributes'    => $this->get_fields_expected_attributes(),
			'counters'      => $this->get_counters(),
			'data' => array(
				'geolocation' => array(
					'enabled' => $this->maps_views->is_frontend_served_over_https()
				),
				'm2m' => array(
					'enabled' => apply_filters( 'toolset_is_m2m_enabled', false )
				)
			)
		);
		$toolset_assets_manager->localize_script(
			self::SCRIPT_MAPS_SHORTCODE,
			'maps_shortcode_i18n',
			$maps_shortcode_i18n
		);
	}

	public function maybe_enqueue_assets() {
		if ( wp_script_is( 'views-shortcodes-gui-script', 'enqueued' ) ) {
			do_action( 'toolset_enqueue_scripts', array( self::SCRIPT_MAPS_SHORTCODE ) );
		}
	}

	protected function get_fields_expected_attributes() {
		// Don't show the marker dialog if API key is not set.
		if ( ! $this->maps_views->is_api_key_set() ) {
			return array(
				'marker' => array(
					'marker' => array(
						'header' => __( 'Missing an API key', 'toolset-maps' ),
						'fields' => array(
							'information' => array(
								'type'      => 'information',
								'content'   => $this->maps_views->get_missing_api_key_warning()
							)
						)
					)
				)
			);
		}

		$saved_options = apply_filters( 'toolset_filter_toolset_maps_get_options', array() );
		$map_id = $saved_options['map_counter'];
		$next_marker_id = $saved_options['marker_counter'] + 1;
		$analytics_strings = array(
			'utm_source'	=> 'toolsetmapsplugin',
			'utm_campaign'	=> 'toolsetmaps',
			'utm_medium'	=> 'marker-shortcode-dialog',
			'utm_term'		=> 'Learn about marker popups'
		);
		$rich_content_documentation_link = '<a href="'
           . Toolset_Addon_Maps_Common::get_documentation_promotional_link( array( 'query' => $analytics_strings, 'anchor' => 'marker-title-and-popup' ) )
           . '" target="_blank" title="'
           . esc_attr( __( 'Learn about marker popups', 'toolset-maps' ) )
           . '">'
           . esc_html( __( 'Learn how to display rich content in the marker popups »', 'toolset-maps' ) )
           . '</a>';
		$custom_markers_documentation_link = '<a href="'
            . Toolset_Addon_Maps_Common::get_documentation_promotional_link( array( 'query' => $analytics_strings, 'anchor' => 'marker-icon' ) )
            . '" target="_blank" title="'
            . esc_attr( __( 'Learn about using custom markers', 'toolset-maps' ) )
            . '">'
			. esc_html( __( 'Learn about using custom markers', 'toolset-maps' ) . ' »' )
			.'</a>';

		return array(
			'marker' => array(
				'marker' => array(
					'header' => __( 'Marker', 'toolset-maps' ),
					'fields' => array(
						'map_id' => array(
							'label'             => __( 'Map ID - required', 'toolset-maps' ),
							'type'              => 'text',
							'description'       => __(
								'This is the unique identifier for the map that this marker belongs to.', 'toolset-maps'
							),
							'required'          => true,
							'defaultForceValue' => 'map-' . $map_id
						),
						'marker_id' => array(
							'label'		        => __( 'Marker ID - required', 'toolset-maps' ),
							'type'		        => 'text',
							'description'       => __( 'This is the marker unique identifier.', 'toolset-maps' ),
							'required'	        => true,
							'defaultForceValue' => 'marker-' . $next_marker_id
						),
						'marker_source' => array(
							'label' => __( 'Source of the marker', 'toolset-maps' ),
							'type' => 'group',
							'required' => true,
							'fields' => array(
								'marker_source_options' => array(
									'type' => 'radio',
									'options' => $this->get_marker_source_options(),
									'defaultForceValue' => 'address',
								),
								'marker_source_meta' => array(
									'type' => 'text',
									'defaultValue' => 'placeholder'
								)
							)
						),
						'address'   => array(
							'type'          => 'text',
							'placeholder'   => __( 'Type your address here', 'toolset-maps' ),
							'required'      => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'address'
								)
							)
						),
						'postmeta'  => array(
							'type'          => 'select',
							'hidden'        => true,
							'options'       => $this->get_post_field_options(),
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'postmeta',
								),
							),
						),
						'postmeta_id'   => array(
							'pseudolabel'   => __( 'From this post', 'toolset-maps' ),
							'type'          => 'postSelector',
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'postmeta',
								),
							),
						),
						'termmeta'  => array(
							'type'          => 'select',
							'options'       => $this->get_term_field_options(),
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'termmeta',
								),
							),
						),
						'termmeta_id' => array(
							'pseudolabel'   => __( 'From this taxonomy term', 'toolset-maps' ),
							'type'          => 'typesViewsTermSelector',
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'termmeta',
								),
							)
						),
						'usermeta'  => array(
							'type'          => 'select',
							'options'       => $this->get_user_field_options(),
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'usermeta',
								),
							),
						),
						'usermeta_id' => array(
							'pseudolabel'   => __( 'From this user', 'toolset-maps' ),
							'type'          => 'userSelector',
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'usermeta',
								),
							)
						),
						'lat' => array(
							'type'          => 'text',
							'placeholder'   => __( 'Latitude', 'toolset-maps' ),
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'latlon'
								)
							)
						),
						'lon' => array(
							'type'          => 'text',
							'placeholder'   => __( 'Longitude', 'toolset-maps' ),
							'hidden'        => true,
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'latlon'
								)
							)
						),
						'map_render' => array(
							'pseudolabel'   => __( 'Geolocation options', 'toolset-maps' ),
							'type'          => 'radio',
							'options'       => array(
								'immediate' => __(
									'Render the map immediately and then add visitor location',
									'toolset-maps'
								),
								'wait'      => __(
									'Wait until visitors share their location and only then render the map',
									'toolset-maps'
								)
							),
							'hidden'        => true,
							'defaultValue' => 'immediate',
							'dependsOn'     => array(
								array(
									'key'   => 'marker_source_options',
									'value' => 'browser_geolocation'
								)
							)
						)
					)
				),
				'markerData' => array(
					'header' => __( 'Marker data', 'toolset-maps' ),
					'fields' => array(
						'marker_title' => array(
							'label' => __( 'Text to display when hovering over the marker', 'toolset-maps' ),
							'type'	=> 'text',
						),
						'content' => array(
							'label' => __( 'Popup content', 'toolset-maps' ),
							'type' => 'content',
							'description' => __(
								'This will be displayed as a popup when someone clicks on the marker. You can add HTML '
								.'and shortcodes here.',
								'toolset-maps'
							),
						),
						'information' => array(
							'type'      => 'information',
							'content'   => $rich_content_documentation_link
						)
					)
				),
				'markerIcons' => array(
					'header' => __( 'Marker icons', 'toolset-maps' ),
					'fields' => array(
						'_icons_settings' => array(
							'label' 		=> __( 'Use the icons settings from the map', 'toolset-maps' ),
							'type'			=> 'radio',
							'options'		=> array(
								'yes' 		=> __( 'Yes', 'toolset-maps' ),
								'no'		=> __( 'No, use other icons', 'toolset-maps' )
							),
							'defaultValue' 	=> 'yes'
						),
						'marker_icon' => array(
							'label'     => __( 'Icon for this marker', 'toolset-maps'),
							'type'      => 'radio',
							'options'   => $this->maps_views->get_marker_options(),
							'hidden'    => true,
							'dependsOn' => array(
								array(
									'key'   => '_icons_settings',
									'value' => 'no',
								),
							),
						),
						'_use_same_icon' => array(
							'label'     => __( 'Icon when hovering this marker', 'toolset-maps'),
							'type'      => 'radio',
							'options'   => array(
								'same'  => __( 'Use the same marker icon', 'toolset-maps' ),
								'other' => __( 'Use a different marker icon', 'toolset-maps' ),
							),
							'defaultValue' => 'same',
							'hidden'    => true,
							'dependsOn' => array(
								array(
									'key'   => '_icons_settings',
									'value' => 'no',
								),
							),
						),
						'marker_icon_hover' => array(
							'type'      => 'radio',
							'options'   => $this->maps_views->get_marker_options(),
							'hidden'    => true,
							'dependsOn' => array(
								array(
									'key'   => '_icons_settings',
									'value' => 'no',
								),
								array(
									'key'   => '_use_same_icon',
									'value' => 'other'
								)
							),
						),
						'information' => array(
							'type'      => 'information',
							'content'   => $custom_markers_documentation_link
						)
					)
				)
			)
		);
	}

	protected function get_marker_source_options() {
		$options = array();

		$options['address'] = __( 'A specific address', 'toolset-maps' );
		$options['postmeta'] = __( 'A post field storing an address', 'toolset-maps' );
		$options['termmeta'] = __( 'A taxonomy field storing an address', 'toolset-maps' );
		$options['usermeta'] = __( 'An user field storing an address', 'toolset-maps' );
		$options['latlon'] = __( 'A pair of latitude and longitude coordinates', 'toolset-maps' );
		$options['browser_geolocation'] = __( 'The location of the current visitor', 'toolset-maps');

		return $options;
	}

	/**
	 * @return array
	 */
	protected function get_counters() {
		$saved_options = apply_filters( 'toolset_filter_toolset_maps_get_options', array() );

		return array(
			'map' => $saved_options['map_counter'],
			'marker' => $saved_options['marker_counter']
		);
	}

	/**
	 * Makes a key-value array from types meta array suitable for select options in shortcode generator.
	 *
	 * @param array $types_meta_fields
	 *
	 * @return array
	 */
	protected function types_meta_2_options( array $types_meta_fields ) {
		$options = array();

		foreach ( $types_meta_fields as $postmeta_field ) {
			$options[$postmeta_field['meta_key']] = $postmeta_field['name'];
		}

		return $options;
	}

	public function get_field_options( $meta_type ) {
		switch ( $meta_type ) {
			case 'post':
				return $this->get_post_field_options();
				break;
			case 'term':
				return $this->get_term_field_options();
				break;
			case 'user':
				return $this->get_user_field_options();
				break;
		}

		return array();
	}

	protected function get_post_field_options() {
		return $this->types_meta_2_options(
			apply_filters( 'toolset_filter_toolset_maps_get_types_postmeta_fields', array() )
		);
	}

	protected function get_term_field_options() {
		return $this->types_meta_2_options(
			apply_filters( 'toolset_filter_toolset_maps_get_types_termmeta_fields', array() )
		);
	}

	protected function get_user_field_options() {
		return $this->types_meta_2_options(
			apply_filters( 'toolset_filter_toolset_maps_get_types_usermeta_fields', array() )
		);
	}

	protected function are_there_any_post_fields() {
		$fields = apply_filters( 'toolset_filter_toolset_maps_get_types_postmeta_fields', array() );
		return ( ! empty( $fields ) );
	}

	protected function are_there_any_term_fields() {
		$fields = apply_filters( 'toolset_filter_toolset_maps_get_types_termmeta_fields', array() );
		return ( ! empty( $fields ) );
	}

	protected function are_there_any_user_fields() {
		$fields = apply_filters( 'toolset_filter_toolset_maps_get_types_usermeta_fields', array() );
		return ( ! empty( $fields ) );
	}

	public function select2_suggest_meta() {

		$meta_type = toolset_getpost( 'metaType' );
		if ( ! in_array( $meta_type, array( 'post', 'term', 'user' ) ) ) {
			wp_send_json_error( array( 'message' => __( 'Wrong meta type', 'toolset-maps' ) ) );
		}

		$output = array();
		$cached = array();
		$gathered = array();

		$types_addres_fields = $this->get_field_options( $meta_type );

		if ( ! empty( $types_addres_fields ) ) {
			$output['cached'] = array(
				'text' => __( 'Types address fields', 'toolset-maps' ),
				'children' => array()
			);

			foreach ( $types_addres_fields as $field_key => $field_label ) {
				$cached[] = array(
					'text' => $field_label,
					'id' => $field_key,
				);
			}
		}

		$search = toolset_getpost( 's' );
		if ( empty( $search ) ) {
			if ( ! empty( $cached ) ) {
				wp_send_json_success( $cached );
			}
			wp_send_json_error( array( 'message' => __( 'Missing search term', 'toolset-maps' ) ) );
		}

		global $wpdb;

		if ( method_exists( $wpdb, 'esc_like' ) ) { 
			$search = '%' . $wpdb->esc_like( $search ) . '%'; 
		} else { 
			$search = '%' . like_escape( esc_sql( $search ) ) . '%'; 
		}

		$table = null;

		switch ( $meta_type ) {
			case 'post':
				$table = $wpdb->postmeta;
				$key = 'meta_key';
				break;
			case 'term':
				$table = $wpdb->termmeta;
				$key = '';
				break;
			case 'user':
				$table = $wpdb->usermeta;
				break;
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key FROM {$table} 
				WHERE meta_key LIKE %s 
				LIMIT 0, 10",
				$search
			)
		);

		if ( 
			isset( $results ) 
			&& ! empty( $results ) 
		) {
			
			if ( is_array( $results ) ) {
				$output['gathered'] = array(
					'text' => __( 'Search results', 'toolset-maps' ),
					'children' => array()
				);

				foreach ( $results as $result ) {
					$gathered[] = array(
						'text' => toolset_getarr( $types_addres_fields, $result->meta_key, $result->meta_key ),
						'id' => $result->meta_key,
					);
				}
			}
		}

		if (
			! empty( $cached )
			&& ! empty( $gathered )
		) {
			$output['cached']['children'] = $cached;
			$output['gathered']['children'] = $gathered;

			$output = array_values( $output );

			wp_send_json_success( $output );
		}

		if ( ! empty( $cached ) ) {
			wp_send_json_success( $cached );
		}

		if ( ! empty( $gathered ) ) {
			wp_send_json_success( $gathered );
		}

		wp_send_json_error( array( 'message' => __( 'No fields to show', 'toolset-maps' ) ) );

	}
}
