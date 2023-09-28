<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_layouts_featured'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_layouts_featured_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}" class="sc_layouts_featured with_image without_content<?php
		$element->sc_add_common_classes('sc_layouts_featured');
		?>" style="background-image: url(<?php echo esc_url(trx_addons_get_no_image()); ?>);">
		<h5 class="sc_layouts_featured_title sc_layouts_featured_title_preview"><?php esc_html_e('On the real page instead of this image you will see Featured image of current post (page)', 'trx_addons'); ?></h5>
</div>