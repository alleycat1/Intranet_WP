<?php
/**
 * Lists generators
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists('trx_addons_get_not_selected_text') ) {
	/**
	 * Return text for not selected item in the list
	 * 
	 * @trigger trx_addons_filter_not_selected_text
	 *
	 * @param string $label  Label to show in the text
	 * 
	 * @return string        Text for not selected item in the list
	 */
	function trx_addons_get_not_selected_text( $label ) {
		return apply_filters( 'trx_addons_filter_not_selected_text',
								sprintf( apply_filters( 'trx_addons_filter_not_selected_mask', __( '- %s -', 'trx_addons' ) ), $label )
							);
	}
}

if ( ! function_exists( 'trx_addons_get_list_range' ) ) {
	/**
	 * Return list with numbers from $from to $to
	 *
	 * @param int $from       Start number
	 * @param int $to         End number
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * 
	 * @return array          List of numbers
	 */
	function trx_addons_get_list_range( $from = 1, $to = 2, $prepend_inherit = false ) {
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			$list[ $i ] = $i;
		}
		return $prepend_inherit 
				? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
				: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_months' ) ) {
	/**
	 * Return list of months
	 *
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * 
	 * @return array          List of months
	 */
	function trx_addons_get_list_months( $prepend_inherit = false ) {
		$list = array();
		for ( $i = 1; $i <= 12; $i++ ) {
			$list[$i] = date_i18n( 'F', strtotime( '2018-'.$i.'-01' ) );
		}
		return $prepend_inherit 
				? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
				: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_taxonomies_all' ) ) {
	/**
	 * Return list of allowed custom post's taxonomies
	 *
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * 
	 * @return array          List of taxonomies
	 */
	function trx_addons_get_list_taxonomies_all( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$post_types = get_post_types(array(
				'public' => true,
				'show_ui' => true,
				'exclude_from_search' => false
			), 'objects');
			if ( is_array( $post_types ) ) {
				foreach ( $post_types as $pt ) {
					$terms = get_object_taxonomies( $pt->name, 'objects' );
					foreach ( $terms as $t ) {
						if ( empty( $t->show_ui ) || empty( $t->show_in_menu ) ) {
							continue;
						}
						if ( ! isset( $list[ $t->name ] ) ) {
							$list[ $t->name ] = sprintf( '%1$s (%2$s)', $t->label, $pt->label );
						}
					}
				}
			}
		}
		return $prepend_inherit
			? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
			: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_taxonomies' ) ) {
	/**
	 * Return list of allowed custom post's taxonomies
	 *
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * @param string $post_type      Post type
	 * 
	 * @return array          List of taxonomies
	 */
	function trx_addons_get_list_taxonomies( $prepend_inherit = false, $post_type = 'post' ) {
		static $list = array();
		if ( empty( $list[ $post_type ] ) ) {
			$list[ $post_type ] = array();
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $taxonomies as $slug => $taxonomy ) {
				$list[$post_type][$slug] = $taxonomy->label;
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list[$post_type] )
					: $list[ $post_type ];
	}
}

if ( ! function_exists( 'trx_addons_get_list_categories' ) ) {
	/**
	 * Return list of categories
	 *
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * 
	 * @return array          List of categories
	 */
	function trx_addons_get_list_categories( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$terms = get_categories( array(
											'type' => 'post',
											'orderby' => 'name',
											'order' => 'ASC',
											'hide_empty' => 0,
											'hierarchical' => 1,
											'taxonomy' => 'category',
											'pad_counts' => false
											)
										);
			if ( ! is_wp_error( $terms ) && is_array( $terms ) && count( $terms ) > 0 ) {
				$list = trx_addons_get_hierarchical_list( apply_filters( 'the_category_list', $terms ) );
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_terms' ) ) {
	/**
	 * Return list of terms
	 *
	 * @param bool $prepend_inherit  If true - add 'Inherit' item to the beginning of the list
	 * @param string $taxonomy       Taxonomy name
	 * @param array $opt             Additional options for get_terms()
	 * 
	 * @return array          List of terms
	 */
	function trx_addons_get_list_terms( $prepend_inherit = false, $taxonomy = 'category', $opt = array() ) {
		static $list = array();
		$opt = array_merge( array(
			'meta_query'   => '',
			'meta_key'	   => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_type'    => '',
			'parent'	   => '',
			'pad_counts'   => false,
			'hide_empty'   => false,
			'return_key'   => 'id'
			), $opt );
		$hash = 'list_terms'
				. '_' . ( is_array( $taxonomy ) ? join( '_', $taxonomy ) : $taxonomy )
				. '_' . ( $opt['return_key'])
				. '_' . ( is_array( $opt['parent'] ) ? join('_', $opt['parent']) : $opt['parent'] )
				. '_' . ( $opt['meta_key'] )
				. '_' . ( $opt['meta_value'] )
				. '_' . ( is_array( $opt['meta_query'] ) ? serialize( $opt['meta_query'] ) : $opt['meta_query'] );
		if ( empty( $list[ $hash ] ) ) {
			$list[ $hash ] = array();
			if ( is_array( $taxonomy ) || taxonomy_exists( $taxonomy ) ) {
				$args = array(
					'orderby' => 'name',
					'order' => 'ASC',
					'hide_empty' => $opt['hide_empty'],
					'hierarchical' => 1,
					'taxonomy' => $taxonomy,
					'pad_counts' => $opt['pad_counts']
					);
				if ( $opt['parent'] != '' ) {
					$args['parent'] = $opt['parent'];
				}
				if ( is_array( $opt['meta_query'] ) ) {
					$args['meta_query'] = $opt['meta_query'];
				} else if ( ! empty( $opt['meta_key'] ) ) {
					$args['meta_key'] = $opt['meta_key'];
					$args['meta_value'] = $opt['meta_value'];
					if ( ! empty( $opt['meta_type'] ) ) {
						$args['meta_type'] = $opt['meta_type'];
					}
					if ( ! empty( $opt['meta_compare'] ) ) {
						$args['meta_compare'] = $opt['meta_compare'];
					}
				}
				$terms = get_terms( $taxonomy, apply_filters( 'trx_addons_filter_get_list_terms_args', $args, $taxonomy, $opt ) );
			} else {
				$terms = trx_addons_get_terms_by_taxonomy_from_db( $taxonomy, $opt );
			}
			if ( ! is_wp_error( $terms ) && is_array( $terms ) && count( $terms ) > 0 ) {
				$list[ $hash ] = trx_addons_get_hierarchical_list( apply_filters( "the_{$taxonomy}_list", $terms ), (int) $opt['parent'], 0, $opt['return_key'] );
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list[ $hash ] ) 
					: $list[ $hash ];
	}
}

if ( ! function_exists( 'trx_addons_get_hierarchical_terms' ) ) {
	/**
	 * Return hierarchical list of terms
	 * 
	 * @param array $terms  List of terms
	 * @param int $parent   Parent term ID
	 * @param int $level    Level of current term
	 * 
	 * @return array        List of terms
	 */
	function trx_addons_get_hierarchical_terms( $terms, $parent = 0, $level = 0 ) {
		$list = array();
		foreach ( $terms as $term ) {
			if ( ( empty( $term->parent ) ? 0 : $term->parent ) == $parent ) {
				$term->hierarchy_level = $level;
				$list[] = $term;
				$list = array_merge( $list, trx_addons_get_hierarchical_terms( $terms, $term->term_id, $level + 1 ) );
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_hierarchical_list' ) ) {
	/**
	 * Return hierarchical list of terms
	 * 
	 * @param array $terms  List of terms
	 * @param int $parent   Parent term ID
	 * @param int $level    Level of current term
	 * @param string $key   Key for the list element: 'id' - term ID, 'slug' - term slug
	 * 
	 * @return array        List of terms
	 */
	function trx_addons_get_hierarchical_list($terms, $parent=0, $level=0, $key='id') {
		$list = array();
		foreach ( $terms as $term ) {
			if ( $term->parent == $parent ) {
				$list[ $key=='id' ? $term->term_id : $term->slug ] = ( $level ? str_repeat( '-', $level ) . ' ': '' )
										. $term->name
										. ( ! empty( $term->count )
												? ' (' . intval( $term->count ) . ')'
												: ''
											);
				$list = trx_addons_array_merge( $list, trx_addons_get_hierarchical_list( $terms, $term->term_id, $level+1, $key ) );
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_posts_types' ) ) {
	/**
	 * Return list of post types
	 * 
	 * @trigger trx_addons_filter_get_list_posts_types
	 *
	 * @param bool $prepend_inherit If true - add first element to the array with 'inherit' key
	 *
	 * @return array Associative array with slugs and names
	 */
	function trx_addons_get_list_posts_types( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$types = get_post_types(
						array( 
							'public' => true,
							'exclude_from_search' => false
						), 
						'objects'
					);
			$list = array();
			if ( is_array( $types ) ) {
				$exclude = apply_filters( 'trx_addons_filter_get_list_post_types_exclude', array( 'attachment' ) );
				foreach ( $types as $slug => $type ) {
					if ( in_array( $type->name, $exclude ) ) continue;
					$list[ $type->name ] = $type->label;
				}
			}
			// Add our custom layouts
			if ( defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) ) {
				$list[ TRX_ADDONS_CPT_LAYOUTS_PT ] = esc_html__( 'Layout', 'trx_addons' );
			}
			$list = apply_filters( 'trx_addons_filter_get_list_post_types', $list );
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_posts' ) ) {
	/**
	 * Return list post items from any post type and taxonomy
	 * 
	 * @trigger trx_addons_filter_get_list_posts_args
	 *
	 * @param bool $prepend_inherit If true - add first element to the array with 'inherit' key
	 * @param array|string $opt  Additional options for query posts (see get_posts) or post type name
	 * 
	 * @return array Associative array with slugs and names
	 */
	function trx_addons_get_list_posts( $prepend_inherit = false, $opt = array() ) {
		static $list = array();
		$opt = array_merge( array(
			'post_type'			=> 'post',
			'post_status'		=> 'publish',
			'post_parent'		=> '',
			'taxonomy'			=> 'category',
			'taxonomy_value'	=> '',
			'meta_key'			=> '',
			'meta_value'		=> '',
			'meta_compare'		=> '',
			'meta_type'			=> '',
			'suppress_filters'	=> false,	// Need to compatibility with WPML, because default value is true in the get_posts()
			'posts_per_page'	=> -1,
			'orderby'			=> 'post_date',
			'order'				=> 'desc',
			'not_selected'		=> true,
			'return'			=> 'id'
			), is_array( $opt ) ? $opt : array( 'post_type' => $opt ) );
		$hash = 'list_posts'
				. ( isset( $opt['post__in'] )
					? '_' . ( is_array( $opt['post__in'] ) ? join( '_', $opt['post__in'] ) : $opt['post__in'] )
					: (   '_' . ( is_array($opt['post_type'] ) ? join( '_', $opt['post_type'] ) : $opt['post_type'] )
						. '_' . ( is_array($opt['post_parent'] ) ? join( '_', $opt['post_parent'] ) : $opt['post_parent'] )
						. '_' . ( $opt['taxonomy'] )
						. '_' . ( is_array( $opt['taxonomy_value'] ) ? join( '_', $opt['taxonomy_value'] ) : $opt['taxonomy_value'] )
						. '_' . ( $opt['meta_key'] )
						. '_' . ( $opt['meta_compare'] )
						. '_' . ( $opt['meta_value'] )
						. '_' . ( $opt['orderby'] )
						. '_' . ( $opt['order'] )
						. '_' . ( $opt['return'] )
						. '_' . ( $opt['posts_per_page'] )
						)
					);
		if ( ! isset( $list[ $hash ] ) ) {
			$list[ $hash ] = array();
			if ( $opt['not_selected'] !== false ) {
				$list[ $hash ]['none'] = $opt['not_selected']===true 
													? trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) )
													: $opt['not_selected'];
			}
			$args = array(
				'post_type' => $opt['post_type'],
				'post_status' => $opt['post_status'],
				'posts_per_page' => -1 == $opt['posts_per_page'] ? 1000 : $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'	=> $opt['orderby'],
				'order'		=> $opt['order']
			);
			if ( ! empty( $opt['post_parent'] ) ) {
				if ( is_array($opt['post_parent'] ) ) {
					$args['post_parent__in'] = $opt['post_parent'];
				} else {
					$args['post_parent'] = $opt['post_parent'];
				}
			}
			if ( ! empty( $opt['taxonomy_value'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field' => is_array( $opt['taxonomy_value'] ) 
										? ( (int)$opt['taxonomy_value'][0] > 0  ? 'term_taxonomy_id' : 'slug' )
										: ( (int)$opt['taxonomy_value'] > 0  ? 'term_taxonomy_id' : 'slug' ),
						'terms' => is_array( $opt['taxonomy_value'] )
										? $opt['taxonomy_value'] 
										: ( (int)$opt['taxonomy_value'] > 0 ? (int)$opt['taxonomy_value'] : $opt['taxonomy_value'] )
					)
				);
			}
			if ( ! empty( $opt['meta_key'] ) ) {
				$args['meta_key'] = $opt['meta_key'];
			}
			if ( ! empty( $opt['meta_value'] ) ) {
				$args['meta_value'] = $opt['meta_value'];
			}
			if ( ! empty( $opt['meta_compare'] ) ) {
				$args['meta_compare'] = $opt['meta_compare'];
			}
			if ( ! empty( $opt['meta_type'] ) ) {
				$args['meta_type'] = $opt['meta_type'];
			}
			$posts = get_posts( apply_filters( 'trx_addons_filter_get_list_posts_args', $args, $opt ) );			
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					$list[$hash][$opt['return']=='id' ? $post->ID : $post->post_title] = $post->post_title . ( $args['post_type'] == 'any' ? ' (' . $post->post_type . ')' : '' );
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__("Inherit", 'trx_addons' ) ), $list[$hash] ) 
					: $list[$hash];
	}
}

if ( ! function_exists( 'trx_addons_get_list_pages' ) ) {
	/**
	 * Return list of the pages
	 * 
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 * @param array|string $opt         Options of the query to get pages or post type name
	 * 
	 * @return array  Associative array with the list of the pages
	 */
	function trx_addons_get_list_pages( $prepend_inherit = false, $opt = array() ) {
		$opt = array_merge( array(
			'post_type'			=> 'page',
			'post_status'		=> 'publish',
			'taxonomy'			=> '',
			'taxonomy_value'	=> '',
			'posts_per_page'	=> -1,
			'orderby'			=> 'title',
			'order'				=> 'asc',
			'return'			=> 'id'
			), is_array( $opt ) ? $opt : array( 'post_type' => $opt ) );
		return trx_addons_get_list_posts( $prepend_inherit, $opt );
	}
}

if ( ! function_exists( 'trx_addons_get_list_layouts' ) ) {
	/**
	 * Return list of the custom layouts
	 *
	 * @param boolean $not_selected  Add not selected item in the begin of the list
	 * @param string $type           Type of the layout (custom, header, footer, etc.)
	 * @param string $order          Order of the layouts
	 * 
	 * @return array                 Associative array with the list of the layouts
	 */
	function trx_addons_get_list_layouts( $not_selected = false, $type = 'custom', $order = 'ID' ) {
		if ( defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) ) {
			$list = trx_addons_get_list_posts( false, array(
						'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
						'meta_key' => 'trx_addons_layout_type',
						'meta_value' => $type,
						'orderby' => $order,
						'order' => 'asc',
						'not_selected' => $not_selected
					) );
		} else {
			$list = array();
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_layouts_show_on' ) ) {
	/**
	 * Return list of cases when layout show on
	 *
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 *
	 * @return array                    Associative array with the list of the layouts show on
	 */
	function trx_addons_get_list_layouts_show_on( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_get_list_layouts_display', array(
			'none'				=> esc_html__( 'Do not show on page loads', 'trx_addons' ),
			'on_page_load'		=> esc_html__( 'Every time the page loads', 'trx_addons' ),
			'on_page_load_once'	=> esc_html__( 'When the page first loads', 'trx_addons' ),
			'on_page_close'		=> esc_html__( 'When leaving site', 'trx_addons' ),
		));
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_users' ) ) {
	/**
	 * Return list of the registered users
	 *
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 * @param string $by                Field to return: 'id' - ID, 'login' - user_login, 'email' - user_email, 'nicename' - user_nicename, 'display_name' - display_name
	 * @param array $roles              List of roles to return
	 *
	 * @return array                    Associative array with the list of the users
	 */
	function trx_addons_get_list_users( $prepend_inherit = false, $by = 'login', $roles = array( 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ) ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$list['none'] = trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) );
			$users = get_users(array(
									'orderby' => 'display_name',
									'order' => 'ASC',
									'role__in' => $roles
									)
								);
			if ( is_array( $users ) && count( $users ) > 0 ) {
				foreach ( $users as $user ) {
					$accept = true;
					//--- Not need to check roles because a param 'role__in' is added to the query above
					//--- ( this param help filter records and increase a query speed:
					//---   if a site has many subscribers - they are not included in the array $users )
					if ( false && is_array( $user->roles ) && count( $user->roles ) > 0 ) {
						$accept = false;
						foreach ( $user->roles as $role ) {
							if ( in_array( $role, $roles ) ) {
								$accept = true;
								break;
							}
						}
					}
					//---
					if ( $accept ) {
						$list[ ( $by=='login' ? $user->user_login : $user->ID ) ] = $user->display_name;
					}
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_users_roles' ) ) {
	/**
	 * Return list of the registered users roles
	 *
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 *
	 * @return array                    Associative array with the list of the users roles
	 */
	function trx_addons_get_list_users_roles( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$list['none'] = trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) );
			if ( function_exists( 'get_editable_roles' ) ) {
				$roles = get_editable_roles();
				if ( is_array( $roles ) ) {
					foreach ( $roles as $role => $details ) {
						$list[ $role ] = translate_user_role( $details['name'] );
					}
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_team_posts_types' ) ) {
	/**
	 * Return list of the team-compatible posts types
	 * 
	 * @trigger trx_addons_filter_get_list_team_posts_types
	 *
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 *
	 * @return array                    Associative array with the list of the team-compatible posts types
	 */
	function trx_addons_get_list_team_posts_types( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = apply_filters( 'trx_addons_filter_get_list_team_posts_types', array( TRX_ADDONS_CPT_TEAM_PT => __( 'Team', 'trx_addons' ) ) );
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_icons' ) ) {
	/**
	 * Return list of icons, images or SVG 
	 *
	 * @param string $style		Style of the icons: 'icons' | 'images' | 'svg'
	 *
	 * @return array			Associative array with the list of the icons
	 */
	function trx_addons_get_list_icons( $style ) {
		$lists = get_transient( 'trx_addons_list_icons' );
		if ( ! is_array( $lists ) || ! isset( $lists[ $style ] ) || ! is_array( $lists[ $style ] ) || count( $lists[ $style ] ) < 2 ) {
			if ( $style == 'icons' ) {
				$lists[ $style ] = trx_addons_array_from_list( trx_addons_get_list_icons_classes() );
			} else if ( $style == 'images' ) {
				$lists[ $style ] = trx_addons_get_list_files( 'css/icons.png', 'png' );
			} else { //if ( $style == 'svg' ) {
				$lists[ $style ] = trx_addons_get_list_files ('css/icons.svg', 'svg' );
			}
			if ( is_admin() && is_array( $lists[ $style ] ) && count( $lists[ $style ] ) > 1 ) {
				set_transient( 'trx_addons_list_icons', $lists, 6 * 60 * 60 );		// Store to the cache for 6 hours
			}
		}
		return $lists[ $style ];
	}
}

if ( ! function_exists( 'trx_addons_get_list_icons_classes' ) ) {
	/**
	 * Return list of the icons classes
	 * 
	 * @trigger trx_addons_filter_get_list_icons_classes
	 *
	 * @param boolean $prepend_inherit  Add inherit to the start of the list
	 *
	 * @return array                    Associative array with the list of the icons classes
	 */
	function trx_addons_get_list_icons_classes( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = apply_filters( 'trx_addons_filter_get_list_icons_classes', $list, $prepend_inherit );
			if ( $list === false || in_array( trx_addons_get_setting( 'icons_source' ), array( 'internal', 'both' ) ) ) {
				if ( ! is_array( $list ) ) {
					$list = array();
				}
				if ( is_admin() ) {
					$list_internal = array_filter(
										trx_addons_parse_icons_classes( trx_addons_get_file_dir( "css/font-icons/css/trx_addons_icons-codes.css" ) ),
										function( $icon ) use ( $list ) {
											return ! in_array( str_replace( 'trx_addons_', '', $icon ), $list );
										} );
					$list = array_merge( $list, $list_internal );
				}
			}
			if ( ! isset( $list['none'] ) ) {
				$list = trx_addons_array_merge( array('none' => 'none'), $list );
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array('inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_files' ) ) {	
	/**
	 * Return list of files in the folder
	 *
	 * @param string $folder		Folder to scan
	 * @param string $ext			Files extension to show
	 * @param boolean $only_names	Show only names of the files (without the extension)
	 * 
	 * @return array				List of files from the folder
	 */
	function trx_addons_get_list_files( $folder, $ext = '', $only_names = false ) {
		static $list = array();
		$hash = $folder . '_' . $ext . '_' . ( $only_names ? '1' : '0' );
		if ( ! isset( $list[ $hash ] ) ) {
			$dir = trx_addons_get_folder_dir( $folder );
			$url = trx_addons_get_folder_url( $folder );
			$list[ $hash ] = array();
			if ( ! empty( $dir ) && is_dir( $dir ) ) {
				$files = @glob( sprintf( "%s/%s", $dir, ! empty($ext) ? "*.{$ext}" : '*.*' ) );
				if ( is_array( $files ) ) {
					foreach ( $files as $file ) {
						if ( substr( $file, 0, 1 ) == '.' || is_dir( $file ) ) {
							continue;
						}
						$file = basename( $file );
						$key = substr( $file, 0, strrpos( $file, '.' ) );
						if ( substr( $key, -4 ) == '.min' ) {
							$key = substr( $file, 0, strrpos( $key, '.' ) );
						}
						$list[ $hash ][ $key ] = $only_names ? ucfirst( str_replace( '_', ' ', $key ) ) : $url . '/' . $file;
					}
				}
				if ( ! isset( $list[ $hash ]['none'] ) ) {
					$list[ $hash ] = trx_addons_array_merge( array( 'none' => '' ), $list[ $hash ] );
				}
			}
		}
		return $list[ $hash ];
	}
}

if ( ! function_exists( 'trx_addons_get_list_folders' ) ) {	
	/**
	 * Return list of folders in the folder
	 *
	 * @param string $folder		Folder to scan
	 * 
	 * @return array				List of folders from the folder
	 */
	function trx_addons_get_list_folders( $folder ) {
		static $list = array();
		$hash = $folder;
		if ( ! isset( $list[ $hash ] ) ) {
			$dir = trx_addons_get_folder_dir( $folder );
			$list[ $hash ] = array();
			if ( ! empty( $dir ) && is_dir( $dir ) ) {
				$folders = @glob( sprintf("%s/*", $dir ));
				if ( is_array( $folders ) ) {
					foreach ( $folders as $fld ) {
						if ( substr( $fld, 0, 1 ) == '.' ) {
							continue;
						}
						$fld = basename( $fld );
						$list[ $hash ][ $fld ] = ucfirst( str_replace( '_', ' ', $fld ) );
					}
				}
				if ( ! isset( $list[ $hash ]['none'] ) ) {
					$list[ $hash ] = trx_addons_array_merge( array( 'none' => '' ), $list[ $hash ] );
				}
			}
		}
		return $list[ $hash ];
	}
}

if ( ! function_exists( 'trx_addons_get_list_thumbnail_sizes' ) ){
	/**
	 * Return list of thumbnail sizes
	 *
	 * @return array  list of thumbnail sizes
	 */
	function trx_addons_get_list_thumbnail_sizes() {
		$list = array();
		$thumbnails = get_intermediate_image_sizes();
		$list['full'] = esc_html__( 'Full size', 'trx_addons' );
		foreach ( $thumbnails as $thumbnail ) {
			if ( ! empty( $GLOBALS['_wp_additional_image_sizes'][ $thumbnail ] ) ){
				$width = $GLOBALS['_wp_additional_image_sizes'][ $thumbnail ]['width'];
				$height = $GLOBALS['_wp_additional_image_sizes'][ $thumbnail ]['height'];
			} else {
				$width = get_option( $thumbnail . '_size_w', '' );
				$height = get_option( $thumbnail . '_size_h', '' );
			}
			$list[ $thumbnail ] = $thumbnail . ' (' . $width . 'x' . $height . ')';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_input_hover' ) ) {
	/**
	 * Return list of the input field's hover effects
	 * 
	 * @trigger trx_addons_filter_get_list_input_hover
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * 
	 * @return array List of hover effects
	 */
	function trx_addons_get_list_input_hover( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_get_list_input_hover', array(
			'default'	=> esc_html__( 'Default',	'trx_addons' ),
			'accent'	=> esc_html__( 'Accented',	'trx_addons' ),
			'path'		=> esc_html__( 'Path',		'trx_addons' ),
			'jump'		=> esc_html__( 'Jump',		'trx_addons' ),
			'underline'	=> esc_html__( 'Underline',	'trx_addons' ),
			'iconed'	=> esc_html__( 'Iconed',	'trx_addons' ),
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_menu_hover' ) ) {
	/**
	 * Return list of the menu hover effects
	 * 
	 * @trigger trx_addons_filter_get_list_menu_hover
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * 
	 * @return array List of hover effects
	 */
	function trx_addons_get_list_menu_hover( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_get_list_menu_hover', array(
			'fade'			=> esc_html__( 'Fade',		'trx_addons' ),
			'fade_box'		=> esc_html__( 'Fade Box',	'trx_addons' ),
			'slide_box'		=> esc_html__( 'Slide Box',	'trx_addons' ),
			'slide_line'	=> esc_html__( 'Slide Line','trx_addons' ),
			'color_line'	=> esc_html__( 'Color Line','trx_addons' ),
			'zoom_line'		=> esc_html__( 'Zoom Line',	'trx_addons' ),
			'path_line'		=> esc_html__( 'Path Line',	'trx_addons' ),
			'roll_down'		=> esc_html__( 'Roll Down',	'trx_addons' ),
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_animations_in' ) ) {
	/**
	 * Return list of the in animations (enter effects)
	 * 
	 * @trigger trx_addons_filter_get_list_animations_in
	 *
	 * @param boolean $prepend_inherit  If true - add value 'inherit' in the beginning
	 * @param string $none_key          Key for the 'none' value
	 * 
	 * @return array List of in animations
	 */
	function trx_addons_get_list_animations_in( $prepend_inherit = false, $none_key = 'none' ) {
		$list = apply_filters( 'trx_addons_filter_get_list_animations_in', array(
			$none_key 			=> trx_addons_get_not_selected_text( esc_html__( 'None', 'trx_addons' ) ),
			'bounceIn'			=> esc_html__( 'Bounce In',			'trx_addons' ),
			'bounceInUp'		=> esc_html__( 'Bounce In Up',		'trx_addons' ),
			'bounceInDown'		=> esc_html__( 'Bounce In Down',	'trx_addons' ),
			'bounceInLeft'		=> esc_html__( 'Bounce In Left',	'trx_addons' ),
			'bounceInRight'		=> esc_html__( 'Bounce In Right',	'trx_addons' ),
			'elastic'			=> esc_html__( 'Elastic In',		'trx_addons' ),
			'fadeIn'			=> esc_html__( 'Fade In',			'trx_addons' ),
			'fadeInUp'			=> esc_html__( 'Fade In Up',		'trx_addons' ),
			'fadeInUpSmall'		=> esc_html__( 'Fade In Up Small',	'trx_addons' ),
			'fadeInUpBig'		=> esc_html__( 'Fade In Up Big',	'trx_addons' ),
			'fadeInDown'		=> esc_html__( 'Fade In Down',		'trx_addons' ),
			'fadeInDownBig'		=> esc_html__( 'Fade In Down Big',	'trx_addons' ),
			'fadeInLeft'		=> esc_html__( 'Fade In Left',		'trx_addons' ),
			'fadeInLeftBig'		=> esc_html__( 'Fade In Left Big',	'trx_addons' ),
			'fadeInRight'		=> esc_html__( 'Fade In Right',		'trx_addons' ),
			'fadeInRightBig'	=> esc_html__( 'Fade In Right Big',	'trx_addons' ),
			'flipInX'			=> esc_html__( 'Flip In X',			'trx_addons' ),
			'flipInY'			=> esc_html__( 'Flip In Y',			'trx_addons' ),
			'lightSpeedIn'		=> esc_html__( 'Light Speed In',	'trx_addons' ),
			'rotateIn'			=> esc_html__( 'Rotate In',			'trx_addons' ),
			'rotateInUpLeft'	=> esc_html__( 'Rotate In Down Left','trx_addons' ),
			'rotateInUpRight'	=> esc_html__( 'Rotate In Up Right','trx_addons' ),
			'rotateInDownLeft'	=> esc_html__( 'Rotate In Up Left',	'trx_addons' ),
			'rotateInDownRight'	=> esc_html__( 'Rotate In Down Right','trx_addons' ),
			'rollIn'			=> esc_html__( 'Roll In',			'trx_addons' ),
			'slideInUp'			=> esc_html__( 'Slide In Up',		'trx_addons' ),
			'slideInDown'		=> esc_html__( 'Slide In Down',		'trx_addons' ),
			'slideInLeft'		=> esc_html__( 'Slide In Left',		'trx_addons' ),
			'slideInRight'		=> esc_html__( 'Slide In Right',	'trx_addons' ),
			'wipeInLeftTop'		=> esc_html__( 'Wipe In Left Top',	'trx_addons' ),
			'zoomIn'			=> esc_html__( 'Zoom In',			'trx_addons' ),
			'zoomInUp'			=> esc_html__( 'Zoom In Up',		'trx_addons' ),
			'zoomInDown'		=> esc_html__( 'Zoom In Down',		'trx_addons' ),
			'zoomInLeft'		=> esc_html__( 'Zoom In Left',		'trx_addons' ),
			'zoomInRight'		=> esc_html__( 'Zoom In Right',		'trx_addons' ),
/*
			'shake'				=> esc_html__( 'Shake',				'trx_addons' ),
			'headShake'			=> esc_html__( 'Head Shake',		'trx_addons' ),
			'jello'				=> esc_html__( 'Jello',				'trx_addons' ),
			'heartBeat'			=> esc_html__( 'Heart Beat',		'trx_addons' ),
			'jackInTheBox'		=> esc_html__( 'Jack In The Box',	'trx_addons' ),
*/
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_animations_out' ) ) {
	/**
	 * Return list of the out animations
	 * 
	 * @trigger trx_addons_filter_get_list_animations_out
	 * 
	 * @param boolean $prepend_inherit  If true - add in the begginig inherit value
	 * @param string $none_key          Key for the 'none' value
	 * 
	 * @return array  List of the out animations
	 */
	function trx_addons_get_list_animations_out( $prepend_inherit = false, $none_key = 'none' ) {
		$list = apply_filters( 'trx_addons_filter_get_list_animations_out', array(
			$none_key			=> trx_addons_get_not_selected_text( esc_html__( 'None', 'trx_addons' ) ),
			'bounceOut'			=> esc_html__( 'Bounce Out',			'trx_addons' ),
			'bounceOutUp'		=> esc_html__( 'Bounce Out Up',			'trx_addons' ),
			'bounceOutDown'		=> esc_html__( 'Bounce Out Down',		'trx_addons' ),
			'bounceOutLeft'		=> esc_html__( 'Bounce Out Left',		'trx_addons' ),
			'bounceOutRight'	=> esc_html__( 'Bounce Out Right',		'trx_addons' ),
			'fadeOut'			=> esc_html__( 'Fade Out',				'trx_addons' ),
			'fadeOutUp'			=> esc_html__( 'Fade Out Up',			'trx_addons' ),
			'fadeOutUpBig'		=> esc_html__( 'Fade Out Up Big',		'trx_addons' ),
			'fadeOutDownSmall'	=> esc_html__( 'Fade Out Down Small',	'trx_addons' ),
			'fadeOutDownBig'	=> esc_html__( 'Fade Out Down Big',		'trx_addons' ),
			'fadeOutDown'		=> esc_html__( 'Fade Out Down',			'trx_addons' ),
			'fadeOutLeft'		=> esc_html__( 'Fade Out Left',			'trx_addons' ),
			'fadeOutLeftBig'	=> esc_html__( 'Fade Out Left Big',		'trx_addons' ),
			'fadeOutRight'		=> esc_html__( 'Fade Out Right',		'trx_addons' ),
			'fadeOutRightBig'	=> esc_html__( 'Fade Out Right Big',	'trx_addons' ),
			'flipOutX'			=> esc_html__( 'Flip Out X',			'trx_addons' ),
			'flipOutY'			=> esc_html__( 'Flip Out Y',			'trx_addons' ),
			'hinge'				=> esc_html__( 'Hinge Out',				'trx_addons' ),
			'lightSpeedOut'		=> esc_html__( 'Light Speed Out',		'trx_addons' ),
			'rotateOut'			=> esc_html__( 'Rotate Out',			'trx_addons' ),
			'rotateOutUpLeft'	=> esc_html__( 'Rotate Out Down Left',	'trx_addons' ),
			'rotateOutUpRight'	=> esc_html__( 'Rotate Out Up Right',	'trx_addons' ),
			'rotateOutDownLeft'	=> esc_html__( 'Rotate Out Up Left',	'trx_addons' ),
			'rotateOutDownRight'=> esc_html__( 'Rotate Out Down Right',	'trx_addons' ),
			'rollOut'			=> esc_html__( 'Roll Out',				'trx_addons' ),
			'slideOutUp'		=> esc_html__( 'Slide Out Up',			'trx_addons' ),
			'slideOutDown'		=> esc_html__( 'Slide Out Down',		'trx_addons' ),
			'slideOutLeft'		=> esc_html__( 'Slide Out Left',		'trx_addons' ),
			'slideOutRight'		=> esc_html__( 'Slide Out Right',		'trx_addons' ),
			'zoomOut'			=> esc_html__( 'Zoom Out',				'trx_addons' ),
			'zoomOutUp'			=> esc_html__( 'Zoom Out Up',			'trx_addons' ),
			'zoomOutDown'		=> esc_html__( 'Zoom Out Down',			'trx_addons' ),
			'zoomOutLeft'		=> esc_html__( 'Zoom Out Left',			'trx_addons' ),
			'zoomOutRight'		=> esc_html__( 'Zoom Out Right',		'trx_addons' )
		));
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_animation_classes' ) ) {
	/**
	 * Return classes list for the specified animation
	 * 
	 * @param string $animation  Animation name
	 * @param string $speed      Animation speed
	 * @param string $loop       Animation loop
	 * 
	 * @return string  		Classes list
	 */
	function trx_addons_get_animation_classes( $animation, $speed = 'normal', $loop = 'none' ) {
		// speed:	fast=0.5s | normal=1s | slow=2s
		// loop:	none | infinite
		return trx_addons_is_off( $animation )
					? '' 
					: 'animated ' . esc_attr( $animation )
								. ' ' . esc_attr( $speed )
								. ( ! trx_addons_is_off( $loop ) ? ' ' . esc_attr( $loop ) : '' );
	}
}

if ( ! function_exists( 'trx_addons_add_blog_animation' ) ) {
	/**
	 * Add (output) parameter data-post-animation for the posts archive or shortcode output
	 *
	 * @param string $sc		Shortcode name
	 * @param array $args		Shortcode attributes
	 */
	function trx_addons_add_blog_animation( $sc, $args = array() ) {
		$animation = '';
		if ( ! empty( $args['animation'] ) ) {
			$animation = $args['animation'];
		} else if ( ! trx_addons_sc_stack_check( "trx_sc_{$sc}" ) && trx_addons_check_option( $sc . '_blog_animation' ) ) {
			$animation = trx_addons_get_option( $sc . '_blog_animation' );
		}		
		if ( ! trx_addons_is_off( $animation ) && empty( $args['slider'] ) ) {
			echo ' data-post-animation="' . esc_attr( trx_addons_get_animation_classes( $animation ) ) . '"';
		}
	}
}

if ( ! function_exists( 'trx_addons_get_list_ease' ) ) {
	/**
	 * Return list of the easing effects
	 * 
	 * @trigger trx_addons_filter_get_list_ease
	 * 
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * 
	 * @return array  List of easing effects
	 */
	function trx_addons_get_list_ease( $prepend_inherit = false ) {
		$list = apply_filters('trx_addons_filter_get_list_ease', array(
			'linear'  => esc_html__( 'Linear', 'trx_addons' ),
			'power1'  => esc_html__( 'Power1', 'trx_addons' ),
			'power2'  => esc_html__( 'Power2', 'trx_addons' ),
			'power3'  => esc_html__( 'Power3', 'trx_addons' ),
			'power4'  => esc_html__( 'Power4', 'trx_addons' ),
			'back'    => esc_html__( 'Back', 'trx_addons' ),
			'elastic' => esc_html__( 'Elastic', 'trx_addons' ),
			'bounce'  => esc_html__( 'Bounce', 'trx_addons' ),
			'rough'   => esc_html__( 'Rough', 'trx_addons' ),
			'slowmo'  => esc_html__( 'Slowmo', 'trx_addons' ),
			'stepped' => esc_html__( 'Stepped', 'trx_addons' ),
			'circ'    => esc_html__( 'Circ', 'trx_addons' ),
			'expo'    => esc_html__( 'Expo', 'trx_addons' ),
			'sine'    => esc_html__( 'Sine', 'trx_addons' )
		));
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_menus' ) ) {
	/**
	 * Return list of the menus
	 * 
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * 
	 * @return array  List of menus
	 */
	function trx_addons_get_list_menus( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$list['none'] = trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) );
			$menus = wp_get_nav_menus();
			if ( is_array( $menus ) && count( $menus ) > 0 ) {
				foreach ( $menus as $menu ) {
					$list[ $menu->slug ] = $menu->name;
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_menu_locations' ) ) {
	/**
	 * Return list of the menu locations
	 * 
	 * @trigger trx_addons_filter_get_list_menu_locations
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 *
	 * @return array  List of menu locations
	 */
	function trx_addons_get_list_menu_locations( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			$list['none'] = trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) );
			$menus = get_registered_nav_menus();
			if ( is_array( $menus ) ) {
				foreach ( $menus as $location => $description )
					$list[ $location ] = $description;
			}
			$list = apply_filters( 'trx_addons_filter_menu_locations', $list );
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_submenu_styles' ) ) {
	/**
	 * Return list of the submenu styles
	 * 
	 * @trigger trx_addons_filter_submenu_styles
	 * 
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * 
	 * @return array  List of submenu styles
	 */
	function trx_addons_get_list_sc_submenu_styles( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_submenu_styles', array(
			'popup'    => esc_html__( 'Popup', 'trx_addons' ),
			'dropdown' => esc_html__( 'Dropdown', 'trx_addons' ),
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list ) 
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sidebars' ) ) {
	/**
	 * Return list of the registered sidebars
	 * 
	 * @trigger trx_addons_filter_get_list_sidebars
	 * 
	 * @param boolean $prepend_inherit  If true - add value 'inherit' in the beginning
	 * @param boolean $add_hide         If true - add value 'hide' in the beginning
	 * 
	 * @return array  List of sidebars
	 */
	function trx_addons_get_list_sidebars( $prepend_inherit = false, $add_hide = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			global $wp_registered_sidebars;
			if ( is_array( $wp_registered_sidebars ) ) {
				foreach ( $wp_registered_sidebars as $k => $v ) {
					$list[ $v['id'] ] = $v['name'];
				}
			}
			$list = apply_filters( 'trx_addons_filter_sidebars', $list );
		}
		if ( $add_hide ) {
			$list = trx_addons_array_merge( array( 'hide' => trx_addons_get_not_selected_text( esc_html__( 'Select widgets', 'trx_addons' ) ) ), $list );
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_background_positions' ) ) {
	/**
	 * Return list of the background positions
	 * 
	 * @trigger trx_addons_filter_get_list_background_positions
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * @param boolean $add_empty       If true - add value 'empty' in the beginning
	 * @param boolean $add_custom      If true - add value 'custom' in the beginning
	 *
	 * @return array  List of background positions
	 */
	function trx_addons_get_list_background_positions( $prepend_inherit = false, $add_empty = true, $add_custom = false ) {
		$list = apply_filters('trx_addons_filter_get_list_background_positions', array(
			'top left' => esc_html__( 'Top Left', 'trx_addons' ),
			'top center' => esc_html__( 'Top Center', 'trx_addons' ),
			'top right' => esc_html__( 'Top Right', 'trx_addons' ),
			'center left' => esc_html__( 'Center Left', 'trx_addons' ),
			'center center' => esc_html__( 'Center Center', 'trx_addons' ),
			'center right' => esc_html__( 'Center Right', 'trx_addons' ),
			'bottom left' => esc_html__( 'Bottom Left', 'trx_addons' ),
			'bottom center' => esc_html__( 'Bottom Center', 'trx_addons' ),
			'bottom right' => esc_html__( 'Bottom Right', 'trx_addons' ),
		) );
		if ( $add_empty ) {
			$list = trx_addons_array_merge( array( '' => esc_html__( "Default", 'trx_addons' ) ), $list );
		}
		if ( $add_custom ) {
			$list = trx_addons_array_merge( array( 'initial' => esc_html__( "Custom", 'trx_addons' ) ), $list );
		}
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_background_repeats' ) ) {
	/**
	 * Return list of the background repeats
	 * 
	 * @trigger trx_addons_filter_get_list_background_repeats
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 *
	 * @return array  List of background repeats
	 */
	function trx_addons_get_list_background_repeats( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_get_list_background_repeats', array(
			'no-repeat' => esc_html__( 'No repeat', 'trx_addons' ),
			'repeat'    => esc_html__( 'Repeat', 'trx_addons' ),
			'repeat-x'  => esc_html__( 'Repeat X', 'trx_addons' ),
			'repeat-y'  => esc_html__( 'Repeat Y', 'trx_addons' ),
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_background_sizes' ) ) {
	/**
	 * Return list of the background sizes
	 * 
	 * @trigger trx_addons_filter_get_list_background_sizes
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 *
	 * @return array  List of background sizes
	 */
	function trx_addons_get_list_background_sizes( $prepend_inherit = false ) {
		$list = apply_filters( 'trx_addons_filter_get_list_background_sizes', array(
			'contain' => esc_html__( 'Contain', 'trx_addons' ),
			'cover'  => esc_html__( 'Cover', 'trx_addons' ),
			'unset' => esc_html__( 'Normal', 'trx_addons' ),
		) );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_show_hide' ) ) {
	/**
	 * Return list of the show/hide states
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * @param boolean $numeric         If true - return numeric values
	 *
	 * @return array  List of show/hide states
	 */
	function trx_addons_get_list_show_hide( $prepend_inherit = false, $numeric = false ) {
		$list = array(
			( $numeric ? 1 : 'show' ) => esc_html__( 'Show', 'trx_addons' ),
			( $numeric ? 0 : 'hide' ) => esc_html__( 'Hide', 'trx_addons' )
		);
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}



// Lists for shortcode's parameters
//-------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_get_list_sc_aligns' ) ) {
	/**
	 * Return list of the alignments
	 * 
	 * @trigger trx_addons_filter_get_list_sc_aligns
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * @param boolean $add_none        If true - add value 'none' in the beginning
	 * @param boolean $add_justify     If true - add value 'justify' in the end
	 *
	 * @return array  List of alignments
	 */
	function trx_addons_get_list_sc_aligns( $prepend_inherit = false, $add_none = true, $add_justify = false ) {
		$list = array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'center' => esc_html__( 'Center', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' )
		);
		if ( $add_none ) {
			$list = trx_addons_array_merge( array( 'none' => esc_html__( "Default", 'trx_addons' ) ), $list );
		}
		if ( $add_justify ) {
			$list['justify'] = esc_html__("Justify", 'trx_addons' );
		}
		$list = apply_filters( 'trx_addons_filter_get_list_sc_aligns', $list );
		return $prepend_inherit
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_aligns_for_elementor' ) ) {
	/**
	 * Return list of the alignments for Elementor
	 * 
	 * @trigger trx_addons_filter_get_list_sc_aligns_for_elementor
	 *
	 * @param boolean $add_justify     If true - add value 'justify' in the end
	 *
	 * @return array  List of alignments
	 */
	function trx_addons_get_list_sc_aligns_for_elementor( $add_justify = false ) {
		$list = array(
			'left' => array(
				'title' => esc_html__( 'Left', 'trx_addons' ),
				'icon' => 'eicon-text-align-left',
			),
			'center' => array(
				'title' => esc_html__( 'Center', 'trx_addons' ),
				'icon' => 'eicon-text-align-center',
			),
			'right' => array(
				'title' => esc_html__( 'Right', 'trx_addons' ),
				'icon' => 'eicon-text-align-right',
			)
		);
		if ( $add_justify ) {
			$list['justify'] = array(
				'title' => esc_html__( 'Justified', 'trx_addons' ),
				'icon' => 'eicon-text-align-justify',
			);
		}
		$list = apply_filters( 'trx_addons_filter_get_list_sc_aligns_for_elementor', $list );
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_flex_aligns' ) ) {
	/**
	 * Return list of the flex alignments
	 * 
	 * @trigger trx_addons_filter_get_list_sc_flex_aligns
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * @param boolean $add_none        If true - add value 'none' in the beginning
	 * @param boolean $add_justify     If true - add value 'justify' in the end
	 *
	 * @return array  List of flex alignments
	 */
	function trx_addons_get_list_sc_flex_aligns( $prepend_inherit = false, $add_none = true, $add_justify = false ) {
		$list = array(
			'flex-start' => esc_html__( 'Left', 'trx_addons' ),
			'center'     => esc_html__( 'Center', 'trx_addons' ),
			'flex-end'   => esc_html__( 'Right', 'trx_addons' )
		);
		if ( $add_none ) {
			$list = trx_addons_array_merge( array( 'none' => esc_html__( "Default", 'trx_addons' ) ), $list );
		}
		if ( $add_justify ) {
			$list['justify'] = esc_html__("Justify", 'trx_addons' );
		}
		$list = apply_filters( 'trx_addons_filter_get_list_sc_flex_aligns', $list );
		return $prepend_inherit
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_flex_aligns_for_elementor' ) ) {
	/**
	 * Return list of the flex alignments for Elementor
	 * 
	 * @trigger trx_addons_filter_get_list_sc_flex_aligns_for_elementor
	 *
	 * @param boolean $add_justify     If true - add value 'justify' in the end
	 *
	 * @return array  List of flex alignments
	 */
	function trx_addons_get_list_sc_flex_aligns_for_elementor( $add_justify = false ) {
		$list = array(
			'flex-start' => array(
				'title' => esc_html__( 'Left', 'trx_addons' ),
				'icon' => 'eicon-text-align-left',
			),
			'center' => array(
				'title' => esc_html__( 'Center', 'trx_addons' ),
				'icon' => 'eicon-text-align-center',
			),
			'flex-end' => array(
				'title' => esc_html__( 'Right', 'trx_addons' ),
				'icon' => 'eicon-text-align-right',
			)
		);
		if ( $add_justify ) {
			$list['justify'] = array(
				'title' => esc_html__( 'Justified', 'trx_addons' ),
				'icon' => 'eicon-text-align-justify',
			);
		}
		$list = apply_filters( 'trx_addons_filter_get_list_sc_flex_aligns_for_elementor', $list );
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_floats' ) ) {
	/**
	 * Return list of the floats
	 * 
	 * @trigger trx_addons_filter_get_list_sc_floats
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 * @param boolean $add_none        If true - add value 'none' in the beginning
	 *
	 * @return array  List of floats
	 */
	function trx_addons_get_list_sc_floats( $prepend_inherit = false, $add_none = true ) {
		$list = array(
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' )
		);
		if ( $add_none ) {
			$list = trx_addons_array_merge( array( 'none' => esc_html__( "None", 'trx_addons' ) ), $list );
		}
		$list = apply_filters( 'trx_addons_filter_get_list_sc_floats', $list );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_tabs_positions' ) ) {
	/**
	 * Return list of the tabs positions
	 * 
	 * @trigger trx_addons_filter_get_list_sc_tabs_positions
	 *
	 * @param boolean $prepend_inherit If true - add value 'inherit' in the beginning
	 *
	 * @return array  List of tabs positions
	 */
	function trx_addons_get_list_sc_tabs_positions( $prepend_inherit = false ) {
		$list = array(
			'top'  => esc_html__( 'Top', 'trx_addons' ),
			'left' => esc_html__( 'Left', 'trx_addons' ),
		);
		$list = apply_filters( 'trx_addons_filter_get_list_sc_tabs_positions', $list );
		return $prepend_inherit 
					? trx_addons_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'trx_addons' ) ), $list )
					: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_paginations' ) ) {
	/**
	 * Return list of the pagination types
	 * 
	 * @trigger trx_addons_filter_get_list_sc_paginations
	 *
	 * @param boolean $none_key If true - add value 'none' in the beginning
	 *
	 * @return array  List of pagination types
	 */
	function trx_addons_get_list_sc_paginations( $none_key = 'none' ) {
		$list = array(
			'prev_next'		=> esc_html__( 'Previous / Next', 'trx_addons' ),
			'pages'			=> esc_html__( 'Page numbers', 'trx_addons' ),
			'advanced_pages'=> esc_html__( 'Advanced page numbers', 'trx_addons' ),
			'load_more'		=> esc_html__( 'Load more', 'trx_addons' ),
			'infinite'		=> esc_html__( 'Infinite scroll', 'trx_addons' ),
		);
		if ( ! empty( $none_key ) ) {
			$list = array_merge( array( $none_key => esc_html__( 'None', 'trx_addons' ) ), $list );
		}
		return apply_filters( 'trx_addons_filter_get_list_sc_paginations', $list );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_title_tags' ) ) {
	/**
	 * Return list of the title tags
	 * 
	 * @trigger trx_addons_filter_get_list_sc_title_tags
	 *
	 * @param boolean $none_key If true - add value 'none' in the beginning. Default - 'none'
	 * @param boolean $extended If true - add tags 'div', 'span' and 'p' in the end of the list
	 *
	 * @return array  List of title tags
	 */
	function trx_addons_get_list_sc_title_tags( $none_key = 'none', $extended = false ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_title_tags', array_merge(
			( ! empty( $none_key )
				? array( $none_key => esc_html__( 'Default', 'trx_addons' ) )
				: array()
			),
			array(
				'h1' => esc_html__( 'Heading 1', 'trx_addons' ),
				'h2' => esc_html__( 'Heading 2', 'trx_addons' ),
				'h3' => esc_html__( 'Heading 3', 'trx_addons' ),
				'h4' => esc_html__( 'Heading 4', 'trx_addons' ),
				'h5' => esc_html__( 'Heading 5', 'trx_addons' ),
				'h6' => esc_html__( 'Heading 6', 'trx_addons' )
			),
			( $extended
				? array(
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
					)
				: array()
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_subtitle_positions' ) ) {
	/**
	 * Return list of the subtitle positions relative to the title
	 * 
	 * @trigger trx_addons_filter_get_list_sc_subtitle_positions
	 * 
	 * @return array  List of subtitle positions
	 */
	function trx_addons_get_list_sc_subtitle_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_subtitle_positions', array(
				'above' => esc_html__( 'Above title', 'trx_addons' ),
				'below' => esc_html__( 'Below title', 'trx_addons' ),
			) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_title_gradient_fills' ) ) {
	/**
	 * Return list of the title gradient fill types
	 * 
	 * @trigger trx_addons_filter_get_list_sc_title_gradient_fills
	 *
	 * @return array  List of title gradient fill types
	 */
	function trx_addons_get_list_sc_title_gradient_fills() {
		return apply_filters('trx_addons_filter_get_list_sc_title_gradient_fills', array(
				'block'  => esc_html__( 'Block', 'trx_addons' ),
				'inline' => esc_html__( 'Inline', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_share_types' ) ) {
	/**
	 * Return list of the share types (dropdown, list, block)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_share_types
	 * 
	 * @return array  List of share types
	 */
	function trx_addons_get_list_sc_share_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_share_types', array(
				'drop'  => __( 'Dropdown list', 'trx_addons' ),
				'list'  => __( 'Small icons', 'trx_addons' ),
				'block' => __( 'Large icons', 'trx_addons' ),
			) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_show_on' ) ) {
	/**
	 * Return list of the show on types (scroll, permanent)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_show_on
	 * 
	 * @return array  List of show on types
	 */
	function trx_addons_get_list_sc_show_on() {
		return apply_filters( 'trx_addons_filter_get_list_sc_show_on', array(
				'permanent' => __( 'Show always', 'trx_addons' ),
				'scroll'    => __( 'Scroll to viewport', 'trx_addons' ),
			) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_supertitle_item_types' ) ) {
	/**
	 * Return list of the supertitle item types (text, media, icon)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_supertitle_item_types
	 * 
	 * @return array  List of supertitle item types
	 */
	function trx_addons_get_list_sc_supertitle_item_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_supertitle_item_types', array(
				'text'  => __( 'Text', 'trx_addons' ),
				'media' => __( 'Media', 'trx_addons' ),
				'icon'  => __( 'Icon', 'trx_addons' ),
			) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_cover_places' ) ) {
	/**
	 * Return list of the cover places: row, column, parent, second-level parent, third-level parent
	 * 
	 * @trigger trx_addons_filter_get_list_sc_cover_places
	 *
	 * @return array  List of cover places
	 */
	function trx_addons_get_list_sc_cover_places() {
		return apply_filters( 'trx_addons_filter_get_list_sc_cover_places', array(
			'row'	 => esc_html__( 'Closest row', 'trx_addons' ),
			'column' => esc_html__( 'Closest column', 'trx_addons' ),
			'1' 	 => esc_html__( 'Parent', 'trx_addons' ),
			'2'		 => esc_html__( 'Second-level parent', 'trx_addons' ),
			'3'		 => esc_html__( 'Third-level parent', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_engines' ) ) {
	/**
	 * Return list of the slider engines: swiper, elastistack
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_engines
	 * 
	 * @return array  List of slider engines
	 */
	function trx_addons_get_list_sc_slider_engines() {
		$list = array(
			"swiper" 	  => esc_html__( "Posts slider (Swiper)", 'trx_addons' ),
			"elastistack" => esc_html__( "Posts slider (ElastiStack)", 'trx_addons' )
		);
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_engines', $list );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_controls' ) ) {
	/**
	 * Return list of the slider controls positions: side, outside, top, bottom
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_controls
	 * 
	 * @param string $none_key  Key for the 'none' value. If empty - don't add 'none' to the list. Default: 'none'
	 * 
	 * @return array  List of slider controls positions
	 */
	function trx_addons_get_list_sc_slider_controls( $none_key = 'none' ) {
		$list = array(
			'side'		=> esc_html__( 'Side', 'trx_addons' ),
			'outside'	=> esc_html__( 'Outside', 'trx_addons' ),
			'top'		=> esc_html__( 'Top', 'trx_addons' ),
			'bottom'	=> esc_html__( 'Bottom', 'trx_addons' )
		);
		if ( ! empty( $none_key ) ) {
			$list = array_merge( array( $none_key => esc_html__( 'None', 'trx_addons' ) ), $list );
		}
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_controls', $list );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_paginations' ) ) {
	/**
	 * Return list of the slider pagination positions: left, right, bottom, bottom_outside
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_paginations
	 * 
	 * @param string $none_key  Key for the 'none' value. If empty - don't add 'none' to the list. Default: 'none'
	 * @param bool   $bottom_outside  Add 'bottom_outside' item to the list. Default: true
	 * 
	 * @return array  List of slider pagination positions
	 */
	function trx_addons_get_list_sc_slider_paginations( $none_key = 'none', $bottom_outside = true ) {
		$list = array(
			'left'		=> esc_html__( 'Left', 'trx_addons' ),
			'right'		=> esc_html__( 'Right', 'trx_addons' ),
			'bottom'	=> esc_html__( 'Bottom', 'trx_addons' )
		);
		if ( ! empty( $none_key ) ) {
			$list = array_merge( array( $none_key => esc_html__( 'None', 'trx_addons' ) ), $list );
		}
		if ( $bottom_outside ) {
			$list['bottom_outside'] = esc_html__( 'Bottom Outside', 'trx_addons' );
		}
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_paginations', $list );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_paginations_types' ) ) {
	/**
	 * Return list of the slider pagination types: bullets, fraction, progressbar
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_paginations_types
	 * 
	 * @return array  List of slider pagination types
	 */
	function trx_addons_get_list_sc_slider_paginations_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_paginations_types', array(
			'bullets'     => esc_html__( 'Bullets', 'trx_addons' ),
			'fraction'    => esc_html__( 'Fraction (slide numbers)', 'trx_addons' ),
			'progressbar' => esc_html__( 'Progress bar', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_controls_paginations_types' ) ) {
	/**
	 * Return list of pagination types for the shortcode "Slider Controls": none, thumbs, bullets, fraction, progressbar
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_controls_paginations_types
	 * 
	 * @return array  List of slider controls pagination types
	 */
	function trx_addons_get_list_sc_slider_controls_paginations_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_controls_paginations_types', array_merge(
						array(
							'none' => esc_html__( 'Hide pagination', 'trx_addons' ),
							'thumbs' => esc_html__( 'Slides thumbnails', 'trx_addons' )
						),
						trx_addons_get_list_sc_slider_paginations_types()
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_titles' ) ) {
	/**
	 * Return list of the slider titles positions: no, center, bottom, bottom_left, bottom_right, outside
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_titles
	 *
	 * @return array  List of slider titles positions
	 */
	function trx_addons_get_list_sc_slider_titles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_titles', array(
			'no'      => esc_html__( 'No titles', 'trx_addons' ),
			'center'  => esc_html__( 'Center', 'trx_addons' ),
			'bottom'  => esc_html__( 'Bottom Center', 'trx_addons' ),
			'lb'      => esc_html__( 'Bottom Left', 'trx_addons' ),
			'rb'      => esc_html__( 'Bottom Right', 'trx_addons' ),
			'outside' => esc_html__( 'Outside Bottom', 'trx_addons' ),
			'outside_top' => esc_html__( 'Outside Top', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_effects' ) ) {
	/**
	 * Return list of the slider effects: slide, swap, fade, cube, flip, coverflow
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_effects
	 *
	 * @return array  List of slider effects
	 */
	function trx_addons_get_list_sc_slider_effects() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_effects', array(
			'slide' => esc_html__( 'Slide', 'trx_addons' ),
			'swap'  => esc_html__( 'Swap', 'trx_addons' ),
			'fade'  => esc_html__( 'Fade', 'trx_addons' ),
			'cube'  => esc_html__( 'Cube', 'trx_addons' ),
			'flip'  => esc_html__( 'Flip', 'trx_addons' ),
			'coverflow' => esc_html__( 'Coverflow', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_toc_styles' ) ) {
	/**
	 * Return list of the slider TOC's styles
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_toc_styles
	 *
	 * @return array  List of slider TOC's styles
	 */
	function trx_addons_get_list_sc_slider_toc_styles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_toc_styles', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_toc_positions' ) ) {
	/**
	 * Return list of the slider TOC's positions
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_toc_positions
	 *
	 * @return array  List of slider TOC's positions
	 */
	function trx_addons_get_list_sc_slider_toc_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_toc_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_controller_styles' ) ) {
	/**
	 * Return list of the slider controller styles
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_controller_styles
	 *
	 * @return array  List of slider controller styles
	 */
	function trx_addons_get_list_sc_slider_controller_styles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_controller_styles', array(
			'thumbs'        => esc_html__( 'Thumbs', 'trx_addons' ),
			'titles'        => esc_html__( 'Titles', 'trx_addons' ),
			'thumbs_titles' => esc_html__( 'Thumbs+Titles', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_controls_styles' ) ) {
	/**
	 * Return list of the slider controls styles
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_controls_styles
	 *
	 * @return array  List of slider controls styles
	 */
	function trx_addons_get_list_sc_slider_controls_styles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_controls_styles', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_slider_directions' ) ) {
	/**
	 * Return list of the slider directions to change slides: horizontal, vertical
	 * 
	 * @trigger trx_addons_filter_get_list_sc_slider_directions
	 *
	 * @return array  List of slider directions
	 */
	function trx_addons_get_list_sc_slider_directions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_slider_directions', trx_addons_get_list_sc_directions() );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_video_list_controller_styles' ) ) {
	/**
	 * Return list of the video controller's styles
	 * 
	 * @trigger trx_addons_filter_get_list_sc_video_list_controller_styles
	 * 
	 * @return array  List of video controller's styles
	 */
	function trx_addons_get_list_sc_video_list_controller_styles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_video_list_controller_styles', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_video_list_controller_positions' ) ) {
	/**
	 * Return list of the video controller's positions
	 * 
	 * @trigger trx_addons_filter_get_list_sc_video_list_controller_positions
	 * 
	 * @return array  List of video controller's positions
	 */
	function trx_addons_get_list_sc_video_list_controller_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_video_list_controller_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_query_orderby' ) ) {
	/**
	 * Return list of the query order by
	 * 
	 * @trigger trx_addons_filter_get_list_sc_query_orderby
	 *
	 * @param string $none_key    Key for the 'none' value. If empty - 'none' will be used
	 * @param array|string $keys  List of keys to return. Allowed values: 'none', 'ID', 'post_date', 'title', 'comments', 'likes', 'views', 'rand'
	 * 
	 * @return array  List of query order by
	 */
	function trx_addons_get_list_sc_query_orderby( $none_key = 'none', $keys = array( 'none', 'ID', 'post_date', 'title', 'comments', 'likes', 'views', 'rand' ) ) {
		$list = array();
		if ( ! is_array( $keys ) && strpos( $keys, ',' ) !== false ) {
			$keys = array_map( 'trim', explode( ',', $keys ) );
		}
		foreach ( $keys as $key ) {
			if ( $key == $none_key )
				$list[$key] = esc_html__( 'None', 'trx_addons' );
			else if ( $key == 'ID' || $key == 'post_id' )
				$list[$key] = esc_html__( 'Post ID', 'trx_addons' );
			else if ( $key == 'date' || $key == 'post_date' )
				$list[$key] = esc_html__( 'Date', 'trx_addons' );
			else if ( $key == 'update' || $key == 'post_update' || $key == 'post_modified' )
				$list[$key] = esc_html__( 'Update', 'trx_addons' );
			else if ( $key == 'title' || $key == 'post_title' )
				$list[$key] = esc_html__( 'Title', 'trx_addons' );
			else if ( $key == 'comments' )
				$list[$key] = esc_html__( 'Comments number', 'trx_addons' );
			else if ( $key == 'likes' )
				$list[$key] = esc_html__( 'Likes number', 'trx_addons' );
			else if ( $key == 'views' )
				$list[$key] = esc_html__( 'Views number', 'trx_addons' );
			else if ( $key == 'price' )
				$list[$key] = esc_html__( 'Price', 'trx_addons' );
			else if ( $key == 'rand' || $key == 'random' )
				$list['rand'] = esc_html__( 'Random', 'trx_addons' );
		}
		return apply_filters( 'trx_addons_filter_get_list_sc_query_orderby', $list, $keys );
	}
}

if ( ! function_exists( 'trx_addons_get_list_widget_query_orderby' ) ) {
	/**
	 * Return list of the orderby options for Popular posts widgets
	 * 
	 * @trigger trx_addons_filter_popular_posts_orderby
	 * 
	 * @return array  List of orderby options
	 */
	function trx_addons_get_list_widget_query_orderby() {
		return apply_filters( 'trx_addons_filter_popular_posts_orderby', array(
			'date'		=> __('Date published', 'trx_addons' ),
			'title'		=> __('Post title', 'trx_addons' ),
			'likes'		=> __('Likes number', 'trx_addons' ),
			'views'		=> __('Views number', 'trx_addons' ),
			'comments'	=> __('Comments number', 'trx_addons' ),
			'rand' 		=> __('Random', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_query_orders' ) ) {
	/**
	 * Return list of the query orders (ascending or descending)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_query_orders
	 * 
	 * @return array  List of query orders
	 */
	function trx_addons_get_list_sc_query_orders() {
		return apply_filters( 'trx_addons_filter_get_list_sc_query_orders', array(
			'desc' => esc_html__( 'Descending', 'trx_addons' ),
			'asc'  => esc_html__( 'Ascending', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_directions' ) ) {
	/**
	 * Return list of the directions (horizontal or vertical)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_directions
	 * 
	 * @return array  List of directions
	 */
	function trx_addons_get_list_sc_directions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_directions', array(
			'horizontal' => esc_html__( 'Horizontal', 'trx_addons' ),
			'vertical'   => esc_html__( 'Vertical', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_mouse_events' ) ) {
	/**
	 * Return list of the mouse events (drag or move)
	 * 
	 * @trigger trx_addons_filter_get_list_sc_mouse_events
	 * 
	 * @return array  List of mouse events
	 */
	function trx_addons_get_list_sc_mouse_events() {
		return apply_filters( 'trx_addons_filter_get_list_sc_mouse_events', array(
			'drag' => esc_html__( 'Drag', 'trx_addons' ),
			'move' => esc_html__( 'Move', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_positions' ) ) {
	/**
	 * Return list of the element positions as combination of horizontal and vertical sides.
	 * For example: 'tl' - Top Left, 'tc' - Top Center, 'tr' - Top Right,
	 *              'ml' - Middle Left, 'mc' - Middle Center, 'mr' - Middle Right,
	 *              'bl' - Bottom Left, 'bc' - Bottom Center, 'br' - Bottom Right
	 * 
	 * @trigger trx_addons_filter_get_list_sc_positions
	 *
	 * @return array  List of positions
	 */
	function trx_addons_get_list_sc_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_positions', array(
			'tl' => esc_html__( 'Top Left', 'trx_addons' ),
			'tc' => esc_html__( 'Top Center', 'trx_addons' ),
			'tr' => esc_html__( 'Top Right', 'trx_addons' ),
			'ml' => esc_html__( 'Middle Left', 'trx_addons' ),
			'mc' => esc_html__( 'Middle Center', 'trx_addons' ),
			'mr' => esc_html__( 'Middle Right', 'trx_addons' ),
			'bl' => esc_html__( 'Bottom Left', 'trx_addons' ),
			'bc' => esc_html__( 'Bottom Center', 'trx_addons' ),
			'br' => esc_html__( 'Bottom Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_blogger_image_positions' ) ) {
	/**
	 * Return list of the blogger image positions: top, left, right, alter
	 * 
	 * @trigger trx_addons_filter_get_list_sc_blogger_image_positions
	 *
	 * @return array  List of positions
	 */
	function trx_addons_get_list_sc_blogger_image_positions() {
		return apply_filters('trx_addons_filter_get_list_sc_blogger_image_positions', array(
			'top'   => esc_html__( 'Top', 'trx_addons' ),
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' ),
			'alter' => esc_html__( 'Alternation', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_image_ratio' ) ) {
	/**
	 * Return list of the image ratios
	 * 
	 * @trigger trx_addons_filter_get_list_sc_image_ratio
	 *
	 * @param bool $masonry Add 'masonry' ratio to the list
	 * @param bool $none Add 'none' ratio to the list
	 * 
	 * @return array  List of ratios
	 */
	function trx_addons_get_list_sc_image_ratio( $masonry = true, $none = true ) {
		$list = apply_filters( 'trx_addons_filter_get_list_sc_image_ratio', array(
			'none'    => esc_html__( 'Default', 'trx_addons' ),
			'masonry' => esc_html__( 'Masonry', 'trx_addons' ),
			'2:1'     => esc_html__( '2:1', 'trx_addons' ),
			'17:9'    => esc_html__( '17:9', 'trx_addons' ),
			'16:9'    => esc_html__( '16:9', 'trx_addons' ),
			'4:3'     => esc_html__( '4:3', 'trx_addons' ),
			'1:1'     => esc_html__( '1:1', 'trx_addons' ),
			'3:4'     => esc_html__( '3:4', 'trx_addons' ),
			'9:16'    => esc_html__( '9:16', 'trx_addons' ),
			'9:17'    => esc_html__( '9:17', 'trx_addons' ),
			'1:2'     => esc_html__( '1:2', 'trx_addons' ),
		) );
		if ( ! $masonry ) {
			unset( $list['masonry'] );
		}
		if ( ! $none ) {
			unset( $list['none'] );
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_image_hover' ) ) {
	/**
	 * Return list of the image hover styles
	 * 
	 * @trigger trx_addons_filter_get_list_sc_image_hover
	 *
	 * @return array  List of hover styles
	 */
	function trx_addons_get_list_sc_image_hover() {
		return apply_filters( 'trx_addons_filter_get_list_sc_image_hover', array(
			'inherit' => esc_html__( 'Inherit', 'trx_addons' ),
			'none'    => esc_html__( 'No hover', 'trx_addons' ),
			'info'    => esc_html__( 'Info', 'trx_addons' ),
			'links'   => esc_html__( 'Links', 'trx_addons' ),
			'plain'   => esc_html__( 'Plain', 'trx_addons' ),
			'wide'    => esc_html__( 'Wide', 'trx_addons' ),
			'zoomin'  => esc_html__( 'Zoom In', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_button_sizes' ) ) {
	/**
	 * Return list of the button sizes
	 * 
	 * @trigger trx_addons_filter_get_list_sc_button_sizes
	 *
	 * @return array  List of sizes
	 */
	function trx_addons_get_list_sc_button_sizes() {
		return apply_filters( 'trx_addons_filter_get_list_sc_button_sizes', array(
			'normal' => esc_html__( 'Normal', 'trx_addons' ),
			'small' => esc_html__( 'Small', 'trx_addons' ),
			'large' => esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_widths' ) ) {
	/**
	 * Return list of the content widths as part of the page width or in percents (if $with_percents = true)
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_widths
	 *
	 * @param string $none_key     Key for the 'none' value
	 * @param bool $with_percents  Add percents to the values
	 *
	 * @return array  List of widths
	 */
	function trx_addons_get_list_sc_content_widths( $none_key = 'none', $with_percents = true ) {
		$list = array(
			$none_key => esc_html__( 'Default', 'trx_addons' ),
			'1_1' => esc_html__( 'Full width', 'trx_addons' ),
			'1_2' => esc_html__( '1/2 of page', 'trx_addons' ),
			'1_3' => esc_html__( '1/3 of page', 'trx_addons' ),
			'2_3' => esc_html__( '2/3 of page', 'trx_addons' ),
			'1_4' => esc_html__( '1/4 of page', 'trx_addons' ),
			'3_4' => esc_html__( '3/4 of page', 'trx_addons' ),
		);
		if ( $with_percents ) {
			$list = array_merge( $list, array(
				'100p'=> esc_html__( '100% of container', 'trx_addons' ),
				'90p' => esc_html__( '90% of container', 'trx_addons' ),
				'80p' => esc_html__( '80% of container', 'trx_addons' ),
				'75p' => esc_html__( '75% of container', 'trx_addons' ),
				'70p' => esc_html__( '70% of container', 'trx_addons' ),
				'60p' => esc_html__( '60% of container', 'trx_addons' ),
				'50p' => esc_html__( '50% of container', 'trx_addons' ),
				'45p' => esc_html__( '45% of container', 'trx_addons' ),
				'40p' => esc_html__( '40% of container', 'trx_addons' ),
				'30p' => esc_html__( '30% of container', 'trx_addons' ),
				'25p' => esc_html__( '25% of container', 'trx_addons' ),
				'20p' => esc_html__( '20% of container', 'trx_addons' ),
				'15p' => esc_html__( '15% of container', 'trx_addons' ),
				'10p' => esc_html__( '10% of container', 'trx_addons' ),
			) );
		}
		return apply_filters( 'trx_addons_filter_get_list_sc_content_widths', $list );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_paddings_and_margins' ) ) {
	/**
	 * Return list of the content's paddings and margins sizes
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_paddings_and_margins
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of paddings and margins
	 */
	function trx_addons_get_list_sc_content_paddings_and_margins( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_content_paddings_and_margins', array(
			$none_key	=> esc_html__( 'None', 'trx_addons' ),
			'tiny'		=> esc_html__( 'Tiny', 'trx_addons' ),
			'small'		=> esc_html__( 'Small', 'trx_addons' ),
			'medium'	=> esc_html__( 'Medium', 'trx_addons' ),
			'large'		=> esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_push_and_pull' ) ) {
	/**
	 * Return list of the content's push and pull sizes
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_push_and_pull
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of push and pull
	 */
	function trx_addons_get_list_sc_content_push_and_pull( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_content_push_and_pull', array(
			$none_key => esc_html__( 'None', 'trx_addons' ),
			'tiny' => esc_html__( 'Tiny', 'trx_addons' ),
			'small' => esc_html__( 'Small', 'trx_addons' ),
			'medium' => esc_html__( 'Medium', 'trx_addons' ),
			'large' => esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_shift' ) ) {
	/**
	 * Return list of the content's shift sizes. Available keys: tiny, small, medium, large and negative values for each key
	 * (e.g. tiny, tiny_negative)
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_shift
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of shift
	 */
	function trx_addons_get_list_sc_content_shift($none_key = 'none') {
		return apply_filters( 'trx_addons_filter_get_list_sc_content_shift', array(
			$none_key => esc_html__( 'None', 'trx_addons' ),
			'tiny' => esc_html__( 'Tiny', 'trx_addons' ),
			'small' => esc_html__( 'Small', 'trx_addons' ),
			'medium' => esc_html__( 'Medium', 'trx_addons' ),
			'large' => esc_html__( 'Large', 'trx_addons' ),
			'tiny_negative' => esc_html__( 'Tiny (negative)', 'trx_addons' ),
			'small_negative' => esc_html__( 'Small (negative)', 'trx_addons' ),
			'medium_negative' => esc_html__( 'Medium (negative)', 'trx_addons' ),
			'large_negative' => esc_html__( 'Large (negative)', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_extra_bg' ) ) {
	/**
	 * Return list of the content's extra background sizes
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_extra_bg
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of extra background sizes
	 */
	function trx_addons_get_list_sc_content_extra_bg($none_key = 'none') {
		return apply_filters( 'trx_addons_filter_get_list_sc_content_extra_bg', array(
			$none_key => esc_html__( 'None', 'trx_addons' ),
			'tiny' => esc_html__( 'Tiny', 'trx_addons' ),
			'small' => esc_html__( 'Small', 'trx_addons' ),
			'medium' => esc_html__( 'Medium', 'trx_addons' ),
			'large' => esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_content_extra_bg_mask' ) ) {
	/**
	 * Return list of the background mask values to color tone of the image
	 *
	 * @trigger trx_addons_filter_get_list_sc_content_extra_bg_mask
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of extra background mask sizes
	 */
	function trx_addons_get_list_sc_content_extra_bg_mask( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_content_extra_bg_mask', array(
			$none_key  => esc_html__( 'None', 'trx_addons' ),
			'bg_color' => esc_html__( 'Use bg color', 'trx_addons' ),
			'1'  => esc_html__( '10%', 'trx_addons' ),
			'2'  => esc_html__( '20%', 'trx_addons' ),
			'3'  => esc_html__( '30%', 'trx_addons' ),
			'4'  => esc_html__( '40%', 'trx_addons' ),
			'5'  => esc_html__( '50%', 'trx_addons' ),
			'6'  => esc_html__( '60%', 'trx_addons' ),
			'7'  => esc_html__( '70%', 'trx_addons' ),
			'8'  => esc_html__( '80%', 'trx_addons' ),
			'9'  => esc_html__( '90%', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_empty_space_heights' ) ) {
	/**
	 * Return list of heights for Spacer and Divider
	 *
	 * @trigger trx_addons_filter_get_list_sc_empty_space_heights
	 *
	 * @param string $none_key     Key for the 'none' value
	 *
	 * @return array  List of empty space heights
	 */
	function trx_addons_get_list_sc_empty_space_heights( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_empty_space_heights', array(
					'tiny'    => esc_html__( 'Tiny', 'trx_addons' ),
					'small'   => esc_html__( 'Small', 'trx_addons' ),
					'medium'  => esc_html__( 'Medium', 'trx_addons' ),
					'large'   => esc_html__( 'Large', 'trx_addons' ),
					'huge'    => esc_html__( 'Huge', 'trx_addons' ),
					$none_key => esc_html__( 'From the value above', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_googlemap_styles' ) ) {
	/**
	 * Return list of the googlemap styles
	 * 
	 * @trigger trx_addons_filter_sc_googlemap_styles
	 * 
	 * @return array  List of googlemap styles
	 */
	function trx_addons_get_list_sc_googlemap_styles() {
		return apply_filters( 'trx_addons_filter_sc_googlemap_styles', array(
			'default'   => esc_html__( 'Default', 'trx_addons' ),
			'greyscale' => esc_html__( 'Greyscale', 'trx_addons' ),
			'inverse'   => esc_html__( 'Inverse', 'trx_addons' ),
			'simple'    => esc_html__( 'Simple', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_googlemap_animations' ) ) {
	/**
	 * Return list of the googlemap animations
	 *
	 * @trigger trx_addons_filter_sc_googlemap_animations
	 *
	 * @return array  List of googlemap animations
	 */
	function trx_addons_get_list_sc_googlemap_animations() {
		return apply_filters( 'trx_addons_filter_sc_googlemap_animations', array(
			'none'   => esc_html__( 'None', 'trx_addons' ),
			'drop'   => esc_html__( 'Drop', 'trx_addons' ),
			'bounce' => esc_html__( 'Bounce', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_osmap_tilers' ) ) {
	/**
	 * Return list of the osmap tilers: vector or raster
	 * 
	 * @trigger trx_addons_filter_sc_osmap_tilers
	 * 
	 * @return array  List of osmap tilers
	 */
	function trx_addons_get_list_sc_osmap_tilers() {
		return apply_filters( 'trx_addons_filter_sc_osmap_tilers', array(
			'vector' => esc_html__( 'Vector', 'trx_addons' ),
			'raster' => esc_html__( 'Raster', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_osmap_styles' ) ) {
	/**
	 * Return list of the osmap styles
	 *
	 * @trigger trx_addons_filter_sc_osmap_styles
	 *
	 * @param bool $full  Return full array with all data or only with titles
	 *
	 * @return array  List of osmap styles
	 */
	function trx_addons_get_list_sc_osmap_styles( $full = false ) {
		$tilers = trx_addons_get_option( 'api_openstreet_tiler_' . trx_addons_get_option( 'api_openstreet_tiler' ) );
		$styles = array();
		if ( is_array( $tilers ) ) {
			foreach( $tilers as $t ) {
				if ( ! empty( $t['title'] ) && ! empty( $t['slug'] ) && ! empty( $t['url'] ) ) {
					$styles[ $t['slug'] ] = $full ? $t : $t['title'];
				}
			}
		}
		return apply_filters( 'trx_addons_filter_sc_osmap_styles', $styles, $full );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_icompare_handlers' ) ) {
	/**
	 * Return list of the Image Compare draggable handlers: round or square
	 * 
	 * @trigger trx_addons_filter_get_list_sc_icompare_handlers
	 * 
	 * @return array  List of Image Compare draggable handlers
	 */
	function trx_addons_get_list_sc_icompare_handlers() {
		return apply_filters( 'trx_addons_filter_get_list_sc_icompare_handlers', array(
			'round'  => esc_html__( 'Round', 'trx_addons' ),
			'square' => esc_html__( 'Square', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_icon_positions' ) ) {
	/**
	 * Return list of the icon's positions
	 * 
	 * @trigger trx_addons_filter_get_list_sc_icon_positions
	 * 
	 * @return array  List of icon's positions
	 */
	function trx_addons_get_list_sc_icon_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_icon_positions', array(
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' ),
			'top'   => esc_html__( 'Top', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_icon_sizes' ) ) {
	/**
	 * Return list of the icon's sizes
	 * 
	 * @trigger trx_addons_filter_get_list_sc_icon_sizes
	 * 
	 * @return array  List of icon's sizes
	 */
	function trx_addons_get_list_sc_icon_sizes() {
		return apply_filters( 'trx_addons_filter_get_list_sc_icon_sizes', array(
			'small'  => esc_html__( 'Small', 'trx_addons' ),
			'medium' => esc_html__( 'Medium', 'trx_addons' ),
			'large'  => esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_instagram_redirects' ) ) {
	/**
	 * Return list of the Instagram redirects - where to go after click on the image
	 * 
	 * @trigger trx_addons_filter_get_list_sc_instagram_redirects
	 * 
	 * @return array  List of Instagram redirects
	 */
	function trx_addons_get_list_sc_instagram_redirects() {
		return apply_filters( 'trx_addons_filter_get_list_sc_instagram_redirects', array(
			'none'      => esc_html__( 'No links', 'trx_addons' ),
			'popup'     => esc_html__( 'Popup', 'trx_addons' ),
			'instagram' => esc_html__( 'Instagram', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_twitter_api' ) ) {
	/**
	 * Return list of the Twitter API to use in the widget
	 *
	 * @trigger trx_addons_filter_get_list_sc_twitter_api
	 *
	 * @return array  List of Twitter API
	 */
	function trx_addons_get_list_sc_twitter_api() {
		return apply_filters( 'trx_addons_filter_get_list_sc_twitter_api', array(
				'bearer' => __( 'Bearer token (new API)', 'trx_addons' ),
				'token'  => __( 'Token (old API)', 'trx_addons' ),
				'embed'  => __( 'Embed timeline', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hotspot_sources' ) ) {
	/**
	 * Return list of the hotspot sources: custom or post
	 * 
	 * @trigger trx_addons_filter_get_list_sc_hotspot_sources
	 * 
	 * @return array  List of hotspot sources
	 */
	function trx_addons_get_list_sc_hotspot_sources() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hotspot_sources', array(
			'custom' => esc_html__( 'Custom', 'trx_addons' ),
			'post'   => esc_html__( 'Post', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hotspot_post_parts' ) ) {
	/**
	 * Return list of the hotspot post parts to show in the popup
	 *
	 * @trigger trx_addons_filter_get_list_sc_hotspot_post_parts
	 *
	 * @return array  List of hotspot post parts
	 */
	function trx_addons_get_list_sc_hotspot_post_parts() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hotspot_post_parts', array(
			'image'    => esc_html__( 'Image', 'trx_addons' ),
			'title'    => esc_html__( 'Title', 'trx_addons' ),
			'category' => esc_html__( 'Category', 'trx_addons' ),
			'excerpt'  => esc_html__( 'Excerpt', 'trx_addons' ),
			'price'    => esc_html__( 'Price', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hotspot_symbols' ) ) {
	/**
	 * Return list of the hotspot symbols
	 * 
	 * @trigger trx_addons_filter_get_list_sc_hotspot_symbols
	 * 
	 * @return array  List of hotspot symbols
	 */
	function trx_addons_get_list_sc_hotspot_symbols() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hotspot_symbols', array(
			'none'   => esc_html__( 'None', 'trx_addons' ),
			'icon'   => esc_html__( 'Icon', 'trx_addons' ),
			'image'  => esc_html__( 'Image', 'trx_addons' ),
			'number' => esc_html__( 'Number', 'trx_addons' ),
			'custom' => esc_html__( 'Custom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_smoke_spot_motions' ) ) {
	/**
	 * Return list of the smoke spot motions: none, slow, fast
	 * 
	 * @trigger trx_addons_filter_get_list_sc_smoke_spot_motions
	 * 
	 * @return array  List of smoke spot motions
	 */
	function trx_addons_get_list_sc_smoke_spot_motions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_smoke_spot_motions', array(
			0 => esc_html__( 'None', 'trx_addons' ),
			1 => esc_html__( 'Slow', 'trx_addons' ),
			2 => esc_html__( 'Fast', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_socials_types' ) ) {
	/**
	 * Return list of the socials types: socials or share
	 *
	 * @trigger trx_addons_filter_get_list_sc_socials_types
	 *
	 * @return array  List of socials types
	 */
	function trx_addons_get_list_sc_socials_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_socials_types', array(
			'socials' => esc_html__( 'Social profiles', 'trx_addons' ),
			'share'   => esc_html__( 'Share links', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_promo_positions' ) ) {
	/**
	 * Return list of the promo image's positions: left or right
	 * 
	 * @trigger trx_addons_filter_get_list_sc_promo_positions
	 * 
	 * @return array  List of promo image's positions
	 */
	function trx_addons_get_list_sc_promo_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_promo_positions', array(
			'left' => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_promo_sizes' ) ) {
	/**
	 * Return list of the promo block sizes: tiny, small, normal, large
	 * 
	 * @trigger trx_addons_filter_get_list_sc_promo_sizes
	 * 
	 * @return array  List of promo sizes
	 */
	function trx_addons_get_list_sc_promo_sizes() {
		return apply_filters( 'trx_addons_filter_get_list_sc_promo_sizes', array(
			'tiny'   => esc_html__( 'Tiny', 'trx_addons' ),
			'small'  => esc_html__( 'Small', 'trx_addons' ),
			'normal' => esc_html__( 'Normal', 'trx_addons' ),
			'large'  => esc_html__( 'Large', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_promo_widths' ) ) {
	/**
	 * Return list of widths of the promo block text section: 1/1, 1/2, 1/3, 2/3, 1/4, 3/4
	 * 
	 * @trigger trx_addons_filter_get_list_sc_promo_widths
	 * 
	 * @param string $none_key  Key for the 'none' value. Default: 'none'
	 * 
	 * @return array  List of promo text widths
	 */
	function trx_addons_get_list_sc_promo_widths( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_promo_widths', array(
			$none_key => esc_html__( 'Default', 'trx_addons' ),
			'1_1' => esc_html__( '1/1', 'trx_addons' ),
			'1_2' => esc_html__( '1/2', 'trx_addons' ),
			'1_3' => esc_html__( '1/3', 'trx_addons' ),
			'2_3' => esc_html__( '2/3', 'trx_addons' ),
			'1_4' => esc_html__( '1/4', 'trx_addons' ),
			'3_4' => esc_html__( '3/4', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_skills_counter_styles' ) ) {
	/**
	 * Return list of the skills counter styles: counter or odometer
	 * 
	 * @trigger trx_addons_filter_get_list_sc_skills_counter_styles
	 * 
	 * @return array  List of skills counter styles
	 */
	function trx_addons_get_list_sc_skills_counter_styles() {
		return apply_filters( 'trx_addons_filter_get_list_sc_skills_counter_styles', array(
			'counter'  => esc_html__( 'Counter', 'trx_addons' ),
			'odometer' => esc_html__( 'Odometer', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_skills_counter_icon_positions' ) ) {
	/**
	 * Return list of the skills counter icon positions: top, left or right
	 * 
	 * @trigger trx_addons_filter_get_list_sc_skills_counter_icon_positions
	 * 
	 * @return array  List of skills counter icon positions
	 */
	function trx_addons_get_list_sc_skills_counter_icon_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_skills_counter_icon_positions', array(
			'top'   => esc_html__( 'Top', 'trx_addons' ),
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_dishes_positions' ) ) {
	/**
	 * Return list of the dishes image's positions: top, left or right
	 * 
	 * @trigger trx_addons_filter_get_list_sc_dishes_positions
	 * 
	 * @return array  List of dishes image's positions
	 */
	function trx_addons_get_list_sc_dishes_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_dishes_positions', array(
			'top' => esc_html__( 'Top', 'trx_addons' ),
			'left' => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_services_featured' ) ) {
	/**
	 * Return list of type of the featured element in services
	 *
	 * @trigger trx_addons_filter_get_list_sc_services_featured
	 * 
	 * @param string $none_key  Key for the 'none' value. Default: 'none'
	 *
	 * @return array  List of featured elements in services
	 */
	function trx_addons_get_list_sc_services_featured( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_services_featured', array(
			'image'		=> esc_html__( 'Image', 'trx_addons' ),
			'pictogram'	=> esc_html__( 'Pictogram', 'trx_addons' ),
			'icon'		=> esc_html__( 'Icon', 'trx_addons' ),
			'number'	=> esc_html__( 'Number', 'trx_addons' ),
			$none_key	=> esc_html__( 'None', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_services_featured_positions' ) ) {
	/**
	 * Return list of the featured element's positions in services
	 *
	 * @trigger trx_addons_filter_get_list_sc_services_featured_positions
	 *
	 * @return array  List of featured elements in services
	 */
	function trx_addons_get_list_sc_services_featured_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_services_featured_positions', array(
			'top'    => esc_html__( 'Top', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_services_tabs_effects' ) ) {
	/**
	 * Return list of the tabs effects in the Services
	 * 
	 * @trigger trx_addons_filter_get_list_sc_services_tabs_effects
	 * 
	 * @return array  List of tabs effects
	 */
	function trx_addons_get_list_sc_services_tabs_effects() {
		return apply_filters( 'trx_addons_filter_get_list_sc_services_tabs_effects', array(
			'fade'  => esc_html__( 'Fade', 'trx_addons' ),
			'slide' => esc_html__( 'Slide', 'trx_addons' ),
			'flip'  => esc_html__( 'Page flip', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_matches_positions' ) ) {
	/**
	 * Return list of main matches positions in the CPT Sport output: top, left or right
	 *
	 * @trigger trx_addons_filter_get_list_sc_matches_positions
	 *
	 * @return array  List of main matches positions
	 */
	function trx_addons_get_list_sc_matches_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_matches_positions', array(
			'top'   => esc_html__( 'Top', 'trx_addons' ),
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_layout_types' ) ) {
	/**
	 * Return list of the CPT Layout types
	 * 
	 * @trigger trx_addons_filter_layout_types
	 * 
	 * @return array  List of the CPT Layout types
	 */
	function trx_addons_get_list_layout_types() {
		return apply_filters( 'trx_addons_filter_layout_types', array(
					'header'  => esc_html__( 'Header', 'trx_addons' ),
					'footer'  => esc_html__( 'Footer', 'trx_addons' ),
					'sidebar' => esc_html__( 'Sidebar', 'trx_addons' ),
					'blog'    => esc_html__( 'Blog', 'trx_addons' ),
					'custom'  => esc_html__( 'Custom', 'trx_addons' )
				) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_type' ) ) {
	/**
	 * Return list of the shortcode Layouts types
	 * 
	 * @trigger trx_addons_filter_get_list_sc_layouts_type
	 * 
	 * @return array  List of the shortcode Layouts types
	 */
	function trx_addons_get_list_sc_layouts_type() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_type', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
			'popup'   => esc_html__( 'Popup', 'trx_addons' ),
			'panel'   => esc_html__( 'Panel', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_panel_positions' ) ) {
	/**
	 * Return list of the panel's positions for the shortcode Layouts: left, right, top or bottom
	 * 
	 * @trigger trx_addons_filter_get_list_sc_layouts_panel_positions
	 * 
	 * @return array  List of the panel's positions
	 */
	function trx_addons_get_list_sc_layouts_panel_positions() {
		return apply_filters( 'trx_addons_get_list_sc_layouts_panel_positions', array(
			"left"		=> esc_html__( 'Left', 'trx_addons' ),
			"right"		=> esc_html__( 'Right', 'trx_addons' ),
			"top"		=> esc_html__( 'Top', 'trx_addons' ),
			"bottom"	=> esc_html__( 'Bottom', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_panel_effects' ) ) {
	/**
	 * Return list of the panel's effects for the shortcode Layouts: slide, flip or flipout
	 * 
	 * @trigger trx_addons_filter_get_list_sc_layouts_panel_effects
	 * 
	 * @return array  List of the panel's effects
	 */
	function trx_addons_get_list_sc_layouts_panel_effects() {
		return apply_filters( 'trx_addons_get_list_sc_layouts_panel_effects', array(
			"slide"		=> esc_html__( 'Slide', 'trx_addons' ),
			"flip"		=> esc_html__( 'Flip In', 'trx_addons' ),
			"flipout"	=> esc_html__( 'Flip Out', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_language_positions' ) ) {
	/**
	 * Return list of the positions of the language switcher
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_language_positions
	 *
	 * @param string $none_key  Key for the 'none' position
	 *
	 * @return array  List of the positions of the language switcher
	 */
	function trx_addons_get_list_sc_layouts_language_positions( $none_key = 'none' ) {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_language_positions', array(
			$none_key	=> esc_html__( 'Hide', 'trx_addons' ),
			"title"		=> esc_html__( 'Only in the title', 'trx_addons' ),
			"menu"		=> esc_html__( 'Only in the menu', 'trx_addons' ),
			"both"		=> esc_html__( 'Both', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_language_parts' ) ) {
	/**
	 * Return list of the parts of the language switcher
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_language_parts
	 *
	 * @param string $none_key  Key for the 'none' parts
	 *
	 * @return array  List of the parts of the language switcher
	 */
	function trx_addons_get_list_sc_layouts_language_parts($none_key = 'none') {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_language_parts', array(
			$none_key	=> esc_html__( 'Hide', 'trx_addons' ),
			"name"		=> esc_html__( 'Language name', 'trx_addons' ),
			"code"		=> esc_html__( 'Language code', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_menu' ) ) {
	/**
	 * Return list of the menu types for the shortcode Layouts: Menu, Burger
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_menu
	 *
	 * @return array  List of the menu types
	 */
	function trx_addons_get_list_sc_layouts_menu() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_menu', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
			'burger'  => esc_html__( 'Burger', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_blog_item_parts' ) ) {
	/**
	 * Return list of the parts for the blog item: title, featured, meta, excerpt, content, custom, button
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_blog_item_parts
	 *
	 * @return array  List of the parts
	 */
	function trx_addons_get_list_sc_layouts_blog_item_parts() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_blog_item_parts', array(
			'title'    => esc_html__( 'Post title', 'trx_addons' ),
			'featured' => esc_html__( 'Featured image', 'trx_addons' ),
			'meta'     => esc_html__( 'Post meta', 'trx_addons' ),
			'excerpt'  => esc_html__( 'Excerpt', 'trx_addons' ),
			'content'  => esc_html__( 'Full content', 'trx_addons' ),
			'custom'   => esc_html__( 'Custom field', 'trx_addons' ),
			'button'   => esc_html__( 'Button', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_meta' ) ) {
	/**
	 * Return list of the meta layouts for the shortcode Layouts: Post meta
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_meta
	 *
	 * @return array  List of the meta parts
	 */
	function trx_addons_get_list_sc_layouts_meta() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_meta', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_search' ) ) {
	/**
	 * Return list of the search layouts for the shortcode Layouts: Search
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_search
	 *
	 * @return array  List of the search layouts
	 */
	function trx_addons_get_list_sc_layouts_search() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_search', array(
			'normal'     => esc_html__( 'Normal', 'trx_addons' ),
			'expand'     => esc_html__( 'Expand', 'trx_addons' ),
			'fullscreen' => esc_html__( 'Fullscreen', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_row_types' ) ) {
	/**
	 * Return list of the row types. Used for rows (sections) in the Layouts Header/Footer
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_row_types
	 *
	 * @return array  List of the row types
	 */
	function trx_addons_get_list_sc_layouts_row_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_row_types', array(
			'inherit' => esc_html__( 'Inherit', 'trx_addons' ),
			'narrow'  => esc_html__( 'Narrow', 'trx_addons' ),
			'compact' => esc_html__( 'Compact', 'trx_addons' ),
			'normal'  => esc_html__( 'Normal', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_icons_positions' ) ) {
	/**
	 * Return list of the icons positions in the shortcode Layouts: Text & Icons
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_icons_positions
	 *
	 * @return array  List of the icons positions
	 */
	function trx_addons_get_list_sc_layouts_icons_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_icons_positions', array(
			'left'  => esc_html__( 'Left', 'trx_addons' ),
			'right' => esc_html__( 'Right', 'trx_addons' )
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_layouts_cart_types' ) ) {
	/**
	 * Return list of the cart types in the shortcode Layouts: Cart
	 *
	 * @trigger trx_addons_filter_get_list_sc_layouts_cart_types
	 *
	 * @return array  List of the cart types
	 */
	function trx_addons_get_list_sc_layouts_cart_types() {
		return apply_filters( 'trx_addons_filter_get_list_sc_layouts_cart_types', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
			'panel'   => esc_html__( 'Panel', 'trx_addons' ),
			'button'  => esc_html__( 'Button', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_content_types' ) ) {
	/**
	 * Return list of the content types for the shortcodes Switcher and HSroll
	 *
	 * @trigger trx_addons_filter_get_list_content_types
	 * 
	 * @param bool $with_content  Add 'content' to the list
	 *
	 * @return array  List of the content types
	 */
	function trx_addons_get_list_content_types( $with_content = false ) {
		return apply_filters( 'trx_addons_filter_get_list_content_types', array_merge(
			$with_content ? array( 'content' => esc_html__( 'Content', 'trx_addons' ) ) : array(),
			array(
				'section'  => esc_html__( 'Section ID', 'trx_addons' ),
				'layout'   => esc_html__( 'Custom layout', 'trx_addons' ),
				'template' => esc_html__( 'Saved template', 'trx_addons' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hscroll_bullets_positions' ) ) {
	/**
	 * Return list of the bullets positions in the shortcode HScroll
	 *
	 * @trigger trx_addons_filter_get_list_sc_hscroll_bullets_positions
	 *
	 * @return array  List of the hscroll bullets positions
	 */
	function trx_addons_get_list_sc_hscroll_bullets_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hscroll_bullets_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hscroll_numbers_positions' ) ) {
	/**
	 * Return list of the numbers positions in the shortcode HScroll
	 *
	 * @trigger trx_addons_filter_get_list_sc_hscroll_numbers_positions
	 *
	 * @return array  List of the hscroll numbers positions
	 */
	function trx_addons_get_list_sc_hscroll_numbers_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hscroll_numbers_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'center' => esc_html__( 'Center', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_hscroll_progress_positions' ) ) {
	/**
	 * Return list of the progress positions in the shortcode HScroll
	 *
	 * @trigger trx_addons_filter_get_list_sc_hscroll_progress_positions
	 *
	 * @return array  List of the hscroll progress positions
	 */
	function trx_addons_get_list_sc_hscroll_progress_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_hscroll_progress_positions', array(
			'top'    => esc_html__( 'Top', 'trx_addons' ),
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_squeeze_bullets_positions' ) ) {
	/**
	 * Return list of the bullets positions in the shortcode Squeeze
	 *
	 * @trigger trx_addons_filter_get_list_sc_squeeze_bullets_positions
	 *
	 * @return array  List of the Squeeze bullets positions
	 */
	function trx_addons_get_list_sc_squeeze_bullets_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_squeeze_bullets_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_squeeze_numbers_positions' ) ) {
	/**
	 * Return list of the numbers positions in the shortcode Squeeze
	 *
	 * @trigger trx_addons_filter_get_list_sc_squeeze_numbers_positions
	 *
	 * @return array  List of the Squeeze numbers positions
	 */
	function trx_addons_get_list_sc_squeeze_numbers_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_squeeze_numbers_positions', array(
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'center' => esc_html__( 'Center', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_squeeze_progress_positions' ) ) {
	/**
	 * Return list of the progress positions in the shortcode Squeeze
	 *
	 * @trigger trx_addons_filter_get_list_sc_squeeze_progress_positions
	 *
	 * @return array  List of the Squeeze progress positions
	 */
	function trx_addons_get_list_sc_squeeze_progress_positions() {
		return apply_filters( 'trx_addons_filter_get_list_sc_squeeze_progress_positions', array(
			'top'    => esc_html__( 'Top', 'trx_addons' ),
			'left'   => esc_html__( 'Left', 'trx_addons' ),
			'right'  => esc_html__( 'Right', 'trx_addons' ),
			'bottom' => esc_html__( 'Bottom', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_sc_switcher_effects' ) ) {
	/**
	 * Return list of the effects in the shortcode Switcher
	 *
	 * @trigger trx_addons_filter_get_list_sc_switcher_effects
	 *
	 * @return array  List of the switcher effects
	 */
	function trx_addons_get_list_sc_switcher_effects() {
		return apply_filters( 'trx_addons_filter_get_list_sc_switcher_effects', array(
			'swap'  => esc_html__( 'Swap', 'trx_addons' ),
			'slide' => esc_html__( 'Slide', 'trx_addons' ),
			'fade'  => esc_html__( 'Fade', 'trx_addons' ),
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_widget_video_layouts' ) ) {
	/**
	 * Return list of the video layouts in the widget Video: Default, Hover (play on mouse hover)
	 *
	 * @trigger trx_addons_filter_get_list_widget_video_layouts
	 *
	 * @return array  List of the video layouts
	 */
	function trx_addons_get_list_widget_video_layouts() {
		return apply_filters( 'trx_addons_filter_get_list_widget_video_layouts', array(
			'default' => esc_html__( 'Default', 'trx_addons' ),
			'hover'   => esc_html__( 'Hover', 'trx_addons' ),
		) );
	}
}
