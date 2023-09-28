<?php
if ( ! function_exists( 'filter_ajax_ticket_func' ) ) {
	function filter_ajax_ticket_func() {
		$user_id = get_current_user_id();
		switch ( sanitize_text_field( $_POST['status'] ) ) {
			case 'open':
				$status = 1;
				break;
			case 'inprogress':
				$status = 2;
				break;
			case 'answered':
				$status = 3;
				break;
			case 'closed':
				$status = 4;
				break;
		}
		global $wpdb;
		if ( $status ) {
			$process_ticket_list = $wpdb->get_results(
				$wpdb->prepare(
                        "SELECT ticket.* , users.ID , users.display_name,status.*,department.* ,department.name as depname ,priority.*,priority.name as proname  ,post.ID,post.post_title as product_name
                            FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                            LEFT JOIN {$wpdb->prefix}users users
                            ON sender_id=ID AND status= %d 
                            LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                            ON status_id=%d 
                            LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                            ON department=department_id
                                LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                            ON priority=priority_id
                            LEFT JOIN  {$wpdb->prefix}posts post
                            ON product=post.ID
                             WHERE (ticket.support_id = %d OR ticket.sender_id = %d OR ticket.id_receiver = %d ) AND status= %d
                            ORDER BY ticket_id DESC ", $status, $status, $user_id, $user_id, $user_id, $status )
			);
			if ( $process_ticket_list ) {
				?>
				<div class="base_list_ticket_uwpyar">
					<ul class="ul_list_ticket_uwpyar">
						<?php foreach ( $process_ticket_list as $row ) : ?>
							<li>
                            <a href="?action=reply&id=<?php echo esc_html( $row->ticket_id ); ?>" class="  <?php
                                    if ( intval( $row->status ) == 1 ) { echo 'arbitrator_wpyaru-red'; }
                                    if ( intval( $row->status ) == 2 ) { echo 'arbitrator_wpyaru-blue'; }
                                    if ( intval( $row->status ) == 3 ) { echo 'arbitrator_wpyaru-purple'; }
                                    if ( intval( $row->status ) == 4 ) { echo 'arbitrator_wpyaru-green'; } ?> ">
									<div class="info_user_time_wpyaru">
										<?php
										$user = wp_get_current_user();
										echo get_avatar( $user->ID, 130 );
										?>
										<div class="icon_nameUser">
											<svg id="svg_username" viewBox="0 0 14.8 17.1" style="width: 17px">
												<path
														d="M10.9 7.3c.6-.8.9-1.7.9-2.8C11.8 2 9.8 0 7.3 0S2.8 2 2.8 4.5c0 1.1.4 2 1 2.8C1.5 8.6 0 11.1 0 13.7c0 2.3 3.7 3.4 7.4 3.4s7.4-1 7.4-3.3c0-2.7-1.5-5.2-3.9-6.5zM7.3 1c2 0 3.5 1.6 3.5 3.5 0 2-1.6 3.5-3.5 3.5-2 0-3.5-1.6-3.5-3.5S5.4 1 7.3 1zm.1 15.1c-3.1 0-6.4-.8-6.4-2.4 0-2.3 1.3-4.5 3.3-5.6.9.6 2 1 3.1 1s2.2-.3 3-1c2 1.1 3.3 3.3 3.3 5.6.1 1.6-3.2 2.4-6.3 2.4z">
												</path>
											</svg>
											<?php echo esc_html( $user->display_name ); ?>
										</div>
										<time>
											<?php
											echo  wp_date( 'd F Y', esc_html(strtotime( $row->date_qustion ) ) );
											?>
											<?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
											<?php
											echo  wp_date( 'H:i', esc_html(strtotime( $row->date_qustion ) ) );
											?>
										</time>
									</div>
									<div class="title_time_ticket">
										<p><?php echo esc_html( $row->subject ); ?></p>
										<time>
											<?php  ago_ticket_nirweb( strtotime( esc_html( $row->time_update ) )) ?>
										</time>
									</div>
								</a>
							</li>
						<?php endforeach ?>
					</ul>
				</div>
				<?php
			} else {
				echo '<h3 style="color:red;font-weight: 400;text-align: center;font-size: 18px;"> ' . esc_html__( 'not found', 'nirweb-support' ) . '
        </h3>';
			}
		} else {
			include NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'func_u_list_ticket.php';
			$list_ticket = nirweb_ticket_get_list_all_ticket_user();
			?>
			<div class="base_list_ticket_uwpyar">
				<ul class="ul_list_ticket_uwpyar">
					<?php foreach ( $list_ticket[0] as $row ) : ?>
						<li>
							<a href="?action=reply&id=<?php echo esc_html( $row->ticket_id ); ?>" class="
																 <?php
																	if ( intval( $row->status ) == 1 ) {
																		echo 'arbitrator_wpyaru-red';
																	}
																	if ( intval( $row->status ) == 2 ) {
																		echo 'arbitrator_wpyaru-blue';
																	}
																	if ( intval( $row->status ) == 3 ) {
																		echo 'arbitrator_wpyaru-purple';
																	}
																	if ( intval( $row->status ) == 4 ) {
																		echo 'arbitrator_wpyaru-green';
																	}
																	?>
							">
								<div class="info_user_time_wpyaru">
									<?php
									$user = wp_get_current_user();
									echo get_avatar( $user->ID, 130 );
									?>
									<div class="icon_nameUser">
										<svg id="svg_username" viewBox="0 0 14.8 17.1" style="width: 17px">
											<path
													d="M10.9 7.3c.6-.8.9-1.7.9-2.8C11.8 2 9.8 0 7.3 0S2.8 2 2.8 4.5c0 1.1.4 2 1 2.8C1.5 8.6 0 11.1 0 13.7c0 2.3 3.7 3.4 7.4 3.4s7.4-1 7.4-3.3c0-2.7-1.5-5.2-3.9-6.5zM7.3 1c2 0 3.5 1.6 3.5 3.5 0 2-1.6 3.5-3.5 3.5-2 0-3.5-1.6-3.5-3.5S5.4 1 7.3 1zm.1 15.1c-3.1 0-6.4-.8-6.4-2.4 0-2.3 1.3-4.5 3.3-5.6.9.6 2 1 3.1 1s2.2-.3 3-1c2 1.1 3.3 3.3 3.3 5.6.1 1.6-3.2 2.4-6.3 2.4z">
											</path>
										</svg>
										<?php echo esc_html( $user->display_name ); ?>
									</div>
									<time>
										<?php echo  wp_date( 'd F Y', esc_html(strtotime( $row->date_qustion ) ) );
										?>
										<?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
										<?php
										echo wp_date( 'H:i',  esc_html(strtotime( $row->date_qustion ) ) );
										?>
									</time>
								</div>
								<div class="title_time_ticket">
									<p><?php echo esc_html( $row->subject ); ?></p>
									<time>
										<?php ago_ticket_nirweb( strtotime( esc_html( $row->time_update ) ) ) ?>
									</time>
								</div>
							</a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
			<div class="pagination_ticket_index">
				<?php echo esc_html( $list_ticket[1] ); ?>
			</div>
			<?php
		}
	}
}
