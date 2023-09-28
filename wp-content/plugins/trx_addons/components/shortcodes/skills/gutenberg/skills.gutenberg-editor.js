(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Skills
	blocks.registerBlockType(
		'trx-addons/skills',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Skills', "trx_addons" ),
			description: __( "Skill counters and pie charts", "trx_addons" ),
			icon: 'awards',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'counter'
					},
					style: {
						type: 'string',
						default: 'counter'
					},
					cutout: {
						type: 'number',
						default: 92
					},
					compact: {
						type: 'boolean',
						default: false
					},
					color: {
						type: 'string',
						default: ''
					},
					icon_position: {
						type: 'string',
						default: 'top'
					},
					icon_color: {
						type: 'string',
						default: ''
					},
					item_title_color: {
						type: 'string',
						default: ''
					},
					back_color: {
						type: 'string',
						default: ''
					},
					border_color: {
						type: 'string',
						default: ''
					},
					max: {
						type: 'number',
						default: 100
					},
					columns: {
						type: 'number',
						default: 1
					},
					values: {
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
			), 'trx-addons/skills' ),
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
									'descr': __( "Select shortcode's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_skills'] )
								},
								// Style
								{
									'name': 'style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select counter's style", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_skills_counter_styles'] )
								},
								// Icon position
								{
									'name': 'icon_position',
									'title': __( 'Icon position', "trx_addons" ),
									'descr': __( "Select an icon's position", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_skills_counter_icon_positions'] )
								},
								// Cutout
								{
									'name': 'cutout',
									'title': __( 'Cutout', "trx_addons" ),
									'descr': __( "Specify the pie cutout radius. Border width = 100% - cutout value.", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'dependency': {
										'type': ['pie']
									}
								},
								// Compact pie
								{
									'name': 'compact',
									'title': __( 'Compact pie', "trx_addons" ),
									'descr': __( "Show all values in one pie or each value in the single pie", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['pie']
									}
								},
								// Icon color
								{
									'name': 'icon_color',
									'title': __( 'Icon color', "trx_addons" ),
									'descr': __( "Select custom color for item icons", "trx_addons" ),
									'type': 'color',
								},
								// Value color
								{
									'name': 'color',
									'title': __( 'Value color', "trx_addons" ),
									'descr': __( "Select custom color for item values", "trx_addons" ),
									'type': 'color',
								},
								// Title color
								{
									'name': 'item_title_color',
									'title': __( 'Title color', "trx_addons" ),
									'descr': __( "Select custom color for item titles", "trx_addons" ),
									'type': 'color',
								},
								// Background color
								{
									'name': 'back_color',
									'title': __( 'Background color', "trx_addons" ),
									'descr': __( "Select custom color for item's background", "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['pie']
									}
								},
								// Border color
								{
									'name': 'border_color',
									'title': __( 'Border color', "trx_addons" ),
									'descr': __( "Select custom color for item's border", "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['pie']
									}
								},
								// Max. value
								{
									'name': 'max',
									'title': __( 'Max. value', "trx_addons" ),
									'descr': __( "Enter max value for all items", "trx_addons" ),
									'type': 'number'
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", "trx_addons" ),
									'type': 'number'
								}
							], 'trx-addons/skills', props ), props )
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
					props.attributes.values = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/skills'
	) );

	// Register block Skills Item
	blocks.registerBlockType(
		'trx-addons/skills-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Skills Item', "trx_addons" ),
			description: __( "Specify values for each counter", "trx_addons" ),
			icon: 'awards',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/skills'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				icon: {
					type: 'string',
					default: ''
				},
				icon_color: {
					type: 'string',
					default: ''
				},
				char: {
					type: 'string',
					default: ''
				},
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				value: {
					type: 'number',
					default: 0
				},
				color: {
					type: 'string',
					default: ''
				},
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				item_title_color: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/skills-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Skills item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': '',
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Char
								{
									'name': 'char',
									'title': __( 'or character', "trx_addons" ),
									'descr': '',
									'type': 'text',
									'dependency': {
										'icon': ['', 'none']
									}
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'or image', "trx_addons" ),
									'descr': '',
									'type': 'image',
									'dependency': {
										'icon': ['', 'none'],
										'char': ''
									}
								},
								// Icon color
								{
									'name': 'icon_color',
									'title': __( 'Icon color', "trx_addons" ),
									'descr': '',
									'type': 'color'
								},
								// Value
								{
									'name': 'value',
									'title': __( 'Value', "trx_addons" ),
									'descr': '',
									'type': 'number',
									'min': 0
								},
								// Color
								{
									'name': 'color',
									'title': __( 'Value color', "trx_addons" ),
									'descr': '',
									'type': 'color'
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': '',
									'type': 'text'
								},
								// Title color
								{
									'name': 'item_title_color',
									'title': __( 'Title color', "trx_addons" ),
									'descr': '',
									'type': 'color'
								},
							], 'trx-addons/skills-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/skills-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
