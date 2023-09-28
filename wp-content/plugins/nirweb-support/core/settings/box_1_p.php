<div id="box_1_p">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="link"></span></span>
        <strong><?php echo esc_html__('Payment Links', 'nirweb-support') ?></strong>
        <div class="pro_ver_txt">   <i uk-icon="lock"></i> Pro Version</div>
    </h2>




    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__('Show a list of paid links', 'nirweb-support'); ?></strong>

        </label>
        <div uk-form-custom="target: > * > span:first-child" class="uk-form-custom">
            <select disabled>
                <option value="0" >
                    <?php echo esc_html__('off', 'nirweb-support'); ?>
                </option>
            </select>
            <button class="uk-button uk-button-default disabled" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down" class="uk-icon"></span>
            </button>
        </div>

    </div>



    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__('Merchant ID', 'nirweb-support'); ?></strong>
         </label>

        <div class="uk-form-controls">
            <input class="uk-input" type="text" disabled>
        </div>

    </div>



    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__('Description of payment gateway', 'nirweb-support'); ?></strong>
         </label>

        <div class="uk-form-controls">
            <input class="uk-input" type="text" disabled>
        </div>

    </div>



    <div class="uk-margin">
        <label class="uk-form-label"
               for="select_page_ticket"><strong><?php echo esc_html__('Select thank you page', 'nirweb-support'); ?></strong></label>
        <div uk-form-custom="target: > * > span:first-child" class="uk-form-custom">
            <select disabled>
                <option value="0"><?php echo esc_html__('Please Select A Page', 'nirweb-support'); ?></option>
            </select>
            <button class="uk-button uk-button-default disabled" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down" class="uk-icon"></span>
            </button>
        </div>
        <div class="set_description">
            <i uk-icon="info"></i>
            <?= esc_html__('short code : [tankyou_paylink]', 'nirweb-support') ?>
        </div>
    </div>




</div>