<?php
/**
 * Social share and profiles
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'trx_addons_get_share_url' ) ) {
	/**
	 * Return URL for the share button (socials share) in the specified social network
	 * 
	 * @trigger trx_addons_filter_share_links
	 * 
	 * @param string $soc		social network name
	 * 
	 * @return string			URL for the share button
	 */
	function trx_addons_get_share_url( $soc = '' ) {
		$list = apply_filters( 'trx_addons_filter_share_links', array(
			'blogger' =>		'//www.blogger.com/blog_this.pyra?t&u={link}&n={title}',
			'delicious' =>		'//delicious.com/save?url={link}&title={title}&note={descr}',
			'designbump' =>		'//designbump.com/node/add/drigg/?url={link}&title={title}',
			'designfloat' =>	'//www.designfloat.com/submit.php?url={link}',
			'digg' =>			'//digg.com/submit?url={link}',
			'evernote' =>		'//www.evernote.com/clip.action?url={link}&title={title}',
			'email' =>			'mailto:'.get_bloginfo('admin_email').'?subject={title}&body={link}',
//			'facebook' =>		'//www.facebook.com/sharer.php?s=100&p[url]={link}&p[title]={title}&p[summary]={descr}&p[images][0]={image}',
			'facebook' =>		'//www.facebook.com/sharer/sharer.php?u={link}',
			'friendfeed' =>		'//www.friendfeed.com/share?title={title} - {link}',
			'google' =>			'//www.google.com/bookmarks/mark?op=edit&output=popup&bkmk={link}&title={title}&annotation={descr}',
			'gplus' => 			'//plus.google.com/share?url={link}', 
			'identi' => 		'//identi.ca/notice/new?status_textarea={title} - {link}', 
			'juick' => 			'//www.juick.com/post?body={title} - {link}',
			'link' =>			'json:{"link": "#",'
									. '"attributes": {'
													. '"title": "' . esc_attr__( 'Copy URL to clipboard', 'trx_addons' ) . '",'
													. '"data-message": "' . esc_attr__( 'Copied!', 'trx_addons' ) . '",'
													. '"data-copy-link-url": "{link}",'
													. '"nopopup": "true"'
													. '}'
									. '}',
			'linkedin' => 		'//www.linkedin.com/shareArticle?mini=true&url={link}&title={title}&summary={descr}', 
			'livejournal' =>	'//www.livejournal.com/update.bml?event={link}&subject={title}',
			'mixx' =>			'//chime.in/chimebutton/compose/?utm_source=bookmarklet&utm_medium=compose&utm_campaign=chime&chime[url]={link}&chime[title]={title}&chime[body]={descr}', 
			'myspace' =>		'//www.myspace.com/Modules/PostTo/Pages/?u={link}&t={title}&c={descr}', 
			'newsvine' =>		'//www.newsvine.com/_tools/seed&save?u={link}&h={title}',
//			'pinterest' =>		'//pinterest.com/pin/create/link/?url={image}',
			'pinterest' =>		'json:{"link": "//pinterest.com/pin/create/button/",'
									. '"script": "//assets.pinterest.com/js/pinit.js",'
									. '"style": "",'
									. '"attributes": {'
													. '"data-pin-do": "buttonPin",'
													. '"data-pin-media": "{image}",'
													. '"data-pin-url": "{link}",'
													. '"data-pin-description": "{title}",'
													. '"data-pin-custom": "true",'
													. '"nopopup": "true"'
													. '}'
									. '}',
			'posterous' =>		'//posterous.com/share?linkto={link}&title={title}',
			'reddit' =>			'//reddit.com/submit?url={link}&title={title}', 
			'stumbleupon' =>	'//www.stumbleupon.com/submit?url={link}&title={title}', 
			'technorati' =>		'//technorati.com/faves?add={link}&title={title}', 
//			'telegram' =>		'tg://msg?text={title}+{link}',			// mobile
			'telegram' =>		'//telegram.me/share/url?url={link}&text={title}',
			'tumblr' =>			'//www.tumblr.com/share?v=3&u={link}&t={title}&s={descr}', 
			'twitter' =>		'//twitter.com/intent/tweet?text={title}&url={link}',
			'viber' =>			'viber://forward?text={title}+{link}',		// mobile
//			'whatsapp' =>		'whatsapp://send?text={title}+{link}',	// mobile
			'whatsapp' =>		'//wa.me/?text={title}+{link}',
			'yahoo' =>			'//bookmarks.yahoo.com/toolbar/savebm?u={link}&t={title}&d={descr}',
		) );
		return $soc 
				? ( isset( $list[ $soc ] ) 
					? $list[ $soc ] 
					: ''
					) 
				: $list;
	}
}

