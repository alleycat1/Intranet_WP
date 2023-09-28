<?php
/**
 * Template to display a shortcode's placeholder in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @package ThemeREX Addons
 * @since v2.9.0
 */
$args = get_query_var( 'trx_addons_args_sc_placeholder' );
?><#
var sc = '<?php echo esc_html( $args['sc'] ); ?>';
var title_field = '<?php echo esc_html( $args['title_field'] ); ?>';
var title = ( sc ? trx_addons_proper( sc.replace('trx_sc_', '').replace('trx_widget_', '').replace('_', ' ') ) + ': ' : '' )
            + ( title_field && settings[title_field] ? trx_addons_proper( settings[title_field] ) : '' );
#>
<div class="trx_addons_pb_preview_placeholder sc_placeholder" title="{{ title }}"><p>{{ title }}</p></div>