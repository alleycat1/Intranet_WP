// Utilities
//-------------------------------------------
function trx_addons_get_wp_editor() {

	"use strict";

	return wp.blockEditor && wp.blockEditor.hasOwnProperty('InspectorControls') ? wp.blockEditor : wp.editor;
}


// Return query params
//-------------------------------------------
function trx_addons_gutenberg_get_param_query( add_params ) {

	"use strict";

	if ( add_params === undefined ) {
		add_params = {};
	}

	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_get_params',
					{
						// Query attributes
						ids: {
							'type': 'string',
							'default': ''
						},
						count: {
							'type': 'number',
							'default': 2
						},
						columns: {
							'type': 'number',
							'default': 2
						},
						offset: {
							'type': 'number',
							'default': 0
						},
						orderby: {
							'type': 'string',
							'default': 'none'
						},
						order: {
							'type': 'string',
							'default': 'asc'
						}
					},
					'common/query'
				);
	for (var prop in add_params) {
		if ( add_params.hasOwnProperty(prop) ) {
			if ( add_params[prop] === false ) {
				if ( params.hasOwnProperty(prop) ) {
					delete params[prop];
				}
			} else {
				params[prop] = add_params[prop];
			}
		}
	}	
	return params;
}

function trx_addons_gutenberg_add_param_query(props, add_params) {

	"use strict";

	if ( add_params === undefined ) {
		add_params = {};
	}

	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// IDs to show
						{
							'name': 'ids',
							'title': __( "IDs to show", "trx_addons" ),
							'descr': __( "Comma separated list of IDs to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", "trx_addons" ),
							'type': 'text'
						},
						// Count
						{
							'name': 'count',
							'title': __( "Count", "trx_addons" ),
							'descr': __( "The number of displayed posts. If IDs are used, this parameter is ignored.", "trx_addons" ),
							'type': 'number',
							'min': 1,
							'dependency': {
								'ids': ['']
							}
						},
						// Columns
						add_params.hasOwnProperty( 'columns' ) && add_params['columns'] === false
							? null
							: {
								'name': 'columns',
								'title': __( "Columns", "trx_addons" ),
								'descr': __( "Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", "trx_addons" ),
								'type': 'number',
								'min': 1,
								'max': 6
								},
						// Offset
						{
							'name': 'offset',
							'title': __( "Offset", "trx_addons" ),
							'descr': __( "Specify the number of items to be skipped before the displayed items.", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'dependency': {
								'ids': ['']
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
					],
					'common/query',
					props
				);
	return el( wp.element.Fragment, { key: props.name + '-query-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "Query", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}



// Return filters params
//-------------------------------------------
function trx_addons_gutenberg_get_param_filters() {

	"use strict";

	var __ = window.wp.i18n.__;

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// Filters
				show_filters: {
					'type': 'boolean',
					'default': false
				},
				filters_tabs_position: {
					'type': 'string',
					'default': 'top'
				},
				filters_tabs_on_hover: {
					'type': 'boolean',
					'default': false
				},
				filters_title: {
					'type': 'string',
					'default': ''
				},
				filters_subtitle: {
					'type': 'string',
					'default': ''
				},
				filters_title_align: {
					'type': 'string',
					'default': 'left'
				},
				filters_taxonomy: {
					'type': 'string',
					'default': 'category'
				},
				filters_ids: {
					'type': 'string',
					'default': ''
				},
				filters_all: {
					'type': 'boolean',
					'default': true
				},
				filters_all_text: {
					'type': 'string',
					'default': __( 'All', "trx_addons" )
				},
				filters_more_text: {
					'type': 'string',
					'default': __( 'More posts', "trx_addons" )
				}
			},
			'common/filters'
		);
}

function trx_addons_gutenberg_add_param_filters(props) {
	
	"use strict";
	
	var post_type = props.attributes.post_type,
		filters_taxonomy  = props.attributes.filters_taxonomy;

	// Change a default value of an attributes (if need)
	var atts = {}, need_update = false;
	if ( typeof TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type] == 'undefined' ) {
		atts.post_type = post_type = 'post';
		need_update = true;
	}
	if ( ! TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].hasOwnProperty( filters_taxonomy ) ) {
		atts.filters_taxonomy = filters_taxonomy = trx_addons_array_first_key( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type] );
		need_update = true;
	}
	if ( need_update ) {
		trx_addons_gutenberg_set_attributes_from_edit( props, atts );
	}
	
	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Filters title
						{
							'name': 'filters_title',
							'title': __( 'Filters area title', "trx_addons" ),
							'descr': '',
							'type': 'text'
						},
						// Filters subtitle
						{
							'name': 'filters_subtitle',
							'title': __( 'Filters area subtitle', "trx_addons" ),
							'descr': '',
							'type': 'text'
						},
						// Filters alignment
						{
							'name': 'filters_title_align',
							'title': __( 'Filters titles position', "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns_short'] )
						},
						// Show filters
						{
							'name': 'show_filters',
							'title': __( 'Show filters tabs', "trx_addons" ),
							'type': 'boolean'
						},
						// Filters tabs position
						{
							'name': 'filters_tabs_position',
							'title': __( 'Filters tabs position', "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_blogger_tabs_positions'] ),
							'dependency': {
								'show_filters': [true]
							}
						},
						// Open tabs on hover
						{
							'name': 'filters_tabs_on_hover',
							'title': __( 'Open tabs on hover', "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'show_filters': [true]
							}
						},
						// Filters taxonomy
						{
							'name': 'filters_taxonomy',
							'title': __( 'Filters taxonomy', "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type], TRX_ADDONS_STORAGE['gutenberg_sc_params']['taxonomies'][post_type].length === 0 ),
							'dependency': {
								'show_filters': [true]
							}
						},
						// Filters terms to show
						{
							'name': 'filters_ids',
							'title': __( 'Filters terms to show', "trx_addons" ),
							'descr': __( "Comma separated list with term IDs or term names to show as filters. If empty - show all terms from filters taxonomy above", "trx_addons" ),
							'type': 'text',
							'dependency': {
								'show_filters': [true]
							}
						},
						// Display the "All Filters" tab
						{
							'name': 'filters_all',
							'title': __( 'Show the "All" tab', "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'show_filters': [true]
							}
						},
						// "All Filters" tab text
						{
							'name': 'filters_all_text',
							'title': __( '"All" tab text', "trx_addons" ),
							'type': 'text',
							'dependency': {
								'show_filters': [true]
							}
						},
						// 'More posts' text
						{
							'name': 'filters_more_text',
							'title': __( "'More posts' text", "trx_addons" ),
							'type': 'text',
							'dependency': {
								'show_filters': [false]
							}
						}
					],
					'common/filters',
					props
				);

	return el( wp.element.Fragment, { key: props.name + '-filters-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "Filters", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}



// Return slider params
//-------------------------------------------
function trx_addons_gutenberg_get_param_slider() {

	"use strict";

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// Slider attributes
				slider: {
					'type': 'boolean',
					'default': false
				},
				slides_space: {
					'type': 'number',
					'default': 0
				},
				slides_centered: {
					'type': 'boolean',
					'default': false
				},
				slides_overflow: {
					'type': 'boolean',
					'default': false
				},
				slider_mouse_wheel: {
					'type': 'boolean',
					'default': false
				},
				slider_autoplay: {
					'type': 'boolean',
					'default': true
				},
				slider_free_mode: {
					'type': 'boolean',
					'default': false
				},
				slider_loop: {
					'type': 'boolean',
					'default': true
				},
				slider_controls: {
					'type': 'string',
					'default': 'none'
				},
				slider_pagination: {
					'type': 'string',
					'default': 'none'
				},
				slider_pagination_type: {
					'type': 'string',
					'default': 'bullets'
				}
			},
			'common/slider'
		);
}

function trx_addons_gutenberg_add_param_slider(props) {

	"use strict";

	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Slider
						{
							'name': 'slider',
							'title': __( "Slider", "trx_addons" ),
							'descr': __( "Show items as slider", "trx_addons" ),
							'type': 'boolean'
						},
						// Space
						{
							'name': 'slides_space',
							'title': __( "Space", "trx_addons" ),
							'descr': __( "Space between slides", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'max': 50,
							'dependency': {
								'slider': [true]
							}
						},
						// Slides centered
						{
							'name': 'slides_centered',
							'title': __( "Slides centered", "trx_addons" ),
							'descr': __( "Center active slide", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true]
							}
						},
						// Slides overflow visible
						{
							'name': 'slides_overflow',
							'title': __( "Slides overflow visible", "trx_addons" ),
							'descr': __( "Don't hide slides outside the borders of the viewport", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true]
							}
						},
						// Enable mouse wheel
						{
							'name': 'slider_mouse_wheel',
							'title': __( "Enable mouse wheel", "trx_addons" ),
							'descr': __( "Enable mouse wheel to control slides", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true]
							}
						},
						// Enable autoplay
						{
							'name': 'slider_autoplay',
							'title': __( "Enable autoplay", "trx_addons" ),
							'descr': __( "Enable autoplay for this slider", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true]
							}
						},
						// Enable free mode
						{
							'name': 'slider_free_mode',
							'title': __( "Enable free mode", "trx_addons" ),
							'descr': __( "Free mode - slides will not have fixed positions", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true]
							}
						},
						// Slider loop
						{
							'name': 'slider_loop',
							'title': __( "Loop", "trx_addons" ),
							'descr': __( "Enable slider loop", "trx_addons" ),
							'type': 'boolean',
							'dependency': {
								'slider': [true],
								'slides_overflow': [false]
							}
						},
						// Slider controls
						{
							'name': 'slider_controls',
							'title': __( "Slider controls", "trx_addons" ),
							'descr': __( "Show arrows in the slider", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_controls'] ),
							'dependency': {
								'slider': [true]
							}
						},
						// Slider pagination
						{
							'name': 'slider_pagination',
							'title': __( "Slider pagination", "trx_addons" ),
							'descr': __( "Show pagination in the slider", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_paginations'] ),
							'dependency': {
								'slider': [true]
							}
						},
						// Slider pagination type
						{
							'name': 'slider_pagination_type',
							'title': __( "Slider pagination type", "trx_addons" ),
							'descr': __( "Select type of the pagination in the slider", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_slider_paginations_types'] ),
							'dependency': {
								'slider': [true],
								'slider_pagination': ['^none']
							}
						}
					],
					'common/slider',
					props
				);

	return el( wp.element.Fragment, { key: props.name + '-slider-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "Slider", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}



// Return button params
//-------------------------------------------
function trx_addons_gutenberg_get_param_button() {

	"use strict";

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// Button attributes
				link: {
					'type': 'string',
					'default': ''
				},
				link_text: {
					'type': 'string',
					'default': ''
				},
				link_size: {
					'type': 'string',
					'default': 'normal'
				},
				link_style: {
					'type': 'string',
					'default': ''
				},
				link_image: {
					'type': 'number',
					'default': 0
				},
				link_image_url: {
					'type': 'string',
					'default': ''
				}
			},
			'common/button'
		);
}

function trx_addons_gutenberg_add_param_button(props, return_args) {

	"use strict";

	var el   = window.wp.element.createElement;
	var __   = window.wp.i18n.__;
	var attr = props.attributes;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Button's URL
						{
							'name': 'link',
							'title': __( "Button's URL", "trx_addons" ),
							'descr': __( "Link URL for the button at the bottom of the block", "trx_addons" ),
							'type': 'text'
						},
						// Button's text
						{
							'name': 'link_text',
							'title': __( "Button's text", "trx_addons" ),
							'descr': __( "Caption for the button at the bottom of the block", "trx_addons" ),
							'type': 'text'
						},
						// Button's size
						{
							'name': 'link_size',
							'title': __( "Button's size", "trx_addons" ),
							'descr': __( "Select the size of the button", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_button_sizes'] )
						},
						// Button's style
						{
							'name': 'link_style',
							'title': __( "Button's style", "trx_addons" ),
							'descr': __( "Select the style (layout) of the button", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_button'] )
						},
						// Button's image
						{
							'name': 'link_image',
							'name_url': 'link_image_url',
							'title': __( "Button's image", "trx_addons" ),
							'descr': __( "Select the promo image from the library for this button", "trx_addons" ),
							'type': 'image'
						}
					],
					'common/button',
					props
				);
	return return_args
				? params
				: el( wp.element.Fragment, { key: props.name + '-button-params' },
					el( trx_addons_get_wp_editor().InspectorControls, {},
						el( wp.components.PanelBody, { title: __( "Button", "trx_addons" ) },
							el( 'div', { className: 'components-panel__body-fieldset' },
								trx_addons_gutenberg_add_params( params, props )
							)
						)
					)
				);
}



// Return button 2 params
//-------------------------------------------
function trx_addons_gutenberg_get_param_button2() {

	"use strict";

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// Button attributes
				link2: {
					'type': 'string',
					'default': ''
				},
				link2_text: {
					'type': 'string',
					'default': ''
				},
				link2_style: {
					'type': 'string',
					'default': ''
				}
			},
			'common/button2'
		);
}

