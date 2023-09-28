<?php
/**
 * The style "default" of the Contact form
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_form');
?><div
	<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
	class="sc_form sc_form_<?php 
		echo esc_attr($args['type']);
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
		if (!empty($args['align']) && !trx_addons_is_off($args['align'])) echo ' sc_align_'.esc_attr($args['align']);
		?>"
	<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"'; 
	trx_addons_sc_show_attributes('sc_form', $args, 'sc_wrapper');
?>><?php

	trx_addons_sc_show_titles('sc_form', $args);
	
	do_action('trx_addons_action_fields_start', $args);
	
	?><div class="sc_form_details <?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>"><?php
		// Contact form. Attention! Column's tags can't start with new line
		?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, 2)); ?>"><?php
			do_action('trx_addons_action_field_name', $args);
		?></div><div class="<?php echo esc_attr(trx_addons_get_column_class(1, 2)); ?>"><?php
			do_action('trx_addons_action_field_email', $args);
		?></div><?php
	?></div><?php
	
	do_action('trx_addons_action_field_message', $args);
	do_action('trx_addons_action_field_send', $args);
	do_action('trx_addons_action_fields_end', $args);

?></div>