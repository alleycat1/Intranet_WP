<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

/**
 * Gets post data for the post author column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Author extends Abstract_Table_Data {

	public function get_data() {
		if ( array_intersect( [ 'all', 'author' ], $this->links ) ) {
			$author = get_the_author_posts_link();
		} else {
			$author = get_the_author();
		}

		return apply_filters( 'document_library_pro_data_author', $author, $this->post );
	}

}
