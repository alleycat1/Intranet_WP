<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets data for the 'button' column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Button extends Abstract_Table_Data {

	private $button_text;

	public function __construct( $post, $button_text ) {
		parent::__construct( $post );
		$this->button_text = $button_text;
	}

	public function get_data() {
		$button_text  = apply_filters( 'document_library_pro_button_column_button_text', $this->button_text );
		$button_class = apply_filters( 'document_library_pro_button_column_button_class', 'posts-table-button button btn' );
		$button       = Util::format_post_link( $this->post, $button_text, $button_class );

		return apply_filters( 'document_library_pro_data_button', $button, $this->post );
	}

}
