<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_gutenberg_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_gutenberg_theme_setup9', 9 );
	function pubzinne_gutenberg_theme_setup9() {

		// Add wide and full blocks support
		add_theme_support( 'align-wide' );

		// Add editor styles to backend
		add_theme_support( 'editor-styles' );
		if ( is_admin() && ( ! is_rtl() || ! is_customize_preview() ) ) {
			if ( pubzinne_exists_gutenberg() && pubzinne_gutenberg_is_preview() ) {
				if ( ! pubzinne_get_theme_setting( 'gutenberg_add_context' ) ) {
					if ( ! pubzinne_exists_trx_addons() ) {
						// Attention! This place need to use 'trx_addons_filter' instead 'pubzinne_filter'
						add_editor_style( apply_filters( 'trx_addons_filter_add_editor_style', array(), 'gutenberg' ) );
					}
				}
			} else {
				add_editor_style( apply_filters( 'pubzinne_filter_add_editor_style', array(
					pubzinne_get_file_url( 'css/font-icons/css/fontello.css' ),
					pubzinne_get_file_url( 'css/editor-style.css' )
					), 'editor' )
				);
			}
		} else {
			add_editor_style( pubzinne_get_file_url( 'css/editor-style.css' ) );
		}

		if ( pubzinne_exists_gutenberg() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_gutenberg_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'pubzinne_gutenberg_responsive_styles', 2000 );
			add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_gutenberg_merge_styles' );
			add_filter( 'pubzinne_filter_merge_styles_responsive', 'pubzinne_gutenberg_merge_styles_responsive' );
		}
		add_action( 'enqueue_block_editor_assets', 'pubzinne_gutenberg_editor_scripts' );
		add_filter( 'pubzinne_filter_localize_script_admin',	'pubzinne_gutenberg_localize_script');
		add_action( 'after_setup_theme', 'pubzinne_gutenberg_add_editor_colors' );
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_gutenberg_tgmpa_required_plugins' );
			add_filter( 'pubzinne_filter_theme_plugins', 'pubzinne_gutenberg_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_gutenberg_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_gutenberg_tgmpa_required_plugins');
	function pubzinne_gutenberg_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'gutenberg' ) ) {
			if ( pubzinne_storage_get_array( 'required_plugins', 'gutenberg', 'install' ) !== false && version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
				$list[] = array(
					'name'     => pubzinne_storage_get_array( 'required_plugins', 'gutenberg', 'title' ),
					'slug'     => 'gutenberg',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'pubzinne_gutenberg_theme_plugins' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_theme_plugins', 'pubzinne_gutenberg_theme_plugins' );
	function pubzinne_gutenberg_theme_plugins( $list = array() ) {
		$group = ! empty( $list['gutenberg']['group'] )
					? $list['gutenberg']['group']
					: pubzinne_storage_get_array( 'required_plugins', 'gutenberg', 'group' ); 
		foreach ( $list as $k => $v ) {
			if ( in_array( $k, array( 'coblocks', 'kadence-blocks' ) ) ) {
				if ( empty( $v['group'] ) ) {
					$list[ $k ]['group'] = $group;
				}
				if ( empty( $v['logo'] ) ) {
					$logo = pubzinne_get_file_url( "plugins/gutenberg/{$k}.png" );
					$list[ $k ]['logo'] = empty( $logo )
											? ( ! empty( $list['gutenberg']['logo'] )
													? ( strpos( $list['gutenberg']['logo'], '//' ) !== false
														? $list['gutenberg']['logo']
														: pubzinne_get_file_url( "plugins/gutenberg/{$list['gutenberg']['logo']}" )
														)
												: ''
												)
											: $logo;
				}
			}
		}
		return $list;
	}
}


// Check if Gutenberg is installed and activated
if ( ! function_exists( 'pubzinne_exists_gutenberg' ) ) {
	function pubzinne_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}

// Return true if Gutenberg exists and current mode is preview
if ( ! function_exists( 'pubzinne_gutenberg_is_preview' ) ) {
	function pubzinne_gutenberg_is_preview() {
		return pubzinne_exists_gutenberg() 
				&& (
					pubzinne_gutenberg_is_block_render_action()
					||
					pubzinne_is_post_edit()
					);
	}
}

// Return true if current mode is "Block render"
if ( ! function_exists( 'pubzinne_gutenberg_is_block_render_action' ) ) {
	function pubzinne_gutenberg_is_block_render_action() {
		return pubzinne_exists_gutenberg() 
				&& pubzinne_check_url( 'block-renderer' ) && ! empty( $_GET['context'] ) && 'edit' == $_GET['context'];
	}
}

// Return true if content built with "Gutenberg"
if ( ! function_exists( 'pubzinne_gutenberg_is_content_built' ) ) {
	function pubzinne_gutenberg_is_content_built($content) {
		return pubzinne_exists_gutenberg() 
				&& has_blocks( $content );	// This condition is equval to: strpos($content, '<!-- wp:') !== false;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'pubzinne_gutenberg_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_gutenberg_frontend_scripts', 1100 );
	function pubzinne_gutenberg_frontend_scripts() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/gutenberg/gutenberg.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-gutenberg', $pubzinne_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'pubzinne_gutenberg_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_gutenberg_responsive_styles', 2000 );
	function pubzinne_gutenberg_responsive_styles() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/gutenberg/gutenberg-responsive.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-gutenberg-responsive', $pubzinne_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'pubzinne_gutenberg_merge_styles' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles', 'pubzinne_gutenberg_merge_styles');
	function pubzinne_gutenberg_merge_styles( $list ) {
		$list[] = 'plugins/gutenberg/gutenberg.css';
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'pubzinne_gutenberg_merge_styles_responsive' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles_responsive', 'pubzinne_gutenberg_merge_styles_responsive');
	function pubzinne_gutenberg_merge_styles_responsive( $list ) {
		$list[] = 'plugins/gutenberg/gutenberg-responsive.css';
		return $list;
	}
}


// Load required styles and scripts for Gutenberg Editor mode
if ( ! function_exists( 'pubzinne_gutenberg_editor_scripts' ) ) {
	//Handler of the add_action( 'enqueue_block_editor_assets', 'pubzinne_gutenberg_editor_scripts');
	function pubzinne_gutenberg_editor_scripts() {
		pubzinne_admin_scripts(true);
		pubzinne_admin_localize_scripts();
		// Editor styles
		wp_enqueue_style( 'pubzinne-gutenberg-editor', pubzinne_get_file_url( 'plugins/gutenberg/gutenberg-editor.css' ), array(), null );
		if ( pubzinne_get_theme_setting( 'gutenberg_add_context' ) ) {
			wp_enqueue_style( 'pubzinne-gutenberg-preview', pubzinne_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' ), array(), null );
		}
		// Editor scripts
		wp_enqueue_script( 'pubzinne-gutenberg-preview', pubzinne_get_file_url( 'plugins/gutenberg/gutenberg-preview.js' ), array( 'jquery' ), null, true );
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'pubzinne_gutenberg_localize_script' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_localize_script_admin',	'pubzinne_gutenberg_localize_script');
	function pubzinne_gutenberg_localize_script( $arr ) {
		// Color scheme
		$arr['color_scheme'] = pubzinne_get_theme_option( 'color_scheme' );
		// Sidebar position on the single posts
		$arr['sidebar_position'] = 'inherit';
		$arr['expand_content']   = 'inherit';
		$post_type               = 'post';
		$post_id                 = pubzinne_get_value_gpc( 'post' );
		if ( pubzinne_gutenberg_is_preview() && ! empty( $post_id ) ) {
			$post_type = pubzinne_get_edited_post_type();
			$meta = get_post_meta( $post_id, 'pubzinne_options', true );
			if ( 'page' != $post_type && ! empty( $meta['sidebar_position_single'] ) ) {
				$arr['sidebar_position'] = $meta['sidebar_position_single'];
			} elseif ( 'page' == $post_type && ! empty( $meta['sidebar_position'] ) ) {
				$arr['sidebar_position'] = $meta['sidebar_position'];
			}
			if ( isset( $meta['expand_content'] ) ) {
				$arr['expand_content'] = $meta['expand_content'];
			}
		}
		if ( 'inherit' == $arr['sidebar_position'] ) {
			if ( 'page' != $post_type ) {
				$arr['sidebar_position'] = pubzinne_get_theme_option( 'sidebar_position_single' );
				if ( 'inherit' == $arr['sidebar_position'] ) {
					$arr['sidebar_position'] = pubzinne_get_theme_option( 'sidebar_position_blog' );
				}
			}
			if ( 'inherit' == $arr['sidebar_position'] ) {
				$arr['sidebar_position'] = pubzinne_get_theme_option( 'sidebar_position' );
			}
		}
		if ( 'inherit' == $arr['expand_content'] ) {
			$arr['expand_content'] = isset( $meta['expand_content_single'] ) && 'inherit' != $meta['expand_content_single']
											? $meta['expand_content_single']
											: pubzinne_get_theme_option( 'expand_content_single' );
			if ( 'inherit' == $arr['expand_content'] && 'post' == $post_type ) {
				$arr['expand_content'] = pubzinne_get_theme_option( 'expand_content_blog' );
			}
			if ( 'inherit' == $arr['expand_content'] ) {
				$arr['expand_content'] = pubzinne_get_theme_option( 'expand_content' );
			}
		}
		$arr['expand_content'] = $arr['expand_content'];
		return $arr;
	}
}

// Save CSS with custom colors and fonts to the gutenberg-editor-style.css
if ( ! function_exists( 'pubzinne_gutenberg_save_css' ) ) {
	add_action( 'pubzinne_action_save_options', 'pubzinne_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options', 'pubzinne_gutenberg_save_css', 30 );
	function pubzinne_gutenberg_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'pubzinne' )
				. "\n----------------------------------------------------------------------- */\n";

		// Get main styles
		$css = apply_filters( 'pubzinne_filter_gutenberg_get_styles', pubzinne_fgc( pubzinne_get_file_dir( 'style.css' ) ) );

		// Append supported plugins styles
		$css .= pubzinne_fgc( pubzinne_get_file_dir( 'css/__plugins.css' ) );

		// Append theme-vars styles
		$css .= pubzinne_customizer_get_css(
			array(
				'colors' => pubzinne_get_theme_setting( 'separate_schemes' ) ? false : null,
			)
		);
		
		// Append color schemes
		if ( pubzinne_get_theme_setting( 'separate_schemes' ) ) {
			$schemes = pubzinne_get_sorted_schemes();
			if ( is_array( $schemes ) ) {
				foreach ( $schemes as $scheme => $data ) {
					$css .= pubzinne_customizer_get_css(
						array(
							'fonts'  => false,
							'colors' => $data['colors'],
							'scheme' => $scheme,
						)
					);
				}
			}
		}

		// Append responsive styles
		$css .= apply_filters( 'pubzinne_filter_gutenberg_get_styles_responsive', pubzinne_fgc( pubzinne_get_file_dir( 'css/__responsive.css' ) ) );

		// Add context class to each selector
		if ( pubzinne_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css = trx_addons_css_add_context(
						$css,
						array(
							'context' => '.edit-post-visual-editor ',
							'context_self' => array( 'html', 'body', '.edit-post-visual-editor' )
							)
					);
		} else {
			$css = apply_filters( 'pubzinne_filter_prepare_css', $css );
		}

		// Save styles to the file
		pubzinne_fpc( pubzinne_get_file_dir( 'plugins/gutenberg/gutenberg-preview.css' ), $msg . $css );
	}
}


// Add theme-specific colors to the Gutenberg color picker
if ( ! function_exists( 'pubzinne_gutenberg_add_editor_colors' ) ) {
	//Hamdler of the add_action( 'after_setup_theme', 'pubzinne_gutenberg_add_editor_colors' );
	function pubzinne_gutenberg_add_editor_colors() {
		$scheme = pubzinne_get_scheme_colors();
		$groups = pubzinne_storage_get( 'scheme_color_groups' );
		$names  = pubzinne_storage_get( 'scheme_color_names' );
		$colors = array();
		foreach( $groups as $g => $group ) {
			foreach( $names as $n => $name ) {
				$c = 'main' == $g ? ( 'text' == $n ? 'text_color' : $n ) : $g . '_' . str_replace( 'text_', '', $n );
				if ( isset( $scheme[ $c ] ) ) {
					$colors[] = array(
						'name'  => ( 'main' == $g ? '' : $group['title'] . ' ' ) . $name['title'],
						'slug'  => $c,
						'color' => $scheme[ $c ]
					);
				}
			}
			// Add only one group of colors
			// Delete next condition (or add false && to them) to add all groups
			if ( 'main' == $g ) {
				break;
			}
		}
		add_theme_support( 'editor-color-palette', $colors );
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( pubzinne_exists_gutenberg() ) {
	require_once pubzinne_get_file_dir( 'plugins/gutenberg/gutenberg-style.php' );
}
