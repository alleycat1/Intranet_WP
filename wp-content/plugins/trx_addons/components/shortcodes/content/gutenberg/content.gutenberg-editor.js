(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Content area
	blocks.registerBlockType(
		'trx-addons/content',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Content area', "trx_addons" ),
			description: __( "Limit content width inside the fullwide rows", "trx_addons" ),
			icon: 'schedule',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					size: {
						type: 'string',
						default: 'none'
					},
					paddings: {
						type: 'string',
						default: 'none'
					},
					margins: {
						type: 'string',
						default: 'none'
					},
					float: {
						type: 'string',
						default: 'none'
					},
					align: {
						type: 'string',
						default: 'none'
					},
					push: {
						type: 'string',
						default: 'none'
					},
					push_hide_on_tablet: {
						type: 'boolean',
						default: false
					},
					push_hide_on_mobile: {
						type: 'boolean',
						default: false
					},
					pull: {
						type: 'string',
						default: 'none'
					},
					pull_hide_on_tablet: {
						type: 'boolean',
						default: false
					},
					pull_hide_on_mobile: {
						type: 'boolean',
						default: false
					},
					shift_x: {
						type: 'string',
						default: 'none'
					},
					shift_y: {
						type: 'string',
						default: 'none'
					},
					number: {
						type: 'string',
						default: ''
					},
					number_position: {
						type: 'string',
						default: 'br'
					},
					number_color: {
						type: 'string',
						default: ''
					},
					extra_bg: {
						type: 'string',
						default: 'none'
					},
					extra_bg_mask: {
						type: 'string',
						default: 'none'
					},
					content: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/content' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'parent': true,
						'allowedblocks': TRX_ADDONS_STORAGE['gutenberg_allowed_blocks'],
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_content'] )
								},
								// Size
								{
									'name': 'size',
									'title': __( 'Size', "trx_addons" ),
									'descr': __( "Select size of the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_widths'] )
								},
								// Inner paddings
								{
									'name': 'paddings',
									'title': __( 'Inner paddings', "trx_addons" ),
									'descr': __( "Select paddings around of the inner text in the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_paddings_and_margins'] )
								},
								// Outer margin
								{
									'name': 'margins',
									'title': __( 'Outer margin', "trx_addons" ),
									'descr': __( "Select margin around of the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_paddings_and_margins'] )
								},
								// Block alignment
								{
									'name': 'float',
									'title': __( 'Block alignment', "trx_addons" ),
									'descr': __( "Select alignment (floating position) of the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Text alignment
								{
									'name': 'align',
									'title': __( 'Text alignment', "trx_addons" ),
									'descr': __( "Select alignment of the inner text in the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Push block up
								{
									'name': 'push',
									'title': __( 'Push block up', "trx_addons" ),
									'descr': __( "Push this block up, so that it partially covers the previous block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_push_and_pull'] )
								},
								// On tablet
								{
									'name': 'push_hide_on_tablet',
									'title': __( 'On tablet', "trx_addons" ),
									'descr': __( "Disable push on the tablets", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'push': ['tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative']
									}
								},
								// On mobile
								{
									'name': 'push_hide_on_mobile',
									'title': __( 'On mobile', "trx_addons" ),
									'descr': __( "Disable push on the mobile", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'push': ['tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative']
									}
								},
								// Pull next block up
								{
									'name': 'pull',
									'title': __( 'Pull next block up', "trx_addons" ),
									'descr': __( "Pull next block up, so that it partially covers this block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_push_and_pull'] )
								},
								// On tablet
								{
									'name': 'pull_hide_on_tablet',
									'title': __( 'On tablet', "trx_addons" ),
									'descr': __( "Disable pull on the tablets", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'push': ['tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative']
									}
								},
								// On mobile
								{
									'name': 'pull_hide_on_mobile',
									'title': __( 'On mobile', "trx_addons" ),
									'descr': __( "Disable pull on the mobile", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'push': ['tiny', 'tiny_negative', 'small', 'small_negative', 'medium', 'medium_negative', 'large', 'large_negative']
									}
								},
								// The X-axis shift
								{
									'name': 'shift_x',
									'title': __( 'The X-axis shift', "trx_addons" ),
									'descr': __( "Shift this block along the X-axis", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_shift'] )
								},
								// The Y-axis shift
								{
									'name': 'shift_y',
									'title': __( 'The Y-axis shift', "trx_addons" ),
									'descr': __( "Shift this block along the Y-axis", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_shift'] )
								},
								// Number
								{
									'name': 'number',
									'title': __( 'Number', "trx_addons" ),
									'descr': __( "Number to display in the corner of this area", "trx_addons" ),
									'type': 'text',
								},
								// Number position
								{
									'name': 'number_position',
									'title': __( 'Number position', "trx_addons" ),
									'descr': __( "Select position to display number", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_positions'] )
								},
								// Color of the number
								{
									'name': 'number_color',
									'title': __( 'Color of the number', "trx_addons" ),
									'descr': __( "Select custom color of the number", "trx_addons" ),
									'type': 'color'
								},
								// Entended background
								{
									'name': 'extra_bg',
									'title': __( 'Entended background', "trx_addons" ),
									'descr': __( "Extend background of this block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_extra_bg'] )
								},
								// Background mask
								{
									'name': 'extra_bg_mask',
									'title': __( 'Background mask', "trx_addons" ),
									'descr': __( "Specify opacity of the background color to use it as mask for the background image", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_content_extra_bg_mask'] )
								}
							], 'trx-addons/content', props ), props )
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
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			}
		},
		'trx-addons/content'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
