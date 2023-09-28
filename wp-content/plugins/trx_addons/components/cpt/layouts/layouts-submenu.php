<?php
/**
 * ThemeREX Addons Layouts: Use layouts as submenu
 *
 * @package ThemeREX Addons
 * @since v1.6.39
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Add layout's type in the list
if (!function_exists('trx_addons_cpt_layouts_submenu_add_layout_type')) {
 	add_filter( 'trx_addons_filter_layout_types', 'trx_addons_cpt_layouts_submenu_add_layout_type' );
	function trx_addons_cpt_layouts_submenu_add_layout_type($list) {
		trx_addons_array_insert_after($list, 'footer', array('submenu' => esc_html__('Submenu', 'trx_addons')));
		return $list;
	}
}

// Store layout id to the menu item's post_meta
if (!function_exists('trx_addons_cpt_layouts_submenu_save_layouts_to_menu_items')) {
 	add_action( 'wp_update_nav_menu', 'trx_addons_cpt_layouts_submenu_save_layouts_to_menu_items', 10, 1 );
	function trx_addons_cpt_layouts_submenu_save_layouts_to_menu_items($nav_menu_selected_id) {
		$menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array(
																		'orderby' => 'ID',
																		'output' => ARRAY_A,
																		'output_key' => 'ID',
																		'post_status' => 'draft,publish'
																		) );
		foreach ($menu_items as $item) {
			if (isset($_POST['menu-item-layout-submenu'][$item->db_id])) {
				update_post_meta($item->db_id, '_menu_item_layout_submenu', $_POST['menu-item-layout-submenu'][$item->db_id]);
			}
		}
	}
}

// Add layout id to the menu item
if (!function_exists('trx_addons_cpt_layouts_submenu_load_layouts_to_menu_items')) {
 	add_filter( 'wp_setup_nav_menu_item', 'trx_addons_cpt_layouts_submenu_load_layouts_to_menu_items' );
	function trx_addons_cpt_layouts_submenu_load_layouts_to_menu_items($menu_item) {
		if ( isset( $menu_item->post_type ) && 'nav_menu_item' == $menu_item->post_type && !isset($menu_item->layout_submenu)) {
			$menu_item->layout_submenu = get_post_meta( $menu_item->ID, '_menu_item_layout_submenu', true);
		}
		return $menu_item;
	}
}

// Add class 'menu-item-has-children' to the items with layouts submenu
if (!function_exists('trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items')) {
 	add_filter( 'wp_nav_menu_objects', 'trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items', 10, 2 );
	function trx_addons_cpt_layouts_submenu_add_class_has_children_to_menu_items($menu_items, $args=array()) {
		if (is_array($menu_items)) {
			foreach($menu_items as $k=>$item ) {
				if (!empty($item->layout_submenu) && (int) $item->layout_submenu > 0) {
					$menu_items[$k]->classes[] = 'menu-item-has-children';
					$menu_items[$k]->classes[] = 'menu-item-has-children-layout';
				}
			}
		}
		return $menu_items;
	}
}

// Add layout content to the item's output (if 'layouts submenu' is set for this item)
if (!function_exists('trx_addons_cpt_layouts_submenu_show_layout')) {
 	add_filter( 'walker_nav_menu_start_el', 'trx_addons_cpt_layouts_submenu_show_layout', 10, 4 );
	function trx_addons_cpt_layouts_submenu_show_layout( $output, $item, $depth, $args ) {
		if ( ! empty( $item->layout_submenu ) && (int) $item->layout_submenu > 0 ) {
			ob_start();
			// Start submenu creation
			trx_addons_sc_stack_push( 'trx_sc_layouts_submenu' );
			// Simulate usage shortcode to prevent wrap widgets to the class 'sc_layouts_item'
			trx_addons_sc_stack_push( 'trx_sc_layouts' );
			// Make layout with submenu
			do_action( 'trx_addons_action_before_show_layout_submenu' );
			trx_addons_cpt_layouts_show_layout( $item->layout_submenu );
			do_action( 'trx_addons_action_after_show_layout_submenu' );
			// End simulation
			trx_addons_sc_stack_pop();
			// End submenu creation
			trx_addons_sc_stack_pop();
			// Get layout and output it (if not empty)
			$submenu = ob_get_contents();
			ob_end_clean();
			if (!empty($submenu)) {
				$output .= '<ul class="sc_layouts_submenu"><li class="sc_layouts_submenu_wrap">' . trim( $submenu ) . "</li></ul>";
			}
		}
		return $output;
	}
}


// Substitute standard WordPress menu edit walker
if (!function_exists('trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class')) {
	add_filter( 'wp_edit_nav_menu_walker', 'trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class', 10, 2 );
	function trx_addons_cpt_layouts_submenu_set_nav_menu_edit_walker_class($class='', $menu_id=0) {
		return 'THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER';
	}
}


// Standard WordPress Walker_Nav_Menu_Edit class
require_once( ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php' );


if (!class_exists('THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER')) {
	class THEMEREX_ADDONS_NAV_MENU_EDIT_WALKER extends Walker_Nav_Menu_Edit {

		var $layouts = false;
		
		/**
		 * Start the element output.
		 *
		 * @see Walker_Nav_Menu::start_el()
		 * @since 3.0.0
		 *
		 * @global int $_wp_nav_menu_max_depth
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 * @param int    $id     Not used.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			if ($this->layouts === false) {
				$this->layouts = trx_addons_get_list_layouts(true, 'submenu', 'title');
			}
			// Add layouts selector if at least one layout is present
			// (item 0 is '- Not Selected -')
			if (is_array($this->layouts) && count($this->layouts) > 1) {
				$tmp = $output;
				parent::start_el($output, $item, $depth, $args, $id);
				$item_id = $item->ID;
				$html = '<p class="field-layout-submenu description description-wide">
						<label for="edit-menu-item-layout-submenu-'.esc_attr($item_id).'">'
							. '<span class="field-title-layout-submenu">' . esc_html__('Layout of submenu (optional)', 'trx_addons') . '</span>'
							. sprintf('<a href="%1$s" class="trx_addons_post_editor'.(empty($item->layout_submenu) || intval($item->layout_submenu)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
										admin_url( apply_filters( 'trx_addons_filter_post_edit_link', sprintf( "post.php?post=%d&amp;action=edit", $item->layout_submenu ), $item->layout_submenu) ),
										__("Open selected layout in a new tab to edit", 'trx_addons')
									)
							.'<select id="edit-menu-item-layout-submenu-'.esc_attr($item_id).'" class="widefat code edit-menu-item-layout-submenu trx_addons_layout_selector" name="menu-item-layout-submenu['.esc_attr($item_id).']">';
				foreach ($this->layouts as $id=>$title) {
					$html .= '<option value="'.esc_attr($id).'"'.(!empty($item->layout_submenu) && $item->layout_submenu == $id ? ' selected="selected"' : '') .'>'
								. esc_html($title)
							. '</option>';
				}
				$html .= '
							</select>
						</label>
					</p>';
				$output = $tmp . str_replace('<fieldset class="field-move', $html . '<fieldset class="field-move', substr($output, strlen($tmp)));
			} else {
				parent::start_el($output, $item, $depth, $args, $id);
			}
		}
	}
}
