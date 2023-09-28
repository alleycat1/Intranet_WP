(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Action
	blocks.registerBlockType(
		'trx-addons/accordionposts',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Accordion posts', "trx_addons" ),
			description: __( "Accordion of posts", "trx_addons" ),
			icon: 'excerpt-view',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					accordions: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/accordionposts' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_accordionposts'] )
								}
							], 'trx-addons/accordionposts', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.accordions = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			}
		},
		'trx-addons/accordionposts'
	) );

	// Register block Accordionposts Item
	var first_page = trx_addons_array_first_key(TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_pages']),
		first_layout = trx_addons_array_first_key(TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_layouts']);

	blocks.registerBlockType(
		'trx-addons/accordionposts-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Accordion posts item', "trx_addons" ),
			description: __( "Insert 'Accordion posts' item", "trx_addons" ),
			icon: 'excerpt-view',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/accordionposts'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Accordion posts item attributes
				title: {
					type: 'string',
					default: __( "Item's title", "trx_addons" )
				},
				subtitle: {
					type: 'string',
					default: __( 'Description', "trx_addons" )
				},
				icon: {
					type: 'string',
					default: ''
				},
				color: {
					type: 'string',
					default: ''
				},
				bg_color: {
					type: 'string',
					default: ''
				},
				content_source: {
					type: 'string',
					default: 'text'
				},
				post_id: {
					type: 'string',
					default: first_page
				},
				layout_id: {
					type: 'string',
					default: first_layout
				},
				inner_content: {
					type: 'string',
					default: ''
				},
				advanced_rolled_content: {
					type: 'boolean',
					default: false
				},
				rolled_content: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				},
			}, 'trx-addons/accordionposts-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Accordion item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'type': 'text'
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'type': 'text'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Icon Color
								{
									'name': 'color',
									'title': __( 'Icon Color', "trx_addons" ),
									'descr': __( "Selected color will also be applied to the subtitle", "trx_addons" ),
									'type': 'color'
								},
								// Icon Background Color
								{
									'name': 'bg_color',
									'title': __( 'Icon Background Color', "trx_addons" ),
									'descr': __( "Selected color will also be applied to the subtitle", "trx_addons" ),
									'type': 'color'
								},
								// Select content source
								{
									'name': 'content_source',
									'title': __( 'Select content source', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists({
										'text': __( 'Use content', "trx_addons" ),
										'page': __( 'Pages', "trx_addons" ),
										'layout': __( 'Layouts', "trx_addons" ),
									} )
								},
								// Page ID
								{
									'name': 'post_id',
									'title': __( 'Page ID', "trx_addons" ),
									'descr': __( "'Use Content' option allows you to create custom content for the selected content area, otherwise you will be prompted to choose an existing page to import content from it. ", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_pages'] ),
									'dependency': {
										'content_source': ['page']
									}
								},
								// Layout ID
								{
									'name': 'layout_id',
									'title': __( 'Layout ID', "trx_addons" ),
									'descr': __( "'Use Content' option allows you to create custom content for the selected content area, otherwise you will be prompted to choose an existing page to import content from it. ", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_layouts'] ),
									'dependency': {
										'content_source': ['layout']
									}
								},
								// Inner content
								{
									'name': 'inner_content',
									'title': __( 'Inner content', "trx_addons" ),
									'type': 'textarea',
									'dependency': {
										'content_source': ['text']
									}
								},
								// Advanced Header Options
								{
									'name': 'advanced_rolled_content',
									'title': __( 'Advanced Header Options', "trx_addons" ),
									'type': 'boolean'
								},
								// Advanced Header Options
								{
									'name': 'rolled_content',
									'title': __( 'Advanced Header Options', "trx_addons" ),
									'type': 'textarea',
									'dependency': {
										'advanced_rolled_content': [ true ]
									}
								}
							], 'trx-addons/accordionposts-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/accordionposts-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
