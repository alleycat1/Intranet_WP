<?php
/**
 * Banners in the single posts
 *
 * @package ThemeREX Addons
 * @since v1.74.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! function_exists( 'trx_addons_banners_params' ) ) {
	/**
	 * Add banners params to the plugin's options and page options
	 * 
	 * @param array $mode Options mode: 'page' or 'plugin'
	 */
	function trx_addons_banners_params( $mode ) {
		return array(
					// Section 'Banners'
					'banners_section' => array(
						"title" => esc_html__('Banners', 'trx_addons'),
						"desc" => wp_kses_data( __("Settings of the single post banners", 'trx_addons') ),
						'icon' => $mode == 'meta' ? '' : 'trx_addons_icon-newspaper',
						"type" => "section"
					),
					'posts_banners' => array(
						"title" => esc_html__("Single post banners", 'trx_addons'),
						"desc" => wp_kses_data( __("List of banners to display its on the single posts", 'trx_addons') ),
						"clone" => true,
						"std" => array( array() ),
						"type" => "group",
						"fields" => array(
							"title" => array(
								"title" => esc_html__("Title", 'trx_addons'),
								"class" => "trx_addons_column-1_3",
								"std" => "",
								"type" => "text"
							),
							"link"  => array(
								"title" => esc_html__("Link", 'trx_addons'),
								"class" => "trx_addons_column-2_3",
								"std" => "",
								"type" => "text"
							),
							"image" => array(
								"title" => esc_html__("Image", 'trx_addons'),
								"class" => "trx_addons_column-1_3 trx_addons_new_row",
								"std" => "",
								"type" => "image"
							),
							"code" => array(
								"title" => esc_html__("Code", 'trx_addons'),
								"class" => "trx_addons_column-2_3",
								"std" => "",
								"allow_html" => true,
								"type" => "textarea"
							),
							"position" => array(
								"title" => esc_html__("Position", 'trx_addons'),
								"class" => "trx_addons_column-1_3",
								"std" => "before_post_content",
								"options" => array(
													'background'          => esc_html__( 'As a page background', 'trx_addons' ),
													'before_header'       => esc_html__( 'Before the page header', 'trx_addons' ),
													'after_header'        => esc_html__( 'After the page header', 'trx_addons' ),
													'before_footer'       => esc_html__( 'Before the page footer', 'trx_addons' ),
													'after_footer'        => esc_html__( 'After the page footer', 'trx_addons' ),
													'before_sidebar'      => esc_html__( 'Before the sidebar', 'trx_addons' ),
													'after_sidebar'       => esc_html__( 'After the sidebar', 'trx_addons' ),
													'between_posts'       => esc_html__( "Above the ajax-loaded post's header", 'trx_addons' ),
													'before_post_header'  => esc_html__( 'Before the post header', 'trx_addons' ),
													'after_post_header'   => esc_html__( 'After the post header', 'trx_addons' ),
													'before_post_content' => esc_html__( 'Before the post content', 'trx_addons' ),
													'after_post_content'  => esc_html__( 'After the post content', 'trx_addons' ),
													),
								"type" => "select"
							),
							"width" => array(
								"title" => esc_html__("Width", 'trx_addons'),
								"class" => "trx_addons_column-1_3",
								"std" => "normal",
								"options" => array(
													'auto' => esc_html__( "Don't stretch banner", 'trx_addons' ),
													'wide' => esc_html__( "Make it wide", 'trx_addons' ),
													'full' => esc_html__( "Stretch to the window width", 'trx_addons' ),
													),
								"type" => "select"
							),
							"padding" => array(
								"title" => esc_html__("Paddings around", 'trx_addons'),
								"desc" => wp_kses_data( __("Up to 4 space separated values: top right bottom left paddings.", 'trx_addons') ),
								"class" => "trx_addons_column-1_3",
								"std" => "",
								"type" => "text"
							),
							"show" => array(
								"title" => esc_html__("Show on", 'trx_addons'),
								"class" => "trx_addons_column-1_3",
								"std" => "permanent",
								"options" => trx_addons_get_list_sc_show_on(),
								"type" => "select"
							),
							"delay" => array(
								"title" => esc_html__("Delay", 'trx_addons'),
								"desc" => wp_kses_data( __("Delay (in seconds) before the banner appears. If 0 - the banner will appear immediately when the corresponding event occurs", 'trx_addons') ),
								"class" => "trx_addons_column-2_3",
								"std" => 0,
								"min" => 0,
								"max" => 10,
								"step" => 0.5,
								"type" => "slider"
							),
						)
					)
				);
	}
}

