<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

/**
 * Gets post data for the post status column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Status extends Abstract_Table_Data {

	public function get_data() {
		return apply_filters( 'document_library_pro_data_status', ucfirst( $this->post->post_status ), $this->post );
	}

}