function trx_addons_gutenberg_add_param_button2( props, return_args ) {

	"use strict";

	var el   = window.wp.element.createElement;
	var __   = window.wp.i18n.__;
	var attr = props.attributes;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Button 2 URL
						{
							'name': 'link2',
							'title': __( 'Button 2 URL', "trx_addons" ),
							'descr': __( "URL for the second button (at the side of the image)", "trx_addons" ),
							'type': 'text',
							'dependency': {
								'type': ['modern']
							}
						},
						// Button 2 text
						{
							'name': 'link2_text',
							'title': __( 'Button 2 text', "trx_addons" ),
							'descr': __( "Caption for the second button (at the side of the image)", "trx_addons" ),
							'type': 'text',
							'dependency': {
								'type': ['modern']
							}
						},
						// Button 2 style
						{
							'name': 'link2_style',
							'title': __( 'Button 2 style', "trx_addons" ),
							'descr': __( "Select the style (layout) of the second button", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_button'] ),
							'dependency': {
								'type': ['modern']
							}
						}
					],
					'common/button2',
					props
				);
	return return_args
				? params
				: el( wp.element.Fragment, { key: props.name + '-button2-params' },
					el( trx_addons_get_wp_editor().InspectorControls, {},
						el( wp.components.PanelBody, { title: __( "Button 2", "trx_addons" ) },
							el( 'div', { className: 'components-panel__body-fieldset' },
								trx_addons_gutenberg_add_params( params, props )
							)
						)
					)
				);
}



