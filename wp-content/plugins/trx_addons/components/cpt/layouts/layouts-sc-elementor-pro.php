<?php
/**
 * ThemeREX Addons Layouts: Elementor Pro utilities
 *
 * @package ThemeREX Addons
 * @since v2.6.1
 */

namespace ElementorPro\Modules\ThemeBuilder\ThemeSupport;

//use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
//use ElementorPro\Modules\ThemeBuilder\Module;

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


class ThemeREX_Theme_Support {

	var $elementor_pro_header_id = -1000;
	
	var $elementor_pro_footer_id = -2000;

	var $elementor_pro_template_replace = true;

	var $elementor_template_type_meta = '_elementor_template_type';


	public function __construct() {
		// Disable default header and footer from Elementor Pro
		add_action( 'elementor/theme/register_locations', [ $this, 'deregister_locations' ] );

		// Add handler for header/footer lists
		$theme_slug = str_replace( '-', '_', get_template() );
		add_filter( "{$theme_slug}_filter_list_header_styles", [ $this, 'add_header_style' ], 20 );
		add_filter( "{$theme_slug}_filter_list_footer_styles", [ $this, 'add_footer_style' ], 20 );

		// Show custom layout from Elementor Pro
		add_filter( 'trx_addons_action_show_layout', [ $this, 'show_header_footer' ], 20, 2 );

		// Replace document type for header/footer
		add_action( 'elementor/documents/register', [ $this, 'replace_document_type' ] );

		// Replace post type template for header/footer
		add_action( 'template_include', [ $this, 'replace_post_type_template' ], 9999 );
	}

	/**
	 * Disable default header and footer from Elementor Pro
	 * 
	 * @param Locations_Manager $manager
	 */
	public function deregister_locations( $manager ) {
		if ( ! is_admin()
			&& (
				! trx_addons_is_preview( 'elementor')
				|| ! class_exists( '\Elementor\TemplateLibrary\Source_Local' )
				|| \Elementor\TemplateLibrary\Source_Local::CPT !== get_post_type()
				)
		) {
			$manager->register_core_location( 'header' );
			$manager->register_core_location( 'footer' );
		}
	}

	public function add_header_style( $list ) {
		// Add universal header - Elementor Pro select corresponding header by conditions
		$list[ 'header-custom-' . $this->elementor_pro_header_id ] = __( 'Elementor Pro Header (by conditions)', 'trx_addons' );
		// Add all headers from Elementor Pro - user can select any header in theme options
		if ( class_exists( '\Elementor\TemplateLibrary\Source_Local' ) ) {
			$layouts = trx_addons_get_list_posts(
				false, array(
					'post_type'    => \Elementor\TemplateLibrary\Source_Local::CPT,
					'meta_key'     => $this->elementor_template_type_meta,
					'meta_value'   => 'header',
					'orderby'      => 'ID',
					'order'        => 'asc',
					'not_selected' => false,
				)
			);
			$new_list = array();
			foreach ( $layouts as $id => $title ) {
				if ( 'none' != $id ) {
					$new_list[ 'header-custom-' . intval( $id ) ] = __( 'Elementor Pro Header:', 'trx_addons' ) . ' ' . $title;
				}
			}
			$list = trx_addons_array_merge( $list, $new_list );
		}
		return $list;
	}

	public function add_footer_style( $list ) {
		// Add universal footer - Elementor Pro select corresponding footer by conditions
		$list[ 'footer-custom-' . $this->elementor_pro_footer_id ] = __( 'Elementor Pro Footer (by conditions)', 'trx_addons' );
		// Add all footers from Elementor Pro - user can select any footer in theme options
		if ( class_exists( '\Elementor\TemplateLibrary\Source_Local' ) ) {
			$layouts = trx_addons_get_list_posts(
				false, array(
					'post_type'    => \Elementor\TemplateLibrary\Source_Local::CPT,
					'meta_key'     => $this->elementor_template_type_meta,
					'meta_value'   => 'footer',
					'orderby'      => 'ID',
					'order'        => 'asc',
					'not_selected' => false,
				)
			);
			$new_list = array();
			foreach ( $layouts as $id => $title ) {
				if ( 'none' != $id ) {
					$new_list[ 'footer-custom-' . intval( $id ) ] = __( 'Elementor Pro Footer:', 'trx_addons' ) . ' ' . $title;
				}
			}
			$list = trx_addons_array_merge( $list, $new_list );
		}
		return $list;
	}

	public function show_header_footer( $layout_id = 0, $post_id = 0 ) {
		if ( in_array( $layout_id, array( $this->elementor_pro_header_id, $this->elementor_pro_footer_id ) ) ) {
			if ( class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
				do_action( 'trx_addons_action_before_show_layout', $layout_id );
				trx_addons_sc_stack_push('show_layout');
				$did_location = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location( $layout_id == $this->elementor_pro_header_id ? 'header' : 'footer' );
				trx_addons_sc_stack_pop();
				do_action( 'trx_addons_action_after_show_layout', $layout_id );
			}
		}
	}

	public function replace_document_type( $documents_manager ) {
		if ( apply_filters( 'trx_addons_filter_replace_elementor_pro_template', $this->elementor_pro_template_replace )
			&& class_exists( '\Elementor\TemplateLibrary\Source_Local' )
			&& ( $fdir = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/class.document-pro.php" ) ) != ''
		) { 
			include_once $fdir;
			$documents_manager->register_document_type( \Elementor\TemplateLibrary\Source_Local::CPT, 'TRX_Addons_Elementor_Layouts_Document_Pro' );
		}
	}

	public function replace_post_type_template( $template ) {
		if ( apply_filters( 'trx_addons_filter_replace_elementor_pro_template', $this->elementor_pro_template_replace )
			&& class_exists( '\Elementor\TemplateLibrary\Source_Local' )
			&& trx_addons_is_singular( \Elementor\TemplateLibrary\Source_Local::CPT )
			&& in_array( get_post_meta( get_the_ID(), $this->elementor_template_type_meta, true ), array( 'header', 'footer' ) )
		) {
			$template = trx_addons_elm_is_preview()
							? trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/tpl.editor-pro.php")
							: trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/tpl.single-pro.php");
		}
		return $template;
	}
}

new ThemeREX_Theme_Support;
