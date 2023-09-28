<?php
$args = get_query_var('trx_addons_args_sc_supertitle_args');
if (!empty($args['media'])) {
	if (is_array($args['media'])) {
		$image_id = !empty($args['media']['id']) ? $args['media']['id'] : false;
		$image_url = !empty($args['media']['url']) ? $args['media']['url'] : false;
	} else {
		$image_id = $args['media'];
		$image_url = wp_get_attachment_image_src($image_id, 'full');
		$image_url = is_array($image_url) ? $image_url[0] : $image_url;
	}

	?><div class="sc_supertitle_media sc_supertitle_position_<?php
		echo esc_attr($args['float_position']);
		if (!empty($args['inline'])) echo ' sc_supertitle_display_inline';
	?>"><?php
			$image_alt = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true) : esc_html__('Icon', 'trx_addons');
			?><img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
	</div><?php
}
