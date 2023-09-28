<?php
/**
 * Plugin support: LearnPress
 *
 * @package ThemeREX Addons
 * @since v1.6.62
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_learnpress' ) ) {
	/**
	 * Check if LearnPress plugin is installed and activated
	 *
	 * @return bool  true if plugin is installed and activated
	 */
	function trx_addons_exists_learnpress() {
		return class_exists('LearnPress');
	}
}

if ( ! function_exists( 'trx_addons_is_learnpress_page' ) ) {
	/**
	 * Check if current page is any LearnPress page
	 *
	 * @return bool  true if current page is any LearnPress page
	 */
	function trx_addons_is_learnpress_page() {
		$rez = false;
		if ( trx_addons_exists_learnpress() && ! is_search() ) {
			$rez = is_learnpress()
					|| ( function_exists( 'learn_press_is_profile' ) && learn_press_is_profile() )
					|| ( function_exists( 'learn_press_is_checkout' ) && learn_press_is_checkout() )
					|| ( function_exists( 'learn_press_is_instructors' ) && learn_press_is_instructors() )
					|| trx_addons_check_url( '/instructor/' );
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_change_courses_slug' ) ) {
	add_filter('trx_addons_cpt_list', 'trx_addons_learnpress_change_courses_slug');
	/**
	 * Change slug for the internl courses post type to avoid conflicts with the LearnPress plugin
	 * 
	 * @hooked trx_addons_cpt_list
	 *
	 * @param array $list  List of post types parameters
	 * 
	 * @return array       Modified list of post types parameters
	 */
	function trx_addons_learnpress_change_courses_slug( $list ) {
		if ( ! empty( $list['courses']['post_type_slug'] ) && $list['courses']['post_type_slug'] == 'courses' ) {
			$list['courses']['post_type_slug'] = 'cpt_courses';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_add_fields' ) ) {
	add_filter( 'learn_press_course_settings_meta_box_args', 'trx_addons_learnpress_add_fields' );
	/**
	 * Add additional meta-fields to the course
	 * 
	 * @hooked learn_press_course_settings_meta_box_args
	 *
	 * @param array $meta_box  Meta box parameters
	 * 
	 * @return array           Modified meta box parameters
	 */
	function trx_addons_learnpress_add_fields( $meta_box ) {
		$meta_box['fields'][] = array(
			'name' => __( 'Intro video (local)', 'trx_addons' ),
			'desc' => __( 'Video-presentation of the course uploaded to your site.', 'trx_addons' ),
			'id'   => '_lp_intro_video',
			'type' => 'video',
			'std'  => ''
		);
		$meta_box['fields'][] = array(
			'name' => __( 'Intro video (external)', 'trx_addons' ),
			'desc' => __( 'or specify url of the video-presentation from popular video hosting (like Youtube, Vimeo, etc.)', 'trx_addons' ),
			'id'   => '_lp_intro_video_external',
			'type' => 'text',
			'std'  => ''
		);
		$meta_box['fields'][] = array(
			'name' => __( 'Includes', 'trx_addons' ),
			'desc' => __( 'List of includes of the course.', 'trx_addons' ),
			'id'   => '_lp_course_includes',
			'type' => 'wysiwyg',
			'std'  => ''
		);
		return $meta_box;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_learnpress_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_learnpress_load_scripts_front', 10, 1 );
	/**
	 * Enqueue scripts and styles for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param bool $force  Force enqueue scripts and styles (without check if it's necessary)
	 */
	function trx_addons_learnpress_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_learnpress() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'learnpress', $force, array(
			'need' => trx_addons_is_learnpress_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'confirm_order' ),
				array( 'type' => 'sc',  'sc' => 'profile' ),
				array( 'type' => 'sc',  'sc' => 'become_teacher_form' ),
				array( 'type' => 'sc',  'sc' => 'login_form' ),
				array( 'type' => 'sc',  'sc' => 'register_form' ),
				array( 'type' => 'sc',  'sc' => 'checkout' ),
				array( 'type' => 'sc',  'sc' => 'recent_courses' ),
				array( 'type' => 'sc',  'sc' => 'featured_courses' ),
				array( 'type' => 'sc',  'sc' => 'popular_courses' ),
				array( 'type' => 'sc',  'sc' => 'button_enroll' ),
				array( 'type' => 'sc',  'sc' => 'button_purchase' ),
				array( 'type' => 'sc',  'sc' => 'button_course' ),
				array( 'type' => 'sc',  'sc' => 'course_curriculum' ),
				array( 'type' => 'sc',  'sc' => 'learn_press_archive_course' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"wp-widget-learnpress_widget_' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[confirm_order' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[profile' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[become_teacher_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[login_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[register_form' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[checkout' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[recent_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[featured_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[popular_courses' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_enroll' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_purchase' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[button_course' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[course_curriculum' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[learn_press_archive_course' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_learnpress_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_learnpress_check_in_html_output', 10, 1 );
	/**
	 * Check if LearnPress shortcodes are present in the HTML output of the page or in the menu or the layouts cache
	 * and force loading scripts and styles
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content  HTML output to check
	 * 
	 * @return string          Checked HTML output
	 */
	function trx_addons_learnpress_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_learnpress() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*learnpress',
				'<(div|section|form|table|ul)[^>]*id=[\'"][^\'"]*learnpress',
				'class=[\'"][^\'"]*type\\-(lp_course|lp_lesson|lp_question|lp_quiz|lp_order)',
				'class=[\'"][^\'"]*(course_category|course_tag|question_tag)\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'learnpress', $content, $args ) ) {
			trx_addons_learnpress_load_scripts_front( true );
		}
		return $content;
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'learnpress/learnpress-demo-importer.php';
}
