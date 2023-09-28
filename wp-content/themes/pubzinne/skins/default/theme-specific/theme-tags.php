<?php
/**
 * Theme tags
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */


//----------------------------------------------------------------------
//-- Common tags
//----------------------------------------------------------------------

// Return true if current page need title
if ( ! function_exists( 'pubzinne_need_page_title' ) ) {
	function pubzinne_need_page_title() {
		return ! is_front_page() && apply_filters( 'pubzinne_filter_need_page_title', true );
	}
}

// Output string with the html layout (if not empty)
// (put it between 'before' and 'after' tags)
// Attention! This string may contain layout formed in any plugin (widgets or shortcodes output)
// and not require escaping to prevent damage layout!
if ( ! function_exists( 'pubzinne_show_layout' ) ) {
	function pubzinne_show_layout( $str, $before = '', $after = '' ) {
		if ( trim( $str ) != '' ) {
			printf( '%s%s%s', $before, $str, $after );
		}
	}
}

// Return logo images (if set)
if ( ! function_exists( 'pubzinne_get_logo_image' ) ) {
	function pubzinne_get_logo_image( $type = '' ) {
		$logo_image  = '';
		if ( empty( $type ) && function_exists( 'the_custom_logo' ) ) {
			$logo_image = pubzinne_get_theme_option( 'custom_logo' );
			if ( empty( $logo_image ) ) {
				$logo_image = get_theme_mod( 'custom_logo' );
			}
			if ( is_numeric( $logo_image ) && (int) $logo_image > 0) {
				$image      = wp_get_attachment_image_src( $logo_image, 'full' );
				$logo_image = $image[0];
			}
		} else {
			$logo_image = pubzinne_get_theme_option( 'logo' . ( ! empty( $type ) ? '_' . trim( $type ) : '' ) );
		}
		$logo_retina = pubzinne_is_on( pubzinne_get_theme_option( 'logo_retina_enabled' ) )
						? pubzinne_get_theme_option( 'logo' . ( ! empty( $type ) ? '_' . trim( $type ) : '' ) . '_retina' )
						: '';
		return array(
					'logo'        => ! empty( $logo_image ) ? pubzinne_remove_protocol_from_url( $logo_image, false ) : '',
					'logo_retina' => ! empty( $logo_retina ) ? pubzinne_remove_protocol_from_url( $logo_retina, false ) : ''
				);
	}
}

// Return header video (if set)
if ( ! function_exists( 'pubzinne_get_header_video' ) ) {
	function pubzinne_get_header_video() {
		$video = '';
		if ( apply_filters( 'pubzinne_header_video_enable', ! wp_is_mobile() && is_front_page() ) ) {
			if ( pubzinne_check_theme_option( 'header_video' ) ) {
				$video = pubzinne_get_theme_option( 'header_video' );
				if ( (int) $video > 0 ) {
					$video = wp_get_attachment_url( $video );
				}
			} elseif ( function_exists( 'get_header_video_url' ) ) {
				$video = get_header_video_url();
			}
		}
		return $video;
	}
}


//----------------------------------------------------------------------
//-- Post parts
//----------------------------------------------------------------------

// Show post featured image
if ( ! function_exists( 'pubzinne_show_post_featured_image' ) ) {
	function pubzinne_show_post_featured_image( $args = array() ) {
		$args = array_merge( array(
								'singular' => true,
								'thumb_bg' => false,
								),
								$args
							);
		// Featured image
		if ( ! pubzinne_sc_layouts_showed( 'featured' ) && strpos( pubzinne_get_post_content(), '[trx_widget_banner]' ) === false ) {
			do_action( 'pubzinne_action_before_post_featured' );
			pubzinne_show_post_featured( $args );
			do_action( 'pubzinne_action_after_post_featured' );
		} elseif ( pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) ) && has_post_thumbnail() ) {
			?>
			<meta itemprop="image" itemtype="<?php echo esc_attr( pubzinne_get_protocol( true ) ); ?>//schema.org/ImageObject" content="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>">
			<?php
		}
	}
}

// Show post title and meta
if ( ! function_exists( 'pubzinne_show_post_title_and_meta' ) ) {
	function pubzinne_show_post_title_and_meta( $args = array() ) {

		$args = array_merge( array(
								'content_wrap'  => false,
								'show_title'    => true,
								'show_meta'     => true,
								'split_meta_by' => '',
								),
								$args
							);

		// Title and post meta
		if ( ( ! pubzinne_sc_layouts_showed( 'title' ) || ! pubzinne_sc_layouts_showed( 'postmeta' ) ) ) {
			do_action( 'pubzinne_action_before_post_title' );
			ob_start();
			?>
			<div class="post_header post_header_single entry-header">
				<?php
				if ( $args['content_wrap'] ) {
					?>
					<div class="content_wrap">
					<?php
				}
				// Post meta
				$meta_components = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );
				$show_categories = in_array( 'categories', $meta_components );
				$meta_components = pubzinne_array_delete_by_value( $meta_components, 'categories' );
				$meta_components = pubzinne_array_delete_by_value( $meta_components, 'edit' );
				$meta_components = pubzinne_array_delete_by_value( $meta_components, 'likes' );
				$share_position  = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'share_position' ) );
				if ( ! in_array( 'top', $share_position ) ) {
					$meta_components = pubzinne_array_delete_by_value( $meta_components, 'share' );
				}
				$seo = pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) );
				if ( ! empty( $args['show_title'] ) ) {
					if ( $show_categories && ! pubzinne_sc_layouts_showed( 'postmeta' ) && pubzinne_is_on( pubzinne_get_theme_option( 'show_post_meta' ) ) ) {
						pubzinne_show_post_meta(
							apply_filters(
								'pubzinne_filter_post_meta_args',
								array_merge(
									array(
										'components' => 'categories',
										'class'      => 'post_meta_categories',
									),
									$args
								),
								'single',
								1
							)
						);
					}
					// Post title
					if ( ! pubzinne_sc_layouts_showed( 'title' ) ) {
						the_title( '<h1 class="post_title entry-title"' . ( $seo ? ' itemprop="headline"' : '' ) . '>', '</h1>' );
					}
					// Post subtitle
					$post_subtitle = pubzinne_get_theme_option( 'post_subtitle' );
					if ( ! empty( $post_subtitle ) ) {
						?>
						<div class="post_subtitle">
							<?php pubzinne_show_layout( $post_subtitle ); ?>
						</div>
						<?php
					}
				}
				// Post meta
				if ( ! empty( $args['show_meta'] ) && ! pubzinne_sc_layouts_showed( 'postmeta' ) && pubzinne_is_on( pubzinne_get_theme_option( 'show_post_meta' ) ) ) {
					if ( ! empty( $args['split_meta_by'] ) ) {
						?><div class="post_meta_other"><?php
							$meta_components = pubzinne_get_theme_option( 'meta_parts' );
							if ( ! is_array( $meta_components ) ) {
								parse_str( str_replace( '|', '&', $meta_components ), $meta_components );
							}
							if ( isset( $meta_components['categories'] ) ) {
								unset( $meta_components['categories'] );
							}
							if ( isset( $meta_components['likes'] ) ) {
								unset( $meta_components['likes'] );
							}
							$part1 = array_keys( pubzinne_array_slice( $meta_components, '', $args['split_meta_by'] ), 1 );
							$part2 = array_keys( pubzinne_array_slice( $meta_components, '+' . $args['split_meta_by'], '' ), 1 );
							if ( ! in_array( 'top', $share_position ) ) {
								$part2 = pubzinne_array_delete_by_value( $part2, 'share' );
							}

							pubzinne_show_post_meta(
								apply_filters(
									'pubzinne_filter_post_meta_args',
									array_merge(
										array(
											'components' => join( ',', $part1 ),
											'seo'        => $seo,
											'class'      => 'post_meta_other_part1',
										),
										$args
									),
									'single',
									1
								)
							);
							pubzinne_show_post_meta(
								apply_filters(
									'pubzinne_filter_post_meta_args',
									array_merge(
										array(
											'components' => join( ',', $part2 ),
											'seo'        => $seo,
											'class'      => 'post_meta_other_part2',
										),
										$args,
										array(
											'show_labels' => ! empty( $args['show_labels'] ) || ! in_array( 'share', $part2 ) || ! pubzinne_exists_trx_addons()
										)
									),
									'single',
									1
								)
							);
						?></div><?php
					} else {
						pubzinne_show_post_meta(
							apply_filters(
								'pubzinne_filter_post_meta_args',
								array_merge(
									array(
										'components' => join( ',', $meta_components ),
										'seo'        => $seo,
										'class'      => 'post_meta_other',
									),
									$args
								),
								'single',
								1
							)
						);
					}
				}
				if ( $args['content_wrap']) {
					?>
					</div>
					<?php
				}
				?>
			</div><!-- .post_header -->
			<?php
			$pubzinne_post_header = ob_get_contents();
			ob_end_clean();
			if ( strpos( $pubzinne_post_header, 'post_subtitle' ) !== false
				|| strpos( $pubzinne_post_header, 'post_title' ) !== false
				|| strpos( $pubzinne_post_header, 'post_meta' ) !== false
			) {
				pubzinne_show_layout( $pubzinne_post_header );
			}
			do_action( 'pubzinne_action_after_post_title' );
		}
	}
}


