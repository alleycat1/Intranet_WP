<?php
/**
 * The style "default" of the shortcode Matches (section "Main matches")
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

$match = get_query_var('trx_addons_args_sc_matches_item');

$meta = $match['meta'];

// Competition type == 'pair'
if (!empty($meta['player1']) && !empty($meta['player2'])) {

	$is_match_page = trx_addons_is_single() && get_the_ID() == $match['id'];
	
	global $post;

	?><div data-post-id="<?php echo esc_attr($match['id']); ?>" class="sc_matches_item sc_item_container post_container sc_matches_item_pair"><?php
		// Player 1
		?><div class="sc_matches_item_player sc_matches_item_player1"><?php
			$post = get_post($meta['player1']);
			setup_postdata($post);
			// Player's logo
			if (has_post_thumbnail()) {
				trx_addons_get_template_part('templates/tpl.featured.php',
											'trx_addons_args_featured',
											apply_filters('trx_addons_filter_args_featured', array(
														'class' => 'sc_matches_item_thumb',
														'hover' => '',
														'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'sport-player')
														), 'sport-player')
											);
			}
			// Name or Command
			?><h5 class="sc_matches_item_player_name"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h5><?php
			// Club or Country
			$player1_meta = get_post_meta($meta['player1'], 'trx_addons_options', true);
			if (!empty($player1_meta['club'])) {
				?><div class="sc_matches_item_player_meta"><?php echo esc_html($player1_meta['club']); ?></div><?php
			}
		?></div><?php
			
		// Score
		?><div class="sc_matches_item_score"><?php
			if (!$is_match_page) {
				?><a href="<?php echo esc_url(get_permalink($match['id'])); ?>" class="sc_matches_item_link"><?php
			}
			if (!empty($meta['date_start'])) {
				?><span class="sc_matches_item_match_date"><?php echo date_i18n(get_option('date_format').(!empty($meta['time_start']) ? ' '.get_option('time_format') : ''), strtotime($meta['date_start'].(!empty($meta['time_start']) ? ' '.$meta['time_start'] : ''))); ?></span><?php
			}
			?><span class="sc_matches_item_score_value"><?php
				if ( empty($meta['score']) ) {
					esc_html_e('vs', 'trx_addons');
				} else {
					$parts = preg_split( "/[\\:\\-]/", $meta['score'] );
					if ( count( $parts ) == 2 ) {
						?><span class="sc_matches_item_score_value1"><?php echo esc_html( $parts[0] ); ?></span><span class="sc_matches_item_score_delimiter"></span><span class="sc_matches_item_score_value2"><?php echo esc_html( $parts[1] ); ?></span><?php
					} else {
						echo esc_html( $meta['score'] );
					}
				}
			?></span><?php
			if (!empty($meta['place'])) {
				?><span class="sc_matches_item_match_place"><?php echo esc_html($meta['place']); ?></span><?php
			}
			if (!empty($meta['review']) && !trx_addons_is_off($meta['review'])) {
				?><a class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_matches_item_review_link theme_button', 'sc_matches', $args)); ?>" href="<?php echo esc_url(get_permalink($meta['review'])); ?>"><span class="sc_matches_item_review_title"><?php esc_html_e('Match Review', 'trx_addons'); ?></span></a><?php
			}
			if (!$is_match_page) {
				?></a><?php
			}
		?></div><?php

		// Player 2
		?><div class="sc_matches_item_player sc_matches_item_player2"><?php
			$post = get_post($meta['player2']);
			setup_postdata($post);
			// Player's logo
			if (has_post_thumbnail()) {
				trx_addons_get_template_part('templates/tpl.featured.php',
											'trx_addons_args_featured',
											apply_filters('trx_addons_filter_args_featured', array(
																'class' => 'sc_matches_item_thumb',
																'hover' => '',
																'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'sport-player')
																), 'sport-player')
											);
			}
			// Name or Command
			?><h5 class="sc_matches_item_player_name"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h5><?php
			// Club or Country
			$player2_meta = get_post_meta($meta['player2'], 'trx_addons_options', true);
			if (!empty($player2_meta['club'])) {
				?><div class="sc_matches_item_player_meta"><?php echo esc_html($player2_meta['club']); ?></div><?php
			}
		?></div>
	</div>
	<?php
	wp_reset_postdata();
}
?>