<?php

/**
 * Class WPML_Elementor_Trx_Sc_Button
 * 
 * A helper class to translate the content of the Button widget
 */
class WPML_Elementor_Trx_Sc_Button extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'buttons';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'title', 'subtitle', 'link' => array( 'url' ) );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Button', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'subtitle':
				return esc_html( sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			default:
				return '';
		}
	}

	/**
	 * Return a field type by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field type
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'subtitle':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
