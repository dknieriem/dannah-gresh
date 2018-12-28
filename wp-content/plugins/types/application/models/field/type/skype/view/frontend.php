<?php

/**
 * Class Types_Field_Type_Skype_View_Frontend
 *
 * Handles view specific tasks for field "Single Line"
 *
 * @since 2.3
 */
class Types_Field_Type_Skype_View_Frontend extends Types_Field_Type_View_Frontend_Abstract {
	/**
	 * Types_Field_Type_Skype_View_Frontend constructor.
	 *
	 * @param Types_Field_Type_Skype $entity
	 * @param array $params
	 */
	public function __construct( Types_Field_Type_Skype $entity, $params = array() ) {
		$this->entity = $entity;
		$this->params = $this->normalise_user_values( $params );
	}

	/**
	 * Gets value when output is not html
	 *
	 * @return string
	 */
	public function get_value() {
		if ( ! $this->is_raw_output() ) {
			/* TODO: new buttons are suppose to exists but there are not, meanwhile legacy decorator will be used
			$decorator_skype = ! isset( $this->params['action'] ) && isset( $this->params['button_style'] )
				? new Types_View_Decorator_Skype_Legacy()
				: new Types_View_Decorator_Skype();
			*/
			$decorator_skype = new Types_View_Decorator_Skype_Legacy();

			$this->add_decorator( $decorator_skype );
		}

		$is_html_output = $this->is_html_output();
		$values = $this->entity->get_value_filtered( $this->params );

		if ( $this->empty_values( $values ) ) {
			return '';
		}
		// Transform each value to HTML
		$rendered_values = array();
		$decorator_html = new Types_View_Decorator_Output_HTML( false );
		foreach( $values as $value ) {
			$value = is_array( $value ) && array_key_exists( 'skypename', $value )
				? $value['skypename']
				: $value;

			$value = $this->filter_field_value_after_decorators( $this->get_decorated_value( $value, $is_html_output ), $value );
			if ( $is_html_output ) {
				 $value = $decorator_html->get_value( $value, $this->params, $this->entity, true, true );
			}
			$rendered_values[] = $value;
		}

		return $this->get_rendered_value( $rendered_values );
	}

}
