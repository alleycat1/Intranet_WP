<?php
/**
 * The style "default" of the Chat Topics
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

$args = get_query_var('trx_addons_args_sc_chat_topics');

if ( empty( $args['topics'][0]['topic'] ) ) {
	$args['topics'] = trx_addons_sc_chat_topics_get_saved_topics( $args['number'] );
} else {
	$args['topics'] = array_slice( $args['topics'], 0, $args['number'] );
}

if ( count( $args['topics'] ) > 0 ) {

	do_action( 'trx_addons_action_sc_chat_topics_before', $args );

	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
		class="sc_chat_topics sc_chat_topics_<?php
			echo esc_attr( $args['type'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes( 'sc_chat_topics', $args, 'sc_wrapper' );
	?>><?php

		trx_addons_sc_show_titles('sc_chat_topics', $args);

		do_action( 'trx_addons_action_sc_chat_topics_before_content', $args );

		?><div class="sc_chat_topics_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_chat_topics', $args, 'sc_items_wrapper' ); ?>><?php
			do_action( 'trx_addons_action_sc_chat_topics_before_list', $args );
			?><ul class="sc_chat_topics_list">
				<?php
				for ( $i = 0; $i < min( $args['number'], count( $args['topics'] ) ); $i++ ) {
					?><li class="sc_chat_topics_item"><a href="javascript:void(0)" data-chat-id="<?php echo esc_attr( $args['chat_id'] ); ?>"><?php echo esc_html( $args['topics'][$i]['topic'] ); ?></a></li><?php
				}
				?>
			</ul><?php
			do_action( 'trx_addons_action_sc_chat_topics_after_list', $args );
		?></div>

		<?php
		do_action( 'trx_addons_action_sc_chat_topics_after_content', $args );

		trx_addons_sc_show_links('sc_chat_topics', $args);
		?>

	</div><?php

	do_action( 'trx_addons_action_sc_chat_topics_after', $args );
}