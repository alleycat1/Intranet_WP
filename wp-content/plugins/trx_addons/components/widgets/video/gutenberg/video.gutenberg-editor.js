(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Video
	blocks.registerBlockType(
		'trx-addons/video',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Video player', "trx_addons" ),
			description: __( "Insert widget with embedded video from popular video hosting: Vimeo, Youtube, etc.", "trx_addons" ),
			icon: 'video-alt3',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					type: {
						type: 'string',
						default: 'default'
					},
					ratio: {
						type: 'string',
						default: '16:9'
					},
					subtitle: {
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
					},
					popup: {
						type: 'boolean',
						default: false
					},
					autoplay: {
						type: 'boolean',
						default: false
					},
					mute: {
						type: 'boolean',
						default: true
					},
					media_from_post: {
						type: 'boolean',
						default: false
					},
					link: {
						type: 'string',
						default: ''
					},
					embed: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/video' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text'
								},
								// Layout
								{
									'name': 'type',
									'title': __( 'Type', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_video'] ),
									'type': 'select'
								},
								// Ratio
								{
									'name': 'ratio',
									'title': __( 'Ratio', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_video_ratio'] ),
									'dependency': {
										'type': [ 'hover' ]
									},
									'type': 'select'
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'dependency': {
										'type': [ 'hover' ]
									},
									'type': 'text'
								},
								// Get media from post
								{
									'name': 'media_from_post',
									'title': __( 'Get video from post', "trx_addons" ),
									'type': 'boolean'
								},
								// Link to video
								{
									'name': 'link',
									'title': __( 'Video URL', "trx_addons" ),
									'descr': __( "Enter an URL of the video (Note: read more about available formats at WordPress Codex page)", "trx_addons" ),
									'dependency': {
										'media_from_post': [ false ]
									},
									'type': 'text'
								},
								// or paste Embed code
								{
									'name': 'embed',
									'title': __( 'or paste Embed code', "trx_addons" ),
									'descr': __( "or paste the HTML code to embed video", "trx_addons" ),
									'dependency': {
										'type': [ '^hover' ],
										'media_from_post': [ false ]
									},
									'type': 'textarea'
								},
								// Cover image
								{
									'name': 'cover',
									'name_url': 'cover_url',
									'title': __( 'Cover image', "trx_addons" ),
									'descr': __( "Select or upload cover image or write URL from other site", "trx_addons" ),
									'type': 'image'
								},
								// Autoplay on load
								{
									'name': 'autoplay',
									'title': __( 'Autoplay on load', "trx_addons" ),
									'descr': __( "Autoplay video on page load", "trx_addons" ),
									'dependency': {
//										'type': [ '^hover' ],
										'cover': ['']
									},
									'type': 'boolean'
								},
								// Mute
								{
									'name': 'mute',
									'title': __( 'Mute', "trx_addons" ),
									'descr': __( "Make a video muted", "trx_addons" ),
//									'dependency': {
//										'autoplay': false
//									},
									'type': 'boolean'
								},
								// Open in the popup
								{
									'name': 'popup',
									'title': __( 'Open in the popup', "trx_addons" ),
									'descr': __( "Open video in the popup", "trx_addons" ),
									'dependency': {
										'type': [ '^hover' ],
										'cover': [ 'not_empty' ],
									},
									'type': 'boolean'
								}
							], 'trx-addons/video', props ), props )
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
		'trx-addons/video'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
