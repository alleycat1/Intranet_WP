<?php
if ( ! function_exists( 'nirweb_ticket_login_ticket' ) ) {
	function nirweb_ticket_login_ticket() {
		if ( ! is_user_logged_in() ) {
			include NIRWEB_SUPPORT_INC_USER_THEMES_TICKET . 'login.php';
			wp_enqueue_style( 'bootstrap.min.css', NIRWEB_SUPPORT_URL_TICKET . 'assets/css/bootstrap.min.css' );
			wp_enqueue_style( 'user-css-wpyt', NIRWEB_SUPPORT_URL_TICKET . 'assets/user.css' );
		} else {
			include NIRWEB_SUPPORT_INC_USER_THEMES_TICKET . 'index.php';
			wp_enqueue_style( 'bootstrap.min.css', NIRWEB_SUPPORT_URL_TICKET . 'assets/css/bootstrap.min.css' );
			wp_enqueue_style( 'user-css-wpyt', NIRWEB_SUPPORT_URL_TICKET . 'assets/user.css' );
			wp_register_script( 'jquery_wpy_ticket', NIRWEB_SUPPORT_URL_TICKET . 'assets/js/jquery.min.js' );
			wp_register_script( 'wpy_scripts', NIRWEB_SUPPORT_URL_TICKET . 'assets/js/wpy_scripts.js' );
			wp_enqueue_script( 'jquery_wpy_ticket' );
			wp_enqueue_script( 'wpy_scripts' );

			wp_localize_script(
				'wpy_scripts',
				'wp',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);

		}
	}
}






