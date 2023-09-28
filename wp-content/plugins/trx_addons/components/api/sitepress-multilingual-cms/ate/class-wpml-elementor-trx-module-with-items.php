<?php

/**
 * Class WPML_Elementor_Trx_Module_With_Items
 * 
 * Attention! Use this class to override base class from WPML to enable using two and more fields with URL
 * (for example: 'link' => ['url' => ''] and 'image' => ['url' => ''])
 */
abstract class WPML_Elementor_Trx_Module_With_Items extends WPML_Elementor_Module_With_Items {

	/**
	 * @param string|int $node_id
	 * @param array $element
	 * @param WPML_PB_String[] $strings
	 *
	 * @return WPML_PB_String[]
	 */
/*
	public function get( $node_id, $element, $strings ) {
		foreach ( $this->get_items( $element ) as $item ) {
			foreach( $this->get_fields() as $key => $field ) {
				if ( ! is_array( $field ) ) {

					if ( ! isset( $item[ $field ] ) ) {
						continue;
					}

					$strings[] = new WPML_PB_String(
						$item[ $field ],
						$this->get_string_name( $node_id, $item[ $field ], $field, $element['widgetType'], $item['_id'] ),
						$this->get_title( $field ),
						$this->get_editor_type( $field )
					);
				} else {
					foreach ( $field as $inner_key => $inner_field ) {

						if ( ! isset( $item[ $key ][ $inner_field ] ) ) {
							continue;
						}

						// If inner key is specified - compose key as 'key_field'
						// to prevent get equal field name from different params:
						// link => [ 'url' ] and image => ['url']
						$inner_key = ! is_numeric( $inner_key ) ? $inner_key : $inner_field;

						$strings[] = new WPML_PB_String(
							$item[ $key ][ $inner_field ],
							$this->get_string_name( $node_id, $item[ $key ][ $inner_field ], $inner_key, $element['widgetType'], $item['_id'] ),
							$this->get_title( $inner_key ),
							$this->get_editor_type( $inner_key )
						);
					}
				}
			}
		}
		return $strings;
	}
*/

	/**
	 * @param int|string $node_id
	 * @param mixed $element
	 * @param WPML_PB_String $string
	 *
	 * @return mixed
	 */
/*
	public function update( $node_id, $element, WPML_PB_String $string ) {
		foreach ( $this->get_items( $element ) as $key => $item ) {
			foreach( $this->get_fields() as $field_key => $field ) {
				if ( ! is_array( $field ) ) {

					if ( ! isset( $item[ $field ] ) ) {
						continue;
					}

					if ( $this->get_string_name( $node_id, $item[ $field ], $field, $element['widgetType'], $item['_id'] ) === $string->get_name() ) {
						$item[ $field ] = $string->get_value();
						$item['index'] = $key;
						return $item;
					}
				} else {
					foreach ( $field as $inner_key => $inner_field ) {
						if ( ! isset( $item[ $field_key ][ $inner_field ] ) ) {
							continue;
						}

						// If inner key is specified - compose key as 'key_field'
						// to prevent get equal field name from different params:
						// 'link' => [ 'url' => '' ] and 'image' => [ 'url' => '' ]
						$inner_key = ! is_numeric( $inner_key ) ? $inner_key : $inner_field;

						if ( $this->get_string_name( $node_id, $item[ $field_key ][ $inner_field ], $inner_key, $element['widgetType'], $item['_id'] ) === $string->get_name() ) {
							$item[ $field_key ][ $inner_field ] = $string->get_value();
							$item['index'] = $key;
							return $item;
						}
					}
				}
			}
		}
	}
*/

	/**
	 * @param string $node_id
	 * @param string $value
	 * @param string $type
	 * @param string $key
	 * @param string $item_id
	 *
	 * @return string
	 */
/*
	private function get_string_name( $node_id, $value, $type, $key = '', $item_id = '' ) {
		return $key . '-' . $type . '-' . $node_id . '-' . $item_id;
	}
*/

}
