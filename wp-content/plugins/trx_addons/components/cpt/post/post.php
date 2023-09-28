<?php
/**
 * ThemeREX Addons Custom post type: Post (add options to the standard WP Post)
 *
 * @package ThemeREX Addons
 * @since v1.6.24
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Post type setup
// -----------------------------------------------------------------

// Add options to the standard WP post
if (!function_exists('trx_addons_cpt_post_init')) {
	add_action( 'init', 'trx_addons_cpt_post_init' );
	function trx_addons_cpt_post_init() {
		
		// Add post's custom fileds
		trx_addons_meta_box_register('post', array(
			'general_section' => array(
				"title" => esc_html__('General', 'trx_addons'),
				"desc" => wp_kses_data( __('General options', 'trx_addons') ),
				"type" => "section"
			),
			"icon" => array(
				"title" => esc_html__("Post's icon", 'trx_addons'),
				"desc" => wp_kses_data( __('Select icon for the current post (used in some shortcodes)', 'trx_addons') ),
				"std" => '',
				"options" => array(),
				"style" => trx_addons_get_setting('icons_type'),
				"type" => "icons"
			),
			"sponsored_post" => array(
				"title" => esc_html__("Sponsored post", 'trx_addons'),
				"desc" => wp_kses_data( __('Turn on if content of this post is sponsored', 'trx_addons') ),
				"std" => 0,
				"type" => "switch"
			),
			"sponsored_label" => array(
				"title" => esc_html__("Sponsored label", 'trx_addons'),
				"desc" => wp_kses_data( __("Add a unique text string with the name of your advertiser, e.g. 'Sponsored by NAME'. If nothing is specified, the default sponsored label will be used", 'trx_addons') ),
				"std" => '',
				"dependency" => array(
					"sponsored_post" => 1
				),
				"type" => "text"
			),
			"sponsored_url" => array(
				"title" => esc_html__("Sponsored URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Link to the site of your advertiser", 'trx_addons') ),
				"std" => '',
				"dependency" => array(
					"sponsored_post" => 1
				),
				"type" => "text"
			),
			"sponsored_rel_nofollow" => array(
				"title" => esc_html__('Add rel="nofollow"', 'trx_addons'),
				"desc" => '',
				"std" => 1,
				"dependency" => array(
					"sponsored_post" => 1
				),
				"type" => "switch"
			),
			"sponsored_rel_sponsored" => array(
				"title" => esc_html__('Add rel="sponsored"', 'trx_addons'),
				"desc" => '',
				"std" => 1,
				"dependency" => array(
					"sponsored_post" => 1
				),
				"type" => "switch"
			),

			// Post format: Video
			'video_section' => array(
				"title" => esc_html__('Video', 'trx_addons'),
				"desc" => wp_kses_data( __('Options of the post format "Video"', 'trx_addons') ),
				"type" => "section"
			),
			"video_source" => array(
				"title" => esc_html__("Video source", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the source of the video to be displayed in the post's header", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video'
				),
				"std" => 'manual',
				"options" => array(
					'manual'        => esc_html__( 'Manually specified videos', 'trx_addons' ),
					'recent_posts'  => esc_html__( 'Videos from recent posts', 'trx_addons' ),
					'related_posts' => esc_html__( 'Videos from related posts', 'trx_addons' ),
				),
				"type" => "select"
			),
			"video_total" => array(
				"title" => esc_html__("Total videos", 'trx_addons'),
				"dependency" => array(
					'.editor-post-format select' => 'video',
					"video_source" => array( 'recent_posts', 'related_posts' )
				),
				"min" => 1,
				"max" => 10,
				"std" => 4,
				"type" => "slider"
			),
			"video_sticky" => array(
				"title" => esc_html__('Make video "sticky"', 'trx_addons'),
				"desc" => wp_kses_data( __("Attach a video to the bottom edge of the window when the page scrolls down", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video'
				),
				"std" => 0,
				"type" => "switch"
			),
			"video_without_cover" => array(
				"title" => esc_html__('Hide cover image', 'trx_addons'),
				"desc" => wp_kses_data( __("Don't show cover/featured image on the video player in the single post", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video'
				),
				"std" => 0,
				"type" => "switch"
			),
			"video_autoplay_archive" => array(
				"title" => esc_html__("Allow autoplay on the archive page", 'trx_addons'),
				"desc" => wp_kses_data( __('Autoplay video when an archive page or shortcode is loaded', 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video'
				),
				"std" => 0,
				"type" => "switch"
			),
			"video_autoplay" => array(
				"title" => esc_html__("Allow autoplay on the single post", 'trx_addons'),
				"desc" => wp_kses_data( __("Autoplay video when a single post is loaded (if single video is specified in the list below) or when switching between videos (if more than one video are specified in the list below)", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video'
				),
				"std" => 0,
				"type" => "switch"
			),
			"video_list" => array(
				"title" => esc_html__("Video list", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify one or more videos to use in the shortcodes output, post header and blog archive", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'video',
					"video_source" => 'manual'
				),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"title" => array(
						"title" => esc_html__("Video title", 'trx_addons'),
						"desc" => wp_kses_data( __('Title of the video', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"subtitle" => array(
						"title" => esc_html__("Video subtitle", 'trx_addons'),
						"desc" => wp_kses_data( __('Subtitle of the video to display above the title', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"meta" => array(
						"title" => esc_html__("Description", 'trx_addons'),
						"desc" => wp_kses_data( __('Text or metadata to be displayed under the title', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"image" => array(
						"title" => esc_html__("Cover image", 'trx_addons'),
						"desc" => wp_kses_data( __("Select an image to be used as a video cover", 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "image"
					),
					"video_url" => array(
						"title" => esc_html__("Video URL", 'trx_addons'),
						"desc" => wp_kses_data( __('Specify URL to show a videoplayer from Youtube, Vimeo or other compatible video hosting', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"video_embed" => array(
						"title" => esc_html__("Video embed code", 'trx_addons'),
						"desc" => wp_kses_data( __('or paste embed code', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "textarea"
					)
				)
			),

			// Post format: Audio
			'audio_section' => array(
				"title" => esc_html__('Audio', 'trx_addons'),
				"desc" => wp_kses_data( __('Options of the post format "Audio"', 'trx_addons') ),
				"type" => "section"
			),
			"audio_list" => array(
				"title" => esc_html__("Audio file", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify the audio URL to use in the shortcodes output, post header and blog archive", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'audio'
				),
				"clone" => false,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"caption" => array(
						"title" => esc_html__("Title", 'trx_addons'),
						"desc" => wp_kses_data( __('Audio file title', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"author" => array(
						"title" => esc_html__("Author", 'trx_addons'),
						"desc" => wp_kses_data( __('Audio file author', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"description" => array(
						"title" => esc_html__("Description", 'trx_addons'),
						"desc" => '',
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "textarea"
					),
					"cover" => array(
						"title" => esc_html__("Cover image", 'trx_addons'),
						"desc" => wp_kses_data( __("Select an image to be used as a audio cover", 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "image"
					),
					"url" => array(
						"title" => esc_html__("URL", 'trx_addons'),
						"desc" => wp_kses_data( __('Specify URL of the audio or stream radio', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"embed" => array(
						"title" => esc_html__("Embed code", 'trx_addons'),
						"desc" => wp_kses_data( __('or paste embed code', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "textarea"
					)
				)
			),

			// Post format: Gallery
			'gallery_section' => array(
				"title" => esc_html__('Gallery', 'trx_addons'),
				"desc" => wp_kses_data( __('Options of the post format "Gallery"', 'trx_addons') ),
				"type" => "section"
			),
			"gallery_list" => array(
				"title" => esc_html__("Image list", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify one or more images to use in a gallery shortcodes output, post header and blog archive", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"multiple" => true,
				"std" => "",
				"type" => "image"
			),
			"slides_per_view" => array(
				"title" => esc_html__("Slides per view", 'trx_addons'),
				"desc" => wp_kses_data( __("How many slides should be displayed at once in the gallery window on the post page.", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 1,
				"min" => 1,
				"max" => 8,
				"type" => "slider"
			),
			"slides_space" => array(
				"title" => esc_html__("Space between slides", 'trx_addons'),
				"desc" => wp_kses_data( __("Space (in pixels) between two slides", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"min" => 0,
				"max" => 100,
				"type" => "slider"
			),
			"slides_centered" => array(
				"title" => esc_html__("Slides centered", 'trx_addons'),
				"desc" => wp_kses_data( __("Place one slide at the center of the window and show parts of previous and next slides", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"slides_overflow" => array(
				"title" => esc_html__("Slides overflow visible", 'trx_addons'),
				"desc" => wp_kses_data( __("Don't hide slides outside the slider", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"mouse_wheel" => array(
				"title" => esc_html__("Enable mouse wheel", 'trx_addons'),
				"desc" => wp_kses_data( __("Enable slide control by rotating the mouse wheel", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"controls" => array(
				"title" => esc_html__("Show controls", 'trx_addons'),
				"desc" => wp_kses_data( __("Add arrows to change slides", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"pagination" => array(
				"title" => esc_html__("Show pagination", 'trx_addons'),
				"desc" => wp_kses_data( __("Add pagination to change slides", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"pagination_type" => array(
				"title" => esc_html__("Pagination type", 'trx_addons'),
				"desc" => wp_kses_data( __("Select pagination type", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'pagination' => 1
				),
				"std" => 'bullets',
				"options" => trx_addons_get_list_sc_slider_paginations_types(),
				"type" => "radio"
			),
			"controller" => array(
				"title" => esc_html__("Show thumbs", 'trx_addons'),
				"desc" => wp_kses_data( __("Add slide thumbnails", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery'
				),
				"std" => 0,
				"type" => "switch"
			),
			"controller_pos" => array(
				"title" => esc_html__("Thumbs position", 'trx_addons'),
				"desc" => wp_kses_data( __("Select slide thumbnail position", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'controller' => 1
				),
				"std" => 'bottom',
				"options" => trx_addons_get_list_sc_slider_toc_positions(),
				"type" => "radio"
			),
			"controller_height" => array(
				"title" => esc_html__("Thumbs height (in px)", 'trx_addons'),
				"desc" => wp_kses_data( __("Thumbs height (in pixels).", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'controller' => 1,
					'controller_pos' => 'bottom'
				),
				"std" => 100,
				"min" => 20,
				"max" => 250,
				"type" => "slider"
			),
			"controller_per_view" => array(
				"title" => esc_html__("Thumbs per view", 'trx_addons'),
				"desc" => wp_kses_data( __("How many thumbnails should be displayed at once?", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'controller' => 1
				),
				"std" => 5,
				"min" => 1,
				"max" => 10,
				"type" => "slider"
			),
			"controller_space" => array(
				"title" => esc_html__("Space between thumbs", 'trx_addons'),
				"desc" => wp_kses_data( __("Space (in pixels) between two thumbs", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'controller' => 1
				),
				"std" => 1,
				"min" => 0,
				"max" => 50,
				"type" => "slider"
			),
			"controller_margin" => array(
				"title" => esc_html__("Margin from gallery", 'trx_addons'),
				"desc" => wp_kses_data( __("Space (in pixels) between a gallery and a thumbs block", 'trx_addons') ),
				"dependency" => array(
					'.editor-post-format select' => 'gallery',
					'controller' => 1
				),
				"std" => 1,
				"min" => 0,
				"max" => 50,
				"type" => "slider"
			),
		));
	}
}


// Open wrapper around single post video
if (!function_exists('trx_addons_cpt_post_before_video_sticky')) {
	add_action( 'trx_addons_action_before_single_post_video', 'trx_addons_cpt_post_before_video_sticky', 10, 1 );
	function trx_addons_cpt_post_before_video_sticky( $args = array() ) {
		if ( ! empty( $args['singular'] ) || ! empty( $args['singular_extra'] ) ) {
			$post_meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! empty( $post_meta['video_sticky'] ) ) {
				?>
				<div class="trx_addons_video_sticky">
					<div class="trx_addons_video_sticky_inner">
				<?php
				$GLOBALS['TRX_ADDONS_STORAGE']['video_sticky_opened'] = true;
			}

		}
	}
}

// Close wrapper around single post video
if (!function_exists('trx_addons_cpt_post_after_video_sticky')) {
	add_action( 'trx_addons_action_after_single_post_video', 'trx_addons_cpt_post_after_video_sticky', 10, 1 );
	function trx_addons_cpt_post_after_video_sticky( $args = array() ) {
		if ( ! empty( $GLOBALS['TRX_ADDONS_STORAGE']['video_sticky_opened'] ) ) {
			?>
				</div>
				<span class="trx_addons_video_sticky_close trx_addons_button_close" tabindex="0"><span class="trx_addons_button_close_icon"></span></span>
			</div>
			<?php
			$GLOBALS['TRX_ADDONS_STORAGE']['video_sticky_opened'] = false;
		}
	}
}



// Modify featured args - add video (if specified)
if (!function_exists('trx_addons_cpt_post_args_featured')) {
	add_filter( 'trx_addons_filter_args_featured', 'trx_addons_cpt_post_args_featured', 10, 3 );
	function trx_addons_cpt_post_args_featured( $featured_args, $sc='', $args=array() ) {
		if ( get_post_type() == 'post' && ! isset( $featured_args['autoplay'] ) ) {
			$post_format = str_replace( 'post-format-', '', get_post_format() );
			if ( $post_format == 'video' ) {
				$key = 'video_autoplay' . ( trx_addons_is_single() ? '' : '_archive' );
				$post_meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
				if ( ! empty( $post_meta[$key] )
					&& ! empty( $post_meta['video_list'] )
					&& is_array( $post_meta['video_list'] )
					&& count( $post_meta['video_list'] ) > 0
					&& ( ! empty( $post_meta['video_list'][0]['video_url'] ) || ! empty( $post_meta['video_list'][0]['video_embed'] ) )
				) {
					$featured_args['autoplay'] = true;
				}
			}
		}
		return $featured_args;
	}
}

// Modify featured classes - add video (if specified)
if (!function_exists('trx_addons_cpt_post_post_featured_classes')) {
	add_filter( 'trx_addons_filter_post_featured_classes', 'trx_addons_cpt_post_post_featured_classes', 10, 3 );
	function trx_addons_cpt_post_post_featured_classes( $classes, $args=array(), $mode='' ) {
		if ( get_post_type() == 'post' && strpos( $classes, 'with_video_autoplay' ) === false ) {
			$post_format = str_replace( 'post-format-', '', get_post_format() );
			if ( $post_format == 'video' ) {
				$post_meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
				if ( ! empty( $post_meta['video_list'][0]['video_url'] ) || ! empty( $post_meta['video_list'][0]['video_embed'] ) ) {
					if ( strpos( $classes, 'with_video' ) === false ) {
						$classes .= ' with_video';
					}
					$key = 'video_autoplay' . ( trx_addons_is_single() ? '' : '_archive' );
					if ( ! empty( $post_meta[$key] ) ) {
						$classes .= ' with_video_autoplay';
					}
				}
			}
		}
		return $classes;
	}
}
