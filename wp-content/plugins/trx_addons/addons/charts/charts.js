/**
 * Shortcode Charts
 *
 * @package ThemeREX Addons
 * @since v2.8.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	var $document = jQuery( document );

	$document.on( 'action.init_trx_addons', function() {

		var $charts_items, charts = {};

		// Update links and values after the new post added
		$document.on( 'action.init_hidden_elements', update_jquery_links );
		$document.on( 'action.got_ajax_response', update_jquery_links );
		var first_run = true;
		function update_jquery_links(e) {
			if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
				first_run = false;
				return; 
			}
			$charts_items  = jQuery( '.sc_charts_canvas' );
			if ( $charts_items.length > 0 ) {
				trx_addons_intersection_observer_add( $charts_items, function( item, enter ) {
					if ( enter ) {
						trx_addons_intersection_observer_remove( item );
						trx_addons_sc_charts_init();
					}
				} );
			}
		}
		update_jquery_links();

		$document
//			.on( 'action.resize_trx_addons',    trx_addons_sc_charts_resize )
			.on( 'action.init_hidden_elements', trx_addons_sc_charts_init )
			.on( 'action.scroll_trx_addons',    trx_addons_sc_charts_init );
		
		// Charts init
		function trx_addons_sc_charts_init( e, container ) {
			if ( $charts_items.length === 0 ) return;
		
			$charts_items.each( function( idx ) {
				var $chartsItem = $charts_items.eq( idx );
				// If item now invisible or inited
				if ( $chartsItem.hasClass('inited') || ! $chartsItem.hasClass('trx_addons_in_viewport') || $chartsItem.parents('div:hidden,article:hidden').length > 0 ) {
					return;
				}

				$chartsItem.addClass('inited');

				var id = $chartsItem.attr('id');

				var item_data = $chartsItem.data('chart-data');

				if ( item_data ) {
					var data = {
							labels: [],
							datasets: []
						},
						ds,
						ds_idx,
						ds_total = 0;
					for ( ds = 1; ds <= TRX_ADDONS_STORAGE['charts_datasets_total']; ds++ ) {
						if ( ( ds == 1 || item_data['dataset'+ds+'_enable'] ) && typeof item_data['dataset'+ds] != 'undefined' ) {
							ds_total++;
						}
					}
					for ( ds = 1; ds <= TRX_ADDONS_STORAGE['charts_datasets_total']; ds++ ) {
						if ( ( ds == 1 || item_data['dataset'+ds+'_enable'] ) && typeof item_data['dataset'+ds] != 'undefined' ) {
							data.datasets.push( {
								// Native properties
								label: item_data['dataset'+ds+'_title'],
								backgroundColor: ['line', 'radar'].indexOf( item_data['type'] ) != -1 ? item_data['dataset'+ds+'_bg_color'] : [],
								borderColor: ['line', 'radar'].indexOf( item_data['type'] ) != -1 ? item_data['dataset'+ds+'_border_color'] : [],
								borderWidth: item_data['dataset'+ds+'_border_width'],
								borderJoinStyle: item_data['dataset'+ds+'_border_join'],
								pointStyle: item_data['dataset'+ds+'_point_style'],
								data: [],
								// Custom properties for legend
								backgroundColorGeneral: item_data['dataset'+ds+'_bg_color'],
								borderColorGeneral: item_data['dataset'+ds+'_border_color']
							} );
							ds_idx = data.datasets.length - 1;
							if ( ['pie', 'polarArea'].indexOf( item_data['type'] ) != -1 ) {
								data.datasets[ds_idx].cutout      = item_data['cutout'] + '%';
								data.datasets[ds_idx].hoverOffset = item_data['hover_offset'];
							} else if ( ['line', 'radar'].indexOf( item_data['type'] ) != -1 ) {
								data.datasets[ds_idx].pointRadius = item_data['dataset'+ds+'_point_size'];
								data.datasets[ds_idx].pointBackgroundColor = [];
								data.datasets[ds_idx].pointBorderColor     = [];
								data.datasets[ds_idx].fill = item_data['dataset'+ds+'_fill'] > 0;
								data.datasets[ds_idx].tension = item_data['dataset'+ds+'_tension']		// Bezier curves coefficient 0.0 - 1.0
																	? item_data['dataset'+ds+'_tension']
																	: 0;
							}
							for ( var i in item_data['dataset'+ds] ) {
								if ( data.labels.length <= i ) {
									data.labels.push( item_data['dataset'+ds][i]['title'] );
								} else if ( item_data['dataset'+ds][i]['title'] && data.labels[i].indexOf( item_data['dataset'+ds][i]['title'] ) == -1 ) {
									data.labels[i] += ( data.labels[i] ? ' / ' : '' ) + item_data['dataset'+ds][i]['title'];
								}
								data.datasets[ds_idx].data.push( item_data['dataset'+ds][i]['value'] );
								if ( ['line', 'radar'].indexOf( item_data['type'] ) != -1 ) {
									data.datasets[ds_idx].pointBackgroundColor.push( item_data['dataset'+ds][i]['bg_color']
																			? item_data['dataset'+ds][i]['bg_color']
																			: item_data['dataset'+ds+'_bg_color'] );
									data.datasets[ds_idx].pointBorderColor.push( item_data['dataset'+ds][i]['border_color']
																			? item_data['dataset'+ds][i]['border_color']
																			: item_data['dataset'+ds+'_border_color'] );
								} else {
									data.datasets[ds_idx].backgroundColor.push( item_data['dataset'+ds][i]['bg_color']
																			? item_data['dataset'+ds][i]['bg_color']
																			: item_data['dataset'+ds+'_bg_color'] );
									data.datasets[ds_idx].borderColor.push( item_data['dataset'+ds][i]['border_color']
																			? item_data['dataset'+ds][i]['border_color']
																			: item_data['dataset'+ds+'_border_color'] );
								}
							}
						}
					}

					// Chart configuration
					var config = {
							type: item_data['type'],
							data: data,
							options: {
								plugins: {
									/*
									title: {
										display: true,
										text: 'Custom Chart Title',
										align: 'center',
										padding: {
											top: 10,
											bottom: 10
										}
									},
									subtitle: {
										display: true,
										text: 'Chart Subtitle',
										align: 'center',
										padding: {
											top: 0,
											bottom: item_data['legend'] != 'none' ? 0 : 30
										}
									},
									*/
									legend: {
										display: item_data['legend'] != 'none',
										position: item_data['legend'],
										title: {
											padding: 10
										},
										labels: {
											color: trx_addons_apply_filters( 'trx_addons_filter_charts_labels_color', '#000' ),
											boxWidth: 20,
											usePointStyle: true
										}
									},
									tooltip: {
										enabled: true,
										boxPadding: 6,
										position: 'average',	// nearest | average
										usePointStyle: true
									}
								}
							}		
					};

					// Start a value axis (Y or R) from zero
					if ( ['line'].indexOf( item_data['type'] ) != -1 ) {
						config.options.scales = {
									y: {
										beginAtZero: item_data['from_zero'] > 0
									}
								};
					} else if ( ['radar'].indexOf( item_data['type'] ) != -1 ) {
						config.options.scales = {
									r: {
										beginAtZero: item_data['from_zero'] > 0
									}
								};
					}

					// Replace labels generator for legend to limit border width

					// If a single dataset is present or chart type is 'pie' or 'polarArea' - 
					// dataset items are shown in the legend
					if ( ds_total == 1 || ['pie', 'polarArea'].indexOf( item_data['type'] ) != -1 ) {
						config.options.plugins.legend.labels.generateLabels = function( chart ) {
							var data = chart.data;
							var {
									labels: {
										pointStyle
									}
								} = chart.legend.options;
							if ( data.labels.length && data.datasets.length ) {
								var pointStyle = data.datasets[0].pointStyle;	//chart.legend.options.labels.pointStyle;
								return data.labels.map( function( label, i ) {
									var meta = chart.getDatasetMeta( 0 );
									var style = meta.controller.getStyle( i );
									return {
										text: label,
										fillStyle: style.backgroundColor,
										strokeStyle: style.borderColor,
										lineWidth: ['cross','crossRot','dash','line'].indexOf( pointStyle ) != -1
														? Math.max( 1, style.borderWidth )
														: 2,
										pointStyle: pointStyle,
										hidden: ! chart.getDataVisibility( i ),
										index: i
									};
								} );
							}
							return [];
						};

					// If multiple datasets are present and chart type is not equal to 'pie' or 'polarArea' -
					// dataset's titles are shown in the legend
					} else {
						Chart.overrides.pie.plugins.legend.onClick =
						Chart.overrides.polarArea.plugins.legend.onClick = function( e, legendItem, legend ) {
							var index = legendItem.datasetIndex;
							var ci = legend.chart;
							if ( ci.isDatasetVisible( index ) ) {
								ci.hide( index );
								legendItem.hidden = true;
							} else {
								ci.show( index );
								legendItem.hidden = false;
							}
						};
//						Chart.overrides.pie.plugins.legend.labels.generateLabels =
//						Chart.overrides.polarArea.plugins.legend.labels.generateLabels =
						config.options.plugins.legend.labels.generateLabels = function(chart) {
							var datasets = chart.data.datasets;
							var {
									labels: {
										usePointStyle,
										pointStyle,
										textAlign,
										color
									}
								} = chart.legend.options;
							return chart._getSortedDatasetMetas().map( function( meta ) {
								var style = meta.controller.getStyle( usePointStyle ? 0 : undefined );
								var borderWidth = datasets[meta.index].borderWidth;	//Chart.helpers.toPadding( style.borderWidth );
								var borderColor = typeof datasets[meta.index].borderColor == 'object'
													? datasets[meta.index].borderColorGeneral || style.borderColor
													: datasets[meta.index].borderColor;
								var backgroundColor = typeof datasets[meta.index].backgroundColor == 'object'
													? datasets[meta.index].backgroundColorGeneral || style.backgroundColor
													: datasets[meta.index].backgroundColor;
								var pointStyle = datasets[meta.index].pointStyle;
								return {
									text: datasets[meta.index].label,
									fillStyle: backgroundColor,
									fontColor: color,
									hidden: ! meta.visible,
									lineCap: style.borderCapStyle,
									lineDash: style.borderDash,
									lineDashOffset: style.borderDashOffset,
									lineJoin: style.borderJoinStyle,
									lineWidth: ['cross','crossRot','dash','line'].indexOf( pointStyle ) != -1
														? Math.max( 1, borderWidth || style.borderWidth )	// style.borderWidth
														: 2, 			// Default way: (borderWidth.width + borderWidth.height) / 4
									strokeStyle: borderColor || style.borderColor,
									pointStyle: pointStyle || style.pointStyle,
									rotation: style.rotation,
									textAlign: textAlign || style.textAlign,
									borderRadius: 0, // TODO: v4, default to style.borderRadius
									// Below is extra data used for toggling the datasets
									datasetIndex: meta.index
								};
							}, this );
						};
					}

					// Create a chart object
					charts[id] = new Chart( $chartsItem.get(0), trx_addons_apply_filters( 'trx_addons_filter_charts_config', config, item_data ) );
				}
			});
		}
	
/*
		// Resize Charts
		function trx_addons_sc_charts_resize() {
			if ( $charts_items.length === 0 ) return;
		}
*/
	} );

})();