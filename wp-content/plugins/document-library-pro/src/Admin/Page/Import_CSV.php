<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Page;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Admin\Importer;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles our plugin import CSV page in the admin.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Import_CSV implements Service, Registerable, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'buffer' ] );
		add_action( 'admin_init', [ $this, 'register_importer' ] );
		add_action( 'admin_menu', [ $this, 'add_import_csv_page' ] );
	}

	/**
	 * Output buffering allows CSV stepped import screen to make redirects.
	 */
	public function buffer() {
		global $plugin_page;

		if ( ! isset( $plugin_page ) || 'dlp_import_csv' !== $plugin_page ) {
			return;
		}

		ob_start();
	}

	/**
	 * Register the WP Importer.
	 */
	public function register_importer() {
		register_importer(
			'dlp_import_csv',
			__( 'Document Library Pro documents (CSV)', 'document-library-pro' ),
			__( 'Import documents to your site via a csv file.', 'document-library-pro' ),
			[ $this, 'redirect_to_importer_page' ]
		);
	}

	/**
	 * Redirect to the actual Import CSV Page.
	 */
	public function redirect_to_importer_page() {
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=dlp_import_csv' ) );
			exit;
		}
	}

	/**
	 * Add our submenu for the Import CSV Page.
	 */
	public function add_import_csv_page() {
		add_submenu_page(
			'document_library_pro',
			'',
			__( 'Import CSV', 'document-library-pro' ),
			apply_filters( 'document_library_pro_import_capability', 'manage_options' ),
			'dlp_import_csv',
			[ $this, 'render_import_csv_page' ],
			11
		);
	}

	/**
	 * Render the Import CSV Page.
	 */
	public function render_import_csv_page() {
		$importer = new Importer\CSV_Controller();
		$importer->render();
	}
}
