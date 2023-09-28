<?php
/**
 * The style "default" of the Users list
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */

$args = get_query_var('trx_addons_args_sc_users');

if ( ! empty( $args['user']->ID ) ) {
	$link = get_author_posts_url( $args['user']->ID );
	$name = ! empty( $args['user']->display_name ) ? $args['user']->display_name : '' ;
	$description = ! empty( $args['user']->description ) ? $args['user']->description : '';
	$email = ! empty( $args['user']->user_email ) ? $args['user']->user_email : '';
	$user_meta = trx_addons_users_get_meta($args['user']->ID);
	$position = ! empty($user_meta['position']) ? $user_meta['position'] : '';
	$socials = ! empty($user_meta['socials']) ? $user_meta['socials'] : array();

	if ($args['slider']) {
		?><div class="slider-slide swiper-slide"><?php
	} else if ($args['columns'] > 1) {
		?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?>"><?php
	}
	?>
	<div class="<?php
			echo apply_filters(
					'trx_addons_filter_sc_item_classes',
					'sc_users_item sc_item_container post_container',
					'sc_users',
					$args
				);
		?>"<?php
		trx_addons_add_blog_animation('users', $args);
	?>><?php
		if ( ! empty( $email ) ) {
			?><div class="post_featured user_avatar sc_users_item_thumb<?php
				if ( ! empty( $args['hover'] ) ) {
					echo ' hover_' . esc_attr( $args['hover'] );
				}
			?>" itemprop="image"><?php
				$mult = trx_addons_get_retina_multiplier();
				echo get_avatar( $email, 370 * $mult );
				if ( ! empty( $args['hover'] ) ) {
					?><div class="mask trx_addons_mask"></div><?php
					do_action( 'trx_addons_action_add_hover_icons', $args['hover'], array(
						'link' => $link
					) );
				}
				if ( ! empty($link) ) {
					?><a href="<?php echo esc_url($link); ?>" class="post_link sc_users_item_link"></a><?php
				}
			?></div><?php
		}
		?>
		<div class="sc_users_item_info">
			<div class="sc_users_item_header"><?php
				$title_tag = $args['type'] == 'list' ? 'h5' : 'h4';
				?><<?php echo esc_html( $title_tag ); ?> class="sc_users_item_title entry-title"><?php
					if ( ! empty($link) ) {
						?><a href="<?php echo esc_url($link); ?>"><?php echo esc_html( $name ); ?></a><?php
					} else {
						echo esc_html( $name );
					}
				?></<?php echo esc_html( $title_tag ); ?>><?php
				if ( ! empty( $position ) ) {
					?><div class="sc_users_item_position"><?php echo esc_html($position); ?></div><?php
				}
			?></div><?php
			if ( ! empty( $description ) ) {
				?><div class="sc_users_item_content"><?php echo wp_kses( nl2br( $description ), 'trx_addons_kses_content' ); ?></div><?php
			}
			if ( ! empty($socials) ) {
				?><div class="sc_users_item_socials socials_wrap"><?php trx_addons_show_layout( trx_addons_get_socials_links_custom( $socials ) ); ?></div><?php
			}
			if ( ! empty($link) ) {
				?><div class="sc_users_item_button"><a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_users', $args)); ?>"><?php esc_html_e('View all posts', 'trx_addons'); ?></a></div><?php
			}
		?></div>
	</div><?php
	if ($args['slider'] || $args['columns'] > 1) {
		?></div><?php
	}
}