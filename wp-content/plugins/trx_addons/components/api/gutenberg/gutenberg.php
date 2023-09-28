<?php
/**
 * Plugin support: Gutenberg
 *
 * @package ThemeREX Addons
 * @since v1.0.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! defined( 'TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY' ) ) {
	define( 'TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY', esc_html__( 'Block is cannot be rendered because has not content. Try to change attributes or add a content.', 'trx_addons' ) );
}

// Check if plugin 'Gutenberg' is installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( ! function_exists( 'trx_addons_exists_gutenberg' ) ) {
	function trx_addons_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}
*/

if ( ! function_exists( 'trx_addons_gutenberg_is_preview' ) ) {
	/**
	 * Check if current mode is preview (edit) of the Gutenberg block editor
	 *
	 * @return boolean  true - preview mode, false - not preview mode
	 */
	function trx_addons_gutenberg_is_preview() {
		return trx_addons_exists_gutenberg() 
				&& (
					trx_addons_gutenberg_is_block_render_action()
					||
					trx_addons_is_post_edit()
					||
					trx_addons_gutenberg_is_widgets_block_editor()
					||
					trx_addons_gutenberg_is_site_editor()
					);
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_is_site_editor' ) ) {
	/**
	 * Check if current mode is "Site Editor" (a new full site editing experience with Gutenberg support)
	 *
	 * @return boolean  true - Site Editor mode, false - not Site Editor mode
	 */
	function trx_addons_gutenberg_is_site_editor() {
		return is_admin()
				&& trx_addons_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.9', '>=' )
				&& trx_addons_check_url( 'site-editor.php' )
				&& function_exists( 'wp_is_block_theme' )
				&& wp_is_block_theme();
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_is_widgets_block_editor' ) ) {
	/**
	 * Check if current mode is "Widgets Block Editor" (a new block-based widget management experience)
	 *
	 * @return boolean  true - Widgets Block Editor mode, false - not Widgets Block Editor mode
	 */
	function trx_addons_gutenberg_is_widgets_block_editor() {
		return is_admin()
				&& trx_addons_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.8', '>=' )
				&& trx_addons_check_url( 'widgets.php' )
				&& function_exists( 'wp_use_widgets_block_editor' )
				&& wp_use_widgets_block_editor();
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_is_block_render_action' ) ) {
	/**
	 * Check if current mode is "Block Render" (a new block-based widget management experience)
	 *
	 * @return boolean  true - Block Render mode, false - not Block Render mode
	 */
	function trx_addons_gutenberg_is_block_render_action() {
		return trx_addons_exists_gutenberg() 
				&& trx_addons_check_url('block-renderer') && !empty($_GET['context']) && $_GET['context']=='edit';
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_is_content_built' ) ) {
	/**
	 * Check if content is built with Gutenberg
	 *
	 * @param string $content  Content to check
	 * @return boolean  true - content is built with Gutenberg, false - content is not built with Gutenberg
	 */
	function trx_addons_gutenberg_is_content_built( $content ) {
		return trx_addons_exists_gutenberg() 
				&& has_blocks( $content );	//strpos($content, '<!-- wp:') !== false;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_gutenberg_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Enqueue required styles and scripts for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 */
	function trx_addons_gutenberg_load_scripts_front() {
		if ( trx_addons_exists_gutenberg() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'trx_addons-gutenberg', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg.css' ), array(), null );
		}
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_load_responsive_styles' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_gutenberg_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	/**
	 * Enqueue responsive styles for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 */
	function trx_addons_gutenberg_load_responsive_styles() {
		if ( trx_addons_exists_gutenberg() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'trx_addons-gutenberg-responsive',
								trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg.responsive.css'),
								array(),
								null,
								trx_addons_media_for_load_css_responsive( 'gutenberg', 'lg' )
							);
		}
	}
}
	
if ( ! function_exists( 'trx_addons_gutenberg_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_gutenberg_merge_styles' );
	/**
	 * Add a plugin-specific frontend styles to the list for merge to the single stylesheet
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 *
	 * @param array $list  List of styles to merge
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_merge_styles( $list ) {
		if ( trx_addons_exists_gutenberg() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_gutenberg_merge_styles_responsive');
	/**
	 * Add a plugin-specific frontend responsive styles to the list for merge to the single stylesheet
	 * 
	 * @hooked trx_addons_filter_merge_styles_responsive
	 *
	 * @param array $list  List of styles to merge
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_merge_styles_responsive( $list ) {
		if ( trx_addons_exists_gutenberg() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg.responsive.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_theme_setup8' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_gutenberg_theme_setup8', 8 );
	/**
	 * Add editor styles to the Gutenberg editor
	 * 
	 * @hooked after_setup_theme, 8
	 * 
	 * @trigger trx_addons_filter_add_editor_style
	 */
	function trx_addons_gutenberg_theme_setup8() {
		if ( is_admin() && ( ! is_rtl() || ! is_customize_preview() ) ) {
			if ( trx_addons_exists_gutenberg() && trx_addons_gutenberg_is_preview() ) {
				if ( ! trx_addons_get_setting( 'gutenberg_add_context' ) ) {
					add_editor_style( apply_filters( 'trx_addons_filter_add_editor_style', array() ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_add_editor_style_icons' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'trx_addons_gutenberg_add_editor_style_icons', 10 );
	/**
	 * Add plugin's icons styles to the Gutenberg editor
	 * 
	 * @hooked trx_addons_filter_add_editor_style
	 * 
	 * @param array $styles  List of file urls with styles to add
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_add_editor_style_icons( $styles ) {
		$trx_addons_url = trx_addons_get_file_url( 'css/font-icons/css/trx_addons_icons.css' );
		if ( '' != $trx_addons_url ) {
			$styles[] = $trx_addons_url;
		}
		return $styles;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_add_editor_style' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'trx_addons_gutenberg_add_editor_style', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Add plugin's styles to the Gutenberg editor
	 * 
	 * @hooked trx_addons_filter_add_editor_style
	 * 
	 * @param array $styles  List of file urls with styles to add
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_add_editor_style( $styles ) {
		$trx_addons_url = trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.css' );
		if ( '' != $trx_addons_url ) {
			$styles[] = $trx_addons_url;
		}
		return $styles;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_add_editor_style_responsive' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'trx_addons_gutenberg_add_editor_style_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	/**
	 * Add plugin's responsive styles to the Gutenberg editor
	 * 
	 * @hooked trx_addons_filter_add_editor_style
	 * 
	 * @param array $styles  List of file urls with styles to add
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_add_editor_style_responsive( $styles ) {
		$trx_addons_url = trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.responsive.css' );
		if ( '' != $trx_addons_url ) {
			$styles[] = $trx_addons_url;
		}
		return $styles;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_add_editor_style_remove_theme_url' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'trx_addons_gutenberg_add_editor_style_remove_theme_url', 9999 );
	$trx_addons_theme_slug = $trx_addons_child_slug = '';
	if ( function_exists( 'get_template' ) ) {
		$trx_addons_theme_slug = str_replace( '-', '_', get_template() );
		add_filter( "{$trx_addons_theme_slug}_filter_add_editor_style", 'trx_addons_gutenberg_add_editor_style_remove_theme_url', 9999 );
	}
	/**
	 * Hack to prevent remote loading each .css-file before the Gutenberg editor started to avoid a long-time loading
	 * ( and many http-requests can cause a timeout error on some hostings )
	 * Replace an absolute URL to the main-theme, child-theme and plugins directories
	 * to the relative path ( started from the current theme directory ) in the each editor-style path
	 * 
	 * @hooked trx_addons_filter_add_editor_style
	 * @hooked {$theme_slug}_filter_add_editor_style
	 * 
	 * @param array $styles  List of file urls with styles to add
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_gutenberg_add_editor_style_remove_theme_url( $styles ) {
		if ( is_array( $styles ) ) {
			$template_uri   = trailingslashit( get_template_directory_uri() );
			$stylesheet_uri = trailingslashit( get_stylesheet_directory_uri() );
			$plugins_uri    = trailingslashit( defined( 'WP_PLUGIN_URL' ) ? WP_PLUGIN_URL : plugins_url() );
			$theme_replace  = '';
			$plugin_replace = '../'            // up to the folder 'themes'
								. '../'        // up to the folder 'wp-content'
								. 'plugins/';  // open the folder 'plugins'
			foreach( $styles as $k => $v ) {
				$styles[ $k ] = str_replace(
									array(
										$template_uri,
										strpos( $template_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $template_uri ) : $template_uri,
										$stylesheet_uri,
										strpos( $stylesheet_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $stylesheet_uri ) : $stylesheet_uri,
										$plugins_uri,
										strpos( $plugins_uri, 'http:' ) === 0 ? str_replace( 'http:', 'https:', $plugins_uri ) : $plugins_uri,
									),
									array(
										$theme_replace,
										$theme_replace,
										$theme_replace,
										$theme_replace,
										$plugin_replace,
										$plugin_replace,
									),
									$v
								);
			}
		}
		return $styles;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_editor_load_scripts' ) ) {
	add_action( "enqueue_block_editor_assets", 'trx_addons_gutenberg_editor_load_scripts' );
	/**
	 * Enqueue styles and scripts for the Gutenberg editor
	 * 
	 * @hooked enqueue_block_editor_assets
	 * 
	 * @trigger trx_addons_action_pagebuilder_admin_scripts
	 */
	function trx_addons_gutenberg_editor_load_scripts() {
		trx_addons_load_scripts_admin(true);
		trx_addons_localize_scripts_admin();
		// Editor styles: register and enqueue style instead directly enqueue
		//                to allow to use this style as 'editorStyle'
		//                in the register_block() calls
		// wp_enqueue_style( 'trx_addons-gutenberg-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-editor.css' ), array(), null );
		wp_register_style( 'trx_addons-gutenberg-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-editor.css' ), array(), null );
		wp_enqueue_style( 'trx_addons-gutenberg-editor' );
		// Block styles
		if ( trx_addons_get_setting( 'gutenberg_add_context' ) ) {
			wp_enqueue_style( 'trx_addons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.css'), array(), null );
			wp_enqueue_style( 'trx_addons-responsive', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.responsive.css'), array(), null );
		}
		if (trx_addons_get_setting('allow_gutenberg_blocks')) {
			wp_enqueue_script( 'trx_addons-gutenberg-blocks',
								trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/blocks/dist/blocks.build.js'),
								trx_addons_block_editor_dependencis( true ),
								null,
								true
							);

			// Load Swiper slider script and styles
			trx_addons_enqueue_slider('swiper');
			trx_addons_enqueue_slider('elastistack');

			// Load Popup script and styles
			trx_addons_enqueue_popup();

			// Load merged scripts
			wp_enqueue_script( 'trx_addons', trx_addons_get_file_url( 'js/__scripts-full.js' ), apply_filters( 'trx_addons_filter_script_deps', array( 'jquery' ) ), null, true );
		}
		do_action('trx_addons_action_pagebuilder_admin_scripts');
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_editor_add_scripts_for_fse' ) ) {
	add_filter( 'trx_addons_gb_map', 'trx_addons_gutenberg_editor_add_scripts_for_fse', 10, 2 );
	/**
	 * Add 'gutenberg-editor' with general styles to the block dependencies for the FSE mode
	 * 
	 * @hooked trx_addons_gb_map
	 * 
	 * @param array $args  Array of arguments for the block
	 * @param string $sc   Shortcode name
	 * 
	 * @return array  Modified array of arguments
	 */
	function trx_addons_gutenberg_editor_add_scripts_for_fse( $args, $sc ) {
		global $pagenow;
		if ( 'site-editor.php' == $pagenow && 'trx-addons/blogger' == $sc && empty( $args['editor_style'] ) ) {
			$args['editor_style'] = apply_filters( 'trx_addons_filter_fse_general_styles', 'trx_addons-gutenberg-editor' );
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_block_editor_dependencis' ) ) {
	/**
	 * Return array of dependencies for the block editor
	 * 
	 * @trigger trx_addons_filter_block_editor_dependencies
	 *
	 * @param bool $only_core  If true - return only core dependencies. Otherwise - return all dependencies
	 * 						   with 'trx_addons-admin', 'trx_addons-utils', 'trx_addons-gutenberg-blocks'
	 * 
	 * @return array  Array of dependencies
	 */
	function trx_addons_block_editor_dependencis( $only_core = false ) {
		global $pagenow; 
		return apply_filters( 'trx_addons_filter_block_editor_dependencies', array_merge(
				array(
					'jquery',
					'wp-blocks',
					'wp-i18n',
					'wp-element',
					'wp-components',
				),
				// wp-editor should not be enqueued with the new Widgets Block Editor (starts from WordPress 5.8+)
				'widgets.php' == $pagenow
					? array( 'wp-edit-widgets' )
					: array( 'wp-editor' ),
				$only_core
					? array()
					: array(
						'trx_addons-admin',
						'trx_addons-utils',
						'trx_addons-gutenberg-blocks'
						)
				) );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_preview_load_scripts' ) ) {
	add_action( "enqueue_block_assets", 'trx_addons_gutenberg_preview_load_scripts' );
	/**
	 * Load required styles and scripts for both: Backend + Frontend mode
	 * 
	 * @hooked enqueue_block_assets
	 * 
	 * @trigger trx_addons_action_pagebuilder_preview_scripts
	 */
	function trx_addons_gutenberg_preview_load_scripts() {
		if ( trx_addons_gutenberg_is_preview() ) {
			do_action( 'trx_addons_action_pagebuilder_preview_scripts', 'gutenberg' );
		}
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_localize_script' ) ) {
	add_filter( "trx_addons_filter_localize_script", 'trx_addons_gutenberg_localize_script' );
	/**
	 * Add shortcode's specific vars to the JS storage for the frontend
	 * 
	 * @hooked trx_addons_filter_localize_script
	 *
	 * @param array $vars  Array with JS vars
	 * 
	 * @return array  Modified array with JS vars
	 */
	function trx_addons_gutenberg_localize_script( $vars ) {
		$vars['pagebuilder_preview_mode'] = ! empty( $vars['pagebuilder_preview_mode'] ) || trx_addons_gutenberg_is_preview();
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_localize_scripts_admin' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_gutenberg_localize_scripts_admin' );
	/**
	 * Add shortcode's specific vars to the JS storage for the backend
	 * 
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @trigger trx_addons_filter_gutenberg_sc_params
	 *
	 * @param array $vars  Array with JS vars
	 * 
	 * @return array  Modified array with JS vars
	 */
	function trx_addons_gutenberg_localize_scripts_admin( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();
			$vars['modify_gutenberg_blocks']  = trx_addons_get_setting( 'modify_gutenberg_blocks' );
			$vars['gutenberg_allowed_blocks'] = trx_addons_gutenberg_get_list_allowed_blocks();
			$vars['gutenberg_sc_params']      = apply_filters( 'trx_addons_filter_gutenberg_sc_params', array(
													'list_spacer_heights' => trx_addons_get_list_sc_empty_space_heights(),
													'theme_colors' => current( (array) get_theme_support( 'editor-color-palette' ) ),
													'sc_layouts' => ! $is_edit_mode ? array() : apply_filters( 'trx_addons_filter_gutenberg_sc_layouts', array() ),
												) );
		}
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_save_css' ) ) {
	add_action( 'trx_addons_action_save_options', 'trx_addons_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options_theme', 'trx_addons_gutenberg_save_css', 30 );
	/**
	 * Save CSS with custom colors and fonts to the gutenberg-preview.css
	 * 
	 * @hooked trx_addons_action_save_options
	 * @hooked trx_addons_action_save_options_theme
	 */
	function trx_addons_gutenberg_save_css() {
		$add_context = array(
							'context'      => '.edit-post-visual-editor ',
							'context_self' => array( 'html', 'body', '.edit-post-visual-editor' )
							);
		// Get main styles
		//-----------------------------------------------------------------------
		$css = trx_addons_fgc( trx_addons_get_file_dir( 'css/__styles-full.css' ) );
		// Add context class to each selector
		if ( trx_addons_get_setting( 'gutenberg_add_context' ) ) {
			$css = trx_addons_css_add_context( $css, $add_context );
		}
		// Save styles to the file
		trx_addons_fpc( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.css' ), $css );

		// Add responsive styles
		//----------------------------------------------------------------------------
		$css = trx_addons_fgc( trx_addons_get_file_dir( 'css/__responsive-full.css' ) );
		// Add context class to each selector
		if ( trx_addons_get_setting( 'gutenberg_add_context' ) ) {
			$css = trx_addons_css_add_context( $css, $add_context );
		}
		// Save styles to the file
		trx_addons_fpc( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.responsive.css' ), $css );

	}
}

if ( ! function_exists( 'trx_addons_gutenberg_enable_cpt' ) ) {
	add_filter( 'trx_addons_filter_register_post_type', 'trx_addons_gutenberg_enable_cpt', 10, 2 );
	/**
	 * Enable Gutenberg for our CPT
	 * 
	 * @hooked trx_addons_filter_register_post_type
	 * 
	 * @trigger trx_addons_filter_add_pt_to_gutenberg
	 *
	 * @param array $args         Array of arguments for registering a post type.
	 * @param string $post_type   Post type key.
	 * 
	 * @return array              Modified array of arguments
	 */
	function trx_addons_gutenberg_enable_cpt( $args, $post_type ) {
		if ( trx_addons_exists_gutenberg() && apply_filters( 'trx_addons_filter_add_pt_to_gutenberg', false, $post_type ) ) {
			$args['show_in_rest'] = true;
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_enable_taxonomies' ) ) {
	add_filter( 'trx_addons_filter_register_taxonomy', 'trx_addons_gutenberg_enable_taxonomies', 10, 3 );
	/**
	 * Enable Gutenberg for our taxonomies
	 * 
	 * @hooked trx_addons_filter_register_taxonomy
	 * 
	 * @trigger trx_addons_filter_add_taxonomy_to_gutenberg
	 *
	 * @param array $args         Array of arguments for registering a taxonomy
	 * @param string $post_type   Post type
	 * @param string $taxonomy    Taxonomy
	 * 
	 * @return array              Modified array of arguments
	 */
	function trx_addons_gutenberg_enable_taxonomies( $args, $post_type, $taxonomy ) {
		if ( trx_addons_exists_gutenberg()
			&& ( ! isset( $args['meta_box_cb'] ) || $args['meta_box_cb'] !== false )
			&& apply_filters( 'trx_addons_filter_add_taxonomy_to_gutenberg', false, $taxonomy )
		) {
			$args['show_in_rest'] = true;
		}
		return $args;
	}
}


//------------------------------------------------------------
//-- Compatibility Gutenberg and other PageBuilders
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_gutenberg_disable_cpt' ) ) {
	add_filter( 'gutenberg_can_edit_post_type', 'trx_addons_gutenberg_disable_cpt', 999, 2 );
	/**
	 * Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)
	 * 
	 * @hooked gutenberg_can_edit_post_type
	 * 
	 * @param boolean $can        Whether the post type can be edited or not.
	 * @param string  $post_type  The post type being checked.
	 * 
	 * @return boolean            Modified value
	 */
	function trx_addons_gutenberg_disable_cpt( $can, $post_type ) {
		$safe_pb = (array)trx_addons_get_setting( 'gutenberg_safe_mode' );
		if ( $can && ! empty( $safe_pb ) ) {
			$disable = false;
			if ( ! $disable && in_array( 'elementor', $safe_pb ) && trx_addons_exists_elementor() ) {
				$post_types = get_post_types_by_support( 'elementor' );
				$disable = is_array( $post_types ) && in_array( $post_type, $post_types );
			}
			if ( ! $disable && in_array( 'vc', $safe_pb ) && trx_addons_exists_vc() ) {
				$post_types = function_exists( 'vc_editor_post_types' ) ? vc_editor_post_types() : array();
				$disable = is_array( $post_types ) && in_array( $post_type, $post_types );
			}
			$can = ! $disable;
		}
		return $can;
	}
}


//------------------------------------------------------------
//-- Shortcodes support
//-------------------------------------------------------------

if ( ! function_exists( 'trx_addons_gutenberg_sc_items_attributes' ) ) {
	add_action( 'trx_addons_action_sc_show_attributes', 'trx_addons_gutenberg_sc_items_attributes', 10, 3 );
	/**
	 * Add attribute 'key' to the slides, columns and masonry items in the Gutenberg preview mode.
	 * Because the Gutenberg needs unique key for each item to render it correctly.
	 * 
	 * @hooked trx_addons_action_sc_show_attributes
	 * 
	 * @param string $sc    Shortcode name
	 * @param array $args   Shortcode attributes
	 * @param string $area  Area to show attributes: 'sc_item_list' - for the list of items, 'sc_item_wrapper' - for the wrapper of item
	 */
	function trx_addons_gutenberg_sc_items_attributes( $sc, $args, $area ) {
		static $key = 1;
		// Remove false to add attribute 'key' to the slides, columns and masonry items in the Gutenberg preview mode
		if ( false && in_array( $area, array( 'sc_item_list', 'sc_item_wrapper' ) ) && trx_addons_gutenberg_is_preview() ) {
			echo ' key="' . esc_attr( $key++ ) . '" ';
		}
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_print_inline_css' ) ) {
	add_filter( 'trx_addons_sc_output', 'trx_addons_gutenberg_print_inline_css', 10, 4 );
	/**
	 * Add inline CSS to the shortcode's layout if called from AJAX with action 'block-render'
	 *
	 * @param string $output  Shortcode output
	 * @param string $sc      Shortcode name
	 * @param array $atts     Shortcode attributes
	 * @param string $content Shortcode content
	 *
	 * @return string         Modified output
	 */
	function trx_addons_gutenberg_print_inline_css( $output, $sc, $atts, $content ) {
		if ( trx_addons_gutenberg_is_block_render_action() ) {
			$css = trx_addons_get_inline_css( true );
			if ( ! empty($css ) ) {
				$output .= sprintf( '<style type="text/css">%s</style>', $css );
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_list_allowed_blocks' ) ) {
	/**
	 * Return list of blocks, allowed inside block-container (i.e. "Content area")
	 * 
	 * @trigger trx_addons_filter_gutenberg_allowed_blocks
	 *
	 * @param string|array $exclude  List of blocks to exclude. Comma separated string or array.
	 *
	 * @return array  	   List of allowed blocks
	 */
	function trx_addons_gutenberg_get_list_allowed_blocks( $exclude = '' ) {
		if ( ! is_array( $exclude ) ) {
			$exclude = ! empty( $exclude ) ? explode( ',', $exclude ) : array();
		}
		// This way not include many 'core/xxx' blocks
		//$list = trx_addons_gutenberg_get_list_registered_blocks();
		// Manual way
		global $TRX_ADDONS_STORAGE;
		$list = array( 'core/archives',			'core/block',			'core/categories',
						'core/latest-comments',	'core/latest-posts',	'core/shortcode',
						'core/heading',			'core/subheading',		'core/paragraph',
						'core/quote',			'core/list',			'core/image',
						'core/gallery',			'core/audio',			'core/video',
						'core/code',			'core/classic',			'core/custom-html',
						'core/table',			'core/columns',			'core/spacer',
						'core/separator',		'core/button',			'core/more',
						'core/preformatted'
					);
		$registry = WP_Block_Type_Registry::get_instance();
		foreach ( $TRX_ADDONS_STORAGE['sc_list'] as $key => $value ) {
			$key = str_replace( '_', '-', $key );
			if ( $registry->is_registered( 'trx-addons/' . $key ) ) {
				$list[] = 'trx-addons/' . $key;
			}
		}
		foreach ( $TRX_ADDONS_STORAGE['widgets_list'] as $key => $value ) {
			$key = str_replace( '_', '-', $key );
			if ( $registry->is_registered( 'trx-addons/' . $key ) ) {
				$list[] = 'trx-addons/' . $key;
			}
		}
		foreach ( $TRX_ADDONS_STORAGE['cpt_list'] as $key => $value ) {
			$key = str_replace( '_', '-', $key );
			if ( $registry->is_registered( 'trx-addons/' . $key ) ) {
				$list[] = 'trx-addons/' . $key;
			}
		}
		foreach ( trx_addons_components_get_allowed_layouts( 'cpt', 'layouts', 'sc' ) as $sc => $title ) {
			$sc = str_replace( '_', '-', $sc );
			if ( $registry->is_registered( 'trx-addons/layouts-' . $sc ) ) {
				$list[] = 'trx-addons/layouts-' . $sc;
			}
		}
		return apply_filters( 'trx_addons_filter_gutenberg_allowed_blocks', $list );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_list_registered_blocks' ) ) {
	/**
	 * Return list of registered blocks. 
	 * 
	 * @trigger trx_addons_filter_gutenberg_registered_blocks
	 *
	 * @param string $type  Type of blocks to return:
	 * 						'all' - all registered blocks,
	 * 						'dynamic' - dynamic blocks,
	 * 						'static' - static blocks
	 *
	 * @return array  	   List of registered blocks
	 */
	function trx_addons_gutenberg_get_list_registered_blocks( $type = 'all' ) {
		$list = array();
		if ( trx_addons_exists_gutenberg() ) {
			$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
			if ( is_array( $blocks ) ) {
				foreach( $blocks as $block ) {
					if (     $type == 'all'
						|| ( $type == 'dynamic' && $block->is_dynamic() )
						|| ( $type == 'static'  && ! $block->is_dynamic() )
					) {
						$list[] = $block->name;
					}
				}
			}
		}
		return apply_filters( 'trx_addons_filter_gutenberg_registered_blocks', $list );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_add_block_styles' ) ) {
	add_action( 'init', 'trx_addons_gutenberg_add_block_styles', 99 );
	/**
	 * Add the new style 'Align outside' to some blocks
	 * 
	 * @hooked init, 99
	 * 
	 * @trigger trx_addons_filter_add_block_styles
	 */
	function trx_addons_gutenberg_add_block_styles() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) && function_exists( 'register_block_style' ) ) {
			$blocks = apply_filters( 'trx_addons_filter_add_block_styles', array(
				'core/image',
				'core/latest-posts',
				'trx-addons/blogger'
			) );
			if ( is_array( $blocks ) ) {
				foreach( $blocks as $block ) {
					register_block_style( $block, array(
						'name'  => 'alignfar',
						'label' => __( 'Align Outside', 'trx_addons' ),
					) );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_block_categories' ) ) {
	if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
		add_filter( 'block_categories', 'trx_addons_gutenberg_block_categories', 10, 2 );
	} else {
		add_filter( 'block_categories_all', 'trx_addons_gutenberg_block_categories', 10, 2 );
	}
	/**
	 * Add new categories 'TRX Addons Blocks', 'TRX Addons Widgets', 'TRX Addons CPT', 'TRX Addons Layouts'
	 * to the list of categories for the Gutenberg editor
	 * 
	 * @hooked block_categories (for WP < 5.8)
	 * @hooked block_categories_all (for WP >= 5.8)
	 * 
	 * @param array $default_categories List of default categories
	 * @param object $post Post object
	 * 
	 * @return array List of categories
	 */
	function trx_addons_gutenberg_block_categories( $default_categories = array(), $post = false ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			$default_categories[] = array(
				'slug'  => 'trx-addons-blocks',
				'title' => __( 'TRX Addons Blocks', 'trx-addons' ),
			);
			$default_categories[] = array(
				'slug'  => 'trx-addons-widgets',
				'title' => __( 'TRX Addons Widgets', 'trx-addons' ),
			);
			$default_categories[] = array(
				'slug'  => 'trx-addons-cpt',
				'title' => __( 'TRX Addons Custom Post Types', 'trx-addons' ),
			);
			$default_categories[] = array(
				'slug'  => 'trx-addons-layouts',
				'title' => __( 'TRX Addons Layouts', 'trx-addons' ),
			);
		}
		return $default_categories;
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_query' ) ) {
	/**
	 * Return an array with parameters for the query ('count', 'offset', 'orderby', 'order', 'ids', 'columns')
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @param array $add_params  Additional params to add/replace in the default list
	 *
	 * @return array  		Array with parameters for the query
	 */
	function trx_addons_gutenberg_get_param_query( $add_params = array() ) {
		$params = array(
			'ids'           => array(
				'type'    => 'string',
				'default' => '',
			),
			'count'			=> array(
				'type'    => 'number',
				'default' => 2,
			),
			'columns'		=> array(
				'type'    => 'number',
				'default' => 2,
			),
			'offset'		=> array(
				'type'    => 'number',
				'default' => 0,
			),
			'orderby'		=> array(
				'type'    => 'string',
				'default' => 'none',
			),
			'order'			=> array(
				'type'    => 'string',
				'default' => 'asc',
			)
		);
		foreach( $add_params as $k=>$v ) {
			if ( $v == false ) {
				if ( isset( $params[$k] ) ) {
					unset( $params[$k] );
				}
			} else if ( is_array( $v ) ) {
				$params[$k] = array_merge( $params[$k], $v );
			}
		}
		return apply_filters( 'trx_addons_gb_map', $params, 'common/query' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_filters' ) ) {
	/**
	 * Return an array with parameters for the filters ('show_filters', 'filters_tabs_position', 'filters_tabs_on_hover',
	 * 'filters_title', 'filters_subtitle', 'filters_title_align', etc.)
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @return array  		Array with parameters for the filters
	 */
	function trx_addons_gutenberg_get_param_filters() {
		return apply_filters( 'trx_addons_gb_map', array(
			'show_filters'		=> array(
				'type'    => 'boolean',
				'default' => false,
			),
			'filters_tabs_position' => array(
				'type'    => 'string',
				'default' => 'top',
			),
			'filters_tabs_on_hover' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'filters_title'		=> array(
				'type'    => 'string',
				'default' => '',
			),
			'filters_subtitle'	=> array(
				'type'    => 'string',
				'default' => '',
			),
			'filters_title_align'=> array(
				'type'    => 'string',
				'default' => 'left',
			),
			'filters_taxonomy'	=> array(
				'type'    => 'string',
				'default' => 'category',
			),
			'filters_ids'		=> array(
				'type'    => 'string',
				'default' => '',
			),
			'filters_all'		=> array(
				'type'    => 'boolean',
				'default' => true,
			),
			'filters_all_text'	=> array(
				'type'    => 'string',
				'default' => esc_html__('All','trx_addons')
			),
			'filters_more_text'	=> array(
				'type'    => 'string',
				'default' => esc_html__('More posts','trx_addons')
			)
		), 'common/filters' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_slider' ) ) {
	/**
	 * Return an array with parameters for the slider ('slider', 'slider_effect', 'slides_space', 'slides_centered',
	 * 'slides_overflow', 'slider_mouse_wheel', etc.)
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @return array  		Array with parameters for the slider
	 */
	function trx_addons_gutenberg_get_param_slider() {
		return apply_filters( 'trx_addons_gb_map', array(
			'slider'             => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'slider_effect'      => array(
				'type'    => 'string',
				'default' => 'slide',
			),
			'slides_space'       => array(
				'type'    => 'number',
				'default' => 0,
			),
			'slides_centered'    => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'slides_overflow'    => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'slider_mouse_wheel' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'slider_autoplay'    => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'slider_loop'   => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'slider_free_mode'   => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'slider_controls'    => array(
				'type'    => 'string',
				'default' => 'none',
			),
			'slider_pagination'  => array(
				'type'    => 'string',
				'default' => 'none',
			),
			'slider_pagination_type'  => array(
				'type'    => 'string',
				'default' => 'bullets',
			)
		), 'common/slider' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_button' ) ) {
	/**
	 * Return an array with parameters for the button ('link', 'link_text', 'link_style', 'link_size', 'link_image', 'link_image_url')
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @return array  		Array with parameters for the button
	 */
	function trx_addons_gutenberg_get_param_button() {
		return apply_filters( 'trx_addons_gb_map', array(
			// Button attributes
			'link'               => array(
				'type'    => 'string',
				'default' => '',
			),
			'link_text'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'link_size'          => array(
				'type'    => 'string',
				'default' => 'normal',
			),
			'link_style'         => array(
				'type'    => 'string',
				'default' => '',
			),
			'link_image'         => array(
				'type'    => 'number',
				'default' => 0,
			),
			'link_image_url'     => array(
				'type'    => 'string',
				'default' => '',
			)
		), 'common/button' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_button2' ) ) {
	/**
	 * Return an array with parameters for the button 2 ( 'link2', 'link2_text', 'link2_style' ) for shortcodes with 2 buttons
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @return array  		Array with parameters for the button 2
	 */
	function trx_addons_gutenberg_get_param_button2() {
		return apply_filters( 'trx_addons_gb_map', array(
			'link2'              => array(
				'type'    => 'string',
				'default' => '',
			),
			'link2_text'         => array(
				'type'    => 'string',
				'default' => '',
			),
			'link2_style'        => array(
				'type'    => 'string',
				'default' => '',
			)
		), 'common/button2' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_title' ) ) {
	/**
	 * Return an array with parameters for the title ('title_style', 'title_tag', 'title_align', 'title', 'title_color',
	 * 'title_color2', etc.)
	 * 
	 * @trigger trx_addons_gb_map
	 *
	 * @return array  		Array with parameters for the title
	 */
	function trx_addons_gutenberg_get_param_title() {
		return apply_filters( 'trx_addons_gb_map', array(
			'title_style'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'title_tag'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'title_align'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'title'              => array(
				'type'    => 'string',
				'default' => '',
			),
			'title_color'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'title_color2'       => array(
				'type'    => 'string',
				'default' => '',
			),
			'gradient_fill'      => array(
				'type'    => 'string',
				'default' => 'block',
			),
			'gradient_direction' => array(
				'type'    => 'string',
				'default' => '0',
			),
			'title_border_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'title_border_width' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'title_bg_image'     => array(
				'type'    => 'number',
				'default' => 0,
			),
			'title_bg_image_url' => array(
				'type'    => 'string',
				'default' => '',
			),
			'title2'             => array(
				'type'    => 'string',
				'default' => '',
			),
			'title2_color'       => array(
				'type'    => 'string',
				'default' => '',
			),
			'title2_color2'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'gradient_fill2'     => array(
				'type'    => 'string',
				'default' => 'block',
			),
			'gradient_direction2'=> array(
				'type'    => 'string',
				'default' => '0',
			),
			'title2_border_color'=> array(
				'type'    => 'string',
				'default' => '',
			),
			'title2_border_width'=> array(
				'type'    => 'number',
				'default' => 0,
			),
			'title2_bg_image'    => array(
				'type'    => 'number',
				'default' => 0,
			),
			'title2_bg_image_url'=> array(
				'type'    => 'string',
				'default' => '',
			),
			'subtitle'           => array(
				'type'    => 'string',
				'default' => '',
			),
			'subtitle_align'     => array(
				'type'    => 'string',
				'default' => 'none',
			),
			'subtitle_position'  => array(
				'type'    => 'string',
				'default' => trx_addons_get_setting( 'subtitle_above_title' ) ? 'above' : 'below',
			),
			'subtitle_color'     => array(
				'type'    => 'string',
				'default' => '',
			),
			'description'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'description_color'  => array(
				'type'    => 'string',
				'default' => '',
			),
			'mouse_helper_highlight' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'typed'              => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'typed_loop'         => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'typed_cursor'       => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'typed_strings'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'typed_color'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'typed_speed'        => array(
				'type'    => 'number',
				'default' => 6,
			),
			'typed_delay'        => array(
				'type'    => 'number',
				'default' => 1,
			)
		), 'common/title' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_hide' ) ) {
	/**
	 * Return array with parameters for 'hide_on_xxx' attributes
	 * 
	 * @hooked trx_addons_gb_map
	 *
	 * @param bool $frontpage  true if need to add 'hide_on_frontpage' attribute
	 *
	 * @return array  Array with parameters
	 */
	function trx_addons_gutenberg_get_param_hide( $frontpage = false ) {
		return apply_filters('trx_addons_gb_map', array_merge(
			array(
				'hide_on_wide'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_desktop'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_notebook' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_tablet'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_mobile'   => array(
					'type'    => 'boolean',
					'default' => false,
				)
			),
			! $frontpage ? array() : array(
				'hide_on_frontpage' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_singular'  => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hide_on_other'     => array(
					'type'    => 'boolean',
					'default' => false,
				)
			)
		), 'common/hide' );
	}
}

if ( ! function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	/**
	 * Return array with parameters for 'id', 'class', 'css' attributes
	 * 
	 * @hooked trx_addons_gb_map
	 *
	 * @return array  Array with parameters
	 */
	function trx_addons_gutenberg_get_param_id() {
		return apply_filters('trx_addons_gb_map', array(
			'id'                => array(
				'type'    => 'string',
				'default' => '',
			),
			'class'             => array(
				'type'    => 'string',
				'default' => '',
			),
			'className'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'css'               => array(
				'type'    => 'string',
				'default' => '',
			)
		), 'common/id' );
	}
}
