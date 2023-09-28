<?php
/**
 * Template of one field of the form
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_form_field');
if (empty($args['field_title'])) $args['field_title'] = '';

?><label class="sc_form_field sc_form_field_<?php echo esc_attr($args['field_name']); ?> sc_form_field_<?php echo esc_attr($args['field_type']); ?><?php if (!empty($args['field_style'])) echo ' sc_form_field_style_'.esc_attr($args['field_style']); ?><?php echo !empty($args['field_req']) ? ' required' : ' optional'; ?>"><?php
	if (!empty($args['labels']) && trx_addons_is_on($args['labels']) && !empty($args['field_title'])) {
		?><span class="sc_form_field_title<?php if (!empty($args['field_tooltip'])) echo ' sc_form_field_title_with_tooltip'; ?>"><?php
			echo esc_html($args['field_title']);
			if (!empty($args['field_tooltip'])) {
				?><span class="sc_form_field_tooltip" data-tooltip-text="<?php echo esc_attr($args['field_tooltip']); ?>">?</span><?php
			}
		?></span><?php
	}
	?><span class="sc_form_field_wrap"><?php

		$id = ! empty($args['field_id']) 
				? $args['field_id'] 
				: trx_addons_generate_id( str_replace( array('[',']'), '', $args['field_name'] ) . '_' );

		if ($args['field_type'] == 'select' || $args['field_type'] == 'select2') {
			if ($args['field_type']=='select2') {
				trx_addons_enqueue_select2();
				if (empty($args['field_class']) || strpos($args['field_class'], 'trx_addons_select2') === false)
					$args['field_class'] = (!empty($args['field_class']) ? $args['field_class'].' ' : '') . 'trx_addons_select2';
			}
			?><select
				name="<?php echo esc_attr($args['field_name']); ?>"
				id="<?php echo esc_attr($id); ?>"
				<?php
				if ($args['field_type'] == 'select' && !empty($args['field_size'])) {
					?> size="<?php echo esc_attr($args['field_size']); ?>"<?php
				}
				if (!empty($args['field_multiple'])) {
					?> multiple="multiple"<?php
				}
				if (!empty($args['field_class'])) {
					?> class="<?php echo esc_attr($args['field_class']); ?>"<?php
				}
				if (!empty($args['field_data']) && is_array(($args['field_data']))) {
					foreach ($args['field_data'] as $k => $v) {
						echo ' data-'.esc_attr($k).'="'.esc_attr($v).'"';
					}
				}
			?>><?php
				if (!empty($args['field_options']) && is_array(($args['field_options']))) {
					foreach ($args['field_options'] as $k=>$v) {
						// To prevent twice encoding
						$k = urldecode( $k );
						?><option value="<?php echo esc_attr($k); ?>"<?php
							if ( ! empty( $args['field_value'] ) && strpos( ',' . $args['field_value'] . ',', ',' . $k . ',' ) !== false ) {
								echo ' selected="selected"';
							}
						?>><?php
							echo esc_html($v);
						?></option><?php
					}
				}
			?></select><?php

		} else if ($args['field_type'] == 'slider' || $args['field_type'] == 'range') {
			wp_enqueue_script('jquery-ui-slider', false, array('jquery', 'jquery-ui-core'), null, true);
			$is_range  = $args['field_type'] == 'range';
			$field_min = !empty($args['field_min']) ? $args['field_min'] : 0;
			$field_max = !empty($args['field_max']) ? $args['field_max'] : 100;
			$field_step= !empty($args['field_step']) ? $args['field_step'] : 1;
			$field_val = !empty($args['field_value']) 
							? ($args['field_value'] . ($is_range && strpos($args['field_value'], ',')===false ? ','.$field_max : ''))
							: ($is_range ? $field_min.','.$field_max : $field_min);
			?><input type="hidden" 
					name="<?php echo esc_attr($args['field_name']); ?>"
					id="<?php echo esc_attr($id); ?>"
					value="<?php echo esc_attr($field_val); ?>"<?php
					if (!empty($args['field_req'])) echo ' aria-required="true"';
					?>><?php
			?><div id="<?php echo esc_attr($args['field_name']); ?>_slider"
					class="trx_addons_range_slider"
					data-range="<?php echo esc_attr($is_range ? 'true' : 'min'); ?>"
					data-min="<?php echo esc_attr($field_min); ?>"
					data-max="<?php echo esc_attr($field_max); ?>"
					data-step="<?php echo esc_attr($field_step); ?>"
					data-linked-field="<?php echo esc_attr($id); ?>">
				<span class="trx_addons_range_slider_label trx_addons_range_slider_label_min"><?php
					echo esc_attr($field_min);
				?></span>
				<span class="trx_addons_range_slider_label trx_addons_range_slider_label_max"><?php
					echo esc_attr($field_max);
				?></span><?php
				$values = explode(',', $field_val);
				for ($i=0; $i < count($values); $i++) {
					?><span class="trx_addons_range_slider_label trx_addons_range_slider_label_cur"><?php
						echo esc_html($values[$i]);
					?></span><?php
				}
			?></div><?php

		} else if ($args['field_type'] == 'radio') {
			if (!empty($args['field_options']) && is_array(($args['field_options']))) {
				foreach ($args['field_options'] as $k=>$v) {
					// To prevent twice encoding
					$k = urldecode( $k );
					?><input type="radio" 
							name="<?php echo esc_attr($args['field_name']); ?>"
							id="<?php echo esc_attr($id."_{$k}"); ?>"
							value="<?php echo esc_attr($k); ?>"<?php 
							if (!empty($args['field_value']) && $args['field_value']==$k) echo ' checked="checked"';
							if (!empty($args['field_req'])) echo ' aria-required="true"';
							?>>
					<label for="<?php echo esc_attr($id."_{$k}"); ?>"><?php echo esc_html($v); ?></label><?php
				}
			}

		} else if ($args['field_type'] == 'checkbox') {
			?><input type="checkbox" 
					name="<?php echo esc_attr($args['field_name']); ?>"
					id="<?php echo esc_attr($id); ?>"
					value="<?php if (!empty($args['field_value'])) echo esc_attr($args['field_value']); ?>"<?php
					if (!empty($args['field_checked'])) echo ' checked="checked"';
					if (!empty($args['field_req'])) echo ' aria-required="true"';
					?>>
			<label for="<?php echo esc_attr($id); ?>"><?php 
					echo esc_html(!empty($args['field_placeholder']) ? $args['field_placeholder'] : $args['field_title']);
			?></label><?php

		} else if ($args['field_type'] == 'checklist') {
			?><div class="sc_form_field_choises sc_form_field_choises_dir_<?php echo !empty($args['field_direction']) ? esc_attr($args['field_direction']) : 'vertical';?>"><?php
			if (!is_array($args['field_value'])) $args['field_value'] = array($args['field_value']);
			foreach ($args['field_options'] as $k=>$v) {
				// To prevent twice encoding
				$k = urldecode( $k );
				?><span class="sc_form_field_choises_item">
					<input type="checkbox" 
							name="<?php echo esc_attr($args['field_name']).'[]'; ?>"
							id="<?php echo esc_attr($id.'_'.$k); ?>"
							value="<?php echo esc_attr($k); ?>"<?php
							if (!empty($args['field_value']) && is_array($args['field_value']) && in_array($k, $args['field_value']))
								echo ' checked="checked"';
							?>>
					<label for="<?php echo esc_attr($id.'_'.$k); ?>"><?php 
						echo esc_html($v);
					?></label>
				</span><?php
			}
			?></div><?php
			
		} else if ($args['field_type'] == 'color') {
			if (empty($args['field_style']) || $args['field_style']=='wp') trx_addons_enqueue_wp_color_picker();
			?><input type="text"
					class="trx_addons_color_selector<?php
							if (!empty($args['field_style']) && $args['field_style'] == 'internal') echo ' iColorPicker';
							?>"
					name="<?php echo esc_attr($args['field_name']); ?>"
					id="<?php echo esc_attr($id); ?>"
					value="<?php if (!empty($args['field_value'])) echo esc_attr($args['field_value']); ?>"
			><?php
			
		} else if ($args['field_type'] == 'textarea') {
			?><textarea
				name="<?php echo esc_attr($args['field_name']); ?>"
				id="<?php echo esc_attr($id); ?>"<?php 
				if (!empty($args['field_req'])) echo ' aria-required="true"';
				if ($args['style']=='default' && !empty($args['field_placeholder']))
							echo ' placeholder="'.esc_attr($args['field_placeholder']).'"';
				?>><?php
					if (!empty($args['field_value'])) echo esc_html($args['field_value']);
			?></textarea><?php

		} else {
			?><input type="<?php echo esc_attr($args['field_type']); ?>" 
					name="<?php echo esc_attr($args['field_name']); ?>"
					id="<?php echo esc_attr($id); ?>"
					value="<?php if (!empty($args['field_value'])) echo esc_attr($args['field_value']); ?>"<?php
					if (!empty($args['field_req'])) echo ' aria-required="true"';
					if ($args['style']=='default' && !empty($args['field_placeholder']))
							echo ' placeholder="'.esc_attr($args['field_placeholder']).'"';
					?>><?php
		}
		if ($args['style']!='default') { 
			?><span class="sc_form_field_hover"><?php
				if ($args['style'] == 'path') {
					$path_height = $args['field_type'] == 'text' ? 75 : 190;
					?><svg class="sc_form_field_graphic" preserveAspectRatio="none" viewBox="0 0 520 <?php echo intval($path_height); ?>" height="100%" width="100%"><path d="m0,0l520,0l0,<?php echo intval($path_height); ?>l-520,0l0,-<?php echo intval($path_height); ?>z"></svg><?php
				} else if ($args['style'] == 'iconed' && ! empty($args['field_icon']) ) {
					?><i class="sc_form_field_icon <?php echo esc_attr($args['field_icon']); ?>"></i><?php
				}
				?><span class="sc_form_field_content" data-content="<?php echo esc_attr($args['field_title']); ?>"><?php echo esc_html($args['field_title']); ?></span><?php
			?></span><?php
		}
	?></span><?php
?></label>