<?php

/**
 * Class WPML_Elementor_Trx_Widget_Custom_Links
 * 
 * A helper class to translate the content of the Custom links widget
 */
class WPML_Elementor_Trx_Widget_Custom_Links extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'links';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'title', 'url' => array( 'url' ), 'caption', 'description', 'label' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  	The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Custom link', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'caption':
				return esc_html( sprintf( __( '%s: caption', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'label':
				return esc_html( sprintf( __( '%s: label', 'trx_addons' ), $sc ) );

			default:
				return '';
		}
	}

	/**
	 * Return a field type by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  	The field type
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'url':
				return 'LINK';

			case 'caption':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'label':
				return 'LINE';

			default:
				return '';
		}
	}

}
