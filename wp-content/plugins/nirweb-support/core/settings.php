<?php
if (isset($_POST['nirweb_support_op']) && wp_verify_nonce($_POST['nirweb_support_op'], 'nirweb_support_op_act')) {

    foreach ($_POST as $kry => $item) {
        if ($kry != 'nirweb_support_op' || $kry != '_wp_http_referer') {
            update_option($kry, wp_kses_post($item));
        }

    }
}


function ticket_text_var_wpyartick()
{

    echo '<div class ="info_set_text_body">';
    echo '<h2 style="margin-bottom:20px;color:#ff0000">' . esc_html__('Email template placeholders', 'nirweb-support') . '</h2>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('ticket ID', 'nirweb-support') . '</span> <code>{{ticket_id}}</code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('ticket title', 'nirweb-support') . '</span><code>{{ticket_title}}</code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('support agent name', 'nirweb-support') . '</span><code>{{ticket_poshtiban}}</code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('department name', 'nirweb-support') . '</span><code>{{ticket_dep}}</code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('priority', 'nirweb-support') . '</span> <code>{{ticket_pri}}</code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . esc_html__('status', 'nirweb-support') . '</span> <code>{{ticket_stu}}</code></p>';
    echo '</div>';
}
function ticket_text_var_nirwebtick_sms()
{
    echo '<div class ="info_set_text_body">';
    echo '<h2 style="margin-bottom:20px;color:#ff0000">' . __('Email template placeholders', 'nirweb-support') . '</h2>';
    echo '<p><span style="width:120px;display:inline-block">' . __('ticket ID', 'nirweb-support') . '</span> <code class="tooltipe">
        <span class="text">{{ticket_id}}</span>
    </code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . __('title', 'nirweb-support') . ' </span><code class="tooltipe">
        <span class="text">{{ticket_title}}</span>
    </code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . __('support agent Name', 'nirweb-support') . '</span><code class="tooltipe">
        <span class="text">{{ticket_poshtiban}}</span>
    </code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . __('sender Name', 'nirweb-support') . '</span> <code class="tooltipe">
        <span class="text">{{user_name}}</span>
    </code></p>';
    echo '<p><span style="width:120px;display:inline-block">' . __('department', 'nirweb-support') . ' </span><code class="tooltipe">
        <span class="text">{{ticket_dep}}</span>
    </code></p>';
    echo '</div>';
}
?>

<link rel="stylesheet"
      href="<?= NIRWEB_SUPPORT_URL_TICKET ?>assets/settings/uikit<?= is_rtl() ? '-rtl' : '' ?>.min.css">
