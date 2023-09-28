(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Promo
	blocks.registerBlockType(
		'trx-addons/promo',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Promo', "trx_addons" ),
			description: __( "Insert promo block", "trx_addons" ),
			icon: 'format-image',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					icon: {
						type: 'string',
						default: ''
					},
					icon_color: {
						type: 'string',
						default: ''
					},				
					text_bg_color: {
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
					image_bg_color: {
						type: 'string',
						default: ''
					},
					image_cover: {
						type: 'boolean',
						default: true
					},
					image_position: {
						type: 'string',
						default: 'left'
					},
					image_width: {
						type: 'string',
						default: '50%'
					},
					video_url: {
						type: 'string',
						default: ''
					},
					video_embed: {
						type: 'string',
						default: ''
					},
					video_in_popup: {
						type: 'boolean',
						default: false
					},
					size: {
						type: 'string',
						default: 'normal'
					},
					full_height: {
						type: 'boolean',
						default: false
					},
					text_width: {
						type: 'string',
						default: 'none'
					},
					text_float: {
						type: 'string',
						default: 'none'
					},
					text_align: {
						type: 'string',
						default: 'none'
					},
					text_paddings: {
						type: 'boolean',
						default: false
					},
					text_margins: {
						type: 'string',
						default: ''
					},
					gap: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_button2(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/promo' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_promo'] )
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Icon color
								{
									'name': 'icon_color',
									'title': __( 'Icon color', "trx_addons" ),
									'descr': __( "Select icon color", "trx_addons" ),
									'type': 'color'
								},
								// Text bg color
								{
									'name': 'text_bg_color',
									'title': __( 'Text bg color', "trx_addons" ),
									'descr': __( "Select custom color, used as background of the text area", "trx_addons" ),
									'type': 'color'
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select the promo image from the library for this section. Show slider if you select 2+ images", "trx_addons" ),
									'type': 'image'
								},
								// Image bg color
								{
									'name': 'image_bg_color',
									'title': __( 'Image bg color', "trx_addons" ),
									'descr': __( "Select custom color, used as background of the image", "trx_addons" ),
									'type': 'color'
								},
								// Image cover area
								{
									'name': 'image_cover',
									'title': __( 'Image cover area', "trx_addons" ),
									'descr': __( "Fit an image into the area or cover it.", "trx_addons" ),
									'type': 'boolean',
								},
								// Image position
								{
									'name': 'image_position',
									'title': __( 'Image position', "trx_addons" ),
									'descr': __( "Place the image to the left or to the right from the text block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_promo_positions'] ),
								},
								// Image width
								{
									'name': 'image_width',
									'title': __( 'Image width', "trx_addons" ),
									'descr': __( "Specify width of the image. If left empty or assigned the value '0', the columns will be equal.", "trx_addons" ),
									'type': 'text',
								},
								// Video URL
								{
									'name': 'video_url',
									'title': __( 'Video URL', "trx_addons" ),
									'descr': __( "Enter link to the video (Note: read more about available formats at WordPress Codex page)", "trx_addons" ),
									'type': 'text',
								},
								// Video embed code
								{
									'name': 'video_embed',
									'title': __( 'Video embed code', "trx_addons" ),
									'descr': __( "or paste the HTML code to embed video in this block", "trx_addons" ),
									'type': 'text',
								},
								// Video in the popup
								{
									'name': 'video_in_popup',
									'title': __( 'Video in the popup', "trx_addons" ),
									'descr': __( "Open video in the popup window or insert it instead the cover image", "trx_addons" ),
									'type': 'boolean',
								},
								// Size
								{
									'name': 'size',
									'title': __( 'Size', "trx_addons" ),
									'descr': __( "Size of the promo block: normal - one in the row, tiny - only image and title, small - insize two or greater columns, large - fullscreen height", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_promo_sizes'] ),
								},
								// Full height
								{
									'name': 'full_height',
									'title': __( 'Full height', "trx_addons" ),
									'descr': __( "Stretch the height of the element to the full screen's height", "trx_addons" ),
									'type': 'boolean',
								},
								// Text width
								{
									'name': 'text_width',
									'title': __( 'Text width', "trx_addons" ),
									'descr': __( "Select width of the text block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_promo_widths'] ),
								},
								// Text block floating
								{
									'name': 'text_float',
									'title': __( 'Text block floating', "trx_addons" ),
									'descr': __( "Select alignment (floating position) of the text block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
								},
								// Text alignment
								{
									'name': 'text_align',
									'title': __( 'Text alignment', "trx_addons" ),
									'descr': __( "Align text to the left or to the right side inside the block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
								},
								// Text paddings
								{
									'name': 'text_paddings',
									'title': __( 'Text paddings', "trx_addons" ),
									'descr': __( "Add horizontal paddings from the text block", "trx_addons" ),
									'type': 'boolean',
								},
								// Text margins
								{
									'name': 'text_margins',
									'title': __( 'Text margins', "trx_addons" ),
									'descr': __( "Margins for the all sides of the text block (Example: 30px 10px 40px 30px = top right botton left OR 30px = equal for all sides)", "trx_addons" ),
									'type': 'text',
								},
								// Gap
								{
									'name': 'gap',
									'title': __( 'Gaps', "trx_addons" ),
									'descr': __( "Gap between text and image (in percent)", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/promo', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true, true ),
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
		'trx-addons/promo'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );