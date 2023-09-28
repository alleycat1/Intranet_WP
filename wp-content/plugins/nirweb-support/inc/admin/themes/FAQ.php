<?php
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_FAQ.php';
if ( isset( $_POST['submit_new_faq'] ) ) {
	nirweb_ticket_save_new_faq();
}
$FAQS = nirweb_ticket_get_all_faq();
?>

<h1 class="title_page_wpyt"><?php echo esc_html__( 'FAQ', 'nirweb-support' ); ?></h1>
<div class="wapper flex">
	<div class="right_FAQ">
		<form action="" id="form_Add_faq" method="post">
			<div class="question__faq flex flexd-cul">
				<label class="w-100"><b><?php echo esc_html__( 'Question', 'nirweb-support' ); ?></b></label>
				<input name="nirweb_ticket_frm_subject_faq_ticket" id="nirweb_ticket_frm_subject_faq_ticket"
					   class="wpyt_input" placeholder="<?php echo esc_html__( 'Enter Question', 'nirweb-support' ); ?>">
			</div>


			<div class="answer__faq flex flexd-cul">
				<label class="w-100"><b><?php echo esc_html__( 'Answer', 'nirweb-support' ); ?></b></label>
				    <?php wp_editor( '', 'nirweb_ticket_frm_faq_ticket' ); ?>
			</div>

			<?php wp_nonce_field( 'add_question_faq_once_act', 'add_question_faq_once' ); ?>
			<button name="submit_new_faq" id="submit_new_faq" class="button button-primary">
				<?php echo esc_html__( 'Add Question', 'nirweb-support' ); ?>
			</button>

		</form>

	</div>

	<?php wp_nonce_field( 'del_question_faq_once_act', 'del_question_faq_once' ); ?>
	<div class="left_FAQ">
		<ul class="list__question_faq">
			<?php
			if ( count( $FAQS ) >= 1 ) {
				foreach ( $FAQS as $faq ) :
					echo  '
    <li class="flex w-100"> <span class="dashicons dashicons-trash remove_faq danger" data-id="' . esc_html( $faq->id ) . '"></span>
    <div class="li_list_question  "> <div class="question_wpy_faq flex"> <span class="soal_name_wpyt">' . esc_html( $faq->question ) . '</span> 
    <span class="arrow_wpyt cret flex aline-c"></span> </div>  <div class="answer_wpys_faq" >  <p>' . wpautop( esc_html( $faq->answer ) ) . ' </p> </div> </div> </li>  '  ;
				endforeach;
			}
			else {
				echo esc_html__( 'not found', 'nirweb-support' );
			}
			?>

		</ul>
	</div>

</div>
