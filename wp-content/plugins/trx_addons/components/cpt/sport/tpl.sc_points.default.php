<?php
/**
 * The style "default" of the Points table
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

$args = get_query_var('trx_addons_args_sc_points');

if (!empty($args['competition'])) {
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
			class="sc_points sc_points_<?php
				echo esc_attr($args['type']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
	?>"><?php
			
		trx_addons_sc_show_titles('sc_points', $args);

		// Get list of the players sorted by descending points
		$players = trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_CPT_PLAYERS_PT,
														'post_parent' => $args['competition'],
														'meta_key' => 'trx_addons_player_points',
														'orderby' => 'meta_value_num',
														'order' => 'DESC'
														)
												);
		if (isset($players['none'])) unset($players['none']);

		?><table width="100%" class="sc_points_table sc_points_content sc_item_content">
			<tr>
				<th class="sc_points_table_rank"><?php esc_html_e('Rank', 'trx_addons'); ?></th>
				<th class="sc_points_table_name"><?php esc_html_e('Player/Command (Club/Country)', 'trx_addons'); ?></th>
				<th class="sc_points_table_games"><?php esc_html_e('Games', 'trx_addons'); ?></th>
				<th class="sc_points_table_points"><?php esc_html_e('Points', 'trx_addons'); ?></th>
			</tr>
			<?php
			$rank = 1;
			foreach ($players as $id=>$name) {
				$meta = (array)get_post_meta($id, 'trx_addons_options', true);
				$points = get_post_meta($id, 'trx_addons_player_points', true);
				$points_data = get_post_meta($id, 'trx_addons_player_points_data', true);
				$link = get_permalink($id);
				$logo = !empty($args['logo']) 
							? trx_addons_get_attachment_url(
															get_post_thumbnail_id($id),
															apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'sport-player-small')
															) 
							: '';
				?><tr<?php
					if ($rank <= $args['accented_top']) echo ' class="sc_points_table_accented sc_points_table_accented_top"';
					else if ($rank > count($players) - $args['accented_bottom']) echo ' class="sc_points_table_accented sc_points_table_accented_bottom"';
				?>>
					<td class="sc_points_table_rank"><a href="<?php echo esc_url($link); ?>" class="sport_page_list_link"><?php
						echo intval($rank++); ?>.
					</a></td>
					<td class="sc_points_table_name"><a href="<?php echo esc_url($link); ?>" class="sport_page_list_link"><?php
						if (!empty($logo)) {
							$attr = trx_addons_getimagesize($logo);
							?><span class="sc_points_table_logo"><img src="<?php echo esc_url($logo); ?>" alt="<?php esc_attr_e("Player's logo", 'trx_addons'); ?>" <?php if (!empty($attr[3])) trx_addons_show_layout($attr[3]);?>></span><?php
						}
						?><span class="sc_points_table_player">
							<span class="sc_points_table_player_name"><?php echo esc_html($name); ?></span><?php
							if (!empty($meta['club'])) {
								?><span class="sc_points_table_player_club"><?php echo ' ('. $meta['club'].')'; ?></span><?php
							}
						?></span>
					</a></td>
					<td class="sc_points_table_games"><a href="<?php echo esc_url($link); ?>" class="sport_page_list_link"><?php
						echo esc_html(count($points_data));
					?></a></td>
					<td class="sc_points_table_points"><a href="<?php echo esc_url($link); ?>" class="sport_page_list_link"><?php
						echo esc_html($points);
					?></a></td>
				</tr><?php
			}
		?></table><?php
		
		trx_addons_sc_show_links('sc_matches', $args);

	?></div><?php
}
