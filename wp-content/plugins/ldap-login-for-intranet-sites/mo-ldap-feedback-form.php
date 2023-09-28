<?php
/**
 * This file is used to display the feedback form during plugin deactivation.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Adding Required files.

require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-account-details.php';
require_once dirname( __FILE__ ) . '/includes/lib/class-mo-ldap-plugin-constants.php';

/**
 * Function display_ldap_feedback_form : Display deactivation form framework.
 *
 * @return void
 */
function display_ldap_feedback_form() {
	if ( isset( $_SERVER['PHP_SELF'] ) && 'plugins.php' !== basename( esc_url( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) ) {
		return;
	}

	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );
	wp_enqueue_script( 'utils' );
	wp_enqueue_style( 'mo_ldap_admin_plugins_page_style', plugins_url( 'includes/css/mo_ldap_plugin_style_settings.min.css', __FILE__ ), array(), MO_LDAP_Plugin_Constants::VERSION );

	?>

	</head>
	<body>

<div id="ldapModal" class="mo_ldap_modal_feedback"  style="width:90%; margin-left:12%; margin-top:5%; text-align:center";>
	<div class="mo_ldap_modal_contatiner_feedback" style="color:black"></div>
		<div class="mo_ldap_modal_content_feedback" style="width:50%;">
			<h3 style="margin: 2%; text-align:center;"><strong>Your feedback</strong><button class="close_local_feedback_form" onclick="getElementById('ldapModal').style.display = 'none'">X</button></h3>
			<hr style="width:75%;">
			<div class="request-free-trial-div">
				<a href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites#get-premium-trial" class="request-free-trial-btn" target="_blank">Full-Featured Trial</a>
			</div>
			<form name="f" method="post" action="" id="mo_ldap_feedback">
				<?php wp_nonce_field( 'mo_ldap_feedback' ); ?>
				<input type="hidden" name="option" value="mo_ldap_feedback"/>
				<div>
					<h4 style="text-align:center;">Please help us improve our plugin by giving us your opinion.</h4>
					<div id="smi_rate" style="text-align:center">
						<input type="radio" name="rate" id="angry" value="1"/>
						<label for="angry"><img class="sm" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/angry.png' ); ?>" />
						</label>

						<input type="radio" name="rate" id="sad" value="2"/>
						<label for="sad"><img class="sm" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/sad.png' ); ?>" />
						</label>


						<input type="radio" name="rate" id="neutral" value="3"/>
						<label for="neutral"><img class="sm" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/normal.png' ); ?>" />
						</label>

						<input type="radio" name="rate" id="smile" value="4"/>
						<label for="smile">
							<img class="sm" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/smile.png' ); ?>" />
						</label>

						<input type="radio" name="rate" id="happy" value="5" checked/>
						<label for="happy"><img class="sm" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/happy.png' ); ?>" />
						</label>

						<div id="outer" style="visibility:visible"><span id="result">Thank you for appreciating our work</span></div>
					</div>
					<hr style="width:75%;">
					<?php
					$email = get_option( 'mo_ldap_local_admin_email' );
					if ( empty( $email ) ) {
						$user  = wp_get_current_user();
						$email = $user->user_email;
					}
					?>

					<div style="text-align:center;">
						<div style="display:inline-block; width:60%;">
							<label for="mail"><strong>Email Address:</strong></label>
							<input type="email" id="query_mail" name="query_mail" style="text-align:center; border:0px solid black; border-style:solid; background:#f0f3f7; width:15vw;border-radius: 6px;"
								placeholder="your email address" required value="<?php echo esc_attr( $email ); ?>" readonly="readonly"/>

							<input type="radio" name="edit" id="edit" onclick="editName()" value=""/>
							<label for="edit"><img class="editable" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/61456.png' ); ?>" />
							</label>

						</div>

						<br><br>
						<input type="checkbox" name="get_reply" value="YES" checked>I want to get in touch with your technical team for more assistance.<br> <br><strong>On submitting the feedback, your email address will be shared with the miniOrange team.</strong></input>
						<br><br>
						<textarea id="query_feedback" name="query_feedback" rows="4" style="width: 60%"
							placeholder="Tell us what happened!" ></textarea>
						<br>
						</div><br>
					<div class="mo_ldap_modal-footer" style="text-align: center;margin-bottom: 2%">
						<input type="submit" style="font-weight:500;" name="miniorange_ldap_feedback_submit" id="miniorange_ldap_feedback_submit"
							class="button button-primary-ldap button-large" value="Submit"/>
						<span width="30%">&nbsp;&nbsp;</span>
						<input type="button" name="miniorange_skip_feedback"
							class="button button-large" style="font-weight:500;" value="Skip feedback & deactivate"
							onclick="document.getElementById('mo_ldap_feedback_form_close').submit();"/>
					</div>
				</div>

				<script>

					const INPUTS = document.querySelectorAll('#smi_rate input');
					INPUTS.forEach(el => el.addEventListener('click', (e) => updateValue(e)));


					function editName(){
						document.querySelector('#query_mail').removeAttribute('readonly');
						document.querySelector('#query_mail').focus();
						return false;
					}
					function updateValue(e) {
						document.querySelector('#outer').style.visibility="visible";
						var result = 'Thank you for appreciating our work';
						switch(e.target.value){
							case '1':	result = 'Not happy with our plugin ? Let us know what went wrong';
								break;
							case '2':	result = 'Found any issues? Let us know and we\'ll fix it ASAP';
								break;
							case '3':	result = 'Let us know if you need any help';
								break;
							case '4':	result = 'We\'re glad that you are happy with our plugin';
								break;
							case '5':	result = 'Thank you for appreciating our work';
								break;
						}
						document.querySelector('#result').innerHTML = result;

					}
				</script>
				<style>
					.editable{
						text-align:center;
						width:1em;
						height:1em;
					}
					.sm {
						text-align:center;
						width: 2vw;
						height: 2vw;
						padding: 1vw;
					}

					input[type=radio] {
						display: none;
					}

					.sm:hover {
						opacity:0.6;
						cursor: pointer;
					}

					.sm:active {
						opacity:0.4;
						cursor: pointer;
					}

					input[type=radio]:checked + label > .sm {
						border: 2px solid #21ecdc;
					}
				</style>



			</form>
			<form name="mo_ldap_feedback_form_close" method="post" action="" id="mo_ldap_feedback_form_close">
				<?php wp_nonce_field( 'mo_ldap_skip_feedback' ); ?>
				<input type="hidden" name="option" value="mo_ldap_skip_feedback"/>
			</form>

		</div>

	</div>

	<script>
		var active_plugins = document.getElementsByClassName('deactivate');
		for (i = 0; i<active_plugins.length;i++) {
			var plugin_deactivate_link = active_plugins.item(i).getElementsByTagName('a').item(0);
			var plugin_name = plugin_deactivate_link.href;
			if (plugin_name.includes('plugin=ldap-login-for-intranet-sites')) {
				jQuery(plugin_deactivate_link).click(function () {

				var mo_ldap_modal = document.getElementById('ldapModal');
				var span = document.getElementsByClassName("mo_ldap_close")[0];
				mo_ldap_modal.style.display = "block";
				window.onclick = function (event) {
					if (event.target == mo_ldap_modal) {
						mo_ldap_modal.style.display = "none";
					}
				}
				return false;
				});
				break;
			}
		}
	</script>
	</body>
	<?php
}
?>
