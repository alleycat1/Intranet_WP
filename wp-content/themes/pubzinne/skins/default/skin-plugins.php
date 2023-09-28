<?php
/**
 * Required plugins
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$pubzinne_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'pubzinne' ),
	'page_builders' => esc_html__( 'Page Builders', 'pubzinne' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'pubzinne' ),
	'socials'       => esc_html__( 'Socials and Communities', 'pubzinne' ),
	'events'        => esc_html__( 'Events and Appointments', 'pubzinne' ),
	'content'       => esc_html__( 'Content', 'pubzinne' ),
	'other'         => esc_html__( 'Other', 'pubzinne' ),
);
$pubzinne_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'pubzinne' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'pubzinne' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $pubzinne_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'pubzinne' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $pubzinne_theme_required_plugins_groups['page_builders'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'pubzinne' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'pubzinne' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $pubzinne_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'pubzinne' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'pubzinne' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $pubzinne_theme_required_plugins_groups['page_builders'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'pubzinne' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'woocommerce.png',
		'group'       => $pubzinne_theme_required_plugins_groups['ecommerce'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'pubzinne' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'elegro-payment.png',
		'group'       => $pubzinne_theme_required_plugins_groups['ecommerce'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( ' The Events Calendar', 'pubzinne' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $pubzinne_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'pubzinne' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $pubzinne_theme_required_plugins_groups['content'],
	),
    'instagram-feed'             => array(
        'title'       => esc_html__( 'Instagram Feed', 'pubzinne' ),
        'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'pubzinne' ),
        'required'    => false,
        'logo'        => 'instagram-feed.png',
        'group'       => $pubzinne_theme_required_plugins_groups['content'],
    ),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'pubzinne' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'revslider.png',
		'group'       => $pubzinne_theme_required_plugins_groups['content'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'pubzinne' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'pubzinne' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $pubzinne_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'pubzinne' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $pubzinne_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'pubzinne' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $pubzinne_theme_required_plugins_groups['other'],
	),
	'trx_popup'                  => array(
		'title'       => esc_html__( 'ThemeREX Popup', 'pubzinne' ),
		'description' => esc_html__( "Add popup to your site.", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'trx_popup.png',
		'group'       => $pubzinne_theme_required_plugins_groups['other'],
	),
    'restaurant-reservations'                  => array(
		'title'       => esc_html__( 'Five Star Restaurant Reservations', 'pubzinne' ),
		'description' => esc_html__( "Restaurant reservations made easy.", 'pubzinne' ),
		'required'    => false,
		'logo'        => 'restaurant.png',
		'group'       => $pubzinne_theme_required_plugins_groups['other'],
	)
);

if ( PUBZINNE_THEME_FREE ) {
	unset( $pubzinne_theme_required_plugins['js_composer'] );
	unset( $pubzinne_theme_required_plugins['vc-extensions-bundle'] );
	unset( $pubzinne_theme_required_plugins['easy-digital-downloads'] );
	unset( $pubzinne_theme_required_plugins['give'] );
	unset( $pubzinne_theme_required_plugins['bbpress'] );
	unset( $pubzinne_theme_required_plugins['booked'] );
	unset( $pubzinne_theme_required_plugins['content_timeline'] );
	unset( $pubzinne_theme_required_plugins['mp-timetable'] );
	unset( $pubzinne_theme_required_plugins['learnpress'] );
	unset( $pubzinne_theme_required_plugins['the-events-calendar'] );
	unset( $pubzinne_theme_required_plugins['calculated-fields-form'] );
	unset( $pubzinne_theme_required_plugins['essential-grid'] );
	unset( $pubzinne_theme_required_plugins['revslider'] );
	unset( $pubzinne_theme_required_plugins['ubermenu'] );
	unset( $pubzinne_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $pubzinne_theme_required_plugins['envato-market'] );
	unset( $pubzinne_theme_required_plugins['trx_updater'] );
	unset( $pubzinne_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
pubzinne_storage_set( 'required_plugins', $pubzinne_theme_required_plugins );
