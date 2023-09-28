(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Socials
	blocks.registerBlockType(
		'trx-addons/socials',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Socials', "trx_addons" ),
			description: __( "Insert social icons with links on your profiles", "trx_addons" ),
			icon: 'facebook-alt',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					icons_type: {
						type: 'string',
						default: 'socials'
					},
					align: {
						type: 'string',
						default: 'none'
					},
					icons: {
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
			), 'trx-addons/socials' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_socials'] )
								},
								// Icons type
								{
									'name': 'align',
									'title': __( 'Icons type', "trx_addons" ),
									'descr': __( "Select type of icons: links to the social profiles or share links", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_socials_types'] )
								},
								// Icons alignment
								{
									'name': 'align',
									'title': __( 'Icons align', "trx_addons" ),
									'descr': __( "Select alignment of the icons", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								}
							], 'trx-addons/socials', props ), props )
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
					props.attributes.icons = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/socials'
	) );

	// Register block Socials Item
	blocks.registerBlockType(
		'trx-addons/socials-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Socials Item', "trx_addons" ),
			description: __( "Select social icons and specify link for each item", "trx_addons" ),
			icon: 'facebook-alt',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/socials'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				link: {
					type: 'string',
					default: ''
				},
				icon: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/socials-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Socials item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the item", "trx_addons" ),
									'type': 'text'
								},
								// Link
								{
									'name': 'link',
									'title': __( 'Link', "trx_addons" ),
									'descr': __( "URL to link this item", "trx_addons" ),
									'type': 'text'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								}
							], 'trx-addons/socials-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/socials-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
