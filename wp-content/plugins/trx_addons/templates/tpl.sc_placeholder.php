<?php
/**
 * Template to display a shortcode's placeholder in the Gutenberg preview area
 *
 * @package ThemeREX Addons
 * @since v2.17.3
 */
$args = get_query_var( 'trx_addons_args_sc_placeholder' );

$title = ( ! empty( $args['sc'] )
            ? ucfirst( str_replace( array( 'trx_sc_', 'trx_widget_', '_' ), array( '', '', ' '), $args['sc'] ) )
                . ( ! empty( $args['title'] ) ? ': ' : '' )
            : ''
            )
        . ( ! empty( $args['title'] )
            ? $args['title'] 
            : ''
            );

?><div class="trx_addons_pb_preview_placeholder sc_placeholder" title="<?php echo esc_html( $title ); ?>"><p><?php echo esc_html( $title ); ?></p></div>