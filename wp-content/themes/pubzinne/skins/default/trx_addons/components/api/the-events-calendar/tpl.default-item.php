<?php
/**
 * The style "default" of the Events
 *
 * @package ThemeREX Addons
 * @since v1.6.54.3
 */

$args = get_query_var('trx_addons_args_sc_events');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$dt = tribe_get_start_date(null, true, 'Y-m-d');
$tm = tribe_get_start_time(null, 'H:i');
$dt2 = tribe_get_end_date(null, true, 'Y-m-d');
$tm2 = tribe_get_end_time(null, 'H:i');

$post_link = get_permalink();

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ((int) $args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div class="sc_events_item sc_item_container post_container">
	<div class="sc_events_item_info">
		<div class="sc_events_item_header">
            <div class="sc_events_item_wraper">
                <h4 class="sc_events_item_title"><a href="<?php echo esc_url($post_link); ?>"><?php the_title(); ?></a></h4>
                <div class="sc_events_item_meta">
                    <span class="sc_events_item_meta_item sc_events_item_meta_date"><?php
                        if ($dt == $dt2) {
                            if ( $tm == null ) {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)), '<span class="sc_events_item_meta_date_start">', '</span>');
                            } else {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)) . ' ' . $tm, '<span class="sc_events_item_meta_time_start">', '</span>');
                            }
                        } else {
                            if ( $tm == null ) {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)), '<span class="sc_events_item_meta_date_start">', '</span>');
                                ?><span class="sc_events_item_meta_date_separator">-</span><?php
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt2)), '<span class="sc_events_item_meta_date_end">', '</span>');
                            } else {
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt)) . ' ' . $tm, '<span class="sc_events_item_meta_date_start">', '</span>');
                                ?><span class="sc_events_item_meta_date_separator">-</span><?php
                                trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($dt2)) . ' ' . $tm2, '<span class="sc_events_item_meta_date_end">', '</span>');
                            }
                        }
                    ?></span>
                </div>
            </div>
			<?php if (($excerpt = get_the_excerpt()) != '') { ?>
				<div class="sc_events_item_text"><?php echo esc_html($excerpt); ?></div>
			<?php } ?>
		</div>
		<div class="sc_events_item_price"><?php echo tribe_get_formatted_cost(); ?></div>
	</div>
	<?php if (!empty($args['more_text'])) { ?>
		<div class="sc_events_item_button sc_item_button">
			<a href="<?php echo esc_url($post_link); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_events', $args)); ?>">
				<?php echo esc_html($args['more_text']); ?>
			</a>
		</div>
	<?php } ?>
</div><?php
if (!empty($args['slider']) || (int) $args['columns'] > 1) {
	?></div><?php
}
