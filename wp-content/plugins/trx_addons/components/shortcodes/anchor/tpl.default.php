<?php
/**
 * The style "default" of the Anchor
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_anchor');
$atts = array(
	'class'			=> "sc_anchor sc_anchor_{$args['type']}",
	'data-vc-icon'	=> $args['icon'],
	'data-url'		=> $args['url']
);
?><a<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="sc_anchor_<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
	title="<?php echo esc_attr($args['title']); ?>" <?php
	foreach ($atts as $k => $v) {
		echo " {$k}=\"{$v}\"";
	}
	trx_addons_sc_show_attributes('sc_anchor', $args, 'sc_item_wrapper');
?>></a>