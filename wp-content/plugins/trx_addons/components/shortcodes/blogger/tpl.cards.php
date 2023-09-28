<?php
/**
 * The style "Cards" of the Blogger
 *
 * @package ThemeREX Addons
 * @since v2.3.1
 */

$args = get_query_var('trx_addons_args_sc_blogger');

$templates = trx_addons_components_get_allowed_templates('sc', 'blogger');
$template  = ! empty( $args['template_'.$args['type']] ) && isset($templates[$args['type']][$args['template_'.$args['type']]])
				? $templates[$args['type']][$args['template_'.$args['type']]]
				: $templates['default']['classic'];

// Override the shortcode's args from the template parameter 'args'
if ( !empty($template['args']) && is_array($template['args']) ) {
	$args = array_merge( $args, $template['args'] );
}

$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_status' => 'publish',
	'ignore_sticky_posts' => true
);

// Posts per page
if ( empty( $args['ids'] ) || count( explode( ',', $args['ids'] ) ) > $args['count'] ) {
	$query_args['posts_per_page'] = $args['count'];
	if ( ! trx_addons_is_off($args['pagination']) && $args['page'] > 1 ) {
		if ( empty( $args['offset'] ) ) {
			$query_args['paged'] = $args['page'];
		} else {
			$query_args['offset'] = $args['offset'] + $args['count'] * ( $args['page'] - 1 );
		}
	} else {
		$query_args['offset'] = $args['offset'];
	}
}

// Post type
$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], $args['post_type']);

// Sort order
$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);

// Filters
$tabs = trx_addons_sc_get_filters_tabs('sc_blogger', $args);
if (count($tabs) > 0 && !empty($args['filters_active']) && $args['filters_active'] != 'all') {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, '', '', $args['filters_active'], $args['filters_taxonomy']);
} else if ( empty($args['ids']) ) {
	$query_args = trx_addons_query_add_posts_and_cats($query_args, '', '', $args['cat'], $args['taxonomy']);
}

// Exclude posts
if ( ! empty( $args['posts_exclude'] ) ) {
	$query_args['post__not_in'] = is_array( $args['posts_exclude'] )
									? $args['posts_exclude']
									: explode( ',', str_replace( array( ';', ' ' ), array( ',', '' ), $args['posts_exclude'] ) );
}

$query_args = apply_filters( 'trx_addons_filter_query_args', $query_args, 'sc_blogger' );

$query = new WP_Query( $query_args );

if ( $query->post_count > 0 || count($tabs) > 0 ) {

	$args = apply_filters( 'trx_addons_filter_sc_prepare_atts_before_output', $args, $query_args, $query, 'blogger.cards' );

	$posts_count = ($args['count'] > $query->post_count) ? $query->post_count : $args['count'];

	$args['columns'] = 1;
	$args['slider']  = 0;

	if ( empty($args['template_' . $args['type']]) ) {
		$args['template_' . $args['type']] = 'default';
	}

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?>
		class="sc_blogger sc_blogger_<?php
			echo esc_attr( $args['type'] );
			echo ' sc_blogger_' . esc_attr( $args['type'] ) . '_' . esc_attr( $args[ 'template_' . $args['type'] ] );
			echo ' sc_item_filters_tabs_' . esc_attr( count( $tabs ) > 0 ? $args['filters_tabs_position'] : 'none' );
			if ( ! empty( $args['align'] ) && ( ! function_exists( 'trx_addons_gutenberg_is_preview' ) || ! trx_addons_gutenberg_is_preview() ) ) echo ' align' . esc_attr( $args['align'] ); 
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] ); 
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_wrapper');
		?>><?php

		// Show titles and links
		if ( ! empty( $args['subtitle'] ) || ! empty( $args['title'] ) || ! empty( $args['description'] ) || ! empty( $args['description'] ) || ( ! empty( $args['link'] ) && ! empty( $args['link_text'] ) ) ) {
			?><div class="sc_blogger_<?php echo esc_attr( $args['type'] ); ?>_header"><?php
				trx_addons_sc_show_titles('sc_blogger', $args);
				trx_addons_sc_show_links('sc_blogger', $args);
				trx_addons_sc_show_pagination('sc_blogger', $args, $query);
			?></div><?php
		}

		// Start content wrapper
		?><div class="sc_blogger_<?php echo esc_attr( $args['type'] ); ?>_content"><?php

			// Show filters
			if ( count($tabs) > 0 ) {
				?><div class="sc_item_filters_wrap"><?php
			}
			trx_addons_sc_show_filters('sc_blogger', $args, $tabs);

			// Shortcode's wrapper
			?><div class="sc_blogger_content sc_item_content sc_item_posts_container no_open_full_post<?php
				if ( ! empty($args['posts_container']) ) {
					echo ' '.esc_attr($args['posts_container'])
						. ( $args['columns'] > 1 && strpos($args['posts_container'], 'columns_padding_bottom') !== false
								? esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $query ) )
								: '' );
				}
			?>"<?php trx_addons_sc_show_attributes('sc_blogger', $args, 'sc_items_wrapper'); ?>><?php

			$args['item_number'] = 0;
			$posts_rest = $posts_count;

			while ( $query->have_posts() ) { $query->the_post();

				$args['item_number']++;
				
				$args_orig = $args;		// Save original shortcode's agruments
				
				if ( ! apply_filters('trx_addons_filter_sc_blogger_template', false, $args) ) {
					$args['item_number'] += !trx_addons_is_off($args['pagination']) && $args['page'] > 1 ? ( $args['page'] - 1 ) * $args['count'] : 0;
					trx_addons_get_template_part(array(
												TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($args['type']).'-item.php',
												TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default-item.php'
												),
												'trx_addons_args_sc_blogger', 
												$args
											);
				}

				$args = $args_orig;		// Restore original shortcode's arguments

			}

			wp_reset_postdata();

			if ( count($tabs) > 0 ) {
				?></div><?php
			}

		// End content wrapper
		?></div><?php

		// End posts container
		?></div><?php

	?></div><?php
}