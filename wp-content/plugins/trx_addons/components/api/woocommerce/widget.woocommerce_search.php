<?php
/**
 * Widget: WooCommerce Search (Advanced search form)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Total number of fields in the widget
if ( ! defined( 'TRX_ADDONS_WOOCOMMERCE_SEARCH_FIELDS') ) define('TRX_ADDONS_WOOCOMMERCE_SEARCH_FIELDS', 8 );

if ( ! function_exists('trx_addons_widget_woocommerce_search_load') ) {
	add_action( 'widgets_init', 'trx_addons_widget_woocommerce_search_load', 21 );
	/**
	 * Register widget 'WooCommerce Search'
	 * 
	 * @hooked widgets_init, 21
	 */
	function trx_addons_widget_woocommerce_search_load() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		register_widget( 'trx_addons_widget_woocommerce_search' );
	}
}

if ( ! function_exists( 'trx_addons_get_list_woocommerce_search_types' ) ) {
	/**
	 * Return list of the WooCommerce search types
	 * 
	 * @trigger trx_addons_filter_get_list_woocommerce_search_types
	 *
	 * @return array  List of the WooCommerce search types
	 */
	function trx_addons_get_list_woocommerce_search_types() {
		return apply_filters( 'trx_addons_filter_get_list_woocommerce_search_types', array(
			'inline' => esc_html__('Inline', 'trx_addons'),
			'form'   => esc_html__('Form', 'trx_addons'),
			'filter' => esc_html__('Filter', 'trx_addons'),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_woocommerce_search_filters' ) ) {
	/**
	 * Return list of the WooCommerce search filters
	 *
	 * @param string $none_key  Key for the 'Not selected' item
	 * 
	 * @return array  List of the WooCommerce search filters
	 */
	function trx_addons_get_list_woocommerce_search_filters( $none_key = 'none' ) {
		$list = array(
			$none_key		=> trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) ),
			's'				=> __('Search string', 'trx_addons'),
			'product_cat'	=> __('Product Category', 'trx_addons'),
			'product_tag'	=> __('Product Tag', 'trx_addons'),
			'min_price'		=> __('Min. price', 'trx_addons'),
			'max_price'		=> __('Max. price', 'trx_addons'),
			'rating'		=> __('Rating', 'trx_addons')
		);
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( !empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				$list[ wc_attribute_taxonomy_name( $attribute->attribute_name ) ] = $attribute->attribute_label;
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_woocommerce_search_expanded' ) ) {
	/**
	 * Return list of options for the field 'Expanded' in the widget 'WooCommerce Search'
	 * 
	 * @trigger trx_addons_filter_get_list_woocommerce_search_expanded
	 *
	 * @return array  List of options
	 */
	function trx_addons_get_list_woocommerce_search_expanded() {
		$list = array(
					0 => esc_html__('Collapse all filters', 'trx_addons'),
					999 => esc_html__('Expand all filters', 'trx_addons'),
					1 => esc_html__('Expand first item only', 'trx_addons'),
				);
		for ( $i = 2; $i < TRX_ADDONS_WOOCOMMERCE_SEARCH_FIELDS; $i++ ) {
			$list[ $i ] = sprintf( esc_html__('Expand first %d items', 'trx_addons'), $i );
		}
		return apply_filters( 'trx_addons_filter_get_list_woocommerce_search_expanded', $list );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_woocommerce_output_start' ) ) {
	add_action( 'woocommerce_before_main_content', 'trx_addons_widget_woocommerce_search_woocommerce_output_start', 1 );
	/**
	 * Mark start of inline classes inside WooCommerce output (used in AJAX)
	 * 
	 * @hooked woocommerce_before_main_content, 1
	 */
	function trx_addons_widget_woocommerce_search_woocommerce_output_start() {
		trx_addons_add_inline_css( '#woocommerce_output_start{}' );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_woocommerce_output_end' ) ) {
	add_action( 'woocommerce_after_main_content', 'trx_addons_widget_woocommerce_search_woocommerce_output_end', 1000 );
	/**
	 * Mark end of inline classes inside WooCommerce output (used in AJAX)
	 * 
	 * @hooked woocommerce_after_main_content, 1000
	 */
	function trx_addons_widget_woocommerce_search_woocommerce_output_end() {
		trx_addons_add_inline_css( '#woocommerce_output_end{}' );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_add_checkbox_use_as_filter_to_attribute' ) ) {
	add_action( 'woocommerce_after_add_attribute_fields', 'trx_addons_widget_woocommerce_search_add_checkbox_use_as_filter_to_attribute' );
	add_action( 'woocommerce_after_edit_attribute_fields', 'trx_addons_widget_woocommerce_search_add_checkbox_use_as_filter_to_attribute' );
	/**
	 * Add checkbox 'Use as a filter' to the WooCommerce attribute edit form
	 * 
	 * @hooked woocommerce_after_add_attribute_fields, 10
	 * @hooked woocommerce_after_edit_attribute_fields, 10
	 */
	function trx_addons_widget_woocommerce_search_add_checkbox_use_as_filter_to_attribute() {
		$att_filter = true;
		$edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
		if ( $edit > 0 ) {
			$att_name = trx_addons_woocommerce_get_attribute_by_id( $edit, 'attribute_name' );
			if ( ! empty( $att_name ) ) {
				$att_filter = (int)trx_addons_woocommerce_get_attributes_data( $att_name, 'attribute_filter', true ) > 0;
			}
		}
		?>
		<tr class="form-field form-required">
			<th scope="row" valign="top">
				<label for="attribute_filter"><?php esc_html_e( 'Use as a filter', 'trx_addons' ); ?></label>
			</th>
			<td>
				<input name="attribute_filter" id="attribute_filter" type="checkbox" value="1" <?php checked( $att_filter, true ); ?> />
				<p class="description"><?php esc_html_e( 'This attribute can be used to filter products in a category.', 'trx_addons' ); ?></p>
			</td>
		</tr>
		<?php
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_save_checkbox_use_as_filter_to_attribute' ) ) {
	add_action( 'woocommerce_attribute_added', 'trx_addons_widget_woocommerce_search_save_checkbox_use_as_filter_to_attribute', 10, 2 );
	add_action( 'woocommerce_attribute_updated', 'trx_addons_widget_woocommerce_search_save_checkbox_use_as_filter_to_attribute', 10, 3 );
	/**
	 * Save checkbox 'Use as a filter' to the WooCommerce attribute meta
	 * 
	 * @hooked woocommerce_attribute_added, 10
	 * @hooked woocommerce_attribute_updated, 10
	 * 
	 * @param int $id  Attribute ID
	 * @param array $data  Attribute data
	 * @param string $old_slug  Old attribute slug
	 */
	function trx_addons_widget_woocommerce_search_save_checkbox_use_as_filter_to_attribute( $id, $data, $old_slug = '' ) {
		if ( $id > 0
			&& trx_addons_check_url( 'edit.php' )
			&& trx_addons_get_value_gp( 'post_type' ) == 'product'
			&& trx_addons_get_value_gp( 'page' ) == 'product_attributes'
			&& current_user_can( 'manage_product_terms' )
		) {
			if ( current_action() == 'woocommerce_attribute_added' ) {
				check_admin_referer( 'woocommerce-add-new_attribute' );
			} else {
				check_admin_referer( 'woocommerce-save-attribute_' . $id );
			}
			$att_name = trx_addons_woocommerce_get_attribute_by_id( $id, 'attribute_name' );
			if ( ! empty( $att_name ) ) {
				trx_addons_woocommerce_set_attributes_data(
					isset( $_POST['attribute_filter'] ) ? (int)$_POST['attribute_filter'] : 0,
					$att_name,
					'attribute_filter'
				);
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_add_attributes_to_filters' ) ) {
	/**
	 * Add attributes from the current category to the filters list
	 * 
	 * @param array $fields  Filters list
	 * @param array $atts_counts  Attributes counts - an array of objects with 'taxonomy' and 'count' properties
	 * 
	 * @return array  Filters list
	 */
	function trx_addons_widget_woocommerce_search_add_attributes_to_filters( $fields, $atts_counts ) {
		$insert_point = 0;
		if ( is_array( $fields ) ) {
			foreach( $fields as $k => $v ) {
				if ( $v['filter'] == 'product_cat' ) {
					$insert_point = $k + 1;
				} else if ( substr( $v['filter'], 0, 3 ) == 'pa_' ) {
					$insert_point = $k;
					break;
				}
			}
		}
		$processed = array();
		if ( is_array( $atts_counts ) ) {
			foreach( $atts_counts as $att ) {
				if ( ! empty( $att->taxonomy ) && ! isset( $processed[ $att->taxonomy ] ) && substr( $att->taxonomy, 0, 3 ) == 'pa_' ) {
					$processed[ $att->taxonomy ] = true;
					if ( (int)trx_addons_woocommerce_get_attributes_data( $att->taxonomy, 'attribute_filter', true ) > 0 && is_array( $fields ) ) {
						$found = false;
						foreach( $fields as $k => $v ) {
							if ( $v['filter'] == $att->taxonomy ) {
								$found = true;
							}
						}
						if ( ! $found ) {
							$tax_obj = get_taxonomy( $att->taxonomy );
							trx_addons_array_insert_before( $fields, $insert_point, array( array(
								'text' => $tax_obj->labels->singular_name,
								'filter' => $att->taxonomy
							) ) );
						}
					}
				}
			}
		}
		return $fields;
	}
}


/**
 * Widget: WooCommerce Search
 */
class trx_addons_widget_woocommerce_search extends TRX_Addons_Widget {

	protected $search_fields = TRX_ADDONS_WOOCOMMERCE_SEARCH_FIELDS;

	/**
	 * Widget's constructor
	 * 
	 * @trigger trx_addons_filter_widget_woocommerce_filters_total
	 */
	function __construct() {
		$widget_ops = array('classname' => 'widget_woocommerce_search', 'description' => esc_html__('Advanced search form for products', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_woocommerce_search', esc_html__('ThemeREX Product Filters', 'trx_addons'), $widget_ops );
		$this->search_fields = apply_filters( 'trx_addons_filter_widget_woocommerce_filters_total', $this->search_fields );
	}

	/**
	 * Display widget
	 * 
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget($args, $instance) {

		$style = isset($instance['style']) ? $instance['style'] : 'default';
		$type  = isset($instance['type']) ? $instance['type'] : 'inline';
		$apply = isset($instance['apply']) ? $instance['apply'] : 0;
		$ajax  = isset($instance['ajax']) ? $instance['ajax'] : 0;
		$force_checkboxes = isset($instance['force_checkboxes']) ? $instance['force_checkboxes'] : 0;
		$show_counters = isset($instance['show_counters']) ? $instance['show_counters'] : 1;
		$show_selected = isset($instance['show_selected']) ? $instance['show_selected'] : 1;
		$expanded = isset($instance['expanded']) ? (int) $instance['expanded'] : 0;
		$autofilters = isset($instance['autofilters']) ? $instance['autofilters'] : 0;
		
		// Hide widget on the single product, cart, checkout and user's account pages
		if ( apply_filters( 'trx_addons_filter_woocommerce_search',
							( $type!='inline' && ! is_shop() && ! is_product_category() && ! is_product_tag() && ! is_product_taxonomy() )
							||
							( $type=='inline' && ( is_product() || is_cart() || is_checkout() || is_account_page() ) )
						) 
		) {
			return;
		}

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);
		if (!isset($instance['fields'])) {
			$fields = array();
			for ($i=1; $i<=$this->search_fields; $i++) {
				$fields[] = array(
					'text' => isset($instance["field{$i}_text"]) ? $instance["field{$i}_text"] : '',
					'filter' => isset($instance["field{$i}_filter"]) ? $instance["field{$i}_filter"] : ''
				);
			}
		} else {
			$fields = $instance['fields'];
		}

		$last_text = isset($instance['last_text']) ? $instance['last_text'] : '';
		$button_text = !empty($instance['button_text']) ? $instance['button_text'] : '';

		if ( $type == 'filter' ) {
			wp_enqueue_script('jquery-ui-slider', false, array( 'jquery', 'jquery-ui-core' ), null, true );
		}

		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_API . 'woocommerce/tpl.widget.woocommerce_search_type_'.trx_addons_esc($type).'.php',
										TRX_ADDONS_PLUGIN_API . 'woocommerce/tpl.widget.woocommerce_search_type_form.php'
										),
									'trx_addons_args_widget_woocommerce_search',
									apply_filters(
										'trx_addons_filter_widget_args',
										array_merge( $args, compact( 'title', 'type', 'apply', 'ajax', 'force_checkboxes', 'show_selected',
																	 'show_counters', 'expanded', 'style', 'autofilters', 'fields',
																	 'last_text', 'button_text'
																	) ),
										$instance,
										'trx_addons_widget_woocommerce_search'
										)
								);
	}

	/**
	 * Update widget options
	 * 
	 * @trigger trx_addons_filter_widget_args_update
	 * 
	 * @param array $new_instance  New options
	 * @param array $instance      Old options
	 */
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['apply'] = isset( $new_instance['apply'] ) && (int)$new_instance['apply'] > 0 ? 1 : 0;
		$instance['ajax'] = isset( $new_instance['ajax'] ) && (int)$new_instance['ajax'] > 0 ? 1 : 0;
		$instance['force_checkboxes'] = isset( $new_instance['force_checkboxes'] ) && (int)$new_instance['force_checkboxes'] > 0 ? 1 : 0;
		$instance['show_selected'] = isset( $new_instance['show_selected'] ) && (int)$new_instance['show_selected'] > 0 ? 1 : 0;
		$instance['show_counters'] = isset( $new_instance['show_counters'] ) && (int)$new_instance['show_counters'] > 0 ? 1 : 0;
		$instance['expanded'] = (int)$instance['expanded'];
		$instance['autofilters'] = isset( $new_instance['autofilters'] ) && (int)$new_instance['autofilters'] > 0 ? 1 : 0;
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_woocommerce_search');
	}

	/**
	 * Display widget form
	 * 
	 * @trigger trx_addons_filter_widget_args_default
	 * @trigger trx_addons_action_before_widget_fields
	 * @trigger trx_addons_action_after_widget_title
	 * @trigger trx_addons_action_after_widget_fields
	 * 
	 * @param array $instance  Widget options
	 */
	function form($instance) {

		// Set up some default widget settings
		$default = array(
			'title' => '',
			'type' => 'inline',
			'apply' => 1,
			'ajax' => 1,
			'force_checkboxes' => 0,
			'show_selected' => 1,
			'show_counters' => 1,
			'expanded' => 0,
			'autofilters' => 0,
			'last_text' => '',
			'button_text' => ''
		);
		for ($i=1; $i<=$this->search_fields; $i++) {
			$default["field{$i}_text"] = '';
			$default["field{$i}_filter"] = '';
		}
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', $default, 'trx_addons_widget_woocommerce_search')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_woocommerce_search', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_woocommerce_search', $this);

		$this->show_field(array('name' => "type",
								'title' => __('Type', 'trx_addons'),
								'value' => $instance["type"],
								'options' => trx_addons_get_list_woocommerce_search_types(),
								'type' => 'select'));

		$this->show_field(array('name' => "ajax",
								'title' => __('Use AJAX to reload products', 'trx_addons'),
								'label' => __('Use AJAX', 'trx_addons'),
								'description' => __('Use AJAX to refresh the product list in the background instead of reloading the entire page.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["ajax"],
								'type' => 'checkbox'));

		$this->show_field(array('name' => "apply",
								'title' => __('Use "Apply" Button for Filtering', 'trx_addons'),
								'label' => __('Use "Apply" Button', 'trx_addons'),
								'description' => __('Select multiple filter values without the page reloading.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["apply"],
								'type' => 'checkbox'));

		$this->show_field(array('name' => "force_checkboxes",
								'title' => __('Simple view', 'trx_addons'),
								'label' => __('Simple fileds', 'trx_addons'),
								'description' => __('Display colors, images and buttons as checkboxes.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["force_checkboxes"],
								'type' => 'checkbox'));

		$this->show_field(array('name' => "show_counters",
								'title' => __('Show counters', 'trx_addons'),
								'label' => __('Show', 'trx_addons'),
								'description' => __('Show product counters after each item.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["show_counters"],
								'type' => 'checkbox'));

		$this->show_field(array('name' => "show_selected",
								'title' => __('Show selected items', 'trx_addons'),
								'label' => __('Show', 'trx_addons'),
								'description' => __('Show selected items counter and "Clear all" button.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["show_selected"],
								'type' => 'checkbox'));

		$this->show_field(array('name' => "expanded",
								'title' => __('Initial toggle state', 'trx_addons'),
								'description' => __('For sidebar placement ONLY!', 'trx_addons'),
								'value' => $instance["expanded"],
								'dependency' => array(
									'type' => array('filter')
								),
								'options' => trx_addons_get_list_woocommerce_search_expanded(),
								'type' => 'select'));

		$this->show_field(array('name' => "autofilters",
								'title' => __('Auto filters in categories', 'trx_addons'),
								'label' => __('Auto filters', 'trx_addons'),
								'description' => __('Use product attributes as filters for current category.', 'trx_addons'),
								'dependency' => array(
									'type' => array('filter')
								),
								'value' => $instance["autofilters"],
								'type' => 'checkbox'));

		for ( $i = 1; $i <= $this->search_fields; $i++ ) {
			$this->show_field(array('name' => "field{$i}_text",
									'title' => sprintf(__('Field %d text', 'trx_addons'), $i),
									'value' => $instance["field{$i}_text"],
									'type' => 'text'));
			$this->show_field(array('name' => "field{$i}_filter",
									'title' => sprintf(__('Field %d filter:', 'trx_addons'), $i),
									'value' => $instance["field{$i}_filter"],
									'options' => trx_addons_get_list_woocommerce_search_filters(),
									'type' => 'select'));
		}

		$this->show_field(array('name' => "last_text",
								'title' => __('Last text', 'trx_addons'),
								'value' => $instance["last_text"],
								'type' => 'text'));

		$this->show_field(array('name' => "button_text",
								'title' => __('Button text', 'trx_addons'),
								'value' => $instance["button_text"],
								'type' => 'text'));

		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_woocommerce_search', $this);
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_query_params' ) ) {
	/**
	 * Parse query params from GET/POST and wp_query_parameters
	 *
	 * @param array $fields  Array of fields to parse values for
	 * @param boolean $return_id  Use a term ID instead of slug for taxonomy fields. Default: false
	 * 
	 * @return array  Array of parsed params
	 */
	function trx_addons_widget_woocommerce_search_query_params( $fields, $return_id = false ) {
		$params = array();
		$q_obj = get_queried_object();
		// Add both price - min and max
		$need_min = $need_max = 1;
		foreach ( $fields as $fld ) {
			if ( $fld['filter'] == 'min_price' ) {
				$need_min = 0;
			} else if ( $fld['filter'] == 'max_price' ) {
				$need_max = 0;
			}
		}
		if ( $need_min + $need_max == 1 ) {	// If present only one of couple fields
			if ( $need_min ) {
				$fields[] = array( 'filter' => 'min_price' );
			} else {
				$fields[] = array( 'filter' => 'max_price' );
			}
		}
		// Fill values
		foreach ( $fields as $fld ) {
			if ( trx_addons_is_off( $fld['filter'] ) ) {
				continue;
			}
			$tax_name = $fld['filter'];
			if ( $tax_name == 'product_cat' && is_tax( $tax_name ) ) {
				$params[ $tax_name ] = $return_id ? $q_obj->term_id : $q_obj->slug;
			} else if ( ( $value = trx_addons_get_value_gp( $tax_name ) ) != '' ) {
				$params[ $tax_name ] = sanitize_text_field( $value );
			} else if ( ( $value = trx_addons_get_value_gp( trx_addons_woocommerce_get_filter_name_from_attribute( $tax_name ) ) ) != '' ) {
				$params[ $tax_name ] = sanitize_text_field( $value );
			} else if ( ( $value = trx_addons_get_value_gp( trx_addons_woocommerce_get_filter_name_from_attribute( $tax_name, true ) ) ) != '' ) {
				$params[ $tax_name ] = sanitize_text_field( $value );
			} else {
				$params[ $tax_name ] = '';
			}
		}
		return $params;
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_parse_title_with_counter' ) ) {
	/**
	 * Parse a title with counter
	 * 
	 * @trigger trx_addons_filter_parse_title_with_counter
	 *
	 * @param string $title  Title to parse in format "Title (123)"
	 * 
	 * @return array  Array of parsed params with keys 'title' and 'total'
	 */
	function trx_addons_widget_woocommerce_search_parse_title_with_counter( $title ) {
		$result = array(
			'title' => '',
			'total' => ''
		);
		if ( preg_match_all( '/(.*)\\([\\d]+\\)$/', $title, $matches ) ) {
			$result['title'] = $matches[1];
			$result['total'] = $matches[2];
		}
		return apply_filters( 'trx_addons_filter_parse_title_with_counter', $result, $title );
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_get_filtered_price' ) ) {
	/**
	 * Get filtered price: min and max values for the current query
	 *
	 * @return array  Array of min and max values
	 */
	function trx_addons_widget_woocommerce_search_get_filtered_price() {
		if ( trx_addons_exists_woocommerce() ) {
			global $wpdb;

			$args       = WC()->query->get_main_query()->query_vars;
			$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
			$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

			if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
				$tax_query[] = WC()->query->get_main_tax_query();
			}

			foreach ( $meta_query + $tax_query as $key => $query ) {
				if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
					unset( $meta_query[ $key ] );
				}
			}

			$meta_query = new WP_Meta_Query( $meta_query );
			$tax_query  = new WP_Tax_Query( $tax_query );
			$search     = WC_Query::get_main_search_query_sql();

			$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
			$search_query_sql = $search ? ' AND ' . $search : '';

			$sql = "
					SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
					FROM {$wpdb->wc_product_meta_lookup}
					WHERE product_id IN (
						SELECT ID FROM {$wpdb->posts}
						" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
						WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
						AND {$wpdb->posts}.post_status = 'publish'
						" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
					)';

			$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );
			return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
		} else {
			return array(
				'min_price' => 0,
				'max_price' => 0
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_get_filtered_product_count_by_rating' ) ) {
	/**
	 * Get filtered product count by rating
	 *
	 * @param int $rating  Rating to filter
	 * 
	 * @return int  Count of products
	 */
	function trx_addons_widget_woocommerce_search_get_filtered_product_count_by_rating( $rating ) {
		if ( trx_addons_exists_woocommerce() ) {
			global $wpdb;

			$tax_query  = WC_Query::get_main_tax_query();
			$meta_query = WC_Query::get_main_meta_query();

			// Unset current rating filter.
			foreach ( $tax_query as $key => $query ) {
				if ( ! empty( $query['rating_filter'] ) ) {
					unset( $tax_query[ $key ] );
					break;
				}
			}

			// Set new rating filter.
			$product_visibility_terms = wc_get_product_visibility_term_ids();
			$tax_query[]              = array(
				'taxonomy'      => 'product_visibility',
				'field'         => 'term_taxonomy_id',
				'terms'         => $product_visibility_terms[ 'rated-' . $rating ],
				'operator'      => 'IN',
				'rating_filter' => true,
			);

			$meta_query     = new WP_Meta_Query( $meta_query );
			$tax_query      = new WP_Tax_Query( $tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
			$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
			$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' ";
			$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

			$search = WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$sql .= ' AND ' . $search;
			}

			return absint( $wpdb->get_var( $sql ) ); // WPCS: unprepared SQL ok.
		} else {
			return 0;
		}
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_get_filtered_terms_in_category' ) ) {
	/**
	 * Get filtered terms in category with count
	 *
	 * @return array  List of terms
	 */
	function trx_addons_widget_woocommerce_search_get_filtered_terms_in_category() {
		$terms = array();
		if ( trx_addons_exists_woocommerce() ) {
			global $wpdb;
			$join = '';
			$where = '';
			$relation = 'AND';
			$tax_query = WC_Query::get_main_tax_query();
			if ( is_array( $tax_query ) ) {
				foreach( $tax_query as $k => $v ) {
					// Skiip relation and attributes
					if ( $k === 'relation' || ( ! empty( $v['taxonomy'] ) && substr( $v['taxonomy'], 0, 3 ) == 'pa_' ) ) {
						continue;
					}
					if ( $v['field'] == 'slug' && ! empty( $v['terms'][0] ) ) {
						$ids = array();
						$term = get_term_by( 'slug', $v['terms'][0], $v['taxonomy'], OBJECT );
						if ( ! empty( $term->term_id ) ) {
							$ids[] = $term->term_id;
						}
					} else if ( ! empty( $v['terms'][0] ) ) {
						$ids[] = $v['terms'][0];
					}
					if ( ! empty( $v['include_children'] ) && ! empty( $ids[0] ) ) {
						$children = get_term_children( $ids[0], $v['taxonomy'] );
						if ( is_array( $children ) && ! empty( $children ) ) {
							$ids = array_merge( $ids, $children );
						}
					}
					if ( count( $ids ) > 0 ) {
						if ( strtoupper( $v['operator'] ) != 'AND' || count( $ids ) > 1 ) {
							if ( strtoupper( $v['operator'] ) == 'AND' || strtoupper( $v['operator'] ) == 'OR' ) {
								$v['operator'] = 'IN';
							}
							$where .= " {$relation} {$wpdb->posts}.ID {$v['operator']} ("
										. "SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (" . join( ',', $ids ) . ")"
										. ")";
						} else {
							$where .= " {$relation} {$wpdb->terms}.term_id = {$ids[0]}";
						}
					}
				}
			}
			$sql = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) AS count, {$wpdb->terms}.term_id, {$wpdb->terms}.slug, {$wpdb->terms}.name, {$wpdb->term_taxonomy}.taxonomy"
						. " FROM {$wpdb->posts}"
							. " LEFT JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id"
							. " LEFT JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id"
							. " LEFT JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id"
						. " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish'"
							. $where;

			$search = WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$sql .= ' AND ' . $search;
			}

			$sql .= " GROUP BY term_id";
			$terms = $wpdb->get_results( $sql );
		}
		return $terms;
	}
}

if ( ! function_exists( 'trx_addons_widget_woocommerce_search_localize_script' ) ) {
	add_filter( "trx_addons_filter_localize_script", 'trx_addons_widget_woocommerce_search_localize_script' );
	/**
	 * Add Woocommerce Search widget specific variables to the localized script
	 *
	 * @param array $vars Localized script array
	 * 
	 * @return array    Modified array
	 */
	function trx_addons_widget_woocommerce_search_localize_script( $vars ) {
		$vars['msg_no_products_found'] = addslashes( esc_html__("No products found! Please, change query parameters and try again.", 'trx_addons') );
		return $vars;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_search-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_woocommerce() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_search-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_woocommerce() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_search-sc-vc.php';
}
