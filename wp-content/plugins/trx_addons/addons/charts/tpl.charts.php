<?php
/**
 * The default layouts of the Charts
 *
 * @package ThemeREX Addons
 * @addon Charts
 * @since v2.8.0
 */

$args = get_query_var( 'trx_addons_args_sc_charts' );

$id = ! empty( $args['id'] ) ? $args['id'] : 'sc_charts_' . mt_rand();

$data = array(
	'type'         => $args['type'],
	'legend'       => $args['legend'],
	'from_zero'    => $args['from_zero'],
	'cutout'       => ! empty( $args['cutout'] ) ? min( 100, max( 0, (int) $args['cutout'] ) ) : 0,
	'hover_offset' => ! empty( $args['hover_offset'] ) ? min( 100, max( 0, (int) $args['hover_offset'] ) ) : 0,
);

$total = apply_filters( 'trx_addons_filter_charts_datasets_total', TRX_ADDONS_CHARTS_DATASETS_TOTAL );

for ( $i = 1; $i <= $total; $i++ ) {
	if ( $i == 1 || (int)$args["dataset{$i}_enable"] > 0 ) {
		$data["dataset{$i}"]              = $args["dataset{$i}"];
		$data["dataset{$i}_enable"]       = $i == 1 || (int)$args["dataset{$i}_enable"] > 0;
		$data["dataset{$i}_title"]        = $args["dataset{$i}_title"];
		$data["dataset{$i}_fill"]         = $args["dataset{$i}_fill"];
		$data["dataset{$i}_tension"]      = ! empty( $args["dataset{$i}_tension"] ) ? min( 1, max( 0, $args["dataset{$i}_tension"] ) ) : 0;
		$data["dataset{$i}_point_style"]  = $args["dataset{$i}_point_style"];
		$data["dataset{$i}_point_size"]   = ! empty( $args["dataset{$i}_point_size"] ) ? min( 20, max( 0, (int)$args["dataset{$i}_point_size"] ) ) : 0;
		$data["dataset{$i}_bg_color"]     = ! empty( $args["dataset{$i}_bg_color"] ) ? $args["dataset{$i}_bg_color"] : apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758');
		$data["dataset{$i}_border_color"] = ! empty( $args["dataset{$i}_border_color"] ) ? $args["dataset{$i}_border_color"] : apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758');
		$data["dataset{$i}_border_width"] = ! empty( $args["dataset{$i}_border_width"] ) ? min( 10, max( 0, (int) $args["dataset{$i}_border_width"] ) ) : 0;
		$data["dataset{$i}_border_join"]  = $args["dataset{$i}_border_join"];
	}
}

?><div id="<?php echo esc_attr( $id ); ?>"
		class="sc_charts sc_charts_type_<?php echo esc_attr( $args['type'] );
				echo ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '';
		?>"
		<?php
		echo ! empty( $args['css'] ) ? ' style="' . esc_attr( $args['css'] ) . '"' : '';
		trx_addons_sc_show_attributes('sc_charts', $args, 'sc_wrapper');
?>><?php

		trx_addons_sc_show_titles('sc_charts', $args);

		?><div class="sc_item_content sc_charts_content">
			<canvas id="<?php echo esc_attr( $id ); ?>_canvas"
					class="sc_charts_canvas"
					data-chart-data="<?php echo esc_attr( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) ); ?>"></canvas>
		</div><?php

		trx_addons_sc_show_links('sc_charts', $args);

?></div><?php
