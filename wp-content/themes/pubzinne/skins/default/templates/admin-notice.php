<?php
/**
 * The template to display Admin notices
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.1
 */

$pubzinne_theme_slug = get_option( 'template' );
$pubzinne_theme_obj  = wp_get_theme( $pubzinne_theme_slug );
?>
<div class="pubzinne_admin_notice pubzinne_welcome_notice update-nag">
	<?php
	// Theme image
	$pubzinne_theme_img = pubzinne_get_file_url( 'screenshot.jpg' );
	if ( '' != $pubzinne_theme_img ) {
		?>
		<div class="pubzinne_notice_image"><img src="<?php echo esc_url( $pubzinne_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'pubzinne' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="pubzinne_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'pubzinne' ),
				$pubzinne_theme_obj->get( 'Name' ) . ( PUBZINNE_THEME_FREE ? ' ' . __( 'Free', 'pubzinne' ) : '' ),
				$pubzinne_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="pubzinne_notice_text">
		<p class="pubzinne_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $pubzinne_theme_obj->description ) );
			?>
		</p>
		<p class="pubzinne_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'pubzinne' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="pubzinne_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=pubzinne_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'pubzinne' );
			?>
		</a>
		<?php		
		// Dismiss this notice
		?>
		<a href="#" data-notice="admin" class="pubzinne_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="pubzinne_hide_notice_text"><?php esc_html_e( 'Dismiss', 'pubzinne' ); ?></span></a>
	</div>
</div>
