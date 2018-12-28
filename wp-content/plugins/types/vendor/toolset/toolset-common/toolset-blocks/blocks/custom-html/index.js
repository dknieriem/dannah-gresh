/**
 * Block dependencies
 //  */
import classnames from 'classnames';
import './styles/editor.scss';

/**
 * Internal block libraries
 */
const {
	__,
} = wp.i18n;

const {
	addFilter,
} = wp.hooks;

const {
	BlockControls,
} = wp.editor;

const {
	createElement,
} = wp.element;

addFilter(
	'blocks.BlockEdit',
	'toolset/extend-html',
	( BlockEdit ) => {
		const modifyCustomHTMLBlock = ( props ) => {
			const clonedProps = Object.assign( {}, props, { key: 'toolset-extended-html-block' } );

			let element = createElement( BlockEdit, clonedProps );
			if (
				(
					props.focus ||
					props.isSelected
				) &&
				'core/html' === props.name
			) {
				const fieldsAndViewsButton = 'undefined' !== typeof window.WPViews && <BlockControls key="toolset-controls-views">
					<div className={ classnames( 'components-toolbar' ) }>
						<button
							className={ classnames( 'components-button wpv-block-button' ) }
							onClick={ ( e ) => {
								window.wpcfActiveEditor = 'toolset-extended-html-' + props.id;

								const customHtmlTextArea = e.target.closest( '.editor-block-contextual-toolbar' ).nextSibling.querySelector( 'textarea' );
								if ( customHtmlTextArea ) {
									// Add an id to the Custom HTML text area to use it when inserting the Fields and Views shortcode.
									customHtmlTextArea.id = window.wpcfActiveEditor;
									// Open the Fields and Views dialog
									window.WPViews.shortcodes_gui.open_fields_and_views_dialog();
								}
							} }>
							<i className={ classnames( 'icon-views-logo', 'fa', 'fa-wpv-custom', 'ont-icon-18', 'ont-color-gray' ) }></i>
							<span> { __( 'Fields and Views' ) }</span>
						</button>
					</div>
				</BlockControls>;

				const credFormsButton = 'undefined' !== typeof window.CRED && <BlockControls key="toolset-controls-cred">
					<div className={ classnames( 'components-toolbar' ) }>
						<button
							className={ classnames( 'components-button wpv-block-button' ) }
							onClick={ ( e ) => {
								window.wpcfActiveEditor = 'toolset-extended-html-' + props.id;

								const customHtmlTextArea = e.target.closest( '.editor-block-contextual-toolbar' ).nextSibling.querySelector( 'textarea' );
								if ( customHtmlTextArea ) {
									// Add an id to the Custom HTML text area to use it when inserting the CRED forms shortcode.
									customHtmlTextArea.id = window.wpcfActiveEditor;
									// Open the CRED Forms dialog
									window.Toolset.CRED.shortcodeGUI.openCredDialog();
								}
							} }>
							<i className={ classnames( 'icon-cred-logo', 'fa', 'fa-wpv-custom', 'ont-icon-18', 'ont-color-gray' ) }></i>
							<span> { __( 'Toolset Forms' ) }</span>
						</button>
					</div>
				</BlockControls>;

				element = [ element, fieldsAndViewsButton, credFormsButton ];
			}
			return element;
		};

		return modifyCustomHTMLBlock;
	}
);
