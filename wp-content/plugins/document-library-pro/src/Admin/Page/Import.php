<?php
namespace Barn2\Plugin\Document_Library_Pro\Admin\Page;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Document_Library_Pro\Admin\Importer;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles our plugin import page in the admin.
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Import implements Service, Registerable, Conditional {

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
		add_action( 'admin_menu', [ $this, 'add_import_page' ] );
	}

	/**
	 * Add the Import sub menu page.
	 */
	public function add_import_page() {
		add_submenu_page(
			'document_library_pro',
			__( 'Document Library Importing', 'document-library-pro' ),
			__( 'Import', 'document-library-pro' ),
			apply_filters( 'document_library_pro_import_capability', 'manage_options' ),
			'dlp_import',
			[ $this, 'render' ],
			11
		);
	}

	/**
	 * Render the import page.
	 */
	public function render() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Import documents', 'document-library-pro' ); ?></h1>

			<p>
				<?php
				printf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( ' The following options allow you to bulk import documents into the document library, either by uploading files directly or by importing a CSV file with additional information about your documents. %1$sRead documentation%2$s', 'document-library-pro' ),
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/add-import-documents/' ), true ),
					'</a>'
				);
				?>
			</p>

			<?php
			$dnd_controller = new Importer\DND_Controller();
			$dnd_controller->render();
			?>

			<h2><?php esc_html_e( 'CSV upload', 'document-library-pro' ); ?></h2>

			<p>
				<?php
				printf(
					/* translators: %1: knowledge base link start, %2: knowledge base link end */
					esc_html__( 'Upload a CSV file in the correct format, and a new document will be created for each row. %1$sRead more%2$s', 'document-library-pro' ),
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/add-import-documents/#3-bulk-import-documents' ), true ),
					'</a>'
				);
				?>
			</p>

			<a href="admin.php?page=dlp_import_csv" class="button primary"><?php esc_html_e( 'Import CSV', 'document-library-pro' ); ?></a>
		</div>
		<?php
	}
}