// Show post content in the blog posts
if ( ! function_exists( 'pubzinne_show_post_content' ) ) {
	function pubzinne_show_post_content( $args = array(), $otag='', $ctag='' ) {
		$simple = true;
		$post_format = get_post_format();
		$post_format = empty( $post_format ) ? 'standard' : str_replace( 'post-format-', '', $post_format );
		ob_start();
		if ( has_excerpt() ) {
			the_excerpt();
		} elseif ( strpos( get_the_content( '!--more' ), '!--more' ) !== false ) {
			do_action( 'pubzinne_action_before_full_post_content' );
			pubzinne_show_layout( pubzinne_filter_post_content( get_the_content('') ) );
			do_action( 'pubzinne_action_after_full_post_content' );
			$simple = false;
		} elseif ( in_array( $post_format, array( 'link', 'aside', 'status' ) ) ) {
			do_action( 'pubzinne_action_before_full_post_content' );
			pubzinne_show_layout( pubzinne_filter_post_content( get_the_content() ) );
			do_action( 'pubzinne_action_after_full_post_content' );
			$simple = false;
		} elseif ( 'quote' == $post_format ) {
			$quote = pubzinne_get_tag( pubzinne_filter_post_content( get_the_content() ), '<blockquote', '</blockquote>' );
			if ( ! empty( $quote ) ) {
				pubzinne_show_layout( wpautop( $quote ) );
				$simple = false;
			} else {
				pubzinne_show_layout( pubzinne_filter_post_content( get_the_content() ) );
			}
		} elseif ( substr( get_the_content(), 0, 4 ) != '[vc_' ) {
			pubzinne_show_layout( pubzinne_filter_post_content( get_the_content() ) );
		}
		$output = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $output ) ) {
			if ( $simple ) {
				$len = ! empty( $args['hide_excerpt'] )
							? 0
							: ( ! empty( $args['excerpt_length'] )
								? max( 0, (int) $args['excerpt_length'] )
								: pubzinne_get_theme_option( 'excerpt_length' )
								);
				$output = pubzinne_excerpt( $output, $len );
			}
		}
		pubzinne_show_layout( $output, $otag, $ctag);
	}
}


// Show post link 'Read more' in the blog posts
if ( ! function_exists( 'pubzinne_show_post_more_link' ) ) {
	function pubzinne_show_post_more_link( $args = array(), $otag='', $ctag='' ) {
		pubzinne_show_layout(
			'<a class="more-link" href="' . esc_url( get_permalink() ) . '">'
				. ( ! empty( $args['more_text'] )
						? esc_html( $args['more_text'] )
						: esc_html__( 'Read more', 'pubzinne' )
						)
			. '</a>',
			$otag,
			$ctag
		);
	}
}


// Show post link 'View comments' in the blog posts
if ( ! function_exists( 'pubzinne_show_post_comments_link' ) ) {
	function pubzinne_show_post_comments_link( $args = array(), $otag='', $ctag='' ) {
		$total = get_comments_number();
		pubzinne_show_layout(
			'<a class="more-link comments-link" href="' . esc_url( get_comments_link() ) . '">'
				. ( ! empty( $args['comments_text'] )
						? esc_html( $args['comments_text'] )
						: ( $total == 0
							? esc_html__( 'Leave a comment', 'pubzinne' )
							: ( $total == 1 ? esc_html__( 'View comment', 'pubzinne' ) : esc_html__( 'View comments', 'pubzinne' ) )
							)
						)
			. '</a>',
			$otag,
			$ctag
		);
	}
}


// Show single post pagination
if ( ! function_exists( 'pubzinne_show_post_pagination' ) ) {
	function pubzinne_show_post_pagination() {
		do_action( 'pubzinne_action_before_post_pagination' );
		wp_link_pages(
			array(
				'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'pubzinne' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'pubzinne' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			)
		);
		do_action( 'pubzinne_action_after_post_pagination' );
	}
}


// Show single post footer: tags, likes and share
if ( ! function_exists( 'pubzinne_show_post_footer' ) ) {
	function pubzinne_show_post_footer( $components = 'pages,tags,likes,share,author,prev_next' ) {

		$components               = array_map( 'trim', explode( ',', $components ) );
		$meta_components          = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) );
		$share_position           = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'share_position' ) );

		$full_post_loading        = pubzinne_get_value_gp( 'action' ) == 'full_post_loading';
		$pubzinne_posts_navigation = pubzinne_get_theme_option( 'posts_navigation' );

		foreach( $components as $comp ) {

			if ( 'tags' == $comp ) {

				// Post tags
				the_tags( '<div class="post_tags_single"><span class="post_meta_label">' . esc_html__( 'Tags:', 'pubzinne' ) . '</span> ', '', '</div>' );

			} else if ( 'likes' == $comp ) {

				// Emotions
				if ( pubzinne_exists_trx_addons() && function_exists( 'trx_addons_get_post_reactions' ) && trx_addons_is_on( trx_addons_get_option( 'emotions_allowed' ) ) ) {
					trx_addons_get_post_reactions( true );
				}

			} else if ( 'share' == $comp ) {

				// Likes and Share
				$meta_footer = array();
				if ( in_array( 'likes', $components ) && in_array( 'likes', $meta_components )
						&&
						( ! function_exists( 'trx_addons_get_option' ) || trx_addons_is_off( trx_addons_get_option( 'emotions_allowed' ) ) || ! apply_filters( 'trx_addons_filter_show_post_reactions', is_single() && ! is_attachment() ) )
				) {
					$meta_footer[] = 'likes';
				}
				if ( in_array( 'bottom', $share_position ) ) {
					$meta_footer[] = 'share';
				}
				if ( count( $meta_footer) > 0 ) {
					ob_start();
					pubzinne_show_post_meta(
						apply_filters(
							'pubzinne_filter_post_meta_args',
							array(
								'components' => join( ',', $meta_footer ),
								'class'      => 'post_meta_single',
								'share_type' => 'block'
							),
							'single',
							1
						)
					);
					$pubzinne_meta_output = ob_get_contents();
					ob_end_clean();
					if ( ! empty( $pubzinne_meta_output ) ) {
						do_action( 'pubzinne_action_before_post_meta' );
						pubzinne_show_layout( $pubzinne_meta_output );
						do_action( 'pubzinne_action_after_post_meta' );
					}
				}

			} else if ( 'author' == $comp ) {

				// Author bio
				if ( pubzinne_get_theme_option( 'show_author_info' ) == 1
					&& ! is_attachment()
					&& get_the_author_meta( 'description' )
					&& ( 'scroll' != $pubzinne_posts_navigation || pubzinne_get_theme_option( 'posts_navigation_scroll_hide_author' ) == 0 )
					&& ( ! $full_post_loading || pubzinne_get_theme_option( 'open_full_post_hide_author' ) == 0 )
				) {
					do_action( 'pubzinne_action_before_post_author' );
					get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/author-bio' ) );
					do_action( 'pubzinne_action_after_post_author' );
				}

			} else if ( 'prev_next' == $comp ) {

				// Previous/next post navigation.
				if ( 'links' == $pubzinne_posts_navigation && ! $full_post_loading ) {
					do_action( 'pubzinne_action_before_post_navigation' );
					?>
					<div class="nav-links-single<?php
						if ( pubzinne_get_theme_setting( 'thumbs_in_navigation' ) ) {
							echo ' nav-links-with-thumbs';
						}
						if ( ! pubzinne_is_off( pubzinne_get_theme_option( 'posts_navigation_fixed' ) ) ) {
							echo ' nav-links-fixed fixed';
						}
					?>">
						<?php
						the_post_navigation( apply_filters( 'pubzinne_filter_post_navigation_args', array(
							'next_text' => ( pubzinne_get_theme_setting( 'thumbs_in_navigation' ) ? '<span class="nav-arrow"></span>' : '' )
								. '<span class="nav-arrow-label">' . esc_html__( 'Next', 'pubzinne' ) . '</span> '
								. '<h6 class="post-title">%title</h6>'
								. '<span class="post_date">%date</span>',
							'prev_text' => ( pubzinne_get_theme_setting( 'thumbs_in_navigation' ) ? '<span class="nav-arrow"></span>' : '' )
								. '<span class="nav-arrow-label">' . esc_html__( 'Prev', 'pubzinne' ) . '</span> '
								. '<h6 class="post-title">%title</h6>'
								. '<span class="post_date">%date</span>',
						), 'post_footer' ) );
						?>
					</div>
					<?php
					do_action( 'pubzinne_action_after_post_navigation' );
				}

			}
		}
	}
}


