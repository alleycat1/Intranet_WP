<?php
/**
 * This file contains framework used to display miniOrange support Widget.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding Required files.

require_once dirname( __FILE__ ) . '/class-mo-ldap-addon-list-content.php';
require_once dirname( __FILE__ ) . '/class-mo-ldap-local-data-store.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-plugin-constants.php';

/**
 * Function mo_ldap_local_support : Display support widget.
 *
 * @return void
 */
function mo_ldap_local_support() {

	$current_user = wp_get_current_user();
	show_plugin_export_form();
	if ( get_option( 'mo_ldap_local_admin_email' ) ) {
		$admin_email = get_option( 'mo_ldap_local_admin_email' );
	} else {
		$admin_email = $current_user->user_email;
	}

	$server_url = get_option( 'mo_ldap_local_server_url' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_url' ) ) : '';
	?>

	<div class="mo_ldap_support_layout mo_ldap_support_layout1 mo_ldap_local_mt_15" id="mo_ldap_support_layout_ldap" style="width: 100%;">
		<div class="mo_ldap_local_support_header">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/24x7.png' ); ?>" alt="">
			<h3 class="contact-us-24-hrs-heading">Contact Us</h3>
			<div class="contact-us-24-hrs-desc">
				Need any help? We can help you with configuring LDAP configuration. Just send us a query so we can help you.
			</div>
		</div>
		<div class="mo_ldap_local_support_box">
			<form name="f" method="post" action="">
				<div class="mo_ldap_local_support_input_box">
					<table class="mo_ldap_settings_table" aria-hidden="true">
						<tr><td>
							<input type="email" class="mo_ldap_table_textbox mo_ldap_colored-input" id="query_email" name="query_email" value="<?php echo esc_attr( $admin_email ); ?>" placeholder="Enter your email" required />
							</td>
						</tr>
						<tr><td>
							<input type="text" class="mo_ldap_table_textbox mo_ldap_colored-input" name="query_phone" id="query_phone" value="<?php echo esc_attr( get_option( 'mo_ldap_local_admin_phone' ) ); ?>" placeholder="Enter your phone"/>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id="query" name="query" class="mo_ldap_settings_textarea mo_ldap_colored-input" style="border-radius:4px;resize: vertical;width:100%" cols="52" rows="4"  placeholder="Write your query here" required ></textarea>
							</td>
						</tr>
					</table>
				</div>
				<input type="hidden" name="option" value="mo_ldap_login_send_query"/>
				<input type="hidden" id="server_url" value=<?php echo esc_attr( $server_url ); ?>>
				<input type="button" onclick="popupForm()" name="send_query" id="send_query_support" value="Submit Query" style="display: block; margin: 15px auto;font-weight:500;" class="button button-primary-ldap button-large" />
			</form>
		</div>
	</div>
	<div class="call-setup-divbox" style="width: 100%;">
		<div class="mo_ldap_local_setupcall_header">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/call.png' ); ?>" alt="">
			<h3 class="call-setup-heading"> Setup a Call / Screen-share session with miniOrange Technical Team </h3>
		</div>
		<div class="call-setup-div ">
			<form name="f" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_call_setup' ); ?>
				<input type="hidden" name="option" value="mo_ldap_call_setup"/>
				<div id="ldap_call_setup_dets" class="ldap-call-setup-details">
					<div>
						<div style="width: 25%; float:left;"><span style="color:#FF0000">*</span><strong>Timezone:</strong></div>
						<div style="width: 100% !important; float: left;">
							<select id="ldap-js-timezone" name="mo_ldap_setup_call_timezone" class=" mo_ldap_colored-input" style="width:95%;" required>
								<?php $zones = MO_LDAP_Local_Data_Store::$zones; ?>
								<option value="" selected disabled>---------Select your timezone--------</option>
								<?php
								foreach ( $zones as $zone => $value ) {
									if ( strcasecmp( $value, 'Etc/GMT' ) === 0 ) {
										?>
										<option value="<?php echo esc_attr( $zone ) . ' ' . esc_attr( $value ); ?>" selected><?php echo esc_html( $zone ); ?></option>
										<?php
									} else {
										?>
										<option value="<?php echo esc_attr( $zone ) . ' ' . esc_attr( $value ); ?>"><?php echo esc_html( $zone ); ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
					</div>
					<br><br><br>

					<div class="ldap-call-setup-datetime">
						<span style="color:#FF0000">*</span><strong>Date:</strong><br>
						<input type="date" id="datepicker" class="ldap-call-setup-textbox mo_ldap_colored-input" placeholder="Select Meeting Date" autocomplete="off" name="mo_ldap_setup_call_date" required/>
					</div>
					<div class="ldap-call-setup-datetime">
						<span style="color:#FF0000">*</span><strong>Time (24-hour):</strong><br>
						<input type="time" id="ldap-timepicker" value='now' placeholder="Select Meeting Time" class="ldap-call-setup-textbox mo_ldap_colored-input" autocomplete="off" name="mo_ldap_setup_call_time" required/>
					</div> <br><br><br>

					<div class="setup-call-email-div">
					<label for="setup-ldap-call-email" style='cursor:text;font-weight:500;'> Email: </label>
						<input type="email" class="mo_ldap_table_textbox mo_ldap_colored-input" id="setup-call-email" name="setup-call-email" value="<?php echo esc_attr( $admin_email ); ?>" placeholder="Enter your email" style="width:96%;margin-top: 6px;margin-bottom: 6px;" required />
					</div>
					<div class="ldap-call-query-textbox">
						<label for="ldap-call-query" style='cursor:text;'> How may we help you? </label>
						<textarea id="ldap-call-query" name="ldap-call-query" class="mo_ldap_settings_textarea mo_ldap_colored-input" style="border-radius:4px;resize: vertical;width:96%;" cols="52" rows="4" required ></textarea>
					</div>
					<div>
						<p class="ldap-call-setup-notice" style="color:#dc143c; ;">
						<strong><span style="color: #dc143c" > Meeting details will be sent to your email. Please verify the email before submitting the meeting request.    </span></strong>
						</p>
					</div>
				</div>
		</div>
		<br>
		<div style="text-align:center;">
			<input type="submit" name="setup-call-btn" id="setup-call-btn" value="Setup a Call" style="margin-bottom:10%;font-weight:500;" class="button button-primary-ldap button-large" />
		</div>
		</form>
	</div>

	<div class="mo_ldap_support_layout1 mo_ldap_local_mt_15">
		<div class="mo_ldap_local_support_header" style="padding: 5px 0;">
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/logo.png' ); ?>" alt="">
			<h3 style="font-size: 25px; margin-top: 5px;color: white;line-height: 1;">
				Active Directory/LDAP Integration for Cloud & Shared Hosting Platforms
			</h3>
		</div>
		<div class="mo_ldap_local_support_body">
			<p class="mo_ldap_local_cloud_advertisement_heading">Looking for LDAP authentication without enabling PHP LDAP extension?</p>
			<p class="mo_ldap_local_cloud_advertisement_desc">
				This plugin allows you to log in to WordPress sites hosted on shared hosting platforms using Active Directory and LDAP Directory credentials without enabling the PHP LDAP extension.
			</p>
			<div class="mo_ldap_local_adv_buttons_container">
				<div class="mo_ldap_local_adv_buttons">
					<a href="https://wordpress.org/plugins/miniorange-wp-ldap-login/" class="button button-primary-ldap button-large button-quick-links" target="_blank">Download</a>
				</div>
				<div class="mo_ldap_local_adv_buttons">
					<a href="https://plugins.miniorange.com/wordpress-ldap-login-cloud" class="button button-primary-ldap button-large button-quick-links" target="_blank">More Details</a>
				</div>
			</div>

		</div>
	</div>


	<script>
		jQuery(document).ready(function () {
			var day = new Date(),
				hour = day.getHours(),
				minutes = day.getMinutes(),
				currentMonth = day.getMonth() + 1,
				currentDay = day.getDate(),
				year = day.getFullYear();

			if(currentMonth < 10)
				currentMonth = '0' + currentMonth.toString();
			if(currentDay < 10)
				currentDay = '0' + currentDay.toString();
			var maxDate = year + '-' + currentMonth + '-' + currentDay;

			jQuery('#datepicker').attr('value', maxDate);
			jQuery('#datepicker').attr('min', maxDate);

			if(hour < 10) hour = '0' + hour;
			if(minutes < 10) minutes = '0' + minutes;

			jQuery('input[type="time"][value="now"]').each(function(){
				jQuery("#ldap-timepicker").attr({'value': hour + ':' + minutes});
				jQuery("#ldap-timepicker").attr('min', hour + ':' + minutes );

				jQuery('#datepicker').change(function() {
					var selectedDate = jQuery('#datepicker').val();
					if(selectedDate === maxDate)
					{
						jQuery("#ldap-timepicker").attr({'value': hour + ':' + minutes});
						jQuery("#ldap-timepicker").attr('min', hour + ':' + minutes );
					}else{
						jQuery("#ldap-timepicker").attr({'value': '00' + ':' + '00'});
						jQuery("#ldap-timepicker").removeAttr('min');
					}
				});

			});
		});
	</script>
	<script>
		jQuery("#query_phone").intlTelInput();

		function popupForm()
		{
			var wpPointer = document.getElementById("wp-pointer-0");
			if(wpPointer != null){
				wpPointer.style.zIndex = "0";
			}
			var queryEmail = document.getElementById("query_email").value;
			var queryPhone = document.getElementById("query_phone").value;
			var queryValue = document.getElementById("query").value;
			var serverUrl =  document.getElementById("server_url").value;
			if(validateEmail()){
				if(queryValue.length>0)
				{
					if(serverUrl.length>0){
						var mo_ldap_modal = document.getElementById('ldapModal');
						mo_ldap_modal.style.display = "block";
						var span = document.getElementsByClassName("mo_ldap_close")[0];
						document.getElementById("inner_form_email_id").value = queryEmail;
						document.getElementById("inner_form_phone_id").value = queryPhone;
						document.getElementById("inner_form_query_id").value = queryValue;
						span.onclick = function () {
							mo_ldap_modal.style.display = "none";
						}
						window.onclick = function (event) {
							if (event.target == mo_ldap_modal) {
								mo_ldap_modal.style.display = "none";
							}
						}
					}
					else
					{
						document.getElementById("inner_form_email_id").value = queryEmail;
						document.getElementById("inner_form_phone_id").value = queryPhone;
						document.getElementById("inner_form_query_id").value = queryValue;
						document.getElementById('export_configuration_choice').value='';
						document.getElementById('mo_ldap_export_pop_up').submit();
					}
				}
				else
				{
					alert("Query field cannot be empty!");
				}
			}
		}

		function validateEmail()
		{
			var email = document.getElementById('query_email');
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
			{
				return (true)
			}
			else if(email.value.length == 0){
				alert("Please enter your email address!")
				return (false)
			}
			else
			{
				alert("You have entered an invalid email address!")
				return (false)
			}

		}

	</script>
	<?php
}

/**
 * Function show_plugin_export_form : Widget for sending config to miniOrange consent.
 *
 * @return void
 */
function show_plugin_export_form() {
	wp_enqueue_style( 'mo_ldap_admin_plugins_style_settings', plugins_url( 'includes/css/mo_ldap_plugin_style_settings.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
	?>
	</head>
<body>

<div id="ldapModal" class="mo_ldap_modal" style="margin-left: 150px;">
	<div class="moldap-modal-contatiner-contact-us" style="color:black"></div>
	<div class="mo_ldap_modal_content_configuration_support">
		<span class="mo_ldap_close">&times;</span>
		<h3>Send LDAP Configuration? </h3>
		<form name="f" method="post" action="" id="mo_ldap_export_pop_up">
			<input type="hidden" name="option" value="mo_ldap_login_send_query"/>
			<?php wp_nonce_field( 'mo_ldap_login_send_query' ); ?>
			<input type="hidden" id="inner_form_email_id" name="inner_form_email_id" />
			<input type="hidden" id="inner_form_phone_id" name="inner_form_phone_id" />
			<input type="hidden" id="inner_form_query_id" name="inner_form_query_id" />
			<input type="hidden" id="export_configuration_choice" name="export_configuration_choice" value ="yes">
			<div>
				<p>
					<h4>Do you also want to send us your configuration information?</h4>
					<p>It helps us better understand the query and save time.<br>
					<br>Configuration information includes your :
					<br>1. LDAP Directory Server
					<br>2. LDAP Server URL
					<br>3. Username
					<br>4. Search Base
					<br>5. Username Attribute<br>
					<br>NOTE: <strong>No Passwords</strong> (Service Account Password) are <strong>shared</strong> while sending Configuration.
					</p>
					<br><br>
				<div class="mo_ldap_modal-footer" style="text-align: center">
					<input type="submit" name="miniorange_ldap_export_submit" id="miniorange_ldap_export_submit"
							class="button button-primary-ldap button-large" value="Yes"/ >
					<input type="button" name="miniorange_ldap_export_skip"
							class="button button-large" value="No"
							onclick="document.getElementById('export_configuration_choice').value='no';document.getElementById('mo_ldap_export_pop_up').submit();"/>
				</div>
			</div>
		</form>
	</div>
</div>
	<?php
}

/**
 * Function feature_request : Widget to request customized feature in the plugin.
 *
 * @return void
 */
function feature_request() {

	$current_user = wp_get_current_user();
	if ( get_option( 'mo_ldap_local_admin_email' ) ) {
		$admin_email = get_option( 'mo_ldap_local_admin_email' );
	} else {
		$admin_email = $current_user->user_email;
	}
	?>
	<div class="mo_ldap_support_layout mo_ldap_support_layout1" id="mo_ldap_support_layout_ldap_feature_request" style="position: relative; line-height: 2">
		<section class="mo-ldap-contact-form">
			<div class="row">
				<h2 class="mo-ldap-h2">We are happy to hear from you</h2>
				<p class="feature-request-text">
					Looking for some other features? Reach out to us with your requirements and we will get back to you at the earliest.
				</p>                    
			</div>
			<div class="row">
				<form name="feature_request_form" id="feature_request_form" method="post" action="">
					<div class="row">
						<div class="col span-1-of-3 feature-request-labels">
							<label for="query_email">Email:</label>
						</div>
						<div class="col span-2-of-3 feature-request-text-boxes">
							<input type="email" class="mo_ldap_table_textbox" id="query_email" name="query_email" value="<?php echo esc_attr( $admin_email ); ?>" placeholder="Enter your email" required />
						</div>
					</div>
					<div class="row">
						<div class="col span-1-of-3 feature-request-labels">
							<label for="query_phone">Phone:</label>
						</div>
						<div class="col span-2-of-3 feature-request-text-boxes">
							<input type="text" class="mo_ldap_table_textbox" name="query_phone" id="query_phone" value="<?php echo esc_attr( get_option( 'mo_ldap_local_admin_phone' ) ); ?>" placeholder="Enter your phone"/>
						</div>
					</div>
					<div class="row">
						<div class="col span-1-of-3 feature-request-labels">
							<label for="query">Query:</label>
						</div>
						<div class="col span-2-of-3 feature-request-text-boxes">
							<textarea id="query" name="query" class="mo_ldap_settings_textarea" style="border-radius:4px;resize: vertical;width:100%" cols="52" rows="7"  placeholder="Write your custom requirement here" required></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col span-1-of-3"></div>
						<div class="col span-2-of-3">
							<input type="hidden" name="option" value="mo_ldap_login_send_feature_request_query"/>
							<?php wp_nonce_field( 'mo_ldap_login_send_feature_request_query' ); ?>
							<input type="button" onclick="sendFeatureRequest()" name="send_query" id="send_query" value="Request Feature" style="margin-bottom:3%;font-weight:500;" class="button button-primary-ldap button-large" />
						</div>
					</div>
				</form>
			</div>
		</section>
	</div>
	<script>
		jQuery("#query_phone").intlTelInput();


		function sendFeatureRequest() {
			var queryValue = document.getElementById("query").value;

			if (validateEmail()) {
				if (queryValue == "") {
					alert("Please enter your requirement.");
				} 
				else {
					jQuery("#feature_request_form").submit();
				}
			}
		}
		function validateEmail()
		{
			var email = document.getElementById('query_email');
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
			{
				return (true)
			}
			else if(email.value.length == 0){
				alert("Please enter your email address!")
				return (false)
			}
			else
			{
				alert("You have entered an invalid email address!")
				return (false)
			}

		}
	</script>
	<?php
}


/**
 * Function show_addons_list : Display list of all avaiolable LDAP/AD Add-ons.
 *
 * @return void
 */
function show_addons_list() {

	$addons_array            = new MO_LDAP_Addon_List_Content();
	$addon_array_recommended = maybe_unserialize( MO_LDAP_RECOMMENDED_ADDONS );
	$addon_array_third_party = maybe_unserialize( MO_LDAP_THIRD_PARTY_INTEGRATION_ADDONS );
	$addon_array             = array_merge( $addon_array_recommended, $addon_array_third_party );

	$copy_of_addons_array                               = $addon_array['CUSTOM_NOTIFICATION_WP_LOGIN'];
	$addon_array['CUSTOM_NOTIFICATION_WP_LOGIN']        = $addon_array['ULTIMATE_MEMBER_PROFILE_INTEGRATION'];
	$addon_array['ULTIMATE_MEMBER_PROFILE_INTEGRATION'] = $copy_of_addons_array;

	$addon_number = 0;
	foreach ( $addon_array as $addonlist ) {
		$addon_number++;
		$addon_no_links_class = ( $addon_number > 9 ) ? 'mo_ldap_local_no_setup_links' : '';

		if ( is_plugin_active( 'buddypress/bp-loader.php' ) && strcasecmp( $addonlist['addonName'], 'Sync BuddyPress Extended Profiles' ) === 0 ) {
			echo '<div class="mo_ldap_local_addon_card activeddon ">
                        <div class="mo_ldap_local_addon_logo_container">
                            <img class="mo_ldap_local_addons_logo" src=' . esc_url( $addonlist['addonLogo'] ) . ' alt="">
                        </div>
                        <div class="mo_ldap_local_card_content">
                            <h3 class="mo_ldap_local_addon_head">' . esc_html( $addon_number ) . '. ' . esc_html( $addonlist['addonName'] ) . '</p>
                            <p class="mo_ldap_local_addon_body">' . esc_html( $addonlist['addonDescription'] ) . '</h3>
                        </div>';
		} elseif ( strcasecmp( $addonlist['addonName'], '' ) !== 0 ) {
			echo '<div class="mo_ldap_local_addon_card ' . esc_attr( $addon_no_links_class ) . '">
                        <div class="mo_ldap_local_addon_logo_container">
                            <img class="mo_ldap_local_addons_logo" src=' . esc_url( $addonlist['addonLogo'] ) . ' alt="">
                        </div>
                        <div class="mo_ldap_local_card_content">
                            <h3 class="mo_ldap_local_addon_head">' . esc_attr( $addon_number ) . '. ' . esc_attr( $addonlist['addonName'] ) . '</h3>
                            <p class="mo_ldap_local_addon_body">' . esc_attr( $addonlist['addonDescription'] ) . '</p>
                        </div>';
		}

		echo '<div class="mo_ldap_local_links_container">';
		if ( strcasecmp( $addonlist['addonVideo'], '' ) !== 0 ) {
			echo '<div class="individual-addons-popup-container">
						<div id="Add_On_Name" title="' . esc_attr( $addonlist['addonName'] ) . '">
							<a onclick="showAddonPopup_video(jQuery(this),title)" style = "display: inline-flex;width: max-content;cursor:pointer;" class="dashicons mo-video-links dashicons-video-alt3 mo_video_icon" title = ' . esc_attr( $addonlist['addonVideo'] ) . ' id = "VideoIcon"><span class="link-text" >Setup Video </span ></a >
						</div >
					</div>';
		}
		if ( strcasecmp( $addonlist['addonGuide'], '' ) !== 0 ) {
			echo '<div >
                            <a style = "display: inline-flex;width: max-content;" class="dashicons mo-video-links dashicons-book-alt mo_book_icon" href = ' . esc_url( $addonlist['addonGuide'] ) . ' title = "Setup Guide" id = "guideLink" target = "_blank" ><span class="link-text" >Setup Guide </span ></a >
                        </div >';
		}
		echo '</div>
            </div>';
	}
	echo '
	<div  hidden id="addonVideoModal_PopUp" class="mo_ldap_modal" style="margin-left: 26%">
		<div class="moldap-modal-contatiner-contact-us" style="color:black"></div>
			<div class="mo_ldap_modal-content" id="addonVideo_PopUp" style="width: 650px; padding:10px;">
				<span id="add_title_popup" style="font-size: 22px; font-weight: bold; display: flex; justify-content: center;"></span><br><br>
					<div style="display: flex; justify-content: center;">
					<iframe width="560" id="iframeVideo_PopUp" title="LDAP add-ons" height="315" src="" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br>
					</div><br>
					<input type="button" style="font-size: medium; display: block;margin: 20px auto 10px;font-weight:500;" name="close_addon_video_modal_PopUp" id="close_addon_video_modal_PopUp" class="button button-primary-ldap button-small" value="Close Video" />
			</div>
		</div>
	<script>
	function showAddonPopup_video(elem,addonSrc){
		setTimeout(function(){
			addonTitle = elem.parent().attr("title");
			jQuery("#iframeVideo_PopUp").attr("src", addonSrc);
			jQuery("span#add_title_popup").text(addonTitle);
		},200);     
		jQuery("#addonVideoModal_PopUp").show();
		jQuery("#wp-pointer-5").css("z-index","0");
		}
		jQuery("#close_addon_video_modal_PopUp").click(function(){
			jQuery("#addonVideoModal_PopUp").hide();
			jQuery("#iframeVideo_PopUp").attr("src", "");
		});

		</script>';
}

/**
 * Function mo_ldap_local_add_on_page : Display page with all Add-ons List
 *
 * @return void
 */
function mo_ldap_local_add_on_page() {
	?>
		<div id="mo_ldap_small_layout" class="mo_ldap_support_layout mo_ldap_support_layout1" style="position: relative; margin-top: 0; line-height: 2; width: 100%;">
			<h4 style="font-size: 25px; margin: 0px 10px; padding: 10px; text-align: center;">Available Add-ons</h4>
			<div class="mo_ldap_local_addon_container"><?php show_addons_list(); ?></div>
		</div>
	<?php
}

?>