// Return title params
//-------------------------------------------
function trx_addons_gutenberg_get_param_title() {

	"use strict";

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// Title attributes
				title_style: {
					'type': 'string',
					'default': ''
				},
				title_tag: {
					'type': 'string',
					'default': ''
				},
				title_align: {
					'type': 'string',
					'default': ''
				},
				title: {
					'type': 'string',
					'default': ''
				},
				title_color: {
					'type': 'string',
					'default': ''
				},
				title_color2: {
					'type': 'string',
					'default': ''
				},
				gradient_direction: {
					'type': 'number',
					'default': 0
				},
				title_border_color: {
					'type': 'string',
					'default': ''
				},
				title_border_width: {
					'type': 'number',
					'default': 0
				},
				title_bg_image: {
					type: 'number',
					default: 0
				},
				title_bg_image_url: {
					type: 'string',
					default: ''
				},
				title2: {
					'type': 'string',
					'default': ''
				},
				title2_color: {
					'type': 'string',
					'default': ''
				},
				title2_border_color: {
					'type': 'string',
					'default': ''
				},
				title2_border_width: {
					'type': 'number',
					'default': 0
				},
				title2_bg_image: {
					type: 'number',
					default: 0
				},
				title2_bg_image_url: {
					type: 'string',
					default: ''
				},
				subtitle: {
					'type': 'string',
					'default': ''
				},
				subtitle_align: {
					'type': 'string',
					'default': 'none'
				},
				subtitle_position: {
					'type': 'string',
					'default': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_subtitle_position']
				},
				subtitle_color: {
					'type': 'string',
					'default': ''
				},
				description: {
					'type': 'string',
					'default': ''
				},
				description_color: {
					'type': 'string',
					'default': ''
				},
				mouse_helper_highlight: {
					'type': 'boolean',
					'default': false
				},
				typed: {
					'type': 'boolean',
					'default': false
				},
				typed_loop: {
					'type': 'boolean',
					'default': true
				},
				typed_cursor: {
					'type': 'boolean',
					'default': true
				},
				typed_strings: {
					'type': 'string',
					'default': ''
				},
				typed_color: {
					'type': 'string',
					'default': ''
				},
				typed_speed: {
					'type': 'number',
					'default': 6
				},
				typed_delay: {
					'type': 'number',
					'default': 1
				}
			},
			'common/title'
		);
}

