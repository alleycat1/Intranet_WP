<?php
/**
 * Plugin support: Google Places API
 *
 * @package ThemeREX Addons
 * @since v2.7.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add 'Google Places' block to the ThemeREX Addons Options
if ( ! function_exists( 'trx_addons_google_places_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_google_places_options' );
	function trx_addons_google_places_options( $options ) {
		if ( trx_addons_reviews_enable() ) {
			trx_addons_array_insert_before( $options, 'api_google_analitics_info', array(
				'api_google_places_info' => array(
					"title" => esc_html__( 'Google Places API', 'trx_addons' ),
					"desc" => wp_kses_data( __( "Specify Google API Key to access Google Places services", 'trx_addons' ) ),
					"type" => "info"
				),
				'api_google_places' => array(
					"title" => esc_html__( 'Google Places API Key', 'trx_addons' ),
					"desc" => wp_kses_data( __( "Insert Google Places API Key to the the field above", 'trx_addons' ) ),
					"std" => "",
					"type" => "text"
				),
			) );
		}
		return $options;
	}
}


// Return a key for Google Places API
if ( ! function_exists( 'trx_addons_google_places_api_key' ) ) {
	function trx_addons_google_places_api_key() {
		return trx_addons_get_option( 'api_google_places', '' );
	}
}


// Retrieve details about a place by id from Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_details' ) ) {
	function trx_addons_google_places_get_details( $id ) {
		$rez = false;
		$key = trx_addons_google_places_api_key();
		if ( ! empty( $key ) && ! empty( $id ) ) {
			$rez = trx_addons_retrieve_json(
						apply_filters( 'trx_addons_filter_google_places_url',
							sprintf( 'https://maps.googleapis.com/maps/api/place/details/json?key=%1$s&placeid=%2$s', $key, $id )
						)
					);
		}
		return $rez;
	}
}


// Return details about a place by id from Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_info' ) ) {
	function trx_addons_google_places_get_info( $id, $force = false ) {
		$rez = false;
		if ( ! empty( $id ) ) {
			$places = $force ? false : get_transient( 'trx_addons_google_places_info' );
			if ( ! is_array( $places ) || empty( $places[ $id ] ) ) {
				$details = trx_addons_google_places_get_details( $id );
				if ( is_array( $details ) && ! empty( $details['status'] ) && $details['status'] == 'OK' ) {
					$rez = array(
						// General
						'name'          => ! empty( $details['result']['name'] ) ? $details['result']['name'] : '',
						'icon'          => ! empty( $details['result']['icon'] ) ? $details['result']['icon'] : '',
						//'photos'        => ! empty( $details['result']['photos'] ) ? $details['result']['photos'] : '',
						
						// Place ID from Google Maps
						'place_id'      => ! empty( $details['result']['place_id'] ) ? $details['result']['place_id'] : '',
						
						// URL to the Google Maps
						'url'           => ! empty( $details['result']['url'] ) ? $details['result']['url'] : '',
						'latlng'        => ! empty( $details['result']['geometry']['location'] ) ? $details['result']['geometry']['location'] : '',
						
						// Address and phone
						'address'       => ! empty( $details['result']['formatted_address'] ) ? $details['result']['formatted_address'] : '',
						'address_short' => ! empty( $details['result']['vicinity'] ) ? $details['result']['vicinity'] : '',
						'address_html'  => ! empty( $details['result']['adr_address'] ) ? $details['result']['adr_address'] : '',
						'phone'         => ! empty( $details['result']['international_phone_number'] ) ? $details['result']['international_phone_number'] : '',
						'website'       => ! empty( $details['result']['website'] ) ? $details['result']['website'] : '',
						
						// Opening hours
						'opening_hours' => ! empty( $details['result']['opening_hours'] ) ? $details['result']['opening_hours'] : '',
						
						// Rating and reviews
						'rating'        => ! empty( $details['result']['rating'] ) ? $details['result']['rating'] : '',
						'ratings_total' => ! empty( $details['result']['user_ratings_total'] ) ? $details['result']['user_ratings_total'] : '',
						'reviews'       => ! empty( $details['result']['reviews'] ) ? $details['result']['reviews'] : '',
					);
					if ( ! empty( $rez['photos'] ) && is_array( $rez['photos'] ) ) {
						foreach( $rez['photos'] as $k => $v ) {
							$rez['photos'][ $k ]['photo_url'] = ! empty( $v['photo_reference'] ) ? trx_addons_google_places_get_photo_url( $v['photo_reference'] ) : '';
						}
					}
					// Save to the cache for 4 hour
					$places[ $id ] = apply_filters( 'trx_addons_filter_google_places_info', $rez, $id );
					set_transient( 'trx_addons_google_places_info', $places, 4 * 60 * 60 );
				}
			} else {
				$rez = $places[ $id ];
			}
		}
		return $rez;
	}
}


// Return photo URL by reference from Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_photo_url' ) ) {
	function trx_addons_google_places_get_photo_url( $ref, $width=1200, $height=900 ) {
		$url = '';
		$key = trx_addons_google_places_api_key();
		if ( ! empty( $key ) ) {
			$url = sprintf( 'https://maps.googleapis.com/maps/api/place/photo?photoreference=%1$s&sensor=false&maxheight=%2$d&maxwidth=%3$d&key=%4$s',
							$ref, $height, $width, $key );
		}
		return $url;
	}
}


// Return URL of the specified place (by place id) on Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_place_url' ) ) {
	function trx_addons_google_places_get_place_url( $id ) {
		return $url = sprintf( 'https://www.google.com/maps/place/?q=place_id:%s', $id );
	}
}


// Return URL to the reviews of the specified place (by place id) on Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_reviews_url' ) ) {
	function trx_addons_google_places_get_reviews_url( $id ) {
		return $url = sprintf( 'https://search.google.com/local/reviews?placeid=%s', $id );
	}
}


// Return URL to the map of the specified place (by place id) on Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_map_url' ) ) {
	function trx_addons_google_places_get_map_url( $id ) {
		$info = trx_addons_google_places_get_info( $id );
		return $info['url'];
	}
}


// Return URL to the 'Add review' for the specified place (by place id) on Google Maps
if ( ! function_exists( 'trx_addons_google_places_get_add_review_url' ) ) {
	function trx_addons_google_places_get_add_review_url( $id ) {
		return $url = sprintf( 'https://search.google.com/local/writereview?placeid=%s', $id );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// Clear some plugin's specific options before export
if ( ! function_exists( 'trx_addons_google_places_importer_export_options' ) ) {
	add_filter( 'trx_addons_filter_export_options', 'trx_addons_google_places_importer_export_options' );
	function trx_addons_google_places_importer_export_options( $options ) {
		if ( ! empty( $options['api_google_places'] ) ) {
			$options['api_google_places'] = '';
		}
		return $options;
	}
}


// CPT support
//----------------------------------------------------------------------------

// Add Google Places API support to the CPT Properties
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/cpt/properties/properties.php';
