<?php
/**
 * The template's part to display the agent's or author's contact form
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$trx_addons_args = get_query_var('trx_addons_args_cars_form');
$trx_addons_meta = $trx_addons_args['meta'];
$trx_addons_agent = $trx_addons_args['agent'];

$form_style = $trx_addons_args['style'] = empty($trx_addons_args['style']) || trx_addons_is_inherit($trx_addons_args['style']) 
					? trx_addons_get_option('input_hover') 
					: $trx_addons_args['style'];

?><div class="sc_form cars_page_agent_form">
	<h5 class="cars_page_agent_form_title"><?php printf(esc_html__('Contact %s', 'trx_addons'), $trx_addons_agent['name']); ?></h5><?php
	if ( (int) ($form_id = trx_addons_get_option('cars_agents_form')) > 0 ) {
		// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
		if ( !function_exists( 'trx_addons_cpt_cars_wpcf7_form_elements' ) ) {
			add_filter('wpcf7_form_elements',	'trx_addons_cpt_cars_wpcf7_form_elements');
			function trx_addons_cpt_cars_wpcf7_form_elements($elements) {
				$trx_addons_args = get_query_var('trx_addons_args_cars_form');
				$trx_addons_meta = $trx_addons_args['meta'];
				$trx_addons_agent = $trx_addons_args['agent'];
				$elements = str_replace('</textarea>',
									esc_html(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_CARS_PT
												? sprintf(__("Hi, %s.\nI'm interested in '%s' [ID = %s].\nPlease, get in touch with me.", 'trx_addons'),
													$trx_addons_agent['name'], get_the_title(), $trx_addons_meta['id'])
												: sprintf(__("Hi, %s.\nI saw your profile on '%s' and wanted to see if you could help me.", 'trx_addons'),
													$trx_addons_agent['name'], get_bloginfo('name'))
											)
									. '</textarea>',
									$elements
									);
				return $elements;
			}
		}
		
		// Store property and agent's data for the form for 4 hours
		set_transient(sprintf('trx_addons_cf7_%d_data', $form_id), array(
													'item'  => trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_CARS_PT ? get_the_ID() : '',
													'agent' => $trx_addons_meta['agent_type']=='author' ? -get_the_author_meta('ID') : $trx_addons_meta['agent']
													), 4 * 60 * 60);

		// Display Contact Form 7
		trx_addons_show_layout(do_shortcode('[contact-form-7 id="'.esc_attr($form_id).'"]'));

		// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
		remove_filter('wpcf7_form_elements', 'trx_addons_cpt_cars_wpcf7_form_elements');

	} else {
		// Default form
		?><form class="sc_form_form <?php
					if ($form_style != 'default') echo 'sc_input_hover_'.esc_attr($form_style);
		?>" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
			<input type="hidden" name="car_agent" value="<?php
					echo esc_attr($trx_addons_meta['agent_type']=='author' ? -get_the_author_meta('ID') : $trx_addons_meta['agent']); ?>">
			<input type="hidden" name="car_id" value="<?php
					echo esc_attr(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_CARS_PT ? get_the_ID() : ''); ?>">
			<?php
			// Field 'Name'
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'field_name'  => 'name',
														'field_type'  => 'text',
														'field_req'   => true,
														'field_icon'  => 'trx_addons_icon-user-alt',
														'field_title' => __('Name', 'trx_addons'),
														'field_placeholder' => __('Your name', 'trx_addons')
														))
										);
			// Field 'E-mail'
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'field_name'  => 'email',
														'field_type'  => 'text',
														'field_req'   => true,
														'field_icon'  => 'trx_addons_icon-mail',
														'field_title' => __('E-mail', 'trx_addons'),
														'field_placeholder' => __('Your e-mail', 'trx_addons')
														))
										);
			// Field 'Phone'
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'field_name'  => 'phone',
														'field_type'  => 'text',
														'field_req'   => true,
														'field_icon'  => 'trx_addons_icon-phone',
														'field_title' => __('Phone', 'trx_addons'),
														'field_placeholder' => __('Your phone', 'trx_addons')
														))
										);
			// Field 'Message'
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'field_name'  => 'message',
														'field_type'  => 'textarea',
														'field_req'   => true,
														'field_icon'  => 'trx_addons_icon-feather',
														'field_title' => __('Message', 'trx_addons'),
														'field_placeholder' => __('Your message', 'trx_addons'),
														'field_value' => trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_CARS_PT
															? sprintf(__("Hi, %s.\nI'm interested in '%s' [ID = %s].\nPlease, get in touch with me.", 'trx_addons'),
																$trx_addons_agent['name'], get_the_title(), $trx_addons_meta['id'])
															: sprintf(__("Hi, %s.\nI saw your profile on '%s' and wanted to see if you could help me.", 'trx_addons'),
																$trx_addons_agent['name'], get_bloginfo('name'))
														))
										);
			?>
			<div class="sc_form_field sc_form_field_button"><button><?php esc_html_e('Send Message', 'trx_addons'); ?></button></div>
			<div class="trx_addons_message_box sc_form_result"></div>
		</form><?php
	}
?></div>