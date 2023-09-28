<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

extract( get_query_var( 'trx_addons_args_sc_chat_topics' ) );

?><#
settings = trx_addons_elm_prepare_global_params( settings );
var topics = settings.topics.length && settings.topics[0].topic ? settings.topics : JSON.parse( '<?php echo addslashes( json_encode( trx_addons_sc_chat_topics_get_saved_topics() ) ); ?>' );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_chat_topics_' + ( '' + Math.random() ).replace( '.', '' );

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_chat_topics sc_chat_topics_' + settings.type, settings ) ); #>">

	<?php $element->sc_show_titles( 'sc_chat_topics' ); ?>

	<# if ( topics.length ) { #>
		<div class="sc_chat_topics_content sc_item_content">
			<ul class="sc_chat_topics_list">
				<# for ( var i = 0; i < Math.min( settings.number.size, topics.length ); i++ ) { #>
					<li class="sc_chat_topics_item"><a href="javascript:void(0)" data-chat-id="{{ settings.chat_id }}">{{ topics[i]['topic'] }}</a></li>
				<# } #>
			</ul>
		</div>
	<# } #>

	<?php $element->sc_show_links('sc_chat_topics'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>