<?php
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_upload_file.php';
$files = get_list_user_files();
?>
<hr class="wp-header-end">


<h1><?php echo esc_html__( 'List of uploaded files', 'nirweb-support' ); ?></h1>
<div class="wrap">
    <div id="wrapper">
        <div class="base_load_file">
            <form method="post" id="list_all_user_files" name="list_all_user_files[]">

                <table class="wpyt_table">
                    <thead>
                    <tr>
                        <th style="width: 45px"></th>
                        <th style="width: 80px"><?php echo esc_html__( 'File', 'nirweb-support' ); ?></th>
                        <th><?php echo esc_html__( 'Link', 'nirweb-support' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>

					<?php

					foreach ( $files[0] as $row ) :

						?>
                        <tr>
                            <th><input type="checkbox" id="frm_check_items" name="frm_check_items[]"
                                       value="<?php echo esc_html( $row->id ); ?>"
                                       data-file="<?php echo esc_html( $row->file_id ); ?>"></th>
                            <th><img src="<?php echo esc_url_raw( $row->url_file ); ?>" width="50" height="50"></th>
                            <th><a href="<?php echo esc_url_raw( $row->url_file ); ?>"
                                   target="_blank"><?php echo esc_html__( 'view File', 'nirweb-support' ); ?></a></th>
                        </tr>
					<?php endforeach ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th><?php echo esc_html__( 'File ', 'nirweb-support' ); ?></th>
                        <th><?php echo esc_html__( 'Link ', 'nirweb-support' ); ?></th>
                    </tr>
                    </tfoot>
                </table>
                <div class="remove_wpyt font-base">

	                <?php wp_nonce_field( 'admin_del_files_act', 'admin_del_files' ); ?>
                    <button type="submit" class="danger" id="frm_btn_delete_files_users">
						<?php echo esc_html__( 'Delete', 'nirweb-support' ); ?>
                    </button>
                </div>
            </form>
            <div class="nirweb_ticket_pagination">
				<?php echo esc_html( $files[1] ); ?>
            </div>
        </div>
    </div>
</div>

