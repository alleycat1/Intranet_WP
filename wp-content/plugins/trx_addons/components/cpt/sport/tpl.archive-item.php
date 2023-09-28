<?php
/**
 * Template of one item from any Sport type archive
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

$args = get_query_var('trx_addons_args_sc_sport');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);

if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_sport_item sc_item_container post_container sc_sport_archive_item<?php if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item'; ?><?php
	echo isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content';
?>"<?php trx_addons_add_blog_animation('competitions', $args); ?>>
	<?php
	// Featured image
	if ( has_post_thumbnail() ) {
		trx_addons_get_template_part('templates/tpl.featured.php',
										'trx_addons_args_featured',
										apply_filters('trx_addons_filter_args_featured', array(
															'class' => 'sc_sport_item_thumb'.(!empty($args['slug']) ? ' sc_'.esc_attr($args['slug']).'_item_thumb' : ''),
															'hover' => '',
															'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size($args['columns'] > 2 ? 'masonry' : 'masonry-big'), 'sport-default')
															),
														'sport-default'
													)
									);
	}
	?>	
	<div class="sc_sport_item_info<?php if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item_info'; ?>">
		<div class="sc_sport_item_header<?php if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item_header'; ?>">
			<h6 class="sc_sport_item_title<?php
				if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item_title';
				if (!empty($meta['score'])) echo ' with_score';
				?>"><a href="<?php echo esc_url(get_permalink()); ?>">
					<span class="sc_sport_item_label'.(!empty($args['slug']) ? ' sc_'.esc_attr($args['slug']).'_item_label' : '').'"><?php
					the_title();
					?></span><?php
					if (!empty($meta['score'])) {
						echo ' <span class="sc_sport_item_score'.(!empty($args['slug']) ? ' sc_'.esc_attr($args['slug']).'_item_score' : '').'">'.trim($meta['score']) . '</span>';
					}
			?></a></h6><?php
			if ($args['slug']=='competitions') {
				?><div class="sc_sport_item_subtitle sc_competitions_item_subtitle"><?php
					trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY));
					?><span class="sc_sport_item_date"><?php
						echo ', '
							. date_i18n(get_option('date_format'), strtotime($meta['date_start']))
							. ' - '
							. date_i18n(get_option('date_format'), strtotime($meta['date_end']));
					?></span><?php
				?></div><?php
			} else if (in_array($args['slug'], array('rounds', 'matches'))) {
				if (!empty($meta['date_start'])) {
					?><div class="sc_sport_item_subtitle sc_rounds_item_subtitle"><?php
						echo date_i18n(get_option('date_format').(!empty($meta['time_start']) ? ' '.get_option('time_format') : ''),
								  strtotime($meta['date_start'].(!empty($meta['time_start']) ? ' '.$meta['time_start'] : '')));
					?></div><?php
				}
			}
			?>
		</div>
		<?php
		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
			?><div class="sc_sport_item_content<?php if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item_content'; ?>"><?php
				the_excerpt();
			?></div><?php
			
			// Uncomment (remove) next line, if you want add buttons with links to the rounds and players posts, like a blog stream
			if (false) {
				?>
				<div class="sc_item_button sc_sport_item_button<?php if (!empty($args['slug'])) echo ' sc_'.esc_attr($args['slug']).'_item_button'; ?>"><?php
					// More
					if (false) {
						?><a href="<?php echo esc_url(get_permalink()); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_sport', $args)); ?>"><?php esc_html_e('More', 'trx_addons'); ?></a><?php
					}
					if ($args['slug']=='competitions') {
						// Rounds
						?><a href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_ROUNDS_PT), array('competition'=>get_the_ID()))); ?>" class="sc_button sc_button_simple"><?php esc_html_e('Rounds', 'trx_addons'); ?></a><?php
						// Players
						?><a href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_PLAYERS_PT), array('competition'=>get_the_ID()))); ?>" class="sc_button sc_button_simple"><?php esc_html_e('Players', 'trx_addons'); ?></a><?php
					} else if ($args['slug']=='rounds') {
						// Matches
						?><a href="<?php echo esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_MATCHES_PT), array('round'=>get_the_ID()))); ?>" class="sc_button sc_button_simple"><?php esc_html_e('Matches', 'trx_addons'); ?></a><?php
					}
					?>	
				</div>
				<?php
			}
		}
		?>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
?>