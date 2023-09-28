<div id="box_2">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="user"></span></span>
        <strong><?php echo esc_html__('Support staff notifications', 'nirweb-support') ?></strong>
    </h2>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="active_send_mail_to_poshtiban"><strong><?php echo esc_html__('Enable Sending Email To support agent', 'nirweb-support'); ?></strong></label>
        <div uk-form-custom="target: > * > span:first-child" class="uk-form-custom">
            <select name="active_send_mail_to_poshtiban">
                <option value="0" <?= get_option('active_send_mail_to_poshtiban') == '0' ? 'selected' : '' ?> >
                    <?php echo esc_html__('off', 'nirweb-support'); ?>
                </option>
                <option value="1" <?= get_option('active_send_mail_to_poshtiban') == '1' ? 'selected' : '' ?> >
                    <?php echo esc_html__('on', 'nirweb-support'); ?>
                </option>
            </select>
            <button class="uk-button uk-button-default" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down" class="uk-icon"></span>
            </button>
        </div>
    </div>
    <hr>
    <h3><?php echo esc_html__('Email template ( used when a ticket is sent )', 'nirweb-support'); ?></h3>
    <div class="uk-margin">
        <label class="uk-form-label"
               for="subject_mail_poshtiban_new"><?= esc_html__('Subject', 'nirweb-support') ?></label>
        <div class="uk-form-controls">
            <input class="uk-input" type="text" name="subject_mail_poshtiban_new" id="subject_mail_poshtiban_new"
                   value="<?= esc_html(get_option('subject_mail_poshtiban_new')) ?>">
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="poshtiban_text_email_send"><strong><?php echo esc_html__('Message', 'nirweb-support'); ?></strong></label>
        <div  class="uk-form-custom">
            <?php esc_html(wp_editor(get_option('poshtiban_text_email_send'), 'poshtiban_text_email_send')); ?>
        </div>
        <div class="uk-card uk-card-default uk-card-body   uk-margin">
            <?php ticket_text_var_wpyartick() ?>
        </div>
    </div>

    <hr>
    <h3><?php echo esc_html__('Email template ( used when a ticket is replied. )', 'nirweb-support'); ?></h3>
    <div class="uk-margin">
        <label class="uk-form-label"
               for="subject_mail_poshtiban_answer"><?= esc_html__('Subject', 'nirweb-support') ?></label>
        <div class="uk-form-controls">
            <input class="uk-input" type="text" name="subject_mail_poshtiban_answer" id="subject_mail_poshtiban_answer"
                   value="<?= esc_html(get_option('subject_mail_poshtiban_answer')) ?>">
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="poshtiban_text_email_send_answer"><strong><?php echo esc_html__('Message', 'nirweb-support'); ?></strong></label>
        <div  class="uk-form-custom">
            <?php esc_html(wp_editor(get_option('poshtiban_text_email_send_answer'), 'poshtiban_text_email_send_answer')); ?>
        </div>
        <div class="uk-card uk-card-default uk-card-body   uk-margin">
            <?php ticket_text_var_wpyartick() ?>
        </div>


    </div>

</div>