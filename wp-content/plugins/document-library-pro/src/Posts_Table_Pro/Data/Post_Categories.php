<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

/**
 * Gets the post data for the categories column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Categories extends Abstract_Table_Data {

	public function get_data() {
		$sep        = parent::get_separator( 'categories' );
		$show_links = array_intersect( [ 'all', 'categories' ], $this->links );

		return apply_filters( 'document_library_pro_data_categories', parent::get_terms_for_column( 'categories', $show_links, $sep ), $this->post );
	}

}
