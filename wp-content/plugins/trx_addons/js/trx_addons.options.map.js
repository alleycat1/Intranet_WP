jQuery(document).ready(function() {
	'use strict';

	var timer = null;
	
	// Use object to store all maps separately
	var MapObject = function(map_wrapper) {
		this.map_wrapper = map_wrapper;
		this.map_type = map_wrapper.hasClass('sc_googlemap')
							? 'google'
							: 'openstreet';
		this.map_id = map_wrapper.attr('id');
		if ( ! this.map_id ) {
			this.map_id = this.map_type + 'map_' + (''+Math.random()).replace('.', '');
			map_wrapper.attr('id', this.map_id);
		}
		this.search_field = map_wrapper.parent().find('.trx_addons_options_map_search_text');
		// Init if an external map object is loaded
		this.ready = false;
		var self = this,
			attempts = 0,
			timer = setInterval( function() {
				if ( self.map_type == 'google' && typeof google != 'undefined' && typeof google.maps != 'undefined'
					||
					self.map_type == 'openstreet' && typeof L != 'undefined' && typeof L.map != 'undefined'
				) {
					clearInterval( timer );
					self.init();
				} else if ( ++attempts >= 100 ) {
					clearInterval( timer );
				}
			}, 100 );
	};

	MapObject.prototype = {
		// Init all elements
		init: function() {
			this.ready = true;
			var coords = (this.map_wrapper.data('coords') || '').split(',');
			this.lat  = coords.length>=1 ? coords[0] : '';
			this.lng  = coords.length>=2 ? coords[1] : '';
			this.zoom = coords.length>=3 ? coords[2] : '';
			if ( this.lat && this.lng ) {
				this.initMapElements();
			} else {
				this.geoLocation();
			}
		},

		// Init find address
		initMapElements: function() {

			var lat = this.lat || '34.05536166179949',
				lng = this.lng || '-118.24996948242188',
				zoom = this.zoom || 14,
				center;
			
			if ( this.map_type == 'google' ) {
				center = new google.maps.LatLng(lat, lng);
				this.map = new google.maps.Map( this.map_wrapper.get(0), {
					center           : center,
					zoom             : zoom*1,
					streetViewControl: false,
					mapTypeId        : google.maps.MapTypeId.ROADMAP
				} );
				this.marker = new google.maps.Marker( { position: center, map: this.map, draggable: true } );
				this.geocoder = new google.maps.Geocoder();
				if ( google.maps.places ) {
					this.autocomplete = new google.maps.places.Autocomplete( this.search_field.get(0) );
					this.autocomplete.bindTo( "bounds", this.map );
					this.autocomplete.setFields( [ "place_id", "geometry", "name", "formatted_address" ] );
					var map_object = this;
					this.autocomplete.addListener("place_changed", function() {
						//infowindow.close();
						var place = map_object.autocomplete.getPlace();
						if ( ! place.place_id ) return;
						map_object.geocoder.geocode( { placeId: place.place_id }, function(results, status) {
							if ( status === google.maps.GeocoderStatus.OK ) {
								// Set a map zoom
								//map_object.map.setZoom(11);
								// Set a map center
								map_object.map.setCenter( results[0].geometry.location );
								// Set the position of the marker using the location
								map_object.marker.setPosition( results[0].geometry.location );
								// Set the position of the marker using the place ID and location
								// ( after this way a marker is not draggable )
								/*
								map_object.marker.setPlace( {
									location: results[0].geometry.location,
									placeId: place.place_id
								} );
								map_object.marker.setVisible(true);
								*/
								// Update a latlng in the params field
								map_object.updateParams( results[0].geometry.location );
								// Update a place id field (if present)
								map_object.map_wrapper.parents( '.trx_addons_options_item' ).next().find( '[data-param="google_place_id"] input[type="text"]' ).val( place.place_id );
							}
						} );
					} );
				}
			
			} else if (this.map_type == 'openstreet') {
				// Create map
				this.map = L.map(this.map_id, {
					center: [lat, lng],
					zoom: zoom*1
				} );
				// Add tile layer
				var tiler = trx_addons_array_first_value( TRX_ADDONS_STORAGE['osmap_tiler_styles'] );
				this.maxzoom = tiler['maxzoom'] ? tiler['maxzoom'] : 21;
				if ( TRX_ADDONS_STORAGE['osmap_tiler'] == 'vector' ) {
					// Add vector tile layer
					L.mapboxGL(
						{
							maxZoom: this.maxzoom,
							attribution: TRX_ADDONS_STORAGE['osmap_attribution'],
							accessToken: tiler['token'] ? tiler['token'] : 'not-needed',
							style: trx_addons_array_first_value( TRX_ADDONS_STORAGE['osmap_tiler_styles'] )['url'].replace('{style}', trx_addons_array_first_key( TRX_ADDONS_STORAGE['osmap_tiler_styles'] ) )
						}
					).addTo(this.map);
				} else {
					// Add raster tile layer
					L.tileLayer(
						trx_addons_array_first_value( TRX_ADDONS_STORAGE['osmap_tiler_styles'] )['url'].replace('{style}', trx_addons_array_first_key( TRX_ADDONS_STORAGE['osmap_tiler_styles'] ) ),
						{
							maxZoom: this.maxzoom,
							attribution: TRX_ADDONS_STORAGE['osmap_attribution'],
							id: 'osmap.tiler'
						}
					).addTo(this.map);
				}
				this.marker = L.marker( L.latLng(lat, lng), {draggable: true}).addTo(this.map);
				this.geocoder = L.Control.Geocoder.nominatim();
				//this.geocoder_control = L.Control.geocoder( { geocoder: this.geocoder } ).addTo(this.map);
			}
			this.addListeners();
		},
		
		// Detect current user position
		geoLocation: function() {
			if (navigator.geolocation) {
				var map_object = this;
				// If user not answer for geo location request - init map with default location
				var geolocation_finished = false;
				navigator.geolocation.getCurrentPosition(
					// If geolocation success
					function(position) {
						map_object.lat = position.coords.latitude;
						map_object.lng = position.coords.longitude;
						if ( ! geolocation_finished) {
							geolocation_finished = true;
							map_object.initMapElements();
						} else {
							if (map_object.map_type == 'google') {
								var latlng = new google.maps.LatLng(map_object.lat, map_object.lng);
								map_object.map.setCenter(latlng);
								map_object.marker.setPosition(latlng);
							} else if (map_object.map_type == 'openstreet') {
								var latlng = [map_object.lat, map_object.lng];
								map_object.map.setView(latlng, Math.min(map_object.maxzoom, map_object.map.getZoom()));
							}
						}
					},
					// If geolocation failed
					function(error) {
						if (!geolocation_finished) {
							geolocation_finished = true;
							map_object.initMapElements();
						}
					}
				);
				setTimeout(function() {
					if ( ! geolocation_finished ) {
						geolocation_finished = true;
						map_object.initMapElements();
					}
				}, 10000);
			} else {
				this.initMapElements();
			}
		},

		// Add event listeners
		addListeners: function() {
			var map_object = this;

			if (map_object.map_type == 'google') {
				google.maps.event.addListener( this.map, 'click', function(e) {
					map_object.marker.setPosition(e.latLng);
					map_object.updateParams(e.latLng);
				});

				google.maps.event.addListener( this.map, 'zoom_changed', function(e) {
					map_object.updateParams(map_object.marker.getPosition());
				});

				google.maps.event.addListener( this.marker, 'drag', function (e) {
					map_object.updateParams(e.latLng);
				});

			} else if (map_object.map_type == 'openstreet') {
				map_object.map.on('click', function (e) {
					var latlng = L.latLng( e.latlng.lat, e.latlng.lng );
					map_object.marker.setLatLng(latlng);
					map_object.map.setView( latlng, Math.min(map_object.maxzoom, map_object.map.getZoom()) );
					map_object.updateParams( e.latlng );
				}, map_object);
				map_object.map.on('zoomend zoomlevelschange', function (e) {
					var latlng = map_object.marker.getLatLng();
					map_object.map.setView( latlng, Math.min(map_object.maxzoom, map_object.map.getZoom()) );
					map_object.updateParams( latlng );
				}, map_object);
				map_object.marker.on("dragend", function (e) {
					map_object.updateParams(map_object.marker.getLatLng());
				}, map_object.marker);
			}

			this.search_field.on( 'keydown', function (e) {
				if (e.keyCode == 13) {
					jQuery(this).next().trigger('click');
					e.preventDefault();
					return false;
				}
			});
			this.search_field.next().on( 'click', function () {
				map_object.geocodeAddress();
				return false;
			} );

			jQuery(document).on('admin_action.init_hidden_elements', function(e, container) {
				if (container === undefined) container = jQuery('.trx_addons_options');
				container.find('.trx_addons_options_map').each(function() {
					var map_object = jQuery(this).data('map-object');
					if (map_object && map_object.ready) map_object.refresh();
				});
			});
		},

		refresh: function() {
			var map_object = this;
			if (this.map) {
				var zoom = this.map.getZoom(),
					center = this.map.getCenter();
				if (map_object.map_type == 'google') {
					google.maps.event.trigger(this.map, 'resize');
				} else if (map_object.map_type == 'openstreet') {
					this.map.setView(center, Math.min(this.maxzoom, zoom)).invalidateSize(true);
				}
			}
		},

		// Update coordinate to input field
		updateParams: function(latLng) {
			var coords = '';
			if ( this.map_type == 'google' ) {
				coords = latLng.lat() + ',' + latLng.lng() + ',' + this.map.getZoom();
			} else if ( this.map_type == 'openstreet' ) {
				coords = latLng.lat + ',' + latLng.lng + ',' + Math.min(this.maxzoom, this.map.getZoom());
			}
			if (coords != '') {
				this.map_wrapper.siblings('input[type="hidden"]').val(coords);
			}
		},

		// Find coordinates by address
		geocodeAddress: function() {

			var map_object = this,
				address = map_object.search_field.val();

			if (address) {
			
				if (map_object.map_type == 'google') {
					var latlng = (''+address).split(',').map(parseFloat);
					if ( latlng.length == 2 && ! isNaN(latlng[0]) && ! isNaN(latlng[1]) ) {
						var coords = new google.maps.LatLng(latlng[0], latlng[1]);
						map_object.map.setCenter(coords);
						map_object.marker.setPosition(coords);
						map_object.updateParams(coords);
					} else {
						this.geocoder.geocode({'address': address}, function(results, status) {
							if (status === google.maps.GeocoderStatus.OK) {
								map_object.map.setCenter(results[0].geometry.location);
								map_object.marker.setPosition(results[0].geometry.location);
								map_object.updateParams(results[0].geometry.location);
							}
						});
					}
				
				} else if (map_object.map_type == 'openstreet') {
					var latlng = (''+address).split(',').map(parseFloat);
					if ( latlng.length == 2 && ! isNaN(latlng[0]) && ! isNaN(latlng[1]) ) {
						map_object.map.setView( latlng, Math.min(map_object.maxzoom, map_object.map.getZoom()) );
						map_object.marker.setLatLng( latlng );
						map_object.updateParams( L.latLng(latlng[0], latlng[1]) );
					} else {
						map_object.geocoder.geocode(address, function(results) {
							var r = results[0];
							if (r) {
								map_object.map.setView( r.center, Math.min(map_object.maxzoom, map_object.map.getZoom()) );
								map_object.marker.setLatLng( r.center );
								map_object.updateParams( r.center );
							}
						});
					}
				}
			}
		}
	};


	// First time init all maps
	//-------------------------------------------------
	jQuery('.trx_addons_options_map:not(.inited)').each(function() {
		var map_object = new MapObject(jQuery(this));
		jQuery(this).addClass('inited').data('map-object', map_object);
	});

});