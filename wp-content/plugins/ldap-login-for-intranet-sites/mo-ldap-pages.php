<?php
/**
 * This file renders the User interface of the plugin.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Adding the required files.
 */

require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-account-details.php';

/**
 * Function get_role_names : Get role names.
 *
 * @return array
 */
function get_role_names() {

	global $wp_roles_object;

	if ( ! isset( $wp_roles_object ) ) {
		$wp_roles_object = new WP_Roles();
	}

	return $wp_roles_object->get_names();
}

/**
 * Function mo_ldap_local_settings : Render the main UI of the plugin.
 *
 * @return void
 */
function mo_ldap_local_settings() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( isset( $_GET['tab'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking if tab name exists, doesn't require nonce verification.
		$active_tab = sanitize_key( $_GET['tab'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking the active tab name, doesn't require nonce verification.
	} else {
		$active_tab = 'default';
	}

	if ( strcmp( $active_tab, 'pricing' ) !== 0 && strcmp( $active_tab, 'add_on' ) !== 0 && strcmp( $active_tab, 'troubleshooting' ) !== 0 && strcmp( $active_tab, 'account' ) !== 0 && strcmp( $active_tab, 'trial_request' ) !== 0 && strcmp( $active_tab, 'ldap_feature_request' ) !== 0 ) {
		?>
		<div class="mo_ldap_local_main_head" >
			<div class="mo_ldap_title_container">
				<div>
					<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/logo.png' ); ?>"  width="50" height="50">
				</div>
				<div class="mo_ldap_local_title">
					miniOrange LDAP/Active Directory Login for Intranet Sites
				</div>
			</div>
			<div style="display:flex; justify-content: flex-end;">
				<a id="ldap_trial_for_premium_plugin" class="button button-large button-request-trial" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'trial_request' ), $request_uri ) ); ?>">Full-Featured Trial</a>
				<a id="license_upgrade" class="button button-primary button-large button-licensing-plans" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( $request_uri ) ) ); ?>">Licensing Plans</a>
			</div>

		</div>
		<?php

		if ( ! MO_LDAP_Utility::is_extension_installed( 'ldap' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<span style="color:#FF0000">Warning: PHP LDAP extension is not installed or disabled.</span>
				<div id="help_ldap_warning_title" class="mo_ldap_title_panel">
					<p><a target="_blank" style="cursor: pointer;">Click here for instructions to enable it.</a></p>
				</div>
				<div hidden="" style="padding: 2px 2px 2px 12px" id="help_ldap_warning_desc" class="mo_ldap_help_desc">
				<ul>
					<li style="font-size: large; font-weight: bold">Step 1 </li>
					<li style="font-size: medium; font-weight: bold">Loaded configuration file : <?php echo esc_attr( php_ini_loaded_file() ); ?></li>
					<li style="list-style-type:square;margin-left:20px">Open php.ini file from above file path</strong></li><br/>
					<li style="font-size: large; font-weight: bold">Step 2</li>
					<li style="font-weight: bold;color: #C31111">For Windows users using Apache Server</li>
					<li style="list-style-type:square;margin-left:20px">Search for <strong>"extension=php_ldap.dll"</strong> in php.ini file. Uncomment this line, if not present then add this line in the file and save the file.</li>
					<li style="font-weight: bold;color: #C31111">For Windows users using IIS server</li>
					<li style="list-style-type:square;margin-left:20px">Search for <strong>"ExtensionList"</strong> in the php.ini file. Uncomment the <strong>"extension=php_ldap.dll"</strong> line, if not present then add this line in the file and save the file.</li>
					<li style="font-weight: bold;color: #C31111">For Linux users</li>
					<ul style="list-style-type:square;margin-left: 20px">
					<li style="margin-top: 5px">Install php ldap extension (If not installed yet)
						<ul style="list-style-type:disc;margin-left: 15px;margin-top: 5px">
							<li>For Ubuntu/Debian, the installation command would be <strong>sudo apt-get -y install php-ldap</strong></li>
							<li>For RHEL based systems, the command would be <strong>yum install php-ldap</strong></li></ul></li>
					<li>Search for <strong>"extension=php_ldap.so"</strong> in php.ini file. Uncomment this line, if not present then add this line in the file and save the file.</li></ul><br/>
					<li style="margin-top: 5px;font-size: large; font-weight: bold">Step 3</li>
					<li style="list-style-type:square;margin-left:20px">Restart your server. After that refresh the "LDAP/AD" plugin configuration page.</li>
					</ul>
					<strong>For any further queries, please contact us.</strong>
				</div>
			<p style="color:black">If your site is hosted on <strong>Shared Hosting</strong> platforms like Bluehost, DreamHost, SiteGround, Flywheel etc and you are not able to enable the extension then you can use our <a href="https://wordpress.org/plugins/miniorange-wp-ldap-login/" target="_blank" rel="noopener" style="cursor: pointer;">Active Directory/LDAP Integration for Cloud & Shared Hosting Platforms</a> plugin.</p>
			</div>
			<?php
		}
		if ( ! MO_LDAP_Utility::is_extension_installed( 'openssl' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
			<p style="color:#FF0000">(Warning: <a target="_blank" rel="noopener" href="http://php.net/manual/en/openssl.installation.php">PHP OpenSSL extension</a> is not installed or disabled)</p>
			</div>
			<?php
		}
	} else {
		?>
		<div style="background-color:#f9f9f9;  display: flex;justify-content: center;position: relative;padding:10px 0;" id="nav-container">
			<div>
				<a style="font-size: 16px; color: #000;text-align: center;text-decoration: none;display: inline-block;"
				<?php
				echo 'href=' . esc_url(
					add_query_arg(
						array(
							'tab'      => 'default',
							'sitetype' => false,
						),
						$request_uri
					)
				);
				?>
				>
					<button id="Back-To-Plugin-Configuration" type="button" value="Back-To-Plugin-Configuration" class="button button-primary-ldap button-large" style="position:absolute;left:10px;font-weight:500;">
						<span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
						Plugin Configuration
					</button> 
				</a> 
			</div>
			<div style="display:block;text-align:center;padding:20px 0; width: 80%;">
				<h2 class="mo_ldap_local_licensing_page_title" style="font-size:20px;text-align: center;">miniOrange LDAP/Active Directory Login for Intranet Sites</h2>
			</div>
		</div>
	<?php } ?>
	<div class="mo2f_container">
		<?php
		if ( strcmp( $active_tab, 'pricing' ) !== 0 && strcmp( $active_tab, 'add_on' ) !== 0 && strcmp( $active_tab, 'troubleshooting' ) !== 0 && strcmp( $active_tab, 'account' ) !== 0 && strcmp( $active_tab, 'trial_request' ) !== 0 && strcmp( $active_tab, 'ldap_feature_request' ) !== 0 ) {
			$check_multisite_message = get_option( 'mo_ldap_local_multisite_message' );
			if ( is_multisite() ) {
				$multisite_msg = 'It seems you have installed WordPress Multisite Environment. ';
			} else {
				$multisite_msg = 'Using a Multisite Environment? ';
			}

			if ( strcmp( $check_multisite_message, 'true' ) !== 0 ) {
				?>
			<div class="modals notice notice-info">
				<div>
					<form method="POST">
						<input type="hidden" name="option" value="mo_ldap_hide_msg">
						<?php wp_nonce_field( 'mo_ldap_hide_msg' ); ?>
						<h4><?php echo esc_attr( $multisite_msg ); ?>
							<a 
								<?php
									echo 'href=' . esc_url(
										add_query_arg(
											array(
												'tab'      => 'pricing',
												'sitetype' => 'multisite',
											),
											$request_uri
										)
									);
								?>
							>Click Here</a> to check our miniOrange LDAP/AD Login For Intranet Sites For Multisite Environment.</h4>
						<input type="submit" name="Close" value="X" style="position: relative; margin-top: -40px;" class="close_local_feedback_form">
					</form>
				</div>
			</div>
				<?php
			}
			?>

	<div class="new-div">
		<div class="nav-new" id="mo_ldap_nav_bar">
			<table class="mo_ldap_local_nav_table">
				<td class="<?php echo strcmp( $active_tab, 'default' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a  href="<?php echo esc_url( add_query_arg( array( 'tab' => 'default' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">LDAP<br>Configuration</div></a> </td>
				<td class="<?php echo strcmp( $active_tab, 'rolemapping' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'rolemapping' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Role<br>Mapping</div></a> </td>
				<td class="<?php echo strcmp( $active_tab, 'attributemapping' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'attributemapping' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Attribute<br>Mapping</div></a> </td>
				<td class="<?php echo strcmp( $active_tab, 'signin_settings' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'signin_settings' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Sign-In<br>Settings</div></a> </td>
				<td class="<?php echo strcmp( $active_tab, 'multiconfig' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'multiconfig' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Multiple<br>Directories</div></a> </td>
				<td  class="<?php echo strcmp( $active_tab, 'config_settings' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'config_settings' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Configuration<br>Settings</div></a> </td>
				<td style="position: relative;" class="<?php echo strcmp( $active_tab, 'users_report' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'users_report' ), $request_uri ) ); ?>"><div style="padding: 6px 0;"">Authentication<br>Report</div></a> </td>
				<td class="<?php echo strcmp( $active_tab, 'addons' ) === 0 ? 'mo_ldap_local_active_tab' : ''; ?>" > <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'addons' ), $request_uri ) ); ?>"><div style="padding: 16px 0;">Add-Ons</div></a> </td>
			</table>
		</div>

		<table class="mo_ldap_local_table" aria-hidden="true">
			<tr>
				<td style="width:74%;vertical-align:top; padding-top: 55px; border-spacing:0;" id="configurationForm">
					<div style="border-top: 4px solid #ff7776; border-radius: 5px;">
						<?php
						if ( strcmp( $active_tab, 'signin_settings' ) === 0 ) {
							mo_ldap_local_signin_settings();
						} elseif ( strcmp( $active_tab, 'multiconfig' ) === 0 ) {
							mo_ldap_local_multiple_ldap();
						} elseif ( strcmp( $active_tab, 'rolemapping' ) === 0 ) {
								mo_ldap_local_rolemapping();
						} elseif ( strcmp( $active_tab, 'attributemapping' ) === 0 ) {
							mo_ldap_show_attribute_mapping_page();
						} elseif ( strcmp( $active_tab, 'config_settings' ) === 0 ) {
							mo_show_export_page();
						} elseif ( strcmp( $active_tab, 'users_report' ) === 0 ) {
							mo_user_report_page();
						} elseif ( strcmp( $active_tab, 'addons' ) === 0 ) {
							mo_ldap_local_add_on_page();
						} else {
							mo_ldap_local_configuration_page();
						}
						?>
					</div>
				</td>
				<?php
				if ( strcmp( $active_tab, 'pricing' ) !== 0 && strcmp( $active_tab, 'addons' ) !== 0 ) {
					?>
					<td style="vertical-align:top;padding-left:1%; border-spacing:0;width: 26%;">
						<div class="mo_ldap_quick_links_container mo_ldap_support_layout1">
							<div class="mo_ldap_local_support_header">
								<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/addon-images/quicklink.png' ); ?>" alt="">
								<h3 class="quick-links-text" style="font-size: 25px; margin-top: 5px;">Quick Links</h3>

							</div>
							<div class="mo_ldap_local_title_btns_container">
								<div class="row">
									<div class="col span-1-of-2">
										<a id="ldap_trial_for_premium_plugin" class="button button-primary-ldap button-large button-quick-links" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'trial_request' ), $request_uri ) ); ?>">Request for Trial</a>
									</div>
									<div class="col span-1-of-2">
										<a id="ldap_feature_request_tab" class="button button-primary-ldap button-large button-quick-links" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'ldap_feature_request' ), $request_uri ) ); ?>">Feature Request</a>
									</div>
								</div>
								<div class="row">
									<div class="col span-1-of-2">
										<a id="ldap_troubleshooting_tab_pointer" class="button button-primary-ldap button-large button-quick-links" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'troubleshooting' ), $request_uri ) ); ?>">FAQ's</a>
									</div>
									<div class="col span-1-of-2">
										<a id="ldap_account_setup_tab_pointer" class="button button-primary-ldap button-large button-quick-links" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'account' ), $request_uri ) ); ?>">My Account</a>
									</div>
								</div>
							</div>
						</div>
						<?php
						mo_ldap_local_support();
						?>
					</td>
				<?php } ?>
			</tr>
		</table>
	</div>
			<?php
		} elseif ( strcmp( $active_tab, 'pricing' ) === 0 || strcmp( $active_tab, 'add_on' ) === 0 ) {
			mo_ldap_show_licensing_page();
		} elseif ( strcmp( $active_tab, 'trial_request' ) === 0 ) {
			mo_ldap_premium_plugin_trial();
		} elseif ( strcmp( $active_tab, 'ldap_feature_request' ) === 0 ) {
			feature_request();
		} elseif ( strcmp( $active_tab, 'troubleshooting' ) === 0 ) {
			mo_ldap_local_troubleshooting();
		} elseif ( strcmp( $active_tab, 'account' ) === 0 ) {
			if ( strcasecmp( get_option( 'mo_ldap_local_verify_customer' ), 'true' ) === 0 ) {
				mo_ldap_show_verify_password_page_ldap();
			} elseif ( ! MO_LDAP_Utility::is_customer_registered() ) {
				mo_ldap_show_new_registration_page_ldap();
			} else {
				mo_ldap_show_customer_details();
			}
		}
		?>

	</div>
	<div class='overlay_back' id="overlay" hidden></div>
	<?php
}

/**
 * Function mo_ldap_show_customer_details : Display my account page.
 *
 * @return void
 */
function mo_ldap_show_customer_details() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	?>
	<div class="mo_ldap_table_layout" >
		<h2>Thank you for registering with miniOrange.</h2>

		<table border="1" aria-hidden="true" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:45%">
			<tr>
				<td style="width:45%; padding: 10px;">miniOrange Account Email</td>
				<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_admin_email' ) ); ?></td>
			</tr>
			<tr>
				<td style="width:45%; padding: 10px;">Customer ID</td>
				<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_admin_customer_key' ) ); ?></td>
			</tr>
		</table>
		<br /><br />

		<table aria-hidden="true">
			<tr>
				<td>
					<form name="f1" method="post" action="" id="mo_ldap_change_account_form">
						<?php wp_nonce_field( 'change_miniorange_account' ); ?>
						<input type="hidden" name="option" value="change_miniorange_account"/>
						<input type="submit" value="Change Account" class="button button-primary-ldap button-large"/>
					</form>
				</td><td>
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( $request_uri ) ) ); ?>"><input type="button" class="button button-primary-ldap button-large" value="Check Licensing Plans"/></a>
				</td>
			</tr>
		</table>

		<br />
	</div>

	<?php
}

