<?php
/* Tribe Events Calendar support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'pubzinne_tribe_events_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_tribe_events_theme_setup1', 1 );
	function pubzinne_tribe_events_theme_setup1() {
		add_filter( 'pubzinne_filter_list_sidebars', 'pubzinne_tribe_events_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'pubzinne_tribe_events_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_tribe_events_theme_setup3', 3 );
	function pubzinne_tribe_events_theme_setup3() {
		if ( pubzinne_exists_tribe_events() ) {
			// Section 'Tribe Events'
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'events' => array(
							'title' => esc_html__( 'Events', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the events pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_options_get_list_cpt_options( 'events' )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_tribe_events_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_tribe_events_theme_setup9', 9 );
	function pubzinne_tribe_events_theme_setup9() {
		if ( pubzinne_exists_tribe_events() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_tribe_events_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'pubzinne_tribe_events_responsive_styles', 2000 );
			add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_tribe_events_merge_styles' );
			add_filter( 'pubzinne_filter_merge_styles_responsive', 'pubzinne_tribe_events_merge_styles_responsive' );
			add_filter( 'pubzinne_filter_post_type_taxonomy', 'pubzinne_tribe_events_post_type_taxonomy', 10, 2 );
			add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_tribe_events_detect_blog_mode' );
			add_filter( 'pubzinne_filter_get_post_categories', 'pubzinne_tribe_events_get_post_categories' );
			add_filter( 'pubzinne_filter_get_post_date', 'pubzinne_tribe_events_get_post_date' );
		}
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_tribe_events_tgmpa_required_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_tribe_events_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_tribe_events_tgmpa_required_plugins');
	function pubzinne_tribe_events_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'the-events-calendar' ) && pubzinne_storage_get_array( 'required_plugins', 'the-events-calendar', 'install' ) !== false ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'the-events-calendar', 'title' ),
				'slug'     => 'the-events-calendar',
				'required' => false,
			);
		}
		return $list;
	}
}


// Remove 'Tribe Events' section from Customizer
if ( ! function_exists( 'pubzinne_tribe_events_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'pubzinne_tribe_events_customizer_register_controls', 100 );
	function pubzinne_tribe_events_customizer_register_controls( $wp_customize ) {
		$wp_customize->remove_panel( 'tribe_customizer' );
	}
}


// Check if Tribe Events is installed and activated
if ( ! function_exists( 'pubzinne_exists_tribe_events' ) ) {
	function pubzinne_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

// Return true, if current page is any tribe_events page
if ( ! function_exists( 'pubzinne_is_tribe_events_page' ) ) {
	function pubzinne_is_tribe_events_page() {
		$rez = false;
		if ( pubzinne_exists_tribe_events() ) {
			if ( ! is_search() ) {
				$rez = tribe_is_event() || tribe_is_event_query() || tribe_is_event_category() || tribe_is_event_venue() || tribe_is_event_organizer();
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'pubzinne_tribe_events_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_tribe_events_detect_blog_mode' );
	function pubzinne_tribe_events_detect_blog_mode( $mode = '' ) {
		if ( pubzinne_is_tribe_events_page() ) {
			$mode = 'events';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'pubzinne_tribe_events_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_post_type_taxonomy',	'pubzinne_tribe_events_post_type_taxonomy', 10, 2 );
	function pubzinne_tribe_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( pubzinne_exists_tribe_events() && Tribe__Events__Main::POSTTYPE == $post_type ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the current event
if ( ! function_exists( 'pubzinne_tribe_events_get_post_categories' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_categories', 		'pubzinne_tribe_events_get_post_categories');
	function pubzinne_tribe_events_get_post_categories( $cats = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$cats = pubzinne_get_post_terms( ', ', get_the_ID(), Tribe__Events__Main::TAXONOMY );
		}
		return $cats;
	}
}

// Return date of the current event
if ( ! function_exists( 'pubzinne_tribe_events_get_post_date' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_date', 'pubzinne_tribe_events_get_post_date');
	function pubzinne_tribe_events_get_post_date( $dt = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			if ( is_int( $dt ) ) {
				// Return start date and time in the Unix format
				$dt = tribe_get_start_date( get_the_ID(), true, 'U' );
			} else {
				// Return start date and time - end date and time
				// Example: $dt = tribe_events_event_schedule_details( get_the_ID(), '', '' )
				
				// Return start date and time as string
				// If second parameter is true - time is showed
				$dt = tribe_get_start_date( get_the_ID(), true );
			}
		}
		return $dt;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'pubzinne_tribe_events_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_tribe_events_frontend_scripts', 1100 );
	function pubzinne_tribe_events_frontend_scripts() {
		if ( pubzinne_is_tribe_events_page() ) {
			if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
				$pubzinne_url = pubzinne_get_file_url( 'plugins/the-events-calendar/the-events-calendar.css' );
				if ( '' != $pubzinne_url ) {
					wp_enqueue_style( 'pubzinne-the-events-calendar', $pubzinne_url, array(), null );
				}
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'pubzinne_tribe_events_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_tribe_events_responsive_styles', 2000 );
	function pubzinne_tribe_events_responsive_styles() {
		if ( pubzinne_is_tribe_events_page() ) {
			if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
				$pubzinne_url = pubzinne_get_file_url( 'plugins/the-events-calendar/the-events-calendar-responsive.css' );
				if ( '' != $pubzinne_url ) {
					wp_enqueue_style( 'pubzinne-the-events-calendar-responsive', $pubzinne_url, array(), null );
				}
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'pubzinne_tribe_events_merge_styles' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles', 'pubzinne_tribe_events_merge_styles');
	function pubzinne_tribe_events_merge_styles( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar.css';
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'pubzinne_tribe_events_merge_styles_responsive' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles_responsive', 'pubzinne_tribe_events_merge_styles_responsive');
	function pubzinne_tribe_events_merge_styles_responsive( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar-responsive.css';
		return $list;
	}
}



// Add Tribe Events specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'pubzinne_tribe_events_list_sidebars' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_sidebars', 'pubzinne_tribe_events_list_sidebars' );
	function pubzinne_tribe_events_list_sidebars( $list = array() ) {
		$list['tribe_events_widgets'] = array(
			'name'        => esc_html__( 'Tribe Events Widgets', 'pubzinne' ),
			'description' => esc_html__( 'Widgets to be shown on the Tribe Events pages', 'pubzinne' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( pubzinne_exists_tribe_events() ) {
	require_once pubzinne_get_file_dir( 'plugins/the-events-calendar/the-events-calendar-style.php' );
}
