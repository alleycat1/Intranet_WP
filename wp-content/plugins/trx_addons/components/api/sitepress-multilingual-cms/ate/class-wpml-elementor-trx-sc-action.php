<?php

/**
 * Class WPML_Elementor_Trx_Sc_Action
 */
class WPML_Elementor_Trx_Sc_Action extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'actions';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array   The field names
	 */
	public function get_fields() {
		return array( 'title', 'subtitle', 'date', 'info', 'description', 'link' => array( 'url' ), 'link_text' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string        The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Action', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'subtitle':
				return esc_html( sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ) );

			case 'date':
				return esc_html( sprintf( __( '%s: date', 'trx_addons' ), $sc ) );

			case 'info':
				return esc_html( sprintf( __( '%s: info', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

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
	 * @return string        The field type
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'subtitle':
				return 'LINE';

			case 'date':
				return 'LINE';

			case 'info':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'url':
				return 'LINK';

			case 'link_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
