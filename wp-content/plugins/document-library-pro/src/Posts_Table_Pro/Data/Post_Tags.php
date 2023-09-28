<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

/**
 * Gets post data for the tags column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Tags extends Abstract_Table_Data {

	public function get_data() {
		$sep        = parent::get_separator( 'tags' );
		$show_links = array_intersect( [ 'all', 'tags' ], $this->links );

		return apply_filters( 'document_library_pro_data_tags', parent::get_terms_for_column( 'tags', $show_links, $sep ), $this->post );
	}

}
