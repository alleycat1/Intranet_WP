<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;

defined( 'ABSPATH' ) || exit;

/**
 * Gets data for the 'file_size' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class File_Size extends Abstract_Table_Data {

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
		$file_size = get_post_meta( $this->post->ID, '_dlp_document_file_size', true );

		return apply_filters( 'document_library_pro_data_file_size', $file_size, $this->post );
	}
}
