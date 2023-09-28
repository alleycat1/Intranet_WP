<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;

defined( 'ABSPATH' ) || exit;

/**
 * Handle the Preview Modal output
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Preview_Modal implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'wp_footer', [ $this, 'add_to_single_document' ], 10 );
		add_filter( 'document_library_pro_get_table_output', [ $this, 'add_to_document_table' ], 99, 3 );
		add_filter( 'document_library_pro_get_grid_output', [ $this, 'add_to_document_table' ], 99, 3 );
	}

	/**
	 * Adds the preview modal output for each table rendered in html.
	 *
	 * @param string $result
	 * @param string $output
	 * @param Posts_Table_Pro/Posts_Table $posts_table
	 */
	public function add_to_document_table( $result, $output, $posts_table ) {
		if ( $output !== 'html' ) {
			return $result;
		}

		if ( ! $posts_table->args->preview ) {
			return $result;
		}

		return $result . $this->get_modal_html( $posts_table->id );
	}

	/**
	 * Adds the preview modal to the single content.
	 */
	public function add_to_single_document() {
		if ( ! is_singular( Post_Type::POST_TYPE_SLUG ) ) {
			return;
		}

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return;
		}

		$options = Options::get_shortcode_options();

		if ( ! $options['preview'] ) {
			return;
		}

		echo $this->get_modal_html( "dlp_single_$post_id" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Gets the HTML for a preview modal.
	 *
	 * @param string $modal_id
	 * @return string
	 */
	private function get_modal_html( $modal_id ) {
		ob_start();

		?>
		<div id="modal_<?php echo esc_attr( $modal_id ); ?>" class="dlp-preview-modal" aria-hidden="true">
			<div class="dlp-preview-modal-overlay" tabindex="-1" data-dlp-preview-close="#modal_<?php echo esc_attr( $modal_id ); ?>">
				<a class="dlp-preview-modal-close" data-dlp-preview-close="#modal_<?php echo esc_attr( $modal_id ); ?>">
					<?php SVG_Icon::render( 'close' ); ?>
				</a>

				<div class="dlp-preview-modal-container" role="dialog" aria-modal="true" aria-labelledby="dlp-preview-modal-title">
					<main class="dlp-preview-modal-content"></main>
					<footer class="dlp-preview-modal-footer"></footer>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}
}
