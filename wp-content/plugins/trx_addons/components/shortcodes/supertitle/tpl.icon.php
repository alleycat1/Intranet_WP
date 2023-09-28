<?php
$args = get_query_var('trx_addons_args_sc_supertitle_args');
if (!empty($args['item_icon'])) {
	$style = (!empty($args['color']) ? 'color: '.esc_attr($args['color']) .' !important;' : '')
			. (!empty($args['size']) ? 'font-size: '.esc_attr($args['size']) .';' : '');
	?><div class="sc_supertitle_icon sc_supertitle_position_<?php
		echo esc_attr($args['float_position']);
		if (!empty($args['inline'])) echo ' sc_supertitle_display_inline';
	?>">
		<span class="sc_icon_type_icons<?php
			echo  ' ' . esc_attr($args['item_icon'])
				. (!empty($style)
					? ' ' . trx_addons_add_inline_css_class($style)
					: ''
					);
		?>"></span>
	</div><?php
}
