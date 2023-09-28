<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets post data for the title column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Title extends Abstract_Table_Data {

	public function get_data() {
		$title = apply_filters( 'document_library_pro_data_title_before_link', get_the_title( $this->post ), $this->post );

		if ( array_intersect( [ 'all', 'title' ], $this->links ) ) {
			$title = Util::format_post_link( $this->post, $title );
		}

		return apply_filters( 'document_library_pro_data_title', $title, $this->post );
	}

}
