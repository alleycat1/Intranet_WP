(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Team
	blocks.registerBlockType(
		'trx-addons/team',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Team', "trx_addons" ),
			icon: 'groups',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					pagination: {
						type: 'string',
						default: 'none'
					},
					no_margin: {
						type: 'boolean',
						default: false
					},
					no_links: {
						type: 'boolean',
						default: false
					},
					more_text: {
						type: 'string',
						default: __( 'Read more', "trx_addons" ),
					},
					post_type: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['CPT_TEAM_PT']
					},
					taxonomy: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['CPT_TEAM_TAXONOMY']
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
			), 'trx-addons/team' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_team'] )
								},
								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_paginations'] )
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
									'title': __( "Disable links", "trx_addons" ),
									'descr': __( "Check if you want disable links to the single posts", "trx_addons" ),
									'type': 'boolean',
								},
								// 'More' text
								{
									'name': 'more_text',
									'title': __( "'More' text", "trx_addons" ),
									'descr': __( "Specify caption of the 'Read more' button. If empty - hide button", "trx_addons" ),
									'type': 'text',
								},
								// Post type
								{
									'name': 'post_type',
									'title': __( "Post type", "trx_addons" ),
									'descr': __( "Select post type to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_team_posts_types'] )
								},
								// Taxonomy
								{
									'name': 'taxonomy',
									'title': __( "Taxonomy", "trx_addons" ),
									'descr': __( "Select taxonomy to show posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type], TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].length === 0 )
								},
								// Group
								{
									'name': 'cat',
									'title': __( "Group", "trx_addons" ),
									'descr': __( "Courses group", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy], true )
								}
							], 'trx-addons/team', props ), props )
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
		'trx-addons/team'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
