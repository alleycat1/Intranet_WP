<?php

/**
 * Class WPML_Elementor_Trx_Sc_Icons
 * 
 * A helper class to translate the content of the Icons widget
 */
class WPML_Elementor_Trx_Sc_Icons extends WPML_Elementor_Trx_Module_With_Items {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'icons';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		// This way need class 'WPML_Elementor_Trx_Module_With_Items'
		// (allow using subkeys in the inner array)
//		return array( 'char', 'title', 'description', 'link' => array( 'url' ), 'image' => array( 'image_url' => 'url' ) );

		// This way based on core WPML class 'WPML_Elementor_Module_With_Items'
		// (not support subkeys in the inner array)
		return array( 'char', 'title', 'description', 'link' => array( 'url' ) );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Icons item', 'trx_addons' );
		switch( $field ) {
			case 'char':
				return esc_html( sprintf( __( '%s: char', 'trx_addons' ), $sc ) );

			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'image_url':
				return esc_html( sprintf( __( '%s: image URL', 'trx_addons' ), $sc ) );

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
			case 'char':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'url':
				return 'LINK';

			case 'image_url':
				return 'LINK';

			default:
				return '';
		}
	}

}
