<div id="box_3_p">
    <h2 class="nwallet_set_title">
        <span class="nwallet_set_title_icon"><span uk-icon="question"></span></span>
        <strong><?php echo esc_html__('PRIORITY FIELD', 'nirweb-support') ?></strong>
        <div class="pro_ver_txt"><i uk-icon="lock"></i> Pro Version</div>
    </h2>


    <div class="uk-margin">

        <label class="uk-form-label pro_ver"
               for="select_page_ticket"><strong><?php echo esc_html__("Priority fields", 'nirweb-support'); ?></strong>

        </label>
        <div class="" uk-sortable>

            <div class="item_rep">
                <label><?php echo esc_html__("Title", 'nirweb-support'); ?></label>
                <input type="text" disabled value="<?php echo esc_html__("Low", 'nirweb-support'); ?>">
                <div class="icons">
                    <i uk-icon="copy"></i>
                    <i uk-icon="minus-circle"></i>
                    <i uk-icon="move"></i>
                </div>
            </div>


            <div class="item_rep">
                <label><?php echo esc_html__("Title", 'nirweb-support'); ?></label>
                <input type="text" disabled value="<?php echo esc_html__("High", 'nirweb-support'); ?>">
                <div class="icons">
                    <i uk-icon="copy"></i>
                    <i uk-icon="minus-circle"></i>
                    <i uk-icon="move"></i>
                </div>
            </div>


            <div class="item_rep">
                <label><?php echo esc_html__("Title", 'nirweb-support'); ?></label>
                <input type="text" disabled value="<?php echo esc_html__("Normal", 'nirweb-support'); ?>">
                <div class="icons">
                    <i uk-icon="copy"></i>
                    <i uk-icon="minus-circle"></i>
                    <i uk-icon="move"></i>
                </div>
            </div>

        </div>


    </div>
    <div style="margin: 5px 0 15px 0;cursor: pointer">
        <i uk-icon="plus-circle"></i>
    </div>


</div>