<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

// Footer sidebar
$pubzinne_footer_name    = pubzinne_get_theme_option( 'footer_widgets' );
$pubzinne_footer_present = ! pubzinne_is_off( $pubzinne_footer_name ) && is_active_sidebar( $pubzinne_footer_name );
if ( $pubzinne_footer_present ) {
	pubzinne_storage_set( 'current_sidebar', 'footer' );
	$pubzinne_footer_wide = pubzinne_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $pubzinne_footer_name ) ) {
		dynamic_sidebar( $pubzinne_footer_name );
	}
	$pubzinne_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $pubzinne_out ) ) {
		$pubzinne_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $pubzinne_out );
		$pubzinne_need_columns = true;   //or check: strpos($pubzinne_out, 'columns_wrap')===false;
		if ( $pubzinne_need_columns ) {
			$pubzinne_columns = max( 0, (int) pubzinne_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $pubzinne_columns ) {
				$pubzinne_columns = min( 4, max( 1, pubzinne_tags_count( $pubzinne_out, 'aside' ) ) );
			}
			if ( $pubzinne_columns > 1 ) {
				$pubzinne_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $pubzinne_columns ) . ' widget', $pubzinne_out );
			} else {
				$pubzinne_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $pubzinne_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'pubzinne_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $pubzinne_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $pubzinne_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'pubzinne_action_before_sidebar', 'footer' );
				pubzinne_show_layout( $pubzinne_out );
				do_action( 'pubzinne_action_after_sidebar', 'footer' );
				if ( $pubzinne_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $pubzinne_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'pubzinne_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
