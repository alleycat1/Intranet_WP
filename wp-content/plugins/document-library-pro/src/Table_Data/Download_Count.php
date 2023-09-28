<?php
namespace Barn2\Plugin\Document_Library_Pro\Table_Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Abstract_Table_Data;

defined( 'ABSPATH' ) || exit;

/**
 * Gets data for the 'download_count' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Download_Count extends Abstract_Table_Data {

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
		$download_count = get_post_meta( $this->post->ID, '_dlp_download_count', true );

		return apply_filters( 'document_library_pro_data_download_count', $download_count, $this->post );
	}
}
