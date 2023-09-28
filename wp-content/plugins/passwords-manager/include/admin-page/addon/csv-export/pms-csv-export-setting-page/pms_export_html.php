<div class="container-fluid clear">
    <!--Start Dashboard Content-->
    <div class="row">
        <div class="col-md-12">
            <div class="card text-dark bg-light p-0">
                <div class="card-header"><?php echo __('CSV Export','passwords-manager');?> </div>
                <div class="card-body">
                    <form id="pwdms_form_csv_export" method="post" action="">
                    <?php wp_nonce_field( 'security_nonce', 'security_nonce' ); ?>
                        <table class="form-table" id="pwdms_csv_export_table">
                            <tbody>
                                <tr>
                                    <th> <?php echo __('Document Title','passwords-manager');?></th>
                                    <td><input type="text" name="pwdms_document_title" id="pwdms_document_title"
                                            value=" <?php echo __('Password Info','passwords-manager');?>"></td>
                                </tr>
                                <tr>
                                    <th> <?php echo __('Category','passwords-manager');?></th>
                                    <td> <?php global $wpdb;                                                                   
                                    $query  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pms_category"); 
                                    $value = json_decode(json_encode($query), True);                                  
                                    if(count($value) == 0){?><p><?php echo __('There are no category to export','passwords-manager');?></p><?php }else{?>
                                        <select name="pwdms_csv_category" id="pwdms_csv_category"
                                            style="display: block;"> <?php 
                                  
                                    if(count($value) > 1){     
                                        $ids = '';                                 
                                       for($i=0; $i<count($value); $i++){
                                        $ids .= $value[$i]['id'].',';
                                       }
                                       $all_id = rtrim($ids,',');
                                       ?> <option value="<?php echo $all_id;?>"> <?php echo __('All','passwords-manager');?></option> <?php } 
                                            foreach ($value as $row) {
                                                $cate_name = ucfirst(esc_html($row['category']));
                                                $cateory_id = absint($row['id']);?> <option
                                                value="<?php echo $cateory_id; ?>"><?php echo $cate_name;?></option>
                                            <?php }?> </select> <?php }
                                    ?> </td>
                                </tr>
                                <tr>
                                    <th> <?php echo __('Show Columns','passwords-manager');?></th>
                                    <td>
                                        <fieldset> <?php
                                        $csv_fields = apply_filters('pwdms_csv_admin_fields', array(
                                            'col_pwdms_name' => __('Name','passwords-manager'),
                                            'col_pwdms_email' => __('Email','passwords-manager'),
                                            'col_pwdms_password' => __('Password','passwords-manager'),
                                            'col_pwdms_url' => __('Url','passwords-manager'), 
                                            'col_pwdms_category' => __('Category','passwords-manager'),
                                            'col_pwdms_category_color' => __('Category Color','passwords-manager'),
                                            'col_pwdms_desc' => __('Note','passwords-manager'), 
                                        ));
                                        $csv_checked_by_default =  apply_filters('pwdms_csv_checked_fields', array(
                                            'col_pwdms_name',
                                            'col_pwdms_email',                                                            
                                        ));                                       

                                        foreach ($csv_fields as $key => $val) {
                                            if (in_array($key, $csv_checked_by_default)) {
                                                $checked = 'checked="checked"';
                                            } else {
                                                $checked = '';
                                            }
                                            ?> <label for="<?php echo esc_attr($key); ?>"
                                                class="pwdms_checkboxes_label">
                                                <input type="checkbox" id="<?php echo esc_attr($key); ?>"
                                                    name="<?php echo esc_attr($key); ?>"
                                                    <?php echo esc_attr($checked); ?>> <?php echo esc_attr($val); ?>
                                            </label> <?php
                                        }
                                        ?> <?php do_action('pwdms_csv_admin_columns'); ?> </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th> <?php echo __('Select All','passwords-manager');?></th>
                                    <td>
                                        <label for="pwdms_select_all_csv" class="pwdms_checkboxes_label">
                                            <input type="checkbox" id="pwdms_select_all_csv"
                                                name="pwdms_select_all_csv">
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="csv_export_progressbar">
                            <div class="progress-label"></div>
                        </div>
                        <p class="submit">
                            <input type="submit" name="pwdms_export_btn" id="pwdms_export_btn"
                                class="button button-primary" value="<?php echo __('Export Data','passwords-manager');?>">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>