function trx_addons_gutenberg_add_param_title(props, button, button2) {

	"use strict";

	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	var attr   = props.attributes;
	var params = [
						// Title style
						{
							'name': 'title_style',
							'title': __( 'Title style', "trx_addons" ),
							'descr': __( "Select style of the title and subtitle", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_title'] )
						},
						// Title tag
						{
							'name': 'title_tag',
							'title': __( 'Title tag', "trx_addons" ),
							'descr': __( "Select tag (level) of the title", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_title_tags'] )
						},
						// Title alignment
						{
							'name': 'title_align',
							'title': __( 'Title alignment', "trx_addons" ),
							'descr': __( "Select alignment of the title, subtitle and description", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
						},
						// Title
						{
							'name': 'title',
							'title': __( 'Title', "trx_addons" ),
							'descr': __( "Title of the block. Enclose any words in {{ and }} to make them italic or in (( and )) to make them bold. If title style is 'accent' - bolded element styled as shadow, italic - as a filled circle", "trx_addons" ),
							'type': 'textarea'
						},
						// Color
						{
							'name': 'title_color',
							'title': __( 'Color', "trx_addons" ),
							'descr': __( "Title custom color", "trx_addons" ),
							'type': 'color'
						},
						// Color 2
						{
							'name': 'title_color2',
							'title': __( 'Color 2', "trx_addons" ),
							'descr': __( "Used for gradient.", "trx_addons" ),
							'type': 'color',
							'dependency': {
								'title_style': ['gradient']
							}
						},
						// Gradient direction
						{
							'name': 'gradient_direction',
							'title': __( 'Gradient direction', "trx_addons" ),
							'descr': __( "Gradient direction in degress (0 - 360)", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'max': 360,
							'step': 1,
							'dependency': {
								'title_style': ['gradient']
							}
						},
						// Border Color
						{
							'name': 'title_border_color',
							'title': __( 'Border Color', "trx_addons" ),
							'descr': __( "Title border color", "trx_addons" ),
							'type': 'color'
						},
						// Border width
						{
							'name': 'title_border_width',
							'title': __( 'Border width', "trx_addons" ),
							'descr': __( "Title border width (in px)", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'max': 10,
							'step': 1
						},
						// Image
						{
							'name': 'title_bg_image',
							'name_url': 'title_bg_image_url',
							'title': __( 'Background image', "trx_addons" ),
							'type': 'image'
						},
						// Title 2
						{
							'name': 'title2',
							'title': __( 'Title part 2', "trx_addons" ),
							'descr': __( "Use this parameter if you want to separate title parts with different color, border or background", "trx_addons" ),
							'type': 'text'
						},
						// Title 2 Color
						{
							'name': 'title2_color',
							'title': __( 'Color', "trx_addons" ),
							'descr': __( "Title 2 custom color", "trx_addons" ),
							'type': 'color'
						},
						// Title 2 Border Color
						{
							'name': 'title2_border_color',
							'title': __( 'Border color', "trx_addons" ),
							'descr': __( "Title 2 border color", "trx_addons" ),
							'type': 'color'
						},
						// Title 2 Border width
						{
							'name': 'title2_border_width',
							'title': __( 'Border width', "trx_addons" ),
							'descr': __( "Title 2 border width (in px)", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'max': 10,
							'step': 1
						},
						// Title 2 Image
						{
							'name': 'title2_bg_image',
							'name_url': 'title2_bg_image_url',
							'title': __( 'Background image', "trx_addons" ),
							'type': 'image'
						},
						// Highlight on mouse hover
						{
							'name': 'mouse_helper_highlight',
							'title': __( 'Highlight on mouse hover', "trx_addons" ),
							'descr': __( 'Used only if option "Mouse helper" is on in the Theme Panel - ThemeREX Addons settings', "trx_addons" ),
							'type': 'boolean'
						},
						// Autotype
						{
							'name': 'typed',
							'title': __( 'Use autotype', "trx_addons" ),
							'descr': '',
							'type': 'boolean'
						},
						// Autotype loop
						{
							'name': 'typed_loop',
							'title': __( 'Autotype loop', "trx_addons" ),
							'descr': '',
							'dependency': {
								'typed': [true]
							},
							'type': 'boolean'
						},
						// Autotype cursor
						{
							'name': 'typed_cursor',
							'title': __( 'Autotype cursor', "trx_addons" ),
							'descr': '',
							'dependency': {
								'typed': [true]
							},
							'type': 'boolean'
						},
						// Autotype strings
						{
							'name': 'typed_strings',
							'title': __( 'Alternative strings', "trx_addons" ),
							'descr': __( "Alternative strings to type. Attention! First string must be equal of the part of the title.", "trx_addons" ),
							'dependency': {
								'typed': [true]
							},
							'rows': 5,
							'type': 'textarea'
						},
						// Color
						{
							'name': 'typed_color',
							'title': __( 'Autotype color', "trx_addons" ),
							'descr': '',
							'dependency': {
								'typed': [true]
							},
							'type': 'color'
						},
						// Autotype speed
						{
							'name': 'typed_speed',
							'title': __( "Autotype speed", "trx_addons" ),
							'descr': __( "Typing speed from 1 (min) to 10 (max)", "trx_addons" ),
							'type': 'number',
							'min': 1,
							'max': 10,
							'step': 0.5,
							'dependency': {
								'typed': [true]
							}
						},
						// Autotype delay
						{
							'name': 'typed_delay',
							'title': __( "Autotype delay", "trx_addons" ),
							'descr': __( "Delay before erase text", "trx_addons" ),
							'type': 'number',
							'min': 0,
							'max': 10,
							'step': 0.5,
							'dependency': {
								'typed': [true]
							}
						},
						// Subtitle
						{
							'name': 'subtitle',
							'title': __( 'Subtitle', "trx_addons" ),
							'descr': __( "Subtitle of the block", "trx_addons" ),
							'type': 'text'
						},
						// Subtitle alignment
						{
							'name': 'subtitle_align',
							'title': __( 'Subtitle alignment', "trx_addons" ),
							'descr': __( "Select alignment of the subtitle", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
						},
						// Subtitle position
						{
							'name': 'subtitle_position',
							'title': __( 'Subtitle position', "trx_addons" ),
							'descr': __( "Select position of the subtitle", "trx_addons" ),
							'type': 'select',
							'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_subtitle_positions'] )
						},
						// Subtitle color
						{
							'name': 'subtitle_color',
							'title': __( 'Subtitle color', "trx_addons" ),
							'descr': __( "Subtitle custom color", "trx_addons" ),
							'type': 'color'
						},
						// Description
						{
							'name': 'description',
							'title': __( 'Description', "trx_addons" ),
							'descr': __( "Description of the block", "trx_addons" ),
							'type': 'textarea'
						},
						// Description color
						{
							'name': 'description_color',
							'title': __( 'Description color', "trx_addons" ),
							'descr': __( "Description custom color", "trx_addons" ),
							'type': 'color'
						}
					];

	// Add Button
	if ( button ) params = params.concat( trx_addons_gutenberg_add_param_button( props, true ) );
	// Button 2
	if ( button2 ) params = params.concat( trx_addons_gutenberg_add_param_button2( props, true ) );

	params = trx_addons_apply_filters( 'trx_addons_gb_map_add_params', params, 'common/title', props );

	return el( wp.element.Fragment, { key: props.name + '-title-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "Title", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}



// Hide on devices params
//-------------------------------------------
function trx_addons_gutenberg_get_param_hide(frontpage) {

	"use strict";

	return trx_addons_apply_filters(
				'trx_addons_gb_map_get_params',
				trx_addons_object_merge(
					{
						// Hide on devices attributes
						hide_on_wide: {
							'type': 'boolean',
							'default': false
						},
						hide_on_desktop: {
							'type': 'boolean',
							'default': false
						},
						hide_on_notebook: {
							'type': 'boolean',
							'default': false
						},
						hide_on_tablet: {
							'type': 'boolean',
							'default': false
						},
						hide_on_mobile: {
							'type': 'boolean',
							'default': false
						}
					},
					! frontpage ? {} : {
						hide_on_frontpage: {
							'type': 'boolean',
							'default': false
						},
						hide_on_singular: {
							'type': 'boolean',
							'default': false
						},
						hide_on_other: {
							'type': 'boolean',
							'default': false
						}			
					}
				),
				'common/hide'
			);
}

function trx_addons_gutenberg_add_param_hide(props, hide_on_frontpage) {

	"use strict";

	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Hide on wide
						{
							'name': 'hide_on_wide',
							'title': __( 'Hide on wide', "trx_addons" ),
							'descr': __( "Hide this item on wide screens", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on desktops
						{
							'name': 'hide_on_desktop',
							'title': __( 'Hide on desktops', "trx_addons" ),
							'descr': __( "Hide this item on desktops", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on notebooks
						{
							'name': 'hide_on_notebook',
							'title': __( 'Hide on notebooks', "trx_addons" ),
							'descr': __( "Hide this item on notebooks", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on tablets
						{
							'name': 'hide_on_tablet',
							'title': __( 'Hide on tablets', "trx_addons" ),
							'descr': __( "Hide this item on tablets", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on mobile devices
						{
							'name': 'hide_on_mobile',
							'title': __( 'Hide on mobile devices', "trx_addons" ),
							'descr': __( "Hide this item on mobile devices", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on frontpage
						! hide_on_frontpage ? null : {
							'name': 'hide_on_frontpage',
							'title': __( 'Hide on Frontpage', "trx_addons" ),
							'descr': __( "Hide this item on the Frontpage", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on single posts
						! hide_on_frontpage ? null : {
							'name': 'hide_on_singular',
							'title': __( 'Hide on single posts and pages', "trx_addons" ),
							'descr': __( "Hide this item on single posts and pages", "trx_addons" ),
							'type': 'boolean'
						},
						// Hide on other pages
						! hide_on_frontpage ? null : {
							'name': 'hide_on_other',
							'title': __( 'Hide on other pages', "trx_addons" ),
							'descr': __( "Hide this item on other pages (posts archive, category or taxonomy posts, author's posts, etc.)", "trx_addons" ),
							'type': 'boolean'
						}
					],
					'common/hide',
					props
				);

	return el( wp.element.Fragment, { key: props.name + '-hide-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "Hide on devices", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}



// Return ID, Class, CSS params
//-------------------------------------------
function trx_addons_gutenberg_get_param_id() {

	"use strict";

	return trx_addons_apply_filters(
			'trx_addons_gb_map_get_params',
			{
				// ID, Class, CSS attributes
				'id': {
					'type': 'string',
					'default': ''
				},
				'class': {
					'type': 'string',
					'default': ''
				},
				'className': {
					'type': 'string',
					'default': ''
				},
				'css': {
					'type': 'string',
					'default': ''
				}
			},
			'common/id'
		);
}

function trx_addons_gutenberg_add_param_id(props, id_name) {

	"use strict";

	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;
	if (id_name === undefined) id_name = 'id';
	var params = trx_addons_apply_filters(
					'trx_addons_gb_map_add_params',
					[
						// Element ID
						{
							'name': id_name,
							'title': __( 'Element ID', "trx_addons" ),
							'descr': __( "ID for current element", "trx_addons" ),
							'type': 'text'
						},
						// Element CSS class
						{
							'name': 'class',
							'title': __( 'Element CSS class', "trx_addons" ),
							'descr': __( "CSS class for current element", "trx_addons" ),
							'type': 'text'
						},
						// CSS box
						{
							'name': 'css',
							'title': __( 'CSS box', "trx_addons" ),
							'descr': __( "Design Options", "trx_addons" ),
							'type': 'textarea'
						}
					],
					'common/id',
					props
				);

	return el( wp.element.Fragment, { key: props.name + '-id-params' },
				el( trx_addons_get_wp_editor().InspectorControls, {},
					el( wp.components.PanelBody, { title: __( "ID & Class", "trx_addons" ) },
						el( 'div', { className: 'components-panel__body-fieldset' },
							trx_addons_gutenberg_add_params( params, props )
						)
					)
				)
			);
}





//
//
//
// ADD PARAMETERS
// Parameters constructor
//-------------------------------------------
function trx_addons_gutenberg_block_params(args, props){

	"use strict";

	var blocks = window.wp.blocks;
	var el     = window.wp.element.createElement;
	var __     = window.wp.i18n.__;

	var html = [];

	// Title
	if ( args['title'] ) {
		html.push( el( 'div', { key: props.name + '-block-title', className: 'editor-block-params' },
							el( 'span', {},
								args['title']
							)
						)
		);
	}

	// General params
	if ( args['general_params'] ) {
		html.push( el( wp.element.Fragment, { key: props.name + '-general-params' },
						el( trx_addons_get_wp_editor().InspectorControls, {},
							el( wp.components.PanelBody, { title: __( "General", "trx_addons" ) },
								args['general_params']
							)
						)
					)
		);
	}

	// Additional params
	if ( args['additional_params'] ) {
		html = html.concat( args['additional_params'] );
	}

	// Block render
	if ( args['render'] ) {
		// wp.components.ServerSideRender is deprecated
		html.push( el( wp.serverSideRender, {
						key: props.name + '-server-side-render',
						block: props.name,
						attributes: props.attributes,
						className: 'sc_server_side_renderer'
						}
					)
		);
	}

	// Block "reload" button
	if ( args['render_button'] ) {
		html.push( el( wp.components.Button,
						{
							key: props.name + '-reload-button',
							className: 'button wp-block-reload trx_addons_gb_reload',// + (!args['parent'] ? ' hide' : ''),
							onClick: function( event ) {
								// "core/editor" is deprecated
								var block = wp.data.select("core/block-editor").getBlock(props.clientId);
								var block_type = blocks.getBlockType( props.name );
								// If block have inner blocks - update their attributes in the parent block
								if ( block && typeof block.innerBlocks == 'object' && typeof block_type.save != 'undefined' ) {	// && block.innerBlocks.length > 0
									block_type.save(block);
									props.setAttributes( block.attributes );
								}

								// Change attribute 'reload' to get new layout from server
								var upd_attr = {
									'reload': Math.floor( Math.random() * 100 )
								};
								props.setAttributes( upd_attr );

								// Reload hidden elements like sliders
								trx_addons_gutenberg_reload_hidden_elements( props );
							}
						},
						''	//__( "Reload", "trx_addons" )
					)
		);
	}

	// Block items
	if ( args['parent'] ) {
		html.push( el( wp.components.PanelBody,
						{
							initialOpen: ! args['render_button'],
							key: props.name + '-inner-blocks',
							title: __( "Inner blocks", "trx_addons" ),
							className: 'wp-inner-blocks trx_addons_gb_inner_blocks'	//remove 'wp-block-columns'
						},
						el( trx_addons_get_wp_editor().InnerBlocks,
							{
								allowedBlocks: args['allowedblocks']
													? args['allowedblocks']
													: [ 'core/paragraph' ]
							}
						)
					)
		);
	}

	return html;
}

// Add multiple parameters from array
//-------------------------------------------
function trx_addons_gutenberg_add_params( args, props ) {

	"use strict";

	var params = [];
	for ( var i = 0; i < args.length; i++ ) {
		if ( args[i] ) {
			params.push( trx_addons_gutenberg_add_param( args[i], props ) );
		}
	}
	return params;
}

// Add single parameter by type
//-------------------------------------------
function trx_addons_gutenberg_add_param( args, props ) {

	"use strict";

	var el = window.wp.element.createElement;
	var __ = window.wp.i18n.__;

	// Set variables
	var param_name     	= args['name'] ? args['name'] : '';
	var param_name_url 	= args['name_url'] ? args['name_url'] : '';
	var param_title    	= args['title'] ? args['title'] : '';
	var param_descr    	= args['descr'] ? args['descr'] : '';
	var param_options  	= args['options'] ? args['options'] : '';
	var param_value    	= param_name ? props.attributes[param_name] : '';
	var param_url_value	= param_name_url ? props.attributes[param_name_url] : '';
	var upd_attr       	= {};

	// Set onChange functions
	var param_change     = function(x) {
								upd_attr[param_name] = x;
								props.setAttributes( upd_attr );
								// Reload hidden elements like sliders
								trx_addons_gutenberg_reload_hidden_elements( props, param_name, x );
	};
	var param_change_img = function(x) {
								if ( typeof x.length != 'undefined' ) {
									var names = '', urls = '';
									for ( var i = 0; i < x.length; i++ ) {
										names += ( names ? '|' : '' ) + x[i].id;
										urls  += ( urls  ? '|' : '' ) + x[i].url;
									}
									upd_attr[param_name]     = names;
									upd_attr[param_name_url] = urls;
								} else {
									upd_attr[param_name]     = x.id;
									upd_attr[param_name_url] = x.url;
								}
								props.setAttributes( upd_attr );
								// Reload hidden elements like sliders
								trx_addons_gutenberg_reload_hidden_elements( props, param_name, x );
	};

	// Parameters dependency
	var dep_all = 0, dep_cnt = 0;
	if ( args['dependency'] ) {
		for (var i in args['dependency']) { 
			// Convert value to an array (if specified as string or number)
			if ( typeof args['dependency'][i] != 'object' ) {
				args['dependency'][i] = [ args['dependency'][i] ];
			}
			// Total dependencies count
			dep_all++;
			for (var t in args['dependency'][i]) {
				if ( props.attributes[i] === args['dependency'][i][t] 
						|| ( (''+args['dependency'][i][t]).charAt(0) == '^' && props.attributes[i] !== args['dependency'][i][t].substr(1) )
						|| ( args['dependency'][i][t] == 'not_empty' && props.attributes[i] !== '' )
				) {
					// Valid dependencies count
					dep_cnt++;
					break;
				}
			}
		}
	}
	// Return parameters options
	if ( dep_all == dep_cnt ) {
		if (args['type'] === 'text') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( wp.components.TextControl, {
//							label: param_descr,
							value: param_value,
							onChange: param_change
							}
						)
					);
		}
		if (args['type'] === 'textarea') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( wp.components.TextareaControl, {
//							label: param_descr,
							value: param_value,
							rows: args['rows'] ? args['rows'] : 6,
							onChange: param_change
							}
						)
					);
		}
		if (args['type'] === 'boolean') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el(	wp.components.ToggleControl, {
//							label: param_descr,
							checked: param_value,
							onChange: param_change
							}
						)
					);
		}
		if (args['type'] === 'radio') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el(	wp.components.RadioControl, {
//							label: param_descr,
							selected: param_value,
							onChange: param_change,
							options: param_options
							}
						)
					);
		}
		if (args['type'] === 'select') {
			if (args['multiple']) {
				param_value = param_value.split( ',' );
			}
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( wp.components.SelectControl, {
//							label: param_descr,
							value: param_value,
							multiple: args['multiple'] ? 1 : 0,
							size: args['multiple'] ? 9 : 1,
							onChange: function(x) {
								if (args['multiple']) {
									var y = '';
									for (var i = 0; i < x.length; i++) {
										y = y + (y ? ',' : '') + x[i];
									}
									upd_attr[param_name] = y;
								} else {
									upd_attr[param_name] = x;
								}
								props.setAttributes( upd_attr );
								// Reload hidden elements like sliders
								trx_addons_gutenberg_reload_hidden_elements( props, param_name, x );
							},
							options: param_options
							}
						)
					);
		}
		if (args['type'] === 'number') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( wp.components.RangeControl, {
//							label: param_descr,
							value: param_value,
							onChange: param_change,
							min: args['min'],
							max: args['max'],
							step: args['step']
							}
						)
					);
		}
		if (args['type'] === 'color') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper', style: { position: 'relative' } },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( wp.components.ColorPalette, {
								value: param_value,
								colors: TRX_ADDONS_STORAGE['gutenberg_sc_params']['theme_colors'],
								onChange: param_change
								}
							)
						);
		}
		if (args['type'] === 'image') {
			return el( 'div', { key: props.name + '-param-' + param_name, className: 'components-base-control-wrapper' },
						el( 'h3', { className: "components-base-control__title" }, param_title ),
						el( 'p', { className: "components-base-control__description" }, param_descr ),
						el( trx_addons_get_wp_editor().MediaUpload, {
								onSelect: param_change_img,
								type: 'image',
								multiple: args['multiple'] ? args['multiple'] : false,
								value: param_value,
								render: function( obj ) {
									var preview = [];
									var images = param_url_value.split( '|' );
									if ( images.length > 1 ) {
										for (var i = 0; i < images.length; i++) {
											preview.push( el( 'img', { key: i, src: images[i] } ) );
										}
									} else {
										preview = el( 'img', { src: param_url_value } );
									}
									return el( 'div', { className: 'image-selector' + (args['multiple'] ? ' image-selector-gallery' : '') },
												param_value
													? el( 'div', { className: 'image-selector-preview' }, preview )
													: null,
												el( wp.components.Button,
													{
														className: 'components-button button button-large button-one',
														onClick: function() {
																	if ( param_value ) {
																		upd_attr[param_name]     = '';
																		upd_attr[param_name_url] = '';
																		props.setAttributes( upd_attr );
																	} else {
																		obj.open();
																	}
																}
													},
													param_value
														? ( args['multiple']
															? __( 'Remove Images', "trx_addons" )
															: __( 'Remove Image', "trx_addons" )
															)
														: ( args['multiple']
															? __( 'Select Images', "trx_addons" )
															: __( 'Select Image', "trx_addons" )
															)
												)
											);
								}
							}
						)
					);
		}
	}
}

// Rewrite array with options for gutenberg
//-------------------------------------------
function trx_addons_gutenberg_get_lists(list, none) {

	"use strict";

	var __     = window.wp.i18n.__;

	var output = [];
	
	if (list != '') {
		jQuery.each(
			list, function(key, value) {
				// Skip empty key if a parameter 'none' is true
				if ( ! none && ( key == '' || key == '0' || key == 0 ) ) {
					return;
				}
				output.push(
					{
						//key: key,
						value: key,
						label: value
					}
				);
				// Prevent adding empty key if it is already in the list
				if ( key == '' || key == '0' || key == 0 ) {
					none = false;
				}
			}
		);
	}
	if ( none ) {
		output[output.length] = {
			//key: '0',
			value: '0', 
			label: __( '-', "trx_addons" )
		};
	}
	return output;
}

// Return iconed classes list
//-------------------------------------------
function trx_addons_gutenberg_get_option_icons_classes() {

	"use strict";

	var output = [];
	var icons  = TRX_ADDONS_STORAGE['gutenberg_sc_params']['icons_classes'];
	if (icons != '') {
		jQuery.each(
			icons, function(key, value) {
				output.push(
					{
						//key: value,
						value: value,
						label: value
					}
				);
			}
		);
	}
	return output;
}


// Get child block values of attributes
//-------------------------------------------
function trx_addons_gutenberg_get_child_attr(props, return_array) {

	"use strict";

	var items = [];
	if ( props.innerBlocks.length ) {
		for ( var i = 0; i < props.innerBlocks.length; i++ ) {
			if ( props.innerBlocks[i].name && props.innerBlocks[i].name.indexOf('core/') == -1 ) { 
				items.push( props.innerBlocks[i].attributes );
			}
		} 
	}
	return items.length
			? ( return_array ? items : JSON.stringify( items ) )
			: ( return_array ? []    : '' );
}


// Change a default value of an attribute after a timeout,
// because setSate() inside the edit is prohibited
//----------------------------------------------------------
function trx_addons_gutenberg_set_attributes_from_edit( props, atts ) {
	if ( ! trx_addons_is_empty( atts ) ) {
		setTimeout( function() {
			props.setAttributes( atts );
		}, 100 );
	}
}

// Re-init hidden elements after any block is reloaded
//--------------------------------------------------------
var trx_addons_gutenberg_block_reload_started = {};
function trx_addons_gutenberg_reload_hidden_elements( props, param_name, x ) {

	"use strict";

	if ( props ) {

		// Before the block loading: Add the class 'reload_mask' and increment a counter
		var $block = jQuery( '[data-block="' + props.clientId + '"]' ).addClass( 'reload_mask' );
		$block.find(' .sc_server_side_renderer' ).addClass('trx_addons_busy');
		trx_addons_gutenberg_block_reload_started[props.clientId] = typeof trx_addons_gutenberg_block_reload_started[props.clientId] == 'undefined'
																		? 1
																		: trx_addons_gutenberg_block_reload_started[props.clientId] + 1;
		// Catch when block is loaded with Mutation Observer
		var rez = false;
		if ( ! $block.data( 'trx-addons-mutation-observer-added' ) ) {
			$block.data( 'trx-addons-mutation-observer-added', true );
			// Create an observer instance to catch when block is loaded
			rez = trx_addons_create_observer( props.clientId, $block, function( mutationsList ) {
				if ( trx_addons_gutenberg_block_reload_started.hasOwnProperty( props.clientId )
					&& trx_addons_gutenberg_block_reload_started[props.clientId] > 0
					&& trx_addons_check_mutations( mutationsList, '.sc_server_side_renderer', 'add' )
				) {
					var $ssr = $block.find('.sc_server_side_renderer' );
					if ( $ssr.length && ! $ssr.hasClass('trx_addons_busy') ) {
						after_block_loaded();
					}
				}
			} );
		}

		// If MutationObserver is not supported - wait 8 sec and init hidden elements
		// Otherwise - wait 10 sec
		setTimeout( function() {
			after_block_loaded();
			}, ! rez ? 8000 : 10000
		);
	}

	// After the block is loaded: Remove the class 'reload_mask', decrement a counter and init hidden elements
	function after_block_loaded() {
		if ( trx_addons_gutenberg_block_reload_started.hasOwnProperty(props.clientId)
			&& trx_addons_gutenberg_block_reload_started[props.clientId] > 0
		) {
			trx_addons_gutenberg_block_reload_started[props.clientId] = Math.max(0, trx_addons_gutenberg_block_reload_started[props.clientId] - 1);
			trx_addons_remove_observer( props.clientId );
			$block.data( 'trx-addons-mutation-observer-added', false );
			$block.removeClass( 'reload_mask' );
			jQuery( document ).trigger( 'action.init_hidden_elements', [ $block ] );
			// Collapse inner blocks
			// Commented, because all inner sections are closed initially
			// $block.find('.trx_addons_gb_inner_blocks.is-opened').each( function() {
			// 	var $self = jQuery( this );
			// 	if ( $self.css('position') == 'absolute' ) {
			// 		$self.find( '> .components-panel__body-title > .components-panel__body-toggle' ).trigger( 'click' );
			// 	}
			// } );			
		}
	}
}


// Init blocks after the page is loaded
//-------------------------------------------
jQuery( window ).on( 'load', function() {

	"use strict";

	var __ = window.wp.i18n.__;

	var $body = jQuery( 'body' );

	var $editor_wrapper = jQuery( '#editor,#site-editor,#widgets-editor' ).eq(0);
	var editor_selector = {
		'post':    '.edit-post-visual-editor',
		'site':    '.edit-site-visual-editor',
		'widgets': '.edit-widgets-block-editor'
	};

	if ( $editor_wrapper.length ) {
		var editor_type = $editor_wrapper.attr( 'id' ) == 'widgets-editor'
							? 'widgets'
							: ( $editor_wrapper.attr( 'id' ) == 'site-editor'
								? 'site'
								: 'post'
								);
		var $skeleton_content = false;
		if ( typeof window.MutationObserver !== 'undefined' ) {
			// Create the observer to reinit visual editor after switch from code editor to visual editor
			trx_addons_create_observer( 'trx-check-visual-editor-wrapper', $editor_wrapper, function( mutationsList ) {
				trx_addons_gutenberg_editor_init();
			} );
		} else {
		 	trx_addons_gutenberg_editor_init();
		}
	}

	// Return Gutenberg editor object
	function trx_addons_gutenberg_editor_object() {
		var editor = {
			$editor: false,
			$frame: false,
			$styles_wrapper: false,
			$writing_flow: false
		};
		if ( ! $skeleton_content || ! $skeleton_content.length ) {
			$skeleton_content = $editor_wrapper.find( '.interface-interface-skeleton__content' ).eq(0);
		}
		if ( $skeleton_content.length ) {
			var $editor = $skeleton_content.find( '>' + editor_selector[editor_type] ).eq( 0 );
			if ( $editor.length ) {
				editor.$editor = $editor;
				if ( editor_type == 'site' ) {
					editor.$frame = $editor.find( 'iframe[name="editor-canvas"]' );
					if ( editor.$frame.length && editor.$frame.get(0).contentDocument ) {
						editor.$styles_wrapper = jQuery( editor.$frame.get(0).contentDocument.body );
						if ( editor.$styles_wrapper.hasClass( 'trx_addons_inited' ) ) {
							editor.$editor = editor.$frame = editor.$styles_wrapper = false;
						}
					} else {
						editor.$frame = false;
					}
				} else {
					if ( ! editor.$editor.hasClass( 'trx_addons_inited' ) ) {
						editor.$writing_flow = $editor.find( '.block-editor-writing-flow' );
						if ( ! editor.$writing_flow.length ) {
							editor.$writing_flow = false;
						}
						editor.$styles_wrapper = editor.$writing_flow && editor.$writing_flow.hasClass( 'editor-styles-wrapper' )
													? editor.$writing_flow
													: $editor.find( '.editor-styles-wrapper' );
						if ( ! editor.$styles_wrapper.length ) {
							editor.$styles_wrapper = false;
						}
					} else {
						editor.$editor = false;
					}
				}
			}
		}
		return editor;
	}

	// Init on page load
	function trx_addons_gutenberg_editor_init() {

		// Get Gutenberg editor object
		var editor = trx_addons_gutenberg_editor_object();
		if ( ! editor.$editor ) {
			return;
		}

		// Common actions
		//----------------------------------------------------------

		// Init hidden elements after each 3s until a first ajax request is finished
		if ( editor.$styles_wrapper ) {
			jQuery( document ).trigger( 'action.init_hidden_elements', [ editor.$styles_wrapper ] );

			var init_attempts = 5,
				init_hidden_timer = setInterval( function() {
					jQuery( document ).trigger( 'action.init_hidden_elements', [ editor.$styles_wrapper ] );
					if ( init_attempts-- <= 1 ) {
						clearInterval( init_hidden_timer );
						init_hidden_timer = null;
					}
				}, 3000 );

			// Stop init hidden elements after first ajax query
			jQuery( document ).on( 'ajaxComplete', function() {
				if ( init_hidden_timer ) {
					clearInterval( init_hidden_timer );
					init_hidden_timer = null;
				}
			} );
		}

		// Post Editor
		//----------------------------------------------------------
		if ( editor_type == 'post' && editor.$styles_wrapper ) {

			// Add a class with a post type to the styles_wrapper
			editor.$styles_wrapper.addClass( trx_addons_get_class_by_prefix( $body.attr( 'class' ), 'post-type-' ) );

			// Create the observer to assign 'Blog item' position to the parent block
			if ( $body.hasClass( 'post-type-cpt_layouts' ) ) {
				trx_addons_remove_observer( 'trx-blog-item-position' );
				trx_addons_create_observer( 'trx-blog-item-position', editor.$styles_wrapper, function( mutationsList ) {
					editor.$styles_wrapper.find('[data-type="trx-addons/layouts-blog-item"]').each( function() {
						var $item = jQuery( this );
						var item_position = $item.find( '[data-blog-item-position]' ).data( 'blog-item-position' );
						if ( item_position !== undefined && ! $item.hasClass( 'sc_layouts_blog_item_position_' + item_position ) ) {
							$item.attr( 'class', trx_addons_chg_class_by_prefix( $item.attr( 'class'), 'sc_layouts_blog_item_position_', 'sc_layouts_blog_item_position_' + item_position ) );
						}
					} );
				} );
			}

			// Add the class 'editor-page-attributes__template'
			// to the field's wrap contains a select with templates ( only for pages )
			trx_addons_remove_observer( 'trx-check-template-selector' );
			trx_addons_create_observer( 'trx-check-template-selector', $editor_wrapper, function( mutationsList ) {
				var $template_selector = $editor_wrapper
											.find( '.edit-post-sidebar .components-panel option[value^="blog"]' ).eq(0)
											.parent().parent();
				if ( $template_selector.length && ! $template_selector.hasClass( 'editor-page-attributes__template' ) ) {
					$template_selector.addClass( 'editor-page-attributes__template' );
					jQuery( document ).trigger( 'trx_addons_action_page_template_selector_appear', [$template_selector] );

				}
			} );
		}

		// Site Editor
		//----------------------------------------------------------
		if ( editor_type == 'site' && editor.$styles_wrapper ) {
			// Mark 'body.editor-styles-wrapper' as inited to catch when the body is changed to re-init
			editor.$styles_wrapper.addClass( 'trx_addons_inited' );
		}

		// Widgets Editor
		//----------------------------------------------------------
		if ( editor_type == 'widgets' && editor.$writing_flow ) {
			// Add a form 'New widgets area' to the Widgets Editor sidebar
			trx_addons_remove_observer( 'trx-add-form-with-custom-widget-areas' );
			trx_addons_create_observer( 'trx-add-form-with-custom-widget-areas', $editor_wrapper, function( mutationsList ) {
				var $widgets_sidebar = $editor_wrapper.find( '.edit-widgets-sidebar .components-panel' ).eq(0);
				if ( $widgets_sidebar.length ) {
					add_custom_widget_areas_form( $widgets_sidebar );
				}
			} );
		}

		// Add a form 'New widgets area' to the Widgets editor
		function add_custom_widget_areas_form( $widgets_sidebar ) {
			var $customize_link = $widgets_sidebar.find( 'a.components-button[href*="customize.php?autofocus"]' );
			var $form_wrap = $widgets_sidebar.find( '.trx_addons_widgets_form_wrap' );
			if ( $customize_link.length ) {
				if ( $form_wrap.length === 0 ) {
					var $wrap = $customize_link.parents( '.edit-widgets-widget-areas' );
					if ( $wrap.length ) {
						$wrap.append(
							'<div class="edit-widgets-widget-areas__top-container">'
								+ '<span class="block-editor-block-icon"></span>'
								+ '<div class="trx_addons_widgets_form_wrap">'
									+ '<p class="trx_addons_widgets_form_title">' + __( 'Add custom widgets area', "trx_addons" ) + '</p>'
									+ '<form class="trx_addons_widgets_form" method="post">'
										+ '<input name="trx_addons_widgets_area_nonce" value="' + TRX_ADDONS_STORAGE['ajax_nonce'] + '" type="hidden">'
										+ '<div class="trx_addons_widgets_area_name">'
											+ '<div class="trx_addons_widgets_area_label">' + __( 'Name (required):', "trx_addons" ) + '</div>'
											+ '<div class="trx_addons_widgets_area_field"><input name="trx_addons_widgets_area_name" value="" type="text"></div>'
										+ '</div>'
										+ '<div class="trx_addons_widgets_area_description">'
											+ '<div class="trx_addons_widgets_area_label">' + __( 'Description:', "trx_addons" ) + '</div>'
											+ '<div class="trx_addons_widgets_area_field"><input name="trx_addons_widgets_area_description" value="" type="text"></div>'
										+ '</div>'
										+ '<div class="trx_addons_widgets_area_submit">'
											+ '<div class="trx_addons_widgets_area_field">'
												+ '<input type="submit" value="' + __( 'Add', "trx_addons" ) + '" name="trx_addons_widgets_area_add" class="trx_addons_widgets_area_button trx_addons_widgets_area_add button-primary" title="' + __( 'To create new widgets area specify it name (required) and description (optional) and press this button', "trx_addons" ) + '">'
												+ '<input type="submit" value="' + __( 'Delete', "trx_addons" ) + '" name="trx_addons_widgets_area_delete" class="trx_addons_widgets_area_button trx_addons_widgets_area_delete button" title="' + __( 'To delete custom widgets area specify it name (required) and press this button', "trx_addons" ) + '">'
											+ '</div>'
										+ '</div>'
									+ '</form>'
								+ '</div>'
							+ '</div>'
						);
					}
				}
			} else {
				$form_wrap.parents( '.edit-widgets-widget-areas__top-container' ).remove();
			}
		}

		// Finish actions
		//----------------------------------------------------------

		// Allow to init other modules related to Gutenberg
		jQuery( document ).trigger( 'action.init_gutenberg', [ editor ] );

		// Mark the editor as inited
		editor.$editor.addClass( 'trx_addons_inited' );
	}
} );


//--------------------------------------------------------
// Modify core blocks
//-------------------------------------------
( function( blocks, i18n, element, components, hooks ) {

	"use strict";

	if ( ! TRX_ADDONS_STORAGE['modify_gutenberg_blocks'] ) {
		return;
	}

	var el = window.wp.element.createElement;
	var __ = window.wp.i18n.__;

	// Add new attribute to the blocks
	function TrxAddonsCoreBlockAddAttribute( settings, name ) {
		if ( name == 'core/spacer' || name == 'core/separator' ) {
			settings.attributes['alter_height'] = {
				type: 'string',
				default: 'none'
			};
		}
		return settings;
	}
	
	hooks.addFilter( 'blocks.registerBlockType', 'trx-addons/core/block', TrxAddonsCoreBlockAddAttribute );

	// Edit block: Add classes to the block wrapper
	var TrxAddonsCoreBlockList = wp.compose.createHigherOrderComponent( function( BlockListBlock ) {
		return function( props ) {
			var change = false;
			if ( props.name == 'core/spacer' || props.name == 'core/separator' ) {
				if ( props.attributes.alter_height && props.attributes.alter_height != 'none' ) {
					change = true;
					var newProps = lodash.assign(
						{
							//key: 'trx_addons-' + props.name
						},
						props,
						{
							className: ( props.className ? props.className + ' ' : '' )
											+ 'sc_height_' + props.attributes.alter_height
						}
					);
				}
			}
			return el( BlockListBlock, change ? newProps : props );

		};
	}, 'TrxAddonsCoreBlockList' );

	hooks.addFilter( 'editor.BlockListBlock', 'trx-addons/core/block', TrxAddonsCoreBlockList );


	// Edit block: Add fields to the Inspector panel
	var TrxAddonsCoreBlockEdit = wp.compose.createHigherOrderComponent( function( BlockEdit ) {
		return function( props ) {
			if ( props.name == 'core/spacer' || props.name == 'core/separator' ) {
				return el( wp.element.Fragment, {},
							el( BlockEdit, props ),
							el( trx_addons_get_wp_editor().InspectorControls, {},
								el( wp.components.PanelBody, { initialOpen: true },
									el( wp.components.PanelRow, {},
										el( components.SelectControl,
											{
												label: __( 'Alter height', "trx_addons" ),
												className: props.name.replace('core/', '') + '_alter_height',
												value: props.attributes.alter_height ? props.attributes.alter_height : 'none',
												options: trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_spacer_heights'] ),
												onChange: function( value ) {
													props.setAttributes( { alter_height: value } );
												}
											}
										)
									)
								)
							)
						);
			} else {
				// return el( wp.element.Fragment, {}, el( BlockEdit, props ) );
				return el( BlockEdit, props );
			}
		};
	}, 'TrxAddonsCoreBlockEdit' );

	hooks.addFilter( 'editor.BlockEdit', 'trx-addons/core/block', TrxAddonsCoreBlockEdit );

	// Save block
	var TrxAddonsCoreBlockSave = function( element, blockType, attributes ) {
		if ( blockType.name == 'core/spacer' || blockType.name == 'core/separator' ) {
			if ( ! trx_addons_is_off( attributes.alter_height ) ) {
				return lodash.assign(
							{},
							element,
							{ props: lodash.assign(
											{},
											element.props,
											{
												className: element.props.className + ' sc_height_' + attributes.alter_height
											}
										)
							}
						);
			}
		}
		return element;
	};

	hooks.addFilter( 'blocks.getSaveElement', 'trx-addons/core/block', TrxAddonsCoreBlockSave );

} )( window.wp.blocks, window.wp.i18n, window.wp.element, window.wp.components, window.wp.hooks );
