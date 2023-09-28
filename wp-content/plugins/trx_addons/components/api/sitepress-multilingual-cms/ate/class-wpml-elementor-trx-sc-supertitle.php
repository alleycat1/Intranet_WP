<?php

/**
 * Class WPML_Elementor_Trx_Sc_Supertitle
 * 
 * A helper class to translate the content of the Super title widget
 */
class WPML_Elementor_Trx_Sc_Supertitle extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'items';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'text' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  	The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Super title item', 'trx_addons' );
		switch( $field ) {
			case 'text':
				return esc_html( sprintf( __( '%s: text', 'trx_addons' ), $sc ) );

			default:
				return '';
		}
	}

	/**
	 * Return a field type by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string 	The field type
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}

}
