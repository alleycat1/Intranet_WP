(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Blog item part
	blocks.registerBlockType(
		'trx-addons/layouts-blog-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Blog item part', "trx_addons" ),
			icon: 'welcome-widgets-menus',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'title'
					},
					thumb_bg: {
						type: 'boolean',
						default: false
					},
					thumb_ratio: {
						type: 'string',
						default: '16:9'
					},
					thumb_mask: {
						type: 'string',
						default: '#000'
					},
					thumb_mask_opacity: {
						type: 'string',
						default: '0.3'
					},
					thumb_hover_mask: {
						type: 'string',
						default: '#000'
					},
					thumb_hover_opacity: {
						type: 'string',
						default: '0.1'
					},
					thumb_size: {
						type: 'string',
						default: 'full'
					},
					title_tag: {
						type: 'string',
						default: 'h4'
					},
					meta_parts: {
						type: 'string',
						default: ''
					},
					custom_meta_key: {
						type: 'string',
						default: ''
					},
					button_text: {
						type: 'string',
						default: __( "Read more", "trx_addons" )
					},
					button_link: {
						type: 'string',
						default: 'post'
					},
					button_type: {
						type: 'string',
						default: 'default'
					},
					seo: {
						type: 'string',
						default: ''
					},
					position: {
						type: 'string',
						default: 'static'
					},
					hide_overflow: {
						type: 'boolean',
						default: false
					},
					animation_in: {
						type: 'string',
						default: 'none'
					},
					animation_in_delay: {
						type: 'number',
						default: 0
					},
					animation_out: {
						type: 'string',
						default: 'none'
					},
					animation_out_delay: {
						type: 'number',
						default: 0
					},
					text_color: {
						type: 'string',
						default: ''
					},
					text_hover: {
						type: 'string',
						default: ''
					},
					font_zoom: {
						type: 'string',
						default: '1'
					},
					post_type: {
						type: 'string',
						default: 'post,'
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-blog-item' ),
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
									'descr': __( "Select layout's type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_blog_item'] ),
								},
								// Use as background
								{
									'name': 'thumb_bg',
									'title': __( 'Use as background', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['featured']
									}
								},
								// Image ratio
								{
									'name': 'thumb_ratio',
									'title': __( 'Image ratio', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'thumb_bg': [true]
									}
								},
								// Image size
								{
									'name': 'thumb_size',
									'title': __( 'Image size', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['thumbnail_sizes'] ),
									'dependency': {
										'type': ['featured']
									}
								},
								// Image mask color
								{
									'name': 'thumb_mask',
									'title': __( 'Image mask color', "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['featured']
									}
								},
								// Image mask opacity
								{
									'name': 'thumb_mask_opacity',
									'title': __( 'Image mask opacity', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['featured']
									}
								},
								// Hovered mask color
								{
									'name': 'thumb_hover_mask',
									'title': __( 'Hovered mask color', "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['featured']
									}
								},
								// Hovered mask opacity
								{
									'name': 'thumb_hover_opacity',
									'title': __( 'Hovered mask opacity', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['featured']
									}
								},
								// Title tag
								{
									'name': 'title_tag',
									'title': __( 'Title tag', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_title_tags'] ),
									'dependency': {
										'type': ['title']
									}
								},
								// Choose meta parts
								{
									'name': 'meta_parts',
									'title': __( 'Choose meta parts', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['meta_parts'] ),
									'dependency': {
										'type': ['meta']
									}
								},
								// Name of the custom meta
								{
									'name': 'custom_meta_key',
									'title': __( 'Name of the custom meta', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['custom']
									}
								},
								// Button type
								{
									'name': 'button_type',
									'title': __( 'Button type', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_button'] ),
									'dependency': {
										'type': ['button']
									}
								},
								// Button link to
								{
									'name': 'button_link',
									'title': __( 'Button link to', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists({
										'post': __( 'Single post', "trx_addons" ),
										'product': __( 'Linked product', "trx_addons" ),
										'cart': __( 'Add to cart', "trx_addons" ),
									}),
									'dependency': {
										'type': ['button']
									}
								},
								// Button caption
								{
									'name': 'button_text',
									'title': __( 'Button caption', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['button']
									}
								},
								// Zoom font size
								{
									'name': 'font_zoom',
									'title': __( 'Zoom font size', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['title', 'excerpt', 'content', 'meta', 'custom', 'button']
									}
								},
								// Hide overflow
								{
									'name': 'hide_overflow',
									'title': __( 'Hide overflow', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['title', 'meta', 'custom']
									}
								},
								// Position
								{
									'name': 'position',
									'title': __( 'Position', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blog_item_positions'] ),
									'dependency': {
										'type': ['title', 'meta', 'excerpt', 'custom', 'button']
									}
								},
								// Hover animation in
								{
									'name': 'animation_in',
									'title': __( 'Hover animation in', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blog_item_animations_in'] ),
									'dependency': {
										'position': ['^static']
									}
								},
								// Hover animation in delay (in ms)
								{
									'name': 'animation_in_delay',
									'title': __( 'Animation in delay', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 2000,
									'step': 100,
									'dependency': {
										'animation_in': ['^none']
									}
								},
								// Hover animation out
								{
									'name': 'animation_out',
									'title': __( 'Hover animation out', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blog_item_animations_out'] ),
									'dependency': {
										'position': ['^static']
									}
								},
								// Hover animation out delay (in ms)
								{
									'name': 'animation_out_delay',
									'title': __( 'Animation out delay', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 2000,
									'step': 100,
									'dependency': {
										'animation_out': ['^none']
									}
								},
								// Text color
								{
									'name': 'text_color',
									'title': __( 'Text color', "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['title', 'meta', 'excerpt', 'custom', 'button']
									}
								},
								// Text color (hovered)
								{
									'name': 'text_hover',
									'title': __( 'Text color (hovered)', "trx_addons" ),
									'type': 'color',
									'dependency': {
										'type': ['title', 'meta', 'excerpt', 'custom', 'button']
									}
								},
								// Supported post types
								{
									'name': 'post_type',
									'title': __( 'Supported post types', "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] ),									
								}
							], 'trx-addons/layouts-blog-item', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
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
		'trx-addons/layouts-blog-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
