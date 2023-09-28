<?php
/**
 * The style "default" of the Widget "Themes Search"
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_edd_search');
extract($trx_addons_args);

$params = array_merge(array(
						'themes_keyword' => '',
						'themes_category' => '',
						'themes_compatibility' => '',
						'themes_label' => '',
						'themes_order' => sprintf("%s_%s", $orderby, $order)
						), trx_addons_edd_query_params());

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
$form_style = $trx_addons_args['style'] = empty($trx_addons_args['style']) || trx_addons_is_inherit($trx_addons_args['style']) 
			? trx_addons_get_option('input_hover') 
			: $trx_addons_args['style'];
?><div
	<?php if (!empty($trx_addons_args['id'])) echo ' id="'.esc_attr($trx_addons_args['id']).'"'; ?>
	class="sc_form edd_search<?php 
		if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']);
		if (!empty($trx_addons_args['align']) && !trx_addons_is_off($trx_addons_args['align'])) echo ' sc_align_'.esc_attr($trx_addons_args['align']);
		?>"<?php
	if (!empty($trx_addons_args['css'])) echo ' style="'.esc_attr($trx_addons_args['css']).'"'; 
?>>
	<form class="edd_search_form sc_form_form sc_form_custom <?php if ($form_style != 'default') echo 'sc_input_hover_'.esc_attr($form_style); ?>" action="<?php echo esc_url(get_post_type_archive_link( TRX_ADDONS_EDD_PT )); ?>" method="get">

		<div class="edd_search_form_keywords"><?php

			// If current page is not themes archive - make new query to show results
			?><input type="hidden" name="edd_search_query" value="<?php echo esc_attr(trx_addons_is_edd_page() && !trx_addons_is_single() ? '0' : '1');	?>"><?php

			// Keywords
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'themes_keyword',
														'field_type'  => 'text',
														'field_value' => $params['themes_keyword'],
														'field_req'   => false,
														'field_icon'  => 'trx_addons_icon-search',
														'field_title' => __('Search for', 'trx_addons'),
														'field_placeholder' => __('Keywords', 'trx_addons')
														))
										);
		
			// Submit button
			?><button class="edd_search_button trx_addons_icon-search" title="<?php esc_attr_e('Start search', 'trx_addons'); ?>" aria-label="<?php esc_attr_e('Start search', 'trx_addons'); ?>"></button>
		</div><?php
		
		// Categories
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
										'trx_addons_args_sc_form_field',
										array_merge($trx_addons_args, array(
													'labels'      => true,
													'field_title' => __('Categories', 'trx_addons'),
													'field_name'  => 'themes_category',
													'field_type'  => 'checklist',
													'field_value' => $params['themes_category'],
													'field_req'   => false,
													'field_options'  => trx_addons_get_list_terms(false, TRX_ADDONS_EDD_TAXONOMY_CATEGORY, array('hide_empty' => 1)),
													'field_data'  => array('not-selected' => 'false')
													))
									);

		do_action('trx_addons_action_edd_search_fields', $trx_addons_args);

		// Results Order
		?><label class="sc_form_field sc_form_field_themes_order">
			<span class="sc_form_field_title"><?php esc_html_e('Order by', 'trx_addons'); ?></span>
			<select name="themes_order">
				<option value="date_asc"<?php if ($params['themes_order']=='date_asc') echo ' selected="selected"'; ?>><?php
					esc_html_e('Date Ascending', 'trx_addons'); ?></option>
				<option value="date_desc"<?php if ($params['themes_order']=='date_desc') echo ' selected="selected"'; ?>><?php
					esc_html_e('Date Descending', 'trx_addons'); ?></option>
				<option value="title_asc"<?php if ($params['themes_order'] == 'title_asc') echo ' selected="selected"'; ?>><?php
					esc_html_e('Title Ascending', 'trx_addons'); ?></option>
				<option value="title_desc"<?php if ($params['themes_order'] == 'title_desc') echo ' selected="selected"'; ?>><?php
					esc_html_e('Title Descending', 'trx_addons'); ?></option>
			</select>
		</label>

		<button class="edd_search_button"><?php esc_html_e('Show Results', 'trx_addons'); ?></button>

	</form>
</div><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
