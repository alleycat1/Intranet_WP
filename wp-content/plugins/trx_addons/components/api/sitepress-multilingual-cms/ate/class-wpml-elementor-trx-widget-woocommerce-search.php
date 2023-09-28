<?php

/**
 * Class WPML_Elementor_Trx_Widget_Woocommerce_Search
 * 
 * A helper class to translate the content of the WooCommerce search widget
 */
class WPML_Elementor_Trx_Widget_Woocommerce_Search extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'fields';
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
		$sc = __( 'WooCommerce search field', 'trx_addons' );
		switch( $field ) {
			case 'text':
				return esc_html( sprintf( __( '%s: field text', 'trx_addons' ), $sc ) );

			default:
				return '';
		}
	}

	/**
	 * Return a field type by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  The field type
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