/**
 * Function mo_ldap_premium_plugin_trial : Display trial request page.
 *
 * @return void
 */
function mo_ldap_premium_plugin_trial() {
	$current_user = wp_get_current_user();
	if ( get_option( 'mo_ldap_local_admin_email' ) ) {
		$admin_email = get_option( 'mo_ldap_local_admin_email' );
	} else {
		$admin_email = $current_user->user_email;
	}
	?>

	<div style="background-color: #FFFFFF; border: 1px solid #CCCCCC; padding: 10px 10px 10px 10px;">
		<div style="width: 60%;margin: auto;">
			<h3 class="mo-ldap-h2" style="color:black;text-align: center;">Request for Trial</h3>
			<div style="font-size: 1rem;">
				Want to try out the paid features before purchasing the license? Just let us know which plan you're interested in and we will setup a trial for you.
			</div>
			<br/><br/>
			<div class="mo_trial_layout" style="padding-bottom:20px; padding-right:5px;">

				<form method="post" action="">
					<?php wp_nonce_field( 'mo_ldap_trial_request' ); ?>
					<input type="hidden" name="option" value="mo_ldap_trial_request"/>
					<table aria-hidden="true">
						<tr>
							<td style="width: 45%; padding:10px;"><span style="color:#FF0000">*</span><div class="mo_ldap_local_trial_labels">Email :</div class="mo_ldap_local_trial_labels"></td>
							<td><input type="text" name="mo_ldap_trial_email" placeholder="We will use this email to setup the trial for you" required style="width:350px" value="<?php echo esc_attr( $admin_email ); ?>"></td>
						</tr>

						<?php
						$license_plans = array(
							'basic-plan'                 => 'Essential Authentication Plan',
							'kerbores-ntlm'              => 'Kerberos / NTLM SSO Plan',
							'standard-plan'              => 'Advanced Syncing & Authentication Plan',
							'enterprise-plan'            => 'All Inclusive Plan',
							'multisite-basic-plan'       => 'Multisite Essential Authentication Plan',
							'multisite-kerbores-ntlm'    => 'Multisite Kerberos / NTLM SSO Plan',
							'multisite-standard-plan'    => 'Multisite Advanced Syncing & Authentication Plan',
							'enterprise-enterprise-plan' => 'Multisite All Inclusive Plan',
						);
						?>
						<tr>
							<td style="width: 45%; padding:10px;"><span style="color:#FF0000">*</span><div class="mo_ldap_local_trial_labels">Request a trial for :</div class="mo_ldap_local_trial_labels"></td>
							<td><select name="mo_ldap_trial_plan" id="mo_ldap_trial_plan" style="width:350px" required onchange="mo_ldap_show_description();">
									<option hidden disabled selected value="">--Select a license plan--</option>
									<?php
									foreach ( $license_plans as $key => $value ) {
										?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?>
										<?php
									}
									?>
								</select></td>
						</tr>
					</table>
						<div id="premium-video" style="display:none; height: fit-content;margin:25px 15px;">
							<span><div class="mo_ldap_local_trial_labels"><em>You can also check out our premium features in the video below.</em></div class="mo_ldap_local_trial_labels"></span><br><br>
							<table aria-hidden="true" style="width:75%">
								<tr>
									<td>
										<div style="width: fit-content;">
										<a class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" href="https://www.youtube.com/watch?v=r0pnB2d0QP8" title="Premium Plugin Features" style="box-shadow: 9px 4px 6px #888888;width: 70%;" id="videoLink" rel="noopener" target="_blank"><span class="link-text">Premium Plugin Features </span></a></div>
									</td>

									<td>
										<div id="ntlm-sso-video" >
										<a class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" href="https://youtu.be/JCVWurFle9I" title="Auto-login (SSO) Features" style="box-shadow: 9px 4px 6px #888888;width: 70%;"id="videoLink" style="width:170px;" rel="noopener" target="_blank"><span class="link-text">Auto-login (SSO) Features </span></a></div>
									</td>
								</tr>
							</table>
						</div>
						<div id="add-on-list" style="display:none">
							<?php
							$addons_array       = new MO_LDAP_Addon_List_Content();
							$recommended_addons = maybe_unserialize( MO_LDAP_RECOMMENDED_ADDONS );
							$third_party_addons = maybe_unserialize( MO_LDAP_THIRD_PARTY_INTEGRATION_ADDONS );
							$addon_array        = array_merge( $recommended_addons, $third_party_addons );

							$addons = array(
								'directory-sync'          => array(
									'name' => 'Sync Users LDAP Directory',
									'key'  => 'DIRECTORY_SYNC',
								),
								'buddypress-integration'  => array(
									'name' => 'Sync BuddyPress Extended Profiles',
									'key'  => 'BUDDYPRESS_PROFILE_SYNC',
								),
								'password-sync'           => array(
									'name' => 'Password Sync with LDAP Server',
									'key'  => 'PASSWORD_SYNC',
								),
								'profile-picture-map'     => array(
									'name' => 'Profile Picture Sync for WordPress and BuddyPress',
									'key'  => 'PROFILE_PICTURE_SYNC',
								),
								'ultimate-member-login'   => array(
									'name' => 'Ultimate Member Login Integration',
									'key'  => 'ULTIMATE_MEMBER_PROFILE_INTEGRATION',
								),
								'search-staff'            => array(
									'name' => 'Search Staff from LDAP Directory',
									'key'  => 'LDAP_SEARCH_WIDGET',
								),
								'profile-sync'            => array(
									'name' => 'Third Party Plugin User Profile Integration',
									'key'  => 'USER_META',
								),
								'page-post-restriction'   => array(
									'name' => 'Page/Post Restriction',
									'key'  => '',
								),
								'gravity-forms'           => array(
									'name' => 'Gravity Forms Integration',
									'key'  => '',
								),
								'buddypress-group'        => array(
									'name' => 'Sync BuddyPress Groups',
									'key'  => '',
								),

								'memberpress-integration' => array(
									'name' => 'MemberPress Plugin Integration',
									'key'  => '',
								),
								'emember-integration'     => array(
									'name' => 'eMember Plugin Integration',
									'key'  => '',
								),
								'buddyboss-integration'   => array(
									'name' => 'BuddyBoss Profile Integration',
									'key'  => '',
								),
								'directory-search'        => array(
									'name' => 'Directory Search',
									'key'  => '',
								),
								'paid-membership-pro'     => array(
									'name' => 'Paid Membership Pro Integrator',
									'key'  => '',
								),
								'wp-groups'               => array(
									'name' => 'WP Groups Plugin Integration',
									'key'  => '',
								),
								'custom-notifications'    => array(
									'name' => 'Custom Notifications on WordPress Login pag',
									'key'  => '',
								),
							)
							?>
							<p><div class="mo_ldap_local_trial_labels">Select the Add-ons you are interested in (Optional)</div class="mo_ldap_local_trial_labels"></p>
							<div style="width: 600px;">
								<?php
								foreach ( $addons as $key => $value ) {
									?>
									<div class="mo_ldap_local_trial_addon_sections">
										<div>
											<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="true"> 
											<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value['name'] ); ?></label>
										</div>
										<div>
											<?php if ( ! empty( $value['key'] ) ) { ?>
											<a onclick="showAddonTrialPopup_video(jQuery(this),'<?php echo esc_attr( $value['name'] ); ?>',title)" style="min-width:115px !important;padding:8px;" class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" href="#videoLink" title="<?php echo esc_url( $addon_array[ ( $value['key'] ) ]['addonVideo'] ); ?> " id="videoLink" ><span class="link-text">Setup video</span></a>
											<?php } ?>
										</div>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					<table aria-hidden="true">
						<tr>
							<td style="width: 45%; padding:10px;"><div class="mo_ldap_local_trial_labels">Description :</div class="mo_ldap_local_trial_labels"></td>
							<td><textarea id="trial_details" name="mo_ldap_trial_description" style="resize: vertical; width:350px; height:100px;" rows="4" placeholder="Need assistance? Write us about your requirements and we will set up a trial for you."></textarea></td>
						</tr>
						<tr><td style="width: 45%; padding:10px;"><span style="color:#FF0000">*</span><div class="mo_ldap_local_trial_labels">Is your LDAP server publicly accessible? :</div class="mo_ldap_local_trial_labels"></td><td  style="width: 60%;"><input type="radio" name="get_directory_access" value="Yes" required>Yes &nbsp; <input type="radio" name="get_directory_access" value="No">No</td>
						</tr>
						<tr><td><br/></td></tr>
						<tr><td></td><td><input type="submit" style="font-weight:500;" value="Send Request" class="button button-primary-ldap button-large"/></td></tr>
					</table>
				</form>
			</div>
		</div>
	</div>

	<div  hidden id="AddOnVideo_PopUp_modal" name="AddOnVideo_PopUp_modal" class="mo_ldap_modal" style="margin-left: 26%">
		<div class="moldap-modal-contatiner-contact-us" style="color:black"></div>
		<div class="mo_ldap_modal-content" id="addonVideo_PopUp" style="width: 650px; padding:10px;"><br>
			<span id="PopUp_Title" style="font-size: 22px; font-weight: bold; display: flex; justify-content: center;"></span><br>
			<div style="display: flex; justify-content: center;"><iframe width="560" id="iframe_PopUp" height="315" src="" title="LDAP add-ons" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br>
			</div><input type="button" style="font-size: medium;display: block;margin: 20px auto 10px;" name="close_video_modal_PopUp" id="close_video_modal_PopUp" class="button button-primary-ldap button-small" value="Close Video" />
		</div>
	</div>

	<script type="text/javascript">

		function showAddonTrialPopup_video(elem,addonTitle,addonSrc){
			setTimeout(function(){
				jQuery("#iframe_PopUp").attr("src", addonSrc);
				jQuery("span#PopUp_Title").text(addonTitle);
			},200);
			jQuery("#AddOnVideo_PopUp_modal").show();
		}
		jQuery("#close_video_modal_PopUp").click(function(){
			jQuery("#AddOnVideo_PopUp_modal").hide();
			jQuery("#iframe_PopUp").attr("src", "");
		});

		function mo_ldap_show_description() {
			var element = document.getElementById("mo_ldap_trial_plan").selectedIndex;
			var allOptions = document.getElementById("mo_ldap_trial_plan").options;
			if (allOptions[element].index == 0){
				document.getElementById("add-on-list").style.display = "none";
			}else if(allOptions[element].index == 2 || allOptions[element].index == 3 || allOptions[element].index == 5 || allOptions[element].index == 6){
				document.getElementById("add-on-list").style.display = "";
				document.getElementById("premium-video").style.display = "";
				document.getElementById("ntlm-sso-video").style.display = "";
			}
			else {
				document.getElementById("add-on-list").style.display = "";
				document.getElementById("premium-video").style.display = "";
				document.getElementById("ntlm-sso-video").style.display = "none";
			}
		}
	</script>
	<?php
}

/**
 * Function mo_ldap_show_new_registration_page_ldap : Display new registration page.
 *
 * @return void
 */
function mo_ldap_show_new_registration_page_ldap() {
	update_option( 'mo_ldap_local_new_registration', 'true' );
	?>
	<form name="mo_ldap_registration_page" id="mo_ldap_registration_page" method="post" action="">
		<?php wp_nonce_field( 'mo_ldap_local_register_customer' ); ?>
		<input type="hidden" name="option" value="mo_ldap_local_register_customer"/>
		<div class="mo_ldap_table_layout" style="padding:0 20% 5% 20%;">


			<h2 class="mo-ldap-h2">Register with miniOrange</h2>

			<div class="mo_ldap_panel">
				<p style="font-size:16px;"><strong>Why should I register? </strong></p>
				<div id="help_register_desc" style="background: aliceblue; padding: 10px 10px 10px 10px; border-radius: 10px;">
					You should register so that in case you need help, we can help you with step by step
					instructions. We support all known directory systems like Active Directory, OpenLDAP, JumpCloud etc.
					<strong>You will also need a miniOrange account to upgrade to the premium version of the plugins.</strong> We do not store any information except the email that you will use to register with us.
				</div>
				</p>
				<table class="mo_ldap_settings_table" aria-hidden="true">
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000;">*</span>Website/Company:</strong></td>
						<td><input class="mo_ldap_table_textbox" type="text" name="company"
								required placeholder="Company Name"
								value="<?php echo isset( $_SERVER['SERVER_NAME'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ) : ''; ?>"/>
						</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong><span>&nbsp;</span>Telephone Number:</strong></td>
						<td>
							<input class="mo_ldap_table_textbox" type="text" name="register_phone" id="register_phone" placeholder="Enter your phone number" value="<?php echo esc_attr( get_option( 'mo_ldap_local_admin_phone' ) ); ?>"/>
						</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000;">*</span>Email:</strong></td>
						<td>
							<?php
							$current_user = wp_get_current_user();
							if ( get_option( 'mo_ldap_local_admin_email' ) ) {
								$admin_email = get_option( 'mo_ldap_local_admin_email' );
							} else {
								$admin_email = $current_user->user_email;
							}
							?>
							<input class="mo_ldap_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo esc_attr( $admin_email ); ?>"/>
						</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000;">*</span>Password:</strong></td>
						<td><input class="mo_ldap_table_textbox" required type="password"
								name="password" placeholder="Choose your password (Min. length 6)"
								minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
								title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."
							/></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000">*</span>Confirm Password:</strong></td>
						<td><input class="mo_ldap_table_textbox" required type="password"
								name="confirmPassword" placeholder="Confirm your password"
								minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
								title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."
							/></td>
					</tr>
					<tr>
						<td style="font-size:16px;"><span>&nbsp;</span><strong>Use Case:</strong></td>
						<td>
							<textarea style="width: 100%;" rows="5" type="text" name="usecase"
								placeholder="Write about your usecase."
								value=""></textarea>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br><input type="submit" style="font-weight:500;" name="submit" value="Register"
									class="button button-primary-ldap button-large"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" name="mo_ldap_goto_login" id="mo_ldap_goto_login" style="font-weight:500;"
								value="Already have an account?" class="button button-primary-ldap button-large"/>&nbsp;&nbsp;

						</td>

					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style="padding: 50px 0px 10px 0px; font-size: 16px;"><strong style="margin-left: 0; font-weight: 700;">Trouble in registering account? click <a href="https://www.miniorange.com/businessfreetrial" target="_blank">here</a> for more info.</strong></td>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<form name="f1" method="post" action="" id="mo_ldap_goto_login_form">
		<?php wp_nonce_field( 'mo_ldap_goto_login' ); ?>
		<input type="hidden" name="option" value="mo_ldap_goto_login"/>
	</form>
	<script>
		jQuery("#register_phone").intlTelInput();

		jQuery('#mo_ldap_goto_login').click(function () {
			jQuery('#mo_ldap_goto_login_form').submit();
		});
	</script>
	<?php
}


