(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__,
		atts = trx_addons_object_merge(
			{
				type: {
					type: 'string',
					default: 'default'
				},
				post_type: {
					type: 'string',
					default: 'post'
				},
				taxonomy: {
					type: 'string',
					default: 'category'
				},
				cat: {
					type: 'string',
					default: ''
				},
				pagination: {
					type: 'string',
					default: 'none'
				},
				// Details
				meta_parts: {
					type: 'string',
					default: ''
				},
				hide_excerpt: {
					type: 'boolean',
					default: false
				},
				excerpt_length: {
					type: 'string',
					default: ''
				},
				full_post: {
					type: 'boolean',
					default: false
				},
				more_button: {
					type: 'boolean',
					default: true
				},
				more_text: {
					type: 'string',
					default: __( 'Read more', "trx_addons" )
				},
				image_position: {
					type: 'string',
					default: 'top'
				},
				image_width: {
					type: 'number',
					default: 40
				},
				image_ratio: {
					type: 'string',
					default: 'none'
				},
				thumb_size: {
					type: 'string',
					default: ''
				},
				hover: {
					type: 'string',
					default: 'inherit'
				},
				text_align: {
					type: 'string',
					default: 'left'
				},
				on_plate: {
					type: 'boolean',
					default: false
				},
				numbers: {
					type: 'boolean',
					default: false
				},
				date_format: {
					type: 'string',
					default: ''
				},
				no_margin: {
					type: 'boolean',
					default: false
				},
				no_links: {
					type: 'boolean',
					default: false
				},
				video_in_popup: {
					type: 'boolean',
					default: false
				},
				align: {
					type: 'string',
					//enum: [ 'left', 'center', 'right', 'wide', 'full' ],
					default: ''
				},
				// Reload block - hidden option
				reload: {
					type: 'string',
					default: ''
				}
			},
			trx_addons_gutenberg_get_param_filters(),
			trx_addons_gutenberg_get_param_query(),
			trx_addons_gutenberg_get_param_slider(),
			trx_addons_gutenberg_get_param_title(),
			trx_addons_gutenberg_get_param_button(),
			trx_addons_gutenberg_get_param_id()
		);

	// Add templates
	for (var l in TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_blogger']) {
		if (l == 'length' || ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_template_'+l]) continue;
		var opts = TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_template_'+l],
			defa = '';
		if (opts) {
			for (var i in opts) {
				defa = i;
				break;
			}
		}
		atts['template_' + l] = {
			type: 'string',
			default: defa
		}
	}
	
	// Register Block - Blogger
	blocks.registerBlockType(
		'trx-addons/blogger', 
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Blogger', "trx_addons" ),
			description: __( "Display posts from specified category in many styles", "trx_addons" ),
			icon: 'welcome-widgets-menus',
			category: 'trx-addons-blocks',
			supports: {
				align: [ 'left', 'center', 'right', 'wide', 'full' ],
				html: false,
			},
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', atts, 'trx-addons/blogger' ),
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
						'render_button': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_blogger'] )
								},
								// Post type
								{
									'name': 'post_type',
									'title': __( 'Post type', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy',
									'title': __( 'Taxonomy', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type], TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].length === 0 )
								},
								// Category
								{
									'name': 'cat',
									'title': __( 'Category', "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy], true )
								},
								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Add pagination links after posts. Attention! If slider is active, pagination is not allowed!", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_paginations'] ),
									'dependency': {
										'type': [ '^cards' ]
									}
								}
							], 'trx-addons/blogger', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
							// Filters params
							trx_addons_gutenberg_add_param_filters( props ),
							// Details params
							trx_addons_gutenberg_add_param_sc_blogger_details( props ),
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
			},
		},
		'trx-addons/blogger'
	) );

	// Return details params
	//-------------------------------------------
	function trx_addons_gutenberg_add_param_sc_blogger_details(props) {
		var el     = window.wp.element.createElement;
		var __     = window.wp.i18n.__;
		var params = [
				// Image position
				{
					'name': 'image_position',
					'title': __( 'Image position', "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_image_positions'] ),
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news' ]
					}
				},
				// Image width
				{
					'name': 'image_width',
					'title': __( 'Image width (in %)', "trx_addons" ),
					'type': 'number',
					'min': 10,
					'max': 90,
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news' ],
						'image_position': ['left', 'right', 'alter']
					}
				},
				// Image ratio
				{
					'name': 'image_ratio',
					'title': __( 'Image ratio', "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_image_ratio'] ),
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news', 'cards' ]
					}
				},
				// Thumb size
				{
					'name': 'thumb_size',
					'title': __( 'Image size', "trx_addons" ),
					'descr': __( "Leave 'Default' to use default size defined in the shortcode template or any registered size to override thumbnail size with the selected value.", "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_thumb_sizes'] ),
					'dependency': {
						'type': [ '^news' ]
					}
				},
				// Image hover
				{
					'name': 'hover',
					'title': __( 'Image hover', "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_image_hover'] )
				},
				// Meta parts
				{
					'name': 'meta_parts',
					'title': __( 'Choose meta parts', "trx_addons" ),
					'type': 'select',
					'multiple': true,
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['meta_parts'] ),
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news' ]
					}
				},
				// Hide excerpt
				{
					'name': 'hide_excerpt',
					'title': __( 'Hide excerpt', "trx_addons" ),
					'type': 'boolean'
				},
				// Text length
				{
					'name': 'excerpt_length',
					'title': __( "Text length (in words)", "trx_addons" ),
					'type': 'text',
					'dependency': {
						'hide_excerpt': [ false ]
					}
				},
				// Open full post
				{
					'name': 'full_post',
					'title': __( 'Open full post', "trx_addons" ),
					'type': 'boolean',
					'dependency': {
						'type': [ '^cards' ],
						'hide_excerpt': [ true ]
					}
				},
				// Remove margin
				{
					'name': 'no_margin',
					'title': __( "Remove margin", "trx_addons" ),
					'descr': __( "Check if you want remove spaces between columns", "trx_addons" ),
					'type': 'boolean',
				},
				// Disable links
				{
					'name': 'no_links',
					'title': __( 'Disable links', "trx_addons" ),
					'type': 'boolean',
					'dependency': {
						'full_post': [false]
					}
				},
				// Show 'More' button
				{
					'name': 'more_button',
					'title': __( "Show 'More' button", "trx_addons" ),
					'type': 'boolean',
					'dependency': {
						'no_links': [false],
						'full_post': [false]
					}
				},
				// 'More' text
				{
					'name': 'more_text',
					'title': __( "'More' text", "trx_addons" ),
					'type': 'text',
					'dependency': {
						'more_button': [true],
						'no_links': [false]
					}
				},
				// Text alignment
				{
					'name': 'text_align',
					'title': __( 'Text alignment', "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news', 'cards' ]
					}
				},
				// On plate
				{
					'name': 'on_plate',
					'title': __( 'On plate', "trx_addons" ),
					'type': 'boolean',
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news' ]
					}
				},
				// Video in the popup
				{
					'name': 'video_in_popup',
					'title': __( 'Video in the popup', "trx_addons" ),
					'descr': __( "Open video in the popup window or insert it instead the cover image", "trx_addons" ),
					'type': 'boolean',
				},
				// Show numbers
				{
					'name': 'numbers',
					'title': __( 'Show numbers', "trx_addons" ),
					'type': 'boolean',
					'dependency': {
						'type': [ 'list' ]
					}
				},
				// Date format
				{
					'name': 'date_format',
					'title': __( "Date format", "trx_addons" ),
					'descr': __( 'See available formats %s', "trx_addons" ).replace( '%s', __( 'here:', "trx_addons" ) + ' ' + '//wordpress.org/support/article/formatting-date-and-time/' ),
					'type': 'text',
					'dependency': {
						'type': [ 'default', 'wide', 'list', 'news', 'cards' ]
					}
				}
			];

		// Add templates
		for (var l in TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_blogger']) {
			if (l == 'length' || ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_template_'+l]) continue;
			var opts = TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_template_'+l];
			if (!opts) continue;
			params.unshift(
				{
					'name': 'template_' + l,
					'title': __( 'Template', "trx_addons" ),
					'type': 'select',
					'options': trx_addons_gutenberg_get_lists( opts ),
					'dependency': {
						'type': [ l ]
					}
				}
			);
		}

		return el( wp.element.Fragment, { key: props.name + '-details-params' },
					el( trx_addons_get_wp_editor().InspectorControls, {}, 
						el( wp.components.PanelBody, { title: __( "Details", "trx_addons" ) },
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', params, 'trx-addons/blogger-details', props ), props )
						)
					)
				);
	}
})( window.wp.blocks, window.wp.i18n, window.wp.element );