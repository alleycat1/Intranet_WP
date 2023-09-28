<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets post data for the ID column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Id extends Abstract_Table_Data {

	public function get_data() {
		$id = apply_filters( 'document_library_pro_data_id_before_link', $this->post->ID, $this->post );

		if ( array_intersect( [ 'all', 'id' ], $this->links ) ) {
			$id = Util::format_post_link( $this->post, $id );
		}

		return apply_filters( 'document_library_pro_data_id', $id, $this->post );
	}

}
