<?php
/**
 * The style "default" of the Audio Effects
 *
 * @package ThemeREX Addons
 * @addon audio-effects
 * @since v1.0
 */

$args = get_query_var('trx_addons_args_sc_audio_effects');
?>
<a<?php
	if ( ! empty( $args['id'] ) ) {
		?> id="sc_audio_<?php echo esc_attr($args['id']); ?>"<?php
	}
	?>
	href="#"
	title="<?php echo esc_attr_e('Enable/Disable sounds on this site', 'trx_addons'); ?>"
	class="sc_audio_effects sc_audio_effects_<?php
		echo esc_attr($args['type']) . ( ! empty($args['class']) ? ' ' . esc_attr($args['class']) : '' );
	?>"
	<?php
	if ( ! empty( $args['css'] ) ) {
		?> style="<?php echo esc_attr( $args['css'] ); ?>"<?php
	}
	trx_addons_sc_show_attributes('sc_audio_effects', $args, 'sc_item_wrapper');
	?>
><span></span><span></span><span></span><span></span></a>