// Show post meta block: post date, author, categories, counters, etc.
if ( ! function_exists( 'pubzinne_show_post_meta' ) ) {
	function pubzinne_show_post_meta( $args = array() ) {
		if ( is_single() && pubzinne_is_off( pubzinne_get_theme_option( 'show_post_meta' ) ) ) {
			return ' ';  // Space is need!
		}
		$args = array_merge(
			array(
				'components'      => 'categories,date,author,comments,share,edit',
				'show_labels'     => true,
				'share_type'      => 'drop',
				'share_direction' => 'horizontal',
				'seo'             => false,
				'author_avatar'   => true,
				'date_format'     => '',
				'class'           => '',
				'add_spaces'      => false,
				'echo'            => true,
			),
			$args
		);

		ob_start();

		$components = is_array( $args['components'] ) ? $args['components'] : explode( ',', $args['components'] );

		// Reorder meta_parts with last user's choise
		if ( pubzinne_storage_isset( 'options', 'meta_parts', 'val' ) ) {
			$parts = explode( '|', pubzinne_get_theme_option( 'meta_parts' ) );
			$list_new = array();
			foreach( $parts as $part ) {
				$part = explode( '=', $part );
				if ( in_array( $part[0], $components ) ) {
					$list_new[] = $part[0];
					$components = pubzinne_array_delete_by_value( $components, $part[0] );
				}
			}
			$components = count( $components ) > 0 ? array_merge( $list_new, $components ) : $list_new;
		}

		// Display components
		$dt_last = '';
		foreach ( $components as $comp ) {
			$comp = trim( $comp );
			if ( 'categories' == $comp ) {
				// Label 'Sponsored content' will be shown always before the categories list
				if ( pubzinne_exists_trx_addons() ) {
					$meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
					if ( ! empty( $meta['sponsored_post'] ) && 1 == (int) $meta['sponsored_post'] ) {
						$cats = ( ! empty( $meta['sponsored_url'] )
									? '<a class="post_sponsored_label"'
										. ' href="' . esc_url( $meta['sponsored_url'] ) . '"'
										. ' target="_blank"'
										. ( ! empty( $meta['sponsored_rel_nofollow'] ) || ! empty( $meta['sponsored_rel_sponsored'] )
											? ' rel="'
													. trim( ( ! empty( $meta['sponsored_rel_nofollow'] ) ? 'nofollow ' : '' )
															. ( ! empty( $meta['sponsored_rel_sponsored'] ) ? 'sponsored' : '' )
														)
													. '"'
											: '' )
										. '>'
									: '<span class="post_sponsored_label">' )
								. ( ! empty( $meta['sponsored_label'] )
									? esc_html( $meta['sponsored_label'] )
									: esc_html__( 'Sponsored content', 'pubzinne' )
									)
								. ( ! empty( $meta['sponsored_url'] )
									? '</a>'
									: '</span>'
									);
						pubzinne_show_layout( $cats, '<span class="post_meta_item post_sponsored">', '</span>');
					}
				}
				// Post categories
				$cats = get_post_type() == 'post' ? get_the_category_list( ' ' ) : apply_filters( 'pubzinne_filter_get_post_categories', '' );
				if ( ! empty( $cats ) ) {
					pubzinne_show_layout( $cats, '<span class="post_meta_item post_categories">', '</span>');
				}
			} elseif ( 'date' == $comp || ( 'modified' == $comp && get_post_type() != 'post' ) ) {
				// Published date
				$dt = apply_filters( 'pubzinne_filter_get_post_date', pubzinne_get_date( '', ! empty( $args['date_format'] ) ? $args['date_format'] : '' ) );
				if ( ! empty( $dt ) && ( empty( $dt_last ) || $dt_last != $dt ) ) {
					pubzinne_show_layout(
						$dt,
						'<span class="post_meta_item post_date' . ( ! empty( $args['seo'] ) ? ' date published' : '' ) . '"'
							. ( ! empty( $args['seo'] ) ? ' itemprop="datePublished"' : '' )
							. '>'
							. ( ! is_single() ? '<a href="' . esc_url( get_permalink() ) . '">' : '' )
							. ( in_array( 'date', $components ) && in_array( 'modified', $components ) && get_post_type() == 'post' ? '<span class="post_meta_item_label">' . esc_html__( 'Published:', 'pubzinne' ) . '</span>' : '' ),
						( ! is_single() ? '</a>' : '' ) . '</span>'
					);
					$dt_last = $dt;
				}
			} elseif ( 'modified' == $comp && get_post_type() == 'post' ) {
				// Modified date
				$dt = apply_filters( 'pubzinne_filter_get_post_modified_date', pubzinne_get_date( get_post_modified_time( 'U' ), ! empty( $args['date_format'] ) ? $args['date_format'] : '' ) );
				if ( ! empty( $dt ) && ( empty( $dt_last ) || $dt_last != $dt ) ) {
					pubzinne_show_layout(
						$dt,
						'<span class="post_meta_item post_date' . ( ! empty( $args['seo'] ) ? ' date updated modified' : '' ) . '"'
							. ( ! empty( $args['seo'] ) ? ' itemprop="dateModified"' : '' )
							. '>'
							. ( ! is_single() ? '<a href="' . esc_url( get_permalink() ) . '">' : '' )
							. '<span class="post_meta_item_label">' . esc_html__( 'Updated:', 'pubzinne' ) . '</span>',
						( ! is_single() ? '</a>' : '' ) . '</span>'
					);
					$dt_last = $dt;
				}
			} elseif ( 'author' == $comp ) {
				// Post author
				$author_id = get_the_author_meta( 'ID' );
				if ( empty( $author_id ) && ! empty( $GLOBALS['post']->post_author ) ) {
					$author_id = $GLOBALS['post']->post_author;
				}
				if ( $author_id > 0 ) {
					$author_link   = get_author_posts_url( $author_id );
					$author_name   = get_the_author_meta( 'display_name', $author_id );
					$author_avatar = ! empty( $args['author_avatar'] )
										? get_avatar( get_the_author_meta( 'user_email', $author_id ), apply_filters( 'pubzinne_filter_author_avatar_size', 56, 'post_meta' ) * pubzinne_get_retina_multiplier() ) 
										: '';
					echo '<a class="post_meta_item post_author" rel="author" href="' . esc_url( $author_link ) . '">'
							. ( ! empty( $author_avatar )
								? sprintf( '<span class="post_author_avatar">%s</span>', $author_avatar )
								: '<span class="post_author_by">' . esc_html__( 'By', 'pubzinne' ) . '</span>'
								)
							. '<span class="post_author_name">' . esc_html( $author_name ) . '</span>'
						. '</a>';
				}

			} else if ( 'comments' == $comp ) {
				// Comments
				if ( !is_single() || have_comments() || comments_open() ) {
					$post_comments = get_comments_number();
					echo '<a href="' . esc_url( get_comments_link() ) . '" class="post_meta_item post_meta_comments icon-comment-light">'
							. '<span class="post_meta_number">' . esc_html( pubzinne_num2size( $post_comments ) ) . '</span>'
							. ( $args['show_labels'] ? '<span class="post_meta_label">' . esc_html( _n( 'Comment', 'Comments', $post_comments, 'pubzinne' ) ) . '</span>' : '' )
						. '</a>';
				}

			// Views
			} else if ( 'views' == $comp ) {
				if ( function_exists( 'trx_addons_get_post_views' ) ) {
					$post_views = trx_addons_get_post_views( get_the_ID() );
					echo '<a href="' . esc_url( get_permalink() ) . '" class="post_meta_item post_meta_views trx_addons_icon-eye">'
							. '<span class="post_meta_number">' . esc_html( pubzinne_num2size( $post_views ) ) . '</span>'
							. ( $args['show_labels'] ? '<span class="post_meta_label">' . esc_html( _n( 'View', 'Views', $post_views, 'pubzinne' ) ) . '</span>' : '' )
						. '</a>';
				}

			// Likes (Emotions)
			} else if ( 'likes' == $comp ) {
				if ( function_exists( 'trx_addons_get_post_likes' ) ) {
					$emotions_allowed = trx_addons_is_on( trx_addons_get_option( 'emotions_allowed' ) );
					if ( $emotions_allowed ) {
						$post_emotions = trx_addons_get_post_emotions( get_the_ID() );
						$post_likes = 0;
						if ( is_array( $post_emotions ) ) {
							foreach ( $post_emotions as $v ) {
								$post_likes += (int) $v;
							}
						}
					} else {
						$post_likes = trx_addons_get_post_likes( get_the_ID() );
					}
					$liked = isset( $_COOKIE['trx_addons_likes'] ) ? $_COOKIE['trx_addons_likes'] : '';
					$allow = strpos( sprintf( ',%s,', $liked ), sprintf( ',%d,', get_the_ID() ) ) === false;
					echo ( true == $emotions_allowed
							? '<a href="' . esc_url( trx_addons_add_hash_to_url( get_permalink(), 'trx_addons_emotions' ) ) . '"'
								. ' class="post_meta_item post_meta_emotions trx_addons_icon-angellist">'
							: '<a href="#"'
								. ' class="post_meta_item post_meta_likes trx_addons_icon-heart' . ( ! empty( $allow ) ? '-empty enabled' : ' disabled' ) . '"'
								. ' title="' . ( ! empty( $allow ) ? esc_attr__( 'Like', 'pubzinne') : esc_attr__( 'Dislike', 'pubzinne' ) ) . '"'
								. ' data-postid="' . esc_attr( get_the_ID() ) . '"'
								. ' data-likes="' . esc_attr( $post_likes ) . '"'
								. ' data-title-like="' . esc_attr__( 'Like', 'pubzinne') . '"'
								. ' data-title-dislike="' . esc_attr__( 'Dislike', 'pubzinne' ) . '"'
								. '>'
							)
								. '<span class="post_meta_number">' . esc_html( pubzinne_num2size( $post_likes ) )  . '</span>'
								. ( $args['show_labels']
									? '<span class="post_meta_label">'
										. ( true == $emotions_allowed
											? esc_html( _n( 'Reaction', 'Reactions', $post_likes, 'pubzinne' ) )
											: esc_html( _n( 'Like', 'Likes', $post_likes, 'pubzinne' ) )
											)
									. '</span>'
									: '' )
							. '</a>';
				}

			} elseif ( 'share' == $comp ) {
				// Socials share
				pubzinne_show_share_links(
					array(
						'type'      => $args['share_type'],
						'direction' => $args['share_direction'],
						'caption'   => 'drop' == $args['share_type'] ? esc_html__( 'Share', 'pubzinne' ) : '',
						'before'    => '<span class="post_meta_item post_share">',
						'after'     => '</span>',
					)
				);

			} elseif ( 'edit' == $comp ) {
				// Edit page link
				edit_post_link( esc_html__( 'Edit', 'pubzinne' ), '', '', 0, 'post_meta_item post_edit icon-pencil' );

			} else {
				// Custom counter
				do_action( 'pubzinne_action_show_post_meta', $comp, get_the_ID(), $args );
			}
			// Spaces between post_items
			if ( ! empty( $args['add_spaces'] ) ) {
				echo ' ';
			}
		}

		$rez = ob_get_contents();
		ob_end_clean();

		if ( ! empty( trim( $rez ) ) ) {
			$rez = '<div class="post_meta' . ( ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '' ) . '">'
						. trim( $rez )
					. '</div>';
			if ( $args['echo'] ) {
				pubzinne_show_layout( $rez );
				$rez = '';
			}
		}

		return $rez;
	}
}

