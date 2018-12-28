var WPViews = WPViews || {};

/**
 * Address autocomplete component. Uses Azure API.
 *
 * @param jQuery $
 * @constructor
 * @since 1.5
 */
WPViews.MapsAddressAutocomplete = function( $ ) {
	"use strict";

	let self = this;

	/** @var {Object} toolset_maps_address_autocomplete_i10n */

	const addressAutocompleteSelector = '.js-toolset-maps-address-autocomplete';
	const autocompleteInitedSelector = '.ui-autocomplete-input';
	const autocompleteSettings = {
		source: function (request, response) {
			$.get({
				url: "https://atlas.microsoft.com/search/address/json",
				dataType: "jsonp",
				data: {
					'subscription-key': toolset_maps_address_autocomplete_i10n.azure_api_key,
					'api-version': '1.0',
					typeahead: true,
					query: request.term
				},
				success: function (data) {
					let addresses = _.pluck(data.results, 'address');
					let freeFormAddresses = _.pluck(addresses, 'freeformAddress');

					response(freeFormAddresses);
				}
			});
		},
		minLength: 2
	};

	/**
	 * Inits all the events for this module
	 */
	self.initEvents = function() {
		/**
		 * Init address field(s) on toolset_ajax_fields_loaded event.
		 */
		$( document ).on( 'toolset_ajax_fields_loaded', function( event, form ) {
			self.initFieldsInsideContainer( $( 'form#' + form.form_id ) );
		});

		/**
		 * Reacts to event triggered by Types after the fields are loaded by ajax (too late for regular init).
		 */
		$( document ).on( 'toolset_types_rfg_item_toggle', function( event, item ) {
			if ( item.visible() ) {
				self.initAllFields();
			}
		});

		// Adds autocomplete to an added repetitive field
		$( document ).on( 'toolset_repetitive_field_added', function( event, $parent ) {
			self.initFieldsInsideContainer( $parent );
		})
	};

	/**
	 * Init just the given field
	 * @param jQuery $field
	 */
	self.initField = function( $field ) {
		if ( ! $field.hasClass( autocompleteInitedSelector ) ) {
			$field.autocomplete( autocompleteSettings );
		}
	};

	/**
	 * Init all uninited fields inside given container
	 * @param jQuery $container
	 */
	self.initFieldsInsideContainer = function( $container ) {
		$( $container )
			.find( addressAutocompleteSelector )
			.not( autocompleteInitedSelector )
			.autocomplete( autocompleteSettings );
	};

	/**
	 * Inits all the fields with addressAutocompleteSelector class
	 */
	self.initAllFields = function() {
		$( addressAutocompleteSelector )
			.not( autocompleteInitedSelector )
			.autocomplete( autocompleteSettings );
	};

	self.init = function() {
		self.initAllFields();
		self.initEvents()
	};

	self.init();
};

jQuery( document ).ready( function( $ ) {
	WPViews.mapsAddressAutocomplete = new WPViews.MapsAddressAutocomplete( $ );
});