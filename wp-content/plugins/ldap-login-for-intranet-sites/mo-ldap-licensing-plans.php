<?php
/**
 * This file contains function to display licensing plans.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding Required files.

require_once dirname( __FILE__ ) . '/class-mo-ldap-addon-list-content.php';
require_once dirname( __FILE__ ) . '/class-mo-ldap-license-plans-pricing.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-plugin-constants.php';


/**
 * Function mo_ldap_show_licensing_page : Display Framework of licensing plans page.
 *
 * @return void
 */
function mo_ldap_show_licensing_page() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$sitetype    = array_key_exists( 'sitetype', $_GET ) ? sanitize_key( $_GET['sitetype'] ) : 'singlesite'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking the sub-tab name, doesn't require nonce verification.
	echo '<style>.update-nag, .updated, .error, .is-dismissible, .notice, .notice-error { display: none; }</style>';
	wp_enqueue_style( 'mo_ldap_license_page_style', plugins_url( 'includes/css/mo_ldap_license_page.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
	wp_enqueue_style( 'mo_ldap_grid_layout_license_page', plugins_url( 'includes/css/mo_ldap_licensing_grid.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );
	?>

<div id="navbar">
	<a href="#licensing_plans" id="plans-section" class="navbar-links">Plans</a>
	<a href="#section-features" id="features-section" class="navbar-links">Feature Comparison</a>
	<a href="#upgrade-steps" id="upgrade-section" class="navbar-links">Upgrade Steps</a>
	<a href="#section-addons" id="addons-section" class="navbar-links">Add - Ons</a>
</div>

<script>

	window.onscroll = function() {ldapStickyNavbar()};

	var navbar = document.getElementById("navbar");
	var sticky = navbar.offsetTop;

	function ldapStickyNavbar() {
		if (window.pageYOffset >= sticky) {
			navbar.classList.add("sticky")
		} else {
			navbar.classList.remove("sticky");
		}
	}

</script>

<div style="text-align: center; font-size: 14px; color: white; padding-top: 4px; padding-bottom: 4px; border-radius: 16px;"></div>
<input type="hidden" id="mo_license_plan_selected" value="licensing_plan" />
<div class="tab-content">
	<div class="tab-pane active text-center" id="cloud">
		<div class="cd-pricing-container cd-has-margins" style="max-width: unset">
			<div id="mo_ldap_local_licensing_plans_section" onmouseenter="onMouseEnterPlans()">
				<div class="ldap_center_div" style="text-align: center;background-color:#f9f9f9;padding-top:30px;"  id="licensing_plans">
					<div class="ldap_heading" style="display: inline-block;">
						<br>
						<h1 class="license_title"><a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#pricing" rel="noopener" target="_blank">Choose Your Licensing Plan</a></h1>
						<br>
					</div>
					<div style="display: flex;justify-content: center;position: relative;line-height:28px;">
						<h4 style="font-size: 20px;color: red;text-align:center;">Are you not able to choose your plan?</h4> 
						<a class="button button-primary" style="font-size:15px; position: absolute;margin-left:500px;" id="licensingContactUs" href="#licensingContactUs">Contact Us</a>
					</div>
					<?php
						$troubleshooting_url = add_query_arg( array( 'tab' => 'troubleshooting' ), $request_uri );
					?>
					<div class="mo_ldap_local_know_more_multisite">
						Want to know more about WordPress Instance, Subsites & Multisite Network? <a href="<?php echo esc_url( $troubleshooting_url ); ?>">Click Here</a>
					</div>
				</div>
				<div class="cd-pricing-switcher" >
					<p class="fieldset" style="background-color: #6292FF;">
						<input type="radio" name="sitetype" value="singlesite" id="singlesite" <?php echo esc_attr( 0 === strcasecmp( $sitetype, 'multisite' ) ? '' : 'checked' ); ?>>
						<label for="singlesite">SINGLE SITE</label>
						<input type="radio" name="sitetype" value="multisite" id="multisite" <?php echo esc_attr( 0 === strcasecmp( $sitetype, 'multisite' ) ? 'checked' : '' ); ?>>
						<label for="multisite">MULTI SITE</label>
					</p>
				</div>

				<script>
					var selectArray = JSON.parse('<?php echo wp_json_encode( new MO_LDAP_License_Plans_Pricing() ); ?>');

					function createSelectOpt(elemId) {
						var selectPricingArray = selectArray[elemId];
						var selectElem = '<span class="cd-currency">$</span><span class="cd-value" id="standardID">' + selectArray[elemId]["1"] + '</span></div><h3 class="instanceClass" >No. of instances:';
						var selectElem =selectElem + ' <select class="no_instance" required="true" onchange="changePricing(this)" id="' + elemId + '">';
						jQuery.each(selectPricingArray, function (instances, price) {
							selectElem = selectElem + '<option value="' + instances + '" data-value="' + instances + '">' + instances + ' </option>';
						})
						selectElem = selectElem + '</select><hr class="plan-seprator">';
						return document.write(selectElem);
					}

					function createSelectWithSubsitesOpt(elemId) {
						var selectPricingArray = selectArray[elemId];
						var selectSubsitePricingArray = selectArray['subsite_intances'];
						let newPricing = parseInt(selectArray[elemId]["1"])+60;
						var selectElem = '<span class="cd-currency">$</span><span class="cd-value" id="standardID">' + newPricing + '</span></div><h3 class="instanceClass" >No. of instances:';
						var selectElem =selectElem + ' <select class="no_instance" required="true" onchange="changePricing(this)" id="' + elemId + '">';
						jQuery.each(selectPricingArray, function (instances, price) {
							selectElem = selectElem + '<option value="' + instances + '" data-value="' + instances + '">' + instances + ' </option>';
						})
						selectElem = selectElem + "</select></h3>";
						selectElem = selectElem + '<h3 class="subsiteClass" >No. of subsites:&nbsp&nbsp';
						selectElem = selectElem + '<select class="no_instance" required="true" onchange="changePricing(this)" id="' + elemId + '" name="' + elemId + '-subsite">';
						let count = 0;
						jQuery.each(selectSubsitePricingArray, function (instances, price) {
							let selected = "";
							if(count == 1) {
								selected = "selected";
							}
							selectElem = selectElem + '<option value="' + instances + '" data-value="' + instances + '" '+ selected +'>' + instances + ' </option>';
							count++;
						})
						selectElem = selectElem + '</select><hr class="plan-seprator">';
						return document.write(selectElem);
					}

					function changePricing($this) {
						var selectId = jQuery($this).attr("id");
						var selectSubsiteValue = jQuery("select[name=" + selectId + "-subsite]").val();
						var e = document.getElementById(selectId);
						var strUser = e.options[e.selectedIndex].value;
						var strUserInstances = strUser != "UNLIMITED" ? strUser : 500;
						selectArrayElement = [];
						selectSubsiteArrayElement = selectArray.subsite_intances[selectSubsiteValue];
						if (selectId == "pricing_custom_profile") {
							selectArrayElement = selectArray.pricing_custom_profile[strUser];
							jQuery("#" + selectId).parents("div.individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "pricing_kerberos") {
							selectArrayElement = selectArray.pricing_kerberos[strUser];
							jQuery("#" + selectId).parents("div.individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "pricing_standard") {
							selectArrayElement = selectArray.pricing_standard[strUser];
							jQuery("#" + selectId).parents("div.individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "pricing_enterprise") {
							selectArrayElement = selectArray.pricing_enterprise[strUser];
							jQuery("#" + selectId).parents("div.individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "mulpricing_custom_profile") {
							selectArrayElement = parseInt(selectArray.mulpricing_custom_profile[strUser].replace(",", "")) + parseInt(parseInt(selectSubsiteArrayElement) * parseInt(strUserInstances));
							jQuery("#" + selectId).parents("div.mul-individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "mulpricing_kerberos") {
							selectArrayElement = parseInt(selectArray.mulpricing_kerberos[strUser].replace(",", "")) + parseInt(parseInt(selectSubsiteArrayElement) * parseInt(strUserInstances));
							jQuery("#" + selectId).parents("div.mul-individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "mulpricing_standard") {
							selectArrayElement = parseInt(selectArray.mulpricing_standard[strUser].replace(",", "")) + parseInt(parseInt(selectSubsiteArrayElement) * parseInt(strUserInstances));
							jQuery("#" + selectId).parents("div.mul-individual-container").find(".cd-value").text(selectArrayElement);
						}
						if (selectId == "mulpricing_enterprise") {
							selectArrayElement = parseInt(selectArray.mulpricing_enterprise[strUser].replace(",", "")) + parseInt(parseInt(selectSubsiteArrayElement) * parseInt(strUserInstances));
							jQuery("#" + selectId).parents("div.mul-individual-container").find(".cd-value").text(selectArrayElement);
						}
					}
				</script>
				<div class="section-plans" id="section-plans" > 
					<div class="plan-boxes">
						<input type="hidden" value="<?php echo esc_attr( MO_LDAP_Utility::is_customer_registered() ); ?>" id="mo_customer_registered">
						<ul class="cd-pricing-list cd-bounce-invert">
							<div id="singlesite_plans" style="<?php echo esc_attr( 0 === strcasecmp( $sitetype, 'multisite' ) ? 'display:none' : 'display:grid' ); ?>; grid-template-columns: repeat(4, 1fr);grid-gap: 20px; padding-bottom: 15px;">
								<li class="ldap_li mo_ldap_single_site_plan">
									<ul class="cd-pricing-wrapper">
										<li name="listPlans" data-type="singlesite" id="standard" class="mosslp mo_ldap_local_plans_border">
											<div id="0" class="individual-container">
												<a id="popover1" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_essential_plan">
															<h2 class="plan_name">Essential Authentication Plan</h2>
															<script>
																createSelectOpt('pricing_custom_profile');
															</script>

														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Full Featured Premium LDAP Plugin</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_intranet_premium_plan')"><span>Buy Now</span></a>
											</div>
										</li>

									</ul>
								</li>

								<li class="ldap_li">
									<ul class="cd-pricing-wrapper ">
										<li name="listPlans" data-type="singlesite" id="standard" class="mosslp mo_ldap_local_plans_border">
											<div id="1" class="individual-container">
												<a id="popover2" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_kerberos_plan">
															<h2 class="plan_name">Kerberos / NTLM SSO Plan</h2>
															<script>
																createSelectOpt('pricing_kerberos');
															</script>

														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Essential Authentication plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i>Kerberos/NTLM SSO Add-On</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_ntlm_sso_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>

								<li class="ldap_li">
									<ul class="cd-pricing-wrapper">
										<li data-type="singlesite" id="standard" class="mosslp mo_ldap_local_plans_border">
											<div id="2" class="individual-container ">
												<a id="popover3" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_advance_sync_plan">
															<h2 class="plan_name">Advanced Syncing & Authentication Plan</h2>
															<script>
																createSelectOpt('pricing_standard');
															</script>

														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3>
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Kerberos/NTLM SSO plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Sync Users LDAP Directory Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Password Sync with LDAP Server Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Profile Picture Sync Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> WP Groups Plugin Integration</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_standard_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>

								<li class="ldap_li">
									<ul class="cd-pricing-wrapper">
										<li data-type="singlesite" id="standard" class="mosslp mo_ldap_local_plans_border">
											<div id="2" class="individual-container ">
												<a id="popover3" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_all_inclusive_plan">
															<h2 class="plan_name">All Inclusive Plan</h2>
															<script>
																createSelectOpt('pricing_enterprise');
															</script>
														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3>
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Advanced Authentication & Syncing Plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Page/Post Restriction Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> WP-CLI Integration Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Search Staff from LDAP Directory Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> BuddyPress Profile Integration</h3>
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> All Third Party App Integrations</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_all_inclusive_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>
							</div>

							<div id="multisite_plans" style="<?php echo esc_attr( 0 === strcasecmp( $sitetype, 'multisite' ) ? 'display:grid' : 'display:none' ); ?>;grid-template-columns: repeat(4, 1fr);grid-gap: 20px; padding-bottom: 15px;">
								<li class="ldap_li_mul">
									<ul class="cd-pricing-wrapper">
										<li name="listPlans" data-type="multisite" id="multisite" class="momslp mo_ldap_local_plans_border">
											<div id="0" class="mul-individual-container">
												<a id="popover1" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_essential_plan">
															<h2 class="mul_plan_name">Multisite Essential <br> Authentication <br> Plan</h2>
															<script>
																createSelectWithSubsitesOpt('mulpricing_custom_profile');
															</script>
														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Full Featured Premium LDAP Plugin</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_intranet_multisite_premium_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>

								<li class="ldap_li_mul">
									<ul class="cd-pricing-wrapper ">
										<li name="listPlans" data-type="multisite" id="multisite" class="momslp mo_ldap_local_plans_border">
											<div id="1" class="mul-individual-container">
												<a id="popover2" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_kerberos_plan">
															<h2 class="mul_plan_name" style="margin-bottom: 10px">Multisite Kerberos / NTLM SSO Plan</h2>
															<script>
																createSelectWithSubsitesOpt('mulpricing_kerberos');
															</script>
														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Essential Authentication plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i>Kerberos/NTLM SSO Add-On</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_ntlm_sso_multisite_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>

								<li class="ldap_li_mul">
									<ul class="cd-pricing-wrapper">
										<li data-type="multisite" id="multisite" class="momslp mo_ldap_local_plans_border">
											<div id="2" class="mul-individual-container">
												<a id="popover3" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_advance_sync_plan">
															<h2 class="mul_plan_name" style="margin-bottom:10px;">Multisite Advanced Syncing & Authentication Plan</h2>
															<script>
																createSelectWithSubsitesOpt('mulpricing_standard');
															</script>
														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Kerberos/NTLM SSO plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Sync Users LDAP Directory Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Password Sync with LDAP Server Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Profile Picture Sync Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> WP Groups Plugin Integration</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_standard_multisite_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>

								<li class="ldap_li_mul">
									<ul class="cd-pricing-wrapper">
										<li data-type="multisite" id="multisite" class="momslp mo_ldap_local_plans_border">
											<div id="2" class="mul-individual-container">
												<a id="popover3" data-toggle="popover">
													<header class="cd-pricing-header">
														<div class="mo_ldap_all_inclusive_plan">
															<h2 class="mul_plan_name" style="margin-bottom:10px;">Multisite All Inclusive Plan</h2>
															<script>
																createSelectWithSubsitesOpt('mulpricing_enterprise');
															</script>
														<div class="subheading">
															<h3 class="subheading_plan">Comes With</h3> 
														</div>
														<div style="padding: 2rem;" class="mo_ldap_licensing_plan_features_desc">
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Everything from the Advanced Authentication & Syncing Plan</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Page/Post Restriction Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> WP-CLI Integration Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> Search Widget Add-On</h3>
															<h3 class="plan_features"><i class="fa fa-check-circle" style="color:#00be0d;"></i> BuddyPress Profile Integration</h3>
															<h3 class="plan_features" style="font-weight: 700;"><i class="fa fa-check-circle" style="color:#00be0d;"></i> All Third Party App Integrations</h3>
														</div>
													</header>
												</a>
											</div>
											<div class="plans-buy-now-footer plans-buy-now-footer_single_site"> 
												<p class="plugin_updates">Free Plugin Updates for 1 Year</p>
												<a href="#" class="plans-buy-now" onclick="upgradeform('wp_ldap_all_inclusive_multisite_bundled_plan')">Buy Now</a> 
											</div>
										</li>
									</ul>
								</li>
							</div>
						</ul>
					</div>
				</div>
				<div class="mo_ldap_local_license_details"> <h3>For more details please <a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#pricing" rel="noopener" target="_blank">click here</a></h3> </div>
			</div>
			<div id="mo_ldap_local_licensing_features_section" onmouseenter="onMouseEnterFeatures()">
				<div class="PricingCard-toggle ldap-plan-title feature-section-heading" id="section-features" >
					<h2 class="mo-ldap-h2"> Features Comparison</h2>
				</div>
				<div class="section-features" >
					<div class="collapse" id="collapseExample" style="width:90%;">
						<table class="FeatureList" aria-hidden="true">
							<tr id="feature_list">
								<th id="premium_plans_feature_list" style="color: white;"> Add-Ons List </th>
								<th id="basic_ldap_auth_plan" style="color: white;"> Essential Authentication Plan </th>
								<th id="kerberos_bundled_plan" style="color: white;"> Kerberos / NTLM SSO Plan </th>
								<th id="standard_plan" style="color: white;"> Advanced Syncing & Authentication Plan </th>
								<th id="enterprise_plan" style="color: white;"> All Inclusive Plan </th>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle table-plus-icon" aria-hidden="true"></i>&nbsp;&nbsp;LDAP Plugin
									</div>
									<div id="plugin-features" class="plugin-features-class" style="display:none;">
										<ul>
										<li class="add-on-comp-list-item"> &#8226; Custom WordPress Profile Mapping.</li>
										<li class="add-on-comp-list-item"> &#8226; Assign WordPress roles based on LDAP groups.</li>
										<li class="add-on-comp-list-item"> &#8226; Support for fetching LDAP groups automatically for performing role mapping.</li>
										<li class="add-on-comp-list-item"> &#8226; Authenticate users from Multiple LDAP Search Bases.</li>
										<li class="add-on-comp-list-item"> &#8226; Support for automatic selection of LDAP OU's as a search base.</li>
										<li class="add-on-comp-list-item"> &#8226; Automatic Custom Search filter builder with group restriction.</li>
										<li class="add-on-comp-list-item"> &#8226; Authenticate users from both LDAP Active Directory and WordPress database.</li>
										<li class="add-on-comp-list-item"> &#8226; WordPress to LDAP user profile update.</li>
										<li class="add-on-comp-list-item"> &#8226; Auto-registration of users present in your LDAP Active Directory to your WordPress website.</li>
										<li class="add-on-comp-list-item"> &#8226; Redirect users to a custom URL after LDAP Authentication.</li>
										<li class="add-on-comp-list-item"> &#8226; Support for LDAPS for Secure Connection to your LDAP Server.</li>
										<li class="add-on-comp-list-item"> &#8226; Generate detailed user authentication reports.</li>
										<li class="add-on-comp-list-item"> &#8226; Support for Import/Export plugin configuration into another WordPress instance.</li>
										</ul>
									</div>
								</td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
								<div class="plugin-data" id="plugin-data" onclick="showData(this)">
									<i class="fa fa-plus-circle table-plus-icon" aria-hidden="true"> </i>&nbsp;&nbsp;Kerberos/NTLM SSO

									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display:none ;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Enable Auto-login/SSO for your WordPress website on a domain joined machine.</li>
										</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td"> 
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
									<i class="fa fa-plus-circle table-plus-icon" aria-hidden="true"></i>&nbsp;&nbsp;Sync Users LDAP Directory
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Sync users from your Active Directory/LDAP Server to your WordPress Database and vice-versa.</li>
											<li class="add-on-comp-list-item"> &#8226; Option to sync users from multiple Search Base(s), customized Search Filter, and Username.</li>
											<li class="add-on-comp-list-item"> &#8226; The LDAP Active Directory to WordPress User Profile sync can be scheduled on an hourly, daily, or twice a day basis.</li>
											<li class="add-on-comp-list-item"> &#8226; Unsync users from WordPress if deleted in your Active Directory/LDAP Server and vice-versa.</li>
											<li class="add-on-comp-list-item"> &#8226; Update the User Profile in Active Directory/LDAP when it is updated on your WordPress website.</li>
											<li class="add-on-comp-list-item"> &#8226; Register users in Active Directory/LDAP Server when they register into your WordPress website.</li>
											<li class="add-on-comp-list-item"> &#8226; Update users' Group in Active Directory/LDAP Server when the WordPress role is changed.</li>
										</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
								<div class="plugin-data" id="plugin-data" onclick="showData(this)">
									<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Password Sync with LDAP Server
								</div>
								<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Updates your user password in the LDAP directory whenever the user updates/ resets the password in the WordPress database.</li>
										<li class="add-on-comp-list-item"> &#8226; User passwords can be changed or reset via the WordPress user profile area.</li>
										<li class="add-on-comp-list-item"> &#8226; User passwords can be changed or reset via the WordPress login page.</li>
									</ul>
								</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
								<div class="plugin-data" id="plugin-data" onclick="showData(this)">
									<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Profile Picture Sync
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Map your Active Directory thumbnail photo to your WordPress user profile and vice versa.</li>
										<li class="add-on-comp-list-item"> &#8226; The profile picture sync can be performed two ways/one way as per your choice.</li>
									</ul>
								</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;WP Groups Plugin Integration
										</div>
										<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Import your Active Directory Group's Users to WordPress Groups.</li>
											<li class="add-on-comp-list-item"> &#8226; Import your Active Directory OU's users to WordPress Groups.</li>
											<li class="add-on-comp-list-item"> &#8226; Import your Active Directory users who have specific attribute values to WordPress Groups.</li>
											<li class="add-on-comp-list-item"> &#8226; Ex. Users having the department value "sales" will be added to the WordPress Group of your choice.</li>
										</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Page/Post Restriction
										</div>
										<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Restrict WordPress Pages/Posts based on the roles given to users in the WordPress database.</li>
											<li class="add-on-comp-list-item"> &#8226; Restrict categorized posts based on WordPress Roles.</li>
											<li class="add-on-comp-list-item"> &#8226; Give access to certain Pages/Posts to logged-in users only.</li>
											<li class="add-on-comp-list-item"> &#8226; Redirect the users to a specific URL when they are restricted to certain pages.</li>
											<li class="add-on-comp-list-item"> &#8226; Display custom messages to users when they are restricted to certain pages.</li>
											<li class="add-on-comp-list-item"> &#8226; Use a customized login page other than the default wp-login.php.</li>
										</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Custom Notification On WordPress Login Page
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Display a custom message to users when they visit your WordPress login page.</li>
									</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Third Party User Profile Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Allows you to integrate the LDAP user profile with the third-party plugin’s user profile.</li>
										<li class="add-on-comp-list-item"> &#8226; It provides a configurable option that allows the user to specify the user meta key for a particular LDAP user attribute.</li>
									</ul>
								</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Search Staff from LDAP Directory
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Search and display your Active Directory users on your WordPress site using a widget.</li>
										<li class="add-on-comp-list-item"> &#8226; Search can be performed using any LDAP attribute.</li>
										<li class="add-on-comp-list-item"> &#8226; You can also search for users present in particular departments.</li>
									</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;BuddyPress Profile Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Sync BuddyPress Extended profile’s attributes with Active Directory attributes.</li>
											<li class="add-on-comp-list-item"> &#8226; Map Active Directory Groups to BuddyPress Groups.</li>
										</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;BuddyBoss Profile Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Sync BuddyBoss extended profile attributes with Active Directory Attributes.</li>
									</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Ultimate Member Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Login into Ultimate Member using your LDAP Active Directory Credentials.</li>
										<li class="add-on-comp-list-item"> &#8226; Map your LDAP Active Directory attributes into your Ultimate Member profile.</li>
									</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;LearnDash Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Import your Active Directory Group's Users to LearnDash Groups.</li>
											<li class="add-on-comp-list-item"> &#8226; Import your Active Directory OU's users to LearnDash Groups.</li>
										</ul>
									</div>
								</td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Gravity Form Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
									<ul>
										<li class="add-on-comp-list-item"> &#8226; Populate your Gravity Form fields with the information present in your Active Directory.</li>
										<li class="add-on-comp-list-item"> &#8226; Supports integration with unlimited forms.</li>
									</ul>
									</div>
								</td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;MemberPress Plugin Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Login into Memberpress’ protected content with your LDAP Credentials.</li>
										</ul>
									</div>
								</td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;Paid Membership Pro Integrator
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; WordPress Paid Memberships Pro Integrator will map the LDAP Security Groups to Paid Memberships Pro groups.</li>
										</ul>
									</div>
								</td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
							<tr>
								<td class="features mo-ldap-add-on-name-td">
									<div class="plugin-data" id="plugin-data" onclick="showData(this)">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;&nbsp;eMember Plugin Integration
									</div>
									<div id="plugin-features" class="plugin-features-class" class="plugin-features" style="display: none;">
										<ul>
											<li class="add-on-comp-list-item"> &#8226; Login to eMember Profiles with LDAP Credentials.</li>
										</ul>
									</div>
								</td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features"><em class="fas fa-times" aria-hidden="true" style="color: red"></em></td>
								<td class="features mo-ldap-add-on-comp-x-icon"><em class="fas fa-times" aria-hidden="true"></em></td>
								<td class="features"><em class="fas fa-check"></em></td>
							</tr>
						</table>
					</div>
				</div>

				<script>
					function showData(e){
						var parent = e.parentElement
						var x = GetElementInsideContainer(parent, "plugin-features");
						if(x.style.display == "none"){
							x.style.display = "block";
						}else{
							x.style.display = "none";
						}
					}
					function GetElementInsideContainer(parentElement, childID) {
						var elm = {};
						var elms = parentElement.getElementsByTagName("*");
						for (var i = 0; i < elms.length; i++) {
							if (elms[i].id === childID) {
								elm = elms[i];
								break;
							}
						}
						return elm;
					}
				</script>
				<div class="PricingCard-toggle ldap-plan-title mul-dir-heading">
					<h2 class="mo-ldap-h2">Multiple Directories Plan</h2>
				</div>
				<div class="multiple-dir-text">
					<h2 style="padding:0 20px;width:30%;text-align:left;">Looking for LDAP Authentication against more than one LDAP Server?</h2>
					<p style="margin-left:80px;font-size:18px;color:green; font-style: italic;font-weight:600;width:40%;"> We do support LDAP authentication from multiple LDAP directories in our Multiple LDAP Directories Plan. To get more details on this plan please <a id="MultipleDirContactUs" href="#MultipleDirContactUs">contact us</a>.</p>
				</div>
			</div>
			<div id="mo_ldap_local_licensing_upgrade_section" onmouseenter="onMouseEnterUpgrade()">
				<div class="PricingCard-toggle ldap-plan-title mul-dir-heading" id="upgrade-steps">
					<h2 class="mo-ldap-h2">How to upgrade to premium</h2>
				</div>
				<section class="section-steps"  id="section-steps">
					<div class="row">
						<div class="col span-1-of-2 steps-box">
							<div class="works-step">
								<div>1</div>
								<p>
									Click on Buy Now button for required premium plan and you will be redirected to <strong> miniOrange login console.</strong>
								</p>
							</div>
							<div class="works-step">
								<div>2</div>
								<p>
									Enter your username and password with which you have created an account with us. After that you will be redirected to payment page.
								</p>
							</div>
							<div class="works-step">
								<div>3</div>
								<p>
									Enter your card details and proceed for payment. On successful payment completion, the premium plugin(s) and add-on(s) will be available to download.
								</p>
							</div>
							<div class="works-step">
								<div>4</div>
								<p>
									Download the premium plugin(s) and add-on(s) from Plugin Releases and Downloads section.
								</p>
							</div>
						</div>
						<div class="col span-1-of-2 steps-box">
							<div class="works-step">
								<div>5</div>
								<p>
									From the WordPress admin dashboard, delete the free plugin currently installed.
									&nbsp;            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
								</p>
							</div>
							<div class="works-step">
								<div>6</div>
								<p style="padding-top:10px;">
									Unzip the downloaded premium plugin and extract the files. <br> <br>
								</p>
							</div>
							<div class="works-step">
								<div>7</div>
								<p>
									Upload the extracted files using FTP to path /wp-content/plugins/. Alternately, go to Add New → Upload Plugin in the plugin's section to install the .zip file directly.<br>
								</p>
							</div>
							<div class="works-step">
								<div>8</div>
								<p>
									After activating the premium plugin, login using the account you have registered with us.
								</p>
							</div>
						</div>
					</div>
					<div class="row" style="font-size:16px;padding-bottom:25px;">
						<strong>Note: </strong>The premium plans are available in the miniOrange dashboard. Please don't update the premium plugin from the WordPress Marketplace. We'll notify you via email whenever a newer version of the plugin is available in the miniOrange dashboard.
					</div>    
				</section>
			</div>
			<div class="PricingCard-toggle ldap-plan-title yt-video-heading">
				<h2 class="mo-ldap-h2"> Watch Choose miniOrange LDAP/AD Solution </h2>
			</div>
			<div class="section-license-page-video">
				<iframe width="560" height="315" title="LDAP add-ons" src="https://www.youtube.com/embed/VdAIDLCN-cQ?si=R8kDlM8vxnZtwSTA" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>

			<div class="PricingCard-toggle ldap-plan-title yt-video-heading">
				<h2 class="mo-ldap-h2"> Watch Premium Version Features</h2>
			</div>
			<div class="section-license-page-video">
				<iframe width="560" height="315" title="LDAP add-ons" src="https://www.youtube.com/embed/r0pnB2d0QP8" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>

			<script>

				jQuery('a[id=MultipleDirContactUs]').click(
					function(){
						jQuery('#licensingContactUsModal').show();
						jQuery("#contact_us_title").text("Contact Us for LDAP Multiple Directories Premium Plan");
						query = "Hi!! I am interested in LDAP Multiple Directories Premium Plan and want to know more about it.";
						jQuery("#mo_ldap_licensing_contact_us #query").val(query);
					});

				jQuery('#multiple_ldap_directories_contact_us_close').click(
					function(){
						jQuery("#mo_ldap_licensing_contact_us #query").val('');
						jQuery('#licensingContactUsModal').hide();
					});
			</script>
			<div id="mo_ldap_local_licensing_addons_section" onmouseenter="onMouseEnterAddons()">
				<div class="cd-pricing-container cd-has-margins" style="max-width: unset">
					<?php $adddon_obj = new MO_LDAP_Addon_List_Content(); ?>
					<div class="section-addons" id="section-addons" >
						<h2 class="mo-ldap-h2">Premium Add-ons </h2>
						<div>
							<p style="font-size:16px;font-weight:500;margin-bottom:30px;text-align:center;color:#000;"> (Requires Essential Authentication Plan) </p>
						</div>
						<div class="premium-addons" >
							<input type="hidden" value="<?php echo esc_attr( MO_LDAP_Utility::is_customer_registered() ); ?>" id="mo_customer_registered">
							<?php
								$adddon_obj->show_addons_content( true );
							?>
						</div>
						<h2 class="mo-ldap-h2">Premium Add-ons for Integration with Third Party Plugins</h2>
						<div>
							<p style="font-size:16px;font-weight:500;margin-bottom:30px;text-align:center;color:#000;"> (Requires Essential Authentication Plan) </p>
						</div>
						<div class="premium-addons" >
							<input type="hidden" value="<?php echo esc_attr( MO_LDAP_Utility::is_customer_registered() ); ?>" id="mo_customer_registered">
							<?php
							$adddon_obj->show_addons_content( false );
							?>
						</div>
					</div>
				</div>

					<section class="payment-methods">
						<div class="row">
							<h2 class="mo-ldap-h2">Supported Payment Methods</h2>
						</div>
						<div class="row">
							<div class="col span-1-of-3">
								<div class="plan-box">
									<div>
										<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>includes/images/cards.png)" width="95%;" height="105%" alt="">
									</div>
									<div>
										If the payment is made through Credit Card/International Debit Card, the license will be created automatically once the payment is completed.
									</div>
								</div>
							</div>
							<div class="col span-1-of-3">
								<div class="plan-box">
									<div>
										<img class="payment-images" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/css/images/paypal.png' ); ?>" alt="image not found">
									</div>
									<div>
										Use the following PayPal ID <strong>info@xecurify.com</strong> for making the payment via PayPal.<br><br>
									</div>
								</div>
							</div>
							<div class="col span-1-of-3">
								<div class="plan-box">
									<div>
										<em style="font-size:30px;" class="fas fa-university" aria-hidden="true"><span style="font-size: 20px;font-weight:500;">&nbsp;&nbsp;Bank Transfer</span></em>
									</div>
									<div>
										If you want to use bank transfer for the payment then contact us at <span style="color:blue;text-decoration:underline; word-wrap: break-word;">ldapsupport@xecurify.com</span>  so that we can provide you the bank details.
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<p style="margin-top:20px;font-size:16px;">
								<span style="font-weight:500;"> Note :</span> Once you have paid through PayPal/Net Banking, please inform us so that we can confirm and update your license.
							</p>
						</div>
					</section>

					<section class="testimonials">
						<div class="row">
							<h2 class="mo-ldap-h2">What Our Customers Say</h2>
						</div>
						<div class="row slideshow-container">
							<div class="row review-slides1">
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> The BEST plugin for LDAP AD integration</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-left: 50px;">
										<p class="review-para">I’ve been looking for a multi-domain plugin for a very long time. The closest competitor of NADI asked just indecent money for a multi-domain. Using it is very simple and the support responds very quickly. I recommend it for use..</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/the-best-plugin-for-ldap-ad-integration/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> Great functioning, Great support</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides">
										<p class="review-para">We had customizations done in LDAP search widget tool – a useful add-on for LDAP login plugin – with which the support help us. The support team is very professional and prepared for problem solving. It was also a great advantage that we could easily arrange online calls and solve problems on the spot.</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/great-functioning-great-support/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> LDAP for intranet Microsoft Active Directory</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-right: 50px;">
										<p class="review-para">This is exactly what it says it does, gives you possibility to use MS AD user, password, and all other attributes (name, phone, postal code) for your intranet or any other site with users to avoid creating users/passwords.</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/ldap-for-intranet-microsoft-active-directory/">See Full Review</a>
									</div>
								</div>
							</div>
							<div class="row review-slides1">
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading">Easy to use and great support</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-left: 50px;">
										<p class="review-para">Just using the free version at the moment as it suits our current needs but it’s quite likely that we will move to the paid for version once the project takes off. It was a really quick and easy to setup.</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/easy-to-use-and-great-support-68/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading">I recommend this plugin</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides">
										<p class="review-para">The plugin is very easy to use, the examples on you Tube and the plugin itself help a lot in the configuration. In addition to the remote support that helped solve a small synchronization problem in my LDAP. I recommend this plugin.</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/i-recommend-this-plugin-13/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> Great Support & Great Features</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-right: 50px;">
										<p class="review-para">At first, I was a litte unsure about the features provided with this plugin – but then just mailed their support team and wow: They are working fast and are really trying to get you all the information needed. </p>                                        
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/great-support-great-features-4/">See Full Review</a>
									</div>
								</div>
							</div>
							<div class="row review-slides1">
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> Very good plugin and support</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-left: 50px;">
										<p class="review-para">Very good plugin to use for LDAP login; I use the multiple domains versions with add-on to sync users metadata from LDAP and it works so well. The support is also quick and prepared, if it comes some problem on the way. </p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/very-good-plugin-and-support-37/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading"> Simply Perfect!</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides">
										<p class="review-para">It’s work like a charm with AD. I had a problem at the beginning, but they provided a fast, efective and friendly support. Totally recommended. </p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/simply-perfect-104/">See Full Review</a>
									</div>
								</div>
								<div class="col span-1-of-3">
									<div class="row review-slides">
										<h4 class="review-heading">Great plugin…Great support</h4>
									</div>
									<div class="row review-slides">
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
										<em class="fas fa-star star-icon"></em>
									</div>
									<div class="row review-slides" style="padding-right: 50px;">
										<p class="review-para">Highly recommend. Presale support is unmatched and found solutions to work with our host. Feel confident in knowing this plugin will work without the hassle. Top notch!</p>
									</div>
									<div class="row review-slides">
										<a class="button button-primary" target="_blank" rel="noopener" href="https://wordpress.org/support/topic/great-plugin-great-support-1454/">See Full Review</a>
									</div>
								</div>
							</div>

							<a class="prev" onclick="plusSlides(-1)">❮</a>
							<a class="next" onclick="plusSlides(1)">❯</a>

							<div class="dot-container">
								<span class="dot" onclick="currentSlide(1)"></span> 
								<span class="dot" onclick="currentSlide(2)"></span> 
								<span class="dot" onclick="currentSlide(3)"></span> 
							</div>
						</div>
					</section>

					<script>
						var slideIndex = 1;
						showSlides(slideIndex);

						function plusSlides(n) {
						showSlides(slideIndex += n);
						}

						function currentSlide(n) {
							showSlides(slideIndex = n);
						}

						function showSlides(n) {
							var i;
							var slides = document.getElementsByClassName("review-slides1");
							var dots = document.getElementsByClassName("dot");
							if (n > slides.length) 
							{
								slideIndex = 1
							}    
							if (n < 1) 
							{
								slideIndex = slides.length
							}
							for (i = 0; i < slides.length; i++) 
							{
								slides[i].style.display = "none";  
							}
							for (i = 0; i < dots.length; i++) 
							{
								dots[i].className = dots[i].className.replace(" active-slide", "");
							}
							slides[slideIndex-1].style.display = "block";  
							dots[slideIndex-1].className += " active-slide";
						}
					</script>

					<div class="PricingCard-toggle ldap-plan-title mul-dir-heading">
						<h2 class="mo-ldap-h2">10 days Return Policy</h2>
					</div>
					<section class="return-policy">
						<p style="font-size:16px;">
							If the premium plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved, we will refund the whole amount within 10 days of the purchase. <br><br>
							<span style="color:red;font-weight:500;font-size:18px;">Note that this policy does not cover the following cases: </span> <br><br>
							<span> 
								1. Change of mind or change in requirements after purchase. <br>
								2. Infrastructure issues not allowing the functionality to work.
							</span> <br><br>
							Please email us at <a href="mailto:info@xecurify.com">info@xecurify.com</a> for any queries regarding the return policy.
							<a href="#nav-container" class="button button-primary button-large back-to-top" style="font-size:15px;">Top &nbsp;↑</a>
						</p>
					</section>
				<?php
					$current_user = wp_get_current_user();
				if ( get_option( 'mo_ldap_local_admin_email' ) ) {
					$admin_email = get_option( 'mo_ldap_local_admin_email' );
				} else {
					$admin_email = $current_user->user_email;
				}
				?>
				<div hidden id="licensingContactUsModal" name="licensingContactUsModal" class="mo_ldap_modal" style="margin-left: 26%;z-index:11;">
					<div class="moldap-modal-contatiner-contact-us" style="color:black;"></div>
					<div class="mo_ldap_modal-content" id="contactUsPopUp" style="width: 700px; padding:30px;"> <span id="contact_us_title" style="font-size: 22px; margin-left: 50px; font-weight: bold;">Contact Us for Choosing the Correct Premium Plan</span>
						<form name="f" method="post" action="" id="mo_ldap_licensing_contact_us" style="font-size: large;">
							<?php wp_nonce_field( 'mo_ldap_login_send_feature_request_query' ); ?>
							<input type="hidden" name="option" value="mo_ldap_login_send_feature_request_query" />
							<div>
								<p style="font-size: large;">
									<br> <strong>Email: </strong>
									<input style=" width: 77%; margin-left: 69px; " type="email" class="mo_ldap_table_textbox" id="query_email" name="query_email" value="<?php echo esc_attr( $admin_email ); ?>" placeholder="Enter email address through which we can reach out to you" required />
									<br>
									<br> <strong style="display:inline-block; vertical-align: top;">Description: </strong>
									<textarea style="width:77%; margin-left: 21px;" id="query" name="query" required rows="5" style="width: 100%" placeholder="Tell us which features you require"></textarea>
								</p>
								<br>
								<br>
								<div class="mo_ldap_modal-footer" style="text-align: center">
									<input type="button" style="font-size: medium" name="miniorange_ldap_feedback_submit" id="miniorange_ldap_feedback_submit" class="button button-primary button-small" onclick="validateRequirement()" value="Submit" />
									<input type="button" style="font-size: medium" name="miniorange_ldap_licensing_contact_us_close" id="miniorange_ldap_licensing_contact_us_close" class="button button-primary button-small" value="Close" /> 
								</div>
							</div>
						</form>
					</div>
				</div>
				<form style="display:none;" id="loginform" action="<?php echo esc_url( MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME ) . '/moas/login'; ?>" target="_blank" method="post">
					<input type="email" name="username" value="<?php echo esc_attr( get_option( 'mo_ldap_admin_email' ) ); ?>" />
					<input type="text" name="redirectUrl" value="<?php echo esc_attr( MO_LDAP_Plugin_Constants::MO_LDAP_HOST_NAME ) . '/moas/initializepayment'; ?>" />
					<input type="text" name="requestOrigin" id="requestOrigin" /> 
				</form> 
				<a id="mo_backto_ldap_accountsetup_tab" style="display:none;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'account' ), htmlentities( $request_uri ) ) ); ?>">Back</a> 

				<script >
					jQuery('.popupCloseButton').click(function () {
						jQuery('.popup').hide();
					});
					jQuery('.popup').click(function () {
						jQuery('.popup').hide();
					});
					jQuery('a[id=licensingContactUs]').click(function () {
						jQuery('#licensingContactUsModal').show();
						jQuery("#contact_us_title").text("Contact Us for Choosing the Correct Premium Plan");
					});
					jQuery('#miniorange_ldap_licensing_contact_us_close').click(function () {
						jQuery("#mo_ldap_licensing_contact_us #query").val('');
						jQuery('#licensingContactUsModal').hide();
					});
					jQuery(document).ready(function ($) {
						$('#buttonToggleCollapseAddon').click(function () {
							$('#buttonToggleAddon').show();
						});
						$('#buttonToggleThirdPartyAddon').click(function () {
							$('#buttonToggleThirdPartyAddon').hide();
						});
						$('#buttonToggleCollapseThirdParyAddon').click(function () {
							$('#buttonToggleThirdPartyAddon').show();
						});
						$('#sso-mfa-features').click(function () {
							if ($('#show-sso-mfa-features').hasClass('in')) {
								$('#sso-mfa-features-icon').removeClass('arrow-rotate-180').addClass('arrow-rotate-zero');
								$('#sso-mfa-features').text('Show Features');
							} else {
								$('#sso-mfa-features-icon').removeClass('arrow-rotate-zero').addClass('arrow-rotate-180');
								$('#sso-mfa-features').text('Collapse Features');
							}
						});
					});

					function hideElements() {
						jQuery(document).ready(function ($) {
							var x = document.getElementById("myDIV");
							var toggle_button = document.getElementById("toggleBack");
							if (x.style.display === "block") {
								x.style.display = "none";
								toggle_button.style.display = "none";
								$('#toggleBack').removeClass('PricingCard-toggle');
								$('#toggleBack').addClass('PricingCard-toggleBack');
							}
						});
					}
					setTimeout(function () {
						var elmnt = document.getElementById("success");
						var elmnt1 = document.getElementById("error");
						if (elmnt1) {
							jQuery(elmnt1).css("display", "block");
							jQuery(elmnt1).css("margin-top", "1%");
						} else if (elmnt) {
							jQuery(elmnt).css("display", "block");
							jQuery(elmnt).css("margin-top", "1%");
						}
						document.body.scrollTop = 0;
						document.documentElement.scrollTop = 0;
					}, 60);

					function validateRequirement() {
						if (validateEmail()) {
							var requirement = document.getElementById("query").value;
							if (requirement.length <= 10) {
								alert("Please enter more details about your requirement.");
							} else {
								document.getElementById("mo_ldap_licensing_contact_us").submit();
							}
						}
					}

					function validateEmail() {
						var email = document.getElementById('query_email');
						if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value)) {
							return (true)
						} else if (email.value.length == 0) {
							alert("Please enter your email address!")
							return (false)
						} else {
							alert("You have entered an invalid email address!")
							return (false)
						}
					}

					function openSupportForm(planType) {
						query = "Hi!! I am interested in " + planType + " Add-on and want to know more about it.";
						jQuery("#mo_ldap_licensing_contact_us #query").val(query);
						jQuery("a[id='licensingContactUs']").click();
					}

					function upgradeform(planType) {
						if (planType == "ContactUs") jQuery("a[id='licensingContactUs']").click();
						else {
							jQuery('#requestOrigin').val(planType);
							if (jQuery('#mo_customer_registered').val() == 1) jQuery('#loginform').submit();
							else {
								location.href = jQuery('#mo_backto_ldap_accountsetup_tab').attr('href');
							}
						}
					}
					jQuery("input[name=sitetype]:radio").change(function () {
						if (this.value == 'multisite') {
							jQuery('#multisite_plans').addClass('is-visible').css("display", "grid");
							jQuery('#singlesite_plans').removeClass('is-visible').css("display", "none");
						}
						if (this.value == 'singlesite') {
							jQuery('#multisite_plans').removeClass('is-visible').css("display", "none");
							jQuery('#singlesite_plans').addClass('is-visible').css("display", "grid");
						}
					});
					jQuery(document).ready(function ($) {
						if (jQuery('#mo_license_plan_selected').val() == 'multisite') {
							document.getElementById("multisite").checked = true;
						}

						checkScrolling($('.cd-pricing-body'));
						$(window).on('resize', function () {
							window.requestAnimationFrame(function () {
								checkScrolling($('.cd-pricing-body'))
							});
						});
						$('.cd-pricing-body').on('scroll', function () {
							var selected = $(this);
							window.requestAnimationFrame(function () {
								checkScrolling(selected)
							});
						});

						function checkScrolling(tables) {
							tables.each(function () {
								var table = $(this),
									totalTableWidth = parseInt(table.children('.cd-pricing-features').width()),
									tableViewport = parseInt(table.width());
								if (table.scrollLeft() >= totalTableWidth - tableViewport - 1) {
									table.parent('li').addClass('is-ended');
								} else {
									table.parent('li').removeClass('is-ended');
								}
							});
						}
						bouncy_filter($('.cd-pricing-container'));

						function bouncy_filter(container) {
							container.each(function () {
								var pricing_table = $(this);
								var filter_list_container = pricing_table.children('.cd-pricing-switcher'),
									filter_radios = filter_list_container.find('input[type="radio"]'),
									pricing_table_wrapper = pricing_table.find('.cd-pricing-wrapper');
								var table_elements = {};
								filter_radios.each(function () {
									var filter_type = $(this).val();
									table_elements[filter_type] = pricing_table_wrapper.find('li[data-type="' + filter_type + '"]');
								});
								filter_radios.on('change', function (event) {
									event.preventDefault();
									var selected_filter = $(event.target).val();
									show_selected_items(table_elements[selected_filter]);
									pricing_table_wrapper.addClass('is-switched').eq(0).one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
										hide_not_selected_items(table_elements, selected_filter);
										pricing_table_wrapper.removeClass('is-switched');
										if (pricing_table.find('.cd-pricing-list').hasClass('cd-bounce-invert')) pricing_table_wrapper.toggleClass('reverse-animation');
									});
								});
							});
						}

						function show_selected_items(selected_elements) {
							selected_elements.addClass('is-selected');
						}

						function hide_not_selected_items(table_containers, filter) {
							$.each(table_containers, function (key, value) {
								if (key != filter) {
									$(this).removeClass('is-visible is-selected').addClass('is-hidden');
								} else {
									$(this).addClass('is-visible').removeClass('is-hidden is-selected');
								}
							});
						}
					});

					jQuery("#mo_ldap_local_view_more_button").click(function() {
						let viewMoreButton = document.getElementById('mo_ldap_local_view_more_button');
						let contentBoxClasses = document.querySelector('.mo_ldap_local_message').classList;
						if(!contentBoxClasses.contains('mo_ldap_full_height')) {
							viewMoreButton.innerHTML = "<i class='fa fa-angle-double-down'></i>"
						}
						else {
							viewMoreButton.innerHTML = "<i class='fa fa-angle-double-up'></i>"
						}
						contentBoxClasses.toggle('mo_ldap_full_height')
						let titleBoxClasses = document.getElementById('mo_ldap_local_message_title').classList;
						let descBoxClasses = document.getElementById('mo_ldap_local_message_desc').classList;
						titleBoxClasses.toggle('d-none')
						descBoxClasses.toggle('d-none')
					})

				</script>
				<script>
					function onMouseEnterPlans(){
						document.getElementById('plans-section').style.borderBottom = '3px solid #e67e22';
						document.getElementById('features-section').style.borderBottom = 'none';
						document.getElementById('upgrade-section').style.borderBottom = 'none';
						document.getElementById('addons-section').style.borderBottom = 'none';
					}
					function onMouseEnterFeatures(){
						document.getElementById('features-section').style.borderBottom = '3px solid #e67e22';
						document.getElementById('plans-section').style.borderBottom = 'none';
						document.getElementById('upgrade-section').style.borderBottom = 'none';
						document.getElementById('addons-section').style.borderBottom = 'none';
					}
					function onMouseEnterUpgrade(){
						document.getElementById('upgrade-section').style.borderBottom = '3px solid #e67e22';
						document.getElementById('features-section').style.borderBottom = 'none';
						document.getElementById('plans-section').style.borderBottom = 'none';
						document.getElementById('addons-section').style.borderBottom = 'none';
					}
					function onMouseEnterAddons(){
						document.getElementById('addons-section').style.borderBottom = '3px solid #e67e22';
						document.getElementById('features-section').style.borderBottom = 'none';
						document.getElementById('plans-section').style.borderBottom = 'none';
						document.getElementById('upgrade-section').style.borderBottom = 'none';
					}
				</script>
			<?php
}