/**
 * Function mo_ldap_show_verify_password_page_ldap : Display account already exist page.
 *
 * @return void
 */
function mo_ldap_show_verify_password_page_ldap() {
	?>
	<form name="mo_ldap_verify_password" id="mo_ldap_verify_password" method="post" action="">
		<?php wp_nonce_field( 'mo_ldap_local_verify_customer' ); ?>
		<input type="hidden" name="option" value="mo_ldap_local_verify_customer"/>
		<div class="mo_ldap_table_layout" style="padding:0 20% 5% 20%;">
			<div id="toggle1" class="panel_toggle">
				<h3 class="mo-ldap-h2">Login with miniOrange</h3>
			</div>
			<div class="mo_ldap_panel">
				<p style="font-size:16px;">It seems you already have an account with miniOrange. Please enter your miniOrange email and password. <a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword" rel="noopener">Click here if you forgot your password?</a></p>
				<br/>
				<table class="mo_ldap_settings_table" aria-hidden="true">
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000">*</span>Email:</strong></td>
						<td><input class="mo_ldap_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo esc_attr( get_option( 'mo_ldap_local_admin_email' ) ); ?>"/></td>

					</tr>
					<tr>
						<td style="font-size:16px;"><strong><span style="color:#FF0000">*</span>Password:</strong></td>
						<td><input class="mo_ldap_table_textbox" required type="password"
								name="password" placeholder="Enter your password"
								minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
								title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."
							/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="submit" value="Login"
								class="button button-primary-ldap button-large"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" name="mo_ldap_goback" id="mo_ldap_goback" value="Back"
								class="button button-primary-ldap button-large"/>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<form name="f" method="post" action="" id="mo_ldap_goback_form">
		<?php wp_nonce_field( 'mo_ldap_local_cancel' ); ?>
		<input type="hidden" name="option" value="mo_ldap_local_cancel"/>
	</form>
	<script>
		jQuery('#mo_ldap_goback').click(function () {
			jQuery('#mo_ldap_goback_form').submit();
		});
	</script>
	<?php
}


/**
 * Function mo_ldap_local_account_page : Display local account page.
 *
 * @return void
 */
function mo_ldap_local_account_page() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	?>

			<div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; width:98%;height:344px">
				<div>
					<h4>Thank You for registering with miniOrange.</h4>
					<h3>Your Profile</h3>
					<table border="1" aria-hidden="true" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:45%">
						<tr>
							<td style="width:45%; padding: 10px;">Username/Email</td>
							<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_admin_email' ) ); ?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Customer ID</td>
							<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_admin_customer_key' ) ); ?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">API Key</td>
								<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_admin_api_key' ) ); ?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Token Key</td>
							<td style="width:55%; padding: 10px;"><?php echo esc_attr( get_option( 'mo_ldap_local_customer_token' ) ); ?></td>
						</tr>
					</table>
					<br/>
					<p><a href="#mo_ldap_local_forgot_password_link">Click here</a> if you forgot your password to your miniOrange account.</p>
				</div>
			</div>

			<form id="forgot_password_form" method="post" action="">
				<?php wp_nonce_field( 'reset_password' ); ?>
				<input type="hidden" name="option" value="reset_password" />
			</form>

			<script>
				jQuery('a[href="#mo_ldap_local_forgot_password_link"]').click(function(){
					jQuery('#forgot_password_form').submit();
				});
			</script>
			<?php
			if ( ! empty( sanitize_text_field( wp_unslash( $_POST['option'] ) ) ) && ( ( strcasecmp( sanitize_text_field( wp_unslash( $_POST['option'] ) ), 'mo_ldap_local_verify_customer' ) === 0 && check_admin_referer( 'mo_ldap_local_verify_customer' ) ) || ( strcasecmp( sanitize_text_field( wp_unslash( $_POST['option'] ) ), 'mo_ldap_local_register_customer' ) === 0 && check_admin_referer( 'mo_ldap_local_register_customer' ) ) ) ) {
				?>
				<script>
					window.location.href = "<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), $request_uri ) ); ?>";
				</script>
				<?php
			}
}

/**
 * Function: mo_ldap_local_link : Display local links.
 *
 * @return void
 */
function mo_ldap_local_link() {

	?>
	<a href="http://miniorange.com/wordpress-ldap-login" style="display:none;">Login to WordPress using LDAP</a>
	<a href="http://miniorange.com/cloud-identity-broker-service" style="display:none;">Cloud Identity broker service</a>
	<a href="http://miniorange.com/strong_auth" style="display:none;"></a>
	<a href="http://miniorange.com/single-sign-on-sso" style="display:none;"></a>
	<a href="http://miniorange.com/fraud" style="display:none;"></a>
	<?php
}

/**
 * Function: mo_ldap_local_configuration_page : Display LDAP configuration page.
 *
 * @return void
 */
