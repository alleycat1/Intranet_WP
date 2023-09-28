
<div id="box_4">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="lifesaver"></span></span>
        <strong><?php echo esc_html__('Ticket', 'nirweb-support') ?></strong>
    </h2>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="require_procut_user_wpyar"><strong><?php echo esc_html__('Make product selection mandatory', 'nirweb-support'); ?></strong></label>
        <div uk-form-custom="target: > * > span:first-child" class="uk-form-custom">
            <select name="require_procut_user_wpyar">
                <option value="0" <?= get_option('require_procut_user_wpyar') == '0' ? 'selected' : '' ?> ><?php echo esc_html__('deactivate', 'nirweb-support'); ?></option>
                <option value="1" <?= get_option('require_procut_user_wpyar') == '1' ? 'selected' : '' ?> ><?php echo esc_html__('active', 'nirweb-support'); ?></option>
            </select>
            <button class="uk-button uk-button-default" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down" class="uk-icon"></span>
            </button>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label"
               for="poshtiban_text_email_send"><strong><?php echo esc_html__('Guidance for customers prior to ticket submission ', 'nirweb-support'); ?></strong></label>
        <div  class="uk-form-custom">
            <?php esc_html(wp_editor(get_option('text_top_send_mail_nirweb_ticket'), 'text_top_send_mail_nirweb_ticket')); ?>
        </div>
    </div>


</div>