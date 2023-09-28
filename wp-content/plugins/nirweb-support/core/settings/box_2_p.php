<div id="box_2_p">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="question"></span></span>
        <strong><?php echo esc_html__('Basic Fields', 'nirweb-support') ?></strong>
        <div class="pro_ver_txt">   <i uk-icon="lock"></i> Pro Version</div>
    </h2>




    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__('Disable product field', 'nirweb-support'); ?></strong>

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
               for="select_page_ticket"><strong><?php echo esc_html__('Disable priority field', 'nirweb-support'); ?></strong>

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








</div>