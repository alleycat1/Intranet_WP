<?php
/**
 * The style "default" of the Widget "Cars Search"
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_cars_search');
extract($trx_addons_args);

$params = array_merge(array(
						'cars_keyword' => '',
						'cars_maker' => '',
						'cars_model' => '',
						'cars_city' => '',
						'cars_status' => '',
						'cars_type' => '',
						'cars_labels' => '',
						'cars_fuel' => '',
						'cars_transmission' => '',
						'cars_type_drive' => '',
						'cars_engine_size' => '',
						'cars_mileage' => '',
						'cars_produced' => '',
						'cars_price' => '',
						'cars_features' => array(),
						'cars_order' => sprintf("%s_%s", $orderby, $order)
						), trx_addons_cpt_cars_query_params());

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
	class="sc_form cars_search cars_search_<?php 
		echo esc_attr($trx_addons_args['type']);
		if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']);
		if (!empty($trx_addons_args['align']) && !trx_addons_is_off($trx_addons_args['align'])) echo ' sc_align_'.esc_attr($trx_addons_args['align']);
		?>"<?php
	if (!empty($trx_addons_args['css'])) echo ' style="'.esc_attr($trx_addons_args['css']).'"'; 
?>>
	<form class="cars_search_form sc_form_form sc_form_custom <?php if ($form_style != 'default') echo 'sc_input_hover_'.esc_attr($form_style); ?>" action="<?php echo esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_CARS_PT )); ?>" method="get">

		<div class="cars_search_basic"><?php

			// Action to allow third-party extensions add own fileds to the form
			do_action( 'trx_addons_action_cars_search_form_start', $trx_addons_args, $params );

			// If current page is not properties archive - make new query to show results
			?><input type="hidden" name="cars_query" value="<?php echo esc_attr(trx_addons_is_cars_page() && !trx_addons_is_single() ? '0' : '1');	?>"><?php
				
			// Keywords
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_keyword',
														'field_type'  => 'text',
														'field_value' => $params['cars_keyword'],
														'field_req'   => false,
														'field_icon'  => 'trx_addons_icon-search',
														'field_title' => __('Search for', 'trx_addons'),
														'field_placeholder' => __("Enter an owner's name, phone or email or a car's ID", 'trx_addons')
														))
										);
		
			// Show Advanced Search
			?><div class="cars_search_show_advanced trx_addons_icon-sliders" title="<?php esc_attr_e('Show advanced search', 'trx_addons'); ?>"></div><?php

			// Basic Submit button
			?><button class="cars_search_button trx_addons_icon-search" title="<?php esc_attr_e('Start search', 'trx_addons'); ?>" aria-label="<?php esc_attr_e('Start search', 'trx_addons'); ?>"></button><?php
			
			
		?></div>

		<div class="cars_search_advanced"><?php

			// Status
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_CARS_TAXONOMY_STATUS);
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_STATUS, array( 'hide_empty' => 1 ) )
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_status',
														'field_type'  => 'select',
														'field_value' => $params['cars_status'],
														'field_req'   => false,
														'field_options'  => $list
														))
										);

			// Type
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE);
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE, array( 'hide_empty' => 1 ) )
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_type',
														'field_type'  => 'select',
														'field_value' => $params['cars_type'],
														'field_req'   => false,
														'field_options'  => $list
														))
										);
			
			// Manufacturer
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER);
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER, array('hide_empty' => 1 ) )
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_maker',
														'field_type'  => 'select',
														'field_value' => $params['cars_maker'],
														'field_req'   => false,
														'field_options'  => $list,
														'field_class' => 'trx_addons_maker_selector',
														'field_data'  => array('not-selected' => 'true')
														))
										);
			
			// City
			$tax_obj = get_taxonomy(TRX_ADDONS_CPT_CARS_TAXONOMY_CITY);
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
											trx_addons_get_list_terms( false, TRX_ADDONS_CPT_CARS_TAXONOMY_CITY, array( 'hide_empty' => 1 ) )
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_city',
														'field_type'  => 'select',
														'field_value' => $params['cars_city'],
														'field_req'   => false,
														'field_options'  => $list,
														'field_class' => 'trx_addons_city_selector',
														'field_data'  => array('not-selected' => 'true')
														))
										);

			// Transmission
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Transmission', 'trx_addons') ) ),
											trx_addons_cpt_cars_get_list_transmission()
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_transmission',
														'field_type'  => 'select',
														'field_value' => $params['cars_transmission'],
														'field_req'   => false,
														'field_options'  => $list
														))
										);

			// Type of drive
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Type of drive', 'trx_addons' ) ) ),
											trx_addons_cpt_cars_get_list_type_of_drive()
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_type_drive',
														'field_type'  => 'select',
														'field_value' => $params['cars_type_drive'],
														'field_req'   => false,
														'field_options'  => $list
														))
										);

			// Fuel
			$list = trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Fuel', 'trx_addons' ) ) ),
											trx_addons_cpt_cars_get_list_fuel()
											);
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => false,
														'field_name'  => 'cars_fuel',
														'field_type'  => 'select',
														'field_value' => $params['cars_fuel'],
														'field_req'   => false,
														'field_options'  => $list
														))
										);

			// Results Order
			?><label class="sc_form_field sc_form_field_cars_order">
				<select name="cars_order">
					<option value="date_asc"<?php if ($params['cars_order']=='date_asc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Date Ascending', 'trx_addons'); ?></option>
					<option value="date_desc"<?php if ($params['cars_order']=='date_desc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Date Descending', 'trx_addons'); ?></option>
					<option value="price_asc"<?php if ($params['cars_order'] == 'price_asc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Price Ascending', 'trx_addons'); ?></option>
					<option value="price_desc"<?php if ($params['cars_order'] == 'price_desc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Price Descending', 'trx_addons'); ?></option>
					<option value="title_asc"<?php if ($params['cars_order'] == 'title_asc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Title Ascending', 'trx_addons'); ?></option>
					<option value="title_desc"<?php if ($params['cars_order'] == 'title_desc') echo ' selected="selected"'; ?>><?php
						esc_html_e('Title Descending', 'trx_addons'); ?></option>
				</select>
			</label><?php

			// Mileage
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => true,
														'field_title' => __('Mileage', 'trx_addons'),
														'field_name'  => 'cars_mileage',
														'field_type'  => 'range',
														'field_value' => $params['cars_mileage'],
														'field_req'   => false,
														'field_min'   => trx_addons_cpt_cars_get_min_max('mileage_min'),
														'field_max'   => trx_addons_cpt_cars_get_min_max('mileage_max'),
														'field_step'  => (trx_addons_cpt_cars_get_min_max('mileage_max') - trx_addons_cpt_cars_get_min_max('mileage_min')) / 20
														))
										);

			// Engine size
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => true,
														'field_title' => __('Engine size', 'trx_addons'),
														'field_name'  => 'cars_engine_size',
														'field_type'  => 'range',
														'field_value' => $params['cars_engine_size'],
														'field_req'   => false,
														'field_min'   => trx_addons_cpt_cars_get_min_max('engine_size_min'),
														'field_max'   => trx_addons_cpt_cars_get_min_max('engine_size_max'),
														'field_step'  => 1
														))
										);

			// Produced
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => true,
														'field_title' => __('Produced', 'trx_addons'),
														'field_name'  => 'cars_produced',
														'field_type'  => 'range',
														'field_value' => $params['cars_produced'],
														'field_req'   => false,
														'field_min'   => trx_addons_cpt_cars_get_min_max('produced_min'),
														'field_max'   => trx_addons_cpt_cars_get_min_max('produced_max'),
														'field_step'  => 1
														))
										);

			// Price
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => true,
														'field_title' => __('Price', 'trx_addons'),
														'field_name'  => 'cars_price',
														'field_type'  => 'range',
														'field_value' => $params['cars_price'],
														'field_req'   => false,
														'field_min'   => trx_addons_cpt_cars_get_min_max('price_min'),
														'field_max'   => trx_addons_cpt_cars_get_min_max('price_max'),
														'field_step'  => (trx_addons_cpt_cars_get_min_max('price_max') - trx_addons_cpt_cars_get_min_max('price_min')) / 20
														))
										);

			// Features
			$list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_CARS_TAXONOMY_FEATURES, array('hide_empty' => 1));
			if (is_array($list)) {
				foreach ($list as $id=>$title) {
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'form/tpl.form-field.php',
											'trx_addons_args_sc_form_field',
											array_merge($trx_addons_args, array(
														'labels'      => true,
														'field_placeholder' => $title,
														'field_name'  => "cars_features_{$id}",
														'field_type'  => 'checkbox',
														'field_value' => $id,
														'field_checked' => in_array($id, $params['cars_features']),
														'field_req'   => false
														))
											);
				}
			}

		?></div>

	</form>
</div><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
