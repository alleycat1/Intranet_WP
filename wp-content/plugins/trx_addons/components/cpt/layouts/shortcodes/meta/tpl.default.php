<?php
/**
 * The template to display block with post meta
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

$args = get_query_var('trx_addons_args_sc_layouts_meta');

?><div<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
   class="post_meta sc_layouts_meta sc_layouts_meta_<?php
   echo esc_attr($args['type']);
   if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
   trx_addons_sc_show_attributes('sc_layouts_meta', $args, 'sc_wrapper');
?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>>
	<?php
	trx_addons_sc_show_post_meta('sc_layouts_meta', apply_filters('trx_addons_filter_post_meta_args', array(
				'components' => is_array($args['components']) ? implode(',', $args['components']) : $args['components'],
				'share_type' => $args['share_type'],
				'seo' => false,
				'theme_specific' => false
				), 'sc_layouts_meta', 1)
			);
?></div>