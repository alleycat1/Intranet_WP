(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Video List
	blocks.registerBlockType(
		'trx-addons/video-player',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Video List', "trx_addons" ),
			description: __( "Show list of videos from posts or from the custom list", "trx_addons" ),
			icon: 'video-alt3',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					autoplay: {
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
					category: {
						type: 'string',
						default: ''
					},
					controller_style: {
						type: 'string',
						default: 'default'
					},
					controller_pos: {
						type: 'string',
						default: 'right'
					},
					controller_height: {
						type: 'string',
						default: ''
					},
					controller_autoplay: {
						type: 'boolean',
						default: true
					},
					controller_link: {
						type: 'boolean',
						default: true
					}
				},
				trx_addons_gutenberg_get_param_query( { columns: false } ),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/video-player' ),
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
						'general_params': el( wp.element.Fragment, { key: props.name + '-additional-params' }, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text'
								},
								// Autoplay
								{
									'name': 'autoplay',
									'title': __( 'Autoplay first video', "trx_addons" ),
									'type': 'boolean'
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
									'name': 'category',
									'title': __( 'Category', "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy], true )
								}
							], 'trx-addons/video-player', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props, { columns: false } ),
							// Controller params
							trx_addons_gutenberg_add_param_sc_video_list_controller( props ),
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
		'trx-addons/video-player'
	) );

	// Return details params
	//-------------------------------------------
	function trx_addons_gutenberg_add_param_sc_video_list_controller(props) {
		var el     = window.wp.element.createElement;
		var i18n   = window.wp.i18n;
		var params = [
						// Controller style
						{
							'name': 'controller_style',
							'title': __( 'Style of the TOC', "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_video_list_controller_styles'] )
						},
						// Controller position
						{
							'name': 'controller_pos',
							'title': __( 'Position of the TOC', "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_video_list_controller_positions'] )
						},
						// Controller height
						{
							'name': 'controller_height',
							'title': __( 'Max. height of the TOC', "trx_addons" ),
							'type': 'text',
							'dependency': {
								'controller_pos': [ 'bottom' ]
							}
						},
						// Autoplay
						{
							'name': 'controller_autoplay',
							'title': __( 'Autoplay selected video', "trx_addons" ),
							'type': 'boolean'
						},
						// Link to the video or to the post
						{
							'name': 'controller_link',
							'title': __( 'Show video or go to the post', "trx_addons" ),
							'type': 'boolean'
						}
		];

		return el( wp.element.Fragment, { key: props.name + '-toc-params' },
					el( trx_addons_get_wp_editor().InspectorControls, {},
						el( wp.components.PanelBody, { title: __( "Table of contents", "trx_addons" ) },
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', params, 'trx-addons/video-player-details', props ), props )
						)
					)
				);
	}

})( window.wp.blocks, window.wp.i18n, window.wp.element );
