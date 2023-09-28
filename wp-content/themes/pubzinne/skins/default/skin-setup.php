<?php
/**
 * Skin Setup
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.76.0
 */


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup', 1 );
	function pubzinne_skin_setup() {
		$GLOBALS['PUBZINNE_STORAGE'] = array_merge( $GLOBALS['PUBZINNE_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-axiom',

			'theme_doc_url'       => '//pubzinne.axiomthemes.com/doc',

			'theme_demofiles_url' => '//demofiles.axiomthemes.com/pubzinne/',
			
			'theme_rate_url'      => '//themeforest.net/download',

			'theme_custom_url'    => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themeinstall',

			'theme_download_url'  => '//themeforest.net/item/pubzinne-sports-bar-wordpress-theme/26405573',         // Axiom

			'theme_support_url'   => '//themerex.net/support/',                                   // Axiom

			'theme_video_url'     => '//www.youtube.com/channel/UCBjqhuwKj3MfE3B6Hg2oA8Q',   // Axiom

			'theme_privacy_url'   => '//axiomthemes.com/privacy-policy/',                    // Axiom

			'portfolio_url'       => '//themeforest.net/user/axiomthemes/portfolio',         // Axiom

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup_fonts', 1 );
	function pubzinne_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be Google fonts or uploaded fonts, placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		pubzinne_storage_set(
			'load_fonts', array(
				// Google font
				array(
					'name'   => 'Libre Baskerville',
					'family' => 'serif',
					'styles' => '400,700',     // Parameter 'style' used only for the Google fonts
				),
				// Font-face packed with theme
				array(
					'name'   => 'HunterRiver',
					'family' => 'sans-serif',
				),
				array(
					'name'   => 'DINCondensed-Bold',
					'family' => 'sans-serif',
				),
                array(
					'name'   => 'DINCondensed',
					'family' => 'sans-serif',
				),
                array(
					'name'   => 'DIN',
					'family' => 'sans-serif',
				),
			)
		);

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		pubzinne_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		$font_description = esc_html__( 'Font settings of the %s of the site. To correctly display the site on mobile devices, use only the following units: "rem", "em" or "ex"', 'pubzinne' );

		pubzinne_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'main text', 'pubzinne' ) ),
					'font-family'     => '"Libre Baskerville",serif',
					'font-size'       => '1rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.8125em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '',
					'margin-top'      => '0em',
					'margin-bottom'   => '1.5em',
				),
				'post'    => array(
					'title'           => esc_html__( 'Article text', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'article text', 'pubzinne' ) ),
					'font-family'     => '',
					'font-size'       => '',
					'font-weight'     => '',
					'font-style'      => '',
					'line-height'     => '',
					'text-decoration' => '',
					'text-transform'  => '',
					'letter-spacing'  => '',
					'margin-top'      => '',
					'margin-bottom'   => '',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H1', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed-Bold",sans-serif',
					'font-size'       => '4em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.328em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.95em',
					'margin-bottom'   => '0.18em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H2', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed-Bold",sans-serif',
					'font-size'       => '3em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.0952em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.0952em',
					'margin-bottom'   => '0.42em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H3', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed-Bold",sans-serif',
					'font-size'       => '2.25em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.1111em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.4545em',
					'margin-bottom'   => '0.5em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H4', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed-Bold",sans-serif',
					'font-size'       => '1.875em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.11em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.43em',
					'margin-bottom'   => '0.5em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H5', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed-Bold",sans-serif',
					'font-size'       => '1.5em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.3333em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.45em',
					'margin-bottom'   => '.39em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H6', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed",sans-serif',
					'font-size'       => '1.125em',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.333em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '1.8px',
					'margin-top'      => '1.706em',
					'margin-bottom'   => '0.6412em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'text of the logo', 'pubzinne' ) ),
					'font-family'     => '"Libre Baskerville",serif',
					'font-size'       => '1.8em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.25em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'buttons', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed",sans-serif',
					'font-size'       => '18px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '26px',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1.8px',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed",sans-serif',
					'font-size'       => '18px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.444em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1.8px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'pubzinne' ) ),
					'font-family'     => '"DIN",sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '19px',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.6px',
					'margin-top'      => '0.4em',
					'margin-bottom'   => '',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'main menu items', 'pubzinne' ) ),
					'font-family'     => '"DINCondensed",sans-serif',
					'font-size'       => '18px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.444em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1.8px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'pubzinne' ),
					'description'     => sprintf( $font_description, esc_html__( 'dropdown menu items', 'pubzinne' ) ),
					'font-family'     =>  '"DINCondensed",sans-serif',
					'font-size'       => '18px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.44em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1.8px',
				),
                'extra' => array(
                    'title'           => esc_html__( 'Extra font', 'pubzinne' ),
                    'description'     => sprintf( $font_description, esc_html__( 'extra font family', 'pubzinne' ) ),
                    'font-family'     =>  '"HunterRiver",sans-serif',
                ),
            )
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup_schemes', 1 );
	function pubzinne_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		pubzinne_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'pubzinne' ),
					'description' => esc_html__( 'Colors of the main content area', 'pubzinne' ),
				),
				'alter'   => array(
					'title'       => esc_html__( 'Alter', 'pubzinne' ),
					'description' => esc_html__( 'Colors of the alternative blocks (sidebars, etc.)', 'pubzinne' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'pubzinne' ),
					'description' => esc_html__( 'Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'pubzinne' ),
				),
				'inverse' => array(
					'title'       => esc_html__( 'Inverse', 'pubzinne' ),
					'description' => esc_html__( 'Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'pubzinne' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'pubzinne' ),
					'description' => esc_html__( 'Colors of the form fields (text field, textarea, select, etc.)', 'pubzinne' ),
				),
			)
		);

		pubzinne_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'pubzinne' ),
					'description' => esc_html__( 'Background color of this block in the normal state', 'pubzinne' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'pubzinne' ),
					'description' => esc_html__( 'Background color of this block in the hovered state', 'pubzinne' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'pubzinne' ),
					'description' => esc_html__( 'Border color of this block in the normal state', 'pubzinne' ),
				),
				'bd_hover'    => array(
					'title'       => esc_html__( 'Border hover', 'pubzinne' ),
					'description' => esc_html__( 'Border color of this block in the hovered state', 'pubzinne' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'pubzinne' ),
					'description' => esc_html__( 'Color of the text inside this block', 'pubzinne' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'pubzinne' ),
					'description' => esc_html__( 'Color of the dark text (bold, header, etc.) inside this block', 'pubzinne' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'pubzinne' ),
					'description' => esc_html__( 'Color of the light text (post meta, etc.) inside this block', 'pubzinne' ),
				),
				'text_link'   => array(
					'title'       => esc_html__( 'Link', 'pubzinne' ),
					'description' => esc_html__( 'Color of the links inside this block', 'pubzinne' ),
				),
				'text_hover'  => array(
					'title'       => esc_html__( 'Link hover', 'pubzinne' ),
					'description' => esc_html__( 'Color of the hovered state of links inside this block', 'pubzinne' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Link 2', 'pubzinne' ),
					'description' => esc_html__( 'Color of the accented texts (areas) inside this block', 'pubzinne' ),
				),
				'text_hover2' => array(
					'title'       => esc_html__( 'Link 2 hover', 'pubzinne' ),
					'description' => esc_html__( 'Color of the hovered state of accented texts (areas) inside this block', 'pubzinne' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Link 3', 'pubzinne' ),
					'description' => esc_html__( 'Color of the other accented texts (buttons) inside this block', 'pubzinne' ),
				),
				'text_hover3' => array(
					'title'       => esc_html__( 'Link 3 hover', 'pubzinne' ),
					'description' => esc_html__( 'Color of the hovered state of other accented texts (buttons) inside this block', 'pubzinne' ),
				),
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'pubzinne' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#EFEDE8', //+
					'bd_color'         => '#B7B4AC', //+

					// Text and links colors
					'text'             => '#746969', //+
					'text_light'       => '#828282', //+
					'text_dark'        => '#1E1E1E', //+
					'text_link'        => '#1E1E1E', //+
					'text_hover'       => '#F1C761', //+
					'text_link2'       => '#F1C761', //+
					'text_hover2'      => '#1E1E1E', //+
					'text_link3'       => '#F1C761',
					'text_hover3'      => '#4D4D4D', //+

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#EFEDE8', //+
					'alter_bg_hover'   => '#E4DFD8', //+
					'alter_bd_color'   => '#B7B4AC', //+
					'alter_bd_hover'   => '#AFA9A0', //+
					'alter_text'       => '#746969', //+
					'alter_light'      => '#828282', //+
					'alter_dark'       => '#1E1E1E', //+
					'alter_link'       => '#1E1E1E', //+
					'alter_hover'      => '#F1C761', //+
					'alter_link2'      => '#2A2A2A', //+
					'alter_hover2'     => '#80d572',
					'alter_link3'      => '#ffffff', //+
					'alter_hover3'     => '#F1C761',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1E1E1E', //+
					'extra_bg_hover'   => '#555151', //+
					'extra_bd_color'   => '#B7B4AC', //+
					'extra_bd_hover'   => '#E4DFD8', //+
					'extra_text'       => '#746969', //+
					'extra_light'      => '#EFEDE8', //+
					'extra_dark'       => '#EFEDE8', //+
					'extra_link'       => '#FFFFFF', //+
 					'extra_hover'      => '#F1C761', //+
					'extra_link2'      => '#1E1E1E', //+
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#F1C761',
					'extra_hover3'     => '#B7B4AC', //+

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#EFEDE8', //+
					'input_bg_hover'   => '#EFEDE8', //+
					'input_bd_color'   => '#CEC8C0', //+
					'input_bd_hover'   => '#AFA9A0', //+
					'input_text'       => '#746969', //+
					'input_light'      => '#746969', //+
					'input_dark'       => '#1E1E1E', //+

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#1E1E1E', //+
					'inverse_bd_hover' => '#B7B4AC', //+
					'inverse_text'     => '#EFEDE8', //+
					'inverse_light'    => '#EFEDE8', //+
					'inverse_dark'     => '#ffffff', //+
					'inverse_link'     => '#ffffff', //+
					'inverse_hover'    => '#F1C761', //+

				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'pubzinne' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#1E1E1E', //+
					'bd_color'         => '#555151', //+

					// Text and links colors
					'text'             => '#828282', //+
					'text_light'       => '#828282', //+
					'text_dark'        => '#ffffff', //+
					'text_link'        => '#FFFFFF', //+
					'text_hover'       => '#F1C761', //+
					'text_link2'       => '#555151', //+
					'text_hover2'      => '#F1C761', //+
					'text_link3'       => '#F1C761',
					'text_hover3'      => '#4D4D4D', //+

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#1E1E1E', //+
					'alter_bg_hover'   => '#EFEDE8', //+
					'alter_bd_color'   => '#555151', //+
					'alter_bd_hover'   => '#B1AFAF', //+
					'alter_text'       => '#828282', //+
					'alter_light'      => '#828282', //+
					'alter_dark'       => '#ffffff', //+
					'alter_link'       => '#FFFFFF', //+
					'alter_hover'      => '#F1C761', //+
					'alter_link2'      => '#2A2A2A', //+
					'alter_hover2'     => '#80d572',
					'alter_link3'      => '#ffffff', //+
					'alter_hover3'     => '#F1C761',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#EFEDE8', //+
					'extra_bg_hover'   => '#B7B4AC', //+
					'extra_bd_color'   => '#555151', //+
					'extra_bd_hover'   => '#B7B4AC', //+
					'extra_text'       => '#746969', //+
					'extra_light'      => '#746969',
					'extra_dark'       => '#1E1E1E', //+
					'extra_link'       => '#746969', //+
					'extra_hover'      => '#1E1E1E', //+
					'extra_link2'      => '#746969', //+
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#F1C761',
					'extra_hover3'     => '#ffffff', //+

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#2A2A2A', //+
					'input_bg_hover'   => '#2A2A2A', //+
					'input_bd_color'   => '#B7B4AC', //+
					'input_bd_hover'   => '#EFEDE8', //+
					'input_text'       => '#746969', //+
					'input_light'      => '#ffffff', //+
					'input_dark'       => '#ffffff', //+

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#1E1E1E', //+
					'inverse_bd_hover' => '#ffffff', //+
					'inverse_text'     => '#1E1E1E', //+
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#000000',
					'inverse_link'     => '#1E1E1E', //+
					'inverse_hover'    => '#F1C761', //+

				),
			),
		);
		pubzinne_storage_set( 'schemes', $schemes );
		pubzinne_storage_set( 'schemes_original', $schemes );


		pubzinne_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),
				'alter_bg_color_07' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.7,
				),
				'alter_bg_color_04' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.4,
				),
				'alter_bg_color_00' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0,
				),
				'alter_bg_color_02' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.2,
				),
				'alter_bd_color_02' => array(
					'color' => 'alter_bd_color',
					'alpha' => 0.2,
				),
				'alter_link_02'     => array(
					'color' => 'alter_link',
					'alpha' => 0.2,
				),
                'inverse_dark_02'     => array(
					'color' => 'inverse_dark',
					'alpha' => 0.2,
				),
				'alter_link_07'     => array(
					'color' => 'alter_link',
					'alpha' => 0.7,
				),
				'extra_bg_color_05' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.5,
				),
				'extra_bg_color_07' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.7,
				),
				'extra_link_02'     => array(
					'color' => 'extra_link',
					'alpha' => 0.2,
				),
                'extra_link_05'     => array(
                    'color' => 'extra_link',
                    'alpha' => 0.5,
                ),
				'extra_link_07'     => array(
					'color' => 'extra_link',
					'alpha' => 0.7,
				),
				'text_dark_07'      => array(
					'color' => 'text_dark',
					'alpha' => 0.7,
				),
                'text_dark_02'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.2,
                ),
                'text_dark_035'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.35,
                ),
				'text_link_02'      => array(
					'color' => 'text_link',
					'alpha' => 0.2,
				),
				'text_link_07'      => array(
					'color' => 'text_link',
					'alpha' => 0.7,
				),
                'text_hover_07'      => array(
                    'color' => 'text_hover',
                    'alpha' => 0.7,
                ),
                'input_light_05'      => array(
					'color' => 'input_light',
					'alpha' => 0.5,
				),
				'input_light_04'      => array(
					'color' => 'input_light',
					'alpha' => 0.4,
				),
                'input_light_08'      => array(
                    'color' => 'input_light',
                    'alpha' => 0.8,
                ),
				'text_link_blend'   => array(
					'color'      => 'text_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'alter_link_blend'  => array(
					'color'      => 'alter_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		pubzinne_storage_set(
			'schemes_simple', array(
				'text_link'        => array(
					'alter_hover'      => 1,
					'extra_link'       => 1,
					'inverse_bd_color' => 0.85,
					'inverse_bd_hover' => 0.7,
				),
				'text_hover'       => array(
					'alter_link'  => 1,
					'extra_hover' => 1,
				),
				'text_link2'       => array(
					'alter_hover2' => 1,
					'extra_link2'  => 1,
				),
				'text_hover2'      => array(
					'alter_link2'  => 1,
					'extra_hover2' => 1,
				),
				'text_link3'       => array(
					'alter_hover3' => 1,
					'extra_link3'  => 1,
				),
				'text_hover3'      => array(
					'alter_link3'  => 1,
					'extra_hover3' => 1,
				),
				'alter_link'       => array(),
				'alter_hover'      => array(),
				'alter_link2'      => array(),
				'alter_hover2'     => array(),
				'alter_link3'      => array(),
				'alter_hover3'     => array(),
				'extra_link'       => array(),
				'extra_hover'      => array(),
				'extra_link2'      => array(),
				'extra_hover2'     => array(),
				'extra_link3'      => array(),
				'extra_hover3'     => array(),
				'inverse_bd_color' => array(),
				'inverse_bd_hover' => array(),
			)
		);

		// Parameters to set order of schemes in the css
		pubzinne_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);
	}
}


