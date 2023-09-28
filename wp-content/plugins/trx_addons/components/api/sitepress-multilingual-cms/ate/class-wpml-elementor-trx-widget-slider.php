<?php

/**
 * Class WPML_Elementor_Trx_Widget_Slider
 * 
 * A helper class to translate the content of the Slider widget
 */
class WPML_Elementor_Trx_Widget_Slider extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * Return a field name with type REPEATER
	 * 
	 * @return string  The field name
	 */
	public function get_items_field() {
		return 'slides';
	}

	/**
	 * Return a field names to translate
	 * 
	 * @return array  The field names
	 */
	public function get_fields() {
		return array( 'title', 'subtitle', 'meta', 'content', 'link' => array( 'url' ), 'video_url', 'video_embed' );
	}

	/**
	 * Return a title for a field by name
	 * 
	 * @param string $field  The field name
	 *
	 * @return string  	The field title
	 */
	protected function get_title( $field ) {
		$sc = __( 'Slide', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'subtitle':
				return esc_html( sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ) );

			case 'meta':
				return esc_html( sprintf( __( '%s: meta', 'trx_addons' ), $sc ) );

			case 'content':
				return esc_html( sprintf( __( '%s: content', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'video_url':
				return esc_html( sprintf( __( '%s: video URL', 'trx_addons' ), $sc ) );

			case 'video_embed':
				return esc_html( sprintf( __( '%s: video embed', 'trx_addons' ), $sc ) );

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

			case 'meta':
				return 'LINE';

			case 'content':
				return 'VISUAL';

			case 'url':
				return 'LINK';

			case 'video_url':
				return 'LINE';

			case 'video_embed':
				return 'AREA';

			default:
				return '';
		}
	}

}
