<?php
/**
 * The template to display Admin notices
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.64
 */

$pubzinne_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$pubzinne_skins_args = get_query_var( 'pubzinne_skins_notice_args' );

?>
<div class="pubzinne_admin_notice pubzinne_skins_notice update-nag">
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
		<?php esc_html_e( 'New skins available', 'pubzinne' ); ?>
	</h3>
	<?php

	// Description
	$pubzinne_total      = $pubzinne_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$pubzinne_skins_msg  = $pubzinne_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $pubzinne_total, 'pubzinne' ), $pubzinne_total ) . '</strong>'
							: '';
	$pubzinne_total      = $pubzinne_skins_args['free'];
	$pubzinne_skins_msg .= $pubzinne_total > 0
							? ( ! empty( $pubzinne_skins_msg ) ? ' ' . esc_html__( 'and', 'pubzinne' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $pubzinne_total, 'pubzinne' ), $pubzinne_total ) . '</strong>'
							: '';
	$pubzinne_total      = $pubzinne_skins_args['pay'];
	$pubzinne_skins_msg .= $pubzinne_skins_args['pay'] > 0
							? ( ! empty( $pubzinne_skins_msg ) ? ' ' . esc_html__( 'and', 'pubzinne' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $pubzinne_total, 'pubzinne' ), $pubzinne_total ) . '</strong>'
							: '';
	?>
	<div class="pubzinne_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'pubzinne' ), $pubzinne_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="pubzinne_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $pubzinne_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'pubzinne' );
			?>
		</a>
		<?php
		// Dismiss
		?>
		<a href="#" data-notice="skins" class="pubzinne_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="pubzinne_hide_notice_text"><?php esc_html_e( 'Dismiss', 'pubzinne' ); ?></span></a>
	</div>
</div>
