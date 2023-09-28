<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Shortcode_Four' ) ) :

	final class WPSC_Shortcode_Four {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// register shortcode.
			add_shortcode( 'wpsc_unresolved_ticket_count', array( __CLASS__, 'layout' ) );
		}

		/**
		 * Layout for this shortcode
		 *
		 * @param array $attrs - Shortcode attributes.
		 * @return string
		 */
		public static function layout( $attrs ) {

			$current_user = WPSC_Current_User::$current_user;
			return $current_user->is_agent ? $current_user->agent->unresolved_count : 0;
		}
	}
endif;

WPSC_Shortcode_Four::init();