// Show post featured block: image, video, audio, etc.
if ( ! function_exists( 'pubzinne_show_post_featured' ) ) {
	function pubzinne_show_post_featured( $args = array() ) {

		$args = array_merge(
			array(
				'popup'         => pubzinne_get_theme_option( 'video_in_popup' ), // Open video in popup
				'hover'         => pubzinne_get_theme_option( 'image_hover' ),    // Hover effect
				'no_links'      => false,                              // Disable links
				'link'          => '',                                 // Alternative (external) link
				'class'         => '',                                 // Additional Class for featured block
				'data'          => array(),                            // Data parameters
				'post_info'     => '',                                 // Additional layout after hover
				'meta_parts'    => array(),                            // String with comma separated meta parts
				'thumb_bg'      => false,                              // Put thumb image as block background or as separate tag
				'thumb_size'    => '',                                 // Image size
				'thumb_ratio'   => '',                                 // Image's ratio for the slider
				'thumb_only'    => false,                              // Display only thumb (without post formats)
				'show_no_image' => pubzinne_is_on( pubzinne_get_theme_setting( 'allow_no_image' ) ),  // Display 'no-image.jpg' if post haven't thumbnail
				'seo'           => pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) ),     // Add SEO-snippets
				'singular'      => false                               // Current page is singular (true) or blog/shortcode (false)
			), $args
		);
		if ( post_password_required() ) {
			return;
		}
		$thumb_size  = ! empty( $args['thumb_size'] )
						? $args['thumb_size']
						: pubzinne_get_thumb_size( is_attachment() || is_single() ? 'full' : 'big' );
		$post_format = str_replace( 'post-format-', '', get_post_format() );
		$no_image    = ! empty( $args['show_no_image'] ) ? pubzinne_get_no_image( '', true ) : '';
		if ( $args['thumb_bg'] ) {
			if ( has_post_thumbnail() ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $thumb_size );
				$image = $image[0];
			} elseif ( 'image' == $post_format ) {
				$image = pubzinne_get_post_image();
				if ( ! empty( $image ) ) {
					$image = pubzinne_add_thumb_size( $image, $thumb_size );
				}
			}
			if ( empty( $image ) ) {
				$image = $no_image;
			}
			if ( ! empty( $image ) ) {
				$args['class'] .= ( $args['class'] ? ' ' : '' ) . 'post_featured_bg' . ' ' . pubzinne_add_inline_css_class( 'background-image: url(' . esc_url( $image ) . ');' );
			}
		}
		if ( ! empty( $args['singular'] ) ) {

			if ( is_attachment() ) {
				?>
				<div class="post_featured post_attachment
				<?php
				if ( $args['class'] ) {
					echo ' ' . esc_attr( $args['class'] );
				}
				?>
				">
				<?php
				if ( ! $args['thumb_bg'] ) {
					echo wp_get_attachment_image(
						get_the_ID(), $thumb_size, false,
						pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) )
													? array( 'itemprop' => 'image' )
													: ''
					);
				}
				if ( pubzinne_get_theme_setting( 'attachments_navigation' ) ) {
					?>
						<nav id="image-navigation" class="navigation image-navigation">
							<div class="nav-previous"><?php previous_image_link( false, '' ); ?></div>
							<div class="nav-next"><?php next_image_link( false, '' ); ?></div>
						</nav><!-- .image-navigation -->
						<?php
				}
				?>
				</div><!-- .post_featured -->
				<?php
				if ( has_excerpt() ) {
					?>
					<div class="entry-caption"><?php the_excerpt(); ?></div><!-- .entry-caption -->
					<?php
				}
			} elseif ( has_post_thumbnail() || ! empty( $args['show_no_image'] ) ) {
				$output = '<div class="post_featured' . ( $args['class'] ? ' ' . esc_attr( $args['class'] ) : '' ) . '"'
					. ( $args['seo'] ? ' itemscope="itemscope" itemprop="image" itemtype="' . esc_attr( pubzinne_get_protocol( true ) ) . '//schema.org/ImageObject"' : '')
					. ( ! empty( $args['thumb_bg'] ) && ! empty( $args['thumb_ratio'] ) ? ' data-ratio="' . esc_attr($args['thumb_ratio']) . '"' : '' );
				if ( ! empty( $args['data'] ) && is_array( $args['data'] ) ) {
					foreach( $args['data'] as $k => $v ) {
						$output .= ' data-' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
					}
				}
				$output .= '>';
				pubzinne_show_layout( $output );
				if ( has_post_thumbnail() && $args['seo'] ) {
					$pubzinne_attr = pubzinne_getimagesize( wp_get_attachment_url( get_post_thumbnail_id() ) );
					?>
						<meta itemprop="width" content="<?php echo esc_attr( $pubzinne_attr[0] ); ?>">
						<meta itemprop="height" content="<?php echo esc_attr( $pubzinne_attr[1] ); ?>">
						<?php
				}
				if ( ! $args['thumb_bg'] ) {
					if ( has_post_thumbnail() ) {
							the_post_thumbnail(
								$thumb_size, array(
									'itemprop' => 'url',
								)
							);
					} elseif ( ! empty( $no_image ) ) {
						?>
						<img
							<?php
							if ( $args['seo'] ) {
								echo ' itemprop="url"';
							}
							?>
							src="<?php echo esc_url( $no_image ); ?>" alt="<?php the_title_attribute( '' ); ?>">
						<?php
					}
				}
				// Put optional info block over the thumb
				pubzinne_show_layout( $args['post_info'] );
				echo '</div><!-- .post_featured -->';
			}

		} else {

			if ( empty( $post_format ) ) {
				$post_format = 'standard';
			}
			$has_thumb = has_post_thumbnail();
			if ( $has_thumb
				|| ! empty( $args['show_no_image'] )
				|| ( ! $args['thumb_only']
						&& ( in_array( $post_format, array( 'image', 'audio', 'video' ) )
							|| ( 'gallery' == $post_format && pubzinne_exists_trx_addons() )
							)
					)
			) {
				$output = '<div class="post_featured '
					. ( ! empty( $has_thumb ) || 'image' == $post_format || ! empty( $args['show_no_image'] )
						? ( 'with_thumb' . ( $args['thumb_only']
												|| ( ! in_array( $post_format, array( 'audio', 'video', 'gallery' ) ) && empty( $args['video'] ) )
												|| ( 'gallery' == $post_format && ( $has_thumb || $args['thumb_bg'] ) )
													? ' hover_' . esc_attr( $args['hover'] )
													: ( in_array( $post_format, array( 'video' ) ) || ! empty( $args['video'] ) ? ' hover_play' : '' )
											)
							)
						: 'without_thumb' )
					. ( ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '' )
					. '"'
					. ( ! empty( $args['thumb_bg'] ) && ! empty( $args['thumb_ratio'] ) ? ' data-ratio="' . esc_attr($args['thumb_ratio']) . '"' : '' );
				if ( ! empty( $args['data'] ) && is_array( $args['data'] ) ) {
					foreach( $args['data'] as $k => $v ) {
						$output .= ' data-' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
					}
				}
				$output .= '>';
				pubzinne_show_layout( $output );
				// Put the thumb or gallery or image or video from the post
				if ( $args['thumb_bg'] ) {
					if ( ! empty( $args['hover'] ) ) {
						?>
						<div class="mask"></div>
						<?php
					}
					if ( ! in_array( $post_format, array( 'audio', 'video', 'gallery' ) ) && empty( $args['video'] ) ) {
						pubzinne_hovers_add_icons(
							$args['hover'],
							array(
								'no_links'   => $args['no_links'],
								'link'       => $args['link'],
								'post_info'  => $args['post_info'],
								'meta_parts' => $args['meta_parts'],
							)
						);
					}
				} elseif ( $has_thumb ) {
					the_post_thumbnail(
						$thumb_size, array()
					);
					if ( ! empty( $args['hover'] ) ) {
						?>
						<div class="mask"></div>
						<?php
					}
					if ( $args['thumb_only'] || ( ! in_array( $post_format, array( 'audio', 'video', 'gallery' ) ) && empty( $args['video'] ) ) ) {
						pubzinne_hovers_add_icons(
							$args['hover'],
							array(
								'no_links'   => $args['no_links'],
								'link'       => $args['link'],
								'post_info'  => $args['post_info'],
								'meta_parts' => $args['meta_parts'],
							)
						);
					}
				} elseif ( 'image' == $post_format ) {
					$image = pubzinne_get_post_image();
					if ( ! empty( $image ) ) {
						$image = pubzinne_add_thumb_size( $image, $thumb_size );
						?>
						<img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(''); ?>">
						<?php
						if ( ! empty( $args['hover'] ) ) {
							?>
							<div class="mask"></div>
							<?php
						}
						if ( empty( $args['video'] ) ) {
							pubzinne_hovers_add_icons(
								$args['hover'],
								array(
									'no_links'   => $args['no_links'],
									'link'       => $args['link'],
									'image'      => $image,
									'post_info'  => $args['post_info'],
									'meta_parts' => $args['meta_parts'],
								)
							);
						}
					}
				} elseif ( ! empty( $args['show_no_image'] ) && ! empty( $no_image ) ) {
					?>
					<img src="<?php echo esc_url( $no_image ); ?>" alt="<?php the_title_attribute(''); ?>">
					<?php
					if ( ! empty( $args['hover'] ) ) {
						?>
						<div class="mask"></div>
						<?php
					}
					if ( empty( $args['video'] ) ) {
						pubzinne_hovers_add_icons(
							$args['hover'],
							array(
								'no_links'   => $args['no_links'],
								'link'       => $args['link'],
								'post_info'  => $args['post_info'],
								'meta_parts' => $args['meta_parts'],
							)
						);
					}
				}
				// Add audio, video or gallery
				if ( ! $args['thumb_only'] && ( in_array( $post_format, array( 'video', 'audio', 'gallery' ) ) || ! empty( $args['video'] ) ) ) {
					$post_content = pubzinne_get_post_content();
					$post_content_parsed = $post_content;

					if ( 'video' == $post_format || ! empty( $args['video'] ) ) {
						$video = ! empty( $args['video'] ) ? $args['video'] : pubzinne_get_post_video( $post_content, false );
						if ( empty( $video ) ) {
							$video = pubzinne_get_post_iframe( $post_content, false );
						}
						if ( empty( $video ) ) {
							// Only get video from the content if a playlist isn't present.
							$post_content_parsed = pubzinne_filter_post_content( $post_content );
							if ( false === strpos( $post_content_parsed, 'wp-playlist-script' ) ) {
								$videos = get_media_embedded_in_content( $post_content_parsed, array( 'video', 'object', 'embed', 'iframe' ) );
								if ( ! empty( $videos ) && is_array( $videos ) ) {
									$video = pubzinne_array_get_first( $videos, false );
								}
							}
						}
						if ( ! empty( $video ) ) {
							$video_out = false;
							if ( $has_thumb && ! empty( $args['popup'] ) && function_exists( 'trx_addons_get_video_layout' ) ) {
								$popup = explode(
												'<!-- .sc_layouts_popup -->',
												trx_addons_get_video_layout( array(
																				'link'  => '',
																				'embed' => $video,
																				'cover' => get_post_thumbnail_id(),
																				'show_cover' => false,
																				'popup' => true
																				)
																			)
												);
								if ( ! empty( $popup[0] ) && ! empty( $popup[1] ) ) {
									if ( preg_match( '/<a .*<\/a>/', $popup[0], $matches ) && ! empty( $matches[0] ) ) {
										$video_out = true;
										?>
										<div class="post_video_hover post_video_hover_popup"><?php pubzinne_show_layout( $matches[0] ); ?></div>
										<?php
										pubzinne_show_layout($popup[1]);
									}
								}
							}
							if ( ! $video_out ) {
								if ( $has_thumb ) {
									$video = pubzinne_make_video_autoplay( $video );
									?>
									<div class="post_video_hover" data-video="<?php echo esc_attr( $video ); ?>"></div>
									<?php
								}
								?>
								<div class="post_video video_frame">
									<?php
									if ( ! $has_thumb ) {
										pubzinne_show_layout( $video );
									}
									?>
								</div>
								<?php
							}
						}

					} elseif ( 'audio' == $post_format ) {
						// Put audio over the thumb
						$audio = pubzinne_get_post_audio( $post_content, false );
						if ( empty( $audio ) ) {
							$audio = pubzinne_get_post_iframe( $post_content, false );
						}
						// Apply filters to get audio, title and author
						$post_content_parsed = pubzinne_filter_post_content( $post_content );
						if ( empty( $audio ) ) {
							// Only get audio from the content if a playlist isn't present.
							if ( false === strpos( $post_content_parsed, 'wp-playlist-script' ) ) {
								$audios = get_media_embedded_in_content( $post_content_parsed, array( 'audio' ) );
								if ( ! empty( $audios ) && is_array( $audios ) ) {
									$audio = pubzinne_array_get_first( $audios, false );
								}
							}
						}
						if ( ! empty( $audio ) ) {
							?>
							<div class="post_audio
								<?php
								if ( strpos( $audio, 'soundcloud' ) !== false ) {
									echo ' with_iframe';
								}
								?>
							">
								<?php
								// Get author and audio title
								$media_author = '';
								$media_title  = '';
								if ( strpos( $audio, '<audio' ) !== false ) {
									$media_author = pubzinne_get_tag_attrib( $audio, '<audio>', 'data-author' );
									$media_title  = pubzinne_get_tag_attrib( $audio, '<audio>', 'data-caption' );
								}
								if ( empty( $media_author) &&  empty( $media_title) ) {
									$media = urldecode( pubzinne_get_tag_attrib( $post_content, '[trx_widget_audio]', 'media' ) );
									if ( ! empty( $media ) ) {
										// Shortcode found in the content
									 	if ( '[{' == substr( $media, 0, 2 ) ) {
											$media = json_decode( $media, true );
											if ( is_array( $media ) ) {
												if ( !empty( $media[0]['author'] ) ) {
													$media_author = $media[0]['author'];
												}
												if ( !empty( $media[0]['caption'] ) ) {
													$media_title = $media[0]['caption'];
												}
											}
										}
									} else {
										// Parse tag params
										$media_author = strip_tags( pubzinne_get_tag( $post_content_parsed, '<h6 class="audio_author">', '</h6>' ) );
										$media_title  = strip_tags( pubzinne_get_tag( $post_content_parsed, '<h5 class="audio_caption">', '</h5>' ) );

									}
								}
								if ( ! empty( $media_author) || ! empty( $media_title) ) {
									?>
									<div class="audio_info">
										<?php
										if ( ! empty( $media_author ) ) {
											?>
											<div class="post_audio_author"><?php pubzinne_show_layout( $media_author ); ?></div>
											<?php
										}
										if ( ! empty( $media_title ) ) {
											?>
											<h5 class="post_audio_title"><?php pubzinne_show_layout( $media_title ); ?></h5>
											<?php
										}
										?>
									</div>
									<?php
								}
								// Display audio
								pubzinne_show_layout( $audio, '<div class="audio_wrap">', '</div>' );
								?>
							</div>
							<?php
						}

					} elseif ( 'gallery' == $post_format ) {
						$slider_args = array(
							'thumb_size' => $thumb_size,
							'controls'   => 'yes',
							'pagination' => 'yes',
						);
						if ( !empty( $args['thumb_ratio'] ) ) {
							$slider_args['slides_ratio'] = $args['thumb_ratio'];
						}
						$output = pubzinne_get_slider_layout( $slider_args );
						if ( '' != $output ) {
							pubzinne_show_layout( $output );
						}
					}
				}
				// Put optional info block over the thumb
				pubzinne_show_layout( $args['post_info'] );
				// Close div.post_featured
				echo '</div>';
			} else {
				// Put optional info block over the thumb
				pubzinne_show_layout( $args['post_info'] );
			}
		}
	}
}


