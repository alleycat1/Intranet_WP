<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets the post data for a custom field column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Custom_Field extends Abstract_Table_Data {

	private $field;
	private $image_size;
	private $date_format;
	private $date_columns;
	private $acf_field_object;
	private $is_acf_field;

	public function __construct( $post, $field, $links = '', $image_size = '', $date_format = '', $date_columns = [] ) {
		parent::__construct( $post, $links );

		$this->field            = $field;
		$this->image_size       = $image_size ?: 'thumbnail';
		$this->date_format      = $date_format;
		$this->date_columns     = (array) $date_columns;
		$this->acf_field_object = Util::get_acf_field_object( $this->field, $this->post->ID ) ?: Util::get_acf_field_object( $this->field );
		$this->is_acf_field     = (bool) $this->acf_field_object;
	}

	public function get_data() {
		if ( $this->is_acf_field ) {
			// Get data for ACF field.
			$cf_value = $this->get_acf_value( $this->acf_field_object, $this->post->ID );
		} else {
			// Get data for standard custom field.
			$cf_value = get_post_meta( $this->post->ID, $this->field, false );
		}

		// Flatten field in case an array was returned.
		$cf_value = array_reduce( (array) $cf_value, [ $this, 'flatten_custom_field' ], '' );

		// Format the date if a custom date format is given and this is a date custom field.
		// We exclude ACF date picker fields, as the date formatting for them is handled by get_acf_value, so we don't double up.
		if ( $this->date_format && $this->is_date_field() && ! $this->is_acf_date_field() ) {

			if ( $timestamp = $this->convert_to_timestamp( $cf_value ) ) {
				// Format date using desired format.
				$cf_value = date_i18n( $this->date_format, $timestamp );
			}
		}

		// Format as link if custom field is a URL
		if ( 0 === strpos( $cf_value, 'http' ) && $url = filter_var( $cf_value, FILTER_VALIDATE_URL ) ) {
			$link_text = str_replace( [ 'http://', 'https://' ], '', $cf_value );
			$cf_value  = sprintf(
				'<a href="%1$s">%2$s</a>',
				apply_filters( 'document_library_pro_url_custom_field_link', $url, $this->field, $this->post ),
				apply_filters( 'document_library_pro_url_custom_field_text', $link_text, $this->field, $this->post )
			);
		}

		// Filter the result.
		$cf_value = apply_filters( 'document_library_pro_data_custom_field', $cf_value, $this->field, $this->post );
		$cf_value = apply_filters( 'document_library_pro_data_custom_field_' . $this->field, $cf_value, $this->post );

		return $cf_value;
	}

	private function get_acf_value( $field_obj, $post_id = false ) {
		if ( ! $field_obj || ! isset( $field_obj['value'] ) || '' === $field_obj['value'] || 'null' === $field_obj['value'] || empty( $field_obj['type'] ) ) {
			return '';
		}

		$cf_value = $field_obj['value'];

		switch ( $field_obj['type'] ) {
			case 'text':
			case 'number':
			case 'email':
			case 'password':
			case 'color_picker':
			case 'textarea':
			case 'wysiwyg':
			case 'google_map':
				$cf_value = get_field( $field_obj['name'], $post_id, true );
				break;
			case 'date_picker':
			case 'date_time_picker':
				if ( $timestamp = $this->convert_to_timestamp( $cf_value ) ) {
					// Use date_format if specified, otherwise use the 'return format' for the date field.
					$date_format = $this->date_format ?: $field_obj['return_format'];
					$cf_value    = date_i18n( $date_format, $timestamp );
				}
				break;
			case 'time_picker':
				if ( $timestamp = $this->convert_to_timestamp( $cf_value ) ) {
					$cf_value = date_i18n( $field_obj['return_format'], $timestamp );
				}
				break;
			case 'radio':
				if ( ! empty( $field_obj['choices'] ) && ( is_int( $cf_value ) || is_string( $cf_value ) ) && isset( $field_obj['choices'][ $cf_value ] ) ) {
					$cf_value = $field_obj['choices'][ $cf_value ];
				}
				break;
			case 'select':
			case 'checkbox':
				if ( ! empty( $field_obj['choices'] ) && ( is_string( $cf_value ) || is_int( $cf_value ) || is_array( $cf_value ) ) ) {
					$labels = [];

					foreach ( (array) $cf_value as $value ) {
						if ( isset( $field_obj['choices'][ $value ] ) ) {
							$labels[] = $field_obj['choices'][ $value ];
						} else {
							$labels[] = $value;
						}
					}
					$cf_value = $labels;
				}
				break;
			case 'true_false':
				$cf_value = $cf_value ? __( 'True', 'document-library-pro' ) : __( 'False', 'document-library-pro' );
				break;
			case 'file':
				$cf_value = wp_get_attachment_link( $cf_value, $this->image_size, false, true );
				break;
			case 'image':
				$cf_value = wp_get_attachment_link( $cf_value, $this->image_size );
				break;
			case 'page_link':
			case 'post_object':
			case 'relationship':
				$cf_value = array_map( [ $this, 'get_post_title' ], (array) $cf_value );
				break;
			case 'taxonomy':
				$term_links = [];

				foreach ( (array) $cf_value as $term_id ) {
					if ( $term = get_term_by( 'id', $term_id, $field_obj['taxonomy'] ) ) {
						if ( array_intersect( [ 'all', 'terms' ], $this->links ) ) {
							$term_links[] = sprintf( '<a href="%1$s" rel="tag">%2$s</a>', esc_url( get_term_link( $term_id, $field_obj['taxonomy'] ) ), $term->name );
						} else {
							$term_links[] = $term->name;
						}
					}
				}
				$cf_value = $term_links;
				break;
			case 'user':
				$users = [];

				foreach ( (array) $cf_value as $user_id ) {
					if ( array_intersect( [ 'all', 'author' ], $this->links ) ) {
						$users[] = sprintf(
							'<a href="%1$s" rel="author">%2$s</a>',
							esc_url( get_author_posts_url( $user_id ) ),
							get_the_author_meta( 'display_name', $user_id )
						);
					} else {
						$users[] = get_the_author_meta( 'display_name', $user_id );
					}
				}
				$cf_value = $users;
				break;
			case 'repeater':
				$repeater_value = [];

				while ( have_rows( $field_obj['name'], $post_id ) ) {
					the_row();

					foreach ( $field_obj['sub_fields'] as $sub_field ) {
						$sub_field_value  = $this->get_acf_value( get_sub_field_object( $sub_field['name'], false ), $post_id );
						$repeater_value[] = apply_filters( 'document_library_pro_acf_sub_field_value', $sub_field_value, $sub_field['name'], $field_obj['name'], $post_id );
					}
				}

				$cf_value = apply_filters( 'document_library_pro_acf_repeater_field_value', $repeater_value );
				break;
			// @todo: Other layout field types?
		}

		return apply_filters( 'document_library_pro_acf_value', $cf_value, $field_obj, $post_id );
	}

	private function convert_to_timestamp( $date ) {
		if ( ! $date ) {
			return false;
		}

		$format = apply_filters( 'document_library_pro_custom_field_stored_date_format', '', $this->field );

		if ( Util::is_european_date_format( $format ) || apply_filters( 'document_library_pro_custom_field_is_eu_au_date', false, $this->field ) ) {
			$date = str_replace( '/', '-', $date );
		}

		return Util::strtotime( $date );
	}

	private function is_date_field() {
		return in_array( 'cf:' . $this->field, $this->date_columns, true ) || $this->is_acf_date_field();
	}

	private function is_acf_date_field() {
		return $this->is_acf_field && in_array( $this->acf_field_object['type'], [ 'date_picker', 'date_time_picker', 'time_picker' ], true );
	}

	public function get_sort_data() {
		if ( $this->is_date_field() ) {
			$date = get_post_meta( $this->post->ID, $this->field, true );

			// Format the hidden date column for sorting
			if ( $timestamp = $this->convert_to_timestamp( $date ) ) {
				return $timestamp;
			}

			// Need to return non-empty string to ensure all cells have a data-sort value.
			return '0';
		}

		return '';
	}

	private function flatten_custom_field( $carry, $item ) {
		if ( is_array( $item ) ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field_row' );
			}
			$carry .= array_reduce( $item, [ $this, 'flatten_custom_field' ], '' );
		} elseif ( '' !== $item && false !== $item ) {
			if ( $carry ) {
				$carry .= parent::get_separator( 'custom_field' );
			}
			$carry .= $item;
		}

		return $carry;
	}

	private function get_post_title( $post_id ) {
		$title_data = new Post_Title( get_post( $post_id ), $this->links );

		return $title_data->get_data();
	}

}
