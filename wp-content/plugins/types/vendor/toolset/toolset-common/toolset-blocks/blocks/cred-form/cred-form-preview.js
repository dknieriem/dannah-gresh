/**
 * The Toolset Form block preview component.
 *
 * A "CREDFormPreview" component is created that is used inside the Toolset Form block to handle the previewing of the
 * selected Content Template.
 *
 * @since  2.6.0
 */

import classnames from 'classnames';

const {
	__,
} = wp.i18n;

const {
	Component,
} = wp.element;

const {
	Spinner,
} = wp.components;

export default class CREDFormPreview extends Component {
	// constructor( props ) {
	constructor() {
		super( ...arguments );
		this.getCREDFormInfo = this.getCREDFormInfo.bind( this );
		this.state = {
			fetching: false,
			error: false,
			errorMessage: '',
		};
	}

	render() {
		const {
			fetching,
			error,
			errorMessage,
			formInfo,
		} = this.state;

		if ( fetching ) {
			return <div key="fetching" className={ classnames( this.props.className ) } >
				<div key="loading" className={ classnames( 'wp-block-embed is-loading' ) }>
					<Spinner />
					<p>{ __( 'Loading the Toolset Form Preview…' ) }</p>
				</div>
			</div>;
		}

		if ( error ) {
			return <div key="error" className={ classnames( this.props.className ) } >
				<div className={ classnames( 'wpv-cred-form-info-warning' ) }>
					{ errorMessage }
				</div>

			</div>;
		}

		return (
			<div className={ this.props.className } >
				<div key="cred-form-information" className="cred-form-information" >
					<div className={ classnames( 'cred-form-preview-render' ) } dangerouslySetInnerHTML={ { __html: formInfo } }></div>
				</div>
			</div>
		);
	}

	componentWillMount() {
		if ( this.props.attributes.form ) {
			// If the Toolset Form is already there, we're loading a saved block, so we need to render
			// a different thing, which is why this doesn't use 'fetching', as that
			// is for when the user is putting in a new url on the placeholder form
			this.setState( { fetching: true } );
			this.getCREDFormInfo();
		}
	}

	componentWillReceiveProps( nextProps ) {
		if ( this.props.attributes.form.ID !== nextProps.attributes.form.ID ) {
			this.setState( {
				fetching: true,
				error: false,
				errorMessage: '',
			} );
			this.getCREDFormInfo( nextProps.attributes.form.ID );
		}
	}

	getCREDFormInfo( formID ) {
		const data = new window.FormData();
		data.append( 'action', window.toolset_cred_form_block_strings.actionName );
		data.append( 'wpnonce', window.toolset_cred_form_block_strings.wpnonce );
		data.append( 'formID', 'undefined' === typeof formID ? this.props.attributes.form.ID : formID );

		window.fetch( window.ajaxurl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
		} ).then( res => res.json() )
			.then( response => {
				const newState = {};
				if (
					0 !== response &&
					response.success &&
					'undefined' !== typeof response.data
				) {
					if ( '' === response.data ) {
						newState.error = true;
						newState.errorMessage = __( 'The selected Toolset Form has an empty form content.' );
					} else {
						newState.formInfo = response.data;
					}
				} else {
					let message = '';
					if (
						'undefined' !== typeof response.data &&
						'undefined' !== typeof response.data.message ) {
						message = response.data.message;
					} else {
						message = __( 'An error occurred while trying to get the Toolset Form information.' );
					}
					newState.error = true;
					newState.errorMessage = message;
				}

				newState.fetching = false;

				this.setState( newState );
			} );
	}
}
