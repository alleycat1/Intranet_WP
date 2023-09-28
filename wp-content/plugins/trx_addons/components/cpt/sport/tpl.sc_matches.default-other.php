<?php
/**
 * The style "default" of the shortcode Matches (section "Other matches")
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

$match = get_query_var('trx_addons_args_sc_matches_item');

$meta = $match['meta'];

?><div data-post-id="<?php echo esc_attr($match['id']); ?>" class="sc_matches_item sc_item_container post_container"><a href="<?php echo esc_url(get_permalink($match['id'])); ?>" class="sc_matches_item_link"><?php
	// Match date
	?><span class="sc_matches_item_date"><span class="sc_matches_item_date_wrap">
		<span class="sc_matches_item_day"><?php echo date_i18n("M, d", strtotime($meta['date_start'])); ?></span>
		<span class="sc_matches_item_time"><?php echo date_i18n("g:i A", strtotime($meta['time_start'])); ?></span>
	</span></span><?php
	// Match info
	?><span class="sc_matches_item_info"><?php
		// If competition's type is 'pair'
		if (isset($meta['player1'])) {
			$player1 = !empty($meta['player1']) ? get_the_title($meta['player1']) : '';
			$player1_meta = get_post_meta( $meta['player1'], 'trx_addons_options', true );
			$player1_logo = trx_addons_get_attachment_url(
														get_post_thumbnail_id( $meta['player1'] ),
														apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'sport-player-small')
														);
			if (!empty($player1_logo)) $player1_attr = trx_addons_getimagesize($player1_logo);
			$player2 = !empty($meta['player2']) ? get_the_title($meta['player2']) : '';
			$player2_meta = get_post_meta( $meta['player2'], 'trx_addons_options', true );
			$player2_logo = trx_addons_get_attachment_url(
														get_post_thumbnail_id( $meta['player2'] ),
														apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'sport-player-small')
														);
			if (!empty($player2_logo)) $player2_attr = trx_addons_getimagesize($player2_logo);
			// First player
			?><span class="sc_matches_item_logo1"><img src="<?php echo esc_url($player1_logo); ?>" alt="<?php esc_attr_e("Player's logo", 'trx_addons'); ?>" <?php if (!empty($player1_attr[3])) trx_addons_show_layout($player1_attr[3]);?>></span><?php
			?><span class="sc_matches_item_name1">
				<span class="sc_matches_item_title"><?php echo esc_html($player1); ?></span><?php
				if (!empty($player1_meta['club'])) {
					?><span class="sc_matches_item_club"><?php echo esc_html($player1_meta['club']); ?></span><?php
				}
			?></span><?php
			// Score
			?><span class="sc_matches_item_score"><?php echo !empty($meta['score']) ? esc_html($meta['score']) : esc_html__('vs', 'trx_addons'); ?></span><?php
			// Second player
			?><span class="sc_matches_item_name2">
				<span class="sc_matches_item_title"><?php echo esc_html($player2); ?></span><?php
				if (!empty($player2_meta['club'])) {
					?><span class="sc_matches_item_club"><?php echo esc_html($player2_meta['club']); ?></span><?php
				}
			?></span><?php
			?><span class="sc_matches_item_logo2"><img src="<?php echo esc_url($player2_logo); ?>" alt="<?php esc_attr_e("Player's logo", 'trx_addons'); ?>" <?php if (!empty($player2_attr[3])) trx_addons_show_layout($player2_attr[3]);?>></span><?php
	
		// If competition's type is 'pair'
		} else {
			// Name of the match
			?><span class="sc_matches_item_name"><?php echo esc_html($match['title']); ?></span><?php
		}
	?></span><?php
?></a></div>