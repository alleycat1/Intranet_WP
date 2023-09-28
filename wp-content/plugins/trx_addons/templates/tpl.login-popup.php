<?php
/**
 * The template to display login popup
 *
 * @package ThemeREX Addons
 * @since v1.6
 */

// Prepare popup
$trx_addons_login_via_socials = do_shortcode(apply_filters('trx_addons_filter_login_via_socials', trx_addons_get_option('login_via_socials')));
$trx_addons_form_style = 'iconed';	//trx_addons_get_option('input_hover');
?>
<div id="trx_addons_login_popup" class="trx_addons_popup mfp-hide">
	<div class="trx_addons_tabs">
		<ul class="trx_addons_tabs_titles"><?php // Attention! No CR or LF between tab titles!
			// Login tab
			?><li class="trx_addons_tabs_title trx_addons_tabs_title_login">
				<a href="<?php echo esc_url(trx_addons_get_hash_link('#trx_addons_login_content')); ?>">
					<i class="trx_addons_icon-lock-open"></i>			
					<?php esc_html_e('Login', 'trx_addons'); ?>
				</a>
			</li><?php
			// Register tab
			?><li class="trx_addons_tabs_title trx_addons_tabs_title_register"<?php
					if ( (int) get_option('users_can_register') == 0 ) {
						echo ' data-disabled="true"';
					}
					?>>
				<a href="<?php echo esc_url(trx_addons_get_hash_link('#trx_addons_register_content')); ?>">
					<i class="trx_addons_icon-user-plus"></i>			
					<?php esc_html_e('Register', 'trx_addons'); ?>
				</a>
			</li><?php
		?></ul><?php
		
		// Login form
		?>
		<div id="trx_addons_login_content" class="trx_addons_tabs_content trx_addons_login_content">
			<div<?php if (!empty($trx_addons_login_via_socials)) echo ' class="trx_addons_left_side"'; ?>>
				<div class="trx_addons_popup_form_wrap trx_addons_popup_form_wrap_login">
					<form class="trx_addons_popup_form trx_addons_popup_form_login <?php
						if ($trx_addons_form_style != 'default') echo 'sc_input_hover_' . esc_attr($trx_addons_form_style);
					?>" action="<?php echo esc_url( trx_addons_add_to_url( wp_login_url(), array( 'rnd' => mt_rand() ) ) ); ?>" method="post" name="trx_addons_login_form">
						<input type="hidden" id="login_redirect_to" name="redirect_to" value="<?php echo esc_url( trx_addons_add_to_url( trx_addons_get_current_url(), array( 'rnd' => mt_rand() ) ) ); ?>">
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_login">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'log',
																'field_type'  => 'text',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-user-alt',
																'field_title' => esc_html__('Username', 'trx_addons'),
																'field_placeholder' => esc_html__('Username', 'trx_addons')
																)
														);
							?>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_password">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'pwd',
																'field_type'  => 'password',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-lock',
																'field_title' => esc_html__('Password', 'trx_addons'),
																'field_placeholder' => esc_html__('Password', 'trx_addons')
																)
														);
							?>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_remember">
							<a href="<?php echo esc_url(wp_lostpassword_url(get_permalink())); ?>" class="trx_addons_popup_form_field_forgot_password"><?php esc_html_e('Forgot password?', 'trx_addons'); ?></a>
							<input type="checkbox" value="forever" id="rememberme" name="rememberme"><label for="rememberme"> <?php esc_html_e('Remember me', 'trx_addons'); ?></label>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_submit">
							<input type="submit" class="submit_button" value="<?php esc_attr_e('Login', 'trx_addons'); ?>">
						</div>
						<div class="trx_addons_message_box sc_form_result"></div>
					</form>
				</div>
			</div>
			<?php if (!empty($trx_addons_login_via_socials)) { ?>
				<div class="trx_addons_right_side">
					<div class="trx_addons_login_socials_title"><?php esc_html_e('or you can login using your favorite social profile!', 'trx_addons'); ?></div>
					<div class="trx_addons_login_socials_list">
						<?php trx_addons_show_layout($trx_addons_login_via_socials); ?>
					</div>
				</div>
			<?php } ?>
		</div><?php
		
		// Registration form
		if ( (int) get_option('users_can_register') > 0 ) {
			?>
			<div id="trx_addons_register_content" class="trx_addons_tabs_content">
				<div class="trx_addons_popup_form_wrap trx_addons_popup_form_wrap_register">
					<form class="trx_addons_popup_form trx_addons_popup_form_register <?php
						if ( $trx_addons_form_style != 'default') echo 'sc_input_hover_' . esc_attr($trx_addons_form_style);
					?>" action="<?php echo esc_url( trx_addons_add_to_url( wp_login_url(), array( 'rnd' => mt_rand() ) ) ); ?>" method="post" name="trx_addons_login_form">
						<input type="hidden" id="register_redirect_to" name="redirect_to" value="<?php echo esc_url( trx_addons_add_to_url( trx_addons_get_current_url(), array( 'rnd' => mt_rand() ) ) ); ?>">
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_login">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'log',
																'field_type'  => 'text',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-user-alt',
																'field_title' => esc_html__('Username', 'trx_addons'),
																'field_placeholder' => esc_html__('Username', 'trx_addons')
																)
														);
							?>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_email">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'email',
																'field_type'  => 'text',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-mail',
																'field_title' => esc_html__('E-mail', 'trx_addons'),
																'field_placeholder' => esc_html__('E-mail', 'trx_addons')
																)
														);
							?>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_password">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'pwd',
																'field_type'  => 'password',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-lock',
																'field_title' => esc_html__('Password', 'trx_addons'),
																'field_placeholder' => esc_html__('Password', 'trx_addons')
																)
														);
							?>
						</div>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_password">
							<?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
															'trx_addons_args_sc_form_field',
															array(
																'style'       => $trx_addons_form_style,
																'field_name'  => 'pwd2',
																'field_type'  => 'password',
																'field_req'   => true,
																'field_icon'  => 'trx_addons_icon-lock',
																'field_title' => esc_html__('Confirm Password', 'trx_addons'),
																'field_placeholder' => esc_html__('Confirm Password', 'trx_addons')
																)
														);
							?>
						</div>
						<?php
						$trx_addons_privacy = trx_addons_get_privacy_text();
						if (!empty($trx_addons_privacy)) {
							?><div class="trx_addons_popup_form_field trx_addons_popup_form_field_agree">
								<input type="checkbox" value="1" id="i_agree_privacy_policy_registration" name="i_agree_privacy_policy"><label for="i_agree_privacy_policy_registration"> <?php echo wp_kses($trx_addons_privacy, 'trx_addons_kses_content'); ?></label>
							</div><?php
						}
						?>
						<div class="trx_addons_popup_form_field trx_addons_popup_form_field_submit">
							<input type="submit" class="submit_button" value="<?php esc_attr_e('Sign Up', 'trx_addons'); ?>"<?php
								if ( false && !empty($trx_addons_privacy) ) {
									?> disabled="disabled"<?php
								}
							?>>
						</div>
						<div class="trx_addons_message_box sc_form_result"></div>
					</form>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>