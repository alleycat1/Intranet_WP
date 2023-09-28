(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Categories List
	blocks.registerBlockType(
		'trx-addons/categories-list',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Categories List', "trx_addons" ),
			description: __( "Insert categories list with icons or images", "trx_addons" ),
			icon: 'editor-ul',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: __( 'Categories List', "trx_addons" )
					},
					style: {
						type: 'string',
						default: '1'
					},
					number: {
						type: 'number',
						default: 5
					},
					columns: {
						type: 'number',
						default: 5
					},
					show_thumbs: {
						type: 'boolean',
						default: true
					},
					show_posts: {
						type: 'boolean',
						default: true
					},
					show_children: {
						type: 'boolean',
						default: false
					},
					post_type: {
						type: 'string',
						default: 'post'
					},
					taxonomy: {
						type: 'string',
						default: 'category'
					},
					cat_list: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/categories-list' ),
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
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'type': 'text',
								},
								// Style
								{
									'name': 'style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select the style to display a categories list", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_categories_list'] )
								},
								// Post type
								{
									'name': 'post_type',
									'title': __( 'Post type', "trx_addons" ),
									'descr': __( "Select the post type to get featured images from the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy',
									'title': __( 'Taxonomy', "trx_addons" ),
									'descr': __( "Select the taxonomy to get featured images from the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type] ),
								},
								// List of the terms
								{
									'name': 'cat_list',
									'title': __( 'List of the terms', "trx_addons" ),
									'descr': __( "The comma separated list of the term's slugs to show. If empty - show 'number' terms (see the field below)", "trx_addons" ),
									'type': 'text',
								},
								// Number of categories to show
								{
									'name': 'number',
									'title': __( 'Number of categories to show', "trx_addons" ),
									'descr': __( "How many categories display in widget?", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Columns number to show
								{
									'name': 'columns',
									'title': __( 'Columns number to show', "trx_addons" ),
									'descr': __( "How many columns use to display categories list?", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Show thumbs
								{
									'name': 'show_thumbs',
									'title': __( 'Show thumbs', "trx_addons" ),
									'descr': __( "Do you want display term's thumbnails (if exists)?", "trx_addons" ),
									'type': 'boolean',
								},
								// Show posts number
								{
									'name': 'show_posts',
									'title': __( 'Show posts number', "trx_addons" ),
									'descr': __( "Do you want display posts number?", "trx_addons" ),
									'type': 'boolean',
								},
								// Show children
								{
									'name': 'show_children',
									'title': __( 'Show children', "trx_addons" ),
									'descr': __( "Show only children of the current category", "trx_addons" ),
									'type': 'boolean',
								}
							], 'trx-addons/categories-list', props ), props )
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
		'trx-addons/categories-list'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
