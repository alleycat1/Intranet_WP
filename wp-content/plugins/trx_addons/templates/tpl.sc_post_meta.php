<?php
/**
 * The template to display block with post meta
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */
$trx_addons_args_sc_show_post_meta = get_query_var('trx_addons_args_sc_show_post_meta');
extract($trx_addons_args_sc_show_post_meta);
if ( empty( $tag ) ) $tag = 'div';
?><<?php echo esc_attr($tag); ?> class="<?php echo esc_attr($sc); ?>_post_meta post_meta<?php echo !empty($args['class']) ? ' ' . esc_attr($args['class']) : ''; ?>"><?php
	
	$components = explode(',', $args['components']);
	$post_id = trx_addons_get_the_ID();
	
	foreach ($components as $comp) {
		
		$comp = trim($comp);
		
		// Categories
		if ($comp == 'categories') {
			?><span class="post_meta_item post_categories"><?php the_category( ', ' ); ?></span><?php

		// Tags
		} else if ($comp == 'tags') {
			the_tags( '<span class="post_meta_item post_tags">', ', ', '</span>' );

		// Date
		} else if ($comp == 'date' && in_array( get_post_type(), array( 'post', 'page', 'attachment' ) ) ) {
			?><span class="post_meta_item post_date<?php if (!empty($args['seo'])) echo ' date updated'; ?>"<?php if (!empty($args['seo'])) trx_addons_seo_snippets('datePublished'); ?>><?php
				if (!trx_addons_is_singular()) {
					?><a href="<?php echo esc_url(get_permalink()); ?>"><?php
				}
				echo wp_kses_data(apply_filters('trx_addons_filter_get_post_date', get_the_date(!empty($args['date_format']) ? $args['date_format'] : '')));
				if (!trx_addons_is_singular()) {
					?></a><?php
				}
			?></span><?php

		// Author
		} else if ($comp == 'author') {
			$author_id = get_the_author_meta('ID');
			if (empty($author_id) && !empty($GLOBALS['post']->post_author))
				$author_id = $GLOBALS['post']->post_author;
			if ($author_id > 0) {
				$author_link = get_author_posts_url($author_id);
				$author_name = get_the_author_meta('display_name', $author_id);
				?><span class="post_meta_item post_author"><a rel="author" href="<?php echo esc_url($author_link); ?>"><?php
					echo esc_html($author_name);
				?></a></span><?php
			}

		// Comments
		} else if ($comp == 'comments') {
			if (!trx_addons_is_singular() || have_comments() || comments_open()) {
				$post_comments = get_comments_number();
				?><a href="<?php echo esc_url(get_comments_link()); ?>" class="post_meta_item post_meta_comments trx_addons_icon-comment">
					<span class="post_meta_number"><?php echo wp_kses_data($post_comments); ?></span>
					<span class="post_meta_label"><?php echo esc_html( _n( 'Comment', 'Comments', $post_comments, 'trx_addons' ) ); ?></span>
				</a><?php
			}

		// Views
		} else if ($comp == 'views') {
			$post_views = trx_addons_get_post_views($post_id);
			if ( trx_addons_is_singular() ) {
				?><span class="post_meta_item post_meta_views trx_addons_icon-eye"><?php
			} else {
				?><a href="<?php echo esc_url(get_permalink()); ?>" class="post_meta_item post_meta_views trx_addons_icon-eye"><?php
			}
				?><span class="post_meta_number"><?php echo wp_kses_data($post_views); ?></span><?php
				?><span class="post_meta_label"><?php echo esc_html( _n( 'View', 'Views', $post_views, 'trx_addons' ) ); ?></span><?php
			if ( trx_addons_is_singular() ) {
				?></span><?php
			} else {
				?></a><?php
			}

		// Likes (Emotions)
		} else if ($comp == 'likes') {
			$emotions_allowed = trx_addons_is_on(trx_addons_get_option('emotions_allowed'));
			if ($emotions_allowed) {
				$post_emotions = trx_addons_get_post_emotions($post_id);
				$post_likes = 0;
				if (is_array($post_emotions)) {
					foreach ($post_emotions as $v) {
						$post_likes += (int) $v;
					}
				}
			} else {
				$post_likes = trx_addons_get_post_likes($post_id);
			}
			$liked = trx_addons_get_cookie( 'trx_addons_likes' );
			$allow = strpos( sprintf( ',%s,', $liked ), sprintf( ',%d,', $post_id ) ) === false;
			trx_addons_show_layout(
				( $emotions_allowed
					? ' <a href="'.esc_url(trx_addons_add_hash_to_url(get_permalink(), 'trx_addons_emotions')).'"'
						. ' class="post_meta_item post_meta_emotions trx_addons_icon-angellist">'
					: ' <a href="#"'
						. ' class="post_meta_item post_meta_likes trx_addons_icon-heart'
							. (!empty($allow) ? '-empty enabled' : ' disabled')
							. '"'
						. ' title="'.(!empty($allow) ? esc_attr__('Like', 'trx_addons') : esc_attr__('Dislike', 'trx_addons')).'"'
						. ' data-postid="' . esc_attr($post_id) . '"'
						. ' data-likes="' . esc_attr($post_likes) . '"'
						. ' data-title-like="' . esc_attr__('Like', 'trx_addons') . '"'
						. '	data-title-dislike="' . esc_attr__('Dislike', 'trx_addons') . '"'
						. '>'
				)
					. '<span class="post_meta_number">' . trim($post_likes) . '</span>'
					. '<span class="post_meta_label">'
						. ($emotions_allowed
							? _n( 'Reaction', 'Reactions', $post_likes, 'trx_addons' )
							: _n( 'Like', 'Likes', $post_likes, 'trx_addons' )
							)
					. '</span>'
				. '</a>'
			);

		// Socials share
		} else if ($comp == 'share') {
			$output = trx_addons_get_share_links(array(
					'type' => !empty($args['share_type']) ? $args['share_type'] : 'drop',
					'caption' => esc_html__('Share', 'trx_addons'),
					'wrap' => 'span',
					'echo' => false
				));
			if ($output) {
				?><span class="post_meta_item post_share"><?php trx_addons_show_layout($output); ?></span><?php
			}

		// Edit page link
		} else if ($comp == 'edit') {
			//edit_post_link( esc_html__( 'Edit', 'trx_addons' ), '<span class="post_meta_item post_edit">', '</span>' );
			edit_post_link( esc_html__( 'Edit', 'trx_addons' ), '', '', 0, 'post_meta_item post_edit trx_addons_icon-pencil' );

		// Custom meta data
		} else {
			do_action( 'trx_addons_action_show_post_meta', $comp, $post_id, $trx_addons_args_sc_show_post_meta );

		}
	}
?></<?php echo esc_attr($tag); ?>>