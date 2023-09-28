<?php
/**
 * Skin Demo importer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.76.0
 */


// Theme storage
//-------------------------------------------------------------------------

pubzinne_storage_set( 'theme_demo_url', '//pubzinne.axiomthemes.com/' );


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'pubzinne_skin_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'pubzinne_skin_importer_set_options', 9 );
	function pubzinne_skin_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			$options['files']['default']['title']       = esc_html__( 'Pubzinne Demo', 'pubzinne' );
			$options['files']['default']['domain_dev']  = esc_url( pubzinne_get_protocol() . '://pubzinne.axiomthemes.com/' );    // Developers domain
			$options['files']['default']['domain_demo'] = pubzinne_storage_get( 'theme_demo_url' );                            // Demo-site domain
			if ( substr( $options['files']['default']['domain_demo'], 0, 2 ) === '//' ) {
				$options['files']['default']['domain_demo'] = pubzinne_get_protocol() . ':' . $options['files']['default']['domain_demo'];
			}
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'pubzinne_skin_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'pubzinne_skin_ocdi_set_options', 9 );
	function pubzinne_skin_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Demo-site domain
			$options['files']['ocdi']['title']       = esc_html__( 'Pubzinne OCDI Demo', 'pubzinne' );
			$options['files']['ocdi']['domain_demo'] = pubzinne_storage_get( 'theme_demo_url' );
			if ( substr( $options['files']['ocdi']['domain_demo'], 0, 2 ) === '//' ) {
				$options['files']['ocdi']['domain_demo'] = pubzinne_get_protocol() . ':' . $options['files']['ocdi']['domain_demo'];
			}
			// If theme need more demo - just copy 'default' and change required parameters
		}
		return $options;
	}
}
