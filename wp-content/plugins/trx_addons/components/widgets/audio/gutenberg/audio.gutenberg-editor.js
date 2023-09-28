(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Audio
	blocks.registerBlockType(
		'trx-addons/audio',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Audio', "trx_addons" ),
			description: __( "Play audio from Soundcloud and other audio hostings or Local hosted audio", "trx_addons" ),
			icon: 'format-audio',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					subtitle: {
						type: 'string',
						default: ''
					},
					next_btn: {
						type: 'boolean',
						default: true
					},
					prev_btn: {
						type: 'boolean',
						default: true
					},
					next_text: {
						type: 'string',
						default: ''
					},
					prev_text: {
						type: 'string',
						default: ''
					},
					now_text: {
						type: 'string',
						default: ''
					},
					track_time: {
						type: 'boolean',
						default: true
					},
					track_scroll: {
						type: 'boolean',
						default: true
					},
					track_volume: {
						type: 'boolean',
						default: true
					},
					media: {
						type: 'string',
						default: ''
					},
					media_from_post: {
						type: 'boolean',
						default: false
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/audio' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
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
								// Get media from post
								{
									'name': 'media_from_post',
									'title': __( 'Get audio from post', "trx_addons" ),
									'type': 'boolean'
								},
								// Show next button
								{
									'name': 'next_btn',
									'title': __( 'Show next button', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'media_from_post': [ false ]
									}
								},
								// Show prev button
								{
									'name': 'prev_btn',
									'title': __( 'Show prev button', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'media_from_post': [ false ]
									}
								},
								// Next button text
								{
									'name': 'next_text',
									'title': __( 'Next button text', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'media_from_post': [ false ]
									}
								},
								// Prev button text
								{
									'name': 'prev_text',
									'title': __( 'Prev button text', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'media_from_post': [ false ]
									}
								},
								// "Now playing" text
								{
									'name': 'now_text',
									'title': __( '"Now playing" text', "trx_addons" ),
									'type': 'text'
								},
								// Show tack time
								{
									'name': 'track_time',
									'title': __( 'Show tack time', "trx_addons" ),
									'type': 'boolean'
								},
								// Show track scroll bar
								{
									'name': 'track_scroll',
									'title': __( 'Show track scroll bar', "trx_addons" ),
									'type': 'boolean'
								},
								// Show track volume bar
								{
									'name': 'track_volume',
									'title': __( 'Show track volume bar', "trx_addons" ),
									'type': 'boolean'
								}
							], 'trx-addons/audio', props ), props )
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
					props.attributes.media = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/audio'
	) );

	// Register block Audio Item
	blocks.registerBlockType(
		'trx-addons/audio-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Audio Item', "trx_addons" ),
			description: __( "Insert audio item", "trx_addons" ),
			icon: 'format-audio',
			category: 'trx-addons-widgets',
			parent: ['trx-addons/audio'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				url: {
					type: 'string',
					default: ''
				},
				embed: {
					type: 'string',
					default: ''
				},
				caption: {
					type: 'string',
					default: ''
				},
				author: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				cover: {
					type: 'number',
					default: 0
				},
				cover_url: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/audio-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Audio item', "trx_addons" ) + (props.attributes.caption ? ': ' + props.attributes.caption : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Media URL
								{
									'name': 'url',
									'title': __( 'Media URL', "trx_addons" ),
									'type': 'text'
								},
								// Embed code
								{
									'name': 'embed',
									'title': __( 'Embed code', "trx_addons" ),
									'type': 'textarea'
								},
								// Audio caption
								{
									'name': 'caption',
									'title': __( 'Audio caption', "trx_addons" ),
									'type': 'text'
								},
								// Author name
								{
									'name': 'author',
									'title': __( 'Author name', "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'type': 'textarea'
								},
								// Cover image
								{
									'name': 'cover',
									'name_url': 'cover_url',
									'title': __( 'Cover image', "trx_addons" ),
									'type': 'image'
								}
							], 'trx-addons/audio-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/audio-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
