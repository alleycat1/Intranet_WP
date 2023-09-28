<?php
/**
 * Template of one field of the widget "Woocommerce Search Filter"
 *
 * @package ThemeREX Addons
 * @since v1.88.0
 */

$args = get_query_var( 'trx_addons_args_widget_woocommerce_search_field' );
if ( empty( $args['field_title'] ) ) $args['field_title'] = '';
if ( empty( $args['field_return'] ) ) $args['field_return'] = 'slug';	// id | slug

?><div class="sc_form_field<?php
			echo ' sc_form_field_' . esc_attr( $args['field_name'] )
				. ' sc_form_field_' . esc_attr( $args['field_type'] )
				. ( ! empty( $args['field_style'] ) ? ' sc_form_field_style_' . esc_attr( $args['field_style'] ) : '' )
				. ( ! empty( $args['field_class'] ) ? ' ' . esc_attr( $args['field_class'] ) : '' )
				. ( ! empty( $args['field_req'] ) ? ' required' : ' optional' );
?>"<?php
	if ( ! empty( $args['field_multiple'] ) ) {
		?> data-multiple="1"<?php
	}
	if ( ! empty( $args['field_data'] ) && is_array( $args['field_data'] ) ) {
		foreach ( $args['field_data'] as $k => $v ) {
			echo ' data-' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
	}
?>><?php

	// Field title
	$title_filled = ! empty( $args['field_value'] ) && ( $args['field_type'] != 'range' || $args['field_value'] != $args['field_min'] . ',' . $args['field_max'] );
	?><div tabindex="0" class="sc_form_field_title<?php
		if ( ! empty( $args['field_tooltip'] ) ) {
			echo ' sc_form_field_title_with_tooltip';
		}
		if ( $title_filled ) {
			echo ' sc_form_field_title_filled';
		}
	?>"><span class="sc_form_field_title_caption"><?php
			echo esc_html( $args['field_title'] );
		?></span><?php
		if ( ! empty( $args['field_tooltip'] ) ) {
			?><span class="sc_form_field_tooltip" data-tooltip-text="<?php echo esc_attr( $args['field_tooltip'] ); ?>">?</span><?php
		}
		// Arrow
		?><span class="sc_form_field_arrow"></span><?php
		// List of selected items
		?><span class="sc_form_field_selected_items"><?php
			if ( $title_filled ) {
 				echo esc_html( urldecode( ! empty( $args['field_selected'] )
 								? $args['field_selected']
 								: (	! empty( $args['field_value'] ) && ! in_array( $args['field_type'], array( 'text', 'slider', 'range' ) )
									? ( ! empty( $args['field_multiple'] )
										? join( ', ', array_map( 'ucfirst', explode( ',', $args['field_value'] ) ) )
										: ucfirst( $args['field_value'] )
										)
									: $args['field_value']
									)
								) );
			}
		?></span><?php
	?></div><?php

	// Field wrap
	?><div class="sc_form_field_wrap"><?php

		// Hidden field(s) with data
		$id = ! empty( $args['field_id'] )
				? $args['field_id'] 
				: trx_addons_generate_id( str_replace( array('[',']'), '', $args['field_name'] ) . '_' );
		?><input type="hidden"
				class="sc_form_field_param"
				id="<?php echo esc_attr( $id ); ?>"
				name="<?php echo esc_attr( $args['field_name'] ); ?>"
				value="<?php echo esc_attr( $args['field_value'] ); ?>"><?php

		// Select (list)
		if ( $args['field_type'] == 'select' ) {
			if ( ! empty( $args['field_options'] ) && is_array( $args['field_options'] )
				&& ( ! isset( $v->count ) || intval( $v->count ) > 0 || ! apply_filters( 'trx_addons_filters_woocommerce_search_hide_empty_filter_items', true ) )
			) {
				?><div class="sc_form_field_items"><?php
					foreach ( $args['field_options'] as $v ) {
						?><span tabindex="0" class="sc_form_field_item<?php
										echo strpos( ',' . $args['field_value'] .',', ',' . ( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ) . ',' ) !== false
												? ' sc_form_field_item_checked'
												: '';
										if ( isset( $v->hierarchy_level ) ) {
											echo ' sc_form_field_item_level_' . $v->hierarchy_level;
										}
										?>"
								data-value="<?php echo esc_attr( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ); ?>"<?php
						?>><?php
							?><span class="sc_form_field_item_label">
								<span class="sc_form_field_item_text"><?php echo wp_kses( $v->name, 'trx_addons_kses_content' ); ?></span><?php
								if ( ! empty( $args['show_counters'] ) && ! empty( $v->count ) ) {
									?><span class="sc_form_field_item_total"><?php echo esc_html( $v->count ); ?></span><?php
								}
							?></span>
						</span><?php
					}
				?></div><?php
			}

		// Button
		} else if ( $args['field_type'] == 'button' ) {
			if ( ! empty( $args['field_options'] ) && is_array( $args['field_options'] )
				&& ( ! isset( $v->count ) || $v->count > 0 || ! apply_filters( 'trx_addons_filters_woocommerce_search_hide_empty_filter_items', true ) )
			) {
				?><div class="sc_form_field_items"><?php
					foreach ( $args['field_options'] as $v ) {
						?><span tabindex="0" class="sc_form_field_item<?php
										echo strpos( ',' . $args['field_value'] .',', ',' . ( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ) . ',' ) !== false
												? ' sc_form_field_item_checked'
												: '';
										?>"
								data-value="<?php echo esc_attr( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ); ?>"<?php
						?>><?php
							?><span class="sc_form_field_item_label">
								<span class="sc_form_field_item_text"><?php echo esc_html( $v->name ); ?></span><?php
								if ( ! empty( $args['show_counters'] ) && ! empty( $v->count ) ) {
									?><span class="sc_form_field_item_total"><?php echo esc_html( $v->count ); ?></span><?php
								}
							?></span>
						</span><?php
					}
				?></div><?php
			}

		// Color
		} else if ( $args['field_type'] == 'color' ) {
			if ( ! empty( $args['field_options'] ) && is_array( $args['field_options'] )
				&& ( ! isset( $v->count ) || $v->count > 0 || ! apply_filters( 'trx_addons_filters_woocommerce_search_hide_empty_filter_items', true ) )
			) {
				?><div class="sc_form_field_items"><?php
					foreach ( $args['field_options'] as $v ) {
						$meta = trx_addons_get_term_meta( $v->term_id );
						if ( empty( $meta ) ) {
							$meta = $v->slug;
						}
						?><span tabindex="0" class="sc_form_field_item<?php
										echo strpos( ',' . $args['field_value'] .',', ',' . ( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ) . ',' ) !== false
												? ' sc_form_field_item_checked'
												: '';
										?>"
								data-value="<?php echo esc_attr( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ); ?>">
							<span class="sc_form_field_item_image"<?php
								if ( ! empty( $meta ) ) {
									?> style="background-color: <?php echo esc_attr( $meta ); ?>;"<?php
								}
							?>></span>
							<span class="sc_form_field_item_label">
								<span class="sc_form_field_item_text"><?php echo esc_html( $v->name ); ?></span><?php
								if ( ! empty( $args['show_counters'] ) && ! empty( $v->count ) ) {
									?><span class="sc_form_field_item_total"><?php echo esc_html( $v->count ); ?></span><?php
								}
							?></span>
						</span><?php
					}
				?></div><?php
			}

		// Image
		} else if ( $args['field_type'] == 'image' ) {
			if ( ! empty( $args['field_options'] ) && is_array( $args['field_options'] )
				&& ( ! isset( $v->count ) || $v->count > 0 || ! apply_filters( 'trx_addons_filters_woocommerce_search_hide_empty_filter_items', true ) )
			) {
				?><div class="sc_form_field_items"><?php
					$no_img = apply_filters('trx_addons_filter_no_thumb', trx_addons_get_file_url('css/images/no-thumb.gif'));
					foreach ( $args['field_options'] as $v ) {
						$meta = trx_addons_get_term_meta( $v->term_id );
						$meta = empty( $meta ) ? $no_img : trx_addons_add_thumb_size($meta, trx_addons_get_thumb_size('tiny'));
						?><span tabindex="0" class="sc_form_field_item<?php
										echo strpos( ',' . $args['field_value'] .',', ',' . ( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ) . ',' ) !== false
												? ' sc_form_field_item_checked'
												: '';
										?>"
								data-value="<?php echo esc_attr( $args['field_return'] == 'id' ? $v->term_id : urldecode( $v->slug ) ); ?>">
							<span class="sc_form_field_item_image"<?php
								if ( ! empty( $meta ) ) {
									?> style="background-image: url(<?php echo esc_url( $meta ); ?>);"<?php
								}
							?>></span>
							<span class="sc_form_field_item_label">
								<span class="sc_form_field_item_text"><?php echo esc_html( $v->name ); ?></span><?php
								if ( ! empty( $args['show_counters'] ) && ! empty( $v->count ) ) {
									?><span class="sc_form_field_item_total"><?php echo esc_html( $v->count ); ?></span><?php
								}
							?></span>
						</span><?php
					}
				?></div><?php
			}

		// Slider or range
		} else if ( $args['field_type'] == 'slider' || $args['field_type'] == 'range' ) {
			wp_enqueue_script('jquery-ui-slider', false, array('jquery', 'jquery-ui-core'), null, true);
			$is_range  = $args['field_type'] == 'range';
			$field_min = ! empty($args['field_min']) ? $args['field_min'] : 0;
			$field_max = ! empty($args['field_max']) ? $args['field_max'] : 100;
			$field_step= ! empty($args['field_step']) ? $args['field_step'] : 1;
			$field_val = ! empty($args['field_value']) 
							? ( $args['field_value'] . ( $is_range && strpos( $args['field_value'], ',' ) === false ? ',' . $field_max : '' ) )
							: ( $is_range ? $field_min . ',' . $field_max : $field_min );
			?><div id="<?php echo esc_attr($args['field_name']); ?>_slider"
					class="trx_addons_range_slider"
					data-range="<?php echo esc_attr($is_range ? 'true' : 'min'); ?>"
					data-min="<?php echo esc_attr($field_min); ?>"
					data-max="<?php echo esc_attr($field_max); ?>"
					data-step="<?php echo esc_attr($field_step); ?>"
					data-linked-field="<?php echo esc_attr($id); ?>"
			>
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
			?></div>
			<div class="trx_addons_range_result">
				<span class="trx_addons_range_result_caption"><?php echo esc_html( $args['field_title'] ); ?>:</span>
				<span class="trx_addons_range_result_value"><?php echo esc_html( $values[0] ) . ( count( $values ) > 1 ? ' - ' . $values[1] : '' ); ?></span>
			</div><?php

		// Text field
		} else if ( $args['field_type'] == 'text' ) {
			?><input type="text"
				name="_<?php echo esc_attr( $args['field_name'] ); ?>"
				class="sc_form_field_input"
				placeholder="<?php esc_attr_e( 'Type here ...', 'trx_addons' ); ?>"
				value="<?php echo esc_attr( $args['field_value'] );
			?>"><?php
		}

		// Show selected items total and button 'Clear All'
		if ( ! empty( $args['field_multiple'] ) && ! empty( $args['show_selected'] ) ) {
			?><div class="trx_addons_multiple_selected sc_form_field_items_selected">
				<span class="sc_form_field_items_selected_label">
					<span class="sc_form_field_items_selected_value"><?php echo empty( $args['field_value'] ) ? 0 : count( explode( ',', $args['field_value'] ) ); ?></span>
					<span class="sc_form_field_items_selected_caption"><?php esc_html_e( 'Selected', 'trx_addons' ); ?></span>
				</span>
				<a href="#" class="sc_form_field_items_selected_clear"><?php esc_html_e( 'Clear all', 'trx_addons' ); ?></a>
				<a href="#" class="sc_form_field_items_selected_select_all"><?php esc_html_e( 'Select all', 'trx_addons' ); ?></a>
			</div><?php
		}

		// Buttons Cancel' and 'Apply'
		if ( ( ! empty( $args['apply'] ) && ( $args['field_type'] != 'select' || ! empty( $args['field_multiple'] ) ) )
				||
			 ( empty( $args['apply'] ) && in_array( $args['field_type'], array( 'text', 'slider', 'range' ) ) )
		) {
			?><div class="trx_addons_search_buttons sc_form_field_buttons">
				<input type="button" class="trx_addons_search_cancel sc_button theme_button" value="<?php esc_attr_e( 'Cancel', 'trx_addons' ); ?>">
				<input type="button" class="trx_addons_search_apply sc_button theme_button" value="<?php esc_attr_e( 'Apply', 'trx_addons' ); ?>">
			</div><?php
		}

	?></div><?php
?></div>