(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Table
	blocks.registerBlockType(
		'trx-addons/table',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Table', "trx_addons" ),
			description: __( "Insert a table", "trx_addons" ),
			icon: 'grid-view',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					align: {
						type: 'string',
						default: 'none'
					},
					width: {
						type: 'string',
						default: '100%'
					},
					content: {
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
			), 'trx-addons/table' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_table'] )
								},
								// Table alignment
								{
									'name': 'align',
									'title': __( 'Table alignment', "trx_addons" ),
									'descr': __( "Select alignment of the table", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Width
								{
									'name': 'width',
									'title': __( 'Width', "trx_addons" ),
									'descr': __( "Width of the table", "trx_addons" ),
									'type': 'text'
								},
								// Content
								{
									'name': 'content',
									'title': __( 'Content', "trx_addons" ),
									'descr': __( "Content, created with any table-generator, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", "trx_addons" ),
									'type': 'textarea'
								}
							], 'trx-addons/table', props ), props )
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
				return el( '', null );
			}
		},
		'trx-addons/table'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
