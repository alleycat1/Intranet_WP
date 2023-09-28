<div class="nirweb_ticket_base">
	<?php
	require_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_status_and_priority.php';
	require_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_department.php';
	require_once NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'func_faq.php';
	$faq = nirweb_ticket_get_all_faq_user();
	?>
	<div class="top_box_send_ticket">
		<div class="head_send_ticket_wpyar">
			<h4 class="wpyar-ticket"><?php echo esc_html__( 'New Ticket', 'nirweb-support' ); ?></h4>
			<?php
			if ( is_plugin_active( 'wpyar_panel/wpyar_panel.php' ) ) {
				$page = esc_url_raw(slug_page . '?endp=nirweb-ticket');
			} else {
				if ( get_option('select_page_ticket') ) {
					$page = esc_url_raw( get_page_link( get_option('select_page_ticket') ) );
				} else {
					$page = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'wpyar-ticket/';
				}
			} ?>
			<a href=" <?php echo esc_url_raw( $page ); ?> "  class="btn btn_back_wpyt"><?php echo esc_html__( 'Back To Tickets', 'nirweb-support' ); ?></a>
		</div>
		<div class="content_ticket_send">
			<?php echo wpautop( wp_kses_post(get_option('text_top_send_mail_nirweb_ticket')) ); ?>
		</div>
		<div class="list_of_faq_wpyar">
			<ul>
				<?php foreach ( $faq as $key => $value ) : ?>
					<li>
						<p class="li_list_of_faq_wpyar">
							<span class="number_faq_wpyar"><?php echo esc_html( $key ) + 1; ?>.</span>
							<span class="title_faq_wpyar"><?php echo esc_html( $value->question ); ?></span>
						</p>
						<div class="content_faq_wpyar"><?php echo esc_html( $value->answer ); ?> </div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="not_found_answer"  <?php  if ( ! $faq ) { echo "style='display:none !important'"; } ?>  >
			<span><?php echo esc_html__( 'I didn\'t find the answer to my question', 'nirweb-support' ); ?></span>
		</div>
	</div>
	<form class="form__global__ticket_new shadow__wpys" id="send_ticket_form" method="post" enctype="multipart/form-data" <?php if ( $faq ) { echo "style='display:none'"; } ?> >
		<div class="ibenic_upload_message"></div>
		<div class="row_nirweb_ticket_send">
			<div class="w-50">
				<label for="nirweb_ticket_frm_subject_send_ticket_user"><?php echo esc_html__( 'Subject *', 'nirweb-support' ); ?></label>
				<input type="text" id="nirweb_ticket_frm_subject_send_ticket_user" name="nirweb_ticket_frm_subject_send_ticket_user">
			</div>
			<div class="w-50">
				<div class="department_form_user_send">
					<label for="nirweb_ticket_frm_department_send_ticket_user"><?php echo esc_html__( 'Department *', 'nirweb-support' ); ?></label>
					<div class="select_custom_wpyar">
						<div class="custom_input_wpyar_send_ticket" id="nirweb_ticket_frm_department_send_ticket_user" data-id="-1" data-user="0">
							<?php echo esc_html__( 'Select department', 'nirweb-support' ); ?>
						</div>
						<i class="fal fa-angle-down"></i>
						<ul>
							<?php $departments = nirweb_ticket_ticket_get_list_department(); foreach ( $departments as $department ) : ?>
								<li data-user="<?php echo esc_html( $department->support_id ); ?>" data-id="<?php echo esc_html( $department->department_id ); ?>">
                                    <?php echo esc_html( $department->name ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="row_nirweb_ticket_send">
			<div class="w-50">
				<label for="nirweb_ticket_frm_priority_send_ticket_user"><?php echo esc_html__( 'Priority *', 'nirweb-support' ); ?></label>
				<div class="select_custom_wpyar">
					<div class="custom_input_wpyar_send_ticket" id="nirweb_ticket_frm_priority_send_ticket_user" data-id="-1">
						<?php echo esc_html__( 'Select priority', 'nirweb-support' ); ?>
					</div>
					<i class="fal fa-angle-down"></i>
					<ul>
						<li data-id="1"> <?php echo esc_html__( 'low', 'nirweb-support' ); ?></li>
						<li data-id="2"> <?php echo esc_html__( 'normal', 'nirweb-support' ); ?></li>
						<li data-id="3"> <?php echo esc_html__( 'necessary', 'nirweb-support' ); ?></li>
					</ul>
				</div>
			</div>
			<div class="w-50">
				<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
					<label for="product_user_wpyar_tixket">
						<?php echo esc_html__( 'Product', 'nirweb-support' ); ?>
						<?php if ( @get_option('require_procut_user_wpyar') == '1' ) { echo ' * '; } ?>
					</label>
					<div class="select_custom_wpyar">
						<div class="custom_input_wpyar_send_ticket" id="product_user_wpyar_tixket" data-id="-1">
							<?php echo esc_html__( 'Select Product', 'nirweb-support' ); ?>
						</div>
						<i class="fal fa-angle-down"></i>
						<ul>
							<?php
							$customer_orders = get_posts(
								array(
									'numberposts' => -1,
									'meta_key'    => '_customer_user',
									'meta_value'  => get_current_user_id(),
									'post_type'   => wc_get_order_types(),
									'post_status' => array_keys( wc_get_order_statuses() ),
								)
							);
							if ( $customer_orders ) {

								foreach ( $customer_orders as $customer_order ) {
									$order = wc_get_order( $customer_order->ID );
									$items = $order->get_items();
									foreach ( $items as $item ) {
										$product_id   = $item->get_product_id();
										$product_name = $item->get_name();
										?>
										<li data-id="<?php echo esc_html( $item['product_id'] ); ?>">
											<?php echo get_the_title(intval($item['product_id'] ) ); ?>
										</li>
										<?php
									}
								}
							}
							?>
						</ul>
					</div>
				<?php } elseif ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) { ?>
				<label><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></label>
				<div class="select_custom_wpyar">
					<div class="custom_input_wpyar_send_ticket" id="product_user_wpyar_tixket" data-id="-1">
						<?php echo esc_html__( 'Select Product', 'nirweb-support' ); ?>
					</div>
					<i class="fal fa-angle-down"></i>
					<ul>
                        <?php $rep = edd_get_users_purchased_products( $user = get_current_user_id(), $status = 'complete' );
                                foreach ( $rep as $row ) :
                                echo '<li data-id="' . esc_html( $row->ID ) . '">' . esc_html( $row->post_title ) . '</li>';
                                endforeach;
                        echo '</ul></div>'; } ?>
				</div>
			</div>
		<div class="row_nirweb_ticket_send">
			<div class="w-100">
				<label><?php echo esc_html__( 'Message *', 'nirweb-support' ); ?></label>
				<textarea id="nirweb_ticket_frm_content_send_ticket_user" name="nirweb_ticket_frm_content_send_ticket_user"
						  placeholder="<?php echo esc_html__( 'Enter Message please', 'nirweb-support' ); ?>"></textarea>
			</div>
		</div>
		<div class="row_nirweb_ticket_send wpyar_upfile_base">
			<div class="upfile_wpyartick">
				<label for="main_image" class="label_main_image">
					<span class="remove_file_by_user"><i class="fal fa-times-circle"></i></span>
					<i class="fal fa-arrow-up upicon" style="font-size: 30px;margin-bottom: 10px;"></i>
					<span class="text_label_main_image"> <?php echo esc_html__( 'Attachment', 'nirweb-support' ); ?></span>
				</label>
				<input type="file" name="main_image" id="main_image"
					   accept="<?php echo esc_html( get_option('mojaz_file_upload_user_wpyar') ); ?>">
			</div>
		</div>
		<div class="send_reset_form">
			<div class="base_loarder">
				<div class="spinner">
					<div class="double-bounce1"></div>
					<div class="double-bounce2"></div>
				</div>
				<p><?php echo esc_html__( 'Sending ...', 'nirweb-support' ); ?></p>
			</div>
			<?php wp_nonce_field( 'nirweb_ticket_user_send_ticket_act', 'nirweb_ticket_user_send_ticket' ); ?>
			<button data-fileurl="" type="submit" class="btn btn-primary text-white"
					name="nirweb_ticket_frm_user_send_ticket" id="nirweb_ticket_frm_user_send_ticket">
				<?php echo esc_html__( 'Send', 'nirweb-support' ); ?>
			</button>
			<p class="stasus_send_wpyt"></p>
		</div>
	</form>
	<?php
	add_action('wp_enqueue_scripts',function(){
		wp_enqueue_script('jquery');
	});
	add_action(
		'wp_footer',
		function () {
			$accsses_file = str_replace( '.', '', esc_html( trim( get_option('mojaz_file_upload_user_wpyar') ) ) );
			$accsses_file = explode( ',', trim( $accsses_file ) );
			?>
	<!-- End form-->
	<script>
		jQuery('body').on('change', '#main_image', function () {
			console.log(this.value.match(/\.(.+)$/)[1]);
			var ext = this.value.match(/\.(.+)$/)[1];
			switch (ext) {
			<?php foreach ( $accsses_file as $file ) : ?>
				case "<?php echo esc_html( esc_js($file) ); ?>":
					break;
			<?php endforeach; ?>

				default:
					alert(wpyarticket.nvalid_file);
					this.value = '';
			}

		});
		//--------------------  Request Send ticket
		jQuery('body').on('click', '#nirweb_ticket_frm_user_send_ticket', function (e) {
			e.preventDefault();
			jQuery('.base_loarder').css('display', 'flex');
			var once = jQuery('#nirweb_ticket_user_send_ticket').val()
			var subject = jQuery('#nirweb_ticket_frm_subject_send_ticket_user').val()
			var department = jQuery('#nirweb_ticket_frm_department_send_ticket_user').attr('data-id');
			var dep_name = jQuery('#nirweb_ticket_frm_department_send_ticket_user').text();
			var resived_id = jQuery('#nirweb_ticket_frm_department_send_ticket_user ').attr('data-user');
			var content = jQuery('#nirweb_ticket_frm_content_send_ticket_user').val();
			var priority = jQuery('#nirweb_ticket_frm_priority_send_ticket_user').attr('data-id');
			var priority_name = jQuery('#nirweb_ticket_frm_priority_send_ticket_user').text();
			var product = jQuery('#product_user_wpyar_tixket').attr('data-id');
			var formData = new FormData();
			formData.append('updoc', jQuery('#main_image')[0].files[0]);
			formData.append('subject', subject),
				formData.append('once', once),
				formData.append('department', department),
				formData.append('dep_name', dep_name),
				formData.append('resived_id', resived_id),
				formData.append('content', content),
				formData.append('priority', priority),
				formData.append('priority_name', priority_name),
				formData.append('product', product);
			var image_select = jQuery('#main_image').val();
			if (image_select) {
				var size_file = jQuery('#main_image')[0].files[0]['size'];
				var ac_size = <?php echo esc_html(esc_js(get_option('size_of_file_wpyartik'))); ?>000000;
				if (size_file >= ac_size) {
					jQuery('.base_loarder').css('display', 'none');
					jQuery('.text_upload').css('display', 'none')
					alert(wpyarticket.max_size_file);
					return false
				}
			}
			<?php
			if ( @get_option('require_procut_user_wpyar') == '1' ) {
				$shart = 'subject && department !=-1 && priority !=-1  && product !=-1  && content';
			} else {
				$shart = 'subject && department !=-1 && priority !=-1  && content';
			}
			?>
			if (<?php echo $shart; ?>) {
				formData.append('action', "user_send_tiket");
				jQuery.ajax({
					url: "<?= admin_url('admin-ajax.php') ?>",
					type: "POST",
					data: formData, cache: false,
					processData: false,
					contentType: false,
					success: function (response) {
						if (response == 'error_valid_type') {
							alert(wpyarticket.nvalid_file);
							jQuery('.base_loarder').css('display', 'none');
							jQuery('.text_label_main_image').html('<?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?>');
							return false;
						}
						jQuery('.base_loarder').css('display', 'none');
						alert_success("<?php echo esc_html__( 'Ticket is submitted successfully.', 'nirweb-support' ); ?>");
						setTimeout(() => {
							location.reload();
						}, 3000);

						return false;
					},
				})
			} else {
				jQuery('.base_loarder').css('display', 'none');
				alert(wpyarticket.nes_field);
				return false;
			}
		})
		jQuery('body').on('click', '.bg_alert__nirweb', function (e) {
			jQuery(this).hide();
		})
		jQuery('body').on('click', '.box_alert_nirweb .cancel', function (e) {
			jQuery('.bg_alert__nirweb').hide();
		})
		jQuery('body').on('click', '.box_alert_nirweb', function (e) {
			e.preventDefault();
			e.stopPropagation();

		})

		function alert_success(message) {
			jQuery('body').append(
				`<div class="bg_alert__nirweb">
			<div class="box_alert_nirweb">
			<svg width="60" height="60" enable-background="new 0 2 98 98" version="1.1" viewBox="0 2 98 98" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
<style type="text/css">
\t.st0{fill:url(#b);}
\t.st1{fill:url(#a);}
</style> <linearGradient id="b" x1="57.767" x2="57.767" y1="96" y2="6.3234" gradientTransform="matrix(1 0 0 -1 0 104)" gradientUnits="userSpaceOnUse">
\t\t<stop stop-color="#00EFD1" offset="0"/>
\t\t<stop stop-color="#00ACEA" offset="1"/>
\t</linearGradient>
\t<path class="st0" d="m33.3 45.9c-1.1-1.2-3-1.3-4.2-0.2s-1.3 3-0.2 4.2l15.1 16.4c0.6 0.6 1.3 1 2.1 1h0.1c0.8 0 1.6-0.3 2.1-0.9l38.2-38.1c1.2-1.2 1.2-3.1 0-4.2s-3.1-1.2-4.2 0l-36 35.9-13-14.1z"/>
\t\t<linearGradient id="a" x1="49" x2="49" y1="96" y2="6.3234" gradientTransform="matrix(1 0 0 -1 0 104)" gradientUnits="userSpaceOnUse">
\t\t<stop stop-color="#00EFD1" offset="0"/>
\t\t<stop stop-color="#00ACEA" offset="1"/>
\t</linearGradient>
\t<path class="st1" d="m85.8 50c-1.7 0-3 1.3-3 3 0 18.6-15.2 33.8-33.8 33.8s-33.8-15.2-33.8-33.8 15.2-33.8 33.8-33.8c1.7 0 3-1.3 3-3s-1.3-3-3-3c-21.9 0-39.8 17.9-39.8 39.8s17.9 39.8 39.8 39.8 39.8-17.9 39.8-39.8c0-1.7-1.3-3-3-3z"/>
</svg>
				 <h4>${message}</h4>
				</div>
		</div>`
			)
		}
	</script>
			<?php
		}
	)
	?>
</div>
