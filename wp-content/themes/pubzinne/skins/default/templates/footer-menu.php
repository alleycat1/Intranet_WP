<?php
/**
 * The template to display menu in the footer
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.10
 */

// Footer menu
$pubzinne_menu_footer = pubzinne_get_nav_menu( 'menu_footer' );
if ( ! empty( $pubzinne_menu_footer ) ) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php
			pubzinne_show_layout(
				$pubzinne_menu_footer,
				'<nav class="menu_footer_nav_area sc_layouts_menu sc_layouts_menu_default"'
					. ' itemscope="itemscope" itemtype="' . esc_attr( pubzinne_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"'
					. '>',
				'</nav>'
			);
			?>
		</div>
	</div>
	<?php
}
