<?php
/**
 * The style "default" of the Logo
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

$args = get_query_var('trx_addons_args_sc_layouts_logo');
?><a href="<?php echo is_front_page() ? '#' : esc_url(home_url('/')); ?>"
	<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
	class="sc_layouts_logo sc_layouts_logo_<?php
				echo esc_attr($args['type']);
				trx_addons_cpt_layouts_sc_add_classes($args);
				if (!empty($args['logo_height']) && (float)$args['logo_height'] > 0) {
					echo ' '.esc_attr(trx_addons_add_inline_css_class(
										'max-height:' . esc_attr(trx_addons_prepare_css_value($args['logo_height'])) . ';',
										'img.logo_image'
										)
									);
				}
		?>"<?php
	if ( ! empty($args['css']) ) {
		echo ' style="'.esc_attr($args['css']).'"';
	}
	trx_addons_sc_show_attributes('sc_layouts_logo', $args, 'sc_wrapper');
?>><?php
	if (!empty($args['logo'])) {
		$logo = trx_addons_get_attachment_url($args['logo'], 'full');
		if (!empty($logo)) {
			$attr = trx_addons_getimagesize($logo);
			?><img class="logo_image"
					src="<?php echo esc_url($logo); ?>"
					<?php
					if (!empty($args['logo_retina'])) {
						$logo_retina = trx_addons_get_attachment_url($args['logo_retina'], 'full');
						?>
						srcset="<?php echo esc_url( $logo_retina ); ?> 2x"
						<?php
					}
					?>
					alt="<?php echo esc_attr(!empty($args['logo_text']) ? $args['logo_text'] : get_bloginfo( 'name' )); ?>" <?php
					if (!empty($attr[3])) trx_addons_show_layout($attr[3]);
			?>><?php
		}
	} else if (!empty($args['logo_text']) || !empty($args['logo_slogan']) || apply_filters('trx_addons_filter_show_site_name_as_logo', true)) {
		if (empty($args['logo_text'])) $args['logo_text'] = get_bloginfo( 'name' );
		if (empty($args['logo_slogan'])) $args['logo_slogan'] = get_bloginfo( 'description', 'display' );
		if ($args['logo_text'] != '#')
			trx_addons_show_layout(trx_addons_prepare_macros($args['logo_text']), '<span class="logo_text">', '</span>');
		if ($args['logo_slogan'] != '#')
			trx_addons_show_layout(trx_addons_prepare_macros($args['logo_slogan']), '<span class="logo_slogan">', '</span>');
	}
?></a><?php

trx_addons_sc_layouts_showed('logo', true);
