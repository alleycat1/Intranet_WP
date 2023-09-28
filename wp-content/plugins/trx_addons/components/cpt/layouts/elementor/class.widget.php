<?php
/**
 * ThemeREX Addons Layouts: Elementor Widget class (template to create our classes with widgets)
 *
 * @package ThemeREX Addons
 * @since v1.6.51
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if (class_exists('TRX_Addons_Elementor_Widget') && !class_exists('TRX_Addons_Elementor_Layouts_Widget')) {
	abstract class TRX_Addons_Elementor_Layouts_Widget extends TRX_Addons_Elementor_Widget {
		
		// DISPLAY TEMPLATE'S PARTS
		//------------------------------------------------------------
		
		// Display common classes for layouts shortcodes
		public function sc_add_common_classes($sc) {
			?><#
			if ( typeof settings.hide_on_wide != 'undefined' && settings.hide_on_wide != '' ) {				print(' hide_on_wide'); }
			if ( typeof settings.hide_on_desktop != 'undefined' && settings.hide_on_desktop != '' ) {		print(' hide_on_desktop'); }
			if ( typeof settings.hide_on_notebook != 'undefined' && settings.hide_on_notebook != '' ) {		print(' hide_on_notebook'); }
			if ( typeof settings.hide_on_tablet != 'undefined' && settings.hide_on_tablet != '' ) {			print(' hide_on_tablet'); }
			if ( typeof settings.hide_on_mobile != 'undefined' && settings.hide_on_mobile != '' ) {			print(' hide_on_mobile'); }
			if ( typeof settings.hide_on_frontpage != 'undefined' && settings.hide_on_frontpage != '' ) {	print(' hide_on_frontpage'); }
			if ( typeof settings.hide_on_singular != 'undefined' && settings.hide_on_singular != '' ) {		print(' hide_on_singular'); }
			if ( typeof settings.hide_on_other != 'undefined' && settings.hide_on_other != '' ) {			print(' hide_on_other'); }
			if ( typeof settings.align != 'undefined' && !trx_addons_is_inherit(settings.align) ) {			print(' sc_align_'+settings.align); }
			#><?php
		}
	}
}
