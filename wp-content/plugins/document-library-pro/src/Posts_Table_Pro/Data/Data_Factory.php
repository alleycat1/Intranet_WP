<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table\Table_Data_Interface;
use WP_Post;

/**
 * Factory class to get the posts table data object for a given column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Data_Factory {

	/**
	 * @var Table_Args The table args object.
	 */
	private $args;

	public function __construct( Table_Args $args ) {
		$this->args = $args;
	}

	/**
	 * Creates the data object for the given column.
	 *
	 * @param string  $column The column to create the data object for.
	 * @param WP_Post $post   The post to retrieve data from.
	 * @return Table_Data_Interface The data object.
	 */
	public function create( $column, WP_Post $post ) {
		$data_obj = false;

		switch ( $column ) {
			case 'id':
			case 'title':
			case 'categories':
			case 'tags':
			case 'author':
			case 'status':
				$data_class = __NAMESPACE__ . '\\Post_' . ucfirst( $column );

				if ( class_exists( $data_class ) ) {
					$data_obj = new $data_class( $post, $this->args->links );
				}
				break;
			case 'image':
				$data_obj = new Post_Image( $post, $this->args->links, $this->args->image_size, $this->args->lightbox );
				break;
			case 'date':
				$data_obj = new Post_Date( $post, $this->args->date_format );
				break;
			case 'date_modified':
				$data_obj = new Post_Date_Modified( $post, $this->args->date_format );
				break;
			case 'content':
				$data_obj = new Post_Content( $post );
				break;
			case 'excerpt':
				$data_obj = new Post_Excerpt( $post );
				break;
			case 'button':
				$data_obj = new Post_Button( $post, $this->args->button_text );
				break;
			default:
				if ( $taxonomy = Columns_Util::get_custom_taxonomy( $column ) ) {
					$data_obj = new Post_Custom_Taxonomy( $post, $taxonomy, $this->args->links, $this->args->date_format, $this->args->date_columns );
				} elseif ( $field = Columns_Util::get_custom_field( $column ) ) {
					$data_obj = new Post_Custom_Field( $post, $field, $this->args->links, $this->args->image_size, $this->args->date_format, $this->args->date_columns );
				} elseif ( $filter = Columns_Util::get_hidden_filter( $column ) ) {
					$data_obj = new Post_Hidden_Filter( $post, $filter, $this->args->lazy_load );
				} else {
					/**
					 * Allow support for custom columns.
					 * Developers: this filter should return an object implementing Table_Data_Interface.
					 *
					 * @see Table_Data_Interface
					 */
					$data_obj = apply_filters( 'document_library_pro_custom_table_data_' . $column, false, $post, $this->args );
				}
		}

		return $data_obj;
	}

}
