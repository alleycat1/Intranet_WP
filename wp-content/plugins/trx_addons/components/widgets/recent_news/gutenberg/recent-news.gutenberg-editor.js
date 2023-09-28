(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Recent News
	blocks.registerBlockType(
		'trx-addons/recent-news',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Recent News', "trx_addons" ),
			description: __( "Insert recent posts list with thumbs, post's meta and category", "trx_addons" ),
			icon: 'list-view',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					style: {
						type: 'string',
						default: 'news-magazine'
					},
					count: {
						type: 'number',
						default: 3
					},
					featured: {
						type: 'number',
						default: 3
					},
					columns: {
						type: 'number',
						default: 3
					},
					ids: {
						type: 'string',
						default: ''
					},
					category: {
						type: 'string',
						default: '0'
					},
					offset: {
						type: 'number',
						default: 0
					},
					orderby: {
						type: 'string',
						default: 'date'
					},
					order: {
						type: 'string',
						default: 'desc'
					},
					widget_title: {
						type: 'string',
						default: ''
					},
					title: {
						type: 'string',
						default: ''
					},
					subtitle: {
						type: 'string',
						default: ''
					},
					show_categories: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/recent-news' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'widget_title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the block", "trx_addons" ),
									'type': 'text',
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'descr': __( "Subtitle of the block", "trx_addons" ),
									'type': 'text',
								},
								// List style
								{
									'name': 'style',
									'title': __( 'List styles', "trx_addons" ),
									'descr': __( "Select style to display news list", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_recent_news'] )
								},
								// Show categories
								{
									'name': 'show_categories',
									'title': __( "Show categories", "trx_addons" ),
									'descr': __( "Show categories in the shortcode's header", "trx_addons" ),
									'type': 'boolean'
								},
								// List IDs
								{
									'name': 'ids',
									'title': __( "List IDs", "trx_addons" ),
									'descr': __( "Comma separated list of IDs list to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", "trx_addons" ),
									'type': 'text'
								},
								// Category
								{
									'name': 'category',
									'title': __( "Category", "trx_addons" ),
									'descr': __( "Select a category to display. If empty - select news from any category or from the IDs list.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_categories'] ),
									'dependency': {
										'ids': ['']
									}
								},
								// Total posts
								{
									'name': 'count',
									'title': __( "Total posts", "trx_addons" ),
									'descr': __( "The number of displayed posts. If IDs are used, this parameter is ignored.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'dependency': {
										'ids': ['']
									}
								},
								// Columns
								{
									'name': 'columns',
									'title': __( "Columns", "trx_addons" ),
									'descr': __( "How many columns use to show news list", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'dependency': {
										'style': ['news-magazine', 'news-portfolio']
									}
								},
								// Offset before select posts
								{
									'name': 'offset',
									'title': __( "Offset before select posts", "trx_addons" ),
									'descr': __( "Skip posts before select next part", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'dependency': {
										'ids': ['']
									}
								},
								// How many posts will be displayed as featured?
								{
									'name': 'featured',
									'title': __( "Featured posts", "trx_addons" ),
									'descr': __( "How many posts will be displayed as featured?", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'dependency': {
										'style': ['news-magazine']
									}
								},
								// Order by
								{
									'name': 'orderby',
									'title': __( "Order by", "trx_addons" ),
									'descr': __( "Select how to sort the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_query_orderby'] )
								},
								// Order
								{
									'name': 'order',
									'title': __( "Order", "trx_addons" ),
									'descr': __( "Select sort order", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_query_orders'] )
								}
							], 'trx-addons/recent-news', props ), props )
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
		'trx-addons/recent-news'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