// Return path to the 'no-image'
if ( ! function_exists( 'pubzinne_get_no_image' ) ) {
	function pubzinne_get_no_image( $no_image = '', $need = false ) {
		static $no_image_url = '';
		$img = pubzinne_get_theme_option( 'no_image' );
		if ( empty( $img ) && ( $need || pubzinne_get_theme_setting( 'allow_no_image' ) ) ) {
			if ( empty( $no_image_url ) ) {
				$no_image_url = pubzinne_get_file_url( 'images/no-image.jpg' );
			}
			$img = $no_image_url;
		}
		if ( ! empty( $img ) ) {
			$no_image = $img;
		}
		return $no_image;
	}
}


// Add featured image as background image to post navigation elements.
if ( ! function_exists( 'pubzinne_add_bg_in_post_nav' ) ) {
	function pubzinne_add_bg_in_post_nav() {
		if ( ! is_single() || ! pubzinne_get_theme_setting( 'thumbs_in_navigation' ) ) {
			return;
		}

		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );
		$css      = '';
		$noimg    = pubzinne_get_no_image();

		if ( is_attachment() && 'attachment' == $previous->post_type ) {
			return;
		}

		if ( $previous ) {
			$img = '';
			if ( has_post_thumbnail( $previous->ID ) ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), pubzinne_get_thumb_size( 'med' ) );
				$img = $img[0];
			} else {
				$img = $noimg;
			}
			if ( ! empty( $img ) ) {
				$css .= '.post-navigation .nav-previous a .nav-arrow { background-image: url(' . esc_url( $img ) . '); }';
			} else {
				$css .= '.nav-links-single .nav-links .nav-previous a { padding-left: 0; }'
					. '.post-navigation .nav-previous a .nav-arrow { display: none; background-color: rgba(128,128,128,0.05); border: 1px solid rgba(128,128,128,0.1); }'
					. '.post-navigation .nav-previous a .nav-arrow:after { top: 0; opacity: 1; }'
					. '.nav-links-single.nav-links-with-thumbs .nav-links .nav-previous a .post-title{padding-left:0;}'
					. '.nav-links-single.nav-links-with-thumbs .nav-links .nav-previous a .post_date{padding-left:0;}';
			}
		}

		if ( $next ) {
			$img = '';
			if ( has_post_thumbnail( $next->ID ) ) {
				$img = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), pubzinne_get_thumb_size( 'med' ) );
				$img = $img[0];
			} else {
				$img = $noimg;
			}
			if ( ! empty( $img ) ) {
				$css .= '.post-navigation .nav-next a .nav-arrow { background-image: url(' . esc_url( $img ) . '); }';
			} else {
				$css .= '.nav-links-single .nav-links .nav-next a { padding-right: 0; }'
					. '.post-navigation .nav-next a .nav-arrow { display: none; background-color: rgba(128,128,128,0.05); border: 1px solid rgba(128,128,128,0.1); }'
					. '.post-navigation .nav-next a .nav-arrow:after { top: 0; opacity: 1; }'
					. '.nav-links-single.nav-links-with-thumbs .nav-links .nav-next a .post-title{padding-right:0;}'
					. '.nav-links-single.nav-links-with-thumbs .nav-links .nav-next a .post_date{padding-right:0;}';
			}
		}

		pubzinne_add_inline_css( $css );
	}
}