<link rel="stylesheet" href="<?= NIRWEB_SUPPORT_URL_TICKET ?>assets/settings/settings.css">
<?php if (is_rtl()) { ?>
    <link rel="stylesheet" href="<?= NIRWEB_SUPPORT_URL_TICKET ?>assets/settings/settings-rtl.css">
<?php } ?>
<script src="<?= NIRWEB_SUPPORT_URL_TICKET ?>assets/settings/uikit.min.js"></script>
<script src="<?= NIRWEB_SUPPORT_URL_TICKET ?>assets/settings/uikit-icons.min.js"></script>
<div class="nwallet_war_setting wrap">
    <div class="uk-grid-match uk-grid" uk-grid>
        <div class="nwallet_side_menu uk-width-1-4@m uk-first-column">
            <div class="uk-card uk-card-default uk-card-body">
                <ul class="uk-list uk-list-divider list_tabs_settings">
                    <li class="plugin_name_nwallet">
                        <div class="logo_support">
                            <img src="<?= NIRWEB_SUPPORT_URL_TICKET . '/assets/images/defualt-logo.png' ?>" width="50"
                             alt="nirweb wallet">
                        <div class="">
                            <p><?= esc_html__('nirweb support', 'nirweb-support') ?></p>
                            <small><?= esc_html__('Version', 'nirweb-support') . ' ' . NIRWEB_SUPPORT_TICKET_VER ?></small>
                        </div>
                        </div>
                        <?php 
                        if(get_bloginfo('language') == 'fa-IR'): ?>
                            <a class="premium_support" href="https://nirweb.ir/product/wordpress-ticket-niweb/" ><?= esc_html__('GO PREMIUM', 'nirweb-support') ?></a>
                        <?php else: ?>
                            <a class="premium_support" href="https://nirwp.com/shop/wordpress-plugins/advanced-support-ticket-plugin/" >GO PREMIUM</a>
                        <?php endif; ?>
                    </li>

                    <li class="n_s_ticket_item">
                        <a  class="active" data-toggle="box_1">
                            <i uk-icon="settings"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('General settings', 'nirweb-support') ?></div>
                                <div class="menu_description"><?= esc_html__('Configure General Settings', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>

                     <li class="n_s_ticket_item">
                        <a  data-toggle="box_4">
                            <i uk-icon="lifesaver"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Ticket', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure ticket Settings', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                    <li class="n_s_ticket_item">
                        <a  data-toggle="box_5">
                            <i uk-icon="cloud-upload"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('File', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure file Settings', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>

                   
                  
                    <li class="n_s_ticket_item">
                        <a  data-toggle="box_2">
                            <i uk-icon="user"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Support staff notifications', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure notifications the for support agent', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                    <li class="n_s_ticket_item">
                        <a  data-toggle="box_3">
                            <i uk-icon="happy"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('User notifications', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure notifications for the user', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                     <li class="n_s_ticket_item">
                        <a  data-toggle="box_1_p">
                            <i uk-icon="link"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Payment Links (Pro)', 'nirweb-support') ?></div>
                                <div class="menu_description"><?= esc_html__('Configure Payment Links', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>
                    
                    
                    <li class="n_s_ticket_item">
                        <a  data-toggle="box_2_p">
                            <i uk-icon="question"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Basic Fields (Pro)', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure Basic Fields', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>
                    
                    
                  <li class="n_s_ticket_item">
                            <a  data-toggle="box_1_e">
                                <i uk-icon="question"></i>
                                <div>
                                    <div class="menu_title"><?php echo esc_html__('Extra Fields (Pro)', 'nirweb-support'); ?> </div>
                                    <div class="menu_description"><?= esc_html__('Configure Extra Fields', 'nirweb-support') ?></div>
                                </div>
                            </a>
                    </li>

                    <li class="n_s_ticket_item">
                        <a  data-toggle="box_3_p">
                            <i uk-icon="question"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Priority fields (Pro)', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Add infinite Fields', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                    <li class="n_s_ticket_item">
                        <a  data-toggle="telegram">
                            <i uk-icon="question"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('Telegram (Pro)', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure Telegram settings', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                    <li class="n_s_ticket_item">
                        <a  data-toggle="sms_notification">
                            <i uk-icon="question"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('SMS Notification (Pro)', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('Configure SMS Notifications', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                    <li class="n_s_ticket_item">
                        <a  data-toggle="css_editor_nsp">
                            <i uk-icon="question"></i>
                            <div>
                                <div class="menu_title"><?php echo esc_html__('CUSTOM STYLE (Pro)', 'nirweb-support'); ?></div>
                                <div class="menu_description"><?= esc_html__('CSS editor', 'nirweb-support') ?></div>
                            </div>
                        </a>
                    </li>


                </ul>
            </div>

        </div>
        <div class=" uk-width-3-4@m">
            <div class="uk-card uk-card-default uk-card-body">
                <form id="nirweb-support_op" method="post" class="uk-form-stacked">
                    <form method="post">

                        <div class="war_settings">
                            <div class="content_settings">
                                <?php
                                require_once 'settings/general.php';
                                require_once 'settings/box_1_p.php';
                                require_once 'settings/box_2_p.php';
                                require_once 'settings/box_3_p.php';
                                require_once 'settings/box_e.php';
                                require_once 'settings/support.php';
                                require_once 'settings/user.php';
                                require_once 'settings/ticket.php';
                                require_once 'settings/file.php';
                                require_once 'settings/telegram.php';
                                require_once 'settings/sms_notification.php';
                                require_once 'settings/css.php';
                                ?>

                            </div>
                        </div>


                        <?php wp_nonce_field('nirweb_support_op_act', 'nirweb_support_op'); ?>
                        <button class="uk-button uk-button-danger btn_save_set_nwallet">
                            <span><?= esc_html__('Save Setting', 'nirweb-support') ?></span>
                            <div uk-spinner></div>
                        </button>
                    </form>

                </form>
            </div>
        </div>
    </div>
</div>

