<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

/**
 * Gets post data for the date modified column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Date_Modified extends Abstract_Table_Data {

	private $date_format;

	public function __construct( $post, $date_format ) {
		parent::__construct( $post );
		$this->date_format = $date_format;
	}

	public function get_data() {
		$date_modified = get_the_modified_date( $this->date_format, $this->post );

		return apply_filters( 'document_library_pro_data_date_modified', $date_modified, $this->post );
	}

	public function get_sort_data() {
		return get_the_modified_date( 'U', $this->post );
	}

}
