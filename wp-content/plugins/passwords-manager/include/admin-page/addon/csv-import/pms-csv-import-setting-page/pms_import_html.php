<div class="container-fluid clear">
    <!--Start Dashboard Content-->
    <div class="row">
        <div class="col-md-12">
            <div class="card text-dark bg-light p-0">
                <div class="card-header"><?php echo __('CSV Import','passwords-manager');?> </div>
                <div class="card-body">
                    <form class="form-horizontal" action="" method="post" name="pwdms_form_csv_import" enctype="multipart/form-data" id="pwdms_form_csv_import">
                    <?php wp_nonce_field( 'security_nonce', 'security_nonce' ); ?>
                        <div class="input-row">
                            <?php $encry_key = get_option('pms_encrypt_key'); ?>
                            <button class="button border" title="import_btn" type="button" id="import_btn"> <?php echo __('Choose file','passwords-manager');?> </button>
                            <span class="filename"><?php echo __('No file chosen','passwords-manager');?></span>
                            <input class="d-none" type="file" name="pwdms_csvim_upload_file" title="import_btn" id="pwdms_csvim_upload_file" accept=".csv" />
                            <input type="submit" name="pwdms_import_btn" id="pwdms_import_btn" class="button button-primary ms-3" value="<?php echo __('Import Data','passwords-manager');?>">
                            <input type="hidden" name="setting_key_hdn" id="setting_key_hdn" value="<?php echo esc_html($encry_key);?>">
                        </div>
                        <div id="labelError"></div>
                        <div id="csv_export_progressbar"><div class="progress-label"></div></div>
                    </form>
                    <?php
                    if(!empty($message)){
                        ?>
                            <div class="alert alert-primary" role="alert">
                                    <?php echo esc_html( $message);?>
                            </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card text-dark bg-light p-0">
                <div class="card-header"><?php echo __('Dummy Data Import','passwords-manager');?></div>
                <div class="card-body">
                    <button type="button" class="btn btn-info" id="importdummy"><?php echo __('Import Data','passwords-manager');?></button>
                </div>
            </div>
        </div>
    </div>
</div>