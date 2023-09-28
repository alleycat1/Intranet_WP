/**
 * Shortcode Google map
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	var googlemap = {
		'inited': false,
		'init_attempts': 0,
		'count': 0,
		'geocoder': null,
		'maps': []
	};
	var timer = null;
	
	jQuery(document).on( 'action.init_hidden_elements', function(e, container) {
		if (container === undefined) container = jQuery('body');
		var gmap = container.find('.sc_googlemap:not(.inited)');
		if (gmap.length > 0) {
			if (timer !== null) clearTimeout(timer);
			// Init Google map after all other elements (i.e. slider)
			timer = setTimeout(function() {
					trx_addons_sc_googlemap_init(e, container);
					}, gmap.parents('.elementor-element-editable,.gutenberg__editor').length > 0 ? 500 : 0);
		}
	});
	
	function trx_addons_sc_googlemap_init(e, container) {

		if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
			if (timer !== null) clearTimeout(timer);
			googlemap.init_attempts++;
			if ( googlemap.init_attempts < 100 ) {
				timer = setTimeout(function() {
					trx_addons_sc_googlemap_init(e, container);
				}, 100);
			}
			return;
		} else if ( typeof window.trx_addons_sc_googlemap_overlay == 'undefined' ) {
			// Custom HTML marker - use map overlay
			window.trx_addons_sc_googlemap_overlay = function(latlng, html, map) {
				// Now initialize all properties.
				this.latlng_ = latlng;
				this.html_ = html;
				this.map_ = map;

				// Define a property to hold the layer's div. We'll
				// actually create this div upon receipt of the onAdd()
				// method so we'll leave it null for now.
				this.div_ = null;

				// Explicitly call setMap on this overlay
				this.setMap(map);
			};

			trx_addons_sc_googlemap_overlay.prototype = new google.maps.OverlayView();

			// onAdd is called when the map's panes are ready and the overlay has been added to the map.
			trx_addons_sc_googlemap_overlay.prototype.onAdd = function() {
				var div = document.createElement('div');
				div.innerHTML = this.html_;

				this.div_ = div.childNodes[0];

				// Add the element to the "overlayImage" pane.
				var panes = this.getPanes();
				panes.markerLayer.appendChild(this.div_);
			};

			trx_addons_sc_googlemap_overlay.prototype.draw = function() {

				// We use the south-west and north-east
				// coordinates of the overlay to peg it to the correct position and size.
				// To do this, we need to retrieve the projection from the overlay.
				var overlayProjection = this.getProjection();

				// Retrieve the south-west and north-east coordinates of this overlay
				// in LatLngs and convert them to pixel coordinates.
				// We'll use these coordinates to resize the div.
				var coords = overlayProjection.fromLatLngToDivPixel(this.latlng_);

				// Resize the image's div to fit the indicated dimensions.
				var div = this.div_;
				div.style.left = ( coords.x - div.clientWidth / 2 ) + 'px';
				div.style.top  = ( coords.y - div.clientHeight / 2 ) + 'px';
			};

			trx_addons_sc_googlemap_overlay.prototype.onRemove = function() {
				this.div_.parentNode.removeChild(this.div_);
			};

		}

		if (container === undefined) container = jQuery('body');
		
		var gmap = container.find('.sc_googlemap:not(.inited)');
		if (gmap.length > 0) {
			gmap.each(function () {
				if (jQuery(this).parents('div:hidden,article:hidden').length > 0) return;
				var map 		= jQuery(this).addClass('inited'),
					map_id		= map.attr('id'),
					map_zoom	= map.data('zoom'),
					map_style	= map.data('style'),
					map_center  = map.data('center'),
					map_editable= map.data('editable')=='1',
					map_cluster_icon = map.data('cluster-icon'),
					map_markers = [];
				map.find('.sc_googlemap_marker').each(function() {
					var marker = jQuery(this),
						address = marker.data('address');
					if ( !address ) return;
					var latlng = address.split(',').map(parseFloat);
					map_markers.push({
						html:			marker.data('html'), 
						url:			marker.data('url'), 
						icon:			marker.data('icon'),
						icon_retina:	marker.data('icon_retina'),
						icon_width:		marker.data('icon_width'),
						icon_height:	marker.data('icon_height'),
						address:		latlng.length == 2 && !isNaN(latlng[0]) && !isNaN(latlng[1]) ? '' : address,
						latlng:			latlng.length != 2 || isNaN(latlng[0]) || isNaN(latlng[1]) ? '' : latlng,
						description:	marker.data('description'),
						title:			marker.data('title'),
						animation:		marker.data('animation') == 'drop'
											? google.maps.Animation.DROP
											: (marker.data('animation') == 'bounce'
												? google.maps.Animation.BOUNCE
												: false
												)
					});
				});
				trx_addons_sc_googlemap_create( map, {
					style: map_style,
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
	
	
	function trx_addons_sc_googlemap_create(map, coords) {
		if (!googlemap.inited) trx_addons_sc_googlemap_init_styles();
//		try {
			var id = map.attr('id');
			googlemap.count++;
			// Change id if already exists on this page
			if (typeof googlemap.maps[id] !== 'undefined') {
				id += '_copy' + googlemap.count;
				map.attr('id', id);
			}
			var center = null;
			if (coords.center) {
				center = (''+coords.center).split(',').map(parseFloat);
				center = center.length == 2 ? new google.maps.LatLng(center[0], center[1]) : null;
			}
			googlemap.maps[id] = {
				dom: map.get(0),
				markers_inited: 0,
				markers_overlay_present: false,
				markers: coords.markers,
				geocoder_request: false,
				clusterer: null,
				clusterIcon: coords.cluster_icon,
				editable: coords.editable,
				fit_to_bounds: false,
				bounds: [ [-999, -999], [-999, -999] ],
				opt: {
					center: center,
					scrollwheel: false,
					scaleControl: false,
					disableDefaultUI: false,
					zoom: coords.zoom
							? parseInt(coords.zoom, 10) 
							: ( coords.markers.length == 1 && center === null ? 16 : 0 ),
					zoomControl: true,
					panControl: true,
					mapTypeControl: false,
					streetViewControl: false,
					overviewMapControl: false,
					styles: TRX_ADDONS_STORAGE['googlemap_styles'][coords.style ? coords.style : 'default'],
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			};
			trx_addons_sc_googlemap_build(id);
//		} catch (e) {
//			console.log(TRX_ADDONS_STORAGE['msg_sc_googlemap_not_avail']);
//		};
	}
	
	function trx_addons_sc_googlemap_refresh() {
		for (var id in googlemap.maps) {
			trx_addons_sc_googlemap_build(id);
		}
	}

	// Add resize listener
	jQuery(document).on('action.resize_trx_addons', function() {
		for (var id in googlemap.maps) {
			if (   googlemap.maps[id].map
				&& jQuery('#'+id).parent().hasClass('ready')
				&& googlemap.maps[id].window_width != jQuery( window ).width()
			) {
				if ( googlemap.maps[id].opt['center']) {
					googlemap.maps[id].map.setCenter(googlemap.maps[id].opt['center'] );
				}
				if ( googlemap.maps[id].markers_inited == googlemap.maps[id].markers.length && googlemap.maps[id].fit_to_bounds ) {
					googlemap.maps[id].map.fitBounds(googlemap.maps[id].bounds);
					googlemap.maps[id].map.panToBounds(googlemap.maps[id].bounds);
				}
			}
		}
	});

	function trx_addons_sc_googlemap_build(id) {
		// Save window width
		googlemap.maps[id].window_width = jQuery( window ).width();

		// Create map
		googlemap.maps[id].map = new google.maps.Map(googlemap.maps[id].dom, googlemap.maps[id].opt);

		// Prepare maps bounds
		googlemap.maps[id].fit_to_bounds = googlemap.maps[id].opt['zoom'] < 1 && (googlemap.maps[id].opt['center'] || googlemap.maps[id].markers.length > 1);
		googlemap.maps[id].bounds = new google.maps.LatLngBounds();
		if ( googlemap.maps[id].opt['center'] ) {
			googlemap.maps[id].bounds.extend(googlemap.maps[id].opt['center']);
		}

		// Set zoom
		if ( googlemap.maps[id].opt['zoom'] > 0 ) {
			googlemap.maps[id].map.setZoom(googlemap.maps[id].opt['zoom']);
		}
			
		// Add markers
		for (var i=0; i < googlemap.maps[id].markers.length; i++) {
			googlemap.maps[id].markers[i].inited = false;
		}
		trx_addons_sc_googlemap_add_markers(id);
	}
	
	function trx_addons_sc_googlemap_add_markers(id) {
		
		for (var i=0; i < googlemap.maps[id].markers.length; i++) {
			
			if (googlemap.maps[id].markers[i].inited) {
				continue;
			}
			
			var geocache = false,
				address = googlemap.maps[id].markers[i].address,
				tm = (new Date).getTime();

			// First try get latlng from cache with previous geocoding data
			if ( ! googlemap.maps[id].markers[i].latlng ) {
				geocache = trx_addons_get_storage('trx_addons_geocache_googlemap');
				if ( geocache && geocache.charAt(0) == '{' ) {
					geocache = JSON.parse(geocache);
				} else {
					geocache = {};
				}
				if ( typeof geocache[address] == 'object' ) {
					if ( geocache[address].expired < tm ) {
						googlemap.maps[id].markers[i].latlng = geocache[address].latlng;
					} else {
						delete geocache[address];
					}
				}
			}

			// Start geocoding (get lat,lng from the address)
			if ( ! googlemap.maps[id].markers[i].latlng ) {
				
				if (googlemap.maps[id].geocoder_request!==false) continue;
				
				if (!googlemap.geocoder) googlemap.geocoder = new google.maps.Geocoder();
				
				if ( ! address ) {
					googlemap.maps[id].markers[i].inited = true;
					continue;
				}

				googlemap.maps[id].geocoder_request = i;
				
				googlemap.geocoder.geocode({address: googlemap.maps[id].markers[i].address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						try {
							var idx = googlemap.maps[id].geocoder_request;
							if (results[0].geometry.location.lat && results[0].geometry.location.lng) {
								googlemap.maps[id].markers[idx].latlng = [ results[0].geometry.location.lat(), results[0].geometry.location.lng() ];
							} else {
								googlemap.maps[id].markers[idx].latlng = results[0].geometry.location.toString().replace(/\(\)/g, '').split(',').map(parseFloat);
							}
							// Put geocoding result to the cache
							geocache[ googlemap.maps[id].markers[idx].address ] = {
								latlng: googlemap.maps[id].markers[idx].latlng,
								expired: (new Date()).getTime() + 24 * 60 * 60
							};
							trx_addons_set_storage('trx_addons_geocache_googlemap', JSON.stringify(geocache));
							// Resume adding markers
							setTimeout(function() { 
								trx_addons_sc_googlemap_add_markers(id); 
								}, 200);
						} catch(e) {
							// Do nothing
						}
					} else {
						console.log(TRX_ADDONS_STORAGE['msg_sc_googlemap_geocoder_error'] + ' ' + status);
					}
					// Release Geocoder
					googlemap.maps[id].geocoder_request = false;
				});
			
			} else {
				
				// Prepare marker object
				var markerInit = {
					map: googlemap.maps[id].map,
					position: new google.maps.LatLng(googlemap.maps[id].markers[i].latlng[0], googlemap.maps[id].markers[i].latlng[1]),
					clickable: googlemap.maps[id].markers[i].description !== ''
				};
				if (googlemap.maps[id].markers[i].html) {
					googlemap.maps[id].markers_overlay_present = true;
					googlemap.maps[id].markers[i].marker = new trx_addons_sc_googlemap_overlay(
						markerInit.position,
						googlemap.maps[id].markers[i].html,
						googlemap.maps[id].map
					);
				} else {
					if (googlemap.maps[id].markers[i].icon) {
						markerInit.icon = googlemap.maps[id].markers[i].icon_width > 0 && googlemap.maps[id].markers[i].icon_height > 0
											? new google.maps.MarkerImage(googlemap.maps[id].markers[i].icon, null, null, null, new google.maps.Size(googlemap.maps[id].markers[i].icon_width, googlemap.maps[id].markers[i].icon_height))
											: googlemap.maps[id].markers[i].icon;
					}
					if (googlemap.maps[id].markers[i].title) {
						markerInit.title = googlemap.maps[id].markers[i].title;
					}
					if (googlemap.maps[id].markers[i].animation) {
						markerInit.animation = googlemap.maps[id].markers[i].animation;
					}
					googlemap.maps[id].markers[i].marker = new google.maps.Marker(markerInit);
				}

				// Set Map center
				if (googlemap.maps[id].opt['center'] === null 
						&& (googlemap.maps[id].markers.length == 1 || googlemap.maps[id].opt['zoom'] > 0)
				) {
					googlemap.maps[id].opt['center'] = markerInit.position;
					googlemap.maps[id].map.setCenter(googlemap.maps[id].opt['center']);				
				}
				
				// Add description window
				if (googlemap.maps[id].markers[i].description !== '') {
					// Create info window for the marker
					googlemap.maps[id].markers[i].infowindow = new google.maps.InfoWindow({
						content: '<div class="sc_googlemap_info_window">' + googlemap.maps[id].markers[i].description + '</div>'
					});
					// Open info window on click on the marker
					google.maps.event.addListener(googlemap.maps[id].markers[i].marker, "click", function(e) {
						var latlng = e.latLng.toString().replace("(", '').replace(")", "").replace(" ", "").split(',').map(parseFloat);
						for (var j=0; j < googlemap.maps[id].markers.length; j++) {
							if (trx_addons_googlemap_compare_latlng(latlng, googlemap.maps[id].markers[j].latlng)) {
								googlemap.maps[id].opened_marker = googlemap.maps[id].markers[j];
								// Zoom map and center to the marker
								googlemap.maps[id].old_zoom = googlemap.maps[id].map.getZoom();
								googlemap.maps[id].map.setZoom(Math.max(16, googlemap.maps[id].old_zoom + 2));
								googlemap.maps[id].old_center = googlemap.maps[id].map.getCenter();
								var center = googlemap.maps[id].markers[j].latlng;
								googlemap.maps[id].map.setCenter(new google.maps.LatLng(center[0], center[1]));
								// Stop animation
								if (googlemap.maps[id].markers[j].animation) {
									googlemap.maps[id].markers[j].marker.setAnimation(null);
								}
								// Open info window
								googlemap.maps[id].markers[j].infowindow.open(
									googlemap.maps[id].map,
									googlemap.maps[id].markers[j].marker
								);
								break;
							}
						}
					});
					// Resume animation and restore zoom after the info window is closed
					google.maps.event.addListener(googlemap.maps[id].markers[i].infowindow, 'closeclick', function(){
						// Restore default zoom
						googlemap.maps[id].map.setZoom(googlemap.maps[id].old_zoom);
						googlemap.maps[id].map.setCenter(googlemap.maps[id].old_center);
						// Resume animation
						if (googlemap.maps[id].opened_marker.animation) {
							googlemap.maps[id].opened_marker.marker.setAnimation(googlemap.maps[id].opened_marker.animation);
						}
					});
				}

				// Add click handler if URL is present
				if ( googlemap.maps[id].markers[i].url ) {
					// Open new window/tab on click on the marker
					googlemap.maps[id].markers[i].marker.url_ = googlemap.maps[id].markers[i].url;
					google.maps.event.addListener(googlemap.maps[id].markers[i].marker, "click", function(e) {
						window.open( this.url_, '_blank' );
					});
				}

				googlemap.maps[id].markers[i].inited = true;
				googlemap.maps[id].markers_inited++;

				googlemap.maps[id].bounds.extend(markerInit.position);
			}
		}
		
		// If all markers inited
		if (googlemap.maps[id].markers_inited == googlemap.maps[id].markers.length) {
			if (googlemap.maps[id].markers_inited > 1 && !googlemap.maps[id].markers_overlay_present) {
				var markers = [];
				for (i = 0; i < googlemap.maps[id].markers.length; i++) {
					markers.push(googlemap.maps[id].markers[i].marker);
				}
				// Make Cluster
				googlemap.maps[id].clusterer = new MarkerClusterer(googlemap.maps[id].map, markers, {
					maxZoom: 18,
					gridSize: 60,
					styles: [
						{
						url: googlemap.maps[id].clusterIcon,
						width: 48,
						height: 48,
						textColor: "#fff"
						}
					]
				});
			}
			// Fit Bounds
			if (googlemap.maps[id].opt['zoom'] < 1 || googlemap.maps[id].fit_to_bounds) {
				googlemap.maps[id].map.fitBounds(googlemap.maps[id].bounds);
				googlemap.maps[id].map.panToBounds(googlemap.maps[id].bounds);
			}
			// Do theme-specific actions on map is inited
			jQuery(document).trigger( 'action.googlemap_inited', id, googlemap.maps[id] );
			// Display map
			setTimeout(function() {
				jQuery('#'+id).parent().addClass('ready');
				// Do theme-specific actions on map is showed
				jQuery(document).trigger( 'action.googlemap_show', id, googlemap.maps[id] );
			}, 100);
		}
	}
	
	// Compare two latlng strings
	function trx_addons_googlemap_compare_latlng(l1, l2) {
		var m0 = Math.min( (''+l1[0]).length, (''+l2[0]).length);
		l1[0] = Number(l1[0]).toFixed(m0);
		l2[0] = Number(l2[0]).toFixed(m0);
		var m1 = Math.min( (''+l1[1]).length, (''+l2[1]).length);
		l1[1] = Number(l1[1]).toFixed(m1);
		l2[1] = Number(l2[1]).toFixed(m1);
		return l1[0]==l2[0] && l1[1]==l2[1];
	}
	
	
	// Add styles for Google map
	function trx_addons_sc_googlemap_init_styles() {
		TRX_ADDONS_STORAGE['googlemap_styles'] = {
			'default': [],
			'greyscale': [
				{ "stylers": [
					{ "saturation": -100 }
					]
				}
			],
			'inverse': [
				{ "stylers": [
					{ "invert_lightness": true },
					{ "visibility": "on" }
					]
				}
			],
			'simple': [
				{ stylers: [
					{ hue: "#00ffe6" },
					{ saturation: -20 }
					]
				},
				{ featureType: "road",
				  elementType: "geometry",
				  stylers: [
					{ lightness: 100 },
					{ visibility: "simplified" }
					]
				},
				{ featureType: "road",
				  elementType: "labels",
				  stylers: [
					{ visibility: "off" }
					]
				}
			]
		};
		jQuery(document).trigger('action.add_googlemap_styles');
		googlemap.inited = true;
	}

})();