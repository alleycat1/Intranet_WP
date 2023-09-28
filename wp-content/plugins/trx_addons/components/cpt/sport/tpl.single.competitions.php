<?php
/**
 * The template to display the competition's single post
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

get_header();

while ( have_posts() ) { the_post();
	
	$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
	
	do_action('trx_addons_action_before_article', 'competitions.single');
	?>
    <article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'sport_single competition_single itemscope' ); trx_addons_seo_snippets('', 'Article'); ?>>

		<?php do_action('trx_addons_action_article_start', 'competitions.single'); ?>
		
		<section class="sport_page_header">	

			<?php
			// Image
			if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
				?><div class="sport_page_featured">
					<?php
					the_post_thumbnail(
										apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'sport-competition'),
										trx_addons_seo_image_params(array(
																		'alt' => get_the_title()
																		))
										);
					?>
				</div>
				<?php
			}
			
			// Title
			if (!trx_addons_sc_layouts_showed('title')) {
				?><h2 class="sport_page_title"><?php the_title(); ?></h2><?php
			}

			// Meta
			if ( !trx_addons_sc_layouts_showed('postmeta') ) {
				?>
				<div class="sport_page_meta">
					<span class="sport_page_meta_item sport_page_meta_date"><?php
						$dt = $meta['date_start'];
						echo sprintf($dt < date('Y-m-d') 
										? esc_html__('Started on %s', 'trx_addons') 
										: esc_html__('Starting %s', 'trx_addons'),
										'<span class="sport_page_meta_item_date">' . date_i18n(get_option('date_format'), strtotime($dt)) . '</span>');
					?></span>
				</div>
				<?php
			}
			?>

		</section>
		<?php

		// Post content
		if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
			?><div class="sport_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				the_content( );
			?></div><?php
		}
		
		?><div class="sport_page_lists competition_page_lists">
			<?php
			// List of the rounds
			$rounds = trx_addons_get_list_posts(false, array(
															'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
															'post_parent' => get_the_ID(),
															'meta_key' => 'trx_addons_round_date',
															'orderby' => 'meta_value',
															'order' => 'ASC'
															)
													);
			if (isset($rounds['none'])) unset($rounds['none']);
			?><div class="sport_page_list_wrap sport_page_rounds">
				<h3 class="sport_page_list_header"><?php esc_html_e("List of the competition's rounds", 'trx_addons'); ?></h3>
				<ul class="sport_page_list">
					<li><?php
						?><span class="sport_page_list_title sport_page_match_time"><?php esc_html_e('Time', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_match_name"><?php esc_html_e('Match', 'trx_addons'); ?></span>
					</li>
					<?php
					$last_date = '';
					foreach ($rounds as $id=>$name) {
						$matches = trx_addons_get_list_posts(false, array(
																		'post_type' => TRX_ADDONS_CPT_MATCHES_PT,
																		'post_parent' => $id,
																		'meta_key' => 'trx_addons_match_date',
																		'orderby' => 'meta_value',
																		'order' => 'ASC'
																		)
																);
						if (isset($matches['none'])) unset($matches['none']);
						foreach ($matches as $id_match=>$name_match) {
							$meta = (array)get_post_meta($id_match, 'trx_addons_options', true);
							if ($last_date != $meta['date_start']) {
								$last_date = $meta['date_start'];
								// Round name and date
								?><li><a href="<?php echo esc_url(get_permalink($id)); ?>" class="sport_page_list_link"><?php
									?><span class="sport_page_round_time" data-round=".sport_page_list_round_<?php echo esc_html($id.'_'.$last_date); ?>"></span><?php
									?><span class="sport_page_round_name"><?php echo esc_html($name).'. '.date_i18n(get_option('date_format'), strtotime($meta['date_start'])); ?></span><?php
								?></li></a><?php
							}
							?><li class="sport_page_list_round_<?php echo esc_html($id.'_'.$last_date); ?>"><a href="<?php echo esc_url(get_permalink($id_match)); ?>" class="sport_page_list_link"><?php
								// Start time
								?><span class="sport_page_match_time"><?php echo esc_html(!empty($meta['time_start']) ? date_i18n(get_option('time_format'), strtotime($meta['time_start'])) : '--:--'); ?></span><?php
								// If competition's type is 'pair'
								if (isset($meta['player1'])) {
									$player1 = !empty($meta['player1']) ? get_the_title($meta['player1']) : '';
									$player2 = !empty($meta['player2']) ? get_the_title($meta['player2']) : '';
									// First player's name
									?><span class="sport_page_match_name1"><?php echo esc_html($player1); ?></span><?php
									// Score
									?><span class="sport_page_match_score"><?php echo esc_html(!empty($meta['score']) ? $meta['score'] : '--:--'); ?></span><?php
									// Second player's name
									?><span class="sport_page_match_name2"><?php echo esc_html($player2); ?></span><?php

								// If competition's type is 'pair'
								} else {
									// Name of the match
									?><span class="sport_page_match_name"><?php echo esc_html($name_match); ?></span><?php
								}
							?></li></a><?php
						}
					}
					?>
				</ul>
			</div>
			<?php
			// List of the players
			$players = trx_addons_get_list_posts(false, array(
															'post_type' => TRX_ADDONS_CPT_PLAYERS_PT,
															'post_parent' => get_the_ID(),
															'meta_key' => 'trx_addons_player_points',
															'orderby' => 'meta_value',
															'order' => 'DESC'
															)
													);
			if (isset($players['none'])) unset($players['none']);
			?><div class="sport_page_list_wrap sport_page_players">
				<h3 class="sport_page_list_header"><?php esc_html_e("Table of the players points", 'trx_addons'); ?></h3>
				<ul class="sport_page_list">
					<li><?php
						?><span class="sport_page_list_title sport_page_player_rank"><?php esc_html_e('Rank', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_player_name"><?php esc_html_e('Player/Command (Club/Country)', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_player_games"><?php esc_html_e('Games', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_player_points"><?php esc_html_e('Points', 'trx_addons'); ?></span>
					</li>
					<?php
					$rank = 1;
					foreach ($players as $id=>$name) {
						$meta = (array)get_post_meta($id, 'trx_addons_options', true);
						$points = get_post_meta($id, 'trx_addons_player_points', true);
						$points_data = get_post_meta($id, 'trx_addons_player_points_data', true);
						?><li><a href="<?php echo esc_url(get_permalink($id)); ?>" class="sport_page_list_link"><?php
							// Player's rank
							?><span class="sport_page_player_rank"><?php echo intval($rank++); ?>.</span><?php
							// Name
							?><span class="sport_page_player_name"><?php echo esc_html($name.(!empty($meta['club']) ? ' ('. $meta['club'].')' : '')); ?></span><?php
							// Games
							?><span class="sport_page_player_games"><?php echo esc_html(count($points_data)); ?></span><?php
							// Points
							?><span class="sport_page_player_points"><?php echo esc_html($points); ?></span>
						</li></a><?php
					}
					?>
				</ul>
			</div>
		</div>

		<?php do_action('trx_addons_action_article_end', 'competitions.single'); ?>

	</article><?php

	do_action('trx_addons_action_after_article', 'competitions.single');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
