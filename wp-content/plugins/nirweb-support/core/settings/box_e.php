<div id="box_1_e">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="question"></span></span>
        <strong><?php echo esc_html__('Extra fields', 'nirweb-support'); ?> </strong>
        <div class="pro_ver_txt">   <i uk-icon="lock"></i> Pro Version</div>
    </h2>

    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__('Enable extra fields', 'nirweb-support'); ?> </strong>

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
               for="select_page_ticket"><strong><?php echo esc_html__('Field name', 'nirweb-support'); ?> </strong>
        </label>

        <div class="uk-form-controls">
            <input class="uk-input" type="text" disabled>
        </div>

    </div>


    <div class="uk-margin">
        <label class="uk-form-label"
               for="select_page_ticket"><strong><?php echo esc_html__('Field type', 'nirweb-support'); ?> </strong></label>
        <div uk-form-custom="target: > * > span:first-child" class="uk-form-custom">
            <select disabled>
                <option value="0"><?php echo esc_html__('Text', 'nirweb-support'); ?></option>
            </select>
            <button class="uk-button uk-button-default disabled" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down" class="uk-icon"></span>
            </button>
        </div>
    </div>


</div>