if ( ! function_exists( 'trx_addons_banners_init' ) ) {
	add_action( 'init', 'trx_addons_banners_init', 11 );
	/**
	 * Add banners parameters to the Meta Box support
	 */
	function trx_addons_banners_init() {
		trx_addons_meta_box_register( 'post', trx_addons_banners_params( 'meta' ) );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner' ) ) {
	/**
	 * Show banner in the single post
	 * 
	 * @param string $pos Position of the banner: 'background', 'before_content', 'after_content', 'before_sidebar', 'after_sidebar'
	 * @param array $banners Array of banners to show. If empty - get banners from the post meta or theme options
	 */
	function trx_addons_show_post_banner( $pos = '', $banners = false ) {
		static $post_options = array();
		if ( '' !== $pos && ( ! empty( $banners ) || trx_addons_is_singular( 'post' ) || trx_addons_is_singular( 'attachment' ) ) ) {
			if ( empty( $banners ) ) {
				$post_id = get_the_ID();
				if ( ! isset( $post_options[ $post_id ] ) ) {
					$post_options[ $post_id ] = get_post_meta( $post_id, 'trx_addons_options', true );
				}
				if ( ! empty( $post_options[ $post_id ]['posts_banners'][0]['image'] ) || ! empty( $post_options[ $post_id ]['posts_banners'][0]['code'] ) ) {
					$banners = $post_options[ $post_id ]['posts_banners'];
				} else {
					$banners = trx_addons_get_option( 'posts_banners' );
				}
			}
			if ( is_array( $banners ) ) {
				foreach( $banners as $item ) {
					if ( ( empty( $item['position'] ) || $item['position'] == $pos ) && ( ! empty( $item['image'] ) || ! empty( $item['code'] ) ) ) {
						$html = '';
						$class = '';
						if ( ! empty( $item['image'] ) || ! empty( $item['code'] ) ) {
							$css = 'background' == $pos
										? ( ! empty( $item['image'] )
												? 'background-image:url(' . esc_url( $item['image'] ) . ');'
												: ''
												)
										: ( isset( $item['padding'] ) && '' != trim( $item['padding'] )
												? 'padding:' . trx_addons_prepare_css_value( $item['padding'] ) . ';'
												: ''
												)
											. ( ! empty( $item['bg_color'] )
												? 'background-color:' . esc_attr( $item['bg_color'] ) . ';'
												: ''
												);
							$class = ( ! empty( $item['image'] ) ? 'banner_with_image ' : '' )
										. ( ! empty( $item['code'] ) ? 'banner_with_code ' : '' )
										. ( empty( $item['bg_color'] ) && isset( $item['padding'] ) && '0' == substr( trim( $item['padding'] ), 0, 1 )
												? 'banner_without_paddings '
												: '' )
										. trx_addons_add_inline_css_class( $css );
						}
						if ( ! in_array( $pos, array( 'background', 'before_sidebar', 'after_sidebar' ) ) ) {
							if ( 'wide' == $item['width'] ) {
								$class .= ' alignwide';
							}
							if ( 'full' == $item['width'] ) {
								$class .= ' alignfull';
							}
						}
						if ( 'background' != $pos && trx_addons_get_setting( 'banners_show_effect' ) && ( $item['delay'] > 0 || $item['show'] != 'permanent' ) ) {
							$class .= ' banner_hidden';
						}
						$html = '<div class="' . esc_attr( $pos ) . '_banner_wrap ' . esc_attr( apply_filters( 'trx_addons_filter_banner_class', $class, $item, $pos ) ) . '">';
						if ( ! empty( $item['title'] ) ) {
							$html .= '<h6 class="banner_wrap_title">' . wp_kses_data( $item['title'] ) . '</h6>';
						}
						if ( ! empty( $item['link'] ) ) {
							$html .= '<a href="' . esc_url( $item['link'] ) . '" class="banner_wrap_link" target="_blank"></a>';
						}
						if ( 'background' != $pos && ! empty( $item['image'] ) ) {
							$attr = trx_addons_getimagesize( $item['image'] );
							$html .= '<img class="banner_wrap_image" src="' . esc_url( $item['image'] ) . '"'
									. ' alt="' . ( ! empty( $item['title'] ) ? esc_attr( $item['title'] ) : esc_attr__( 'Banner', 'trx_addons' ) ) . '"'
									. ( ! empty( $attr[3] ) ? ' ' . wp_kses_data( $attr[3] ) : '' )
									. '>';
						}
						if ( ! empty( $item['code'] ) ) {
							$html .= '<div class="banner_wrap_code">' . do_shortcode( $item['code'] ) . '</div>';
						}
						$html .= '</div>';
						$html = apply_filters( 'trx_addons_filter_banner_html', $html, $item, $pos );
						if ( 'background' != $pos && ( $item['delay'] > 0 || $item['show'] != 'permanent' ) ) {
							?><div class="trx_addons_banner_placeholder"
								data-banner="<?php echo esc_attr( $html ); ?>"
								data-banner-show="<?php echo esc_attr( $item['show'] ); ?>"
								data-banner-delay="<?php echo esc_attr( $item['delay'] * 1000 ); ?>"
							></div><?php
						} else {
							trx_addons_show_layout( $html );
						}
					}
				}
			}
		}	
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_background' ) ) {
	add_action( 'trx_addons_action_page_content_wrap', 'trx_addons_show_post_banner_background', 10, 1 );
	/**
	 * Show banner on the background of the page content if current request is not AJAX
	 * 
	 * @hooked trx_addons_action_page_content_wrap - 10
	 * 
	 * @param boolean $ajax  Is AJAX request?
	 */
	function trx_addons_show_post_banner_background( $ajax = false ) {
		if ( ! $ajax ) {
			trx_addons_show_post_banner( 'background' );
		}
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_before_header' ) ) {
	add_action( 'trx_addons_action_before_header', 'trx_addons_show_post_banner_before_header' );
	/**
	 * Show banner before header
	 * 
	 * @hooked trx_addons_action_before_header - 10
	 */
	function trx_addons_show_post_banner_before_header() {
		trx_addons_show_post_banner( 'before_header' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_after_header' ) ) {
	add_action( 'trx_addons_action_after_header', 'trx_addons_show_post_banner_after_header' );
	/**
	 * Show banner after header
	 * 
	 * @hooked trx_addons_action_after_header - 10
	 */
	function trx_addons_show_post_banner_after_header() {
		trx_addons_show_post_banner( 'after_header' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_before_footer' ) ) {
	add_action( 'trx_addons_action_before_footer', 'trx_addons_show_post_banner_before_footer' );
	/**
	 * Show banner before footer
	 * 
	 * @hooked trx_addons_action_before_footer - 10
	 */
	function trx_addons_show_post_banner_before_footer() {
		trx_addons_show_post_banner( 'before_footer' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_after_footer' ) ) {
	add_action( 'trx_addons_action_after_footer', 'trx_addons_show_post_banner_after_footer' );
	/**
	 * Show banner after footer
	 * 
	 * @hooked trx_addons_action_after_footer - 10
	 */
	function trx_addons_show_post_banner_after_footer() {
		trx_addons_show_post_banner( 'after_footer' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_before_sidebar_wrap' ) ) {
	add_action( 'trx_addons_action_before_sidebar_wrap', 'trx_addons_show_post_banner_before_sidebar_wrap', 10, 1 );
	/**
	 * Show banner before main sidebar
	 * 
	 * @hooked trx_addons_action_before_sidebar_wrap - 10
	 * 
	 * @param string $sb  Sidebar name
	 */
	function trx_addons_show_post_banner_before_sidebar_wrap( $sb = '' ) {
		if ( 'sidebar' == $sb ) {
			trx_addons_show_post_banner( 'before_sidebar' );
		}
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_after_sidebar_wrap' ) ) {
	add_action( 'trx_addons_action_after_sidebar_wrap', 'trx_addons_show_post_banner_after_sidebar_wrap', 10, 1 );
	/**
	 * Show banner after main sidebar
	 * 
	 * @hooked trx_addons_action_after_sidebar_wrap - 10
	 * 
	 * @param string $sb  Sidebar name
	 */
	function trx_addons_show_post_banner_after_sidebar_wrap( $sb = '' ) {
		if ( 'sidebar' == $sb ) {
			trx_addons_show_post_banner( 'after_sidebar' );
		}
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_between_posts' ) ) {
	add_action( 'trx_addons_action_between_posts', 'trx_addons_show_post_banner_between_posts' );
	/**
	 * Show banner between posts
	 * 
	 * @hooked trx_addons_action_between_posts - 10
	 */
	function trx_addons_show_post_banner_between_posts() {
		trx_addons_show_post_banner( 'between_posts' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_before_post_header' ) ) {
	add_action( 'trx_addons_action_before_post_header', 'trx_addons_show_post_banner_before_post_header' );
	/**
	 * Show banner before post header
	 * 
	 * @hooked trx_addons_action_before_post_header - 10
	 */
	function trx_addons_show_post_banner_before_post_header() {
		trx_addons_show_post_banner( 'before_post_header' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_after_post_header' ) ) {
	add_action( 'trx_addons_action_after_post_header', 'trx_addons_show_post_banner_after_post_header' );
	/**
	 * Show banner after post header
	 * 
	 * @hooked trx_addons_action_after_post_header - 10
	 */
	function trx_addons_show_post_banner_after_post_header() {
		trx_addons_show_post_banner( 'after_post_header' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_before_post_content' ) ) {
	add_action( 'trx_addons_action_before_post_content', 'trx_addons_show_post_banner_before_post_content' );
	/**
	 * Show banner before post content
	 * 
	 * @hooked trx_addons_action_before_post_content - 10
	 */
	function trx_addons_show_post_banner_before_post_content() {
		trx_addons_show_post_banner( 'before_post_content' );
	}
}

if ( ! function_exists( 'trx_addons_show_post_banner_after_post_content' ) ) {
	add_action( 'trx_addons_action_after_post_content', 'trx_addons_show_post_banner_after_post_content' );
	/**
	 * Show banner after post content
	 * 
	 * @hooked trx_addons_action_after_post_content - 10
	 */
	function trx_addons_show_post_banner_after_post_content() {
		trx_addons_show_post_banner( 'after_post_content' );
	}
}
