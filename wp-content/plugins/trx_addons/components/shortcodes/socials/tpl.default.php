<?php
/**
 * The style "default" of the Socials
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_socials');

$show = str_replace('default', 'icons', $args['type']);
$icon_present = '';
$socials_default = !is_array($args['icons']) 
					|| count($args['icons'])==0 
					|| (count($args['icons'])==1 && (count($args['icons'][0])<2 || empty($args['icons'][0]['link'])))
						? ( $args['icons_type']=='socials'
								? trx_addons_get_socials_links('', $show)
								: trx_addons_get_share_links( array(
									'type' => 'block',
									'caption' => '',
									'style' => $show,
									'echo' => false
									) )
							)
						: '';
if ($socials_default || (is_array($args['icons']) && count($args['icons'])) > 0 && count($args['icons'][0]) > 1 && !empty($args['icons'][0]['link'])) {
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
		class="sc_socials sc_socials_<?php
				echo esc_attr($args['type']);
				if (!empty($args['align'])) echo ' sc_align_'.esc_attr($args['align']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
				?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		trx_addons_sc_show_attributes('sc_socials', $args, 'sc_wrapper');
	?>><?php
	
		trx_addons_sc_show_titles('sc_socials', $args);
	
		?><div class="<?php echo esc_attr($args['icons_type']); ?>_wrap sc_item_content"><?php
	
		if ($socials_default) {
			trx_addons_show_layout($socials_default);
		} else {
			$icons = array();
			foreach ($args['icons'] as $item) {
				$icon = !empty($item['icon_type']) 
						&& !empty($item['icon_' . $item['icon_type']]) 
						&& $item['icon_' . $item['icon_type']] != 'empty' 
							? $item['icon_' . $item['icon_type']] 
							: '';
				if (!empty($icon)) {
					if (strpos($icon_present, $item['icon_type'])===false)
						$icon_present .= (!empty($icon_present) ? ',' : '') . $item['icon_type'];
				} else {
					if (!empty($item['icon']) && strtolower($item['icon'])!='none') $icon = $item['icon'];
				}
				if ( ! empty( $item['link'] ) 
					&& (
						( ! empty( $icon ) && strpos( $show, 'icons' ) !== false )
						||
						( ! empty( $item['title'] ) && strpos( $show, 'names' ) !== false )
					)
				) {
					$icons[] = array(
						'name' => $icon,
						'title' => !empty($item['title']) ? $item['title'] : '',
						'url' => $item['link']
					);
				}
			}
			trx_addons_show_layout(
				$args['icons_type']=='socials'
					? trx_addons_get_socials_links_custom($icons, '', $show)
					: trx_addons_get_share_links( array(
									'type' => 'block',
									'caption' => '',
									'style' => $show,
									'echo' => false
									), $icons )
			);
		}
		
		?></div><?php
	
		trx_addons_sc_show_links('sc_icons', $args);
	
	?></div><?php
	
	trx_addons_load_icons($icon_present);
}