// Show related posts
if ( ! function_exists( 'pubzinne_show_related_posts' ) ) {
	function pubzinne_show_related_posts( $args = array(), $style = 1, $title = '' ) {
		$args = array_merge(
			array(
				//  Attention! Parameter 'suppress_filters' is damage WPML-queries!
				'ignore_sticky_posts' => true,
				'posts_per_page'      => 2,
				'columns'             => 0,
				'orderby'             => 'rand',
				'order'               => 'DESC',
				'post_type'           => '',
				'post_status'         => 'publish',
				'post__not_in'        => array(),
				'category__in'        => array(),
			), $args
		);

		if ( empty( $args['post_type'] ) ) {
			$args['post_type'] = get_post_type();
		}

		$taxonomy = 'post' == $args['post_type'] ? 'category' : pubzinne_get_post_type_taxonomy();

		$args['post__not_in'][] = get_the_ID();

		if ( empty( $args['columns'] ) ) {
			$args['columns'] = $args['posts_per_page'];
		}

		if ( empty( $args['category__in'] ) || is_array( $args['category__in'] ) && count( $args['category__in'] ) == 0 ) {
			$post_categories_ids = array();
			$post_cats           = get_the_terms( get_the_ID(), $taxonomy );
			if ( is_array( $post_cats ) && ! empty( $post_cats ) ) {
				foreach ( $post_cats as $cat ) {
					$post_categories_ids[] = $cat->term_id;
				}
			}
			$args['category__in'] = $post_categories_ids;
		}

		if ( 'post' != $args['post_type'] && count( $args['category__in'] ) > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_taxonomy_id',
					'terms'    => $args['category__in'],
				),
			);
			unset( $args['category__in'] );
		}

		$query = new WP_Query( $args );

		if ( $query->found_posts > 0 ) {
			$slider_args = array();
			$columns = intval( max( 1, min( 6, $args['columns'] ) ) );
			$args['slider'] = (int) pubzinne_get_theme_option( 'related_slider' ) && min( $args['posts_per_page'], $query->found_posts) > $columns;
			$related_position = pubzinne_get_theme_option( 'related_position' );
			$related_style = pubzinne_get_theme_option( 'related_style' );
			$related_tag = strpos( $related_position, 'inside' ) === 0 ? 'h5' : 'h3';
			if ( in_array( $related_position, array( 'inside_left', 'inside_right' ) ) ) {
				$columns = 1;
			}
			?>
			<section class="related_wrap related_position_<?php echo esc_attr( $related_position ); ?> related_style_<?php echo esc_attr( $related_style ); ?>">
				<<?php echo esc_html( $related_tag ); ?> class="section_title related_wrap_title"><?php
					if ( ! empty( $title ) ) {
						echo esc_html( $title );
					} else {
						esc_html_e( 'You May Also Like', 'pubzinne' );
					}
				?></<?php echo esc_html( $related_tag ); ?>><?php
				if ( $args['slider'] ) {
					$slider_args                      = $args;
					$slider_args['count']             = max(1, $query->found_posts);
					$slider_args['slides_min_width']  = 250;
					$slider_args['slides_space']      = pubzinne_get_theme_option( 'related_slider_space' );
					$slider_args['slider_controls']   = pubzinne_get_theme_option( 'related_slider_controls' );
					$slider_args['slider_pagination'] = pubzinne_get_theme_option( 'related_slider_pagination' );
					$slider_args                      = apply_filters( 'pubzinne_related_posts_slider_args', $slider_args, $query );
					?><div class="related_wrap_slider"><?php
					pubzinne_get_slider_wrap_start('related_posts_wrap', $slider_args);
				} else {
					?><div class="columns_wrap posts_container columns_padding_bottom"><?php
				}
					while ( $query->have_posts() ) {
						$query->the_post();
						if ( $args['slider'] ) {
							?><div class="slider-slide swiper-slide"><?php
						} else {
							?><div class="column-1_<?php echo intval( max( 1, min( 4, $columns ) ) ); ?>"><?php
						}
						if ( ! apply_filters( 'pubzinne_filter_related_post_showed', false, $args, $style ) ) {
							get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/related-posts', $style ), $style );
						}
						?></div><?php
					}
				?></div><!-- /.swiper-wrapper || .columns_wrap --><?php
				if ( $args['slider'] ) {
					pubzinne_get_slider_wrap_end('related_posts_wrap', $slider_args);
					?></div><!-- /.related_wrap_slider --><?php
				}
				wp_reset_postdata();
				?>
			</section><!-- /.related_wrap -->
			<?php
		}
	}
}

