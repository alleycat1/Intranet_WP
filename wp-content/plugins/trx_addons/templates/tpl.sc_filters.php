<?php
/**
 * The template to display shortcode's filters header (title, subtitle and tabs)
 *
 * @package ThemeREX Addons
 * @since v1.6.54
 */

extract(get_query_var('trx_addons_args_sc_show_filters'));

$sc_blogger_filters_ajax = true;
$sc_blogger_filters_id = 'blogger_filters';
$args['sc'] = 'sc_blogger';

if (!empty($args['filters_title']) || !empty($args['filters_subtitle']) || count($tabs) > 0 ) {
	?><div class="sc_item_filters sc_blogger_filters sc_blogger_tabs sc_blogger_tabs_ajax<?php
		echo ' sc_item_filters_align_'.esc_attr($args['filters_title_align']);
		if ( count($tabs) == 0 ) {
			echo ' sc_item_filters_no_tabs';
		}
		if ( empty($args['filters_title']) && empty($args['filters_subtitle']) ) {
			echo ' sc_item_filters_no_title';
			if ( $args['filters_tabs_position'] == 'left' || count($tabs) == 0 ) {
				echo ' sc_item_filters_empty';
			}
		}
	?>" data-params="<?php echo esc_attr(serialize(apply_filters('trx_addons_filter_sc_args_to_serialize', $args))); ?>"><?php
		if (!empty($args['filters_title']) || !empty($args['filters_subtitle']) || ($args['filters_tabs_position'] == 'left' && count($tabs) > 0) ) {
			?><div class="sc_item_filters_header"><?php
				if (!empty($args['filters_title'])) {
					?><h3 class="sc_item_filters_title"><?php echo esc_html($args['filters_title']); ?></h3><?php
				}
				if (!empty($args['filters_subtitle'])) {
					?><h5 class="sc_item_filters_subtitle"><?php echo esc_html($args['filters_subtitle']); ?></h5><?php
				}
			?></div><?php
		}
		if (count($tabs) > 0) {
			?>
			<ul class="sc_item_filters_tabs<?php
				if ( ! empty( $args['filters_tabs_on_hover'] ) ) {
					echo ' sc_item_filters_tabs_open_on_hover';
				}
			?>"><?php
				// Add "All" tab
				if ( ! empty( $args['filters_all'] ) ) {
					$sc_bloggertitle = empty($args['filters_all_text']) ? esc_html__('All','trx_addons') : $args['filters_all_text'];
					$sc_bloggerid = 'all';
					?><li<?php echo ( empty( $args['filters_active'] ) || $args['filters_active'] == $sc_bloggerid ? ' class="sc_item_filters_tabs_active"' : '' ); ?>><?php
						?><a href="<?php echo esc_url( trx_addons_get_hash_link( sprintf( '#%s_%s_content', $sc_blogger_filters_id, $sc_bloggerid ) ) ); ?>"
							class="sc_item_filters_all<?php echo ( empty( $args['filters_active'] ) || $args['filters_active'] == $sc_bloggerid ? ' active' : '' ); ?>"
							data-tab="<?php echo esc_attr( $sc_bloggerid ); ?>"
							data-page="1"><?php
								echo($sc_bloggertitle);
						?></a></li><?php
				}
				foreach ( $tabs as $sc_bloggerid => $sc_bloggertitle ) {
					$sc_bloggertitle = trx_addons_sc_blogger_remove_terms_counter($sc_bloggertitle);
					?><li<?php echo ( (int) $args['filters_active'] == (int) $sc_bloggerid ? ' class="sc_item_filters_tabs_active"' : '' ); ?>><?php
						?><a href="<?php echo esc_url(trx_addons_get_hash_link(sprintf('#%s_%s_content', $sc_blogger_filters_id, $sc_bloggerid))); ?>"
							<?php echo ( (int) $args['filters_active'] == (int) $sc_bloggerid ? ' class="active"' : '' ); ?>
							data-tab="<?php echo esc_attr($sc_bloggerid); ?>"
							data-page="1"><?php
							trx_addons_show_layout( $sc_bloggertitle );
						?></a></li><?php
				}
				?>
			</ul><?php
		}
		if ( ( !empty($args['filters_more_text']) || ( count($tabs) == 0 && $args['filters_tabs_position'] == 'left' ) )
			&& ( !empty($args['filters_title']) || !empty($args['filters_subtitle']) )
			&& ( count($tabs) == 0 || $args['filters_tabs_position'] == 'left' )
		) {
			$link = (int) $args['cat'] > 0 && ! empty( $args['taxonomy'] )
						? get_term_link( (int)$args['cat'], $args['taxonomy'] )
						: '';
			if ( empty( $link ) || is_wp_error( $link ) || ! is_string( $link ) ) {
				$link = ! empty( $args['post_type'] )
							? get_post_type_archive_link( $args['post_type'] )
							: home_url( '/' );
			}
			?><div class="sc_item_filters_more_link_wrap"><?php
				if ( ! empty($args['filters_more_text']) ) {
					?><a href="<?php echo esc_url( $link ); ?>" class="sc_item_filters_more_link sc_button sc_button_simple"><?php
						echo esc_html($args['filters_more_text']);
					?></a><?php
				}
			?></div><?php
		}
		?>
	</div><?php
}
