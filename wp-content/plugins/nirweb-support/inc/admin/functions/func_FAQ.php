<?php
if ( ! function_exists( 'nirweb_ticket_get_all_faq' ) ) {
	function nirweb_ticket_get_all_faq() {
		global $wpdb;
		$faqs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nirweb_ticket_ticket_faq WHERE %d ORDER BY id DESC  ", 1 ) );
		return $faqs;
	}
}
if ( ! function_exists( 'nirweb_ticket_ajax_get_all_faq' ) ) {
	function nirweb_ticket_ajax_get_all_faq() {
		 global $wpdb;
		$faqs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nirweb_ticket_ticket_faq  WHERE %d ORDER BY id DESC", 1 ) );
		foreach ( $faqs as $faq ) :
			echo '
      <li class="flex w-100">
      <span class="dashicons dashicons-trash remove_faq danger" data-id=""></span>
      <div class="li_list_question  ">
          <div class="question_wpy_faq flex">
              <span class="soal_name_wpyt">' . esc_html( $faq->question ) . '</span>
              <span class="arrow_wpyt cret flex aline-c"></span>
          </div>
          <div class="answer_wpys_faq" >
              <p>' . esc_html( $faq->answer ) . '
              </p>
          </div>  </div> </li>
         ';
		endforeach;
	}
}
if ( ! function_exists( 'nirweb_ticket_add_question_faq' ) ) {
	function nirweb_ticket_add_question_faq() {
		 $answer = preg_replace( '/\\\\/', '', $_POST['content_question_faq'] );
		global $wpdb;
		$ary_info = array(
			'question' => sanitize_text_field( $_POST['text_question_faq'] ),
			'answer'   => sanitize_textarea_field( wpautop( $answer ) ),
		);
		$wpdb->insert( $wpdb->prefix . 'nirweb_ticket_ticket_faq', $ary_info ,['%s','%s'] );
	}
}
if ( ! function_exists( 'nirweb_ticket_delete_faq' ) ) {
	function nirweb_ticket_delete_faq() {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'nirweb_ticket_ticket_faq', array( 'id' => intval( sanitize_text_field( $_POST['col_id'] ) ) ),['%d'] );
	}
}

