<?php

/**
 * Class WPML_Elementor_Trx_Sc_Googlemap
 * 
 * A helper class to translate the content of the Google map widget
 */
class WPML_Elementor_Trx_Sc_Googlemap extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
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
		return array( 'address', 'link' => array( 'url' ), 'html', 'title', 'description' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Google map marker', 'trx_addons' );
		switch( $field ) {
			case 'address':
				return esc_html( sprintf( __( '%s: address or Lat,Lng', 'trx_addons' ), $sc ) );

			case 'html':
				return esc_html( sprintf( __( '%s: custom HTML', 'trx_addons' ), $sc ) );

			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

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
			case 'address':
				return 'LINE';

			case 'html':
				return 'VISUAL';

			case 'title':
				return 'LINE';

			case 'description':
				return 'VISUAL';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