//--------------------------------------------
// THUMBS
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup_thumbs' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup_thumbs', 1 );
	function pubzinne_skin_setup_thumbs() {
		pubzinne_storage_set(
			'theme_thumbs', apply_filters(
				'pubzinne_filter_add_thumb_sizes', array(
					// Width of the image is equal to the content area width (without sidebar)
					// Height is fixed
					'pubzinne-thumb-huge'        => array(
						'size'  => array( 1170, 658, true ),
						'title' => esc_html__( 'Huge image', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Width of the image is equal to the content area width (with sidebar)
					// Height is fixed
					'pubzinne-thumb-big'         => array(
						'size'  => array( 800, 460, true ),
						'title' => esc_html__( 'Large image', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-big',
					),

					// Width of the image is equal to the 1/3 of the content area width (without sidebar)
					// Height is fixed
					'pubzinne-thumb-med'         => array(
						'size'  => array( 370, 208, true ),
						'title' => esc_html__( 'Medium image', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-medium',
					),

					// Small square image (for avatars in comments, etc.)
					'pubzinne-thumb-tiny'        => array(
						'size'  => array( 90, 90, true ),
						'title' => esc_html__( 'Small square avatar', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-tiny',
					),

					// Width of the image is equal to the content area width (with sidebar)
					// Height is proportional (only downscale, not crop)
					'pubzinne-thumb-masonry-big' => array(
						'size'  => array( 760, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),

					// Width of the image is equal to the 1/3 of the full content area width (without sidebar)
					// Height is proportional (only downscale, not crop)
					'pubzinne-thumb-masonry'     => array(
						'size'  => array( 370, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'pubzinne' ),
						'subst' => 'trx_addons-thumb-masonry',
					),
                    'pubzinne-thumb-team'     => array(
                        'size'  => array( 470, 560, true ),
                        'title' => esc_html__( 'Team', 'pubzinne' ),
                        'subst' => 'trx_addons-thumb-team',
                    ),
				)
			)
		);
	}
}


//--------------------------------------------
// BLOG STYLES
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup_blog_styles' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup_blog_styles', 1 );
	function pubzinne_skin_setup_blog_styles() {

		$blog_styles = array(
			'excerpt' => array(
				'title'   => esc_html__( 'Standard', 'pubzinne' ),
				'archive' => 'index',
				'item'    => 'templates/content-excerpt',
				'styles'  => 'excerpt',
				'icon'    => "images/theme-options/blog-style/excerpt.png",
			),
			'classic' => array(
				'title'   => esc_html__( 'Classic', 'pubzinne' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3),
				'styles'  => 'classic',
				'icon'    => "images/theme-options/blog-style/classic-%d.png",
				'new_row' => true,
			),
		);
		if ( ! PUBZINNE_THEME_FREE ) {
			$blog_styles['classic-masonry']   = array(
				'title'   => esc_html__( 'Classic Masonry', 'pubzinne' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3 ),
				'styles'  => array( 'classic', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/classic-masonry-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio'] = array(
				'title'   => esc_html__( 'Portfolio', 'pubzinne' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3 ),
				'styles'  => 'portfolio',
				'icon'    => "images/theme-options/blog-style/portfolio-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio-masonry'] = array(
				'title'   => esc_html__( 'Portfolio Masonry', 'pubzinne' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3 ),
				'styles'  => array( 'portfolio', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/portfolio-masonry-%d.png",
				'new_row' => true,
			);
		}
		pubzinne_storage_set( 'blog_styles', apply_filters( 'pubzinne_filter_add_blog_styles', $blog_styles ) );
	}
}

if ( ! function_exists( 'pubzinne_filter_get_list_menu_hover' ) ) {
    add_filter( 'trx_addons_filter_get_list_menu_hover', 'pubzinne_filter_get_list_menu_hover' );
    function pubzinne_filter_get_list_menu_hover( $list ) {
        unset( $list['fade_box'] );
        unset( $list['slide_line'] );
        unset( $list['slide_box'] );
        unset( $list['zoom_line'] );
        unset( $list['path_line'] );
        unset( $list['roll_down'] );
        unset( $list['color_line'] );
        return $list;
    }
}

if ( ! function_exists( 'pubzinne_filter_get_list_input_hover' ) ) {
    add_filter( 'trx_addons_filter_get_list_input_hover', 'pubzinne_filter_get_list_input_hover' );
    function pubzinne_filter_get_list_input_hover( $list ) {
        unset( $list['accent'] );
        unset( $list['path'] );
        unset( $list['jump'] );
        unset( $list['underline'] );
        unset( $list['iconed'] );
        return $list;
    }
}


if ( ! function_exists( 'pubzinne_get_sys_info' ) ) {
    add_filter( 'trx_addons_filter_get_sys_info', 'pubzinne_get_sys_info', 9 );
    function pubzinne_get_sys_info( $options = array() ) {
        $options['php_version']['recommended'] = '7.0.0+';
        $options['wp_version']['recommended'] = '5.0.0+';
        return $options;
    }
}

if ( ! function_exists( 'pubzinne_skin_trx_popup_classes' ) ) {
    add_filter( 'trx_popup_filter_classes', 'pubzinne_skin_trx_popup_classes' );
    function pubzinne_skin_trx_popup_classes() {
        return 'scheme_dark';
    }
}


//--------------------------------------------
// SINGLE STYLES
//--------------------------------------------
if ( ! function_exists( 'pubzinne_skin_setup_single_styles' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_skin_setup_single_styles', 1 );
	function pubzinne_skin_setup_single_styles() {

		pubzinne_storage_set( 'single_styles', apply_filters( 'pubzinne_filter_add_single_styles', array(
			'style-2'   => array(
				'title'       => esc_html__( 'Style 2', 'pubzinne' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are inside the content area', 'pubzinne' ),
				'styles'      => 'style-2',
				'icon'        => "images/theme-options/single-style/style-2.png",
			),
            'style-3'   => array(
                'title'       => esc_html__( 'Style 3', 'pubzinne' ),
                'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are below the image', 'pubzinne' ),
                'styles'      => 'style-3',
                'icon'        => "images/theme-options/single-style/style-3.png",
            ),
		) ) );
	}
}
