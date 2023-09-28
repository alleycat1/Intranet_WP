<?php
/**
 * This file contains the details of all the addons.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Addon_List_Content' ) ) {
	/**
	 * MO_LDAP_Addon_List_Content : Class to store the details of addons.
	 */
	class MO_LDAP_Addon_List_Content {

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			define(
				'MO_LDAP_RECOMMENDED_ADDONS',
				maybe_serialize(
					array(

						'DIRECTORY_SYNC'               => array(
							'addonName'        => 'Sync Users LDAP Directory',
							'addonDescription' => 'Synchronize WordPress users with LDAP directory and vice versa. Schedules can be configured for the synchronization to run at a specific time and after a specific interval.',
							'addonPrice'       => '169',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-configure-miniorange-directory-sync-add-on-for-wordpress',
							'addonVideo'       => 'https://www.youtube.com/embed/DqRtOauJjY8',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/sync_users.png',
						),
						'KERBEROS_NTLM'                => array(
							'addonName'        => 'Auto Login (SSO) using Kerberos/NTLM',
							'addonDescription' => 'Enable Auto-login (SSO) for your WordPress website on a domain joined machine.',
							'addonPrice'       => '169',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-kerberos-single-sign-sso',
							'addonVideo'       => 'https://www.youtube.com/embed/JCVWurFle9I',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/auto_login.png',
						),
						'PASSWORD_SYNC'                => array(
							'addonName'        => 'Password Sync with LDAP Server',
							'addonDescription' => 'Synchronize your WordPress profile password with your LDAP user profile.',
							'addonPrice'       => '119',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-password-sync-with-ldap-add-on',
							'addonVideo'       => 'https://www.youtube.com/embed/6XGUvlvjeUQ',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/password_sync.png',
						),
						'PROFILE_PICTURE_SYNC'         => array(
							'addonName'        => 'Profile Picture Sync',
							'addonDescription' => 'Update WordPress user profile picture with the thumbnail photo stored in your Active Directory/ LDAP server.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/configure-miniorange-profile-picture-map-add-on-for-wordpress',
							'addonVideo'       => 'https://www.youtube.com/embed/RL_TJ48kV5w',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/profile_picture.png',
						),
						'LDAP_SEARCH_WIDGET'           => array(
							'addonName'        => 'Search Staff from LDAP Directory',
							'addonDescription' => 'Search/display your directory users on your website using search widget and shortcode.',
							'addonPrice'       => '129',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-miniorange-ldap-search-widget-add-on',
							'addonVideo'       => 'https://www.youtube.com/embed/GEw6dOx7hRo',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/search_staff.png',
						),
						'PAGE_POST_RESTRICTION'        => array(
							'addonName'        => 'Page/Post Restriction',
							'addonDescription' => 'Allows you to control access to your site\'s content (pages/posts) based on LDAP groups/WordPress roles.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/wordpress-page-restriction',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/page_restriction.png',
						),
						'USER_META'                    => array(
							'addonName'        => 'Third Party Plugin User Profile Integration',
							'addonDescription' => 'Update profile information of any third-party plugin with information from LDAP Directory.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-third-party-user-profile-integration-with-ldap-add-on',
							'addonVideo'       => 'https://www.youtube.com/embed/KLKKe4tEiWI',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/third_party.png',
						),
						'CUSTOM_NOTIFICATION_WP_LOGIN' => array(
							'addonName'        => 'Custom Notifications on WordPress Login page',
							'addonDescription' => 'Add/Display customized messages on your WordPress login page.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/custom_notifications.png',
						),
					)
				)
			);

			define(
				'MO_LDAP_THIRD_PARTY_INTEGRATION_ADDONS',
				maybe_serialize(
					array(

						'BUDDYPRESS_PROFILE_SYNC'        => array(
							'addonName'        => 'BuddyPress Profile Integration',
							'addonDescription' => 'Sync your BuddyPress extended user profiles with the attributes present in your Active Directory/LDAP Server. You can also assign users to BuddyPress groups based on their groups memberships in Active Directory',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-miniorange-ldap-buddypress-integration-add-on',
							'addonVideo'       => 'https://www.youtube.com/embed/7itUoIINyTw',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/BuddyPress.png',
						),
						'BUDDYBOSS_PROFILE_INTEGRATION'  => array(
							'addonName'        => 'BuddyBoss Profile Integration',
							'addonDescription' => 'Integration with BuddyBoss to sync extended profile of users with LDAP attributes upon login.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/BuddyPress.png',
						),
						'ULTIMATE_MEMBER_PROFILE_INTEGRATION' => array(
							'addonName'        => 'Ultimate Member Add-On',
							'addonDescription' => 'Using LDAP credentials, login to Ultimate Member and integrate your Ultimate Member User Profile with LDAP attributes.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => 'https://plugins.miniorange.com/guide-to-setup-ultimate-member-login-integration-with-ldap-credentials',
							'addonVideo'       => 'https://www.youtube.com/embed/-d2B_0rDFi0',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/ultimate_member.png',
						),
						'PAID_MEMBERSHIP_PRO_INTEGRATOR' => array(
							'addonName'        => 'Paid Membership Pro Integrator',
							'addonDescription' => 'WordPress Paid Memberships Pro Integrator will map the LDAP Security Groups to Paid Memberships Pro groups.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/paidmembership.png',
						),
						'LDAP_WP_GROUPS_INTEGRATION'     => array(
							'addonName'        => 'WP Groups Plugin Integration',
							'addonDescription' => 'Assign LDAP users to WordPress groups based on their group membership in LDAP Server.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/groups.png',
						),
						'GRAVITY_FORMS_INTEGRATION'      => array(
							'addonName'        => 'Gravity Forms Integration',
							'addonDescription' => 'Populate Gravity Form fields with information from LDAP. You can integrate with unlimited forms.',
							'addonPrice'       => '129',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/gravity_forms.png',
						),
						'MEMBERPRESS_INTEGRATION'        => array(
							'addonName'        => 'MemberPress Plugin Integration',
							'addonDescription' => 'Login to MemberPress protected content with LDAP Credentials.',
							'addonPrice'       => '119',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/memberpress.png',
						),
						'LEARNDASH_ADDON'                => array(
							'addonName'        => 'LearnDash Integration Add-On',
							'addonDescription' => 'Assign users to LearnDash groups based on their groups memberships in Active Directory. You can map any number of LearnDash groups to LDAP/AD groups.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/learndash.png',
						),
						'EMEMBER_INTEGRATION'            => array(
							'addonName'        => 'eMember Plugin Integration',
							'addonDescription' => 'Login to eMember profiles with LDAP Credentials.',
							'addonPrice'       => '119',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/emember.png',
						),
						'WOOCOMMERCE_INTEGRATION'        => array(
							'addonName'        => 'WooCommerce Integration Add-On',
							'addonDescription' => 'Login to your WooCommerce site with LDAP Credentials and integrate your WooCommerce User Profile with LDAP attributes.',
							'addonPrice'       => '149',
							'addonLicense'     => 'ContactUs',
							'addonGuide'       => '',
							'addonVideo'       => '',
							'addonLogo'        => plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/woocommerce.png',
						),
					)
				)
			);

		}

		/**
		 * Function show_addons_content : Display the addon card.
		 *
		 * @param  boolean $is_recommended_addons Check if the addon is selected.
		 * @return string
		 */
		public static function show_addons_content( $is_recommended_addons ) {
			$display_message = '';
			if ( $is_recommended_addons ) {
				$messages = maybe_unserialize( MO_LDAP_RECOMMENDED_ADDONS );
			} else {
				$messages = maybe_unserialize( MO_LDAP_THIRD_PARTY_INTEGRATION_ADDONS );
			}
			echo '<div id="ldap_addon_container" class="mo_ldap_wrapper">';

			foreach ( $messages as $message_key ) {
				if ( '' === $message_key['addonName'] ) {
					echo '<div style="width: 250px;"></div>';
				}
				if ( 'Auto Login (SSO) using Kerberos/NTLM' !== $message_key['addonName'] && '' !== $message_key['addonName'] ) {
					echo '
						<div class="cd-pricing-wrapper-addons">
							<div data-type="singlesite" class="is-visible ldap-addon-box">
							<div class="individual-container-addons" >
								<header class="cd-pricing-header cd-addon-pricing-header">
								<div class="cd-pricing-title" style="height:35px"> <h2 class="addonNameh2" id="addonNameh2" title=' . esc_url( $message_key['addonVideo'] ) . '>' . esc_html( $message_key['addonName'] ) . '</h2>
								</div><br><br>';

					echo '<div class="cd-addon-setup-links" style="margin-right: 3%;" title="' . esc_attr( $message_key['addonName'] ) . '">';
					if ( ! empty( $message_key['addonVideo'] ) ) {
						echo '<a onclick="showAddonPopup(jQuery(this),title)" class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" title="' . esc_url( $message_key['addonVideo'] ) . '" id="addonVideos" href="#addonVideos" style="width:max-content; margin: 1%;"><span class="link-text" style="color: black;">Setup Video</span></a>';
					}
					if ( ! empty( $message_key['addonGuide'] ) ) {
						echo '<a class="dashicons mo-form-links dashicons-book-alt mo_book_icon" href=' . esc_url( $message_key['addonGuide'] ) . ' title="Setup Guide" id="guideLink"  target="_blank" style="width:max-content; margin: 1%;"><span class="link-text" style="color: black; ">Setup Guide</span></a>';
					}
					echo '</div>';

					echo '
						<div class="cd-addon-desc"><h3  class="add-on-subtitle" style="color:#000;font-weight: 500;padding-left:unset;vertical-align: middle;text-align: center;">' . esc_html( $message_key['addonDescription'] ) . '</h3></div><br>
							
									</header>
										<footer class="add-on-card-footer">
										<div class="cd-priceAddon">
											<span class="cd-currency add-ons-currency">$</span>
											<div style="display:inline">
												<span class="cd-value add-ons-value" id="addon2Price" >' . esc_html( $message_key['addonPrice'] ) . ' </span><p style="display:inline;font-size:20px" class="addon2Text" id="addon2Text"> / instance</p></span>
											</div>
										</div>
										<a style="" class="add-on-contact-us" onclick="openSupportForm(\'' . esc_js( $message_key['addonName'] ) . '\')" >Contact Us </a>
									</footer>
								</div>
							</div> </div>';
				}
			}
			echo '</div><br>
	<div hidden id="addonVideoModal" class="mo_ldap_modal" style="margin-left: 26%">
		<div class="moldap-modal-contatiner-contact-us" style="color:black"></div>
			<div class="mo_ldap_modal-content" id="addonVideoPopUp" style="width: 650px; padding:10px;"><br>
			<span id="add_title" style="font-size: 22px;font-weight: bold; display: flex; justify-content: center;"></span><br>
					<div style="display: flex; justify-content: center;">
					<iframe width="560" id="iframeVideo" title="LDAP add-ons" height="315" src="" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br>
					</div>
					<input type="button" style="font-size: medium;display: block;margin: 20px auto 10px;font-weight:500;" name="close_addon_video_modal" id="close_addon_video_modal" class="button button-primary-ldap button-small" value="Close Video" />
			</div>

		</div>
	<script>
	function showAddonPopup(elem, addonSrc){
		setTimeout(function(){
			addonTitle = elem.parent().attr("title");
			jQuery("#iframeVideo").attr("src", addonSrc);
			jQuery("span#add_title").text(addonTitle);
		},200);     
		jQuery("#addonVideoModal").show();
		jQuery("#wp-pointer-5").css("z-index","0");
		}
	jQuery("#close_addon_video_modal").click(function(){
		jQuery("#addonVideoModal").hide();
		jQuery("#iframeVideo").attr("src", "");
	});

	</script>';
			return $display_message;
		}
	}
}
