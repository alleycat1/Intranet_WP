<?php
/**
 * The template to display the download's features on the single page
 *
 * @package ThemeREX Addons
 * @since v1.6.29
 */

$trx_addons_args = get_query_var('trx_addons_args_sc_edd_details');

$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

?><div<?php
		if (!empty($trx_addons_args['id'])) echo ' id="'.esc_attr($trx_addons_args['id']).'"';
		if (!empty($trx_addons_args['class'])) echo ' class="'.esc_attr($trx_addons_args['class']).'"';
		if (!empty($trx_addons_args['css'])) echo ' style="'.esc_attr($trx_addons_args['css']).'"';
?>><?php

do_action('trx_addons_action_edd_before_features_section', $trx_addons_args, $trx_addons_meta);

// Details
?><section class="downloads_page_section downloads_page_details"><?php
	// Title
	?><h4 class="downloads_page_section_title"><?php esc_html_e('Details', 'trx_addons'); ?></h4><?php
	// Data
	?><div class="downloads_page_features_list"><?php

		do_action('trx_addons_action_edd_before_features_list', $trx_addons_args, $trx_addons_meta);

		// Price
		if (false && !empty($trx_addons_args['type'])) {
			$variable = edd_has_variable_prices(get_the_ID());
			?><span class="downloads_page_section_item downloads_page_section_item_price"><?php
				?><span class="downloads_page_label"><?php esc_html_e('Price:', 'trx_addons'); ?></span><?php
				?><span class="downloads_page_data downloads_page_price<?php if ($variable) echo ' downloads_page_price_variable'; ?>"><?php
					if ($variable) echo '<span>'.esc_html__('from', 'trx_addons').'</span>';
					edd_price(get_the_ID());
				?></span>
			</span><?php
		}
		// Date created
		if (!empty($trx_addons_meta['date_created'])) {
			?><span class="downloads_page_section_item downloads_page_section_item_created"><?php
				?><span class="downloads_page_label"><?php esc_html_e('Created:', 'trx_addons'); ?></span><?php
				?><span class="downloads_page_data"><?php echo date_i18n(get_option('date_format'), strtotime($trx_addons_meta['date_created'])); ?></span>
			</span><?php
		}
		// Date updated
		if (!empty($trx_addons_meta['date_updated'])) {
			?><span class="downloads_page_section_item downloads_page_section_item_updated"><?php
				?><span class="downloads_page_label"><?php esc_html_e('Updated:', 'trx_addons'); ?></span><?php
				?><span class="downloads_page_data"><?php echo date_i18n(get_option('date_format'), strtotime($trx_addons_meta['date_updated'])); ?></span>
			</span><?php
		}
		// Version
		if (!empty($trx_addons_meta['version'])) {
			?><span class="downloads_page_section_item downloads_page_section_item_version"><?php
				?><span class="downloads_page_label"><?php esc_html_e('Version:', 'trx_addons'); ?></span><?php
				?><span class="downloads_page_data"><?php echo esc_html($trx_addons_meta['version']); ?></span>
			</span><?php
		}

		do_action('trx_addons_action_edd_before_additional_details', $trx_addons_args, $trx_addons_meta);

		// Additional details
		if (!empty($trx_addons_meta['details']) && is_array($trx_addons_meta['details'])) {
			foreach ($trx_addons_meta['details'] as $detail) {
				if (!empty($detail['title'])) {
					?><span class="downloads_page_section_item"><?php
						?><span class="downloads_page_label"><?php
							trx_addons_show_layout(trx_addons_prepare_macros($detail['title'])); 
						?>:</span><?php
						?><span class="downloads_page_data"><?php 
							trx_addons_show_layout(trx_addons_prepare_macros($detail['value'])); 
						?></span>
					</span><?php
				}
			}
		}

		do_action('trx_addons_action_edd_after_features_list', $trx_addons_args, $trx_addons_meta);

	?></div>
</section><?php

do_action('trx_addons_action_edd_after_features_section', $trx_addons_args, $trx_addons_meta);

do_action('trx_addons_action_edd_before_tags_section', $trx_addons_args, $trx_addons_meta);

// Tags
?><section class="downloads_page_section downloads_page_tags"><?php
	// Title
	?><h4 class="downloads_page_section_title"><?php esc_html_e('Tags', 'trx_addons'); ?></h4><?php
	// Data
	?><div class="downloads_page_data">
		<?php trx_addons_show_layout(trx_addons_get_post_terms('<span class="downloads_page_data_separator"></span>', get_the_ID(), TRX_ADDONS_EDD_TAXONOMY_TAG)); ?>
	</div>
</section><?php

do_action('trx_addons_action_edd_after_tags_section', $trx_addons_args, $trx_addons_meta);

?></div>