// Callback for action 'Related posts'
if ( ! function_exists( 'pubzinne_show_related_posts_callback' ) ) {
	add_action( 'pubzinne_action_related_posts', 'pubzinne_show_related_posts_callback' );
	function pubzinne_show_related_posts_callback() {
		if ( is_single() && ! apply_filters( 'pubzinne_filter_show_related_posts', false ) ) {
			$pubzinne_related_posts   = (int) pubzinne_get_theme_option( 'related_posts' );
			$pubzinne_related_columns = (int) pubzinne_get_theme_option( 'related_columns' );
			$pubzinne_related_style   = pubzinne_get_theme_option( 'related_style' );
			if ( (int) pubzinne_get_theme_option( 'show_related_posts' ) && $pubzinne_related_posts > 0 ) {
				pubzinne_show_related_posts(
					array(
						'orderby'        => 'rand',
						'posts_per_page' => max( 1, min( 9, $pubzinne_related_posts ) ),
						'columns'        => max( 1, min( 6, $pubzinne_related_posts, $pubzinne_related_columns ) ),
					),
					$pubzinne_related_style
				);
			}
		}
	}
}


// Return true if blog style use masonry
if ( ! function_exists( 'pubzinne_is_blog_style_use_masonry' ) ) {
	function pubzinne_is_blog_style_use_masonry( $style ) {
		$blog_styles = pubzinne_storage_get( 'blog_styles' );
		return ! empty( $blog_styles[ $style ][ 'scripts' ] ) && in_array( 'masonry', (array) $blog_styles[ $style ][ 'scripts'] );
	}
}

// Show tabs with blog filters
if ( ! function_exists( 'pubzinne_show_filters' ) ) {
	function pubzinne_show_filters( $args = array() ) {
		$args = array_merge(
			array(
				'post_type'  => '',
				'taxonomy'   => '',
				'parent_cat' => 0,
				'posts_per_page' => 0,
			), $args
		);
		// Query terms
		$query_args = array(
			'type'         => ! empty( $args['post_type'] ) ? $args['post_type'] : 'post',
			'taxonomy'     => ! empty( $args['taxonomy'] ) ? $args['taxonomy'] : 'category',
			'child_of'     => $args['parent_cat'],
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 0,
			'pad_counts'   => false,
		);
		$terms = get_terms( $args );
		$tabs = array();
		if ( is_array( $terms ) && count( $terms ) > 0 ) {
			$tabs[ $args['parent_cat'] ] = esc_html__( 'All', 'pubzinne' );
			foreach ( $terms as $term ) {
				if ( isset( $term->term_id ) ) {
					$tabs[ $term->term_id ] = $term->name;
				}
			}
		}
		if ( count( $tabs ) > 0 ) {
			$filters_ajax   = pubzinne_get_theme_setting( 'blog_filters_use_ajax' );
			$filters_active = $args['parent_cat'];
			$filters_id     = 'pubzinne_filters';
			?>
			<div class="pubzinne_tabs pubzinne_tabs_ajax pubzinne_filters">
				<ul class="pubzinne_tabs_titles">
					<?php
					foreach ( $tabs as $tab_id => $tab_title ) {
						?>
						<li><a href="<?php
							echo esc_url( pubzinne_get_hash_link( sprintf( '#%1$s_%2$s_content', $filters_id, $tab_id ) ) );
							?>" data-tab="<?php echo esc_attr( $tab_id ); ?>"><?php echo esc_html( $tab_title ); ?></a></li>
						<?php
					}
					?>
				</ul>
				<?php
				foreach ( $tabs as $tab_id => $tab_title ) {
					$tab_need_content = $tab_id == $filters_active || ! $filters_ajax;
					?>
					<div id="<?php echo esc_attr( sprintf( '%1$s_%2$s_content', $filters_id, $tab_id ) ); ?>"
						class="pubzinne_tabs_content"
						data-blog-template="<?php echo esc_attr( pubzinne_storage_get( 'blog_template' ) ); ?>"
						data-blog-style="<?php echo esc_attr( $args['blog_style'] ); ?>"
						data-post-type="<?php echo esc_attr( $args['post_type'] ); ?>"
						data-taxonomy="<?php echo esc_attr( $args['taxonomy'] ); ?>"
						data-parent-cat="<?php echo esc_attr( $args['parent_cat'] ); ?>"
						data-cat="<?php echo esc_attr( $tab_id ); ?>"
						data-posts-per-page="<?php echo esc_attr( $args['posts_per_page'] ); ?>"
						data-need-content="<?php echo ( false === $tab_need_content ? 'true' : 'false' ); ?>"
					>
						<?php
						if ( $tab_need_content ) {
							pubzinne_show_posts( array_merge( $args, array( 'cat' => $tab_id ) ) );
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		} else {
			pubzinne_show_posts( array_merge( $args, array( 'cat' => $args['parent_cat'] ) ) );
		}
	}
}


// Show portfolio posts
if ( ! function_exists( 'pubzinne_show_posts' ) ) {
	function pubzinne_show_posts( $args = array() ) {
		$args = array_merge(
			array(
				'post_type'      => 'post',
				'taxonomy'       => 'category',
				'parent_cat'     => 0,
				'cat'            => 0,
				'posts_per_page' => (int) pubzinne_get_theme_option( 'posts_per_page' ),
				'page'           => 1,
				'sticky'         => false,
				'blog_style'     => '',
				'echo'           => true,
			), $args
		);

		$blog_styles  = pubzinne_storage_get( 'blog_styles' );

		$blog_style   = empty( $args['blog_style'] ) ? pubzinne_get_theme_option( 'blog_style' ) : $args['blog_style'];
		$parts        = explode( '_', $blog_style );
		$style        = $parts[0];
		$columns      = empty( $parts[1] ) ? 1 : max( 1, $parts[1] );

		$custom_style = 'none';
		if ( strpos( $style, 'blog-custom-' ) === 0 ) {
			$custom_blog_id   = pubzinne_get_custom_blog_id( $style );
			$custom_blog_meta = pubzinne_get_custom_layout_meta( $custom_blog_id );
			if ( ! empty( $custom_blog_meta['margin'] ) ) {
				pubzinne_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( pubzinne_prepare_css_value( $custom_blog_meta['margin'] ) ) ) );
			}
			if ( ! empty( $custom_blog_meta['scripts_required'] ) ) {
				$custom_style = $custom_blog_meta['scripts_required'];
			}
		}

		if ( ! $args['echo'] ) {
			ob_start();

			$q_args = array(
				'post_status' => current_user_can( 'read_private_pages' ) && current_user_can( 'read_private_posts' )
										? array( 'publish', 'private' )
										: 'publish',
			);
			$q_args = pubzinne_query_add_posts_and_cats( $q_args, '', $args['post_type'], $args['cat'], $args['taxonomy'] );
			if ( $args['page'] > 1 ) {
				$q_args['paged']               = $args['page'];
				$q_args['ignore_sticky_posts'] = true;
			}
			if ( 0 != $args['posts_per_page'] ) {
				$q_args['posts_per_page'] = $args['posts_per_page'];
			}

			// Make a new query
			$q             = 'wp_query';
			$GLOBALS[ $q ] = new WP_Query( $q_args );
		}

		// Show posts
		$class = 'posts_container'
				. sprintf( ' %1$s_wrap %1$s_%2$d', $style, $columns )
				. ( ! pubzinne_is_off( $custom_style )
					? sprintf( ' %s_wrap', $custom_style ) . ( 'masonry' == $custom_style ? sprintf( ' masonry_%d', $columns ) : '' )
					: ( pubzinne_is_blog_style_use_masonry( $style )
						?  sprintf( ' masonry_wrap masonry_%1$d', $columns )
						: ( $columns > 1
							? ' columns_wrap columns_padding_bottom'
							: ''
							)
						)
					);
		if ( $args['sticky'] ) {
			?>
			<div class="sticky_wrap columns_wrap">
			<?php
		} else {
			if ( pubzinne_get_theme_option( 'first_post_large' ) && ! is_paged() && ! in_array( pubzinne_get_theme_option( 'body_style' ), array( 'fullwide', 'fullscreen' ) ) && have_posts() ) {
				the_post();
				get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/content', 'excerpt' ), 'excerpt' );
			}
			?>
			<div class="<?php echo esc_attr( $class ); ?>">
			<?php
		}

		while ( have_posts() ) {
			the_post();
			if ( $args['sticky'] && ! is_sticky() ) {
				$args['sticky'] = false;
				?>
				</div><div class="<?php echo esc_attr( $class ); ?>">
				<?php
			}
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', $args['sticky'] && is_sticky() ? 'templates/content-sticky' : pubzinne_blog_item_get_template( $blog_style ) ) );
		}

		?>
		</div>
		<?php

		pubzinne_show_pagination();

		if ( ! $args['echo'] ) {
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
}

// AJAX handler for the pubzinne_ajax_get_posts action
if ( ! function_exists( 'pubzinne_ajax_get_posts_callback' ) ) {
	add_action( 'wp_ajax_pubzinne_ajax_get_posts', 'pubzinne_ajax_get_posts_callback' );
	add_action( 'wp_ajax_nopriv_pubzinne_ajax_get_posts', 'pubzinne_ajax_get_posts_callback' );
	function pubzinne_ajax_get_posts_callback() {
		pubzinne_verify_nonce();

		$id = ! empty( $_REQUEST['blog_template'] ) ? wp_kses_data( wp_unslash( $_REQUEST['blog_template'] ) ) : 0;
		if ( (int)$id > 0 ) {
			pubzinne_storage_set( 'blog_archive', true );
			pubzinne_storage_set( 'blog_mode', 'blog' );
			pubzinne_storage_set( 'options_meta', get_post_meta( $id, 'pubzinne_options', true ) );
		}

		$response = array(
			'error' => '',
			'data'  => pubzinne_show_posts(
				array(
					'cat'        => intval( wp_unslash( $_REQUEST['cat'] ) ),
					'parent_cat' => intval( wp_unslash( $_REQUEST['parent_cat'] ) ),
					'page'       => intval( wp_unslash( $_REQUEST['page'] ) ),
					'post_type'  => trim( wp_unslash( $_REQUEST['post_type'] ) ),
					'taxonomy'   => trim( wp_unslash( $_REQUEST['taxonomy'] ) ),
					'blog_style' => trim( wp_unslash( $_REQUEST['blog_style'] ) ),
					'echo'       => false,
				)
			),
			'css'  => pubzinne_get_inline_css(),
		);

		if ( empty( $response['data'] ) ) {
			$response['error'] = esc_html__( 'Sorry, but nothing matched your search criteria.', 'pubzinne' );
		}

		pubzinne_ajax_response( $response );
	}
}


// Show pagination
if ( ! function_exists( 'pubzinne_show_pagination' ) ) {
	function pubzinne_show_pagination( $args = array() ) {
		global $wp_query;
		$pagination = ! empty( $args[ 'pagination' ] )
						? $args[ 'pagination' ]
						: pubzinne_get_theme_option( 'blog_pagination' );
		$prefix     = ! empty( $args[ 'class_prefix' ] )
						? $args[ 'class_prefix' ]
						: 'nav';
		if ( 'pages' == $pagination ) {
			pubzinne_show_layout( str_replace( "\n", '', get_the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => esc_html__( '<', 'pubzinne' ),
					'next_text'          => esc_html__( '>', 'pubzinne' ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'pubzinne' ) . ' </span>',
				)
			) ) );
		} elseif ( 'more' == $pagination || 'infinite' == $pagination ) {
			$page_number = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );
			if ( $page_number < $wp_query->max_num_pages ) {
				?>
				<div class="<?php echo esc_attr( $prefix ); ?>-links-more
					<?php
					if ( 'infinite' == $pagination ) {
						echo ' ' . esc_attr( $prefix ) . '-links-infinite';
					}
					?>
				">
					<a class="<?php echo esc_attr( $prefix ); ?>-load-more" href="#" 
						data-page="<?php echo esc_attr( $page_number ); ?>" 
						data-max-page="<?php echo esc_attr( $wp_query->max_num_pages ); ?>"
						><span><?php esc_html_e( 'Load more posts', 'pubzinne' ); ?></span></a>
				</div>
				<?php
			}
		} elseif ( 'links' == $pagination ) {
			?>
			<div class="<?php echo esc_attr( $prefix ); ?>-links-old">
				<span class="<?php echo esc_attr( $prefix ); ?>-prev"><?php previous_posts_link( is_search() ? esc_html__( 'Previous posts', 'pubzinne' ) : esc_html__( 'Newest posts', 'pubzinne' ) ); ?></span>
				<span class="<?php echo esc_attr( $prefix ); ?>-next"><?php next_posts_link( is_search() ? esc_html__( 'Next posts', 'pubzinne' ) : esc_html__( 'Older posts', 'pubzinne' ), $wp_query->max_num_pages ); ?></span>
			</div>
			<?php
		}
	}
}



