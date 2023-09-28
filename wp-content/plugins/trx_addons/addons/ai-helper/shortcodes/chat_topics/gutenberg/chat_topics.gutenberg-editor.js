(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Chat
	blocks.registerBlockType(
		'trx-addons/chat-topics',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Chat Topics', "trx_addons" ),
			description: __( "AI Helper Chat Topics - list latest topics from the AI Chat", "trx_addons" ),
			icon: 'text',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					number: {
						type: 'number',
						default: 5
					},
					chat_id: {
						type: 'string',
						default: ''
					},
					topics: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/chat' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_chat_topics'] )
								},
								// Number of topics
								{
									'name': 'number',
									'title': __( 'Number of topics', "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': trx_addons_apply_filters( 'trx_addons_filter_sc_chat_topics_max', 20 ),
								},
								// Chat ID
								{
									'name': 'chat_id',
									'title': __( 'Chat ID', "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/chat-topics', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.topics = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/chat-topics'
	) );

	// Register block Tag Item
	blocks.registerBlockType(
		'trx-addons/chat-topics-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Topic', "trx_addons" ),
			description: __( "Add a custom topic for Chat Topics", "trx_addons" ),
			icon: 'tag',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/chat-topics'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Topic attributes
				topic: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/chat-topics-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Topic', "trx_addons" ) + (props.attributes.topic ? ': ' + props.attributes.topic : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Topic
								{
									'name': 'topic',
									'title': __( 'Topic', "trx_addons" ),
									'descr': __( "Enter a text of a custom topic", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/chat-topics-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/chat-topics-item'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
