<?php
/**
 * The style "default" of the Layouts
 *
 * @package ThemeREX Addons
 * @since v1.6.06
 */

$args = get_query_var('trx_addons_args_sc_layouts');
if ( empty($args['effect']) ) {
	$args['effect'] = 'slide';
}

if (!empty($args['layout']) || !empty($args['template']) || !empty($args['content'])) {
	if ($args['type'] == 'panel' && (int) $args['modal'] == 1) {
		?><div class="sc_layouts_panel_hide_content"></div><?php
	}
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_layouts sc_layouts_<?php
				echo esc_attr($args['type']);
				if (!empty($args['layout'])) {
					echo ' sc_layouts_' . esc_attr($args['layout']);
				} else if (!empty($args['template'])) {
					echo ' sc_layouts_' . esc_attr($args['template']);
				}
				if ($args['type'] == 'panel') {
					echo ' sc_layouts_panel_' . esc_attr($args['position']);
					echo ' sc_layouts_effect_' . esc_attr($args['effect']);
				}
				if (!trx_addons_is_off($args['show_on'])) {
					echo ' sc_layouts_show_' . esc_attr($args['show_on']);
				}
				if (!empty($args['class'])) {
					echo ' '.esc_attr($args['class']);
				}
		?>"
		data-delay="<?php echo esc_attr( $args['show_delay'] ); ?>"
		<?php
		if ($args['type'] == 'panel') {
			echo ' data-panel-position="' . esc_attr($args['position']) . '"';
			echo ' data-panel-effect="' . esc_attr($args['effect']) . '"';
			if ( ! empty( $args['panel_class'] ) ) {
				echo ' data-panel-class="' . esc_attr($args['panel_class']) . '"';
				// Shift page_wrap when panel is opened
				$panel_wrap_class = trx_addons_get_option('page_wrap_class');
				if ( (int) $args['shift_page'] == 1 && ! empty( $panel_wrap_class ) ) {
					$size = empty( $args['size'] ) || $args['size'] == 'auto' ? '14em' : $args['size'];
					if ( $args['position'] == 'left' ) {
						$transform = "translateX({$size})";
					} else if ( $args['position'] == 'right' ) {
						$transform = "translateX(-{$size})";
					} else if ( $args['position'] == 'top' ) {
						$transform = "translateY({$size})";
					} else if ( $args['position'] == 'bottom' ) {
						$transform = "translateY(-{$size})";
					}
					trx_addons_add_inline_css(
						"{$panel_wrap_class} {
							-webkit-transition: -webkit-transform 0.8s ease;
							   	-ms-transition: -ms-transform 0.8s ease;
									transition: transform 0.8s ease;
						}
						body.{$args['panel_class']}_opened {$panel_wrap_class} {
							-webkit-transform: {$transform};
							    -ms-transform: {$transform};
									transform: {$transform};
						}"
					);
				}
			}
		}
		trx_addons_sc_show_attributes('sc_layouts_layout', $args, 'sc_wrapper');
	?>><?php
		if ($args['type'] == 'panel' ) {
			?><div class="sc_layouts_panel_inner"><?php
		}
		// Show layout
		if (!empty($args['layout'])) {
			$args['content'] = trx_addons_cpt_layouts_show_layout($args['layout'], 0, false);

		// Show template
		} else if (!empty($args['template'])) {
			$args['content'] = trx_addons_cpt_layouts_show_layout($args['template'], 0, false);
		}
		if (!empty($args['content'])) {
			if ($args['type'] == 'popup' && strpos($args['content'], '<iframe') !== false) {
				$args['content'] = preg_replace( '/(<iframe[^>]*)(src=)/i', '${1}data-src=', trx_addons_make_video_autoplay($args['content']), 1 );
			}
			trx_addons_show_layout($args['content']);
		}
		// Add Close button
		if ($args['type'] == 'panel') {
			?><a href="#" class="sc_layouts_panel_close trx_addons_button_close"><span class="sc_layouts_panel_close_icon trx_addons_button_close_icon"></span></a><?php
		}
		if ($args['type'] == 'panel' ) {
			?></div><?php
		}
	?></div><?php
}
