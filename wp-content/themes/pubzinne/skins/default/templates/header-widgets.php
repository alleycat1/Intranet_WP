<?php
/**
 * The template to display the widgets area in the header
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Header sidebar
$pubzinne_header_name    = pubzinne_get_theme_option( 'header_widgets' );
$pubzinne_header_present = ! pubzinne_is_off( $pubzinne_header_name ) && is_active_sidebar( $pubzinne_header_name );
if ( $pubzinne_header_present ) {
	pubzinne_storage_set( 'current_sidebar', 'header' );
	$pubzinne_header_wide = pubzinne_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $pubzinne_header_name ) ) {
		dynamic_sidebar( $pubzinne_header_name );
	}
	$pubzinne_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $pubzinne_widgets_output ) ) {
		$pubzinne_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $pubzinne_widgets_output );
		$pubzinne_need_columns   = strpos( $pubzinne_widgets_output, 'columns_wrap' ) === false;
		if ( $pubzinne_need_columns ) {
			$pubzinne_columns = max( 0, (int) pubzinne_get_theme_option( 'header_columns' ) );
			if ( 0 == $pubzinne_columns ) {
				$pubzinne_columns = min( 6, max( 1, pubzinne_tags_count( $pubzinne_widgets_output, 'aside' ) ) );
			}
			if ( $pubzinne_columns > 1 ) {
				$pubzinne_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $pubzinne_columns ) . ' widget', $pubzinne_widgets_output );
			} else {
				$pubzinne_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $pubzinne_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'pubzinne_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $pubzinne_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $pubzinne_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'pubzinne_action_before_sidebar', 'header' );
				pubzinne_show_layout( $pubzinne_widgets_output );
				do_action( 'pubzinne_action_after_sidebar', 'header' );
				if ( $pubzinne_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $pubzinne_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'pubzinne_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
