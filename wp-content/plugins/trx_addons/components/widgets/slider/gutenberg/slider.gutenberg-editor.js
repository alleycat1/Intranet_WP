(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Slider
	blocks.registerBlockType(
		'trx-addons/slider',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Slider', "trx_addons" ),
			description: __( "Insert slider ", "trx_addons" ),
			icon: 'images-alt',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					engine: {
						type: 'string',
						default: 'swiper'
					},
					slider_id: {
						type: 'string',
						default: ''
					},
					slider_style: {
						type: 'string',
						default: 'default'
					},
					slides_per_view: {
						type: 'number',
						default: 1
					},
					slides_space: {
						type: 'number',
						default: 0
					},
					slides_parallax: {
						type: 'number',
						default: 0
					},
					slides_type: {
						type: 'string',
						default: 'bg'
					},
					slides_ratio: {
						type: 'string',
						default: '16:9'
					},
					slides_centered: {
						type: 'boolean',
						default: false
					},
					slides_overflow: {
						type: 'boolean',
						default: false
					},
					autoplay: {
						type: 'boolean',
						default: true
					},
					loop: {
						type: 'boolean',
						default: true
					},
					mouse_wheel: {
						type: 'boolean',
						default: false
					},
					free_mode: {
						type: 'boolean',
						default: false
					},
					noswipe: {
						type: 'boolean',
						default: false
					},
					noresize: {
						type: 'boolean',
						default: false
					},
					effect: {
						type: 'string',
						default: 'slide'
					},
					height: {
						type: 'string',
						default: ''
					},
					alias: {
						type: 'string',
						default: ''
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
						default: '0'
					},
					posts: {
						type: 'number',
						default: 5
					},
					speed: {
						type: 'number',
						default: 600
					},
					interval: {
						type: 'number',
						default: 7000
					},
					titles: {
						type: 'string',
						default: 'center'
					},
					large: {
						type: 'boolean',
						default: false
					},
					controls: {
						type: 'boolean',
						default: false
					},
					controls_pos: {
						type: 'string',
						default: 'side'
					},
					label_prev: {
						type: 'string',
						default: __( 'Prev|PHOTO', "trx_addons" )
					},
					label_next: {
						type: 'string',
						default: __( 'Next|PHOTO', "trx_addons" )
					},
					pagination: {
						type: 'boolean',
						default: false
					},
					pagination_type: {
						type: 'string',
						default: 'bullets'
					},
					pagination_pos: {
						type: 'string',
						default: 'bottom'
					},
					direction: {
						type: 'string',
						default: 'horizontal'
					},
					slides: {
						type: 'string',
						default: ''
					},
					slave_id: {
						type: 'string',
						default: ''
					},
					// Controller (TOC)
					controller: {
						type: 'boolean',
						default: false
					},
					controller_style: {
						type: 'string',
						default: 'default'
					},
					controller_pos: {
						type: 'string',
						default: 'right'
					},
					controller_controls: {
						type: 'boolean',
						default: false
					},
					controller_effect: {
						type: 'string',
						default: 'slide'
					},
					controller_per_view: {
						type: 'number',
						default: 3
					},
					controller_space: {
						type: 'number',
						default: 0
					},
					controller_height: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/slider' ),
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
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'type': 'text'
								},
								// Slider engine
								{
									'name': 'engine',
									'title': __( 'Slider engine', "trx_addons" ),
									'descr': __( "Select engine to show slider", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sliders_list'] )
								},
								// RevSlider alias
								{
									'name': 'alias',
									'title': __( 'RevSlider alias', "trx_addons" ),
									'descr': __( "Select previously created Revolution slider", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_revsliders'] ),
									'dependency': {
										'engine': ['revo']
									}
								},
								// Swiper style
								{
									'name': 'slider_style',
									'title': __( 'Swiper style', "trx_addons" ),
									'descr': __( "Select style of the Swiper slider", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_slider'] ),
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Slider height
								{
									'name': 'height',
									'title': __( "Slider height", "trx_addons" ),
									'descr': __( "Initial height of the slider. If empty - calculate from width and aspect ratio", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'noresize': [true]
									}
								},
								// Swiper effect
								{
									'name': 'effect',
									'title': __( 'Swiper effect', "trx_addons" ),
									'descr': __( "Select slides effect of the Swiper slider", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_effects'] ),
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Direction
								{
									'name': 'direction',
									'title': __( 'Direction', "trx_addons" ),
									'descr': __( "Select direction to change slides", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_directions'] ),
									'dependency': {
										'engine': ['swiper'],
										'effect': ['slide', 'coverflow', 'swap']
									}
								},
								// Slides per view in the Swiper
								{
									'name': 'slides_per_view',
									'title': __( 'Slides per view in the Swiper', "trx_addons" ),
									'descr': __( "Specify slides per view in the Swiper", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 6,
									'dependency': {
										'engine': ['swiper'],
										'effect': ['slide', 'coverflow', 'swap', 'cards', 'creative']
									}
								},
								// Space between slides in the Swiper
								{
									'name': 'slides_space',
									'title': __( 'Space between slides in the Swiper', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'dependency': {
										'engine': ['swiper'],
										'effect': ['slide', 'coverflow', 'swap', 'cards', 'creative']
									}
								},
								// Parallax coefficient to shift images while slides change
								{
									'name': 'slides_parallax',
									'title': __( 'Parallax coeff', "trx_addons" ),
									'descr': __( "Parallax coefficient from 0.0 to 1.0 to shift images while slides change", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 1,
									'step': 0.01,
									'dependency': {
										'engine': ['swiper'],
										'effect': ['slide'],
										'slides_per_view': [1]
									}
								},

								// Post type
								{
									'name': 'post_type',
									'title': __( 'Post type', "trx_addons" ),
									'descr': __( "Select post type to get featured images from the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] ),
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Taxonomy
								{
									'name': 'taxonomy',
									'title': __( 'Taxonomy', "trx_addons" ),
									'descr': __( "Select taxonomy to get featured images from the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type], TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].length === 0 ),
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Category
								{
									'name': 'category',
									'title': __( 'Category', "trx_addons" ),
									'descr': __( "Select category to get featured images from the posts", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['categories'][taxonomy], true ),
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Posts number
								{
									'name': 'posts',
									'title': __( 'Posts number', "trx_addons" ),
									'descr': __( "Number of posts or comma separated post's IDs to show images", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},

								// Controls
								{
									'name': 'controls',
									'title': __( 'Controls', "trx_addons" ),
									'descr': __( "Do you want to show arrows to change slides?", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Controls position
								{
									'name': 'controls_pos',
									'title': __( 'Controls position', "trx_addons" ),
									'descr': __( "Select controls position", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_controls'] ),
									'dependency': {
										'engine': ['swiper'],
										'controls': [true]
									}
								},
								// Prev Slide
								{
									'name': 'label_prev',
									'title': __( 'Prev Slide', "trx_addons" ),
									'descr': __( "Label of the 'Prev Slide' button in the Swiper (Modern style). Use '|' to break line", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'slider_style': ['modern'],
										'controls': [true]
									}
								},
								// Next Slide
								{
									'name': 'label_next',
									'title': __( 'Next Slide', "trx_addons" ),
									'descr': __( "Label of the 'Next Slide' button in the Swiper (Modern style). Use '|' to break line", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'slider_style': ['modern'],
										'controls': [true]
									}
								},

								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Do you want to show bullets to change slides?", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Pagination type
								{
									'name': 'pagination_type',
									'title': __( 'Pagination type', "trx_addons" ),
									'descr': __( "Select type of the pagination", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_paginations_types'] ),
									'dependency': {
										'pagination': [true]
									}
								},
								// Pagination position
								{
									'name': 'pagination_pos',
									'title': __( 'Pagination position', "trx_addons" ),
									'descr': __( "Select pagination position", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_paginations'] ),
									'dependency': {
										'pagination': [true]
									}
								},

								// Disable swipe
								{
									'name': 'noswipe',
									'title': __( 'Disable swipe', "trx_addons" ),
									'descr': __( "Disable swipe guestures", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},

								// Enable mouse wheel
								{
									'name': 'mouse_wheel',
									'title': __( 'Enable mouse wheel', "trx_addons" ),
									'descr': __( "Enable mouse wheel to control slidest", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},

								// Enable free mode
								{
									'name': 'free_mode',
									'title': __( 'Enable free mode', "trx_addons" ),
									'descr': __( "Free mode - slides will not have fixed positions", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},

								// Enable loop
								{
									'name': 'loop',
									'title': __( 'Enable loop mode', "trx_addons" ),
									'descr': __( "Enable loop mode for this slider", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},

								// Enable autoplay
								{
									'name': 'autoplay',
									'title': __( 'Enable autoplay', "trx_addons" ),
									'descr': __( "Enable autoplay for this slider", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Slides change speed in the Swiper
								{
									'name': 'speed',
									'title': __( 'Slides change speed', "trx_addons" ),
									'descr': __( "Specify slides change speed in the Swiper", "trx_addons" ),
									'type': 'number',
									'min': 300,
									'max': 3000,
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Interval between slides in the Swiper
								{
									'name': 'interval',
									'title': __( 'Interval between slides in the Swiper', "trx_addons" ),
									'descr': __( "Specify interval between slides change in the Swiper", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 10000,
									'dependency': {
										'engine': ['swiper']
									}
								},

								// No resize slide's content
								{
									'name': 'noresize',
									'title': __( "No resize slide's content", "trx_addons" ),
									'descr': __( "Disable resize slide's content, stretch images to cover slide", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},

								// Type of the slides content
								{
									'name': 'slides_type',
									'title': __( 'Type of the slides content', "trx_addons" ),
									'descr': __( "Use images from slides as background (default) or insert it as tag inside each slide", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['slides_type'] ),
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Slides ratio
								{
									'name': 'slides_ratio',
									'title': __( "Slides ratio", "trx_addons" ),
									'descr': __( "Ratio to resize slides on tabs and mobile. If empty - 16:9", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'noresize': [false]
									}
								},
								// Slides centered
								{
									'name': 'slides_centered',
									'title': __( 'Slides centered', "trx_addons" ),
									'descr': __( "Center active slide", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Slides overflow visible
								{
									'name': 'slides_overflow',
									'title': __( 'Slides overflow visible', "trx_addons" ),
									'descr': __( "Don't hide slides outside the borders of the viewport", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},
								// Titles in the Swiper
								{
									'name': 'titles',
									'title': __( 'Titles in the slides', "trx_addons" ),
									'descr': __( "Show post's titles and categories on the slides", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_titles'] ),
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},
								// Large titles
								{
									'name': 'large',
									'title': __( 'Large titles', "trx_addons" ),
									'descr': __( "Do you want use large titles?", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper', 'elastistack']
									}
								},

								// Controller (TOC)
								{
									'name': 'controller',
									'title': __( 'Table of contents', "trx_addons" ),
									'descr': '',
									'type': 'boolean',
									'dependency': {
										'engine': ['swiper']
									}
								},
								{
									'name': 'controller_style',
									'title': __( 'Style of the TOC', "trx_addons" ),
									'descr': '',
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_toc_styles'] ),
									'dependency': {
										'controller': [true]
									}
								},
								{
									'name': 'controller_pos',
									'title': __( 'Position of the TOC', "trx_addons" ),
									'descr': '',
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_toc_positions'] ),
									'dependency': {
										'controller': [true]
									}
								},
								{
									'name': 'controller_controls',
									'title': __( 'Show arrows', "trx_addons" ),
									'descr': '',
									'type': 'boolean',
									'dependency': {
										'controller': [true]
									}
								},
								{
									'name': 'controller_effect',
									'title': __( 'Effect for change items', "trx_addons" ),
									'descr': '',
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_effects'] ),
									'dependency': {
										'controller': [true]
									}
								},
								{
									'name': 'controller_per_view',
									'title': __( 'Items per view', "trx_addons" ),
									'descr': '',
									'type': 'number',
									'min': 1,
									'max': 10,
									'dependency': {
										'controller': [true],
										'controller_effect': ['slide','coverflow', 'swap', 'cards', 'creative']
									}
								},
								{
									'name': 'controller_space',
									'title': __( 'Space between items', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'dependency': {
										'controller': [true]
									}
								},
								{
									'name': 'controller_height',
									'title': __( "Height of the TOC", "trx_addons" ),
									'descr': '',
									'type': 'text',
									'dependency': {
										'controller': [true],
										'controller_pos': ['bottom']
									}
								},

								// Slave ID
								{
									'name': 'slave_id',
									'title': __( "Slave ID", "trx_addons" ),
									'descr': '',
									'type': 'text',
									'dependency': {
										'engine': ['swiper']
									}
								}
							], 'trx-addons/slider', props ), props )
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
					props.attributes.slides = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/slider'
	) );

	// Register block Slider Item
	blocks.registerBlockType(
		'trx-addons/slider-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Slide', "trx_addons" ),
			description: __( "Select icons, specify title and/or description for each item", "trx_addons" ),
			icon: 'images-alt',
			category: 'trx-addons-widgets',
			parent: ['trx-addons/slider'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: ''
				},
				subtitle: {
					type: 'string',
					default: ''
				},
				link: {
					type: 'string',
					default: ''
				},
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				video_url: {
					type: 'string',
					default: ''
				},
				video_embed: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/slider-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Slide', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {}, 
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the item", "trx_addons" ),
									'type': 'text'
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'descr': __( "Enter subtitle of the item", "trx_addons" ),
									'type': 'text'
								},
								// Link
								{
									'name': 'link',
									'title': __( 'Link', "trx_addons" ),
									'descr': __( "URL to link this item", "trx_addons" ),
									'type': 'text'
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image'
								},
								// Video URL
								{
									'name': 'video_url',
									'title': __( 'Video URL', "trx_addons" ),
									'descr': __( "Enter link to the video (Note: read more about available formats at WordPress Codex page)", "trx_addons" ),
									'type': 'text'
								},
								// Video embed code
								{
									'name': 'video_embed',
									'title': __( 'Video embed code', "trx_addons" ),
									'descr': __( "or paste the HTML code to embed video in this slide", "trx_addons" ),
									'type': 'textarea'
								}
							], 'trx-addons/slider-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/slider-item'
	) );

	// Register Block - Slider Controller
	blocks.registerBlockType(
		'trx-addons/slider-controller',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Slider Controller', "trx_addons" ),
			description: __( "Insert slider controller", "trx_addons" ),
			icon: 'images-alt',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				slider_id: {
					type: 'string',
					default: ''
				},
				height: {
					type: 'string',
					default: ''
				},
				controls: {
					type: 'boolean',
					default: false
				},
				controller_style: {
					type: 'string',
					default: 'thumbs'
				},
				effect: {
					type: 'string',
					default: 'slide'
				},
				direction: {
					type: 'string',
					default: 'horizontal'
				},
				slides_per_view: {
					type: 'number',
					default: 1
				},
				slides_space: {
					type: 'number',
					default: 0
				},
				interval: {
					type: 'number',
					default: 7000
				},
				// ID, Class, CSS attributes
				id: {
					type: 'string',
					default: ''
				},
				class: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				},
				css: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/slider-controller' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Controlled Slider ID
								{
									'name': 'slider_id',
									'title': __( 'Slave slider ID', "trx_addons" ),
									'descr': __( "ID of the controlled slider", "trx_addons" ),
									'type': 'text'
								},
								// Slider height
								{
									'name': 'height',
									'title': __( "Slider height", "trx_addons" ),
									'descr': __( "Initial height of the slider. If empty - calculate from width and aspect ratio", "trx_addons" ),
									'type': 'text'
								},
								// Controls
								{
									'name': 'controls',
									'title': __( 'Controls', "trx_addons" ),
									'descr': __( "Do you want to show arrows to change slides?", "trx_addons" ),
									'type': 'boolean'
								},
								// Controller style
								{
									'name': 'controller_style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select style of the Controller", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_controller_styles'] )
								},
								// Swiper effect
								{
									'name': 'effect',
									'title': __( 'Effect', "trx_addons" ),
									'descr': __( "Select slides effect of the controller slider", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_effects'] )
								},
								// Direction
								{
									'name': 'direction',
									'title': __( 'Direction', "trx_addons" ),
									'descr': __( "Select direction to change slides", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_directions'] ),
									'dependency': {
										'effect': ['slide']
									}
								},
								// Slides per view in the Swiper
								{
									'name': 'slides_per_view',
									'title': __( 'Slides per view', "trx_addons" ),
									'descr': __( "Specify slides per view in the Swiper", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 6,
									'dependency': {
										'effect': ['slide', 'coverflow', 'swap', 'cards', 'creative']
									}
								},
								// Space between slides in the Swiper
								{
									'name': 'slides_space',
									'title': __( 'Space between slides', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'dependency': {
										'effect': ['slide', 'coverflow', 'swap', 'cards', 'creative']
									}
								},
								// Interval between slides in the Swiper
								{
									'name': 'interval',
									'title': __( 'Interval between slides change', "trx_addons" ),
									'descr': __( "Specify interval between slides change", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'min': 10000
								}
							], 'trx-addons/slider-controller', props ), props )
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
			},
		},
		'trx-addons/slider-controller'
	) );

	// Register Block - Slider Controls
	blocks.registerBlockType(
		'trx-addons/slider-controls',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Slider Controls', "trx_addons" ),
			description: __( "Insert slider controls", "trx_addons" ),
			icon: 'images-alt',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				slider_id: {
					type: 'string',
					default: ''
				},
				controls_style: {
					type: 'string',
					default: 'default'
				},
				align: {
					type: 'string',
					default: 'left'
				},
				hide_prev: {
					type: 'boolean',
					default: false
				},
				title_prev: {
					type: 'string',
					default: ''
				},
				hide_next: {
					type: 'boolean',
					default: false
				},
				title_next: {
					type: 'string',
					default: ''
				},
				pagination_style: {
					type: 'string',
					default: 'none'
				},
				// ID, Class, CSS attributes
				id: {
					type: 'string',
					default: ''
				},
				class: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				},
				css: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/slider-controls' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Controlled Slider ID
								{
									'name': 'slider_id',
									'title': __( 'Slave slider ID', "trx_addons" ),
									'descr': __( "ID of the controlled slider", "trx_addons" ),
									'type': 'text'
								},
								// Controls style
								{
									'name': 'controls_style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select style of the Controls", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_controls_styles'] )
								},
								// Alignment
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Select alignment of the arrows", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns_short'] )
								},
								// Hide 'Prev'
								{
									'name': 'hide_prev',
									'title': __( "Hide 'Prev'", "trx_addons" ),
									'descr': __( "Hide the button 'Prev'", "trx_addons" ),
									'type': 'boolean'
								},
								// Title 'Prev'
								{
									'name': 'title_prev',
									'title': __( "Title 'Prev'", "trx_addons" ),
									'descr': __( "Title of the button 'Prev'", "trx_addons" ),
									'type': 'text'
								},
								// Hide 'Next'
								{
									'name': 'hide_next',
									'title': __( "Hide 'Next'", "trx_addons" ),
									'descr': __( "Hide the button 'Next'", "trx_addons" ),
									'type': 'boolean'
								},
								// Title 'Next'
								{
									'name': 'title_next',
									'title': __( "Title 'Next'", "trx_addons" ),
									'descr': __( "Title of the button 'Next'", "trx_addons" ),
									'type': 'text'
								},
								// Pagination
								{
									'name': 'pagination_style',
									'title': __( 'Show pagination', "trx_addons" ),
									'descr': __( "Select pagination style of the controls", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_controls_paginations_types'] )
								}
							], 'trx-addons/slider-controls', props ), props )
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
			},
		},
		'trx-addons/slider-controls'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
