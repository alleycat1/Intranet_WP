(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Twitter
	blocks.registerBlockType(
		'trx-addons/twitter',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Twitter', "trx_addons" ),
			icon: 'twitter',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'list'
					},
					title: {
						type: 'string',
						default: ''
					},
					count: {
						type: 'number',
						default: 2
					},
					columns: {
						type: 'number',
						default: 1
					},
					follow: {
						type: 'boolean',
						default: true
					},
					back_image: {
						type: 'number',
						default: 0
					},
					back_image_url: {
						type: 'string',
						default: ''
					},
					twitter_api: {
						type: 'string',
						default: 'token'
					},
					username: {
						type: 'string',
						default: ''
					},
					consumer_key: {
						type: 'string',
						default: ''
					},
					consumer_secret: {
						type: 'string',
						default: ''
					},
					token_key: {
						type: 'string',
						default: ''
					},
					token_secret: {
						type: 'string',
						default: ''
					},
					bearer: {
						type: 'string',
						default: ''
					},
					embed_header: {
						type: 'boolean',
						default: true
					},
					embed_footer: {
						type: 'boolean',
						default: true
					},
					embed_borders: {
						type: 'boolean',
						default: true
					},
					embed_scrollbar: {
						type: 'boolean',
						default: true
					},
					embed_transparent: {
						type: 'boolean',
						default: true
					}
				},
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/twitter' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_twitter'] )
								},
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text'
								},
								// Twitter API
								{
									'name': 'twitter_api',
									'title': __( 'Twitter API', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_twitter_api'] ),
									'type': 'select'
								},
								// Tweets number
								{
									'name': 'count',
									'title': __( 'Tweets number', "trx_addons" ),
									'descr': __( "Tweets number to show in the feed", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 20
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 4,
									'dependency': {
										'type': [ 'default' ],
										'twitter_api': [ '^embed' ]
									}
								},
								// Show Follow Us
								{
									'name': 'follow',
									'title': __( 'Show Follow Us', "trx_addons" ),
									'descr': __( "Do you want display Follow Us link below the feed?", "trx_addons" ),
									'type': 'boolean'
								},
								// Widget background
								{
									'name': 'back_image',
									'name_url': 'back_image_url',
									'title': __( 'Widget background', "trx_addons" ),
									'descr': __( "Select or upload image or write URL from other site for use it as widget background", "trx_addons" ),
									'type': 'image'
								},
								// Twitter Username
								{
									'name': 'username',
									'title': __( 'Twitter Username', "trx_addons" ),
									'type': 'text'
								},
								// Show embed header
								{
									'name': 'embed_header',
									'title': __( 'Show embed header', "trx_addons" ),
									'descr': '',
									'dependency': {
										'twitter_api': [ 'embed' ]
									},
									'type': 'boolean'
								},
								// Show embed footer
								{
									'name': 'embed_footer',
									'title': __( 'Show embed footer', "trx_addons" ),
									'descr': '',
									'dependency': {
										'twitter_api': [ 'embed' ]
									},
									'type': 'boolean'
								},
								// Show embed borders
								{
									'name': 'embed_borders',
									'title': __( 'Show embed borders', "trx_addons" ),
									'descr': '',
									'dependency': {
										'twitter_api': [ 'embed' ]
									},
									'type': 'boolean'
								},
								// Show embed scrollbar
								{
									'name': 'embed_scrollbar',
									'title': __( 'Show embed scrollbar', "trx_addons" ),
									'descr': '',
									'dependency': {
										'twitter_api': [ 'embed' ]
									},
									'type': 'boolean'
								},
								// Make embed bg transparent
								{
									'name': 'embed_transparent',
									'title': __( 'Make embed bg transparent', "trx_addons" ),
									'descr': '',
									'dependency': {
										'twitter_api': [ 'embed' ]
									},
									'type': 'boolean'
								},
								// Consumer Key
								{
									'name': 'consumer_key',
									'title': __( 'Consumer Key', "trx_addons" ),
									'descr': __( "Specify a Consumer Key from Twitter application", "trx_addons" ),
									'dependency': {
										'twitter_api': [ 'token' ]
									},
									'type': 'text'
								},
								// Consumer Secret
								{
									'name': 'consumer_secret',
									'title': __( 'Consumer Secret', "trx_addons" ),
									'descr': __( "Specify a Consumer Secret from Twitter application", "trx_addons" ),
									'dependency': {
										'twitter_api': [ 'token' ]
									},
									'type': 'text'
								},
								// Token Key
								{
									'name': 'token_key',
									'title': __( 'Token Key', "trx_addons" ),
									'descr': __( "Specify a Token Key from Twitter applicationd", "trx_addons" ),
									'dependency': {
										'twitter_api': [ 'token' ]
									},
									'type': 'text'
								},
								// Token Secret
								{
									'name': 'token_secret',
									'title': __( 'Token Secret', "trx_addons" ),
									'descr': __( "Specify a Token Secret from Twitter application", "trx_addons" ),
									'dependency': {
										'twitter_api': [ 'token' ]
									},
									'type': 'text'
								},
								// Bearer
								{
									'name': 'bearer',
									'title': __( 'Bearer', "trx_addons" ),
									'descr': __( "Specify a Bearer authorization token from a Twitter application", "trx_addons" ),
									'dependency': {
										'twitter_api': [ 'token' ]
									},
									'type': 'text'
								}
							], 'trx-addons/twitter', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
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
		'trx-addons/twitter'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
