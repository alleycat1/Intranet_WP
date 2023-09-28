<?php
/**
 * The template to display Admin notices
 *
 * @package ThemeREX Addons
 * @since v1.82.0
 */

$trx_addons_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_addons' );
$trx_addons_args = get_query_var( 'trx_addons_args_addons_notice' );

?>
<div class="trx_addons_admin_notice notice notice-info is-dismissible" data-notice="addons">
	<?php
	// Theme image
	if ( file_exists( trailingslashit( get_template_directory() ) .  'screenshot.jpg' ) ) {
		?>
		<div class="trx_addons_admin_notice_image"><img src="<?php echo esc_url( trailingslashit( get_template_directory_uri() ) .  'screenshot.jpg' ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'trx_addons' ); ?>"></div>
		<?php
	}
	// Title
	?>
	<h3 class="trx_addons_admin_notice_title">
		<?php esc_html_e( 'Theme required addons', 'trx_addons' ); ?>
	</h3>
	<?php

	// Description
	$trx_addons_total = $trx_addons_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$trx_addons_msg   = $trx_addons_total > 0
							// Translators: Add new addons number
							? '<strong>' . sprintf( _n( 'to update %d addon', 'to update %d addons', $trx_addons_total, 'trx_addons' ), $trx_addons_total ) . '</strong>'
							: '';
	$trx_addons_total = $trx_addons_args['download'];
	$trx_addons_msg  .= $trx_addons_total > 0
							? ( ! empty( $trx_addons_msg ) ? ' ' . esc_html__( 'and', 'trx_addons' ) . ' ' : '' )
								// Translators: Add new addons number
								. '<strong>' . sprintf( _n( 'to install %d addon', 'to install %d addons', $trx_addons_total, 'trx_addons' ), $trx_addons_total ) . '</strong>'
							: '';
	?>
	<div class="trx_addons_admin_notice_text">
		<p>
			<?php
			// Translators: Add new addons info
			echo wp_kses_data( sprintf( __( "Attention! The theme needs %s!", 'trx_addons' ), $trx_addons_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="trx_addons_admin_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $trx_addons_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Addons manager', 'trx_addons' );
			?>
		</a>
	</div>
</div>
