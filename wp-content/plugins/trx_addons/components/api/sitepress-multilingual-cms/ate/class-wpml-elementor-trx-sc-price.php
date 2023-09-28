<?php

/**
 * Class WPML_Elementor_Trx_Sc_Price
 * 
 * A helper class to translate the content of the Price widget
 */
class WPML_Elementor_Trx_Sc_Price extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'prices';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'title', 'subtitle', 'label', 'description', 'before_price', 'price', 'after_price', 'details', 'link' => array( 'url' ), 'link_text' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  	The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Price item', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'subtitle':
				return esc_html( sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ) );

			case 'label':
				return esc_html( sprintf( __( '%s: label', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'before_price':
				return esc_html( sprintf( __( '%s: before price', 'trx_addons' ), $sc ) );

			case 'price':
				return esc_html( sprintf( __( '%s: price', 'trx_addons' ), $sc ) );

			case 'after_price':
				return esc_html( sprintf( __( '%s: after price', 'trx_addons' ), $sc ) );

			case 'details':
				return esc_html( sprintf( __( '%s: details', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'link_text':
				return esc_html( sprintf( __( '%s: link text', 'trx_addons' ), $sc ) );

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

			case 'subtitle':
				return 'LINE';

			case 'label':
				return 'LINE';

			case 'description':
				return 'LINE';

			case 'before_price':
				return 'LINE';

			case 'price':
				return 'LINE';

			case 'after_price':
				return 'LINE';

			case 'details':
				return 'VISUAL';

			case 'url':
				return 'LINK';

			case 'link_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
