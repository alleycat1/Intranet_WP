<?php
/**
 * The style "inline" of the Widget "WooCommerce Search"
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

$trx_addons_args = get_query_var('trx_addons_args_widget_woocommerce_search');
extract($trx_addons_args);

$params = trx_addons_widget_woocommerce_search_query_params($trx_addons_args['fields']);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><div <?php if (!empty($trx_addons_args['id'])) echo ' id="'.esc_attr($trx_addons_args['id']).'"'; ?>
	class="sc_form trx_addons_woocommerce_search trx_addons_woocommerce_search_type_<?php 
		echo esc_attr($trx_addons_args['type']);
		if (!empty($trx_addons_args['class'])) echo ' '.esc_attr($trx_addons_args['class']);
		?>"<?php
	if (!empty($trx_addons_args['css'])) echo ' style="'.esc_attr($trx_addons_args['css']).'"'; 
?>>
	<form class="trx_addons_woocommerce_search_form sc_form_form sc_form_custom" action="<?php echo esc_url(trx_addons_woocommerce_get_shop_page_link()); ?>" method="get"><?php
		
		// Search options
		?><div class="trx_addons_woocommerce_search_form_options"><?php
			// Fields
			if (is_array($trx_addons_args['fields'])) {
				$not_empty = false;
				foreach ($trx_addons_args['fields'] as $fld) {
					if (!trx_addons_is_off($fld['text'])) {
						?><span class="trx_addons_woocommerce_search_form_text"><?php echo wp_kses_data(trx_addons_parse_codes($fld['text'])); ?></span><?php
					}
					if ( trx_addons_is_off( $fld['filter'] ) ) {
						continue;
					}
					$tax_name = $fld['filter'];
					$field_type = in_array( $tax_name, array('s', 'min_price', 'max_price') ) ? 'input' : 'select';
					if ( ! trx_addons_is_off( $params[ $tax_name ] ) ) {
						$not_empty = true;
					}
					?><span class="trx_addons_woocommerce_search_form_field trx_addons_woocommerce_search_form_field_type_<?php echo esc_attr($field_type); ?>"><input type="<?php echo ('input' == $field_type ? 'text' : 'hidden'); ?>" value="<?php echo esc_attr($params[$tax_name]); ?>" name="<?php echo esc_attr($tax_name); ?>"><?php
						if ( $field_type == 'select' ) {
							$any = esc_html__('any', 'trx_addons');
							$list = trx_addons_array_merge(
										array( '' => $any ),
										trx_addons_get_list_terms(false,
																$tax_name,
																array(
																	'hide_empty' => 1,
																	'return_key' => 'slug'
																	)
																)
										);
							?><span class="trx_addons_woocommerce_search_form_field_label"><?php
								echo isset( $list[ $params[ $tax_name ] ] ) ? wp_kses( $list[ $params[ $tax_name ] ], 'trx_addons_kses_content' ) : $any;
							?></span>
							<ul class="trx_addons_woocommerce_search_form_field_list"><?php
								if ( is_array( $list ) ) {
									foreach ( $list as $k => $v ) {
										?><li data-value="<?php echo esc_attr( $k ); ?>"><?php echo wp_kses( $v, 'trx_addons_kses_content'); ?></li><?php
									}
								}
							?></ul><?php
						}
					?></span><?php
				}
			}
			
			if ( ! empty( $trx_addons_args['last_text'] ) ) {
				?><span class="trx_addons_woocommerce_search_last_text"><?php echo esc_html( $trx_addons_args['last_text'] ); ?></span><?php
			}
		
		// Submit button
		?></div><div class="trx_addons_woocommerce_search_form_button">
			<button class="trx_addons_woocommerce_search_button trx_addons_icon-search"<?php if (!$not_empty) echo 'disable="disable"'; ?>><?php echo esc_attr(!empty($trx_addons_args['button_text']) ? $trx_addons_args['button_text'] : __('Filter now', 'trx_addons')); ?></button>
		</div>
	</form>
</div><?php

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