// Return template for the single field in the comments
if ( ! function_exists( 'pubzinne_single_comments_field' ) ) {
	function pubzinne_single_comments_field( $args ) {
		$path_height = 'path' == $args['form_style']
							? ( 'text' == $args['field_type'] ? 75 : 190 )
							: 0;
		$html = '<div class="comments_field comments_' . esc_attr( $args['field_name'] ) . '">'
					. ( 'default' == $args['form_style'] && 'checkbox' != $args['field_type']
						? '<label for="' . esc_attr( $args['field_name'] ) . '" class="' . esc_attr( $args['field_req'] ? 'required' : 'optional' ) . '">' . esc_html( $args['field_title'] ) . '</label>'
						: ''
						)
					. '<span class="sc_form_field_wrap">';
		if ( 'text' == $args['field_type'] ) {
			$html .= '<input id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '" type="text"' . ( 'default' == $args['form_style'] ? ' placeholder="' . esc_attr( $args['field_placeholder'] ) . ( $args['field_req'] ? ' *' : '' ) . '"' : '' ) . ' value="' . esc_attr( $args['field_value'] ) . '"' . ( $args['field_req'] ? ' aria-required="true"' : '' ) . ' />';
		} elseif ( 'checkbox' == $args['field_type'] ) {
			$html .= '<input id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '" type="checkbox" value="' . esc_attr( $args['field_value'] ) . '"' . ( $args['field_req'] ? ' aria-required="true"' : '' ) . ' />'
					. ' <label for="' . esc_attr( $args['field_name'] ) . '" class="' . esc_attr( $args['field_req'] ? 'required' : 'optional' ) . '">' . wp_kses( $args['field_title'], 'pubzinne_kses_content' ) . '</label>';
		} else {
			$html .= '<textarea id="' . esc_attr( $args['field_name'] ) . '" name="' . esc_attr( $args['field_name'] ) . '"' . ( 'default' == $args['form_style'] ? ' placeholder="' . esc_attr( $args['field_placeholder'] ) . ( $args['field_req'] ? ' *' : '' ) . '"' : '' ) . ( $args['field_req'] ? ' aria-required="true"' : '' ) . '></textarea>';
		}
		if ( 'default' != $args['form_style'] && in_array( $args['field_type'], array( 'text', 'textarea' ) ) ) {
			$html .= '<span class="sc_form_field_hover">'
						. ( 'path' == $args['form_style']
							? '<svg class="sc_form_field_graphic" preserveAspectRatio="none" viewBox="0 0 520 ' . intval( $path_height ) . '" height="100%" width="100%"><path d="m0,0l520,0l0,' . intval( $path_height ) . 'l-520,0l0,-' . intval( $path_height ) . 'z"></svg>'
							: ''
							)
						. ( 'iconed' == $args['form_style']
							? '<i class="sc_form_field_icon ' . esc_attr( $args['field_icon'] ) . '"></i>'
							: ''
							)
						. '<span class="sc_form_field_content" data-content="' . esc_attr( $args['field_title'] ) . '">' . wp_kses( $args['field_title'], 'pubzinne_kses_content' ) . '</span>'
					. '</span>';
		}
		$html .= '</span></div>';
		return $html;
	}
}
