/**
 * The Content Template block inspector component.
 *
 * An "Inspector" component is created that is used inside the Toolset Content Template block to handle all the functionality related
 * to the controls on the Gutenberg editor sidebar.
 *
 * @since  2.6.0
 */

/**
 * Block dependencies
 */
import CREDFormSelect from './cred-form-select';
import Select2 from '../../common/select2';

/**
 * Internal block libraries
 */
const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	InspectorControls,
} = wp.editor;

const {
	BaseControl,
	RadioControl,
	PanelBody,
} = wp.components;

/**
 * Create an Inspector Controls wrapper Component
 */
export default class Inspector extends Component {
	render() {
		const {
			attributes,
			className,
			onChangeCredForm,
			onChangePostToEdit,
			onChangeAnotherPostToEdit,
			onChangeUserToEdit,
			onChangeAnotherUserToEdit,
		} = this.props;

		const {
			form,
			formType,
			formAction,
			postToEdit,
			anotherPostToEdit,
			userToEdit,
			anotherUserToEdit,
			newPostForms,
			editPostForms,
			newUserForms,
			editUserForms,
		} = attributes;

		return (
			<InspectorControls>
				<div className={ className }>
					<PanelBody title={ __( 'Toolset Form' ) }>
						<BaseControl>
							<CREDFormSelect
								attributes={
									{
										newPostForms: newPostForms,
										editPostForms: editPostForms,
										newUserForms: newUserForms,
										editUserForms: editUserForms,
										form: form,
									}
								}
								className="components-select-control__input"
								onChangeCredForm={ onChangeCredForm }
							/>
						</BaseControl>
						{
							'edit' === formAction && 'post' === formType ?
								[
									<RadioControl
										key="postToEdit"
										label={ __( 'Post to edit' ) }
										selected={ postToEdit }
										onChange={ onChangePostToEdit }
										options={
											[
												{ value: 'current', label: __( 'The current post' ) },
												{ value: 'another', label: __( 'Another post' ) },
											]
										}
									/>,
									// @todo Switch the custom endpoint with the native one, once it's available.
									// The endpoint used on the autocomplete component below to get the post according to a
									// search keyword, needs to be changed with the native WP REST API endpoint (as soon as
									// it's available).
									// https://github.com/WordPress/gutenberg/issues/2084
									// https://core.trac.wordpress.org/ticket/39965
									'another' === postToEdit ?
										<BaseControl label={ __( 'Post' ) } key="another-post-select">
											<Select2
												onChange={ onChangeAnotherPostToEdit }
												restInfo={
													{
														base: '/toolset/v2/search-posts',
														args: {
															search: '%s',
														},
													}
												}
												value={ anotherPostToEdit }
											/>
										</BaseControl> :
										null,
								] :
								null
						}
						{
							'edit' === formAction && 'user' === formType ?
								[
									<RadioControl
										key="userToEdit"
										label={ __( 'User to edit' ) }
										selected={ userToEdit }
										onChange={ onChangeUserToEdit }
										options={
											[
												{ value: 'current', label: __( 'The current logged in user' ) },
												{ value: 'another', label: __( 'Another user' ) },
											]
										}
									/>,
									'another' === userToEdit ?
										<BaseControl label={ __( 'User' ) } key="another-user-select">
											<Select2
												onChange={ onChangeAnotherUserToEdit }
												restInfo={
													{
														base: '/wp/v2/users',
														args: {
															search: '%s',
															per_page: 20,
															orderby: 'name',
															context: 'edit',
														},
													}
												}
												value={ anotherUserToEdit }
											/>
										</BaseControl> :
										null,
								] :
								null
						}
					</PanelBody>
				</div>
			</InspectorControls>
		);
	}
}
