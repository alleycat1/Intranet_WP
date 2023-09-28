<?php
/**
 * System messages
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


//-------------------------------------------------------
//-- Admin messages
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_set_admin_message' ) ) {
	/**
	 * Set internal message to display it in the admin panel
	 * 
	 * @param string $msg  Message to display
	 * @param string $type Message type: 'error' or 'success'
	 * @param bool   $next_session If true - message will be displayed on the next page load
	 */
	function trx_addons_set_admin_message( $msg = false, $type = false, $next_session = false ) {
		if ( $next_session ) {
			$store = array( 'error' => '', 'success' => '' );
			if ( ! empty( $type ) && ! empty( $msg ) ) {
				$store[ $type ] = $msg;
			}
			set_transient( 'trx_addons_admin_message', $store, 60 * 60 );		// Store to the cache for 1 hour
		} else if ( ! empty( $msg ) ) {
			global $TRX_ADDONS_STORAGE;
			if ( empty( $type ) ) {
				$TRX_ADDONS_STORAGE['admin_message'] = is_array( $msg ) ? $msg : array( 'error' => '', 'success' => $msg );
			} else {
				$TRX_ADDONS_STORAGE['admin_message'][ $type ] = $msg;
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_get_admin_message' ) ) {
	/**
	 * Get internal message from the previous session
	 * 
	 * @param string $type Message type: 'error' or 'success'
	 * 
	 * @return string    Message
	 */
	function trx_addons_get_admin_message( $type = false ) {
		global $TRX_ADDONS_STORAGE;
		return empty( $type ) ? $TRX_ADDONS_STORAGE['admin_message'] : $TRX_ADDONS_STORAGE['admin_message'][ $type ];
	}
}

if ( !function_exists( 'trx_addons_init_admin_message' ) ) {
	/**
	 * Init an internal messages subsystem. Load a message from the previous session (if set) to the global storage
	 */
	function trx_addons_init_admin_message() {
		$msg = get_transient( 'trx_addons_admin_message' );
		if ( ! empty( $msg ) ) {
			trx_addons_set_admin_message( $msg );
			delete_transient( 'trx_addons_admin_message' );
		}
	}
}

// Init messages subsystem in the admin mode
if ( is_admin() ) {
	trx_addons_init_admin_message();
}


//-------------------------------------------------------
//-- Frontend messages
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_set_front_message' ) ) {
	/**
	 * Set internal message to display it in the frontend
	 * 
	 * @param string $msg  Message to display
	 * @param string $type Message type: 'error' or 'success'
	 * @param bool   $next_session If true - message will be displayed on the next page load
	 */
	function trx_addons_set_front_message( $msg = false, $type = false, $next_session = false ) {
		if ( $next_session ) {
			$store = array( 'error' => '', 'success' => '' );
			if ( ! empty( $type ) && ! empty( $msg ) ) {
				$store[ $type ] = $msg;
			}
			set_transient( 'trx_addons_front_message', $store, 60 * 60 );       // Store to the cache for 1 hour
		} else if ( ! empty( $msg ) ) {
			global $TRX_ADDONS_STORAGE;
			if ( empty( $type ) ) {
				$TRX_ADDONS_STORAGE['front_message'] = is_array( $msg ) ? $msg : array( 'error' => '', 'success' => $msg );
			} else {
				$TRX_ADDONS_STORAGE['front_message'][ $type ] = $msg;
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_get_front_message' ) ) {
	/**
	 * Get internal message from the previous session
	 * 
	 * @param string $type Message type: 'error' or 'success'
	 * 
	 * @return string    Message
	 */
	function trx_addons_get_front_message( $type = false ) {
		global $TRX_ADDONS_STORAGE;
		return empty( $type ) ? $TRX_ADDONS_STORAGE['front_message'] : $TRX_ADDONS_STORAGE['front_message'][ $type ];
	}
}

if ( ! function_exists( 'trx_addons_init_front_message' ) ) {
	/**
	 * Init an internal messages subsystem. Load a message from the previous session (if set) to the global storage
	 */
	function trx_addons_init_front_message() {
		$msg = get_transient( 'trx_addons_front_message' );
		if ( ! empty( $msg ) ) {
			trx_addons_set_front_message($msg);
			delete_transient( 'trx_addons_front_message' );
		}
	}
}

// Init messages subsystem in the frontend mode
if ( ! is_admin() ) {
	trx_addons_init_front_message();
}

if ( ! function_exists( 'trx_addons_show_front_message' ) ) {
	add_action( 'wp_footer', 'trx_addons_show_front_message' );
	/**
	 * Show internal message in the footer in the frontend mode
	 * 
	 * @hooked wp_footer
	 */
	function trx_addons_show_front_message() {
		$result = trx_addons_get_front_message();
		if ( ! empty( $result['error'] ) ) {
			?><div class="trx_addons_message_box trx_addons_message_box_system trx_addons_message_box_error">
				<h6 class="trx_addons_message_box_title"><?php esc_html_e( 'Error!', 'trx_addons' ); ?></h6>
				<div class="trx_addons_message_box_text"><?php echo wp_kses( $result['error'], 'trx_addons_kses_content' ); ?></div>
			</div><?php
		} else if ( ! empty( $result['success'] ) ) {
			?><div class="trx_addons_message_box trx_addons_message_box_system trx_addons_message_box_success">
				<h6 class="trx_addons_message_box_title"><?php esc_html_e( 'Success!', 'trx_addons' ); ?></h6>
				<div class="trx_addons_message_box_text"><?php echo wp_kses( $result['success'], 'trx_addons_kses_content' ); ?></div>
			</div><?php
		}
	}
}
