<?php
/**
 * Skins support: Main skin file for the skin 'Default'
 *
 * Load scripts and styles,
 * and other operations that affect the appearance and behavior of the theme
 * when the skin is activated
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.46
 */


// SKIN SETUP
//--------------------------------------------------------------------

// Setup fonts, colors, blog and single styles, etc.
$pubzinne_skin_path = pubzinne_get_file_dir( pubzinne_skins_get_current_skin_dir() . 'skin-setup.php' );
if ( ! empty( $pubzinne_skin_path ) ) {
	require_once $pubzinne_skin_path;
}

// Skin options
$pubzinne_skin_path = pubzinne_get_file_dir( pubzinne_skins_get_current_skin_dir() . 'skin-options.php' );
if ( ! empty( $pubzinne_skin_path ) ) {
	require_once $pubzinne_skin_path;
}

// Required plugins
$pubzinne_skin_path = pubzinne_get_file_dir( pubzinne_skins_get_current_skin_dir() . 'skin-plugins.php' );
if ( ! empty( $pubzinne_skin_path ) ) {
	require_once $pubzinne_skin_path;
}

// Demo import
$pubzinne_skin_path = pubzinne_get_file_dir( pubzinne_skins_get_current_skin_dir() . 'skin-demo-importer.php' );
if ( ! empty( $pubzinne_skin_path ) ) {
	require_once $pubzinne_skin_path;
}


// TRX_ADDONS SETUP
//--------------------------------------------------------------------

// Filter to add in the required plugins list
// Priority 11 to add new plugins to the end of the list
if ( ! function_exists( 'pubzinne_skin_tgmpa_required_plugins' ) ) {
	add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_skin_tgmpa_required_plugins', 11 );
	function pubzinne_skin_tgmpa_required_plugins( $list = array() ) {
		// ToDo: Check if plugin is in the 'required_plugins' and add his parameters to the TGMPA-list
		//       Replace 'skin-specific-plugin-slug' to the real slug of the plugin
		if ( pubzinne_storage_isset( 'required_plugins', 'skin-specific-plugin-slug' ) ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'skin-specific-plugin-slug', 'title' ),
				'slug'     => 'skin-specific-plugin-slug',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter to add/remove components of ThemeREX Addons when current skin is active
if ( ! function_exists( 'pubzinne_skin_trx_addons_default_components' ) ) {
	add_filter('trx_addons_filter_load_options', 'pubzinne_skin_trx_addons_default_components', 20);
	function pubzinne_skin_trx_addons_default_components($components) {
		// ToDo: Set key value in the array $components to 0 (disable component) or 1 (enable component)
		//---> For example (enable reviews for posts):
		//---> $components['components_components_reviews'] = 1;
		return $components;
	}
}

// Filter to add/remove CPT
if ( ! function_exists( 'pubzinne_skin_trx_addons_cpt_list' ) ) {
	add_filter('trx_addons_cpt_list', 'pubzinne_skin_trx_addons_cpt_list');
	function pubzinne_skin_trx_addons_cpt_list( $list = array() ) {
		// ToDo: Unset CPT slug from list to disable CPT when current skin is active
		//---> For example to disable CPT 'Portfolio':
		//---> unset( $list['portfolio'] );
		return $list;
	}
}

// Filter to add/remove shortcodes
if ( ! function_exists( 'pubzinne_skin_trx_addons_sc_list' ) ) {
	add_filter('trx_addons_sc_list', 'pubzinne_skin_trx_addons_sc_list');
	function pubzinne_skin_trx_addons_sc_list( $list = array() ) {
		// ToDo: Unset shortcode's slug from list to disable shortcode when current skin is active
		//---> For example to disable shortcode 'Action':
		//---> unset( $list['action'] );

		// Also can be used to add/remove/modify shortcodes params
		//---> For example to add new template to the 'Blogger':
//		---> $list['blogger']['templates']['default']['new_template_slug'] = array(
//		--->		'title' => __('Title of the new template', 'pubzinne'),
//		--->		'layout' => array(
//		--->			'featured' => array(),
//		--->			'content' => array('meta_categories', 'title', 'excerpt', 'meta', 'readmore')
//		--->		)
//		---> );
		return $list;
	}
}

// Filter to add/remove widgets
if ( ! function_exists( 'pubzinne_skin_trx_addons_widgets_list' ) ) {
	add_filter('trx_addons_widgets_list', 'pubzinne_skin_trx_addons_widgets_list');
	function pubzinne_skin_trx_addons_widgets_list( $list = array() ) {
		// ToDo: Unset widget's slug from list to disable widget when current skin is active
		//---> For example to disable widget 'About Me':
		//---> unset( $list['aboutme'] );
		return $list;
	}
}

if ( ! function_exists( 'pubzinne_skin_customizer_theme_setup1' ) ) {
    add_action( 'after_setup_theme', 'pubzinne_skin_customizer_theme_setup1', 1 );
    function pubzinne_skin_customizer_theme_setup1() {
        pubzinne_storage_set_array('settings', 'thumbs_in_navigation', true );
    }
}


// Add custom animations
if ( ! function_exists( 'pubzinne_elm_add_theme_animations' ) ) {
    add_filter( 'elementor/controls/animations/additional_animations', 'pubzinne_elm_add_theme_animations' );
    function pubzinne_elm_add_theme_animations( $animations ) {
        return array_merge( $animations, array(
            esc_html__( 'Theme Specific', 'pubzinne' ) => array(
                'pubzinne-fadeinup' => esc_html__( 'Pubzinne - Fade In Up', 'pubzinne' ),
                'pubzinne-fadeinright' => esc_html__( 'Pubzinne - Fade In Right', 'pubzinne' ),
                'pubzinne-fadeinleft' => esc_html__( 'Pubzinne - Fade In Left', 'pubzinne' ),
                'pubzinne-fadeindown' => esc_html__( 'Pubzinne - Fade In Down', 'pubzinne' ),
                'pubzinne-fadein' => esc_html__( 'Pubzinne - Fade In', 'pubzinne' ),
                'pubzinne-blur' => esc_html__( 'Pubzinne - Blur', 'pubzinne' )
            )
        ) );
    }
}


// SCRIPTS AND STYLES
//--------------------------------------------------

// Enqueue skin-specific scripts
// Priority 1050 -  before main theme plugins-specific (1100)
if ( ! function_exists( 'pubzinne_skin_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'pubzinne_skin_frontend_scripts', 1050 );
	function pubzinne_skin_frontend_scripts() {
		$pubzinne_url = pubzinne_get_file_url( pubzinne_skins_get_current_skin_dir() . 'css/style.css' );
		if ( '' != $pubzinne_url ) {
			wp_enqueue_style( 'pubzinne-skin-' . esc_attr( pubzinne_skins_get_current_skin_name() ), $pubzinne_url, array(), null );
		}
		$pubzinne_url = pubzinne_get_file_url( pubzinne_skins_get_current_skin_dir() . 'skin.js' );
		if ( '' != $pubzinne_url ) {
			wp_enqueue_script( 'pubzinne-skin-' . esc_attr( pubzinne_skins_get_current_skin_name() ), $pubzinne_url, array( 'jquery' ), null, true );
		}
	}
}





// Custom styles
$pubzinne_style_path = pubzinne_get_file_dir( pubzinne_skins_get_current_skin_dir() . 'css/style.php' );
if ( ! empty( $pubzinne_style_path ) ) {
	require_once $pubzinne_style_path;
}
