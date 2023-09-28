(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Custom Links
	blocks.registerBlockType(
		'trx-addons/custom-links',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Custom Links', "trx_addons" ),
			description: __( "Insert widget with list of the custom links", "trx_addons" ),
			icon: 'admin-links',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					title: {
						type: 'string',
						default: __( 'Custom Links', "trx_addons" )
					},
					icons_animation: {
						type: 'boolean',
						default: false
					},			
					links: {
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
			), 'trx-addons/custom-links' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Animation
								{
									'name': 'icons_animation',
									'title': __( 'Animation', "trx_addons" ),
									'descr': __( "Toggle on if you want to animate icons. Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", "trx_addons" ),
									'type': 'boolean',
								}
							], 'trx-addons/custom-links', props ), props )
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
					props.attributes.links = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			}
		},
		'trx-addons/custom-links'
	) );

	// Register block Custom Link
	blocks.registerBlockType(
		'trx-addons/custom-links-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Custom Link', "trx_addons" ),
			description: __( "Insert 'Custom Link'", "trx_addons" ),
			icon:  'admin-links',
			category: 'trx-addons-widgets',
			parent: ['trx-addons/custom-links'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				url: {
					type: 'string',
					default: ''
				},
				caption: {
					type: 'string',
					default: ''
				},
				color: {
					type: 'string',
					default: ''
				},
				label: {
					type: 'string',
					default: ''
				},
				label_bg_color: {
					type: 'string',
					default: ''
				},
				label_on_hover: {
					type: 'boolean',
					default: false
				},
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				new_window: {
					type: 'boolean',
					default: false
				},
				icon: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/custom-links-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Custom Link', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el(
							'div', {}, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the item", "trx_addons" ),
									'type': 'text'
								},
								// Link URL
								{
									'name': 'url',
									'title': __( 'Link URL', "trx_addons" ),
									'descr': __( "URL to link this item", "trx_addons" ),
									'type': 'text'
								},
								// Caption
								{
									'name': 'caption',
									'title': __( 'Caption', "trx_addons" ),
									'descr': __( "Caption to create button. If empty - the button is not displayed", "trx_addons" ),
									'type': 'text'
								},
								// Color
								{
									'name': 'color',
									'title': __( 'Link color', "trx_addons" ),
									'descr': __( "Select new color of this link. If empty - default theme color is used", "trx_addons" ),
									'type': 'color'
								},
								// Label
								{
									'name': 'label',
									'title': __( 'Label', "trx_addons" ),
									'descr': __( "Text of the label. If empty - the label is not displayed", "trx_addons" ),
									'type': 'text'
								},
								// Label bg color
								{
									'name': 'label_bg_color',
									'title': __( 'Label bg color', "trx_addons" ),
									'descr': __( "Select background color of the label", "trx_addons" ),
									'type': 'color'
								},
								// Show label on hover
								{
									'name': 'label_on_hover',
									'title': __( 'Show label on hover', "trx_addons" ),
									'descr': __( "Check if you want show label on the item is hovered", "trx_addons" ),
									'type': 'boolean'
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image'
								},
								// Open link in a new window
								{
									'name': 'new_window',
									'title': __( 'Open link in a new window', "trx_addons" ),
									'descr': __( "Check if you want open this link in a new window (tab)", "trx_addons" ),
									'type': 'boolean'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Enter short description for this item", "trx_addons" ),
									'type': 'textarea'
								}
							], 'trx-addons/custom-links-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/custom-links-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
