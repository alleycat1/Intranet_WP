<?php
/**
 * Shortcode: Form
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_form_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_form_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_sc_form_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_form', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.css'), array(), null );
			wp_enqueue_script('trx_addons-sc_form', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.js'), array('jquery'), null, true );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_sc_form_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_form_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_sc_form_load_responsive_styles() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_form-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'sc-form', 'sm' ) 
			);
		}
	}
}
	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_form_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_form_merge_styles');
	function trx_addons_sc_form_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_form_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_form_merge_styles_responsive');
	function trx_addons_sc_form_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge contact form specific scripts into single file
if ( !function_exists( 'trx_addons_sc_form_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_form_merge_scripts');
	function trx_addons_sc_form_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form.js' ] = true;
		return $list;
	}
}


// AJAX handler for the send_form action
if ( !function_exists( 'trx_addons_sc_form_ajax_send_sc_form' ) ) {
	add_action('wp_ajax_send_sc_form',			'trx_addons_sc_form_ajax_send_sc_form');
	add_action('wp_ajax_nopriv_send_sc_form',	'trx_addons_sc_form_ajax_send_sc_form');
	function trx_addons_sc_form_ajax_send_sc_form() {

		trx_addons_verify_nonce();
	
		$response = array('error'=>'');

		parse_str($_POST['data'], $post_data);
		$post_data = wp_unslash($post_data);

		$contact_email = !empty($post_data['form_data']) && (int) $post_data['form_data'] > 0 
								? get_transient(sprintf("trx_addons_form_data_%d", (int) $post_data['form_data'])) 
								: '';
		if (empty($contact_email) && !($contact_email = get_option('admin_email'))) 
			$response['error'] = esc_html__('Unknown admin email!', 'trx_addons');
		else {
			$user_name	= !empty($post_data['name']) ? $post_data['name'] : '';
			$user_email	= !empty($post_data['email']) ? $post_data['email'] : '';
			$user_phone	= !empty($post_data['phone']) ? $post_data['phone'] : '';
			$user_msg	= !empty($post_data['message']) ? $post_data['message'] : '';
			
			// Attention! Strings below not need html-escaping, because mail is a plain text
			$subj = sprintf(__('Site %s - Contact form message from %s', 'trx_addons'), get_bloginfo('site_name'), $user_name);
			$msg = (!empty($user_name)	? "\n".sprintf(__('Name: %s', 'trx_addons'), $user_name) : '')
				.  (!empty($user_email) ? "\n".sprintf(__('E-mail: %s', 'trx_addons'), $user_email) : '')
				.  (!empty($user_phone) ? "\n".sprintf(__('Phone: %s', 'trx_addons'), $user_phone) : '');
			// Extra fields
			foreach ($post_data as $k=>$v) {
				if (in_array($k, array('name', 'email', 'phone', 'message', 'form_data'))) continue;
				$msg .= "\n".sprintf("%s: %s", ucfirst($k), $v);
			}
			// Message and site info
			$msg .= (!empty($user_msg)	? "\n\n".trim($user_msg) : '')
				.  "\n\n............. " . get_bloginfo('site_name') . " (" . esc_url(home_url('/')) . ") ............";
			// Additional headers
			$headers = "From: {$user_email}\r\n"
					. "Reply-To: {$user_email}\r\n"
					. "X-Mailer: PHP/" . phpversion();
			// Try send message via wp_mail(). If failed - try via native php mail() function
			$contact_email = str_replace('|', ',', $contact_email);
			if (is_email($contact_email) && !(wp_mail($contact_email, $subj, $msg, $headers) || mail($contact_email, $subj, $msg, $headers))) {
				$response['error'] = esc_html__('Error send message!', 'trx_addons');
			}
		
			trx_addons_ajax_response( $response );
		}
	}
}


// Action to start form
if ( !function_exists( 'trx_addons_sc_form_start' ) ) {
	add_action('trx_addons_action_fields_start', 'trx_addons_sc_form_start', 10, 1);
	function trx_addons_sc_form_start($args) {
		?><form class="sc_form_form <?php
						if ($args['style'] != 'default') echo 'sc_input_hover_'.esc_attr($args['style']);
						?>" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>"><?php
			if (!empty($args['form_data'])) {
				?><input type="hidden" name="form_data" value="<?php echo esc_attr($args['form_data']); ?>"><?php
			}
	}
}

// Action to end form
if ( !function_exists( 'trx_addons_sc_form_end' ) ) {
	add_action('trx_addons_action_fields_end', 'trx_addons_sc_form_end', 10, 1);
	function trx_addons_sc_form_end($args=array()) {
		?></form><?php
	}
}

// Action to show field 'name'
if ( !function_exists( 'trx_addons_sc_form_field_name' ) ) {
	add_action('trx_addons_action_field_name', 'trx_addons_sc_form_field_name', 10, 1);
	function trx_addons_sc_form_field_name($args) {
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
				'trx_addons_args_sc_form_field',
				array_merge(array(
									'field_name' => 'name',
									'field_type' => 'text',
									'field_req' => true,
									'field_icon' => 'trx_addons_icon-user-alt',
									'field_title' => __('Name', 'trx_addons'),
									'field_placeholder' => __('Your name', 'trx_addons')
									),
							$args));
	}
}

// Action to show field 'e-mail'
if ( !function_exists( 'trx_addons_sc_form_field_email' ) ) {
	add_action('trx_addons_action_field_email', 'trx_addons_sc_form_field_email', 10, 1);
	function trx_addons_sc_form_field_email($args) {
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
				'trx_addons_args_sc_form_field',
				array_merge(array(
									'field_name'  => 'email',
									'field_type'  => 'text',
									'field_req'   => true,
									'field_icon'  => 'trx_addons_icon-mail',
									'field_title' => __('E-mail', 'trx_addons'),
									'field_placeholder' => __('Your e-mail', 'trx_addons')
									),
							$args));
	}
}

// Action to show field 'message'
if ( !function_exists( 'trx_addons_sc_form_field_message' ) ) {
	add_action('trx_addons_action_field_message', 'trx_addons_sc_form_field_message', 10, 1);
	function trx_addons_sc_form_field_message($args) {
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
				'trx_addons_args_sc_form_field',
				array_merge(array(
									'field_name'  => 'message',
									'field_type'  => 'textarea',
									'field_req'   => true,
									'field_icon'  => 'trx_addons_icon-feather',
									'field_title' => __('Message', 'trx_addons'),
									'field_placeholder' => __('Your message', 'trx_addons')
									),
							$args));
	}
}

// Action to show custom field
if ( !function_exists( 'trx_addons_sc_form_field_custom' ) ) {
	add_action('trx_addons_action_field_custom', 'trx_addons_sc_form_field_custom', 10, 1);
	function trx_addons_sc_form_field_custom($args) {
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
				'trx_addons_args_sc_form_field',
				array_merge(array(
									'field_name' => 'custom',
									'field_type' => 'text',
									'field_req' => false,
									'field_icon' => '',
									'field_title' => __('Custom field title', 'trx_addons'),
									'field_placeholder' => __('Custom field placeholder', 'trx_addons')
									),
							$args));
	}
}

// Action to show button 'send' and message box with result of the action
if ( !function_exists( 'trx_addons_sc_form_field_send' ) ) {
	add_action('trx_addons_action_field_send', 'trx_addons_sc_form_field_send', 10, 1);
	function trx_addons_sc_form_field_send($args=array()) {
		static $cnt = 0;
		$cnt++;
		$privacy = trx_addons_get_privacy_text();
		if (!empty($privacy)) {
			?><div class="sc_form_field sc_form_field_checkbox"><?php
				?><input type="checkbox" id="i_agree_privacy_policy_sc_form_<?php echo esc_attr($cnt); ?>" name="i_agree_privacy_policy" class="sc_form_privacy_checkbox" value="1">
				<label for="i_agree_privacy_policy_sc_form_<?php echo esc_attr($cnt); ?>"><?php trx_addons_show_layout($privacy); ?></label>
			</div><?php
		}
		?><div class="sc_form_field sc_form_field_button sc_form_field_submit"><?php
			?><button class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', '', 'sc_form', $args)); ?>"<?php
				if ( false && ! empty($privacy) ) echo ' disabled="disabled"'
			?>><?php
				if (!empty($args['button_caption']))
					echo esc_html($args['button_caption']);
				else
					esc_html_e('Send Message', 'trx_addons');
			?></button>
		</div>
		<div class="trx_addons_message_box sc_form_result"></div>
		<?php
	}
}



// trx_sc_form
//-------------------------------------------------------------
/*
[trx_sc_form id="unique_id" style="default"]
*/
if ( !function_exists( 'trx_addons_sc_form' ) ) {
	function trx_addons_sc_form($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_form', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "default",
			"style" => "inherit",
			"align" => "",
			"button_caption" => "",
			"labels" => 0,
			"phone" => "",
			"email" => "",
			"address" => "",
			))
		);

		ob_start();
		if (empty($atts['style']) || trx_addons_is_inherit($atts['style'])) 
			$atts['style'] = trx_addons_get_option('input_hover');
		if (!empty($atts['email'])) {
			$atts['form_data'] = mt_rand();
			set_transient("trx_addons_form_data_{$atts['form_data']}", str_replace(array(' ', ',', ';'), '|', $atts['email']), 60*60);
		}
		if (!empty($atts['phone'])) 
			$atts['phone'] = str_replace(array(',', ';'), '|', $atts['phone']);
		if (!empty($atts['address'])) 
			$atts['address'] = str_replace(';', '|', $atts['address']);
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.default.php'
										),
										'trx_addons_args_sc_form', 
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_form', $atts, $content);
	}
}


// Add shortcode [trx_sc_form]
if (!function_exists('trx_addons_sc_form_add_shortcode')) {
	function trx_addons_sc_form_add_shortcode() {
		add_shortcode("trx_sc_form", "trx_addons_sc_form");
	}
	add_action('init', 'trx_addons_sc_form_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'form/form-sc-vc.php';
}
