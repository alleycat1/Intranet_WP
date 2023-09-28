<?php
/**
 * The style "default" of the Featured image
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_layouts_featured');

$preview_image = trx_addons_is_preview( 'elementor' ) && get_post_type()==TRX_ADDONS_CPT_LAYOUTS_PT ? trx_addons_get_no_image() : '';

if ( ! empty( $preview_image ) && empty( $args['content'] ) ) {
	$args['content'] = '<h5 class="sc_layouts_featured_title sc_layouts_featured_title_preview">'
							. esc_html__('On the real page instead of this image you will see Featured image of current post (page)', 'trx_addons')
						. '</h5>';
}

$need_content = !empty($args['content']);
$need_image = apply_filters('trx_addons_filter_featured_image_override', ( ! get_header_image() 
																			&& (
																				( trx_addons_is_singular() && has_post_thumbnail() )
																				|| is_category()
																				|| is_tax()
																				)
																			//&& in_array(get_post_type(), array('post', 'page')
																			)
																			|| ! empty($preview_image)
							);
if ( $need_content || $need_image )  {
	if ($need_image) {
		$trx_addons_attachment_src = !empty($preview_image) ? $preview_image : trx_addons_get_current_mode_image();
		if (!empty($trx_addons_attachment_src))
			$args['css'] = 'background-image:url('.esc_url($trx_addons_attachment_src).');' . (!empty($preview_image) ? 'min-height: 10em;' : '') . $args['css'];
		else
			$need_image = false;
	}
	if ( $need_content || $need_image )  {
		if (!empty($args['height']))
			$args['css'] = trx_addons_get_css_dimensions_from_values(array('min-height' => $args['height'])) . ';' . $args['css'];
		?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> class="sc_layouts_featured<?php
				trx_addons_cpt_layouts_sc_add_classes($args);
				echo esc_attr($need_content && empty($preview_image) ? ' with' : ' without') . '_content';
				echo esc_attr($need_image ? ' with' : ' without') . '_image';
				if (!empty($args['css'])) echo ' '.trx_addons_add_inline_css_class($args['css']);
				trx_addons_sc_show_attributes('sc_layouts_featured', $args, 'sc_wrapper');
		?>"><?php
			
			if ($need_content) trx_addons_show_layout($args['content'], '<div class="sc_layouts_featured_content">', '</div>');

		?></div><?php

		trx_addons_sc_layouts_showed('featured', $need_image);
	}
}
