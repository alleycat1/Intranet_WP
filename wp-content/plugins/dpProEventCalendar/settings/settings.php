<?php 

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('admin_init', 'dpProEventCalendar_register_mysettings'); 


// This function displays the page content for the Settings submenu
function dpProEventCalendar_settings_page() 
{

    global $dpProEventCalendar, $wpdb, $pec_admin;

    $max_upload = (ini_get('upload_max_filesize'));
    $max_upload_kb = (int)(dpProEventCalendar_convertBytes($max_upload) / 1000);

    if(!isset($dpProEventCalendar['year_from']))
        $dpProEventCalendar['year_from'] = 2;
    
    if(!isset($dpProEventCalendar['year_until'])) 
        $dpProEventCalendar['year_until'] = 3;
    
    ?>

    <div class="wrap" style="clear:both;" id="dp_options">

    <h2></h2>

    <form method="post" id="dpProEventCalendar_events_meta" action="options.php" enctype="multipart/form-data">
    <?php settings_fields( 'dpProEventCalendar-group' ); ?>
    <div class="clear"></div>
     <!--end of poststuff --> 
    	
        <div id="dp_ui_content">
        	
            <?php $pec_admin->template_left() ?>
            
            <div id="rightSide">
            	<div id="menu_general_settings">
                    <div class="titleArea">
                        <div class="wrapper">
                            <div class="pec_pageTitle">
                                <h2><?php _e('General Settings','dpProEventCalendar'); ?></h2>
                                <div class="pec_pageSubtitle">
                                    <span></span>
                                </div>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                    
                    <div class="wrapper">
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Purchase Code:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo pec_setting('purchase_code')?>" name='dpProEventCalendar_options[purchase_code]' class="large-text"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Introduce the purchase code to get automatic updates.','dpProEventCalendar'); ?> <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php _e('Where is my purchase code?.','dpProEventCalendar'); ?></a>
                                        <br>
                                        <?php _e('<strong>Note: </strong>Every License is for a single domain, if the same purchase code is used from different domains it will be blocked and won\'t receive automatic updates', 'dpProEventCalendar')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('User Roles:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name='dpProEventCalendar_options[user_roles][]' multiple="multiple" class="multiple">
                                        	<option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                                           <?php 
    									   $user_roles = '';
                                           $editable_roles = get_editable_roles();

    								       foreach ( $editable_roles as $role => $details ) {
    								           $name = translate_user_role($details['name'] );
    								           if(esc_attr($role) == "administrator" || esc_attr($role) == "subscriber") { continue; }
    										   if ( in_array($role, $dpProEventCalendar['user_roles']) ) // preselect specified role
    								               $user_roles .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
    								           else
    								               $user_roles .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
    								       }
    									   echo $user_roles;
    									   ?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the user role that will manage the plugin.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Events Slug:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo pec_setting('events_slug')?>" name='dpProEventCalendar_options[events_slug]' class="large-text"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Introduce the events URL slug. Be sure that there is not any other post type using it already. <br>(Default: pec-events)','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Categories Slug:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo pec_setting('categories_slug')?>" name='dpProEventCalendar_options[categories_slug]' class="large-text"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Introduce the categories URL slug. Be sure that there is not any other post type using it already. <br>(Default: pec_events_category)','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Redirect archive page:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo pec_setting('redirect_archive')?>" name='dpProEventCalendar_options[redirect_archive]' class="large-text"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Introduce a custom url if you want to redirect the default archive page. ('.get_post_type_archive_link(DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE).')') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Email to send emails from:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="text" value="<?php echo pec_setting('wp_mail_from')?>" name='dpProEventCalendar_options[wp_mail_from]' class="large-text" placeholder="wordpress@<?php echo str_replace("www.", "", $_SERVER['HTTP_HOST'])?>"/>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Custom CSS:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <textarea name='dpProEventCalendar_options[custom_css]' rows="10" placeholder=".classname {
        background: #333;
    }"><?php echo pec_setting('custom_css')?></textarea>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Add your custom CSS code.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('RTL (Right-to-left) Support','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo (pec_setting('rtl_support') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[rtl_support]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Add RTL support for the calendars.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Exclude Google Maps JS file?','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo (pec_setting('exclude_gmaps') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[exclude_gmaps]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Check this option if you have a conflict with other plugins related to the Google Maps feature.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Disable URL Rewrite Rules?','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo (pec_setting('disable_rewrite_rules') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[disable_rewrite_rules]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select this option if you have unexpected 404 error pages in your site.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Disable Reminders?','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="checkbox" value="1" <?php echo (pec_setting('disable_reminders') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[disable_reminders]' class="checkbox"/>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Disable booking reminders for all calendars.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Send reminders','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="1" maxlength="2" max="99" value="<?php echo (pec_setting('days_reminders') == "" ? 3 : $dpProEventCalendar['days_reminders'])?>" name='dpProEventCalendar_options[days_reminders]' class="large-text" style="width:50px;" /> <?php _e('Days before the event starts','dpProEventCalendar'); ?>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Amount of days prior to the event to send the reminders.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Terms & Conditions Page','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="dpProEventCalendar_options[terms_conditions]">
                                        	<option value=""></option>
                                            <?php 
    										  $pages = get_pages(); 
    										  foreach ( $pages as $page ) {
    											$option = '<option value="' . $page->ID . '" ' . ($page->ID == pec_setting('terms_conditions') ? 'selected="selected"' : '') . '>';
    											$option .= $page->post_title;
    											$option .= '</option>';
    											echo $option;
    										  }
    										 ?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the Terms & Conditions page for booking events','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Pagination:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="1" maxlength="2" max="99" value="<?php echo (pec_setting('pagination') == "" ? 10 : $dpProEventCalendar['pagination'])?>" name='dpProEventCalendar_options[pagination]' class="large-text" style="width:50px;" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Set the number of items to display per page.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Frontend Editor Rows:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="1" maxlength="2" max="99" value="<?php echo (pec_setting('editor_rows') == "" ? 10 : $dpProEventCalendar['editor_rows'])?>" name='dpProEventCalendar_options[editor_rows]' class="large-text" style="width:50px;" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Set the height of the frontend editor form.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Max image size:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <input type="number" min="1" maxlength="10" max="<?php echo $max_upload_kb?>" value="<?php echo pec_setting('max_file_size')?>" name='dpProEventCalendar_options[max_file_size]' placeholder='<?php echo $max_upload_kb?>' class="large-text" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Set the max size in kb for the image upload in frontend forms. Leave blank to use server limit.','dpProEventCalendar'); ?> <?php _e('Max','dpProEventCalendar'); ?>: <?php echo $max_upload_kb?>kb</div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select option_w">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Year range:','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp" id="div_year_range">
                                        <select name='dpProEventCalendar_options[year_from]'>
                                        <?php for($i = 10; $i >= 1; $i--) {?>
                                            <option value="<?php echo $i?>" <?php echo ($dpProEventCalendar['year_from'] == $i ? 'selected="selected"' : '')?>><?php echo (date('Y') - $i) . ' (-'.$i.')'?></option>
                                        <?php }?>
                                        </select>
                                        &nbsp;
                                        <select name='dpProEventCalendar_options[year_until]'>
                                        <?php for($i = 1; $i <= 10; $i++) {?>
                                            <option value="<?php echo $i?>" <?php echo ($dpProEventCalendar['year_until'] == $i ? 'selected="selected"' : '')?>><?php echo (date('Y') + $i) . ' (+'.$i.')'?></option>
                                        <?php }?>
                                        </select>
                                    </div>
                                    <div class="desc"><?php _e('Set the year range used in the calendar layouts.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <h2 class="subtitle accordion_title" id="settings_maps_title" onclick="showAccordion('div_maps', this);"><?php _e('Google Maps','dpProEventCalendar'); ?></h2>
                        <div id="div_maps" class="pec_admin_accordion">
                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('API Key:','dpProEventCalendar'); ?>
                                    </label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="text" value="<?php echo pec_setting('google_map_key')?>" name='dpProEventCalendar_options[google_map_key]' class="large-text" />
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('Create an API key as per instructions here:.','dpProEventCalendar'); ?>  <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">https://developers.google.com/maps/documentation/javascript/get-api-key</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Google Maps Zoom:','dpProEventCalendar'); ?>
                                        <?php $pec_admin->show_info( __('Higher number means a closer view.','dpProEventCalendar') ); ?>
                                    </label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="number" min="1" maxlength="2" max="99" value="<?php echo (pec_setting('google_map_zoom') == "" ? 10 : $dpProEventCalendar['google_map_zoom'])?>" name='dpProEventCalendar_options[google_map_zoom]' class="large-text" style="width:50px;" />
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('Set the Google Map Zoom number.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Google Map Custom Marker:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input id="dpProEventCalendar_options_map_marker" style="width: 350px;" type="text" class="large-text" name="dpProEventCalendar_options[map_marker]" value="<?php echo pec_setting('map_marker')?>" placeholder="<?php _e('Enter an URL or upload an image to use a custom marker.','dpProEventCalendar'); ?>" />
                                        </div>
                                        <input id="upload_image_button" type="button" class="button-secondary" value="<?php _e('Upload Image','dpProEventCalendar'); ?>" style="width:auto; padding:auto; font-weight:normal;" />
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Default Ubication:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input id="pec_map" style="width: 350px;" type="text" class="large-text" name="dpProEventCalendar_options[map_default_ubication]" value="<?php echo pec_setting('map_default_ubication')?>" placeholder="<?php _e('Enter a city / country to set as the default ubication.','dpProEventCalendar'); ?>" />
                                            <input id="pec_map_lnlat" type="text" name="dpProEventCalendar_options[map_default_latlng]" value="<?php echo pec_setting('map_default_latlng')?>" />
                                             <div class="dp_pec_date_event_map_overlay" onclick="style.pointerEvents='none'" style="height:400px; margin-top: -400px; top: 400px;"></div>
                                             <?php
                                            $map_lat = 0;
                                            $map_lng = 0;

                                            if(pec_setting('map_default_latlng') != "") {
                                                
                                                $map_lnlat = explode(",", $dpProEventCalendar['map_default_latlng']);
                                                $map_lat = $map_lnlat[0];
                                                $map_lng = $map_lnlat[1];
                                            }
                                             ?>
                                            <div id="mapCanvas" data-map-lat="<?php echo $map_lat?>" data-map-lng="<?php echo $map_lng?>" style="height: 400px;"></div>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        
                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_fb_api', this);"><?php _e('Facebook API Keys','dpProEventCalendar'); ?></h2>
                        <div id="div_fb_api" class="pec_admin_accordion">
                            <?php
                            $site_url = get_home_url();
                            $facebook_app_id = pec_setting('facebook_app_id');
                            $facebook_app_secret = pec_setting('facebook_app_secret');
                            $dpProEventCalendar_user_token_options = get_option( 'dpProEventCalendar_user_token_options', array() );
                            $dpProEventCalendar_fb_authorize_user  = get_option( 'dpProEventCalendar_fb_authorize_user', array() );

                            if ( ! isset( $_SERVER['HTTPS'] ) && false === stripos( $site_url, 'https' ) ) { // WPCS: input var okay.
                                ?>
                                <div class="pec_admin_errorCustom">
                                    <p><?php printf( '%1$s <b><a href="https://developers.facebook.com/blog/post/2018/06/08/enforce-https-facebook-login/" target="_blank">%2$s</a></b> %3$s', esc_attr__( "HTTPS is required to authorize your facebook account.", 'dpProEventCalendar' ), esc_attr__( 'Click here', 'dpProEventCalendar' ), esc_attr__( 'for more information.', 'dpProEventCalendar' ) ); ?></p>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="pec_admin_errorCustom">
                                <p>
                                <?php printf( '<b>%1$s</b> %2$s <b><a href="https://developers.facebook.com/apps" target="_blank">%3$s</a></b> %4$s', esc_attr__( 'Note : ', 'dpProEventCalendar' ), esc_attr__( 'If you are not registered as a developer in Facebook, you will have to register in', 'dpProEventCalendar' ), esc_attr__( 'here', 'dpProEventCalendar' ), esc_attr__( 'to create new Facebook application.', 'dpProEventCalendar' ) ); ?>

                                    <br/>
                                    
                                    <strong><?php esc_attr_e( 'Set the site url as :', 'dpProEventCalendar' ); ?> </strong>
                                    <span class="pec_error_featured"><?php echo esc_url( get_site_url() ); ?></span>
                                    <br/>
                                    <strong><?php esc_attr_e( 'Set Valid OAuth redirect URI :', 'dpProEventCalendar' ); ?> </strong>
                                    <span class="pec_error_featured"><?php echo esc_url( admin_url( 'admin-post.php?action=dpProEventCalendar_facebook_authorize_callback' ) ); ?></span>

                                    <br />

                                    <?php _e("If you created the App succesfully, you will see the new App ID and Secret keys in the dashboard", 'dpProEventCalendar');?>
                                </p>
                            </div>


                            <?php
                            if ( ! empty( $facebook_app_id ) && ! empty( $facebook_app_secret ) ) {
                                ?>
                                <h4><?php esc_attr_e( 'Authorize your Facebook Account', 'dpProEventCalendar' ); ?></h4>
                                <div class="fb_authorize">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row">
                                                    <?php esc_attr_e( 'Facebook Authorization', 'dpProEventCalendar' ); ?> :
                                                </th>
                                                <td>

                                                        <?php wp_nonce_field( 'dpProEventCalendar_facebook_authorize_action', 'dpProEventCalendar_facebook_authorize_nonce' ); ?>
                                                        <?php
                                                        $button_value = esc_attr__( 'Authorize', 'dpProEventCalendar' );
                                                        if ( isset( $dpProEventCalendar_user_token_options['authorize_status'] ) && 1 === $dpProEventCalendar_user_token_options['authorize_status'] && isset( $dpProEventCalendar_user_token_options['access_token'] ) && ! empty( $dpProEventCalendar_user_token_options['access_token'] ) ) {
                                                            $button_value = esc_attr__( 'Reauthorize', 'dpProEventCalendar' );
                                                        }
                                                        ?>
                                                        <input type="button" class="button-secondary" onclick="location.href='<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>?action=dpProEventCalendar_facebook_authorize_action'" name="dpProEventCalendar_facebook_authorize" value="<?php echo esc_attr( $button_value ); ?>" />
                                                        <?php
                                                        if ( ! empty( $dpProEventCalendar_fb_authorize_user ) && isset( $dpProEventCalendar_fb_authorize_user['name'] ) && dpProEventCalendar_has_authorized_user_token() ) {
                                                            $fbauthname = sanitize_text_field( $dpProEventCalendar_fb_authorize_user['name'] );
                                                            if ( ! empty( $fbauthname ) ) {
                                                                // translators: %s is user's name.
                                                                printf( esc_attr__( ' ( Authorized as: %s )', 'dpProEventCalendar' ), '<b>' . esc_attr( $fbauthname ) . '</b>' );
                                                            }
                                                        }
                                                        ?>

                                                    <div class="desc">
                                                        <?php esc_attr_e( 'Authorize your facebook account to import events. Use the same account that created the facebook app.', 'dpProEventCalendar' ); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php
                            }
                            ?>



                            <div class="option option-select">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('App ID:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type='text' name='dpProEventCalendar_options[facebook_app_id]' value="<?php echo $facebook_app_id?>"/>
                                            <br>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            
                            <div class="option option-select no_border">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('App Secret:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type='text' name='dpProEventCalendar_options[facebook_app_secret]' value="<?php echo $facebook_app_secret?>"/>
                                            <br>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="desc">
                            <?php esc_attr_e( 'After you save the App ID and Secret key, come back to this page to authorize the app.', 'dpProEventCalendar' ); ?>
                            </div>

                        </div>

                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_fa', this);"><?php _e('Font Awesome','dpProEventCalendar'); ?></h2>
                        
                        <div id="div_fa" class="pec_admin_accordion">

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Disable Font Awesome?','dpProEventCalendar'); ?>
                                        <?php $pec_admin->show_info( __('Font Awesome is a font and icon toolkit.','dpProEventCalendar') ); ?>
                                    </label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="checkbox" value="1" <?php echo (pec_setting('exclude_fa') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[exclude_fa]' class="checkbox"/>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('You can disable Font Awesome library if your theme or another plugin already includes it.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Font Awesome URL:','dpProEventCalendar'); ?>
                                        <?php $pec_admin->show_info( __('Get a font awesome kit in https://fontawesome.com/.','dpProEventCalendar') ); ?>
                                    </label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="text" value="<?php echo pec_setting('fontawesome_url')?>" placeholder="<?php echo DP_PRO_EVENT_CALENDAR_FONT_AWESOME_JS; ?>" name='dpProEventCalendar_options[fontawesome_url]' class="large-text"/>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('If you have created a custom font-awesome url, add it here.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                        </div>
                        
                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_recaptcha', this);"><?php _e('ReCaptcha','dpProEventCalendar'); ?></h2>
    					
                        <div id="div_recaptcha" class="pec_admin_accordion">
                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Enable ReCaptcha for frontend forms','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="checkbox" value="1" <?php echo (pec_setting('recaptcha_enable') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[recaptcha_enable]' class="checkbox"/>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('A captcha will be displayed in some of the frontend forms. Useful to block spam bots.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            
                            <div class="option option-select">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Site Key:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type='text' name='dpProEventCalendar_options[recaptcha_site_key]' value="<?php echo pec_setting('recaptcha_site_key')?>"/>
                                            <br>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            
                            <div class="option option-select no_border">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Secret Key:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type='text' name='dpProEventCalendar_options[recaptcha_secret_key]' value="<?php echo pec_setting('recaptcha_secret_key')?>"/>
                                            <br>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            
                            <p><strong><?php _e('Get the Keys adding your domain in the recaptcha site:','dpProEventCalendar'); ?> <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a></strong></p>
                            
                        </div>
                        
                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_expired', this);"><?php _e('Expired Events','dpProEventCalendar'); ?></h2>
                        
                        <div id="div_expired" class="pec_admin_accordion">

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Remove expired events automatically','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="checkbox" value="1" <?php echo (pec_setting('remove_expired_enable') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[remove_expired_enable]' class="checkbox"/>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('Events which End date field has passed or the Start date has passed and it is not a recurrent event. This will also prevent importing old events.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Status of events','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <select name="dpProEventCalendar_options[remove_expired_status]">
                                                <option value="publish"><?php _e('Only published Events','dpProEventCalendar'); ?></option>
                                                <option value="any" <?php echo ('any' == pec_setting('remove_expired_status') ? 'selected="selected"' : '')?>><?php _e('Events with any status','dpProEventCalendar'); ?></option>
                                            </select>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('Select if you want to remove expired events with published / any status.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Trash / Remove','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <select name="dpProEventCalendar_options[remove_expired_completly]">
                                                <option value="trash"><?php _e('Send to trash','dpProEventCalendar'); ?></option>
                                                <option value="remove" <?php echo ('remove' == pec_setting('remove_expired_completly') ? 'selected="selected"' : '')?>><?php _e('Remove completly','dpProEventCalendar'); ?></option>
                                            </select>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('Select if you want to remove expired events completly or send them to the trash.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>

                            <div class="option option-select option_w">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Expire after:','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="number" min="0" maxlength="3" max="999" value="<?php echo (pec_setting('remove_expired_days') == "" ? 10 : pec_setting('remove_expired_days'))?>" name='dpProEventCalendar_options[remove_expired_days]' class="large-text" style="width:50px;" /> <?php _e('Days','dpProEventCalendar'); ?>
                                            <br>
                                        </div>
                                        <div class="desc"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <!--
                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_landing', this);"><?php _e('Event Landing Page (beta)','dpProEventCalendar'); ?></h2>
    					
                        <div id="div_landing" class="pec_admin_accordion">

                            <div class="option option-select option_w no_border">
                                <div class="option-inner">
                                    <label class="titledesc"><?php _e('Enable Event Landing Page Template','dpProEventCalendar'); ?></label>
                                    <div class="formcontainer">
                                        <div class="forminp">
                                            <input type="checkbox" value="1" <?php echo (pec_setting('event_single_enable') ? "checked='checked'" : "")?> name='dpProEventCalendar_options[event_single_enable]' class="checkbox"/>
                                            <br>
                                        </div>
                                        <div class="desc"><?php _e('The events single pages will be displayed with a different theme.','dpProEventCalendar'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        -->

                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_custom_fields', this);"><?php _e('Event Custom Fields','dpProEventCalendar'); ?></h2>
    					
                        <div id="div_custom_fields" class="pec_admin_accordion">
                            <table class="widefat" cellpadding="0" cellspacing="0" id="custom_fields_list">
                                <thead>
                                    <tr style="cursor:default !important;">
                                        <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Text','dpProEventCalendar'); ?></th>
                                        <th>&nbsp;</th>
                                     </tr>
                                </thead>
                                <tbody>
                                    <tr id="custom_field_new" style="display:none;">
                                    	<input type="hidden" name="dpProEventCalendar_options_replace[custom_fields_counter][]" value="1" />
                                        <td><input type="text" name="dpProEventCalendar_options_replace[custom_fields][id][]" class="pec_custom_field_id" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                        <td><input type="text" name="dpProEventCalendar_options_replace[custom_fields][name][]" class="" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                        <td style="overflow: visible;">
                                        	<select class="dp_pec_custom_field_type" name="dpProEventCalendar_options_replace[custom_fields][type][]">
                                            	<option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                <option value="checkbox"><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                                <option value="multiple_checkbox"><?php _e('Multiple Checkbox','dpProEventCalendar'); ?></option>
                                                <option value="dropdown"><?php _e('Dropdown','dpProEventCalendar'); ?></option>
                                        	</select>
                                        </td>
                                        <td>
                                            <select name="dpProEventCalendar_options_replace[custom_fields][optional][]">
                                                <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                <option value="0"><?php _e('No','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="#" class="dp_pec_custom_field_add_option"><i class="dashicons dashicons-plus"></i></a>
                                            <input type="text" name="dpProEventCalendar_options_replace[custom_fields][placeholder][]" class="" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                        <td>
        									<input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                        </td>
                                    </tr>
                                    <?php
        							if(is_array(pec_setting('custom_fields_counter'))) {
        								$counter = 0;
                                       
        								foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
        								?>
        								<tr>
                                        	<input type="hidden" name="dpProEventCalendar_options[custom_fields_counter][]" value="1" />
        									<td><input type="text" name="dpProEventCalendar_options[custom_fields][id][]" class="pec_custom_field_id" value="<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
        									<td><input type="text" name="dpProEventCalendar_options[custom_fields][name][]" class="" value="<?php echo htmlentities($dpProEventCalendar['custom_fields']['name'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
        									<td style="overflow: visible;">
        										<select class="dp_pec_custom_field_type" name="dpProEventCalendar_options[custom_fields][type][]">
        											<option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                    <option value="checkbox" <?php echo ($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox' ? 'selected="selected"' : '')?>><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                                    <option value="multiple_checkbox" <?php echo ($dpProEventCalendar['custom_fields']['type'][$counter] == 'multiple_checkbox' ? 'selected="selected"' : '')?>><?php _e('Multiple Checkbox','dpProEventCalendar'); ?></option>
                                                    <option value="dropdown" <?php echo ($dpProEventCalendar['custom_fields']['type'][$counter] == 'dropdown' ? 'selected="selected"' : '')?>><?php _e('Dropdown','dpProEventCalendar'); ?></option>
        										</select>
        									</td>
        									<td>
                                                <select name="dpProEventCalendar_options[custom_fields][optional][]">
                                                    <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                    <option value="0" <?php echo ($dpProEventCalendar['custom_fields']['optional'][$counter] == '0' ? 'selected="selected"' : '')?>><?php _e('No','dpProEventCalendar'); ?></option>
                                                </select>
                                            </td>
        									<td>
                                                <a href="#" class="dp_pec_custom_field_add_option" <?php echo ($dpProEventCalendar['custom_fields']['type'][$counter] == 'dropdown' || $dpProEventCalendar['custom_fields']['type'][$counter] == 'multiple_checkbox' ? 'style="display:block;"' : '')?>><i class="dashicons dashicons-plus"></i></a>
                                                <?php
                                                $placeholder =$dpProEventCalendar['custom_fields']['placeholder'][$counter];

                                                if(is_array($placeholder)) 
                                                {

                                                    foreach($placeholder as $key => $value) 
                                                    {

                                                        ?>
                                                        <input type="text" name="dpProEventCalendar_options[custom_fields][placeholder][<?php echo $counter?>][]" class="" value="<?php echo htmlentities($dpProEventCalendar['custom_fields']['placeholder'][$counter][$key])?>" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" />
                                                        <?php

                                                    }

                                                } else {

                                                ?>

                                                <input type="text" name="dpProEventCalendar_options[custom_fields][placeholder][<?php echo $counter?>][]" class="" value="<?php echo htmlentities($dpProEventCalendar['custom_fields']['placeholder'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" />

                                                <?php } ?>
                                            </td>
        									<td>
        										<input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
        									</td>
        								</tr>
        								<?php 
        									$counter++;
        								}
        							}?>
                                </tbody>
                        	</table>

                            <div class="submit">
                                <input type="button" class="button-primary" value="<?php echo __( 'Add New', 'dpProEventCalendar' )?>" onclick="jQuery('#custom_fields_list tbody').append('<tr>'+jQuery('#custom_field_new').html().replace(/dpProEventCalendar_options_replace/g, 'dpProEventCalendar_options')+'</tr>'); jQuery('#custom_fields_list tbody select').selectric('refresh');" />
                            </div>
                        </div>
                                
                        <h2 class="subtitle accordion_title" onclick="showAccordion('div_booking_fields', this);"><?php _e('Booking Extra Fields','dpProEventCalendar'); ?></h2>
                        
                        <div id="div_booking_fields" class="pec_admin_accordion">

                            <table class="widefat" cellpadding="0" cellspacing="0" id="booking_custom_fields_list">
                                <thead>
                                    <tr style="cursor:default !important;">
                                        <th><?php _e('ID','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Name','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Type','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Optional','dpProEventCalendar'); ?></th>
                                        <th><?php _e('Text','dpProEventCalendar'); ?></th>
                                        <th>&nbsp;</th>
                                     </tr>
                                </thead>
                                <tbody>
                                    <tr id="booking_custom_field_new" style="display:none;">
                                        <input type="hidden" name="dpProEventCalendar_options_replace[booking_custom_fields_counter][]" value="1" />
                                        <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][id][]" class="pec_booking_custom_field_id" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                        <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][name][]" class="" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                        <td style="overflow: visible;">
                                            <select name="dpProEventCalendar_options_replace[booking_custom_fields][type][]">
                                                <option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                <option value="checkbox"><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="dpProEventCalendar_options_replace[booking_custom_fields][optional][]">
                                                <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                <option value="0"><?php _e('No','dpProEventCalendar'); ?></option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="dpProEventCalendar_options_replace[booking_custom_fields][placeholder][]" class="" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                        <td>
                                            <input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_booking_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                        </td>
                                    </tr>
                                    <?php
                                    if(is_array(pec_setting('booking_custom_fields_counter'))) {
                                        $counter = 0;
                                        foreach($dpProEventCalendar['booking_custom_fields_counter'] as $key) {
                                        ?>
                                        <tr>
                                            <input type="hidden" name="dpProEventCalendar_options[booking_custom_fields_counter][]" value="1" />
                                            <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][id][]" class="pec_booking_custom_field_id" value="<?php echo $dpProEventCalendar['booking_custom_fields']['id'][$counter]?>" style="width: 100%;" placeholder="<?php _e('Introduce a lower case id wihout spaces.', 'dpProEventCalendar')?>" /></td>
                                            <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][name][]" class="" value="<?php echo htmlentities($dpProEventCalendar['booking_custom_fields']['name'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Name of the Field', 'dpProEventCalendar')?>" /></td>
                                            <td style="overflow: visible;">
                                                <select name="dpProEventCalendar_options[booking_custom_fields][type][]">
                                                    <option value="text"><?php _e('Text Field','dpProEventCalendar'); ?></option>
                                                    <option value="checkbox" <?php echo ($dpProEventCalendar['booking_custom_fields']['type'][$counter] == 'checkbox' ? 'selected="selected"' : '')?>><?php _e('Checkbox','dpProEventCalendar'); ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="dpProEventCalendar_options[booking_custom_fields][optional][]">
                                                    <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
                                                    <option value="0" <?php echo ($dpProEventCalendar['booking_custom_fields']['optional'][$counter] == '0' ? 'selected="selected"' : '')?>><?php _e('No','dpProEventCalendar'); ?></option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="dpProEventCalendar_options[booking_custom_fields][placeholder][]" class="" value="<?php echo htmlentities($dpProEventCalendar['booking_custom_fields']['placeholder'][$counter])?>" style="width: 100%;" placeholder="<?php _e('Text to display in the form', 'dpProEventCalendar')?>" /></td>
                                            <td>
                                                <input type="button" value="<?php _e('Delete','dpProEventCalendar'); ?>" name="delete_booking_booking_custom_field" class="button-secondary" onclick="if(confirm('<?php _e('Are you sure?', 'dpProEventCalendar')?>')) { jQuery(this).closest('tr').remove(); }" />
                                            </td>
                                        </tr>
                                        <?php 
                                            $counter++;
                                        }
                                    } else {
                                        
                                    }?>
                                </tbody>
                            </table>
                            
                            <div class="submit">
        	                    <input type="button" class="button-primary" value="<?php echo __( 'Add New', 'dpProEventCalendar' )?>" onclick="jQuery('#booking_custom_fields_list tbody').append('<tr>'+jQuery('#booking_custom_field_new').html().replace(/dpProEventCalendar_options_replace/g, 'dpProEventCalendar_options')+'</tr>'); jQuery('#booking_custom_fields_list tbody select').selectric('refresh');" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    	
        <p align="right">
    		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

    <script type="text/javascript">
    jQuery(document).on('keyup', '.pec_custom_field_id, .pec_booking_custom_field_id', function() {
    	
    	jQuery(this).val(jQuery(this).val().replace(/ /g, "").toLowerCase());
    	
    });
    </script>

                        
    </div> <!--end of float wrap -->


<?php	
}

function dpProEventCalendar_register_mysettings () 
{
    
    register_setting( 'dpProEventCalendar-group', 'dpProEventCalendar_options', 'dpProEventCalendar_validate' );

}

function dpProEventCalendar_validate( $input ) 
{

	global $dpProEventCalendar;
	
	if(!$input['rtl_support']) 
		$input['rtl_support'] = 0;
	
	if(!$input['exclude_from_search']) 
		$input['exclude_from_search'] = 0;
		
	if(!$input['exclude_gmaps']) 
		$input['exclude_gmaps'] = 0;

    if(!$input['exclude_fa']) 
        $input['exclude_fa'] = 0;

    if(!$input['disable_rewrite_rules']) 
        $input['disable_rewrite_rules'] = 0;

    if(!$input['disable_reminders']) 
        $input['disable_reminders'] = 0;
	
    if(!$input['remove_expired_enable']) 
        $input['remove_expired_enable'] = 0;

    if(!$input['recaptcha_enable']) 
        $input['recaptcha_enable'] = 0;

	if(!$input['event_single_enable']) 
		$input['event_single_enable'] = 0;
		
	if(!$input['paypal_enable']) 
		$input['paypal_enable'] = 0;
		
	if(!$input['paypal_testmode']) 
		$input['paypal_testmode'] = 0;
		
	if(!$input['stripe_enable']) 
		$input['stripe_enable'] = 0;
		
	if(!$input['stripe_testmode']) 
		$input['stripe_testmode'] = 0;
		
	$dpProEventCalendar['custom_fields_counter'] = '';
    $dpProEventCalendar['booking_custom_fields_counter'] = '';
		
	$input = dpProEventCalendar_array_merge( $dpProEventCalendar, $input );
    return $input;
    
}

function dpProEventCalendar_array_merge( $paArray1, $paArray2 )
{
    if ( !is_array( $paArray1 ) or !is_array( $paArray2 ) ) { return $paArray2; }
    foreach ( $paArray2 AS $sKey2 => $sValue2 )
    {
		if( $sKey2 == "user_roles" ) 
			$paArray1[$sKey2] = array(); 	
		
        $paArray1[$sKey2] = dpProEventCalendar_array_merge( @$paArray1[$sKey2], $sValue2 );
    }
    return $paArray1;
}
?>