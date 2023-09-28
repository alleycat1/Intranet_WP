<?php
/**
 * The template to display the player's single post
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

get_header();

while ( have_posts() ) { the_post();
	
	$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
	
	do_action('trx_addons_action_before_article', 'players.single');
	?>
    <article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'sport_single player_single itemscope' ); trx_addons_seo_snippets('', 'Article'); ?>>

		<?php do_action('trx_addons_action_article_start', 'players.single'); ?>
		
		<section class="sport_page_header">	

			<?php
			// Image
			if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
				?><div class="sport_page_featured">
					<?php
					the_post_thumbnail(
										apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'sport-players'),
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
				?><div class="sport_page_meta"><?php
					// Birthday
					if (!empty($meta['birthday'])) {
						?><div class="sport_page_meta_item sport_page_meta_date">
							<span class="sport_page_meta_item_label"><?php esc_html_e('Birthday:', 'trx_addons'); ?></span>
							<span class="sport_page_meta_item_value"><?php
								echo date_i18n(get_option('date_format'), strtotime($meta['birthday']));
							?></span>
						</div><?php
					}
					// Club/Country
					if (!empty($meta['club'])) {
						?><div class="sport_page_meta_item sport_page_meta_club">
							<span class="sport_page_meta_item_label"><?php esc_html_e('Club/Country:', 'trx_addons'); ?></span>
							<span class="sport_page_meta_item_value"><?php
								echo esc_html($meta['club']);
							?></span>
						</div><?php
					}
					// Brief info
					if (!empty($meta['info'])) {
						?><div class="sport_page_meta_item sport_page_meta_info">
							<span class="sport_page_meta_item_label"><?php esc_html_e('Brief info:', 'trx_addons'); ?></span>
							<span class="sport_page_meta_item_value"><?php
								echo nl2br(esc_html($meta['info']));
							?></span>
						</div><?php
					}
				?></div><?php
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
			global $post;
			// List of the matches with this player
			$rounds = trx_addons_get_list_posts(false, array(
															'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
															'post_parent' => $post->post_parent,
															'meta_key' => 'trx_addons_round_date',
															'orderby' => 'meta_value',
															'order' => 'ASC'
															)
													);
			if (isset($rounds['none'])) unset($rounds['none']);
			$matches = trx_addons_get_list_posts(false, array(
															'post_type' => TRX_ADDONS_CPT_MATCHES_PT,
															'post_parent' => array_keys($rounds),
															'meta_key' => 'trx_addons_match_date',
															'orderby' => 'meta_value',
															'order' => 'ASC'
															)
													);
			if (isset($matches['none'])) unset($matches['none']);
			?><div class="sport_page_list_wrap sport_page_rounds">
				<h3 class="sport_page_list_header"><?php esc_html_e("List of matches with this player", 'trx_addons'); ?></h3>
				<ul class="sport_page_list">
					<li><?php
						?><span class="sport_page_list_title sport_page_match_round"><?php esc_html_e('Round', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_match_date"><?php esc_html_e('Date', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_match_time"><?php esc_html_e('Time', 'trx_addons'); ?></span><?php
						?><span class="sport_page_list_title sport_page_match_name"><?php esc_html_e('Match', 'trx_addons'); ?></span>
					</li>
					<?php
					$id_player = get_the_ID();
					foreach ($matches as $id_match=>$name_match) {
						$meta = (array)get_post_meta($id_match, 'trx_addons_options', true);
						if ( !((isset($meta['players']) && isset($meta['players'][$id_player]) && $meta['players'][$id_player]==1)
							|| $meta['player1']==$id_player || $meta['player2']==$id_player)) continue;
						$post = get_post($id_match);
						setup_postdata($post);
						$link = get_permalink($id_match);
						?><li><?php
								// Round
								?><span class="sport_page_match_round"><a href="<?php echo esc_url(get_permalink($post->post_parent)); ?>"><?php 
									echo esc_html(get_the_title($post->post_parent)); 
								?></a></span><?php
								// Start date
								?><span class="sport_page_match_date"><a href="<?php echo esc_url($link); ?>"><?php 
									echo esc_html(!empty($meta['date_start']) 
													? date_i18n(get_option('date_format'), strtotime($meta['date_start'])) 
													: ''); 
								?></a></span><?php
								// Start time
								?><span class="sport_page_match_time"><a href="<?php echo esc_url($link); ?>"><?php 
									echo esc_html(!empty($meta['time_start']) 
													? date_i18n(get_option('time_format'), strtotime($meta['time_start'])) 
													: '--:--'); 
								?></a></span><?php
								// If competition's type is 'pair'
								if (isset($meta['player1'])) {
									$player1 = !empty($meta['player1']) ? get_the_title($meta['player1']) : '';
									$player2 = !empty($meta['player2']) ? get_the_title($meta['player2']) : '';
									// First player's name
									?><span class="sport_page_match_name1"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($player1); ?></a></span><?php
									// Score
									?><span class="sport_page_match_score"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html(!empty($meta['score']) ? $meta['score'] : '--:--'); ?></a></span><?php
									// Second player's name
									?><span class="sport_page_match_name2"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($player2); ?></a></span><?php

								// If competition's type is 'pair'
								} else {
									// Name of the match
									?><span class="sport_page_match_name"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></span><?php
								}
						?></li><?php
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		</div>

		<?php do_action('trx_addons_action_article_end', 'players.single'); ?>

	</article><?php

	do_action('trx_addons_action_after_article', 'players.single');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
