<?php

add_action(
	'admin_enqueue_scripts',
	function () {
		if ( is_rtl() ) {
			wp_enqueue_style( 'admin-css-wpyt', NIRWEB_SUPPORT_URL_CSS_TICKET . 'admin-rtl.css' );
		} else {
			wp_enqueue_style( 'admin-css-wpyt', NIRWEB_SUPPORT_URL_CSS_TICKET . 'admin.css' );
		}
		wp_enqueue_style( 'select-wpyt-tw.css', NIRWEB_SUPPORT_URL_CSS_TICKET . 'select.tw.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'select_2-js-wpyt', NIRWEB_SUPPORT_URL_TICKET . 'assets/js/select_2.js' );
		wp_enqueue_script( 'admin-js-file-wpyt', NIRWEB_SUPPORT_URL_TICKET . 'assets/js/admin.js' );
		wp_localize_script(
			'admin-js-file-wpyt',
			'wpyarticket',
			array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'comp_sec'         => esc_html__( 'Please complete all starred sections', 'nirweb-support' ),
				'send_info'        => esc_html__( 'Sending information...', 'nirweb-support' ),
				'add_file'         => esc_html__( 'Add File', 'nirweb-support' ),
				'use_file'         => esc_html__( 'Use the file', 'nirweb-support' ),
				'send_tik_success' => esc_html__( 'Your ticket was sent successfully', 'nirweb-support' ),
				'send_ans_success' => esc_html__( 'Your answer was sent successfully', 'nirweb-support' ),
				'send_ans_err'     => esc_html__( 'There was a problem sending the reply', 'nirweb-support' ),
				'ques'             => esc_html__( 'Are you sure?', 'nirweb-support' ),
				'subdel'           => esc_html__( 'The delete action causes the information to be lost.', 'nirweb-support' ),
				'ok'               => esc_html__( 'Ok', 'nirweb-support' ),
				'cancel'           => esc_html__( 'Cancel', 'nirweb-support' ),
				'add_dep'          => esc_html__( 'Add Department', 'nirweb-support' ),
				'name_dep_err'     => esc_html__( 'Please enter the name of the department', 'nirweb-support' ),
				'sup_dep_err'      => esc_html__( 'Please enter the support of the department', 'nirweb-support' ),
				'chenge_dep'       => esc_html__( 'The department changed successfully', 'nirweb-support' ),
				'add_ques_err'     => esc_html__( 'Please enter a question', 'nirweb-support' ),
				'add_text_faq_err' => esc_html__( 'Please enter the answer text', 'nirweb-support' ),
				'faq_ques_add'     => esc_html__( 'Question added', 'nirweb-support' ),
			)
		);
		wp_localize_script(
			'sweetalert2-wpyt-min-js',
			'wpyarticketsw',
			array(
				'ok'     => esc_html__( 'Ok', 'nirweb-support' ),
				'cancel' => esc_html__( 'Cancel', 'nirweb-support' ),
			)
		);

	}
);






