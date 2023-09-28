<?php
$ticket_id    = intval(sanitize_text_field( $_GET['id'] ));
$user_tickets = array();
$info_ticket  = nirweb_ticket_edit_ticket( $ticket_id );
require_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_list_answered.php';
$info_answerd = nirweb_ticket_get_list_answerd();
if ( current_user_can( 'administrator' ) ) {
	$tickets = wp_list_ticket_check();
} else {
	 $tickets = wp_list_ticket_check_posht( get_current_user_id() );
}


foreach ( $tickets as $tts ) {
	if ( is_array( $tts ) || is_object( $tts ) ) {
		   array_push( $user_tickets, $tts->ticket_id );
	}
}

if ( in_array( $ticket_id, $user_tickets ) || current_user_can( 'administrator' ) ) {
	?>

 <h1 class="title_page_wpyt">
	  <?php echo esc_html__( 'Ticket Number ', 'nirweb-support' ); ?>
	 <?php echo esc_html( $ticket_id ); ?>

 </h1>
 <div class="edit_ticket_bg">

		 <div class="display_content_ticket">
			 <h3>
				 <span class="subject"> <?php echo esc_html( $info_ticket->subject ); ?></span>
				 <span class="titme_title">
					 <?php echo  wp_date( 'Y-m-d ', strtotime( esc_html($info_ticket->date_qustion) ) ); ?>
					 <?php echo date( 'H:i:s', strtotime( esc_html($info_ticket->date_qustion )) ); ?>
				 </span>
			 </h3>
			 <div class="text_ticket">
				 <?php echo wpautop( wp_unslash( $info_ticket->content ) ); ?>
			 </div>
		 </div>


		 <div class="info_wpyar_ticket">
				 <h2> <?php echo esc_html__( 'Ticket Information ', 'nirweb-support' ); ?> </h2>
				 <div class="row_wpyt">
						 <div class="left_info">
								 <div class="row_wpyt">
									 <div class="name_info">
										 <p>   <?php echo esc_html__( 'Sender', 'nirweb-support' ); ?> : </p>
									 </div>
									 <div class="val_info">
										 <p class="sender" user-id="<?php echo esc_html( $info_ticket->sender_id ); ?>">
											 <?php echo esc_html( $info_ticket->display_name ); ?></p>
									 </div>
								 </div>

								 <div class="row_wpyt">
									 <div class="name_info">
										 <p>   <?php echo esc_html__( 'Receiver', 'nirweb-support' ); ?>: </p>
									 </div>
									 <div class="val_info">
										 <p class="resivered" data-id="<?php echo esc_html( $info_ticket->id_receiver ); ?>">
											 <?php echo esc_html( $info_ticket->rev_name ); ?></p>
									 </div>
								 </div>



								 <div class="row_wpyt">
									 <div class="name_info">
										 <p>  <?php echo esc_html__( 'Priority', 'nirweb-support' ); ?> : </p>
									 </div>
									 <div class="val_info">
										 <p class="proname"> <?php echo esc_html( $info_ticket->proname ); ?></p>
									 </div>
								 </div>


								 <div class="row_wpyt">
									 <div class="name_info">
										 <p><?php echo esc_html__( 'WebSite', 'nirweb-support' ); ?></p>
									 </div>
									 <div class="val_info">
										 <p><?php echo esc_html( $info_ticket->website ); ?></p>
									 </div>
								 </div>


								 <div class="row_wpyt">
									 <div class="name_info">
										 <p><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></p>
									 </div>
									 <div class="val_info">
										 <p><?php echo esc_html( $info_ticket->product_name ); ?></p>
									 </div>
								 </div>

								 <div class="row_wpyt">
									 <div class="name_info">
										 <p><?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?></p>
									 </div>
									 <div class="val_info">
										 <?php if ( $info_ticket->file_url ) { ?>
										 <p><a href="<?php echo esc_url_raw( $info_ticket->file_url ); ?>" target="_blank">
												 <?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?></a></p>
										 <?php } ?>
									 </div>
								 </div>
						 </div>

							 <div class="right_info">
									  <div class=" ">
										 <label><?php echo esc_html__( 'Department', 'nirweb-support' ); ?></label>
										 <select class="wpyt_select" id="nirweb_ticket_frm_department_send_ticket"
											 name="nirweb_ticket_frm_department_send_ticket">
											 <?php foreach ( $departments as $department ) : ?>
											 <option
													<?php selected( $department->department_id, $info_ticket->department ); ?>
												 value="<?php echo esc_html( $department->department_id ); ?>">
													<?php echo esc_html( $department->name ); ?></option>
											 <?php endforeach; ?>
										 </select>
									 </div>



									 <div class="">
										 <label><?php echo esc_html__( 'Status', 'nirweb-support' ); ?></label>
										 <select class="wpyt_select" id="nirweb_ticket_frm_status_send_ticket"
											 name="nirweb_ticket_frm_status_send_ticket">
											 <?php
												$list_status = nirweb_ticket_get_status();
												foreach ( $list_status as $status ) :
													?>
											 <option <?php selected( $info_ticket->status, $status->status_id ); ?>
												 value="<?php echo esc_html( $status->status_id ); ?>">
																	<?php echo esc_html( $status->name_status ); ?></option>
																				   <?php endforeach; ?>
										 </select>
									 </div>
							 </div>
				 </div>
		 </div>

			 <div class="answerd_this_ticket_wpyar">
				 <h2><?php echo esc_html__( 'Answer', 'nirweb-support' ); ?></h2>

				 <div class="war_pre_answer_wp_yar">
					 <div class="head">
						 <p>
							 <span class="icons"></span>
							 <span class="icons"></span>
							 <span class="icons"></span>
						 </p>
						 <p><?php echo esc_html__( 'Canned replies', 'nirweb-support' ); ?></p>
					 </div>


					 <ul class="list_pre_Answer_wp_yar">

						  <?php
							$args = array(
								'post_type'      => 'pre_answer_wpyticket',
								'posts_per_page' => -1,
							);
							$loop = new WP_Query( $args );
							while ( $loop->have_posts() ) :
								$loop->the_post();
								?>

						 <li class="li_list_question  sa">
							 <div class="question_wpy_faq flex">
								 <span class="soal_name_wpyt"><?php the_title(); ?></span>
								 <div class="flex">
									 <a href="#" class="insert_text_into_editor_wp"><?php echo esc_html__( 'Insert', 'nirweb-support' ); ?></a>
									 <span class="arrow_wpyt flex aline-c cret_t"></span>
								 </div>
							 </div>
							 <div class="answer_wpys_faq">
								 <?php the_content(); ?>
							 </div>

						 </li>
								<?php
						 endwhile;
							wp_reset_query();
							?>
					 </ul>
				  </div>

				 <div class="war_insert_pre_answer_wp_yar">
					 <form data-id="<?php echo esc_html( $ticket_id ); ?>" action="" id="send_answerd_ticket" method="post"
						 enctype="multipart/form-data">
						 <?php
								$content = '';
							$editor_id   = 'nirweb_ticket_answer_editor';
							 wp_editor( esc_html( $content ), $editor_id );
							?>

						 <div class="file__wpyt">
							 <label><?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?></label>

							 <input type="text" id="nirweb_ticket_frm_file_send_ticket" name="nirweb_ticket_frm_file_send_ticket"
								 class="regular-text process_custom_images">
							 <input id="plupload-browse-button" name="misha_upload_image_button" type="button"
								 value="<?php echo esc_html__( 'Attach', 'nirweb-support' ); ?>" class="button wpyt_upload_image_button"
								 style=" position: relative; z-index: 1;">
						 </div>
						 <?php wp_nonce_field( 'admin_answer_nirweb_ticker__act', 'admin_answer_nirweb_ticker' ); ?>

                         <div class="box_send_answered">
                             <div class="base_loarder">
                                 <div class="spinner">
                                     <div class="double-bounce1"></div>
                                     <div class="double-bounce2"></div>
                                 </div>
                                 <p><?php echo __('Sending ...', 'nirweb-support') ?></p>
                             </div>
							 <button type="submit" class="btn_send_answered btn-send "><?php echo esc_html__( 'Send Answer', 'nirweb-support' ); ?></button>
						 </div>
					 </form>
				 </div>
			 </div>
			 <div class="list_answerd_in_dash_admin">
				 <h2 class="title_get_answered"><?php echo esc_html__( 'List Answer', 'nirweb-support' ); ?></h2>
				 <ul class="list_all_answered">
					  <?php foreach ( $info_answerd as $row ) : ?>
					 <li>
						 <div class="head_answer">

							 <span class="name">
								 <?php echo esc_html( $row->display_name ); ?>
							 </span>

							 <span class="time">
								 <?php echo  date( '(H:i:s)', strtotime( esc_html($row->time_answer ) ) ); ?>
								 <?php echo wp_date( 'Y-m-d', strtotime( esc_html( $row->time_answer ) ) ); ?>
							 </span>

						 </div>


						 <div class="content">
							  <?php echo wp_unslash(wpautop( $row->text )); ?>
							 <?php
								if ( $row->attach_url ) {
									echo '<p>' .
									 esc_html__( 'Attachment File', 'nirweb-support' ) . '
                                ' . esc_url_raw( $row->attach_url ) . '
                                </p>';
								}
								?>
						 </div>
					 </li>
					  <?php endforeach ?>
				  </ul>
			  </div>
 </div>
	  <?php
} else {
	echo '<p>' . esc_html__( 'You do not have permission to access this ticket', 'nirweb-support' ) . '</p>';
}
