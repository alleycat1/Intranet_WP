<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Wizard;

use Barn2\Plugin\Document_Library_Pro\Admin\Wizard\Steps,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\EDD_Licensing,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\License\Plugin_License,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Setup_Wizard\Setup_Wizard as Wizard;

/**
 * Main Setup Wizard Loader
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Setup_Wizard implements Registerable {

	private $plugin;
	private $wizard;

	/**
	 * Constructor.
	 *
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {

		$this->plugin = $plugin;

		$steps = [
			new Steps\License_Verification(),
			new Steps\Layout(),
			new Steps\Table(),
			new Steps\Filters(),
			new Steps\Grid(),
			new Steps\Upsell(),
			new Steps\Completed(),
		];

		$wizard = new Wizard( $this->plugin, $steps, false );

		$wizard->configure(
			[
				'skip_url'        => admin_url( 'admin.php?page=document_library_pro' ),
				'license_tooltip' => esc_html__( 'The licence key is contained in your order confirmation email.', 'document-library-pro' ),
				'utm_id'          => 'dlp',
				'signpost'        => [
					[
						'title' => __( 'Create a document', 'document-library-pro' ),
						'href'  => admin_url( 'post-new.php?post_type=dlp_document' ),
					],
					[
						'title' => __( 'Import documents by drag and drop or CSV', 'document-library-pro' ),
						'href'  => admin_url( 'admin.php?page=dlp_import' ),
					],
					[
						'title' => __( 'Go to settings page', 'document-library-pro' ),
						'href'  => admin_url( 'admin.php?page=document_library_pro' ),
					],
				]
			]
		);

		$wizard->add_edd_api( EDD_Licensing::class );
		$wizard->add_license_class( Plugin_License::class );

		$wizard->add_custom_asset(
			$plugin->get_dir_url() . 'assets/js/admin/dlp-wizard-custom.js',
			Lib_Util::get_script_dependencies( $this->plugin, 'admin/dlp-wizard-custom.js' )
		);

		$this->wizard = $wizard;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->wizard->boot();
	}

}
