<h1 class="title_page_wpyt"><?php echo esc_html__( 'Send ticket', 'nirweb-support' ); ?></h1>
<div class="container flex justify-content-sb">
    <div class="nirweb_ticket_right_container_send_ticket">
        <form action="" id="send_form_ticket" name="send_form_ticket[]" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'admin_send_ticket_act', 'admin_send_ticket' ); ?>
            <div class="nirweb_ticket_frm_send_ticket_select_receiver flex aline-c">
                <div class="nirweb_ticket_frm_type_receiver flex flexd-cul halft_wpyt">
                    <label><?php echo esc_html__( 'Receiver type', 'nirweb-support' ); ?> *</label>
                    <select id="nirweb_ticket_frm_type_receiver" name="nirweb_ticket_frm_type_receiver"
                            class="wpyt_select" required aria-required="true">
                        <option value="0"><?php echo esc_html__( 'Select Receiver type', 'nirweb-support' ); ?></option>
                        <option value="1"><?php echo esc_html__( 'User', 'nirweb-support' ); ?></option>
                        <option value="2"> <?php echo esc_html__( 'Support', 'nirweb-support' ); ?></option>
                    </select>
                </div>
                <!--  List Receiver Final-->
                <div class="nirweb_ticket_frm_final_items_receiver flex flexd-cul halft_wpyt sel2">
                    <label><?php echo esc_html__( 'Receiver', 'nirweb-support' ); ?> *</label>
                    <select id="selUser" class="wpyt_select" name="selUser" aria-required="true">
						<?php
						if ( isset( $info_ticket->id_receiver ) ) {
							if ( $info_ticket->receiver_type == 1 ) {
								$get_users = get_users();
								foreach ( $get_users as $user ) { ?>
                                    <option value="<?php echo esc_html( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></option>
								<?php }
							} else {
								$get_users = get_users( array( 'role__in' => array( 'user_support' ) ) );
								foreach ( $get_users as $user ) { ?>
                                    <option value="<?php echo esc_html( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></option>;
								<?php }
							}
						} ?>
                    </select>
                </div>
            </div>
            <label for="nirweb_ticket_frm_subject_send_ticket">
                <?php echo esc_html__( 'Subject', 'nirweb-support' ); ?> *</label>
            <input type="text" id="nirweb_ticket_frm_subject_send_ticket" class="wpyt_input"
                   name="nirweb_ticket_frm_subject_send_ticket"
                   placeholder="<?php echo esc_html__( 'Enter Subject', 'nirweb-support' ); ?>">
            <div class="box_wpyt flex justify-content-sb">
                <div class="halft_wpyt">
                    <label><?php echo esc_html__( 'Department', 'nirweb-support' ); ?></label>
                    <select class="wpyt_select" id="nirweb_ticket_frm_department_send_ticket"
                            name="nirweb_ticket_frm_department_send_ticket">
                        <option value="0"><?php echo esc_html__( 'Select Department', 'nirweb-support' ); ?></option>
						<?php foreach ( $departments as $department ) : ?>
                            <option value="<?php echo esc_html( $department->department_id ); ?>"><?php echo esc_html( $department->name ); ?></option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="halft_wpyt">
                    <label><?php echo esc_html__( 'Priority', 'nirweb-support' ); ?></label>
                    <select class="wpyt_select" id="nirweb_ticket_frm_priority_send_ticket"
                            name="nirweb_ticket_frm_priority_send_ticket">
                        <option value="0"><?php echo esc_html__( 'Select Priority', 'nirweb-support' ); ?></option>
						<?php $list_priority = nirweb_ticket_get_priority();
						foreach ( $list_priority as $priority ) { ?>
                            <option value="<?php echo esc_html( $priority->priority_id ); ?>"><?php echo esc_html( $priority->name ); ?></option>
						<?php } ?>
                    </select>
                </div>
            </div>
            <div class="box_wpyt flex justify-content-sb">
                <div class="halft_wpyt">
                    <label><?php echo esc_html__( 'WebSite', 'nirweb-support' ); ?></label>
                    <input type="text" class="wpyt_input" name="nirweb_ticket_frm_website_send_ticket"
                           id="nirweb_ticket_frm_website_send_ticket">
                </div>
				<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
                    <div class="halft_wpyt select_product_wpyt sel3">
                        <label><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></label>
                        <select id="nirweb_ticket_frm_product_send_ticket" class="wpyt_select"
                                name="nirweb_ticket_frm_product_send_ticket">
                            <option value="-1"><?php echo esc_html__( 'Select Product', 'nirweb-support' ); ?></option>
							<?php $args = array( 'post_type' => 'product' );
							$loop       = new WP_Query( $args );
							while ( $loop->have_posts() ) : $loop->the_post();
								global $product;
								echo '<option value="' . esc_html( $product->get_ID() ) . '" >' . esc_html( get_the_title() ) . '</option>';
							endwhile;
							wp_reset_query(); ?>
                        </select>
                    </div>

				<?php } elseif ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) { ?>
                    <div class="halft_wpyt select_product_wpyt sel3">
                        <label><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></label>
                        <select id="nirweb_ticket_frm_product_send_ticket" class="wpyt_select"
                                name="nirweb_ticket_frm_product_send_ticket">
                            <option value="-1"><?php echo esc_html__( 'Select Product', 'nirweb-support' ); ?></option>
							<?php $args = array( 'post_type' => 'download' );
							$loop       = new WP_Query( $args );
							while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
							<?php endwhile;
							wp_reset_query(); ?>
                        </select>
                    </div>
				<?php } ?>
            </div>
            <div class="box_wpyt">
                <label for="nirweb_ticket_frm_custom_editor"><?php echo esc_html__( 'Message', 'nirweb-support' ); ?>
                    *</label>
				<?php wp_editor( '', 'nirweb_ticket_frm_custom_editor' ); ?>
            </div>
            <div class="file__wpyt">
                <label><?php echo esc_html__( 'Attachment File', 'nirweb-support' ); ?></label>
                <input type="text" id="nirweb_ticket_frm_file_send_ticket" name="nirweb_ticket_frm_file_send_ticket"
                       class="regular-text process_custom_images">
                <input id="plupload-browse-button" name="misha_upload_image_button" type="button"
                       value="<?php echo esc_html__( 'Attach', 'nirweb-support' ); ?>"
                       class="button wpyt_upload_image_button" style=" position: relative; z-index: 1;">
            </div>
			<?php wp_nonce_field( '_act__admin_send_ticket_nirweb', 'admin_send_ticket_nirweb' ); ?>
            <div class="send__form">
                <button type="submit" class="btn-send btn_send_ticket"
                        data-name="nirweb_ticket_frm_btn_send_ticket"><?php echo esc_html__( 'Send Ticket', 'nirweb-support' ); ?></button>
            </div>
    </div>
    <!--Left Container -->
         <div class="nirweb_ticket_left_container_send_ticke w-25">
        <div class="nirweb_ticket_left_sidebar_send_ticket">
            <div class="nirweb_ticket_left_sidebar_header">
                <h4><?php echo esc_html__( 'Status', 'nirweb-support' ); ?></h4>
                <span class="arrow_wpyt cret flex aline-c"></span>
            </div>
            <div class="nirweb_ticket_left_sidebar_content">
                <label><?php echo esc_html__( 'Select Status', 'nirweb-support' ); ?></label>
                <select id="nirweb_ticket_frm_status_send_ticket" class="wpyt_select"
                        name="nirweb_ticket_frm_status_send_ticket">
					<?php $list_status = nirweb_ticket_get_status();
					foreach ( $list_status as $status ) : ?>
                        <option value="<?php echo esc_html( $status->status_id ); ?>"><?php echo esc_html( $status->name_status ); ?></option>
					<?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="nirweb_ticket_left_sidebar_send_ticket">
            <div class="nirweb_ticket_left_sidebar_header">
                <h4><?php echo esc_html__( 'Notification', 'nirweb-support' ); ?></h4>
                <span class="arrow_wpyt cret"></span>
            </div>
            <div class="nirweb_ticket_left_sidebar_content">
                <label><?php echo esc_html__( 'Select Notification Type', 'nirweb-support' ); ?></label>
                <div class="arow">
                    <input id="chk_email" name="chk_email" type="checkbox">
                    <label><?php echo esc_html__( 'Send Email To User', 'nirweb-support' ); ?></label>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

