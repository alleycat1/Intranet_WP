(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Services
	blocks.registerBlockType(
		'trx-addons/services',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Services', "trx_addons" ),
			icon: 'hammer',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					tabs_effect: {
						type: 'string',
						default: 'fade'
					},
					featured: {
						type: 'string',
						default: 'image'
					},
					featured_position: {
						type: 'string',
						default: 'top'
					},
					thumb_size: {
						type: 'string',
						default: ''
					},
					no_links: {
						type: 'boolean',
						default: false
					},
					more_text: {
						type: 'string',
						default: __( 'Read more', "trx_addons" ),
					},
					pagination: {
						type: 'string',
						default: 'none'
					},
					hide_excerpt: {
						type: 'boolean',
						default: false
					},
					no_margin: {
						type: 'boolean',
						default: false
					},
					icons_animation: {
						type: 'boolean',
						default: false
					},
					hide_bg_image: {
						type: 'boolean',
						default: false
					},
					popup: {
						type: 'boolean',
						default: false
					},
					post_type: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['CPT_SERVICES_PT']
					},
					taxonomy: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['CPT_SERVICES_TAXONOMY']
					},
					cat: {
						type: 'string',
						default: '0'
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/services' ),
			edit: function(props) {
				var post_type = props.attributes.post_type,
					taxonomy  = props.attributes.taxonomy;
				
				// Change a default value of an attributes (if need)
				var atts = {}, need_update = false;
				if ( typeof TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type] == 'undefined' ) {
					atts.post_type = post_type = 'post';
					need_update = true;
				}
				if ( ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].hasOwnProperty( taxonomy ) ) {
					atts.taxonomy = taxonomy = trx_addons_array_first_key( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type] );
					need_update = true;
				}
				if ( need_update ) {
					trx_addons_gutenberg_set_attributes_from_edit( props, atts );
				}

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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_services'] )
								},
								// Tabs change effect
								{
									'name': 'tabs_effect',
									'title': __( 'Tabs change effect', "trx_addons" ),
									'descr': __( "Select the tabs change effect", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_services_tabs_effects'] ),
									'dependency': {
										'type': ['tabs']
									}
								},
								// Featured
								{
									'name': 'featured',
									'title': __( 'Featured', "trx_addons" ),
									'descr': __( "What to use as featured element?", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_services_featured'] ),
									'dependency': {
										'type': ['default', 'callouts', 'hover', 'light', 'list', 'iconed', 'tabs', 'tabs_simple', 'timeline']
									}
								},
								// Featured position
								{
									'name': 'featured_position',
									'title': __( 'Featured position', "trx_addons" ),
									'descr': __( "Select the position of the featured element. Attention! Use 'Bottom' only with 'Callouts' or 'Timeline'", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_services_featured_positions'] ),
									'dependency': {
										'featured': ['image', 'icon', 'number', 'pictogram']
									}
								},
								// Thumb size
								{
									'name': 'thumb_size',
									'title': __( 'Image size', "trx_addons" ),
									'descr': __( "Leave 'Default' to use default size defined in the shortcode template or any registered size to override thumbnail size with the selected value.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_services_thumb_sizes'] ),
									'dependency': {
										'type': [ '^news' ]
									}
								},
								// Disable links
								{
									'name': 'no_links',
									'title': __( 'Disable links', "trx_addons" ),
									'descr': __( "Check if you want disable links to the single posts", "trx_addons" ),
									'type': 'boolean'
								},
								// 'More' text
								{
									'name': 'more_text',
									'title': __( "'More' text", "trx_addons" ),
									'descr': __( "Specify caption of the 'Read more' button. If empty - hide button", "trx_addons" ),
									'type': 'text',
								},
								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_paginations'] )
								},
								// Excerpt
								{
									'name': 'hide_excerpt',
									'title': __( "Hide excerpt", "trx_addons" ),
									'descr': __( "Toggle this option to hide the excerpt.", "trx_addons" ),
									'type': 'boolean',
								},
								// Remove margin
								{
									'name': 'no_margin',
									'title': __( "Remove margin", "trx_addons" ),
									'descr': __( "Check if you want remove spaces between columns", "trx_addons" ),
									'type': 'boolean',
								},
								// Animation
								{
									'name': 'icons_animation',
									'title': __( "Icons animation", "trx_addons" ),
									'descr': __( "Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", "trx_addons" ),
									'type': 'boolean',
								},
								// Hide bg image
								{
									'name': 'hide_bg_image',
									'title': __( "Hide bg image", "trx_addons" ),
									'descr': __( "Toggle to hide the background image on the front item.", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['hover']
									}
								},
								// Open in the popup
								{
									'name': 'popup',
									'title': __( "Open in the popup", "trx_addons" ),
									'descr': __( "Open details in the popup or navigate to the single post (default)", "trx_addons" ),
									'type': 'boolean',
								},
								// Post type
								{
									'name': 'post_type',
									'title': __( "Post type", "trx_addons" ),
									'descr': __( "Select post type to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy',
									'title': __( "Taxonomy", "trx_addons" ),
									'descr': __( "Select taxonomy to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type], TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].length === 0 )
								},
								// Category
								{
									'name': 'cat',
									'title': __( 'Category', "trx_addons" ),
									'descr': __( "Services group", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy], true )
								}
							], 'trx-addons/services', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// Slider params
							trx_addons_gutenberg_add_param_slider( props ),
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
		'trx-addons/services'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
