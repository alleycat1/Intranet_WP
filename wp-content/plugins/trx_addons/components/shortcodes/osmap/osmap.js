/**
 * Shortcode OpenStreet map
 *
 * @package ThemeREX Addons
 * @since v1.6.63
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	var osmap = {
		'inited': false,
		'init_attempts': 0,
		'count': 0,
		'geocoder': null,
		'tiler': TRX_ADDONS_STORAGE['osmap_tiler'],
		'maps': []
	};



	var timer = null, osmap_ready = false;

	jQuery(document).ready(function() {
		if (typeof L !== 'undefined' && typeof L.map !== 'undefined') {
			osmap_ready = true;
		}
	});

	jQuery(document).on( 'action.init_hidden_elements', function(e, container) {
		if (container === undefined) container = jQuery('body');
		var sc_osmap = container.find('.sc_osmap:not(.inited)');
		if (sc_osmap.length > 0) {
			if (timer !== null) clearTimeout(timer);
			// Init OpenStreet map after all other elements (i.e. slider)
			timer = setTimeout(function() {
					trx_addons_sc_osmap_init(e, container);
					}, sc_osmap.parents('.elementor-element-editable,.gutenberg__editor').length > 0 ? 500 : 0);
		}
	});

	function trx_addons_sc_osmap_init(e, container) {

		if (!osmap_ready) {
			if (timer !== null) clearTimeout(timer);
			osmap.init_attempts++;
			if ( osmap.init_attempts < 100 ) {
				timer = setTimeout(function() {
					trx_addons_sc_osmap_init(e, container);
				}, 100);
			}
			return;
		}

		if (container === undefined) container = jQuery('body');

		var sc_osmap = container.find('.sc_osmap:not(.inited)');
		if (sc_osmap.length > 0) {
			sc_osmap.each(function () {
				if (jQuery(this).parents('div:hidden,article:hidden').length > 0) return;
				var map 		= jQuery(this).addClass('inited'),
					map_id		= map.attr('id'),
					map_zoom	= map.data('zoom'),
					map_style	= map.data('style'),
					map_tiler   = TRX_ADDONS_STORAGE['osmap_tiler_styles'][map_style],
					map_center  = map.data('center'),
					map_editable= map.data('editable')=='1',
					map_cluster_icon = map.data('cluster-icon'),
					map_markers = [];
				map.find('.sc_osmap_marker').each(function() {
					var marker = jQuery(this),
						address = marker.data('address');
					if ( !address ) return;
					var latlng = address.split(',').map(parseFloat);
					map_markers.push({
						icon:			marker.data('icon'),
						icon_retina:	marker.data('icon_retina'),
						icon_shadow:	marker.data('icon_shadow'),
						icon_width:		marker.data('icon_width'),
						icon_height:	marker.data('icon_height'),
						address:		latlng.length == 2 && !isNaN(latlng[0]) && !isNaN(latlng[1]) ? '' : address,
						latlng:			latlng.length != 2 || isNaN(latlng[0]) || isNaN(latlng[1]) ? '' : latlng,
						description:	marker.data('description'),
						title:			marker.data('title')
					});
				});
				trx_addons_sc_osmap_create( map, {
					style: map_style,
					tiler: map_tiler,
					zoom: map_zoom,
					center: map_center,
					editable: map_editable,
					cluster_icon: map_cluster_icon,
					markers: map_markers
					}
				);
			});
		}
	}

	function trx_addons_sc_osmap_create(map, coords) {
//		try {
			var id = map.attr('id');
			osmap.count++;
			// Change id if already exists on this page
			if (typeof osmap.maps[id] !== 'undefined') {
				id += '_copy' + osmap.count;
				map.attr('id', id);
			}
			var center = [];
			if (coords.center) {
				center = (''+coords.center).split(',').map(parseFloat);
			}
			var zoom = coords.zoom
							? parseInt(coords.zoom, 10)
							: ( coords.markers.length == 1 && center.length === 0 ? 16 : 0 );
			osmap.maps[id] = {
				style: coords.style,
				tiler: coords.tiler,
				markers_inited: 0,
				markers: coords.markers,
				geocoder_request: false,
				geocoder_control: false,
				clusterer: null,
				clusterIcon: coords.cluster_icon,
				editable: coords.editable,
				fit_to_bounds: false,
				bounds: [ [-999, -999], [-999, -999] ],
				opt: {
					minZoom: 1,
					maxZoom: coords.tiler['maxzoom'] ? coords.tiler['maxzoom'] : 18,
					center: center,
					zoom: zoom
				}
			};
			if ( center.length > 0 ) {
				osmap.maps[id].opt['center'] = center;
			}
			if ( zoom > 0 ) {
				osmap.maps[id].opt['zoom'] = zoom;
			}
			trx_addons_sc_osmap_build(id);
//		} catch (e) {
//			console.log(TRX_ADDONS_STORAGE['msg_sc_osmap_not_avail']);
//		};
	}

	function trx_addons_sc_osmap_refresh() {
		for (id in osmap.maps) {
			// Remove objects
			if (osmap.maps[id].clusterer !== null) {
				osmap.maps[id].clusterer.removeLayers();
			}
			trx_addons_sc_osmap_build(id);
		}
	}

	// Add resize listener
	jQuery(document).on('action.resize_trx_addons', function() {
		for (var id in osmap.maps) {
			if (   osmap.maps[id].map
				&& jQuery('#'+id).parent().hasClass('ready')
				&& osmap.maps[id].window_width != jQuery( window ).width()
			) {
				if (osmap.maps[id].markers_inited == osmap.maps[id].markers.length) {		// && osmap.maps[id].fit_to_bounds
					if (osmap.maps[id].fit_to_bounds) {
						osmap.maps[id].map.fitBounds(trx_addons_sc_osmap_get_bounds(id));
					} else {
						osmap.maps[id].map.setView(
							osmap.maps[id].opt['center'].length > 0
								? osmap.maps[id].opt['center']
								: osmap.maps[id].markers[0].latlng,
							osmap.maps[id].opt['zoom'] > 0
								? osmap.maps[id].opt['zoom']
								: 16
						);
					}
				}
			}
		}
	});

	function trx_addons_sc_osmap_build(id) {
		// Save window width
		osmap.maps[id].window_width = jQuery( window ).width();

		// Create map
		osmap.maps[id].map = osmap.maps[id].opt['center'].length > 0 && osmap.maps[id].opt['zoom'] > 0
								? L.map(id, osmap.maps[id].opt)
								: L.map(id, { minZoom: osmap.maps[id].opt.minZoom, maxZoom: osmap.maps[id].opt.maxZoom });

		// Add tile layer
		if ( osmap.tiler == 'vector' ) {

			// Add vector tile layer
			L.mapboxGL({
				maxZoom: osmap.maps[id].opt['maxZoom'] ? osmap.maps[id].opt['maxZoom'] : 18,
				attribution: TRX_ADDONS_STORAGE['osmap_attribution'],
				accessToken: osmap.maps[id].tiler['token'] ? osmap.maps[id].tiler['token'] : 'not-needed-2',
				style: osmap.maps[id].tiler['url'].replace( '{style}', osmap.maps[id].style )
			}).addTo(osmap.maps[id].map);

		} else {

			// Add raster tile layer
			L.tileLayer(
				osmap.maps[id].tiler['url'].replace( '{style}', osmap.maps[id].style ),
				{
					maxZoom: osmap.maps[id].opt['maxZoom'] ? osmap.maps[id].opt['maxZoom'] : 18,
					attribution: TRX_ADDONS_STORAGE['osmap_attribution'],
					id: 'osmap.tiler'
				}
			).addTo(osmap.maps[id].map);
		}

		// Add GeoCoder
		if ( ! osmap.geocoder ) {
			osmap.geocoder = L.Control.Geocoder.nominatim();
		}
		osmap.maps[id].geocoder_control = L.Control.geocoder( { geocoder: osmap.geocoder } ).addTo(osmap.maps[id].map);

		// Create clusterer
		if (osmap.maps[id].markers.length > 1 && !osmap.maps[id].editable && typeof L.markerClusterGroup != 'undefined') {
			var clusterInit = osmap.maps[id].clusterIcon
								? {
										iconCreateFunction: function(cluster) {
											return L.divIcon( {
												html: '<div style="background-image: url('+osmap.maps[id].clusterIcon+')"><span>' + cluster.getChildCount() + '</span></div>',
												className: 'sc_osmap_cluster',
												iconSize: new L.Point(48, 48)
											} );
										}
									}
								: {};
			osmap.maps[id].clusterer = L.markerClusterGroup( clusterInit );
		}

		// Prepare maps bounds
		osmap.maps[id].fit_to_bounds = osmap.maps[id].opt['zoom'] < 1 && ( osmap.maps[id].opt['center'].length > 0 || osmap.maps[id].markers.length > 1);
		if (osmap.maps[id].opt['center'].length > 0) {
			trx_addons_sc_osmap_add_bounds(id, osmap.maps[id].opt['center']);
		}

		// Add markers
		for (var i=0; i < osmap.maps[id].markers.length; i++) {
			osmap.maps[id].markers[i].inited = false;
		}
		trx_addons_sc_osmap_add_markers(id);
	}

	function trx_addons_sc_osmap_add_markers(id) {
		
		for (var i=0; i < osmap.maps[id].markers.length; i++) {
			
			if (osmap.maps[id].markers[i].inited) {
				continue;
			}

			var geocache = false,
				address = osmap.maps[id].markers[i].address,
				tm = (new Date).getTime();

			// First try get latlng from cache with previous geocoding data
			if ( ! osmap.maps[id].markers[i].latlng ) {
				geocache = trx_addons_get_storage('trx_addons_geocache_osmap');
				if ( geocache && geocache.charAt(0) == '{' ) {
					geocache = JSON.parse(geocache);
				} else {
					geocache = {};
				}
				if ( typeof geocache[address] == 'object' ) {
					if ( geocache[address].expired < tm ) {
						osmap.maps[id].markers[i].latlng = geocache[address].latlng;
					} else {
						delete geocache[address];
					}
				}
			}

			// Start geocoding (get lat,lng from the address)
			if ( ! osmap.maps[id].markers[i].latlng ) {
				
				if ( osmap.maps[id].geocoder_request !== false ) continue;

				if ( ! address ) {
					osmap.maps[id].markers[i].inited = true;
					continue;
				}

				osmap.maps[id].geocoder_request = i;

				osmap.geocoder.geocode( osmap.maps[id].markers[i].address, function(results) {
					var idx = osmap.maps[id].geocoder_request;
					if ( results[0] && results[0].center ) {
						try {
							osmap.maps[id].markers[idx].latlng = [ results[0].center.lat, results[0].center.lng ];
							// Put geocoding result to the cache
							geocache[ osmap.maps[id].markers[idx].address ] = {
								latlng: osmap.maps[id].markers[idx].latlng,
								expired: (new Date()).getTime() + 24 * 60 * 60
							};
							trx_addons_set_storage('trx_addons_geocache_osmap', JSON.stringify(geocache));
							// Resume adding markers
							setTimeout(function() { 
								trx_addons_sc_osmap_add_markers(id);
								}, 1);
						} catch(e) {
							// Do nothing
						}
					} else {
						console.log(TRX_ADDONS_STORAGE['msg_sc_osmap_geocoder_error'] + ': "' + osmap.maps[id].markers[idx].address + '"');
					}
					// Release Geocoder
					osmap.maps[id].geocoder_request = false;
				});
			
			// Put marker to the map (if lat,lng are known)
			} else {

				// Prepare marker object
				var markerInit = {
						draggable: osmap.maps[id].editable,
						title: osmap.maps[id].markers[i].title,
						alt: osmap.maps[id].markers[i].title
					};
				if (osmap.maps[id].markers[i].icon) {
					if (osmap.maps[id].markers[i].icon_width === 0) osmap.maps[id].markers[i].icon_width = 32;
					if (osmap.maps[id].markers[i].icon_height === 0) osmap.maps[id].markers[i].icon_height = 32;
					var iconInit = {
						iconUrl: osmap.maps[id].markers[i].icon,
						iconSize: [osmap.maps[id].markers[i].icon_width, osmap.maps[id].markers[i].icon_height],
						iconAnchor: [osmap.maps[id].markers[i].icon_width/2, osmap.maps[id].markers[i].icon_height],
						popupAnchor: [0, -osmap.maps[id].markers[i].icon_height-3]
					};
					if (osmap.maps[id].markers[i].icon_shadow) {
						iconInit.shadowUrl = osmap.maps[id].markers[i].icon_shadow;
						iconInit.shadowSize = [osmap.maps[id].markers[i].icon_width, osmap.maps[id].markers[i].icon_height];
						iconInit.shadowAnchor = [osmap.maps[id].markers[i].icon_width/2, osmap.maps[id].markers[i].icon_height];
					}
					markerInit.icon = L.icon(iconInit);
				}
				osmap.maps[id].markers[i].marker = L.marker( L.latLng(osmap.maps[id].markers[i].latlng[0], osmap.maps[id].markers[i].latlng[1]), markerInit);
				if ( osmap.maps[id].clusterer === null ) {
					osmap.maps[id].markers[i].marker.addTo(osmap.maps[id].map);
				}
				if (osmap.maps[id].markers[i].description) {
					osmap.maps[id].markers[i].marker.bindPopup(osmap.maps[id].markers[i].description);
				}
				osmap.maps[id].markers[i].inited = true;
				osmap.maps[id].markers_inited++;
				trx_addons_sc_osmap_add_bounds(id, osmap.maps[id].markers[i].latlng);
			}
		}

		// If all markers inited
		if (osmap.maps[id].markers.length > 0 && osmap.maps[id].markers_inited == osmap.maps[id].markers.length) {
			
			// Make Cluster
			if (osmap.maps[id].markers_inited > 1 && osmap.maps[id].clusterer !== null ) {
				for (i = 0; i < osmap.maps[id].markers.length; i++) {
					osmap.maps[id].clusterer.addLayer(osmap.maps[id].markers[i].marker);
				}
				osmap.maps[id].map.addLayer(osmap.maps[id].clusterer);
			}
			
			// Fit Bounds
			if (osmap.maps[id].fit_to_bounds) {
				osmap.maps[id].map.fitBounds(trx_addons_sc_osmap_get_bounds(id));
			} else {
				osmap.maps[id].map.setView( osmap.maps[id].opt['center'].length > 0 
												? osmap.maps[id].opt['center'] 
												: osmap.maps[id].markers[0].latlng,
											osmap.maps[id].opt['zoom'] > 0
												? osmap.maps[id].opt['zoom']
												: 13
											);
			}
			
			// Display map
			if ( ! jQuery('#'+id).parent().hasClass('ready') ) {
				setTimeout(function() {
					jQuery('#'+id).parent().addClass('ready');
				}, 100);
			}
		}
	}

	function trx_addons_sc_osmap_add_bounds(id, latlng) {
		if (osmap.maps[id].bounds[0][0] == -999 || osmap.maps[id].bounds[0][0] > latlng[0]) osmap.maps[id].bounds[0][0] = latlng[0];
		if (osmap.maps[id].bounds[0][1] == -999 || osmap.maps[id].bounds[0][1] > latlng[1]) osmap.maps[id].bounds[0][1] = latlng[1];
		if (osmap.maps[id].bounds[1][0] == -999 || osmap.maps[id].bounds[1][0] < latlng[0]) osmap.maps[id].bounds[1][0] = latlng[0];
		if (osmap.maps[id].bounds[1][1] == -999 || osmap.maps[id].bounds[1][1] < latlng[1]) osmap.maps[id].bounds[1][1] = latlng[1];
	}

	function trx_addons_sc_osmap_get_bounds(id) {
		return osmap.maps[id].bounds;
	}

})();