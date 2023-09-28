<?php
/**
 * Template to display theme-specific products layout
 *
 * @package ThemeREX Addons
 * @since v1.85.0
 */

$args = get_query_var('trx_addons_args_sc_extended_products');

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_extended_products sc_extended_products_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr($args['css']) . '"';
	trx_addons_sc_show_attributes( 'sc_extended_products', $args, 'sc_wrapper' );
	?>><?php

	trx_addons_sc_show_titles( 'sc_extended_products', $args );

	?><div class="sc_extended_products_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_extended_products', $args, 'sc_items_wrapper' ); ?>><?php

	trx_addons_show_layout(
		do_shortcode(
			trx_addons_sc_make_string(
				'products',
				apply_filters(
					'trx_addons_filter_sc_extended_products_args_to_woocommerce',
					array_intersect_key(
						$args,
						array(
							'products'       => 1,         // Output mode - must be specified only one of next four keys
							'on_sale'        => 0,         //
							'best_selling'   => 0,         //
							'top_rated'      => 0,         //
							'per_page'       => 3,         // Deprecated. The 'limit' is used now (see the next argument).
							'limit'          => '-1',      // Results limit.
							'columns'        => '',        // Number of columns.
							'rows'           => '',        // Number of rows. If defined, limit will be ignored.
							'orderby'        => '',        // menu_order, title, date, rand, price, popularity, rating, or id.
							'order'          => '',        // ASC or DESC.
							'ids'            => '',        // Comma separated IDs.
							'skus'           => '',        // Comma separated SKUs.
							'category'       => '',        // Comma separated category slugs or ids.
							'cat_operator'   => 'IN',      // Operator to compare categories. Possible values are 'IN', 'NOT IN', 'AND'.
							'attribute'      => '',        // Single attribute slug.
							'terms'          => '',        // Comma separated term slugs or ids.
							'terms_operator' => 'IN',      // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
							'tag'            => '',        // Comma separated tag slugs.
							'tag_operator'   => 'IN',      // Operator to compare tags. Possible values are 'IN', 'NOT IN', 'AND'.
							'visibility'     => 'visible', // Product visibility setting. Possible values are 'visible', 'catalog', 'search', 'hidden'.
							'class'          => '',        // HTML class.
							'page'           => 1,         // Page for pagination.
							'paginate'       => false,     // Should results be paginated.
							'cache'          => true,      // Should shortcode output be cached.
						)
					)
				)
			)
		)
	);

	?></div><?php

	trx_addons_sc_show_links( 'sc_extended_products', $args );

?></div>