function mo_ldap_local_configuration_page() {
	$request_uri              = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$directory_server_value   = ! empty( get_option( 'mo_ldap_directory_server_value' ) ) ? get_option( 'mo_ldap_directory_server_value' ) : '';
	$server_url               = ( get_option( 'mo_ldap_local_server_url' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_url' ) ) : '' );
	$ldap_server_protocol     = ( get_option( 'mo_ldap_local_ldap_protocol' ) ? get_option( 'mo_ldap_local_ldap_protocol' ) : 'ldap' );
	$ldap_server_address      = get_option( 'mo_ldap_local_ldap_server_address' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_ldap_server_address' ) ) : '';
	$ldap_server_port_number  = ( get_option( 'mo_ldap_local_ldap_port_number' ) ? get_option( 'mo_ldap_local_ldap_port_number' ) : '389' );
	$ldaps_server_port_number = ( get_option( 'mo_ldap_local_ldaps_port_number' ) ? get_option( 'mo_ldap_local_ldaps_port_number' ) : '636' );

	$dn             = ( get_option( 'mo_ldap_local_server_dn' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_dn' ) ) : '' );
	$admin_password = ( get_option( 'mo_ldap_local_server_password' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_server_password' ) ) : '' );
	$search_base    = ( get_option( 'mo_ldap_local_search_base' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) ) : '' );

	$mo_ldap_local_server_url_status = get_option( 'mo_ldap_local_server_url_status' ) ? get_option( 'mo_ldap_local_server_url_status' ) : '';
	if ( ! MO_LDAP_Utility::check_empty_or_null( $server_url ) ) {
		if ( strcasecmp( $mo_ldap_local_server_url_status, 'VALID' ) === 0 ) {
			$mo_ldap_local_server_url_status = 'mo_ldap_input_success';
		} elseif ( strcasecmp( $mo_ldap_local_server_url_status, 'INVALID' ) === 0 ) {
			$mo_ldap_local_server_url_status = 'mo_ldap_input_error';
		}
	}

	$mo_ldap_local_service_account_status = get_option( 'mo_ldap_local_service_account_status' ) ? get_option( 'mo_ldap_local_service_account_status' ) : '';
	if ( strcasecmp( $mo_ldap_local_service_account_status, 'VALID' ) === 0 ) {
		$mo_ldap_local_service_account_status = 'mo_ldap_input_success';
	} elseif ( strcasecmp( $mo_ldap_local_service_account_status, 'INVALID' ) === 0 ) {
		$mo_ldap_local_service_account_status = 'mo_ldap_input_error';
	}

	$mo_ldap_local_user_mapping_status = get_option( 'mo_ldap_local_user_mapping_status' ) ? get_option( 'mo_ldap_local_user_mapping_status' ) : '';
	if ( strcasecmp( $mo_ldap_local_user_mapping_status, 'VALID' ) === 0 ) {
		$mo_ldap_local_user_mapping_status = 'mo_ldap_input_success';
	} elseif ( strcasecmp( $mo_ldap_local_user_mapping_status, 'INVALID' ) === 0 ) {
		$mo_ldap_local_user_mapping_status = 'mo_ldap_input_error';
	}

	$mo_ldap_local_username_status = get_option( 'mo_ldap_local_username_status' ) ? get_option( 'mo_ldap_local_username_status' ) : '';
	if ( strcasecmp( $mo_ldap_local_username_status, 'VALID' ) === 0 ) {
		$mo_ldap_local_username_status = 'mo_ldap_input_success';
	} elseif ( strcasecmp( $mo_ldap_local_username_status, 'INVALID' ) === 0 ) {
		$mo_ldap_local_username_status = 'mo_ldap_input_error';
	}
		delete_option( 'mo_ldap_local_username_status' );

	$mo_ldap_local_pass_status = get_option( 'mo_ldap_local_password_status' ) ? get_option( 'mo_ldap_local_password_status' ) : '';
	if ( strcasecmp( $mo_ldap_local_pass_status, 'VALID' ) === 0 ) {
		$mo_ldap_local_pass_status = 'mo_ldap_input_success';
	} elseif ( strcasecmp( $mo_ldap_local_pass_status, 'INVALID' ) === 0 ) {
		$mo_ldap_local_pass_status = 'mo_ldap_input_error';
	}
		delete_option( 'mo_ldap_local_password_status' );

	$mo_ldap_local_ldap_username_attribute = get_option( 'mo_ldap_local_username_attribute' );
	?>

		<div class="mo_ldap_small_layout" style="margin-top:0px;">
			<form id="mo_ldap_connection_info_form" name="f" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_local_save_config' ); ?>
				<input id="mo_ldap_local_connection_configuration_form_action" type="hidden" name="option" value="mo_ldap_local_save_config" />
				<input id="mo_ldap_local_ldap_server_port_no" type="hidden" name="mo_ldap_local_ldap_server_port_no" value="<?php echo esc_attr( $ldap_server_port_number ); ?>" />
				<input id="mo_ldap_local_ldaps_server_port_no" type="hidden" name="mo_ldap_local_ldaps_server_port_no" value="<?php echo esc_attr( $ldaps_server_port_number ); ?>" />
				<div>
					<a class="button button-large button-next" style="float: right;margin: 2px 13px;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'rolemapping ' ), htmlentities( $request_uri ) ) ); ?>">Next ❯ </a>
				</div>

				<h3 class="mo_ldap_left">LDAP Connection Information</h3>
				<div class="mo_ldap_panel">
					<p class="check-out-guides-title">Check our Premium features and find out how to get the configuration done through the videos and guides below. </p>
					<table style="margin-left: auto;  margin-right: auto;" aria-hidden="true"><tr>
						<div class="form_links_container">
							<div class="setup-guides-buttons-div">
								<a class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" href="https://youtu.be/5DUGgP-Hf-k" title="LDAP/AD Plugin Setup" id="videoLink" target="_blank" rel="noopener"><span class="link-text">LDAP/AD Plugin Setup</span></a>
							</div>
							<div class="setup-guides-buttons-div">
								<a class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" href="https://www.youtube.com/watch?v=r0pnB2d0QP8" title="Premium Plugin Features" id="videoLink" target="_blank" rel="noopener"><span class="link-text">Premium Plugin Features</span></a>
							</div>
							<div class="setup-guides-buttons-div">
								<a class="dashicons mo-form-links dashicons-book-alt mo_book_icon" href="https://plugins.miniorange.com/step-by-step-guide-for-wordpress-ldap-login-plugin" title="Setup LDAP/AD plugin" id="guideLink" rel="noopener" target="_blank"><span class="link-text">Setup LDAP/AD plugin</span></a>
							</div>
							<div class="setup-guides-buttons-div">
								<a class="dashicons mo-form-links dashicons-book-alt mo_book_icon" href="https://www.miniorange.com/guide-to-setup-ldaps-on-windows-server" title="Setup LDAPS connection" id="guideLink" target="_blank" rel="noopener"><span class="link-text">Setup LDAPS connection</span></a>
							</div>
						</div>
					</table>
					<p class="check-out-guides-note-para"><span class="check-out-guides-note">NOTE: &nbsp;&nbsp; </span> You need to find out the values for the below given fields from your LDAP Administrator.</p>
					<table class="mo_ldap_settings_table" aria-hidden="true">
						<tr>
							<td>
								<span style="color: red">*</span><strong>Select Your Directory Server:</strong></td><td><span id="mo_ldap_directory_servers" style="position: relative;">
									<select name="mo_ldap_directory_server_value" id="mo_ldap_directory_server_value" onchange="mo_ldap_show_custom_directory()" required>
										<option value="">Select</option>
										<option value="msad" 
										<?php
										if ( strcmp( $directory_server_value, 'msad' ) === 0 ) {
											echo 'selected';}
										?>
										>Microsoft Active Directory</option>
										<option value="openldap" 
										<?php
										if ( strcmp( $directory_server_value, 'openldap' ) === 0 ) {
											echo 'selected';}
										?>
										>OpenLDAP</option>
										<option value="freeipa" 
										<?php
										if ( strcmp( $directory_server_value, 'freeipa' ) === 0 ) {
											echo 'selected';}
										?>
										>FreeIPA</option>
										<option value="jumpcloud" 
										<?php
										if ( strcmp( $directory_server_value, 'jumpcloud' ) === 0 ) {
											echo 'selected';}
										?>
										>JumpCloud</option>
										<option value="other" 
										<?php
										if ( strcmp( $directory_server_value, 'other' ) === 0 ) {
											echo 'selected';}
										?>
										>Other</option>
									</select>
								</span>
							</td>
						</tr>
						<?php
						if ( strcmp( $directory_server_value, 'other' ) === 0 ) {
							?>
							<tr><td></td><td><input class="mo_ldap_table_textbox" style="width: 65%;" type="text" id="mo_ldap_directory_server_custom_value" name="mo_ldap_directory_server_custom_value"  placeholder="Enter your directory name"  value="<?php echo esc_attr( get_option( 'mo_ldap_directory_server_custom_value' ) ); ?>"></td></tr>

							<?php
						} else {
							?>
							<tr><td></td><td><input class="mo_ldap_table_textbox" style="width: 65%;display: none;" type="text" id="mo_ldap_directory_server_custom_value" name="mo_ldap_directory_server_custom_value"  placeholder="Enter your directory name"  value="<?php echo esc_attr( get_option( 'mo_ldap_directory_server_custom_value' ) ); ?>"></td></tr>
							<?php
						}
						?>

						<tr>
						<tr><td>&nbsp;</td></tr>
						<script type="text/javascript">

							function mo_ldap_show_custom_directory() {
								var element = document.getElementById("mo_ldap_directory_server_value").selectedIndex;
								var allOptions = document.getElementById("mo_ldap_directory_server_value").options;
								if (allOptions[element].index == 5){
									document.getElementById("mo_ldap_directory_server_custom_value").style.display = "";
								} else {
									document.getElementById("mo_ldap_directory_server_custom_value").style.display = "none";
								}
								selected_value = allOptions[element].value
								document.getElementById("ldap_username_attribute").innerHTML = "";
								var html_content = "";
								if(selected_value == 'openldap' || selected_value == 'freeipa' ){
									html_content += "<option value='uid' selected>uid</option>";
									html_content += "<option value='samaccountname' >sAMAccountName</option>";
									html_content += "<option value='userprincipalname' >userPrincipalName</option>";
									html_content += "<option value='cn' >cn</option>";
									html_content += "<option value='custom_ldap_attribute' >Provide custom LDAP attribute name</option>";

								}
								else{
									html_content += "<option value='samaccountname' selected>sAMAccountName</option>";
									html_content += "<option value='userprincipalname' >userPrincipalName</option>";
									html_content += "<option value='cn' >cn</option>";
									html_content += "<option value='custom_ldap_attribute' >Provide custom LDAP attribute name</option>";
								}
								document.getElementById("ldap_username_attribute").innerHTML = html_content;
							}
						</script>

						<tr>
							<td style="width: 24%"><strong><span style="color:#FF0000">*</span>LDAP Server:</strong></td>
							<td style="float:left;width: 10%;padding-right: 10px;"><div id="ldap_server_url_pointer" style="position: relative;border-radius: 10px;"> 
									<select style="width: 100%;" name="mo_ldap_protocol" id="mo_ldap_protocol" >
										<?php if ( strcmp( $ldap_server_protocol, 'ldap' ) === 0 ) { ?>
										<option value="ldap" selected>ldap</option>
											<option value="ldaps">ldaps</option>
										<?php } elseif ( strcmp( $ldap_server_protocol, 'ldaps' ) === 0 ) { ?>
											<option value="ldap">ldap</option>
										<option value="ldaps" selected>ldaps</option>
										<?php } ?>
									</select></div></td>
							<td style="float: left;width:60%;padding-right: 3%;"><div id="mo_ldap_directory_server_url" style="position: relative;width:100%;"><input class="mo_ldap_table_textbox mo_ldap_local_ad_url_input <?php echo esc_attr( $mo_ldap_local_server_url_status ); ?>" type="text" id="ldap_server" name="ldap_server" style="width: 100%;" required placeholder="LDAP Server hostname or IP address" value="<?php echo esc_attr( $ldap_server_address ); ?>" /></div></td>
							<td style="width: 10%; float: left;"><div id="mo_ldap_server_port_number_div" style="position: relative;border-radius: 10px">
									<?php if ( strcmp( $ldap_server_protocol, 'ldap' ) === 0 ) { ?>
									<input type="number" id="mo_ldap_server_port_no" style="width: 100%; text-align: center;" name="mo_ldap_server_port_no" required placeholder="port number" value="<?php echo esc_attr( $ldap_server_port_number ); ?>" />
									<?php } elseif ( strcmp( $ldap_server_protocol, 'ldaps' ) === 0 ) { ?>
									<input type="number" id="mo_ldap_server_port_no" style="width: 100%; text-align: center;" name="mo_ldap_server_port_no" required placeholder="port number" value="<?php echo esc_attr( $ldaps_server_port_number ); ?>" />
									<?php } ?>
								</div></td>
						</tr>
						<tr><td></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>Select ldap or ldaps from the above dropdown list. Specify the host name for the LDAP server in the above text field. Edit the port number if you have custom port number.</em>
							</td>
						</tr>
					</table>
					<table style="width: 100%" aria-hidden="true">
						<tr><td style="width: 24%"></td></tr>
						<tr>
							<td style="width: 24%"><strong><span style="color:#FF0000">*</span>Username:</strong></td>
							<td><div id="ldap_server_username" style="position: relative;width: 70%;" ><input class="mo_ldap_table_textbox <?php echo esc_attr( $mo_ldap_local_service_account_status ); ?>" type="text" id="dn" name="dn" required placeholder="Enter username" value="<?php echo esc_attr( $dn ); ?>" /></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>You can specify the Username of the LDAP server in the either way as follows<br/><strong> Username@domainname or Distinguished Name(DN) format</strong></em></td>
						</tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr>
							<td style="width: 24%"><strong><span style="color:#FF0000">*</span>Password:</strong></td>
							<td><div id="ldap_server_password" style="position: relative;width: 70%;"><input class="mo_ldap_table_textbox <?php echo esc_attr( $mo_ldap_local_service_account_status ); ?>" required type="password" name="admin_password" placeholder="Enter password" value="<?php echo esc_attr( $admin_password ); ?>" id="ldap_server_password_field" /><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>/includes/images/eye.svg" alt="Show/Hide" class="toggle" id="toggle_ldap_server_password" onClick="togglePasswordVisibility('ldap_server_password_field', 'toggle_ldap_server_password')"></div></td>

							<script type="text/javascript">

								function togglePasswordVisibility(password_id, toggle_id) {
									const toggle = document.querySelector("#" + toggle_id);
									const password = document.querySelector("#" + password_id);

									//masks/unmasks password
									var type = password.getAttribute("type");
									if (type === "password") {
										type = "text";
									} else {
										type = "password";
									}
									password.setAttribute("type", type);

									//toggles eye image
									visible_path = "<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>/includes/images/eye.svg";
									invisible_path = "<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>/includes/images/eye-slash.svg";

									if (toggle.src === visible_path) {
										toggle.src = invisible_path;
									} else {
										toggle.src = visible_path;
									}
								} 


							</script>

							<style type="text/css">
								.toggle {
									margin-left: -30px;
									cursor: pointer;
								}
							</style>
						</tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><strong>The above username and password will be used to establish the connection to your LDAP server.</strong></td>
						</tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div class="save-ldap-conf-buttons-div">
									<input type="submit" class="button button-primary-ldap button-large" style="font-weight: 600;font-size:0.9rem !important;" value="Test Connection & Save"/>&nbsp;&nbsp; 
									<input type="button" id="conn_help" class="help button button-large mo_ldap_trouble_button" value="Troubleshooting" />
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="conn_troubleshoot" hidden>
								<div class="mo_ldap_local_troubleshoot_desc">
									<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
									<ol>
										<li>Please make sure that all the values entered are correct.</li>
										<li>If you are having firewall, open the firewall to allow incoming requests to your LDAP from your WordPress <strong>Server IP</strong> and <strong>port 389.</strong></li>
										<li>If you are still having problems, submit a query using the support panel on the right hand side.</li>
									</ol>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<script>
					jQuery("#mo_ldap_protocol").change(function() {

						var current_selected_protocol_name = jQuery("#mo_ldap_protocol").val();
						var port_number_field = jQuery("#mo_ldap_server_port_no").val();
						var ldap_port_number_value = jQuery("#mo_ldap_local_ldap_server_port_no").val();
						var ldaps_port_number_value = jQuery("#mo_ldap_local_ldaps_server_port_no").val();
						if (current_selected_protocol_name == "ldaps") {
							jQuery("#mo_ldap_server_port_no").val(ldaps_port_number_value);
						} else {
							jQuery("#mo_ldap_server_port_no").val(ldap_port_number_value);
						}
					});

				</script>
			</form>
		</div>

		<div class="mo_ldap_small_layout">
		<h3 class="mo_ldap_left">LDAP User Mapping Configuration</h3>
		<form id="mo_ldap_user_mapping_form" name="f" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_local_save_user_mapping' ); ?>
				<input id="mo_ldap_local_user_mapping_configuration_form_action" type="hidden" name="option" value="mo_ldap_local_save_user_mapping" />
				<div class="mo_ldap_panel">
					<table class="mo_ldap_settings_table" aria-hidden="true">
						<tr>
							<td style="width: 24%"></td>
							<td></td>
						</tr>

						<tr>
							<td><strong><span style="color:#FF0000">*</span>Search Base:</strong></td>
							<td>
								<div id="search_base_ldap" style="position: relative;line-height: 5; display: flex;justify-content: space-between; width:90%;">
									<input style="width: 60%;min-height: 34px;" class="mo_ldap_table_textbox  <?php echo esc_attr( $mo_ldap_local_user_mapping_status ); ?>" type="text" id="search_base" name="search_base" required placeholder="dc=domain,dc=com" value="<?php echo esc_attr( $search_base ); ?>" />
									<input style="margin-left: 3px;" type="button" id="searchbases" class="button button-primary-ldap button-large mo_ldap_local_primary_button" name="Search Bases" value="Possible Search Bases / Base DNs">
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>This is the LDAP Tree under which we will search for the users for authentication.  If we are not able to find a user in LDAP it means they are not present in this search base or any of its sub trees. They may be present in some other search base.<br> Provide the distinguished name of the Search Base object. <strong>eg. cn=Users,dc=domain,dc=com</strong>.

							<?php if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) { ?>
								<br><span style="color:#008000;"><strong>Multiple Search Bases are supported in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</strong></span></em><br><br></td>
							<?php } else { ?>
								If you have users in different locations in the directory(OU's), separate the distinguished names of the search base objects by a semi-colon(;). <strong>eg. cn=Users,dc=domain,dc=com; ou=people,dc=domian,dc=com</strong></em></td>
							<?php } ?>
						</tr>
						<tr>
							<td><strong><span style="color:#FF0000">*</span>Username Attribute:</strong></td>
							<td><span id="search_filter_ldap" style="position: relative;border-radius: 10px;">
									<select name="ldap_username_attribute" style="width:100%" id="ldap_username_attribute" >
										<?php
										$directory_server_value = get_option( 'mo_ldap_directory_server_value' );

										if ( strcmp( $directory_server_value, 'openldap' ) === 0 || strcmp( $directory_server_value, 'freeipa' ) === 0 ) {
											$username_ldap_attributes = array(
												'uid'  => 'uid',
												'sAMAccountName' => 'samaccountname',
												'mail' => 'mail',
												'userPrincipalName' => 'userprincipalname',
												'cn'   => 'cn',
												'Provide custom LDAP attribute name' => 'custom_ldap_attribute',
											);
										} else {
											$username_ldap_attributes = array(
												'sAMAccountName' => 'samaccountname',
												'mail' => 'mail',
												'userPrincipalName' => 'userprincipalname',
												'cn'   => 'cn',
												'Provide custom LDAP attribute name' => 'custom_ldap_attribute',
											);
										}

										foreach ( $username_ldap_attributes as $ldap_attribute_name => $ldap_attribute_value ) {
											$selected = ( $mo_ldap_local_ldap_username_attribute === $ldap_attribute_value ) ? 'selected' : '';

											echo "<option value='" . esc_attr( $ldap_attribute_value ) . "' " . esc_attr( $selected ) . '>' . esc_attr( $ldap_attribute_name ) . '</option>';
										}
										?>
									</select>
									</span></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<?php
								if ( strcasecmp( $mo_ldap_local_ldap_username_attribute, 'custom_ldap_attribute' ) === 0 ) {
									?>
									<input class="mo_ldap_table_textbox" style="width: 65%" type="text" id="custom_ldap_username_attribute" name="custom_ldap_username_attribute"  placeholder="eg. mail"  value="<?php echo esc_attr( get_option( 'Filter_search' ) ); ?>" />
									<?php
								} else {
									?>
									<input hidden class="mo_ldap_table_textbox" style="width: 65%" type="text" id="custom_ldap_username_attribute" name="custom_ldap_username_attribute"  placeholder="eg. mail"  value="<?php echo esc_attr( get_option( 'Filter_search' ) ); ?>"
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>This field is important for two reasons. <br>1. While searching for users, this is the attribute that is going to be matched to see if the user exists.  <br>2. If you want your users to login with their username or firstname.lastname or email - you need to specify those options in this field. e.g. <strong> LDAP_ATTRIBUTE</strong>. Replace <strong>&lt;LDAP_ATTRIBUTE&gt;</strong> with the attribute where your username is stored. Some common attributes are
							<ol>
							<table aria-hidden="true">
								<tr><td>logon name</td><td><strong>sAMAccountName</strong><br/><strong>userPrincipalName</strong></td></tr>
								<tr><td>email</td><td><strong>mail</strong></td></tr>
								<tr><td style="width:50%">common name</td><td><strong>cn</strong></td></tr>
								<tr><td>custom attribute where you store your WordPress usernames use</td> <td><strong>customAttribute</strong></td></tr>


							</table><br>
								You can even allow logging in with multiple attributes, separated with <strong>' ; ' </strong>. e.g. you can allow logging in with username or email. e.g.<strong> cn;mail</strong>
								<?php if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) { ?>
								<br><span style="color:#008000;"><strong>Logging in with multiple attributes are supported in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</strong></span></em><br><br></td>
							<?php } ?>
							</ol>
						</tr>
						<tr><td></td><td>Please make clear that the attributes that we are showing are examples and the actual ones could be different. These should be confirmed with the LDAP Admin.</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary-ldap button-large" style="font-weight: 600;font-size:0.9rem !important;" value="Save User Mapping"/>&nbsp;&nbsp; <input
								type="button" id="conn_help_user_mapping" class="help button button-large mo_ldap_trouble_button" value="Troubleshooting" /></td>
						</tr>
						<tr>
							<td colspan="2" id="conn_user_mapping_troubleshoot" hidden>
								<div class="mo_ldap_local_troubleshoot_desc">
									<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
									<ol>
										<li>The <strong>search base</strong> URL is typed incorrectly. Please verify if that search base is present.</li>
										<li>User is not present in that search base. The user may be present in the directory but in some other tree and you may have entered a tree where this users is not present.</li>
										<li><strong>Search filter</strong> is incorrect - User is present in the search base but the username is mapped to a different attribute in the search filter. E.g. you may be logging in with username and may have mapped it to the email attribute. So this wont work. Please make sure that the right attribute is mentioned in the search filter (with which you want the mapping to happen)</li>
										<li>Please make sure that the user is present and test with the right user.</li>
										<li>If you are still having problems, submit a query using the support panel on the right hand side.</li>
									</ol>
								</div>

							</td>
						</tr>
					</table>
				</div>
			</form>

			<script>
				jQuery("#searchbases").click(function (){
					showsearchbaselist();
				});
				function showsearchbaselist() {
					var nonce = "<?php echo esc_attr( wp_create_nonce( 'searchbaselist_nonce' ) ); ?>";
					var myWindow =   window.open('<?php echo esc_url( site_url() ); ?>' + '/?option=searchbaselist' + '&_wpnonce='+nonce, "Search Base Lists", "width=600, height=600");

				}
			</script>

			<script>

				jQuery("#ldap_username_attribute").change(function() {
				var current_selected_attribute_name = jQuery("#ldap_username_attribute").val();
				var custom_username_attribute_field = document.getElementById("custom_ldap_username_attribute");
				if (current_selected_attribute_name == "custom_ldap_attribute") {
					custom_username_attribute_field.style.display = "block";
				} else {
					custom_username_attribute_field.style.display = "none";
				}
				});
			</script>
		</div>

		<div class="mo_ldap_small_layout" id="Test_auth_ldap" style="position: relative;">
		<form name="f" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_local_test_auth' ); ?>
			<input type="hidden" name="option" value="mo_ldap_local_test_auth" />
			<h3 class="mo_ldap_left">Test Authentication</h3>

				<?php if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) { ?>
					WordPress username is mapped to the <strong>LDAP attribute defined in the Search Filter</strong> attribute in LDAP. Ensure that you have an administrator user in LDAP with the same attribute value. <br><br>
				<?php } ?>
			<div id="test_conn_msg"></div>
			<div class="mo_ldap_panel">
				<table class="mo_ldap_settings_table" aria-hidden="true">
					<tr>
						<td style="width: 24%"><strong><span style="color:#FF0000">*</span>Username:</strong></td>
						<td><input class="mo_ldap_table_textbox 
						<?php
						if ( ! empty( get_option( 'mo_ldap_local_username_status' ) ) && strcasecmp( get_option( 'mo_ldap_local_username_status' ), 'VALID' ) === 0 ) {
							echo esc_attr( $mo_ldap_local_username_status );}
						?>
						" type="text" name="test_username" required placeholder="Enter username"
						<?php
						if ( isset( $_POST['test_username'] ) && check_admin_referer( 'mo_ldap_local_test_auth' ) ) {
							echo 'value=' . esc_attr( sanitize_text_field( wp_unslash( $_POST['test_username'] ) ) );
						}
						?>
							" />
						</td>
					</tr>
					<tr>
						<td><strong><span style="color:#FF0000">*</span>Password:</strong></td>
						<td><input class="mo_ldap_table_textbox 
						<?php
						if ( ! empty( get_option( 'mo_ldap_local_username_status' ) ) && strcasecmp( get_option( 'mo_ldap_local_username_status' ), 'INVALID' ) === 0 ) {
							echo esc_attr( $mo_ldap_local_pass_status );}
						?>
						" type="password" name="test_password" required placeholder="Enter password" id="test_password_field" /><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>/includes/images/eye.svg" alt="Show/Hide" class="toggle" id="toggle_test_password" onClick="togglePasswordVisibility('test_password_field', 'toggle_test_password')"></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<div class="mo_ldap_local_test_btns_container">
								<div>
									<input type="submit" class="button button-primary-ldap button-large" style="font-weight: 600;font-size:0.9rem !important;" value="Test Authentication"/>
									<input type="button" id="auth_help" class="help button button-large mo_ldap_trouble_button" value="Troubleshooting" />
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" id="auth_troubleshoot" hidden>
							<div class="mo_ldap_local_troubleshoot_desc">
								<strong>User is not getting authenticated? Check the following:</strong>
								<ol>
									<li>The username-password you are entering is correct.</li>
									<li>The user is not present in the search bases you have specified against <strong>SearchBase(s)</strong> above.</li>
									<li>Your Search Filter may be incorrect and the username mapping may be to an LDAP attribute other than the ones provided in the Search Filter</li>
								</ol>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</form>
		</div>
	<?php
}

/**
 * Function: mo_show_export_page : Display export configurations page.
 *
 * @return void
 */
function mo_show_export_page() {
	?>
	<div class="mo_ldap_small_layout" style="margin-top: 0;">
		<div> 
			<div id="enable_save_config_ldap" style="position: relative; background: white; height: 30px;border-radius: 10px;">
			<form method="post" action="">
				<?php wp_nonce_field( 'enable_config' ); ?>
				<input type="hidden" name="option" value="enable_config" />
				<table aria-hidden="true"><tr><td><input class="toggle_button" type="checkbox" id = "enable_save_config" name="enable_save_config" value="1" onchange="this.form.submit()" <?php checked( esc_attr( strcasecmp( get_option( 'en_save_config' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="enable_save_config"></label><span class="mo_ldap_local_toggle_label">Keep configuration upon uninstall</span>
						</td></tr></table>
			</form></div><div id="mo_export" style="background: white;position: relative;border-radius: 10px;">
			<form method="post" action="" name="mo_export_pass">
				<?php wp_nonce_field( 'mo_ldap_pass' ); ?>
				<input type="hidden" name="option" value="mo_ldap_pass" />
				<table aria-hidden="true">
					<tr><td><h3 class="mo_ldap_left">Export Configuration</h3></td></tr>
					<tr>
						<td><p><em>This feature will allow you to export your plugin configuration into a JSON file.</em></p></td></tr>
					<tr><td><input class="toggle_button" type="checkbox" id="enable_ldap_login" name="enable_ldap_login" value="1" onchange="this.form.submit()" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_export' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="enable_ldap_login"></label><span class="mo_ldap_local_toggle_label">Export Service Account password. (This will lead to your service account password to be exported in encrypted fashion in a file)</span></td>
					</tr><tr><td>(Enable this only when server password is needed)</td>
						<td></td>
					</tr>
				</table>
			</form></div>
			<form method="post" action="" name="mo_export">
				<?php wp_nonce_field( 'mo_ldap_export' ); ?>
				<input type="hidden" name="option" value="mo_ldap_export"/>
				<br>
				<input type="button" style="font-weight:500;" class="button button-primary-ldap button-large" onclick="document.forms['mo_export'].submit();" value= "Export configuration" />
				<br><br>
			</form>
		</div>
	</div>
	<div class="mo_ldap_small_layout" style="margin-top:10px;">
			<div id="mo_import" style="background: white;position: relative;border-radius: 10px;">
				<table aria-hidden="true">
					<tr><td><h3 class="mo_ldap_left">Import Configuration<sup style="font-size: 12px;color:#008000;">  [Available in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.]</sup></td></tr><br>
					<tr>
						<td><p><em>This feature will allow you to import your plugin configuration from a previously exported JSON file.</em></p></td>
					</tr>
					<tr>
						<td><input type="file" name="mo_ldap_import_file" id="mo_ldap_import_file" required disabled/></td>
					</tr>
					<tr><td><br><td></tr>
					<tr>
						<td><input type="submit" class="button button-primary-ldap button-large"  value="Import Configuration" name="import_file" disabled/></td>
					</tr>
					<tr><td><br><td></tr>
				</table>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Function: mo_ldap_local_troubleshooting : Display Troubleshooting tab.
 *
 * @return void
 */
function mo_ldap_local_troubleshooting() {
	?>
	<div class="mo_ldap_table_layout" style="padding:0 10% 3% 10%;">
		<h2 class="mo-ldap-h2">Frequently Asked Questions</h2>
		<table class="mo_ldap_help" aria-hidden="true">
					<tbody>
					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_ldap_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How to enable PHP LDAP extension? (Pre-requisite)</div>
							</div>
							<div hidden="" style="padding: 2px 2px 2px 12px" id="help_ldap_desc" class="mo_ldap_help_desc">
								<ul>
									<li style="font-size: large; font-weight: bold">Step 1 </li>
									<li style="font-size: medium; font-weight: bold">Loaded configuration file : <?php echo esc_attr( php_ini_loaded_file() ); ?></li>
									<li style="list-style-type:square;margin-left:20px">Open php.ini file from above file path</li><br/>
									<li style="font-size: large; font-weight: bold">Step 2</li>
									<li style="font-weight: bold;color: #C31111">For Windows users using Apache Server</li>
									<li style="list-style-type:square;margin-left:20px">Search for <strong>"extension=php_ldap.dll"</strong> in php.ini file. Uncomment this line, if not present then add this line in the file and save the file.</li>
									<li style="font-weight: bold;color: #C31111">For Windows users using IIS server</li>
									<li style="list-style-type:square;margin-left:20px">Search for <strong>"ExtensionList"</strong> in the php.ini file. Uncomment the <strong>"extension=php_ldap.dll"</strong> line, if not present then add this line in the file and save the file.</li>
									<li style="font-weight: bold;color: #C31111">For Linux users</li>
										<ul style="list-style-type:square;margin-left: 20px">
											<li style="margin-top: 5px">Install php ldap extension (If not installed yet)
												<ul style="list-style-type:disc;margin-left: 15px;margin-top: 5px">
													<li>For Ubuntu/Debian, the installation command would be <strong>sudo apt-get -y install php-ldap</strong></li>
													<li>For RHEL based systems, the command would be <strong>yum install php-ldap</strong></li></ul></li>
											<li>Search for <strong>"extension=php_ldap.so"</strong> in php.ini file. Uncomment this line, if not present then add this line in the file and save the file.</li></ul><br/>
									<li style="margin-top: 5px;font-size: large; font-weight: bold">Step 3</li>
									<li style="list-style-type:square;margin-left:20px">Restart your server. After that refresh the "LDAP/AD" plugin configuration page.</li>
								</ul>
								<strong>For any further queries, please contact us.</strong>
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_instance_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is an instance?</div>
							</div>
							<div hidden="" id="help_instance_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>A WordPress instance refers to a single installation of a WordPress site. It refers to each individual website where the plugin is active. In the case of a single site WordPress, each website will be counted as a single instance.</li>
									<li>For example, You have 3 sites hosted like one each for development, staging, and production. This will be counted as 3 instances.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_subsite_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is a multisite network?</div>
							</div>
							<div hidden="" id="help_subsite_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>A multisite network means managing multiple sites within the same WordPress installation and has the same database.</li>
									<li>For example, You have 1 WordPress instance/site with 3 subsites in it then it will be counted as 1 instance with 3 subsites. You have 1 WordPress instance/site with 3 subsites and another WordPress instance/site with 2 subsites then it will be counted as 2 instances with 3 subsites.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="connect_using_ldaps" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How to setup/connect LDAP Server using LDAPS (LDAP over SSL)?</div>
							</div>
							<div hidden="" id="connect_ldaps_server" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li><a href="https://www.miniorange.com/guide-to-setup-ldaps-on-windows-server" rel="noopener" target="_blank">Click here</a> to go through the configuration steps to connect with LDAP server over LDAPS (LDAP over SSL:636).</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
						<div id="help_ping_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Contact LDAP Server not working?</div>
							</div>
							<div hidden="" id="help_ping_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Check your LDAP Server URL to see if it is correct.<br>
									eg. ldap://myldapserver.domain:389 , ldap://89.38.192.1:389. When using SSL, the host may have to take the form ldaps://host:636.</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Your LDAP Server may be behind a firewall. Check if the firewall is open to allow requests from your WordPress installation.</li>
								</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_selinuxboolen_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">I can connect to LDAP server through the command line (using ping/telnet) but get an error when I test connection from the plugin.</div>
							</div>
							<div hidden="" id="help_selinuxboolen_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>This issue usually occurs for users whose WordPress is hosted on CentOS server. this error because SELinux Boolean httpd_can_network_connect is not set.<br></li>
									<li>Follow these steps to resolve the issue:</li>
									<li>1. Run command: setsebool -P httpd_can_network_connect on</li>
									<li>2. Restart apache server.</li>
									<li>3. Run command: getsebool –a | grep httpd and make sure that httpd_can_network_connect is on</li>
									<li>4. Try Ldap connect from the plugin again</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="single_site_multisite_comaparision" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What’s the difference between a single site vs multisite network?</div>
							</div>
							<div hidden="" id="single_site_multisite_comaparision_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>A single site network only has one site, whereas a multisite network manages several sites all using the same WordPress installation and database.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_invaliddn_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Test LDAP Configuration not working?</div>
							</div>
							<div hidden="" id="help_invaliddn_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Check if you have entered valid Service Account DN(distinguished Name) of the LDAP server. <br>e.g. cn=username,cn=group,dc=domain,dc=com<br>
									uid=username,ou=organisational unit,dc=domain,dc=com</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Check if you have entered correct Password for the Service Account.</li>
								</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_invalidsf_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Test Authentication not working?</div>
							</div>
							<div hidden="" id="help_invalidsf_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;The username/password combination you provided may be incorrect.</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;You may have provided a <strong>Search Base(s)</strong> in which the user does not exist.</li>
								</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_seracccre_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What are the LDAP Service Account Credentials?</div>
							</div>
							<div hidden="" id="help_seracccre_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Service account is an non privileged user which is used to bind to the LDAP Server. It is the preferred method of binding to the LDAP Server if you have to perform search operations on the directory.</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;The distinguished name(DN) of the service account object and the password are provided as credentials.</li>
									</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_sbase_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is meant by Search Base in my LDAP environment?</div>
							</div>
							<div hidden="" id="help_sbase_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Search Base denotes the location in the directory where the search for a particular directory object begins.</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;It is denoted as the distinguished name of the search base directory object. eg: CN=Users,DC=domain,DC=com.</li>
								</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_sfilter_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is meant by Search Filter in my LDAP environment? <span style="color:#FF0000">*PREMIUM*</span></></div>
							</div>
							<div hidden="" id="help_sfilter_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Search Filter is a basic LDAP Query for searching users based on mapping of username to a particular LDAP attribute.</li>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;The following are some commonly used Search Filters. You will need to use a search filter which uses the attributes specific to your LDAP environment. Confirm from your LDAP administrator.</li>
										<ul>
											<table aria-hidden="true">
												<tr><td style="width:50%">common name</td><td>(&(objectClass=*)(<strong>cn</strong>=?))</td></tr>
												<tr><td>email</td><td>(&(objectClass=*)(<strong>mail</strong>=?))</td></tr>
												<tr><td>logon name</td><td>(&(objectClass=*)(<strong>sAMAccountName</strong>=?))<br/>(&(objectClass=*)(<strong>userPrincipalName</strong>=?))</td></tr>
												<tr><td>custom attribute where you store your WordPress usernames use</td> <td>(&(objectClass=*)(<strong>customAttribute</strong>=?))</td></tr>
												<tr><td>if you store WordPress usernames in multiple attributes(eg: some users login using email and others using their username)</td><td>(&(objectClass=*)(<strong>|</strong>(<strong>cn=?</strong>)(<strong>mail=?</strong>)))</td></tr>
											</table>
										</ul>
									</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_ou_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How do users present in different Organizational Units (OU's) login into WordPress? <span style="color:#FF0000">*PREMIUM*</span></div>
							</div>
							<div hidden="" id="help_ou_desc" class="mo_ldap_help_desc" style="display: none;">
								<ol>
									<li>&nbsp;&nbsp;&nbsp;&nbsp;Support for multiple search bases is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</li>
								</ol>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_loginusing_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Some of my users login using their email and the rest using their usernames. How will both of them be able to login?<span style="color:#FF0000"> *PREMIUM*</span></div>
							</div>
							<div hidden="" id="help_loginusing_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Support for multiple username attributes is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_rolemap_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How Role Mapping works?<span style="color:#FF0000"> *PREMIUM*</span></div>
							</div>
							<div hidden="" id="help_rolemap_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Support for Advanced Role Mapping is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_multiplegroup_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How Role Mapping works if user belongs to multiple groups?<span style="color:#FF0000"> *PREMIUM*</span></div>
							</div>
							<div hidden="" id="help_multiplegroup_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Support for Advanced Role Mapping is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

				</tbody></table>
	</div>
	<?php

}

/**
 * Function: mo_user_report_page : Display authentication report tab.
 *
 * @return void
 */
function mo_user_report_page() {
	?>

	<div class="mo_ldap_small_layout" style="margin-top:0px; height: auto;">

		<h2 class="mo_ldap_left">User Report</h2>
		<div style="display: flex; word-wrap: break-word;">
			<div style="width: 50%;">
				<form name="f" id="user_report_form" method="post" action="">
					<?php wp_nonce_field( 'user_report_logs' ); ?>
					<input type="hidden" name="option" value="user_report_logs" />
					<input class="toggle_button" type="checkbox" id="mo_ldap_local_user_report_log" name="mo_ldap_local_user_report_log" value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_user_report_log' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="mo_ldap_local_user_report_log"></label><span class="mo_ldap_local_toggle_label">Log Authentication Requests</span>
				</form><br>
			</div>
		<?php
		$log_user_reporting = get_option( 'mo_ldap_local_user_report_log' );
		$user_logs_empty    = MO_LDAP_Utility::mo_ldap_is_user_logs_empty();

		if ( strcasecmp( $log_user_reporting, '1' ) === 0 && ! $user_logs_empty ) {
			global $wpdb;
			$wp_user_report_data_cache = wp_cache_get( 'mo_ldap_user_report_data_cache' );
			if ( $wp_user_report_data_cache ) {
				$log_reports = $wp_user_report_data_cache;
			} else {
				$log_reports = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_report" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Fetching data from a custom table.
				wp_cache_set( 'mo_ldap_user_report_data_cache', $log_reports );
			}
			?>

			<script type="text/javascript">
				var result_object = <?php echo wp_json_encode( $log_reports ); ?>;
			</script>
				<div style="word-wrap: break-word; width: 50%; display: flex; justify-content: flex-end;">
					<form method="post" action="" name="mo_ldap_local_authentication_report" style="margin: 0px 1rem;">
						<?php wp_nonce_field( 'mo_ldap_authentication_report' ); ?>
						<input type="hidden" name="option" value="mo_ldap_authentication_report"/>
						<input type="button" class="button button-primary-ldap button-large" style="font-weight:500;" onclick="document.forms['mo_ldap_local_authentication_report'].submit();" value= "Export Report" />
					</form>
					<form method="post" action="" name="mo_ldap_local_clear_authentication_report">
						<?php wp_nonce_field( 'mo_ldap_clear_authentication_report' ); ?>
						<input type="hidden" name="option" value="mo_ldap_clear_authentication_report"/>
						<input type="button" class="button button-primary-ldap button-large" style="background-color: #dc3545;font-weight:500;" onclick="document.forms['mo_ldap_local_clear_authentication_report'].submit();" value= "Clear Logs" />
						<br>
					</form>
				</div>
			</div>
			<br><br>
			<div id="mo-ldap-local-user-report-table-div" class="mo-ldap-local-user-report-table-div">
			<?php
		} else {
			echo '</div> <br> No audit logs are available currently. <br><br>';
		}
		?>
	</div>
	<script>

		jQuery('#mo_ldap_local_user_report_log').change(function() {
			jQuery('#user_report_form').submit();
		});
		jQuery('#mo_ldap_local_keep_user_report_log').change(function() {
			jQuery('#keep_user_report_form_on_uinstall').submit();
		});

		<?php
		$log_user_reporting = get_option( 'mo_ldap_local_user_report_log' );
		$user_logs_empty    = MO_LDAP_Utility::mo_ldap_is_user_logs_empty();

		if ( strcasecmp( $log_user_reporting, '1' ) === 0 && ! $user_logs_empty ) {
			?>
			jQuery(document).ready(mo_ldap_display_log_table(result_object));
			<?php
		}
		?>

	</script>
	<?php

}
/**
 * Function: mo_ldap_local_signin_settings : Display sign-in setting tab.
 *
 * @return void
 */
function mo_ldap_local_signin_settings() {
	?>
<div class="mo_ldap_small_layout" style="margin-top:0px;">
			<form name="f" id="enable_login_form" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_local_enable' ); ?>
				<input type="hidden" name="option" value="mo_ldap_local_enable" />
				<h3 class="mo_ldap_left">Enable login using LDAP</h3>
				<div id="enable_ldap_login_bckgrnd">
					<input class="toggle_button" type="checkbox" id="enable_ldap_login" name="enable_ldap_login" value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_enable_login' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="enable_ldap_login"></label><span class="mo_ldap_local_toggle_label">Enable LDAP Login</span>
				</div>
				<p>Enabling LDAP login will protect your login page by your configured LDAP. <strong>Please check this only after you have successfully tested your configuration</strong> as the default WordPress login will stop working.</p>
			</form>
			<script>
				jQuery('#enable_ldap_login').change(function() {
					jQuery('#enable_login_form').submit();
				});
			</script>
			<form name="f" id="enable_admin_wp_login" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_local_enable_admin_wp_login' ); ?>
				<input type="hidden" name="option" value="mo_ldap_local_enable_admin_wp_login" />
				<?php
				$enable_both_login = get_option( 'mo_ldap_local_enable_login' );
				if ( strcasecmp( $enable_both_login, '1' ) === 0 ) {
					?>
					<input class="toggle_button" type="checkbox" id="mo_ldap_local_enable_admin_wp_login" name="mo_ldap_local_enable_admin_wp_login" value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_enable_admin_wp_login' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="mo_ldap_local_enable_admin_wp_login"></label><span class="mo_ldap_local_toggle_label">Authenticate Administrators from both LDAP and WordPress</span><br>
				<?php } ?>
			</form>
			<br>
			<form name="f" id="enable_register_user_form" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_local_register_user' ); ?>
				<input type="hidden" name="option" value="mo_ldap_local_register_user" />
				<input class="toggle_button" type="checkbox" id="mo_ldap_local_register_user" name="mo_ldap_local_register_user" value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_register_user' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="mo_ldap_local_register_user"></label><span class="mo_ldap_local_toggle_label">Enable Auto Registering users if they do not exist in WordPress</span>
			</form>
			<div id="miniorange-fallback-login" style="position:relative;background:white; line-height: 5;border-radius: 10px;">
				<input class="toggle_button" type="checkbox" id="" name="" disabled /><label class="toggle_button_label" for=""></label><span class="mo_ldap_local_toggle_label">Authenticate WP Users from both LDAP and WordPress <span style="color:#008000;"><strong> <em>( Supported in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin. )</em></strong></span></span>
			</div>
			<div id="miniorange-protect-site">
				<input class="toggle_button" type="checkbox" id="" name="" disabled /><label class="toggle_button_label" for=""></label><span class="mo_ldap_local_toggle_label">Protect all website content by login <span style="color:#008000;"><strong> <em>( Supported in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin. )</em></strong></span></span>
			</div>
			<div class="mo_ldap_local_enable_sso">
				<input class="toggle_button" type="checkbox" id="" name="" disabled /><label class="toggle_button_label" for=""></label><span class="mo_ldap_local_toggle_label">Enable Single Sign-On (SSO)<span style="color:#008000;"><strong> <em>( Supported in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin. )</em></strong></span></span>
				<p>Enable Auto-login (SSO) for your WordPress website on a domain joined machines using Kerberos / NTLM.</p>
			</div>
			<br/>
		</div>

		<div class="mo_ldap_small_layout" style="margin-top:10px;">
			<h3>Restrict User Login by Role <span style="color: #008000;font-style: italic;font-size: 14px;margin-left: 5px;">[Available in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.]</span></h3>
			<div id="mo_ldap_restrict_login_role">
				<form name="f" id="mo_ldap_save_restrict_login_by_role_form" method="post" action="">
					<input type="hidden" name="option" value="mo_ldap_save_restrict_login_by_role"/>
					<input disabled type="checkbox"  value="1" name="mo_ldap_local_restrict_user_by_role" id="mo_ldap_local_restrict_user_by_role" />Enable Restrict User Login by Role
					<br>
					<p style="color: green;"><em><strong>Note:</strong> User with the Administrator role will not be restricted while login.</em></p>
					<div id="panel1">
						<table class="mo_ldap_settings_table" id="mo_ldap_restrict_login_table" style="width:95%;table-layout: fixed;">
							<tr>
								<th></th>
								<th></th>
							</tr>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td>
									<font style="font-size:13px;font-weight:bold;">Restrict Role(s)</font>
								</td>
								<td>
									<div id="mo_ldap_local_restrict_login_dd" class="mo-ldap-restrict-login-role-dropdown" tabindex="100">
										<span class="mo_ldap_restrict_anchor">Select Role(s)</span>
										<ul class="mo_ldap_local_restrict_roles_list">
										<?php
										$roles = get_role_names();
										foreach ( $roles as $key => $role ) {
											if ( strcasecmp( $key, 'administrator' ) === 0 ) {
												continue;
											}
											echo '<li><input disabled type="checkbox" id="mo_ldap_restrict_role[]" name="mo_ldap_restrict_role[]" value="' . esc_attr( $key ) . '"/>' . esc_html( $role ) . '</li>';
										}
										?>
										</ul>
									</div>
								</td>
							</tr>
							<tr></tr>
							<tr></tr>
							<br>
							<tr>
								<td>
									<input disabled type="submit" value="Save Configuration" class="button button-primary button-large" >
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>

		<script>
			var checkList = document.getElementById('mo_ldap_local_restrict_login_dd');
			checkList.getElementsByClassName('mo_ldap_restrict_anchor')[0].onclick = function(evt) {
				checkList.classList.toggle('visible');
			}
			jQuery('#mo_ldap_local_register_user').change(function() {
				jQuery('#enable_register_user_form').submit();
			});
			jQuery('#enable_fallback_login_form').change(function() {
				jQuery('#enable_fallback_login_form').submit();
			});
			jQuery('#enable_admin_wp_login').change(function() {
				jQuery('#enable_admin_wp_login').submit();
			});
		</script>

		<?php
}

/**
 * Function: mo_ldap_local_multiple_ldap : Display multiple directories tab.
 *
 * @return void
 */
function mo_ldap_local_multiple_ldap() {
	$current_user = wp_get_current_user();
	if ( get_option( 'mo_ldap_local_admin_email' ) ) {
		$admin_email = get_option( 'mo_ldap_local_admin_email' );
	} else {
		$admin_email = $current_user->user_email;
	}
	?>

	<div  hidden id="licensingContactUsModalMultidir" name="licensingContactUsModal" class="mo_ldap_modal" style="margin-left: 26%;z-index:999999;">
	<div class="moldap-modal-contatiner-contact-us" style="color:black"></div>
		<div class="mo_ldap_modal-content" id="contactUsPopUp" style="width: 700px; padding:30px;">
			<span id="contact_us_title_multidir" style="font-size: 22px; margin-left: 50px; font-weight: bold;">Contact Us for Choosing the Correct Premium Plan</span>
			<form name="f" method="post" action="" id="mo_ldap_licensing_contact_us_multidir" style="font-size: large;">
				<?php wp_nonce_field( 'mo_ldap_login_send_feature_request_query' ); ?>
				<input type="hidden" name="option" value="mo_ldap_login_send_feature_request_query"/>
				<div>
					<p style="font-size: large;">
						<br>
						<strong>Email: </strong>
						<input style=" width: 77%; margin-left: 69px; " type="email" class="mo_ldap_table_textbox" id="query_email_multidir" name="query_email" value="<?php echo esc_attr( $admin_email ); ?>" placeholder="Enter email address through which we can reach out to you" required />
						<br><br>
						<span style="display:inline-block; vertical-align: top;">Description: </span>
						<textarea style="width:77%; margin-left: 21px;" id="query_multidir" name="query" required rows="5" style="width: 100%"
								placeholder="Tell us which features you require"></textarea></p>
						<br><br>
					<div class="mo_ldap_modal-footer" style="text-align: center">
						<input type="button" style="font-size: medium" name="miniorange_ldap_feedback_submit" id="miniorange_ldap_feedback_submit_multidir"
							class="button button-primary-ldap button-small" onclick="validateRequirements()" value="Submit"/>
						<input type="button" style="font-size: medium" name="miniorange_ldap_licensing_contact_us_close" id="miniorange_ldap_licensing_contact_us_close_multidir" class="button button-primary-ldap button-small" value="Close" />
					</div>
				</div>
			</form>
		</div>

	</div>
	<div class="mo_ldap_small_layout" style="margin-top: 0;">
		<h3 class="mo_ldap_left">Add new LDAP Server &nbsp<sup style="font-size: 12px;color: green;">[To get more details on this plan please <a id="MultipleDirContactUsTab" href="#MultipleDirContactUs">Contact Us</a> ]</sup></h3>
				<table class="mo_ldap_settings_table" aria-hidden="true">
					<tr>
							<td style="width: 24%"><strong><span style="color:#FF0000">*</span>LDAP Server:</strong></td>
							<td><input class="mo_ldap_table_textbox" disabled type="url" id="ldap_server" name="ldap_server" required placeholder="ldap://<server_address or IP>:<port>"</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>eg: ldap://myldapserver.domain:389 , ldap://89.38.192.1:389. </em></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong><span style="color:#FF0000">*</span>Username:</strong></td>
							<td><input class="mo_ldap_table_textbox" disabled type="text" id="multiple_ldap_dn" name="multiple_ldap_dn" required placeholder="CN=service,DC=domain,DC=com"</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>e.g. cn=username,cn=group,dc=domain,dc=com</em></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong><span style="color:#FF0000">*</span>Password:</strong></td>
							<td><input class="mo_ldap_table_textbox" required disabled type="password" name="admin_password" placeholder="Enter password of Service Account"</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong><span style="color:#FF0000">*</span>Search Base(s):</strong></td>
							<td><input class="mo_ldap_table_textbox" disabled type="text" id="multiple_ldap_search_base" name="multiple_ldap_search_base" required placeholder="dc=domain,dc=com" </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><em>e.g. cn=Users,dc=domain,dc=com</em></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td></td></tr><tr><td></td></tr>
						<tr><td><strong><span style="color:#FF0000"></span>Search Conditions:</strong></td>
							<td>
								<label class="switch">
							<input type="checkbox"disabled id="search_filter_check_add" name="ldap_search_filters_add">
						<div class="slider round"</div>
						</label>
						</td>
						</tr>

						<tr><td></td><td><strong>Enable Custom Search Filter </strong>(Enable this to add more search-filter conditions.)</td></tr>
						<tr>
							<td></td>
							<td><br>
								<div id="user_div" style=" margin-top: -20px; ">

									<table aria-hidden="true">
										<tbody><tr>
											<td>
								<strong><span style="color:#FF0000">*</span>Username Attribute:</strong></td>
								<td>
							<input class="mo_ldap_table_textbox" disabled type="text" size="58" name="username_attribute_text" id="username_attribute_id_add" placeholder="Enter username attribute" required="required"></td></tr>
								<tr>
							<td colspan="2"><em>e.g. sAMAccountName, userPrincipalName;mail</em></td>
						</tr>

							</tbody></table>

							</div>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><br>
								<div id="ldap_search_filter_div" style="margin-top: -30px;display: none;">
									<table aria-hidden="true"><tbody><tr>
							<td width="140px"><strong><span style="color:#FF0000">*</span>Custom Search Filter:</strong></td>
							<td><input class="mo_ldap_table_textbox" type="text" id="search_filter_add" size="58" name="search_filter" placeholder="(&amp;(objectClass=*)(cn=?))" value="(&amp;(objectClass=*)(sAMAccountName=?))" pattern=".*\?.*" title="Must contain Question Mark(?) for attributes you want to match e.g. (&amp;(objectClass=*)(uid=?))"></td>
						</tr>
						<tr>
							<td colspan="2"><em>e.g. (&amp;(objectClass=*)(cn=?)), (&amp;(objectClass=*)(sAMAccountName=?))</em></td>
						</tr>

					</tbody></table></div></td></tr>

						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary-ldap button-large" disabled value="Test Connection &amp; Save">&nbsp;&nbsp;<input type="button" disabled class="button button-primary-ldap button-large" value="Add New configuration" onclick="add_new_config()"></td>
						</tr>

						</tbody>
					</table>
				</div>
			</form>

	<script>


		jQuery('a[id=MultipleDirContactUsTab]').click(
			function(){

				jQuery('#licensingContactUsModalMultidir').show();
				jQuery("#contact_us_title_multidir").text("Contact Us for LDAP Multiple Directories Premium Plan");
				query = "Hi!! I am interested in LDAP Multiple Directories Premium Plan and want to know more about it.";
				jQuery("#mo_ldap_licensing_contact_us_multidir #query_multidir").val(query);
			});

		jQuery('#miniorange_ldap_licensing_contact_us_close_multidir').click(
			function(){
			jQuery("#mo_ldap_licensing_contact_us_multidir #query_multidir").val('');
			jQuery('#licensingContactUsModalMultidir').hide();
		});

	function validateRequirements() {

	if(validateEmails()){
		var requirement = document.getElementById("query_multidir").value;
		if (requirement.length <= 10) {
					alert("Please enter more details about your requirement.");
		} else {
			document.getElementById("mo_ldap_licensing_contact_us_multidir").submit();
		}
	}
	}

	function validateEmails()
	{
		var email = document.getElementById('query_email_multidir');
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
 * Function: mo_ldap_local_rolemapping : Display role mapping tab.
 *
 * @return void
 */
function mo_ldap_local_rolemapping() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	?>
	<div class="mo_ldap_small_layout" style="margin-top:0px;">
		<form name="f" id="enable_role_mapping_form" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_local_enable_role_mapping' ); ?>
			<input type="hidden" name="option" value="mo_ldap_local_enable_role_mapping" />
			<a class="button button-large button-next" style="float: right; margin: 10px; 
			<?php
			if ( strcasecmp( get_option( 'mo_ldap_local_enable_role_mapping' ), '1' ) !== 0 ) {
				echo 'pointer-events: none;';}
			?>
			" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'attributemapping' ), htmlentities( $request_uri ) ) ); ?>" 
			<?php
			if ( strcasecmp( get_option( 'mo_ldap_local_enable_role_mapping' ), '1' ) !== 0 ) {
				echo 'disabled';
			}
			?>
			>Next ❯</a>
			<a class="button button-primary button-large button-skip" style="float: right; margin: 10px;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'attributemapping' ), htmlentities( $request_uri ) ) ); ?>" >Skip </a>
			<h3 class="mo_ldap_left">Role Mapping Configuration<sup style="font-size: 12px"></h3>
			<table aria-hidden="true">
				<tr>
					<td class="mo_ldap_local_role_mapping_table_left">
						<input type="checkbox" class="toggle_button" id="enable_ldap_role_mapping" name="enable_ldap_role_mapping"  value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_enable_role_mapping' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="enable_ldap_role_mapping"></label><span class="mo_ldap_local_toggle_label">Enable Role Mapping</span>
					</td>
				</tr>
				<tr>
					<td class="mo_ldap_local_role_mapping_table_left">
						<input class="toggle_button" type="checkbox" id="keep_existing_user_roles" name="keep_existing_user_roles" value="1" <?php checked( esc_attr( strcasecmp( get_option( 'mo_ldap_local_keep_existing_user_roles' ), '1' ) === 0 ) ); ?> /><label class="toggle_button_label" for="keep_existing_user_roles"></label><span class="mo_ldap_local_toggle_label">Keep existing roles of users (New roles will still be added).</span>
					</td>
				</tr>
			</table>
			<br>
			<table class="mo_ldap_mapping_table" id="ldap_default_role_mapping_table" style="width: 75%;" aria-hidden="true">
					<tr>
						<td style="width: 50%;"><span style="font-size:13px;font-weight:bold;">Select the default WordPress role all users will have:</span>
					</td>
					<td style="width: 30%;">
						<div id="default_role_value" style="position: relative;border-radius: 10px;">
						<select name="mapping_value_default" style="width:100%" id="default_group_mapping" >
							<?php
							if ( get_option( 'mo_ldap_local_mapping_value_default' ) ) {
								$default_role = get_option( 'mo_ldap_local_mapping_value_default' );
							} else {
								$default_role = get_option( 'default_role' );
							}
								wp_dropdown_roles( $default_role );
							?>
						</select>
						<select style="display:none" id="wp_roles_list">
							<?php wp_dropdown_roles( $default_role ); ?>
						</select></div>
					</td>
				</tr>
				<tr>
					<td><br><input id="save-default-mapping" style="font-weight:500;" type="submit" class="button button-primary-ldap button-large" value="Save Configuration" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="mo_ldap_small_layout" style="position: relative;">
		<form name="mo_ldap_select_role_mapping_type" method="post" id="mo_ldap_select_role_mapping_type">
			<input type="hidden" name="option" value="mo_ldap_role_mapping_selection"/>
			<h3>Configure Role Mapping <span style="color: #008000;font-style: italic;font-size: 14px;margin-left: 5px;">[Available in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.]</span></h3><br>

			<div class="mo_ldap_role_mapping_subnav">
				<input type="radio" class="mo_ldap_role_mapping_subnav_hidden_radio" id="mo_ldap_role_mapping_tab_1" name="mo_ldap_role_mapping_selected_tab" value="1">
				<label for="mo_ldap_role_mapping_tab_1">
					<div class="mo_ldap_role_mapping_subnav_tab mo_ldap_role_mapping_subnav_tab_active" id="mo_ldap_local_role_mapping_type_1">
						Assign WordPress Roles Based On LDAP Security Groups
					</div>
				</label>
				<input type="radio" class="mo_ldap_role_mapping_subnav_hidden_radio" id="mo_ldap_role_mapping_tab_2" name="mo_ldap_role_mapping_selected_tab" value="2">
				<label for="mo_ldap_role_mapping_tab_2">
					<div class="mo_ldap_role_mapping_subnav_tab" id="mo_ldap_local_role_mapping_type_2">
						Assign WordPress Roles Based On LDAP OU
					</div>
				</label>

				<input type="radio" class="mo_ldap_role_mapping_subnav_hidden_radio" id="mo_ldap_role_mapping_tab_3" name="mo_ldap_role_mapping_selected_tab" value="3">
				<label for="mo_ldap_role_mapping_tab_3">
					<div class="mo_ldap_role_mapping_subnav_tab" id="mo_ldap_local_role_mapping_type_3">
						Assign WordPress Roles Based On LDAP Attributes
					</div>
				</label>
			</div>
		</form>

		<div id="role_mapping_ldap_group" class="role_mapping_sub_tab_section d-none">
			<form id="mo_ldap_group_role_mapping_form" name="f" method="post" action="">
				<p><input disabled type="checkbox" id="enable_ldap_role_mapping_based_on_ldap_groups" name="enable_ldap_role_mapping_based_on_ldap_groups" value="1"/> Enable Role Mapping Based On LDAP Security Groups</p>
			</form>

			<form name="mo_ldap_fetch_groups_form" method="post" action="" id="mo_ldap_fetch_groups_form">

				<br>
				<i>Provide the Search Base DN which contains the LDAP security groups information and Click on Show Groups
					button.</i>
				<br>

				<table id="groups_table" class="mo_ldap_settings_table" style="width:95%;table-layout: fixed;">
					<tbody>
					<tr></tr>
					<tr></tr>
					<tr>
						<td colspan="3">
							<input disabled type="text" id="mo_ldap_groups_search_base" name="mo_ldap_groups_search_base" required placeholder="cn=groups,dc=domain,dc=com" style="width:80%;">
						</td>
						<td>
							<input disabled type="submit" value="Show Groups" class="button button-primary button-large">
						</td>
					</tr>
					<tr></tr>
					</tbody>
				</table>
			</form>
			<br>
			<form id="mo_ldap_role_mapping_form" name="f" method="post" action="">
				<div id="mo_ldap_local_role_mapping_list" style="max-width:100%; padding:10px 5px 10px 10px;overflow-y: auto;max-height: 30em;box-shadow: 0px 0px 0px 2px #888;">
					<table>
						<tr>
							<td><input disabled type="text" name="filter_groups_list" id="filter_groups_list" placeholder="Enter group name"></td>
							<td><input disabled type="button" class="button button-primary" name="filter_groups_list_button" id="filter_groups_list_button" value="Filter Groups" onclick="filter_groups_for_role_mapping()"></td>&nbsp;&nbsp;
							<td><input disabled type="button" class="button button-primary" name="clear_filter_groups_list_text" id="clear_filter_groups_list_text" value="Clear text" onclick="clear_filter_group_text_for_role_mapping()"></td>
							<td><input disabled type="button" class="button button-primary" value="Refresh" id="mo_refresh_ldap_groups"></td>
						</tr>
					</table> 
					<br>	
					<table id="mo_ldap_role_mapping_groups_list_table" style="width: 100%">
						<tr>
							<th style="width: 15%;">Group Name</th>
							<th style="width: 50%;">Distinguished Name</th>
							<th style="width: 25%;">WordPress Role</th>
							<th style="width: 10%;">Add/Remove</th>
						</tr>
						<tr>
							<td style="width: 15%; text-align:center">demogroup</th>
							<td style="width: 50%; text-align:center">CN=demogroup,DC=domain,DC=com</th>
							<td style="width: 25%; text-align:center">
							<select disabled style="width: 100%;">
								<option>Select role(s)</option>
							</select></th>
							<td style="width: 10%;"><input style="margin-left:20px;" type="button" name="add_attribute" value="+" class="button button-primary" disabled></th>
						</tr>
					</table>
				</div> 
				<br>
				<div>
					<i> Specify attribute which stores group names to which LDAP Users belong.</i>
					<br><br>
					<table class="mo_ldap_mapping_table" style="width:95%;table-layout: fixed;">
						<tr>
							<td style="width:50%">
								<font style="font-size:13px;font-weight:bold;">LDAP Group Attributes Name</font>
							</td>
							<td>
								<input disabled type="text" name="mapping_memberof_attribute" required="true" placeholder="Group Attributes Name" style="width:100%;" value="memberOf" >
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><input disabled type="submit" class="button button-primary button-large"
									value="Save Mapping" /></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</form>
		</div>

		<div id="role_mapping_ldap_ou" class="role_mapping_sub_tab_section d-none">
			<form name="mo_ldap_roles_based_on_ldap_ou_form" method="post" id="mo_ldap_roles_based_on_ldap_ou_form">
				<div id="panel1" class="wp_roles_based_on_ldap_ou">
					<p><input disabled type="checkbox" id="enable_ldap_role_mapping_based_on_ldap_ou" name="enable_ldap_role_mapping_based_on_ldap_ou" value="1"   /> Enable Role Mapping Based On LDAP OU</p>
					<table class="" id="ldap_user_ou_mapping_table" style="width:90%">
						<td>
							<select disabled id="ou_mapping_wp_roles_dropdown" style="display:none" >
								<option value='subscriber'>Subscriber</option>
								<option value='contributor'>Contributor</option>
								<option value='author'>Author</option>
								<option value='editor'>Editor</option>
								<option value='administrator'>Administrator</option>
							</select>
						</td>
						<tr>
							<th><b>LDAP OU DN</b></th>
							<th><b>WordPress Role</b></th>
						</tr>
						<tbody>
							<tr>
								<td style="width: 25%; text-align: center;">
									<input disabled style="width: 80%" class="mo_ldap_ou_table_textbox" type="text" name="mo_ldap_role_mapping_ou_value_1" value=""  placeholder="ou=myou,dc=domain,dc=com"  />
								</td>
								<td style="width: 25%; text-align: center;">
									<select disabled name="mo_ldap_ou_mapping_role_name_1" id="wp_role" style="width:100%"  >
										<option value='subscriber'>Subscriber</option>
										<option value='contributor'>Contributor</option>
										<option value='author'>Author</option>
										<option value='editor'>Editor</option>
										<option value='administrator'>Administrator<option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<br>
					<a style="cursor:pointer" id="add_ldap_ou_mapping"><b>Add More OU(s)</b></a> <br><br>

					<input disabled type="submit" class="button button-primary button-large" value="Save Mapping" />
				</div>
			</form>
		</div>

		<div id="role_mapping_ldap_attribute" class="role_mapping_sub_tab_section d-none">
			<form name="mo_ldap_roles_based_on_ldap_attribute_form" method="post" id="mo_ldap_roles_based_on_ldap_ou_form">
				<div id="panel1" class="wp_roles_based_on_ldap_attribute">
					<p><input disabled type="checkbox" id="enable_ldap_role_mapping_based_on_ldap_attribute" name="enable_ldap_role_mapping_based_on_ldap_attribute" value="1"   /> Enable Role Mapping Based On LDAP Attribute</p>
					<table class="" id="ldap_user_attribute_mapping_table" style="width:90%">
						<td>
							<select disabled id="attribute_mapping_wp_roles_dropdown" style="display:none" >
								<option value='subscriber'>Subscriber</option>
								<option value='contributor'>Contributor</option>
								<option value='author'>Author</option>
								<option value='editor'>Editor</option>
								<option value='administrator'>Administrator</option>
							</select>
						</td>
						<tr>
							<th><b>LDAP Attribute</b></th>
							<th><b>Attribute Value</b></th>
							<th><b>WordPress Role</b></th>
						</tr>
						<tbody>
							<tr>
								<td style="width: 25%; text-align: center;">
									<input disabled style="width: 80%" class="mo_ldap_attribute_table_textbox" type="text" name="mo_ldap_role_mapping_attribute_1" placeholder="Department" />
								</td>
								<td style="width: 25%; text-align: center;">
									<input disabled style="width: 80%" class="mo_ldap_attribute_table_textbox" type="text" name="mo_ldap_role_mapping_attribute_value_1" placeholder="Sales" />
								</td>
								<td style="width: 25%">
									<select disabled name="mo_ldap_attribute_mapping_role_name_1" id="wp_role" style="width:100%"  >
										<option value='subscriber'>Subscriber</option>
										<option value='contributor'>Contributor</option>
										<option value='author'>Author</option>
										<option value='editor'>Editor</option>
										<option value='administrator'>Administrator<option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<br>
					<a style="cursor:pointer" id="add_ldap_attribute_mapping"><b>Add More Attribute(s)</b></a> <br><br>

					<input disabled type="submit" class="button button-primary button-large" value="Save Mapping" />
				</div>
			</form>
		</div>
	</div>

	<div id="mo_rolemap_ldap_username" style="position: relative;border-radius: 10px;" class="mo_ldap_small_layout">
		<h3 class="mo_ldap_left">Test Role Mapping Configuration <sup style="font-size: 12px;color:#008000;">[Available in <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium version</a> of the plugin.]</sup></h3>Enter LDAP username to test role mapping configuration
		<table id="attributes_table" aria-hidden="true" class="mo_ldap_settings_table">
			<tbody>
				<tr></tr>
				<tr></tr>
				<tr>
					<td><strong>Username</strong></td>
					<td><input type="text" id="mo_ldap_username" name="mo_ldap_username" disabled placeholder="Enter Username" style="width:61%;background: #DCDAD1;"></td>
				</tr>
				<tr>
					<td><input type="button" value="Test Configuration" class="button button-primary-ldap button-large" disabled></td>
				</tr>
			</tbody>
		</table>
	</div>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#default_group_mapping option[value='administrator']").remove();
		});

		jQuery( document ).ready(function() {
			jQuery("#mo_ldap_fetch_groups_form :input").prop("disabled", true);
			jQuery("#enable_role_mapping_form :input").prop("enabled", true);
			jQuery("#rolemappingtest :input").prop("disabled",true);
			jQuery("#rolemappingtest :input[type=text]").val("");
			jQuery("#ldap_role_mapping_table :input").prop("disabled",true);
			jQuery("#role_mapping_form_ldap :input").prop("disabled",true);
			jQuery('#default_group_mapping').prop("enabled",true);
			jQuery('#add_mapping').prop("disabled",true);
			jQuery('#save-default-mapping').prop("enabled",true);
			jQuery('#save_role_mapping').prop("disabled",true);
		});
	</script>
	<?php
}

/**
 * Function: mo_ldap_show_attribute_mapping_page : Display attribute mapping tab.
 *
 * @return void
 */
function mo_ldap_show_attribute_mapping_page() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	?>
		<div class="mo_ldap_small_layout" style="margin-top: 0;">
			<div id="ldap_intranet_attribute_mapping_div">
				<form name="f" method="post" id="attribute_config_form">
					<?php wp_nonce_field( 'mo_ldap_save_attribute_config' ); ?>
					<table id="attributes_table" aria-hidden="true" class="mo_ldap_settings_table">
						<input type="hidden" name="option" value="mo_ldap_save_attribute_config"/>
						<a class="button button-large button-next"  
						<?php
						echo 'style="float: right; margin: 10px;';
						if ( empty( get_option( 'mo_ldap_local_email_attribute' ) ) ) {
							echo 'pointer-events: none;';
						}
						echo '"';
						?>
						<?php
							echo 'href=' . esc_url( add_query_arg( array( 'tab' => 'signin_settings' ), htmlentities( $request_uri ) ) );
						?>
						<?php
						if ( empty( get_option( 'mo_ldap_local_email_attribute' ) ) ) {
							echo 'disabled';
						}
						?>
						>Next ❯</a>
						<a class="button button-primary button-large button-skip" style="float: right; margin: 10px;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'signin_settings' ), htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ); ?>" >Skip </a>
						<h3 class="mo_ldap_left">Attribute Configuration</h3>
						<tr>
							<td style="width:70%;">Enter the LDAP attribute names for Email, Phone, First Name and Last Name attributes.</td>
						</tr>
						<tr><td></td></tr>
						<tr>
							<td style="width:40%;"><strong><span style="color:#FF0000">*</span>Email Attribute</strong></td>
							<td><div id="ldap_intranet_attribute_mail_name" style="position: relative;border-radius: 10px;"><input type="text"  name="mo_ldap_email_attribute" placeholder="Enter Email attribute" required style="width:80%;"
							value="<?php echo esc_attr( get_option( 'mo_ldap_local_email_attribute' ) ); ?>"/></div></td>
						</tr>
						<tr>
							<td style="width:40%;"><strong>Phone Attribute</strong></td>
							<td><div id="ldap_intranet_attribute_phone_name" style="position: relative"><input type="text" name="mo_ldap_phone_attribute"  placeholder="Enter Phone attribute" style="width:80%;background: #DCDAD1;"
							value="<?php echo esc_attr( get_option( 'mo_ldap_local_phone_attribute' ) ); ?>" 
							<?php
							if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) {
								echo 'disabled';
							}
							?>
							/></div></td>
						</tr>
						<tr>
							<td style="width:40%;"><strong>First Name Attribute</strong></td>
							<td><input type="text" name="mo_ldap_fname_attribute"  placeholder="Enter First Name attribute" style="width:80%;background: #DCDAD1;"
							value="<?php echo esc_attr( get_option( 'mo_ldap_local_fname_attribute' ) ); ?>" 
							<?php
							if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) {
								echo 'disabled'; }
							?>
							/></td>
						</tr>
						<tr>
							<td style="width:40%;"><strong>Last Name Attribute</strong></td>
							<td><input type="text" name="mo_ldap_lname_attribute"  placeholder="Enter Last Name attribute" style="width:80%;background: #DCDAD1;"
							value="<?php echo esc_attr( get_option( 'mo_ldap_local_lname_attribute' ) ); ?>" 
							<?php
							if ( strcasecmp( get_option( 'mo_ldap_local_cust', '1' ), '0' ) === 0 ) {
								echo 'disabled'; }
							?>
							/></td>
						</tr>
						<tr>
							<td><h3>Add Custom Attributes</h3></td>
						</tr>
						<tr>
							<td colspan="2">Enter extra LDAP attributes which you wish to be included in the user profile.</td>
						</tr>
						<tr>
							<td>
								<input type="text" name="mo_ldap_local_custom_attribute_1_name" placeholder="Custom Attribute Name" style="width:45%;" disabled/>
								<input style="margin-left:20px;" type="button" name="add_attribute" value="+" class="button button-primary" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="button" name="remove_attribute" value="-" class="button button-primary"  disabled/>
							</td>
							<td style="width:100%;float:right;">&nbsp;</td>
						</tr>
						<tr><td colspan="2"><p><span style="color:#008000;"><strong><em>Support for Phone, First Name, Last Name and Custom attributes from LDAP is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</em></strong></span></p></em></td></tr>
						<tr><td><br></td></tr>

						<tr>
							<td style="width:40%;"><strong>Email Domain</strong></td>
							<?php $pattern_string = '[a-z0-9.-]+\.[a-z]{2,}$'; ?>
							<td><div id="ldap_intranet_email_domain" style="position: relative;border-radius: 10px;"><input type="text" pattern="<?php echo esc_attr( $pattern_string ); ?>" title="Please Enter Valid Domain Name. Ex. miniorange.com" name="mo_ldap_email_domain" placeholder="example.com"  style="width:80%;" value="<?php echo esc_attr( get_option( 'mo_ldap_local_email_domain' ) ); ?>"/></div></td>
						</tr>
						<tr></tr>
						<tr></tr>
						<tr>
							<td colspan=2><em>  Set user email to <strong>username@email_domain</strong> in WordPress, if the "mail" attribute is not set in LDAP directory.</em></td>
						</tr>

						<tr>
							<td colspan="2"><h3>Custom LDAP to User Meta Mapping</h3></td>
						</tr>
						<tr>
							<td colspan="2">
								<table style="width: 100%;">
									<tr>
										<td style="width: 80%;">
										This form is used to assocaiate the attributes from your AD/LDAP to the meta_key field in your WordPress<br>For Eg: You can associate the givenName LDAP attribute to the first_name meta_key in WordPress.
										</td>
										<td style="float:right;margin-right: 15px;">
											<input disabled type="button" value="+" class="button button-primary">
											<input disabled type="button" value="-" class="button button-primary" style="margin-left: 5px;" onclick="remove_ldap_usermeta_mapping()">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr class="usermeta_row">
							<td><input disabled type="text" placeholder="Enter meta_key" style="width: 60%;"></td>
							<td><input disabled type="text" placeholder="Enter LDAP Attribute name" style="width: 90%;"></td>
						</tr>
						<tr><td colspan="2"><p><span style="color:#008000;"><strong><em>Custom LDAP to User Meta Mapping is present in the <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank" rel="noopener">Premium Version</a> of the plugin.</em></strong></span></p></em></td></tr>
						<tr id="ldap_to_usermeta_rows">
							<td style="padding: 10px;"></td>
						</tr>

						<tr></tr>
						<tr></tr>

						<tr id="save_config_element">
							<td style="padding-top: 10px;">
								<input type="submit" style="font-weight:500;" value="Save Configuration" class="button button-primary-ldap button-large" />
							</td>
						</tr>

					</table>
				</form>
			</div><br>
			<div id="attribiteconfigtest_ldap" style="background: white;position: relative;border-radius: 10px; ">
				<form method="post" id="attribiteconfigtest">
					<input type="hidden" name="option" value="mo_ldap_test_attribute_configuration" />
					<table id="attributes_table" class="mo_ldap_settings_table" aria-hidden="true">
						<tr><h3 class="mo_ldap_left">Test Attribute Configuration</h3></tr>
						<tr>Enter LDAP username to test attribute configuration</tr>
						<tr>
							<td><strong>Username</strong></td>
							<td><input type="text" id="mo_ldap_username" name="mo_ldap_username" required placeholder="Enter Username" style="width:61%;" />
						</tr>
						<tr>
							<?php
							$search_base_string = get_option( 'mo_ldap_local_search_base' ) ? MO_LDAP_Utility::decrypt( get_option( 'mo_ldap_local_search_base' ) ) : '';
							$search_bases       = explode( ';', $search_base_string );
							?>
							<td style="padding-top: 10px;"><input type="submit" style="font-weight:500;
							<?php
							if ( empty( $search_bases[0] ) ) {
								echo 'pointer-events: none;'; }
							?>
							" 
	<?php
	if ( empty( $search_bases[0] ) ) {
								echo 'disabled'; }
	?>
	value="Test Configuration" class="button button-primary-ldap button-large" /></td>
						</tr>
						<tr style="color: red;"><td>
						<?php
						if ( empty( $search_bases[0] ) ) {
							echo 'Please Check your LDAP User mapping configuration.'; }
						?>
						</td></tr>
					</table>
				</form>
			</div>
			<script>
				jQuery("#attribiteconfigtest").submit(function(event ) {
					event.preventDefault();
					testConfiguration();
				});

				function testConfiguration(){

					var nonce = "<?php echo esc_attr( wp_create_nonce( 'testattrconfig_nonce' ) ); ?>";

					var username = jQuery("#mo_ldap_username").val();
					var myWindow = window.open('<?php echo esc_url( site_url() ); ?>' + '/?option=testattrconfig&user='+username + '&_wpnonce='+nonce, "Test Attribute Configuration", "width=700, height=600");
				}
			</script>
		</div>
	<?php
}
?>
