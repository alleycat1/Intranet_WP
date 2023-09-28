<div class="nirweb_ticket_base">
	<?php
	$ticket_id    = intval(sanitize_text_field( $_GET['id'] ));
	$user_tickets = array();
	require NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'func_u_list_ticket.php';
	$ticket = nirweb_ticket_get_list_all_ticket_user();
	foreach ( $ticket as $tts ) {
		if ( is_array( $tts ) || is_object( $tts ) ) {
			foreach ( $tts as $rs ) {
				array_push( $user_tickets, $rs->ticket_id );
			}
		}
	}
	if ( in_array( $ticket_id, $user_tickets ) ) {
		include NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_status_and_priority.php';
		$info_ticket = nirweb_ticket_edit_ticket( $ticket_id );
		include NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_list_answered.php';
		$info_answerd = nirweb_ticket_get_list_answerd(); ?>
		<div class="top_box_back_rep">
			<?php
			if ( is_plugin_active( 'wpyar_panel/wpyar_panel.php' ) ) {
				$page = esc_url_raw(slug_page . '?endp=nirweb-ticket');
			} else {
				if ( get_option('select_page_ticket') ) {
					$page = esc_url_raw( get_page_link( get_option('select_page_ticket') ) );
				} else {
					$page = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'wpyar-ticket/';
				}
			}
			?>
			<a href=" <?php echo esc_url_raw( $page ); ?> " class="btn btn_back_wpyt">
				<?php echo esc_html__( 'Back To List Tickets', 'nirweb-support' ); ?>
			</a>
		</div>
		<div class="edit_ticket_bg">
			<div class="display_content_ticket">
		 <span>
			 <?php echo esc_html__( 'Subject', 'nirweb-support' ); ?>
			   <span class="subject_ticket"> <?php echo esc_html( $info_ticket->subject ); ?></span>
		 </span>
				<span>
			<?php echo esc_html__( 'Ticket Number', 'nirweb-support' ); ?>
			<?php echo esc_html( $ticket_id ); ?></span>
			</div>
			<div class="info_wpyar_ticket wpyar-ticket">
				<div class="box_info_ticket">
					<p class="title"><?php echo esc_html__( 'Department', 'nirweb-support' ); ?></p>
					<p class="info dep"
					   user-id="<?php echo esc_html( $info_ticket->support_id ); ?>"><?php echo esc_html( $info_ticket->depname ); ?></p>
				</div>
				<div class="box_info_ticket">
					<p class="title"><?php echo esc_html__( 'Status', 'nirweb-support' ); ?></p>
					<p class="info"> <?php echo esc_html( $info_ticket->name_status ); ?></p>
				</div>
				<div class="box_info_ticket">
					<p class="title"><?php echo esc_html__( 'Priority', 'nirweb-support' ); ?></p>
					<p class="info priority"><?php echo esc_html( $info_ticket->proname ); ?></p>
				</div>
				<?php if ( $info_ticket->product_name ) : ?>
					<div class="box_info_ticket">
						<p class="title"><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></p>
						<p class="info product"><?php echo esc_html( $info_ticket->product_name ); ?></p>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<div class="box_answer_war_wpyar_ticket">
			<div class="ticket_question">
				<div class="img_avatar_wpyartick">
					<?php echo get_avatar( $info_ticket->sender_id, 100 ); ?>
				</div>

				<div class="info_answer_box_wpyartick">
					<div class="text_message_wpyartick">
						<?php echo wpautop( wp_unslash( $info_ticket->content ) ); ?>

						<?php if ( $info_ticket->file_url ) : ?>
							<p class="file_atach_url">
								<a href="<?php echo esc_url_raw( $info_ticket->file_url ); ?>" target="_blank">
									<?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?>
								</a>
							</p>
						<?php endif; ?>
					</div>

					<div class="head_answer">
					<span class="name">
						<?php echo esc_html( $info_ticket->display_name ); ?>
					</span>
						<span class="date">
						<?php echo esc_html__( 'Date ', 'nirweb-support' ); ?> :
						<?php echo esc_html( wp_date( 'd F Y', strtotime( $info_ticket->date_qustion ) ) ); ?>
					</span>
						<span class="time">
						<?php echo esc_html__( ' Hour ', 'nirweb-support' ); ?>
						<?php echo esc_html( date( 'H:i:s', strtotime( $info_ticket->date_qustion ) ) ); ?>
					</span>
					</div>
				</div>
			</div>
			<div class="list_answerd_in_dash_admin">
				<ul class="list_all_answered">
					<?php
					foreach ( $info_answerd as $row ) :
						$user = get_userdata( $row->user_id );
						$role = $user->roles;
						if ( ( in_array( 'user_support', $role ) ) or ( in_array( 'administrator', $role ) ) ) {
							$cls = 'user_support_wpyar';
						} else {
							$cls = '';
						}
						?>
						<li class="<?php echo esc_html($cls); ?>">
							<div class="img_avatar_wpyartick">
								<?php echo get_avatar( $row->user_id, 100 ); ?>
							</div>

							<div class="info_answer_box_wpyartick">
								<div class="text_message_wpyartick">
									<?php echo wpautop( wp_unslash( $row->text ) ); ?>
									<?php if ( $row->attach_url ) { ?>
										<p class="file_atach_url">
											<a href="<?php echo esc_url_raw( $row->attach_url ); ?>" target="_blank">
												<?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?>
											</a>
										</p>
									<?php } ?>
								</div>


								<div class="head_answer">
						<span class="name">
							<?php echo esc_html( $row->display_name ); ?>
						</span>
									<?php if ( in_array( 'user_support', $role ) ) { ?>
										<span class="time">
                                            <?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
                                            <?php echo  date( 'H:i:s', strtotime( esc_html($row->time_answer ) ) ); ?>
						                </span>
										<span class="date">
                                            <?php echo esc_html__( 'Date :', 'nirweb-support' ); ?>
                                            <?php echo  wp_date( 'd F Y', strtotime( esc_html($row->time_answer ) ) ); ?>
					                	</span>
									<?php } else { ?>
										<span class="date">
                                            <?php echo esc_html__( 'Date :', 'nirweb-support' ); ?> :
                                            <?php echo  wp_date( 'd F Y', strtotime( esc_html($row->time_answer ) ) ); ?>
					                	</span>
										<span class="time">
                                            <?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
                                            <?php echo  date( 'H:i:s', strtotime( esc_html( $row->time_answer ) ) ); ?>
					                	</span>
									<?php } ?>

								</div>

							</div>

						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>

		<div class="row_wpyt">
			<div class="content_answer_user_wpyar_ticket">
				<form action="" id="answer_form">
					<div class="w-100">
						<label><?php echo esc_html__( 'Message *', 'nirweb-support' ); ?></label>
					 <textarea id="user_content_answ" name="user_content_answ" placeholder="<?php echo esc_html__( 'Enter the text of the message Plase', 'nirweb-support' ); ?>"></textarea>
					</div>

					<div class="row_wpyar_ticket ">
						<div class="row_nirweb_ticket_send wpyar_upfile_base">
							<div class="upfile_wpyartick">
								<label for="main_image" class="label_main_image">
									<span class="remove_file_by_user"><i class="fal fa-times-circle"></i></span>
									<i class="fal fa-arrow-up upicon" style="font-size: 30px;margin-bottom: 10px;"></i>
									<span class="text_label_main_image"> <?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?> </span>
								</label>
								<input type="file" name="main_image" id="main_image" accept="  <?php echo esc_html( get_option('mojaz_file_upload_user_wpyar') ) 	?>  ">
							</div>
							<div id="apf-response"></div>
						</div>
					</div>
					<div class="wpyar-ticket box_btn_send_answer_user">
						<div class="base_loarder">
							<div class="spinner">
								<div class="double-bounce1"></div>
								<div class="double-bounce2"></div>
							</div>
							<p><?php echo esc_html__( 'Sending ...', 'nirweb-support' ); ?></p>
						</div>

						<div class="box_btn_send_answer_user">
							<input id="closed_answer" name="closed_answer" value="closed_answer" type="checkbox">
							<label for="closed_answer"><?php echo esc_html__( 'Close Ticket', 'nirweb-support' ); ?></label>
						</div>

						<?php wp_nonce_field( 'nirweb_ticket_user_send_answer_act', 'nirweb_ticket_user_send_answer' ); ?>
						<button data-id="<?php echo esc_html( $_GET['id'] ); ?>" class="send_user_answer"><?php echo esc_html__( 'Send Answer', 'nirweb-support' ); ?></button>
					</div>

				</form>

			</div>
		</div>
			<?php
			add_action(
				'wp_footer',
				function () {
					$accsses_file = str_replace( '.', '', trim( get_option('mojaz_file_upload_user_wpyar') ) );
					$accsses_file = explode( ',', trim( $accsses_file ) );
					?>
				<script>
					jQuery("#main_image").change(function () {
						var ext = this.value.match(/\.(.+)$/)[1];
						switch (ext) {
						<?php foreach ( $accsses_file as $file ) : ?>
							case "<?php echo esc_html( esc_js($file) ); ?>":
							<?php endforeach; ?>
								break;
							default:
								alert(wpyarticket.nvalid_file);
								this.value = '';
						}
					});
					jQuery('.send_user_answer').click(function (e) {
						e.preventDefault();
						jQuery('.base_loarder').css('display', 'flex');
						var once = jQuery('#nirweb_ticket_user_send_answer').val();
						var user_answer = jQuery('#user_content_answ').val();
						var tik_id = jQuery(this).attr('data-id');
						var id_user = jQuery('.dep').attr('user-id');
						var dep_name = jQuery('.dep').text();
						var priority_name = jQuery('.priority').text();
						var subject = jQuery('.subject_ticket').text();
						var closed_answer = jQuery('input[name=closed_answer]:checked').val();
						var formData = new FormData();
						formData.append('updoc', jQuery('#main_image')[0].files[0]);
						formData.append('user_answer', user_answer),
							formData.append('once', once),
							formData.append('tik_id', tik_id),
							formData.append('id_user', id_user),
							formData.append('dep_name', dep_name),
							formData.append('subject', subject),
							formData.append('priority_name', priority_name);
						if (closed_answer) {
							formData.append('closed_answer', closed_answer);
						}
						if (jQuery('#main_image')[0].files[0]) {
							var size_file = jQuery('#main_image')[0].files[0]['size'];
							var ac_size = <?php echo esc_html(esc_js(get_option('size_of_file_wpyartik'))); ?>000000;
							if (size_file >= ac_size) {
								jQuery('.text_upload').css('display', 'none')
								alert(wpyarticket.max_size_file);
								return false
							}
						}
						formData.append('action', "user_answer_ticket");
						jQuery.ajax({
							url: "<?= admin_url('admin-ajax.php') ?>",
							type: "POST",
							data: formData, cache: false,
							processData: false,
							contentType: false,
							success: function (response) {
								console.log(response);
								if (response == 'error_valid_type') {
									alert(wpyarticket.nvalid_file);
									jQuery('.base_loarder').css('display', 'none');
									jQuery('.text_label_main_image').html('<?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?>');
									return false;
								}
								jQuery('.base_loarder').css('display', 'none');
								jQuery('#answer_form').trigger('reset');
								jQuery('.list_all_answered').html(response)
								jQuery('.remove_file_by_user').hide();
								jQuery('.text_label_main_image').html('<?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?>');
								//window.location.reload();
							},
						});
					});
				</script>
					<?php
				}
			);
	} else {
		echo '<p>' . esc_html__( 'You do not have permission to access this ticket', 'nirweb-support' ) . '</p>';
	}
	?>
</div>
