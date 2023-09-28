<?php
/**
 * The style "default" of the Users list
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */

$args = get_query_var('trx_addons_args_sc_users');
if ( ! is_array( $args['roles'] ) ) {
	$args['roles'] = (array) $args['roles'];
}
$args['hover'] = apply_filters( 'trx_addons_filter_featured_hover', ! empty( $args['hover'] ) ? $args['hover'] : 'icon', 'sc_users' );

$users = get_users(array(
						'orderby' => 'display_name',
						'order' => 'ASC'
						)
					);

if ( is_array($users) && count($users) > 0 ) {
	$total = 0;
	foreach ($users as $k => $user) {
		$accept = $total < $args['number'];
		if ( $accept && is_array($user->roles) && count( $args['roles'] ) > 0 ) {
			if ( is_array($user->roles) && count($user->roles) > 0 ) {
				$accept = false;
				foreach ( $user->roles as $role ) {
					if ( in_array($role, $args['roles']) ) {
						$accept = true;
						break;
					}
				}
			}
		}
		if ( ! $accept ) {
			unset($users[ $k ]);
		} else {
			$total++;
		}
	}
}


$posts_count = count( $users );

if ( $posts_count > 0 ) {
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	$args['slider'] = $args['slider'] > 0 && $posts_count > $args['columns'];

	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_users sc_users_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
	?>><?php

		trx_addons_sc_show_titles('sc_users', $args);

		if ($args['slider']) {
			$args['slides_space'] = max(0, (int) $args['slides_space']);
			$args['slides_min_width'] = 220;
			trx_addons_sc_show_slider_wrap_start('sc_users', $args);
		
		} else if ($args['columns'] > 1) {
			?><div class="sc_users_columns_wrap sc_item_columns sc_item_posts_container <?php
				echo esc_attr(trx_addons_get_columns_wrap_class())
					. ( ! empty( $args['no_margin'] ) ? ' no_margin' : ' columns_padding_bottom' )
					. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $users ) );
			?>"><?php
		
		} else {
			?><div class="sc_users_content sc_item_content sc_item_posts_container"><?php
		}	

		foreach ($users as $user) {
			$args['user'] = $user;
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'users/tpl.' . trx_addons_esc($args['type']) . '-item.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'users/tpl.default-item.php'
											), 
											'trx_addons_args_sc_users',
											$args
										);
		}

		?></div><?php

		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_users', $args);
		}

		trx_addons_sc_show_links('sc_users', $args);

	?></div><?php
}
