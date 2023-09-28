(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Popular Posts
	blocks.registerBlockType(
		'trx-addons/popular-posts',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Popular Posts', "trx_addons" ),
			description: __( "Insert popular posts list with thumbs, post's meta and category", "trx_addons" ),
			icon: 'list-view',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					title: {
						type: 'string',
						default: ''
					},
					title_1: {
						type: 'string',
						default: __( 'Tab 1', "trx_addons" )
					},
					title_2: {
						type: 'string',
						default: __( 'Tab 2', "trx_addons" )
					},
					title_3: {
						type: 'string',
						default: __( 'Tab 3', "trx_addons" )
					},
					orderby_1: {
						type: 'string',
						default: 'views'
					},
					orderby_2: {
						type: 'string',
						default: 'comments'
					},
					orderby_3: {
						type: 'string',
						default: 'likes'
					},
					post_type_1: {
						type: 'string',
						default: 'post'
					},
					post_type_2: {
						type: 'string',
						default: 'post'
					},
					post_type_3: {
						type: 'string',
						default: 'post'
					},
					taxonomy_1: {
						type: 'string',
						default: 'category'
					},
					taxonomy_2: {
						type: 'string',
						default: 'category'
					},
					taxonomy_3: {
						type: 'string',
						default: 'category'
					},
					cat_1: {
						type: 'number',
						default: 0
					},
					cat_2: {
						type: 'number',
						default: 0
					},
					cat_3: {
						type: 'number',
						default: 0
					},
					number: {
						type: 'number',
						default: 4
					},
					show_date: {
						type: 'boolean',
						default: true
					},
					show_image: {
						type: 'boolean',
						default: true
					},
					show_author: {
						type: 'boolean',
						default: true
					},
					show_counters: {
						type: 'boolean',
						default: true
					},
					show_categories: {
						type: 'boolean',
						default: true
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/popular-posts' ),
			edit: function(props) {
				var post_type_1 = props.attributes.post_type_1,
					taxonomy_1  = props.attributes.taxonomy_1,
					post_type_2 = props.attributes.post_type_2,
					taxonomy_2  = props.attributes.taxonomy_2,
					post_type_3 = props.attributes.post_type_3,
					taxonomy_3  = props.attributes.taxonomy_3;
				
				// Change a default value of an attributes (if need)
				var atts = {}, need_update = false;
				if ( typeof TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_1] == 'undefined' ) {
					atts.post_type_1 = post_type_1 = 'post';
					need_update = true;
				}
				if ( ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_1].hasOwnProperty( taxonomy_1 ) ) {
					atts.taxonomy_1 = taxonomy_1 = trx_addons_array_first_key( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_1] );
					need_update = true;
				}
				if ( typeof TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_2] == 'undefined' ) {
					atts.post_type_2 = post_type_2 = 'post';
					need_update = true;
				}
				if ( ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_2].hasOwnProperty( taxonomy_2 ) ) {
					atts.taxonomy_2 = taxonomy_2 = trx_addons_array_first_key( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_2] );
					need_update = true;
				}
				if ( typeof TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_3] == 'undefined' ) {
					atts.post_type_3 = post_type_3 = 'post';
					need_update = true;
				}
				if ( ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_3].hasOwnProperty( taxonomy_3 ) ) {
					atts.taxonomy_3 = taxonomy_3 = trx_addons_array_first_key( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_3] );
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
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Number posts to show
								{
									'name': 'number',
									'title': __( 'Number posts to show', "trx_addons" ),
									'descr': __( "How many posts display in widget?", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Show post's image
								{
									'name': 'show_image',
									'title': __( "Show post's image", "trx_addons" ),
									'descr': __( "Do you want display post's featured image?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show post's author
								{
									'name': 'show_author',
									'title': __( "Show post's author", "trx_addons" ),
									'descr': __( "Do you want display post's author?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show post's date
								{
									'name': 'show_date',
									'title': __( "Show post's date", "trx_addons" ),
									'descr': __( "Do you want display post's publish date?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show post's counters
								{
									'name': 'show_counters',
									'title': __( "Show post's counters", "trx_addons" ),
									'descr': __( "Do you want display post's counters?", "trx_addons" ),
									'type': 'boolean'
								},
								// Show post's categories
								{
									'name': 'show_categories',
									'title': __( "Show post's categories", "trx_addons" ),
									'descr': __( "Do you want display post's categories?", "trx_addons" ),
									'type': 'boolean'
								},
								// Title
								{
									'name': 'title_1',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Tab 1 title", "trx_addons" ),
									'type': 'text',
								},
								// Order by
								{
									'name': 'orderby_1',
									'title': __( "Order by", "trx_addons" ),
									'descr': __( "Select posts order", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['widget_query_orderby'] )
								},
								// Post type
								{
									'name': 'post_type_1',
									'title': __( 'Post type', "trx_addons" ),
									'descr': __( "Select post type to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy_1',
									'title': __( 'Taxonomy', "trx_addons" ),
									'descr': __( "Select taxonomy to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_1] )
								},
								// Category
								{
									'name': 'cat_1',
									'title': __( 'Category', "trx_addons" ),
									'descr': __( "Select category to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy_1]  )
								},
								// Title
								{
									'name': 'title_2',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Tab 2 title", "trx_addons" ),
									'type': 'text',
								},
								// Order by
								{
									'name': 'orderby_2',
									'title': __( "Order by", "trx_addons" ),
									'descr': __( "Select posts order", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['widget_query_orderby'] )
								},
								// Post type
								{
									'name': 'post_type_2',
									'title': __( 'Post type', "trx_addons" ),
									'descr': __( "Select post type to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy_2',
									'title': __( 'Taxonomy', "trx_addons" ),
									'descr': __( "Select taxonomy to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_2] )
								},
								// Category
								{
									'name': 'cat_2',
									'title': __( 'Category', "trx_addons" ),
									'descr': __( "Select category to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy_2] )
								},
								// Title
								{
									'name': 'title_3',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Tab 3 title", "trx_addons" ),
									'type': 'text',
								},
								// Order by
								{
									'name': 'orderby_3',
									'title': __( "Order by", "trx_addons" ),
									'descr': __( "Select posts order", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['widget_query_orderby'] )
								},
								// Post type
								{
									'name': 'post_type_3',
									'title': __( 'Post type', "trx_addons" ),
									'descr': __( "Select post type to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy_3',
									'title': __( 'Taxonomy', "trx_addons" ),
									'descr': __( "Select taxonomy to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type_3] )
								},
								// Category
								{
									'name': 'cat_3',
									'title': __( 'Category', "trx_addons" ),
									'descr': __( "Select category to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy_3] )
								}
							], 'trx-addons/popular-posts', props ), props )
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
		'trx-addons/popular-posts'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
