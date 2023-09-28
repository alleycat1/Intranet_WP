<?php

/**
 * Class WPML_Elementor_Trx_Sc_Osmap
 * 
 * A helper class to translate the content of the OpenStreet map widget
 */
class WPML_Elementor_Trx_Sc_Osmap extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'markers';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'address', 'title', 'description' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'OpenStreet map marker', 'trx_addons' );
		switch( $field ) {
			case 'address':
				return esc_html( sprintf( __( '%s: address or Lat,Lng', 'trx_addons' ), $sc ) );

			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

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
			case 'address':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'description':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
