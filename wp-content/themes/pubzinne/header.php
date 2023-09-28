<?php
/**
 * The Header: Logo and main menu
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js
									<?php
										// Class scheme_xxx need in the <html> as context for the <body>!
										echo ' scheme_' . esc_attr( pubzinne_get_theme_option( 'color_scheme' ) );
									?>
										">
<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'pubzinne_action_before_body' );
	?>

	<div class="body_wrap">

		<div class="page_wrap">
			
			<?php
			$pubzinne_full_post_loading = ( is_singular( 'post' ) || is_singular( 'attachment' ) ) && pubzinne_get_value_gp( 'action' ) == 'full_post_loading';
			$pubzinne_prev_post_loading = ( is_singular( 'post' ) || is_singular( 'attachment' ) ) && pubzinne_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $pubzinne_full_post_loading && ! $pubzinne_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="pubzinne_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to content", 'pubzinne' ); ?></a>
				<?php if ( pubzinne_sidebar_present() ) { ?>
				<a class="pubzinne_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to sidebar", 'pubzinne' ); ?></a>
				<?php } ?>
				<a class="pubzinne_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to footer", 'pubzinne' ); ?></a>
				
				<?php
				do_action( 'pubzinne_action_before_header' );

				// Header
				$pubzinne_header_type = pubzinne_get_theme_option( 'header_type' );
				if ( 'custom' == $pubzinne_header_type && ! pubzinne_is_layouts_available() ) {
					$pubzinne_header_type = 'default';
				}
				get_template_part( apply_filters( 'pubzinne_filter_get_template_part', "templates/header-{$pubzinne_header_type}" ) );

				// Side menu
				if ( in_array( pubzinne_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'pubzinne_action_after_header' );

			}
			?>

			<div class="page_content_wrap">
				<?php
				do_action( 'pubzinne_action_page_content_wrap', $pubzinne_full_post_loading || $pubzinne_prev_post_loading );

				// Single posts banner
				if ( is_singular( 'post' ) || is_singular( 'attachment' ) ) {
					if ( $pubzinne_prev_post_loading ) {
						if ( pubzinne_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'pubzinne_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$pubzinne_path = apply_filters( 'pubzinne_filter_get_template_part', 'templates/single-styles/' . pubzinne_get_theme_option( 'single_style' ) );
					if ( pubzinne_get_file_dir( $pubzinne_path . '.php' ) != '' ) {
						get_template_part( $pubzinne_path );
					}
				}

				// Widgets area above page content
				$pubzinne_body_style   = pubzinne_get_theme_option( 'body_style' );
				$pubzinne_widgets_name = pubzinne_get_theme_option( 'widgets_above_page' );
				$pubzinne_show_widgets = ! pubzinne_is_off( $pubzinne_widgets_name ) && is_active_sidebar( $pubzinne_widgets_name );
				if ( $pubzinne_show_widgets ) {
					if ( 'fullscreen' != $pubzinne_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					pubzinne_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $pubzinne_body_style ) {
						?>
						</div><!-- </.content_wrap> -->
						<?php
					}
				}

				// Content area
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $pubzinne_body_style ? '_fullscreen' : ''; ?>">

					<div class="content">
						<?php
						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="pubzinne_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( is_singular( 'post' ) || is_singular( 'attachment' ) )
							&& $pubzinne_prev_post_loading 
							&& pubzinne_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'pubzinne_action_between_posts' );
						}

						// Widgets area inside page content
						pubzinne_create_widgets_area( 'widgets_above_content' );
