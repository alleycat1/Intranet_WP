<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data,
	Barn2\Plugin\Document_Library_Pro\Document;

defined( 'ABSPATH' ) || exit;
/**
 * Gets data for the 'version' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Version extends Abstract_Table_Data {

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post
	 * @param Table_Args $args
	 */
	public function __construct( $post, $args ) {

		parent::__construct( $post );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
		$html     = '';
		$document = new Document( $this->post->ID );

		$html = sprintf( '<div class="dlp-table-document-version-wrap">%s</div>', (string) $document->get_link_version() );

		return apply_filters( 'document_library_pro_data_version', $html, $this->post );
	}
}
