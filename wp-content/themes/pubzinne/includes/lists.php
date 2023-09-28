<?php
/**
 * Theme lists
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }



// Return numbers range
if ( ! function_exists( 'pubzinne_get_list_range' ) ) {
	function pubzinne_get_list_range( $from = 1, $to = 2, $prepend_inherit = false ) {
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			$list[ $i ] = $i;
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}



// Return styles list
if ( ! function_exists( 'pubzinne_get_list_styles' ) ) {
	function pubzinne_get_list_styles( $from = 1, $to = 2, $prepend_inherit = false ) {
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			// Translators: Add number to the style name 'Style 1', 'Style 2' ...
			$list[ $i ] = sprintf( esc_html__( 'Style %d', 'pubzinne' ), $i );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'Yes' and 'No' items
if ( ! function_exists( 'pubzinne_get_list_yesno' ) ) {
	function pubzinne_get_list_yesno( $prepend_inherit = false ) {
		$list = array(
			'yes' => esc_html__( 'Yes', 'pubzinne' ),
			'no'  => esc_html__( 'No', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'Yes' and 'No' items for checkboxes: 'Yes' -> 1, 'No' -> 0
if ( ! function_exists( 'pubzinne_get_list_checkbox_values' ) ) {
	function pubzinne_get_list_checkbox_values( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_checkbox_values', array(
				1         => esc_html__( 'Yes', 'pubzinne' ),
				0         => esc_html__( 'No', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'On' and 'Of' items
if ( ! function_exists( 'pubzinne_get_list_onoff' ) ) {
	function pubzinne_get_list_onoff( $prepend_inherit = false ) {
		$list = array(
			'on'  => esc_html__( 'On', 'pubzinne' ),
			'off' => esc_html__( 'Off', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'Show' and 'Hide' items
if ( ! function_exists( 'pubzinne_get_list_showhide' ) ) {
	function pubzinne_get_list_showhide( $prepend_inherit = false ) {
		$list = array(
			'show' => esc_html__( 'Show', 'pubzinne' ),
			'hide' => esc_html__( 'Hide', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'Visible' and 'Hidden' items
if ( ! function_exists( 'pubzinne_get_list_visiblehidden' ) ) {
	function pubzinne_get_list_visiblehidden( $prepend_inherit = false ) {
		$list = array(
			'visible' => esc_html__( 'Visible', 'pubzinne' ),
			'hidden'  => esc_html__( 'Hidden', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with 'Horizontal' and 'Vertical' items
if ( ! function_exists( 'pubzinne_get_list_directions' ) ) {
	function pubzinne_get_list_directions( $prepend_inherit = false ) {
		$list = array(
			'horizontal' => esc_html__( 'Horizontal', 'pubzinne' ),
			'vertical'   => esc_html__( 'Vertical', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list with paddings sizes
if ( ! function_exists( 'pubzinne_get_list_paddings' ) ) {
	function pubzinne_get_list_paddings( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_paddings', array(
				'none'  => array(
							'title' => esc_html__( 'No Padding', 'pubzinne' ),
							'icon'  => 'images/theme-options/section-padding/none.png',
						),
				'small'  => array(
							'title' => esc_html__( 'Small Padding', 'pubzinne' ),
							'icon'  => 'images/theme-options/section-padding/small.png',
						),
				'medium' => array(
							'title' => esc_html__( 'Medium Padding', 'pubzinne' ),
							'icon'  => 'images/theme-options/section-padding/medium.png',
						),
				'large' => array(
							'title' => esc_html__( 'Large Padding', 'pubzinne' ),
							'icon'  => 'images/theme-options/section-padding/large.png',
						),
			)
		);
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return list with image's hovers
if ( ! function_exists( 'pubzinne_get_list_hovers' ) ) {
	function pubzinne_get_list_hovers( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_hovers', array(
				'dots'    => esc_html__( 'Dots', 'pubzinne' ),
				'icon'    => esc_html__( 'Icon', 'pubzinne' ),
				'icons'   => esc_html__( 'Icons', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return blog content types
if ( ! function_exists( 'pubzinne_get_list_blog_contents' ) ) {
	function pubzinne_get_list_blog_contents( $prepend_inherit = false ) {
		$list = array(
				'excerpt'  => esc_html__( 'Excerpt', 'pubzinne' ),
				'fullpost' => esc_html__( 'Full post', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return blog paginations
if ( ! function_exists( 'pubzinne_get_list_blog_paginations' ) ) {
	function pubzinne_get_list_blog_paginations( $prepend_inherit = false ) {
		$list = array(
					'pages'    => array(
										'title' => esc_html__( 'Page numbers', 'pubzinne' ),
										'icon'  => 'images/theme-options/pagination/page-numbers.png',
										),
					'links'    => array(
										'title' => esc_html__( 'Older/Newest', 'pubzinne' ),
										'icon'  => 'images/theme-options/pagination/older-newest.png',
										),
					'more'     => array(
										'title' => esc_html__( 'Load more', 'pubzinne' ),
										'icon'  => 'images/theme-options/pagination/load-more.png',
										),
					'infinite' => array(
										'title' => esc_html__( 'Infinite scroll', 'pubzinne' ),
										'icon'  => 'images/theme-options/pagination/infinite-scroll.png',
										),
		);
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return custom sidebars list, prepended inherit and main sidebars item (if need)
if ( ! function_exists( 'pubzinne_get_list_sidebars' ) ) {
	function pubzinne_get_list_sidebars( $prepend_inherit = false, $add_hide = false ) {
		$list = pubzinne_storage_get( 'list_sidebars' );
		if ( '' == $list ) {
			global $wp_registered_sidebars;
			$list = array();
			if ( is_array( $wp_registered_sidebars ) ) {
				foreach ( $wp_registered_sidebars as $k => $v ) {
					$list[ $v['id'] ] = $v['name'];
				}
			}
			pubzinne_storage_set( 'list_sidebars', $list );
		}
		if ( $add_hide ) {
			$list = pubzinne_array_merge( array( 'hide' => esc_html__( '- Select widgets -', 'pubzinne' ) ), $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return sidebars positions
if ( ! function_exists( 'pubzinne_get_list_sidebars_positions' ) ) {
	function pubzinne_get_list_sidebars_positions( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_sidebars_positions', array(
				'hide'  => array(
							'title' => esc_html__( 'No sidebar', 'pubzinne' ),
							'icon'  => 'images/theme-options/sidebar-position/hide.png',
						),
				'left'  => array(
							'title' => esc_html__( 'Left sidebar', 'pubzinne' ),
							'icon'  => 'images/theme-options/sidebar-position/left.png',
						),
				'right' => array(
							'title' => esc_html__( 'Right sidebar', 'pubzinne' ),
							'icon'  => 'images/theme-options/sidebar-position/right.png',
						),
			)
		);
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return sidebars positions on the small screen
if ( ! function_exists( 'pubzinne_get_list_sidebars_positions_ss' ) ) {
	function pubzinne_get_list_sidebars_positions_ss( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_sidebars_positions_ss', array(
				'above' => esc_html__( 'Above the content', 'pubzinne' ),
				'below' => esc_html__( 'Below the content', 'pubzinne' ),
				'float' => esc_html__( 'Floating bar', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return sidebar styles
if ( ! function_exists( 'pubzinne_get_list_sidebar_styles' ) ) {
	function pubzinne_get_list_sidebar_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'pubzinne_filter_list_sidebar_styles', array() );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Return header/footer/sidebar types
if ( ! function_exists( 'pubzinne_get_list_header_footer_types' ) ) {
	function pubzinne_get_list_header_footer_types( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_header_footer_types', array(
				'default' => esc_html__( 'Default', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return header styles
if ( ! function_exists( 'pubzinne_get_list_header_styles' ) ) {
	function pubzinne_get_list_header_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'pubzinne_filter_list_header_styles', array() );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return header positions
if ( ! function_exists( 'pubzinne_get_list_header_positions' ) ) {
	function pubzinne_get_list_header_positions( $prepend_inherit = false ) {
		$list = array(
			'default' => esc_html__( 'Default', 'pubzinne' ),
			'over'    => esc_html__( 'Over', 'pubzinne' ),
			'under'   => esc_html__( 'Under', 'pubzinne' ),
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return footer styles
if ( ! function_exists( 'pubzinne_get_list_footer_styles' ) ) {
	function pubzinne_get_list_footer_styles( $prepend_inherit = false ) {
		static $list = false;
		if ( ! $list ) {
			$list = apply_filters( 'pubzinne_filter_list_footer_styles', array() );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return body styles list, prepended inherit
if ( ! function_exists( 'pubzinne_get_list_body_styles' ) ) {
	function pubzinne_get_list_body_styles( $prepend_inherit = false, $force_fullscreen = false ) {
		$list = array(
			'boxed' => array(
						'title' => esc_html__( 'Boxed', 'pubzinne' ),
						'icon'  => 'images/theme-options/body-style/boxed.png',
					),
			'wide'  => array(
						'title' => esc_html__( 'Wide', 'pubzinne' ),
						'icon'  => 'images/theme-options/body-style/wide.png',
					),
		);
		if ( apply_filters( 'pubzinne_filter_allow_fullscreen', $force_fullscreen || pubzinne_get_theme_setting( 'allow_fullscreen' ) || pubzinne_get_edited_post_type() == 'page' ) ) {
			$list['fullwide']   = array(
									'title' => esc_html__( 'Fullwidth', 'pubzinne' ),
									'icon'  => 'images/theme-options/body-style/fullwide.png',
									);
			$list['fullscreen'] = array(
									'title' => esc_html__( 'Fullscreen', 'pubzinne' ),
									'icon'  => 'images/theme-options/body-style/fullscreen.png',
									);
		}
		$list = apply_filters( 'pubzinne_filter_list_body_styles', $list );
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return 'expand content' choices
if ( ! function_exists( 'pubzinne_get_list_expand_content' ) ) {
	function pubzinne_get_list_expand_content( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_expand_content', array(
				'narrow' => array(
							'title' => esc_html__( 'Narrow', 'pubzinne' ),
							'icon'  => 'images/theme-options/expand-content/narrow.png',
						),
				'normal' => array(
							'title' => esc_html__( 'Normal', 'pubzinne' ),
							'icon'  => 'images/theme-options/expand-content/normal.png',
						),
				'expand' => array(
							'title' => esc_html__( 'Wide', 'pubzinne' ),
							'icon'  => 'images/theme-options/expand-content/wide.png',
						),
			)
		);
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return 'remove margins' choices
if ( ! function_exists( 'pubzinne_get_list_remove_margins' ) ) {
	function pubzinne_get_list_remove_margins( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_remove_margins', array(
				'0'  => array(
							'title' => esc_html__( 'Margins On', 'pubzinne' ),
							'icon'  => 'images/theme-options/remove-margins/on.png',
						),
				'1'  => array(
							'title' => esc_html__( 'Margins Off', 'pubzinne' ),
							'icon'  => 'images/theme-options/remove-margins/off.png',
						),
			)
		);
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return meta parts list
if ( ! function_exists( 'pubzinne_get_list_meta_parts' ) ) {
	function pubzinne_get_list_meta_parts( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_meta_parts',
			array(
				'author'     => esc_html__( 'Post author', 'pubzinne' ),
				'date'       => esc_html__( 'Published date', 'pubzinne' ),
				'modified'   => esc_html__( 'Modified date', 'pubzinne' ),
				'views'      => esc_html__( 'Views', 'pubzinne' ),
				'likes'      => esc_html__( 'Likes', 'pubzinne' ),
				'comments'   => esc_html__( 'Comments', 'pubzinne' ),
				'share'      => esc_html__( 'Share links', 'pubzinne' ),
				'categories' => esc_html__( 'Categories', 'pubzinne' ),
				'edit'       => esc_html__( 'Edit link', 'pubzinne' ),
			)
		);
		// Reorder meta_parts with last user's choise
		if ( pubzinne_storage_isset( 'options', 'meta_parts', 'val' ) ) {
			$parts = explode( '|', pubzinne_get_theme_option( 'meta_parts' ) );
			$list_new = array();
			foreach( $parts as $part ) {
				$part = explode( '=', $part );
				if ( isset( $list[ $part[0] ] ) ) {
					$list_new[ $part[0] ] = $list[ $part[0] ];
					unset( $list[ $part[0] ] );
				}
			}
			$list = count( $list ) > 0 ? array_merge( $list_new, $list ) : $list_new;
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return share links positions
if ( ! function_exists( 'pubzinne_get_list_share_links_positions' ) ) {
	function pubzinne_get_list_share_links_positions( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_list_share_links_positions',
			array(
				'top'    => esc_html__( 'Top', 'pubzinne' ),
				'left'   => esc_html__( 'Left', 'pubzinne' ),
				'bottom' => esc_html__( 'Bottom', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return blog styles list, prepended inherit
if ( ! function_exists( 'pubzinne_get_list_blog_styles' ) ) {
	function pubzinne_get_list_blog_styles( $prepend_inherit = false, $filter = 'arh' ) {
		$list   = array();
		$styles = pubzinne_storage_get( 'blog_styles' );
		if ( is_array( $styles ) ) {
			foreach ( $styles as $k => $v ) {
				if ( empty( $filter ) || ! isset( $v[ "{$filter}_allowed" ] ) || $v[ "{$filter}_allowed" ] ) {
					if ( 'arh' == $filter && isset( $v['columns'] ) && is_array( $v['columns'] ) ) {
						$new_row = ! empty( $v['new_row'] );
						foreach ( $v['columns'] as $col ) {
							// Translators: Make blog style title: "Layout name /X columns/"
							$list[ "{$k}_{$col}" ] = 'arh' == $filter
														? array(
															'title'   => sprintf( _n( '%1$s /%2$d column/', '%1$s /%2$d columns/', $col, 'pubzinne' ), $v['title'], $col ),
															'icon'    => ! empty( $v['icon'] )
																			? ( strpos( $v['icon'], '%d' ) !== false ? sprintf( $v['icon'], $col ) : $v['icon'] )
																			: 'images/theme-options/blog-style/custom.png',
															'new_row' => $new_row,
															)
														: sprintf( _n( '%1$s /%2$d column/', '%1$s /%2$d columns/', $col, 'pubzinne' ), $v['title'], $col );
							$new_row = false;
						}
					} else {
						$list[ $k ] = 'arh' == $filter
											? array(
													'title' => $v['title'],
													'icon'  => ! empty( $v['icon'] )
																	? ( strpos( $v['icon'], '%d' ) !== false ? sprintf( $v['icon'], $col ) : $v['icon'] )
																	: 'images/theme-options/blog-style/custom.png',
												)
											: $v['title'];
					}
				}
			}
		}
		$list = apply_filters( 'pubzinne_filter_list_blog_styles', $list, $filter );
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}

// Return single styles list, prepended inherit
if ( ! function_exists( 'pubzinne_get_list_single_styles' ) ) {
	function pubzinne_get_list_single_styles( $prepend_inherit = false ) {
		$list = apply_filters( 'pubzinne_filter_list_single_styles', pubzinne_storage_get( 'single_styles' ) );
		return $prepend_inherit
					? pubzinne_array_merge(
							array( 
								'inherit' => array(
												'title' => esc_html__( 'Inherit', 'pubzinne' ),
												'icon'  => 'images/theme-options/inherit.png',
												),
							),
							$list
						)
					: $list;
	}
}


// Return list of categories
if ( ! function_exists( 'pubzinne_get_list_categories' ) ) {
	function pubzinne_get_list_categories( $prepend_inherit = false ) {
		$list = pubzinne_storage_get( 'list_categories' );
		if ( '' == $list ) {
			$list       = array();
			$taxonomies = get_categories(
				array(
					'type'         => 'post',
					'orderby'      => 'name',
					'order'        => 'ASC',
					'hide_empty'   => 0,
					'hierarchical' => 1,
					'taxonomy'     => 'category',
					'pad_counts'   => false,
				)
			);
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
				foreach ( $taxonomies as $cat ) {
					$list[ $cat->term_id ] = apply_filters( 'pubzinne_filter_term_name', $cat->name, $cat );
				}
			}
			pubzinne_storage_set( 'list_categories', $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Return list of taxonomies
if ( ! function_exists( 'pubzinne_get_list_terms' ) ) {
	function pubzinne_get_list_terms( $prepend_inherit = false, $taxonomy = 'category' ) {
		$list = pubzinne_storage_get( 'list_taxonomies_' . ( $taxonomy ) );
		if ( '' == $list ) {
			$list       = array();
			$taxonomies = get_terms(
				$taxonomy, array(
					'orderby'      => 'name',
					'order'        => 'ASC',
					'hide_empty'   => 0,
					'hierarchical' => 1,
					'taxonomy'     => $taxonomy,
					'pad_counts'   => false,
				)
			);
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
				foreach ( $taxonomies as $cat ) {
					// Remove false to append term names with taxonomy name
					$list[ $cat->term_id ] = apply_filters( 'pubzinne_filter_term_name', $cat->name . ( false && 'category' != $taxonomy ? " /{$cat->taxonomy}/" : '' ), $cat );
				}
			}
			pubzinne_storage_set( 'list_taxonomies_' . ( $taxonomy ), $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return list of post's types
if ( ! function_exists( 'pubzinne_get_list_posts_types' ) ) {
	function pubzinne_get_list_posts_types( $prepend_inherit = false ) {
		$list = pubzinne_storage_get( 'list_posts_types' );
		if ( '' == $list ) {
			$list = apply_filters(
				'pubzinne_filter_list_posts_types', array(
					'post' => esc_html__( 'Post', 'pubzinne' ),
				)
			);
			pubzinne_storage_set( 'list_posts_types', $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Return list post items from any post type and taxonomy
if ( ! function_exists( 'pubzinne_get_list_posts' ) ) {
	function pubzinne_get_list_posts( $prepend_inherit = false, $opt = array() ) {
		$opt = array_merge(
			array(
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'post_parent'      => '',
				'taxonomy'         => 'category',
				'taxonomy_value'   => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'meta_compare'     => '',
				'suppress_filters' => false,  // Need to compatibility with WPML, because default value is true in the get_posts()
				'posts_per_page'   => -1,
				'orderby'          => 'post_date',
				'order'            => 'desc',
				'not_selected'     => true,
				'return'           => 'id',
			), is_array( $opt ) ? $opt : array( 'post_type' => $opt )
		);

		$hash = 'list_posts'
				. '_' . ( is_array( $opt['post_type'] ) ? join( '_', $opt['post_type'] ) : $opt['post_type'] )
				. '_' . ( is_array( $opt['post_parent'] ) ? join( '_', $opt['post_parent'] ) : $opt['post_parent'] )
				. '_' . ( $opt['taxonomy'] )
				. '_' . ( is_array( $opt['taxonomy_value'] ) ? join( '_', $opt['taxonomy_value'] ) : $opt['taxonomy_value'] )
				. '_' . ( $opt['meta_key'] )
				. '_' . ( $opt['meta_compare'] )
				. '_' . ( $opt['meta_value'] )
				. '_' . ( $opt['orderby'] )
				. '_' . ( $opt['order'] )
				. '_' . ( $opt['return'] )
				. '_' . ( $opt['posts_per_page'] );
		$list = pubzinne_storage_get( $hash );
		if ( '' == $list ) {
			$list = array();
			if ( false !== $opt['not_selected'] ) {
				$list['none'] = true === $opt['not_selected'] ? esc_html__( '- Not selected -', 'pubzinne' ) : $opt['not_selected'];
			}
			$args = array(
				'post_type'           => $opt['post_type'],
				'post_status'         => $opt['post_status'],
				'posts_per_page'      => -1 == $opt['posts_per_page'] ? 1000 : $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'             => $opt['orderby'],
				'order'               => $opt['order'],
			);
			if ( ! empty( $opt['post_parent'] ) ) {
				if ( is_array( $opt['post_parent'] ) ) {
					$args['post_parent__in'] = $opt['post_parent'];
				} else {
					$args['post_parent'] = $opt['post_parent'];
				}
			}
			if ( ! empty( $opt['taxonomy_value'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field'    => is_array( $opt['taxonomy_value'] )
										? ( (int) $opt['taxonomy_value'][0] > 0 ? 'term_taxonomy_id' : 'slug' )
										: ( (int) $opt['taxonomy_value'] > 0 ? 'term_taxonomy_id' : 'slug' ),
						'terms'    => is_array( $opt['taxonomy_value'] )
										? $opt['taxonomy_value']
										: ( (int) $opt['taxonomy_value'] > 0 ? (int) $opt['taxonomy_value'] : $opt['taxonomy_value'] ),
					),
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
			$posts = get_posts( $args );
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					$list[ 'id' == $opt['return'] ? $post->ID : $post->post_title ] = $post->post_title;
				}
			}
			pubzinne_storage_set( $hash, $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Return list of registered users
if ( ! function_exists( 'pubzinne_get_list_users' ) ) {
	function pubzinne_get_list_users( $prepend_inherit = false, $roles = array( 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ) ) {
		$list = pubzinne_storage_get( 'list_users' );
		if ( '' == $list ) {
			$list         = array();
			$list['none'] = esc_html__( '- Not selected -', 'pubzinne' );
			$users        = get_users(
				array(
					'orderby' => 'display_name',
					'order'   => 'ASC',
				)
			);
			if ( is_array( $users ) && count( $users ) > 0 ) {
				foreach ( $users as $user ) {
					$accept = true;
					if ( is_array( $user->roles ) ) {
						if ( is_array( $user->roles ) && count( $user->roles ) > 0 ) {
							$accept = false;
							foreach ( $user->roles as $role ) {
								if ( in_array( $role, $roles ) ) {
									$accept = true;
									break;
								}
							}
						}
					}
					if ( $accept ) {
						$list[ $user->user_login ] = $user->display_name;
					}
				}
			}
			pubzinne_storage_set( 'list_users', $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return menus list, prepended inherit
if ( ! function_exists( 'pubzinne_get_list_menus' ) ) {
	function pubzinne_get_list_menus( $prepend_inherit = false ) {
		$list = pubzinne_storage_get( 'list_menus' );
		if ( '' == $list ) {
			$list            = array();
			$list['default'] = esc_html__( 'Default', 'pubzinne' );
			$menus           = wp_get_nav_menus();
			if ( is_array( $menus ) && count( $menus ) > 0 ) {
				foreach ( $menus as $menu ) {
					$list[ $menu->slug ] = $menu->name;
				}
			}
			pubzinne_storage_set( 'list_menus', $list );
		}
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Return list of the specified icons (font icons, svg icons or png icons)
if ( ! function_exists( 'pubzinne_get_list_icons' ) ) {
	function pubzinne_get_list_icons( $style ) {
		$lists = get_transient( 'pubzinne_list_icons' );
		if ( ! is_array( $lists ) || ! isset( $lists[ $style ] ) || ! is_array( $lists[ $style ] ) || count( $lists[ $style ] ) < 2 ) {
			if ( 'icons' == $style ) {
				$lists[ $style ] = pubzinne_array_from_list( pubzinne_get_list_icons_classes() );
			} elseif ( 'images' == $style ) {
				$lists[ $style ] = pubzinne_get_list_images();
			} else { // 'svg'
				$lists[ $style ] = pubzinne_get_list_images( false, 'svg' );
			}
			if ( is_admin() && is_array( $lists[ $style ] ) && count( $lists[ $style ] ) > 1 ) {
				set_transient( 'pubzinne_list_icons', $lists, 12 * 60 * 60 );       // Store to the cache for 12 hours
			}
		}
		return $lists[ $style ];
	}
}

// Return iconed classes list
if ( ! function_exists( 'pubzinne_get_list_icons_classes' ) ) {
	function pubzinne_get_list_icons_classes( $prepend_inherit = false ) {
		static $list = false;
		if ( ! is_array( $list ) ) {
			$list = ! is_admin() ? array() : pubzinne_parse_icons_classes( pubzinne_get_file_dir( 'css/font-icons/css/fontello-codes.css' ) );
		}
		$list = pubzinne_array_merge( array( 'none' => 'none' ), $list );
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}

// Return images list
if ( ! function_exists( 'pubzinne_get_list_images' ) ) {
	function pubzinne_get_list_images( $prepend_inherit = false, $type = 'png' ) {
		$list = function_exists( 'trx_addons_get_list_files' )
				? trx_addons_get_list_files( "css/icons.{$type}", $type )
				: array();
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}


// Additional attributes for VC and SOW
//----------------------------------------------------
if ( ! function_exists( 'pubzinne_get_list_sc_color_styles' ) ) {
	function pubzinne_get_list_sc_color_styles( $prepend_inherit = false ) {
		$list = apply_filters(
			'pubzinne_filter_get_list_sc_color_styles', array(
				'default' => esc_html__( 'Default', 'pubzinne' ),
				'link2'   => esc_html__( 'Link 2', 'pubzinne' ),
				'link3'   => esc_html__( 'Link 3', 'pubzinne' ),
				'dark'    => esc_html__( 'Dark', 'pubzinne' ),
			)
		);
		return $prepend_inherit ? pubzinne_array_merge( array( 'inherit' => esc_html__( 'Inherit', 'pubzinne' ) ), $list ) : $list;
	}
}
