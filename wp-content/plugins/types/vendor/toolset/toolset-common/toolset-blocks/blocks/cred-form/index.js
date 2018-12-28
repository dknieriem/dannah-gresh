/**
 * Handles the creation and the behavior of the Toolset Content Template block.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import classnames from 'classnames';
import icon from './icon';
import Inspector from './inspector/inspector';
import CREDFormSelect from './inspector/cred-form-select';
import CREDFormPreview from './cred-form-preview';
import './styles/editor.scss';

/**
 * Internal block libraries
 */
const {
	__,
} = wp.i18n;

const {
	registerBlockType,
} = wp.blocks;

const {
	Placeholder,
} = wp.components;

const {
	RawHTML,
} = wp.element;

const name = window.toolset_cred_form_block_strings.block_name;

const settings = {
	title: __( 'Toolset Form' ),
	description: __( 'Add a Toolset Form to the editor.' ),
	category: 'widgets',
	icon: icon.blockIcon,
	keywords: [
		__( 'Toolset' ),
		__( 'Form' ),
		__( 'Shortcode' ),
	],

	edit: props => {
		const onChangeCredForm = ( event ) => {
			const formInfo = JSON.parse( event.target.value );
			props.setAttributes( { form: event.target.value } );
			props.setAttributes( { formType: formInfo.formType } );
			props.setAttributes( { formAction: formInfo.formAction } );
		};

		const onChangePostToEdit = ( value ) => {
			props.setAttributes( { postToEdit: value } );
		};

		const onChangeAnotherPostToEdit = ( value ) => {
			props.setAttributes( { anotherPostToEdit: value } );
		};

		const onChangeUserToEdit = ( value ) => {
			props.setAttributes( { userToEdit: value } );
		};

		const onChangeAnotherUserToEdit = ( value ) => {
			if ( null === value ) {
				value = '';
			}
			props.setAttributes( { anotherUserToEdit: value } );
		};

		const assignFormTypeAndAction = ( forms, type, action ) => {
			forms.map( ( item ) => {
				item.formType = type;
				item.formAction = action;
				return item;
			} );

			return forms;
		};

		const newPostForms = assignFormTypeAndAction( window.toolset_cred_form_block_strings.published_forms.postForms.new, 'post', 'new' );
		const editPostForms = assignFormTypeAndAction( window.toolset_cred_form_block_strings.published_forms.postForms.edit, 'post', 'edit' );
		const newUserForms = assignFormTypeAndAction( window.toolset_cred_form_block_strings.published_forms.userForms.new, 'user', 'new' );
		const editUserForms = assignFormTypeAndAction( window.toolset_cred_form_block_strings.published_forms.userForms.edit, 'user', 'edit' );

		return [
			!! (
				props.focus ||
				props.isSelected
			) && (
				<Inspector
					key="wpv-gutenberg-cred-form-block-render-inspector"
					className={ classnames( 'wp-block-toolset-cred-form-inspector' ) }
					attributes={
						{
							newPostForms: newPostForms,
							editPostForms: editPostForms,
							newUserForms: newUserForms,
							editUserForms: editUserForms,
							form: props.attributes.form,
							formType: props.attributes.formType,
							formAction: props.attributes.formAction,
							postToEdit: props.attributes.postToEdit,
							anotherPostToEdit: props.attributes.anotherPostToEdit,
							userToEdit: props.attributes.userToEdit,
							anotherUserToEdit: props.attributes.anotherUserToEdit,
						}
					}
					onChangeCredForm={ onChangeCredForm }
					onChangePostToEdit={ onChangePostToEdit }
					onChangeUserToEdit={ onChangeUserToEdit }
					onChangeAnotherPostToEdit={ onChangeAnotherPostToEdit }
					onChangeAnotherUserToEdit={ onChangeAnotherUserToEdit }
				/>
			),
			(
				'' === props.attributes.form ?
					<Placeholder
						key="cred-form-block-placeholder"
						className={ classnames( 'wp-block-toolset-cred-form' ) }
					>
						<div className="wp-block-toolset-cred-form-placeholder">
							{ icon.blockPlaceholder }
							<p>
								<strong>{ __( 'Toolset Form' ) }</strong>
							</p>
						</div>
						<CREDFormSelect
							attributes={
								{
									newPostForms: newPostForms,
									editPostForms: editPostForms,
									newUserForms: newUserForms,
									editUserForms: editUserForms,
									form: props.attributes.form,
								}
							}
							className={ classnames( 'components-select-control__input' ) }
							onChangeCredForm={ onChangeCredForm }
						/>
					</Placeholder> :
					<CREDFormPreview
						key="toolset-cred-form-gutenberg-block-preview"
						className={ classnames( props.className, 'wp-block-toolset-cred-form-preview' ) }
						attributes={
							{
								form: JSON.parse( props.attributes.form ),
							}
						}
					/>
			),
		];
	},
	save: ( props ) => {
		let form = props.attributes.form || '';
		let post = '',
			user = '',
			shortcodeStart = '[cred-form';

		const shortcodeEnd = ']';

		if ( ! form.length ) {
			return null;
		}

		if (
			'post' === props.attributes.formType &&
			'edit' === props.attributes.formAction &&
			'another' === props.attributes.postToEdit &&
			props.attributes.anotherPostToEdit
		) {
			post = 'post="' + props.attributes.anotherPostToEdit.value + '" ';
			user = '';
		}

		if ( 'user' === props.attributes.formType ) {
			shortcodeStart = '[cred_user_form';
			if (
				'edit' === props.attributes.formAction &&
				'another' === props.attributes.userToEdit &&
				_.has( props.attributes.anotherUserToEdit, 'value' ) &&
				_.propertyOf( props.attributes.anotherUserToEdit )( 'value' )
			) {
				user = 'user="' + props.attributes.anotherUserToEdit.value + '" ';
				post = '';
			}
		}

		form = JSON.parse( form );

		form = ' form="' + form.post_name + '" ';

		return <RawHTML>{ shortcodeStart + form + post + user + shortcodeEnd }</RawHTML>;
	},
};

if ( 'undefined' !== typeof CRED ) {
	registerBlockType( name, settings );
}
