<div id="box_5">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="cloud-upload"></span></span>
        <strong><?php echo esc_html__('File', 'nirweb-support') ?></strong>
    </h2>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="size_of_file_wpyartik"><?= esc_html__('Maximum upload volume in MB', 'nirweb-support') ?></label>
        <div class="uk-form-controls">
            <input class="uk-input" type="text" name="size_of_file_wpyartik" id="size_of_file_wpyartik"
                   value="<?= esc_html(get_option('size_of_file_wpyartik')) ?>">
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="size_of_file_wpyartik"><?= esc_html__('Allowed file extensions', 'nirweb-support') ?></label>
        <div class="uk-form-controls">
            <input class="uk-input" type="text" name="mojaz_file_upload_user_wpyar" id="mojaz_file_upload_user_wpyar"
                   value="<?= get_option('mojaz_file_upload_user_wpyar') ? esc_html(get_option('mojaz_file_upload_user_wpyar')) : '.png,.jpg,.jpeg' ?>">
        </div>

        <div class="set_description">
            <i uk-icon="info"></i>
            <?=  esc_html__('Example : .png,.jpg,.jpeg', 'nirweb-support') ?>
        </div>

    </div>


</div>