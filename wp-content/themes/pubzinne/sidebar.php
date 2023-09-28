<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

if ( pubzinne_sidebar_present() ) {
	
	$pubzinne_sidebar_type = pubzinne_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $pubzinne_sidebar_type && ! pubzinne_is_layouts_available() ) {
		$pubzinne_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $pubzinne_sidebar_type ) {
		// Default sidebar with widgets
		$pubzinne_sidebar_name = pubzinne_get_theme_option( 'sidebar_widgets' );
		pubzinne_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $pubzinne_sidebar_name ) ) {
			dynamic_sidebar( $pubzinne_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$pubzinne_sidebar_id = pubzinne_get_custom_sidebar_id();
		do_action( 'pubzinne_action_show_layout', $pubzinne_sidebar_id );
	}
	$pubzinne_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $pubzinne_out ) ) {
		$pubzinne_sidebar_position    = pubzinne_get_theme_option( 'sidebar_position' );
		$pubzinne_sidebar_position_ss = pubzinne_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $pubzinne_sidebar_position );
			echo ' sidebar_' . esc_attr( $pubzinne_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $pubzinne_sidebar_type );

			if ( 'float' == $pubzinne_sidebar_position_ss ) {
				echo ' sidebar_float';
			}
			$pubzinne_sidebar_scheme = pubzinne_get_theme_option( 'sidebar_scheme' );
			if ( ! empty( $pubzinne_sidebar_scheme ) && ! pubzinne_is_inherit( $pubzinne_sidebar_scheme ) ) {
				echo ' scheme_' . esc_attr( $pubzinne_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="pubzinne_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'pubzinne_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $pubzinne_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$pubzinne_title = apply_filters( 'pubzinne_filter_sidebar_control_title', 'float' == $pubzinne_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'pubzinne' ) : '' );
				$pubzinne_text  = apply_filters( 'pubzinne_filter_sidebar_control_text', 'above' == $pubzinne_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'pubzinne' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $pubzinne_title ); ?>"><?php echo esc_html( $pubzinne_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'pubzinne_action_before_sidebar', 'sidebar' );
				pubzinne_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $pubzinne_out ) );
				do_action( 'pubzinne_action_after_sidebar', 'sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
			<?php

			do_action( 'pubzinne_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div><!-- /.sidebar -->
		<div class="clearfix"></div>
		<?php
	}
}