if ( ! function_exists( 'trx_addons_get_share_links' ) ) {
	/**
	 * Return list of the socials share links or show share buttons
	 * 
	 * @trigger trx_addons_filter_use_share_color
	 * 
	 * @param array $args    Arguments to show share buttons
	 * @param boolean $list  Alternative list of the socials share links
	 * 
	 * @return string        Html with share buttons
	 */
	function trx_addons_get_share_links( $args, $list = false ) {

		$socials_style = trx_addons_get_setting( 'socials_type' );

		$args = array_merge( array(
			'post_id' => 0,						// post ID
			'post_link' => '',					// post link
			'post_title' => '',					// post title
			'post_descr' => '',					// post descr
			'post_thumb' => '',					// post featured image
			'size' => 'tiny',					// icons size: tiny|small|medium|big
			'style' => $socials_style == 'images'	// style for show icons: icons|images|bg|svg
						? 'bg' 
						: ( $socials_style == 'svg'
							? 'svg'
							: 'icons'
							),
			'type' => 'block',					// share block type: block|drop|list
			'wrap' => 'div',					// tag to wrap share block
			'popup' => true,					// open share url in new window or in popup window
			'counters' => true,					// show share counters
			'direction' => 'horizontal',		// share block direction
			'caption' => esc_html__( 'Share:', 'trx_addons' ),	// share block caption
			'before' => '',						// HTML-code before the share links
			'after' => '',						// HTML-code after the share links
			'echo' => true						// if true - show on page, else - only return as string
			), $args );
		
		if ( empty( $args['post_id'] ) )	$args['post_id']    = get_the_ID();
		if ( empty( $args['post_link'] ) )	$args['post_link']  = get_permalink();
		if ( empty( $args['post_title'] ) )	$args['post_title'] = get_the_title();
		if ( empty( $args['post_descr'] ) )	$args['post_descr'] = strip_tags( strip_shortcodes( trx_addons_get_post_excerpt() ) );	// Don't use get_the_excerpt() outside the loop
		if ( empty( $args['post_thumb'] ) )	$args['post_thumb'] = trx_addons_get_attachment_url( get_post_thumbnail_id( $args['post_id'] ), trx_addons_get_thumb_size('big') );

		$output = '';
		
		if ( empty( $list ) ) $list = trx_addons_get_option('share');

		$show_icons = strpos( $args['style'], 'icons' ) !== false;
		$show_names = strpos( $args['style'], 'names' ) !== false;
		if ( is_array( $list ) ) {
			foreach ( $list as $social ) {
				if ( empty( $social['name'] ) ) {
					continue;
				}
				$sn = $social['name'];
				$fn = $show_icons || $show_names ? trx_addons_clear_icon_name( $sn ) : trx_addons_get_file_name( $sn );
				$title = ! empty( $social['title'] ) ? $social['title'] : ucfirst( str_replace( '-circled', '', $fn ) );
				$color = apply_filters( 'trx_addons_filter_use_share_color', ! empty( $social['color'] ) ? $social['color'] : '', $fn, $args['style'] );
				$url = $social['url'];
				if ( empty( $url ) ) {
					$url = trx_addons_get_share_url( str_replace( '-circled', '', $fn ) );
				}
				if ( substr( $url, 0, 5 ) == 'json:' ) {
					$url = json_decode( substr( $url, 5 ), true );
					if ( is_null( $url ) ) {
						continue;
					}
				} else {
					$url = array( 'link' => $url );
				}
				if ( ! isset( $url['attributes'] ) ) {
					$url['attributes'] = array();
				}
				$url['attributes']['href'] = $url['link'];
				$email = strpos( $url['link'], 'mailto:' ) !== false;
				$popup = ! empty( $args['popup'] ) && ! $email && empty( $url['attributes']['nopopup'] );
				if ( ! empty( $popup ) ) {
					$url['attributes']['data-link'] = $url['link'];
				} else {
					$url['attributes']['target'] = '_blank';
				}
				if ( $args['counters'] ) {
					$url['attributes']['data-count'] = $fn;				
				}
				$output .= '<a class="social_item' . ( ! empty( $popup ) ? ' social_item_popup' : '' ) . '"';
				foreach( $url['attributes'] as $k => $v ) {
					$v = str_replace(
									array( '{id}', '{link}', '{title}', '{descr}', '{image}' ),
									array(
										$k == 'href' ? urlencode( $args['post_id'] ) : $args['post_id'],
										$k == 'href' || $k=='data-link'? urlencode( $args['post_link'] ) : $args['post_link'],
										$k == 'href' && !$email ? urlencode( strip_tags( $args['post_title'] ) ) : strip_tags( $args['post_title'] ),
										$k == 'href' && !$email ? urlencode( strip_tags( $args['post_descr'] ) ) : strip_tags( $args['post_descr'] ),
										$k == 'href' ? urlencode( $args['post_thumb'] ) : $args['post_thumb']
										),
									$v );
					$output .= " {$k}=\"" . ( $k == 'href' ? esc_url( $v ) : esc_attr( $v ) ) . '"';
				}
				$output .= apply_filters( 'trx_addons_filter_social_sharing_attr', '', $social ) . '>'
							. ( ! $show_names || $show_icons
									? '<span class="social_icon'
											. ' social_icon_' . esc_attr( $fn )
											. ' sc_icon_type_' . esc_attr( $args['style'] )
											. ( ! empty( $color ) ? ' social_icon_colored' : '' )
										. '"'
										. ' style="'
											. ( $args['style'] == 'bg' ? 'background-image: url(' . esc_url( $sn ) . ');' : '' )
											. ( ! empty( $color )
												? ( $args['type'] == 'block'
													? 'background-color:' . esc_attr( $color ) . ';'
													: ( $args['type'] == 'list'
														? 'color:' . esc_attr( $color ) . ';'
														: ''
														)
													)
												: ''
												)
										. '"'
									. '>'
										. ( $show_icons || $show_names
											? ( $show_icons
												? '<span class="' . esc_attr( $sn ) . '"></span>'
												: ''
												)
											: ( $args['style'] == 'svg'
												? trx_addons_get_svg_from_file( $sn )
												: ( $args['style'] == 'images' 
													? '<img src="' . esc_url( $sn ) . '" alt="' . esc_attr( $title ) .'" />' 
													: '<span class="social_hover" style="background-image: url(' . esc_url( $sn ) . ');"></span>'
													)
												)
											)
										//. ( $args['counters'] ? '<span class="share_counter">0</span>' : '' ) 
										. ( ! empty( $title ) && $args['type'] == 'drop' ? '<i>' . trim( $title ) . '</i>' : '' )
									. '</span>'
								: ''
								)
							. ( ! empty( $title ) && $show_names
								? '<span class="social_name social_' . esc_attr( $fn ) . '"'
										. ( ! empty( $color )
											? ' style="color:' . esc_attr( $color ) . ';"'
											: ''
											)
									.'>'
										. trim( $title )
									. '</span>'
								: ''
								)
						. '</a>';
				if ( ! empty( $url['script'] ) ) {
					if ( ! is_array( $url['script'] ) ) {
						$url['script'] = array( $url['script'] );
					}
					$i = 0;
					foreach ( $url['script'] as $s ) {
						$i++;
						wp_enqueue_script( "trx_addons_share_{$fn}" . ( $i > 1 ? "_{$i}" : '' ), $s, array(), null, true );
					}
				}
				if ( ! empty( $url['style'] ) ) {
					if (!is_array($url['style'] ) )
						$url['style'] = array( $url['style'] );
					$i = 0;
					foreach ( $url['style'] as $s ) {
						$i++;
						wp_enqueue_style( "trx_addons_share_{$fn}" . ( $i > 1 ? "_{$i}" : '' ), $s, array(), null );
					}
				}
			}
		}
		
		if ( ! empty( $output ) ) {
			$output = $args['before']
						. '<' . esc_html( $args['wrap'] ) . ' class="socials_share'
							. ' socials_size_' . esc_attr( $args['size'] )
							. ' socials_type_' . esc_attr( $args['type'] )
							. ' socials_dir_' . esc_attr( $args['direction'] )
							. ( $args['type'] != 'drop' ? ' socials_wrap' : '' )
						. '">'
							. ( $args['caption'] != '' 
								? ( $args['type'] == 'drop' 
									? '<a href="#" class="socials_caption"><span class="socials_caption_label">' . $args['caption'] . '</span></a>'
									: '<span class="socials_caption">' . $args['caption'] . '</span>'
									)
								: '' )
							. '<span class="social_items">'
								. $output
							. '</span>'
						. '</' . esc_html( $args['wrap'] ) . '>'
					. $args['after'];
			if ( $args['echo'] ) {
				trx_addons_show_layout( $output );
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_get_socials_links' ) ) {
	/**
	 * Return social icons links
	 *
	 * @param string $style		icons|bg|svg
	 * @param string $show		icons|names|icons_names
	 * 
	 * @return string  	   html with social icons
	 */
	function trx_addons_get_socials_links( $style = '', $show = 'icons' ) {
		return trx_addons_get_socials_links_custom( trx_addons_get_option('socials'), $style, $show );
	}
}

if ( ! function_exists( 'trx_addons_get_socials_links_custom' ) ) {
	/**
	 * Return social icons links from the specified list
	 * 
	 * @trigger trx_addons_filter_use_social_color
	 *
	 * @param string|array $icons	List of the icons in the format: icon1=url1;icon2=url2;... or array( 'icon1' => 'url1', 'icon2' => 'url2', ... )
	 * @param string $style			icons|bg|svg
	 * @param string $show			icons|names|icons_names
	 * 
	 * @return string  	   html with social icons
	 */
	function trx_addons_get_socials_links_custom( $icons, $style = '', $show = 'icons' ) {
		if ( empty( $style ) ) {
			$socials_type = trx_addons_get_setting('socials_type');
			$style = $socials_type == 'images' ? 'bg' : ( $socials_type == 'svg' ? 'svg' : 'icons' );
		}
		$output = '';
		if ( is_string( $icons ) ) {
			$tmp = explode( "\n", $icons );
			$icons = array();
			foreach ( $tmp as $str ) {
				$tmp2 = explode( "=", trim( $str ) );
				if ( count( $tmp2 ) == 2 ) {
					$icons[] = array(
						'name' => ( strpos( $tmp2[0], 'icon-') === false ? 'trx_addons_icon-' : '' ) . trim( $tmp2[0] ),
						'url' => trim( $tmp2[1] )
					);
				}
			}
		}
		$show_icons = strpos( $show, 'icons' ) !== false;
		$show_names = strpos( $show, 'names' ) !== false;
		if ( is_array( $icons ) && ! empty( $icons[0] ) ) {
			foreach ( $icons as $social ) {
				$sn = $social['name'];
				$fn = $style == 'icons' ? trx_addons_clear_icon_name( $sn ) : trx_addons_get_file_name( $sn );
				$title = ! empty( $social['title'] ) ? $social['title'] : ucfirst( str_replace( '-circled', '', $fn ) );
				$color = apply_filters( 'trx_addons_filter_use_social_color', ! empty( $social['color'] ) ? $social['color'] : '', $fn, $show );
				$url = $social['url'];
				if ( ! $show_names ) {
					$title = '';
				}
				if ( ! $show_icons || strtolower( $sn ) == 'none' ) {
					$sn = '';
				}
				if ( empty( $url ) || ( empty( $sn ) && empty( $title ) ) ) {
					continue;
				}
				$output .= '<a target="_blank" href="' . ( strpos( $url, 'mailto:' ) !== false || strpos( $url, 'skype:' ) !== false
															? esc_attr( $url )
															: esc_url( $url )
															)
														. '"'
								. ' class="social_item social_item_style_' . esc_attr( $style ) . ' sc_icon_type_' . esc_attr( $style ) . ' social_item_type_' . esc_attr( $show ) . '"'
							. '>'
							. ($show_icons
								? '<span class="social_icon social_icon_' . esc_attr( $fn) . ( ! empty( $color ) ? ' social_icon_colored' : '' ) . '"'
									. ' style="'
										. ( $style == 'bg' ? 'background-image: url(' . esc_url( $sn ) . ');' : '' )
										. ( ! empty( $color )
											? ( $style == 'icons'
												? 'background-color:' . esc_attr( $color ) . ';'
												: ''
												)
											: ''
											)
										. '"'
									. '>'
										. ( $style == 'icons' 
											? '<span class="' . esc_attr( $sn ) . '"></span>' 
											: ( $style == 'svg'
												? trx_addons_get_svg_from_file( $sn )
												: ( $style == 'images' 
													? '<img src="' . esc_url( $sn ) . '" alt="' . esc_attr( $title ) . '" />' 
													: '<span class="social_hover" style="background-image: url(' . esc_url( $sn ) . ');"></span>'
													)
												)
											)
									. '</span>'
								: '')
							. ( $show_names
								? '<span class="social_name social_' . esc_attr( $fn ) . '"'
									. ( ! empty( $color )
											? ' style="color:' . esc_attr( $color ) . ';"'
											: ''
										)
									. '>' . esc_html( $title ) . '</span>'
								: ''
								)
						. '</a>';
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_add_og_tags' ) ) {
	add_action( 'wp_head', 'trx_addons_add_og_tags', 5 );
	/**
	 * Add Open Graph tags to the header
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_add_og_tags() {
		global $wp_query;
		if ( is_admin() || trx_addons_is_off( trx_addons_get_option( 'add_og_tags' ) ) ) {
			return;
		}
		if ( trx_addons_is_singular() && ( ! isset( $wp_query->is_posts_page ) || $wp_query->is_posts_page != 1 ) && ! is_home() && ! is_front_page() && ! empty( $wp_query->post ) ) {
			?>
			<meta property="og:type" content="article" />
			<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>" />
			<meta property="og:title" content="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" />
			<meta property="og:description" content="<?php
				$descr = explode( ' ', strip_tags( preg_replace( '/<style[^>]*>[^<]*<\/style>/', '', strip_shortcodes( trx_addons_get_post_excerpt() ) ) ), 55 );
				array_pop( $descr );
				echo esc_attr( join( ' ', $descr ) );
			?>" />  
			<?php
			if ( has_post_thumbnail( get_the_ID() ) ) {
				?>
				<meta property="og:image" content="<?php echo esc_url( trx_addons_add_protocol( trx_addons_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'full' ) ) ); ?>"/>
				<?php
			}
		} else {
			?>
			<meta property="og:type" content="website" />
			<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo('name')); ?>" />
			<meta property="og:description" content="<?php echo esc_attr( get_bloginfo('description', 'display') ); ?>" />
			<?php
			$logo = apply_filters( 'trx_addons_filter_theme_logo', '' );
			if ( is_array( $logo ) ) {
				$logo = ! empty( $logo['logo'] ) ? $logo['logo'] : '';
			}
			if ( ! empty( $logo ) ) {
				?>
				<meta property="og:image" content="<?php echo esc_url( trx_addons_add_protocol( $logo ) ); ?>" />
				<?php
			}
		}  		
	}
}

if ( ! function_exists( 'trx_addons_add_fb_app_id' ) ) {
	add_action( 'wp_head', 'trx_addons_add_fb_app_id', 4 );
	/**
	 * Add Facebook App ID to the header
	 * 
	 * @hooked wp_head
	 */
	function trx_addons_add_fb_app_id() {
		$id = trx_addons_get_option( 'api_fb_app_id' );
		if ( ! is_admin() && ! empty( $id ) ) {
			?>
			<meta property="fb:admins" content="<?php echo esc_attr( $id ); ?>" />
			<?php
		}
	}
}
