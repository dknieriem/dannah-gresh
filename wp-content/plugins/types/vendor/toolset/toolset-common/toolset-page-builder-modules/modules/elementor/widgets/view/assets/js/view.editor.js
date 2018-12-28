/**
 * Backend script for the Toolset View Elementor widget.
 *
 * @since 3.0.5
 */

var ToolsetCommon = ToolsetCommon || {};
ToolsetCommon.PageBuilderWidget	= ToolsetCommon.PageBuilderWidget || {};
ToolsetCommon.PageBuilderWidget.Elementor = ToolsetCommon.PageBuilderWidget.Elementor || {};

ToolsetCommon.PageBuilderWidget.Elementor.ViewWidget = function( $ ) {

	var self = this;

	self.onEditViewButtonClick = function( data ) {
		var selectedViewID = data.options.elementSettingsModel.attributes.view || '0';
		if ( '0' !== selectedViewID ) {
			window.open( window.toolsetPageBuilderElementorWidgetViewStrings.editViewURL + selectedViewID , '_blank' );
		} else {
			alert( window.toolsetPageBuilderElementorWidgetViewStrings.selectViewFirstMessage );
		}
	};

	self.attachEvents = function() {
		if ( window.toolsetPageBuilderElementorWidgetViewStrings.isPreviewMode ) {
			elementor.channels.editor.on( 'toolset:pageBuilderWidgets:elementor:editor:editView', self.onEditViewButtonClick );
		}
	};

	self.init = function() {
		self.attachEvents();
	};

	self.init();

};

jQuery( document ).ready( function( $ ) {
	ToolsetCommon.PageBuilderWidget.Elementor.ViewWidget = new ToolsetCommon.PageBuilderWidget.Elementor.ViewWidget( $ );
});