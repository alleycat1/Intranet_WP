/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	// Settings
	var planeClassPrefix = 'trx_addons_image_effects_on_';		// class prefix of wrappers with image

	var globalCanvas = false;									// true - one canvas for all images is used (twitches when scrolling the page),
																// false - separate canvas for each image is created (smooth scrolling, but WebKit limit: maximum of 16 objects per page)

	var permanentDrawing = globalCanvas || false;				// true - permanent redraw images on canvas,
																// false - redraw only on ready and on hover

	var curtains = null;										// global curtains object (used if globalCanvas is true)

	var $document = jQuery(document);

	var firstLoad = false;

	var sliderInited = false;


	window.addEventListener( 'load', function() {
		if ( typeof trx_addons_apply_filters == 'function' ) {
			globalCanvas = trx_addons_apply_filters( 'trx_addons_filter_image_effects_use_global_canvas', globalCanvas );
			permanentDrawing = globalCanvas || false;
		}
		firstLoad = true;
		create_planes();
	} );

	function create_planes() {

		// not available in the edit mode of Elementor
		if ( typeof window.elementorFrontend !== 'undefined' && elementorFrontend.isEditMode() ) {
			return;
		}
		// exit if a module 'Curtains' is not available
		if ( typeof window.Curtains == 'undefined' ) {
			return;
		}

		// get our plane element
		var planeElements = document.querySelectorAll( '[class*="' + planeClassPrefix + '"]:not(.trx_addons_image_effects_inited)'
														+ ( jQuery('body').hasClass( 'allow_lazy_load' )
															? '.lazyload_inited'
															: '' )
														);

		// exit if no image effects are present on the current page
		if ( planeElements.length === 0 ) return;

		// create global canvas and append it to the body
		if ( globalCanvas ) {
			curtains = create_canvas( document.body );
		}

		// create planes and handle them
		trx_addons_when_images_loaded( jQuery( planeElements ), function() {
			var effect, total = 0;
			for (var i = 0; i < planeElements.length; i++) {
				if ( ! firstLoad && planeElements[i].closest( '.elementor-section-stretched' ) ) continue;
				if ( ! sliderInited && planeElements[i].closest( '.slider-slide' ) ) continue;
				total++;
				if ( ! planeElements[i].classList.contains( 'trx_addons_image_effects_inited' ) && jQuery( planeElements[i] ).parents(':hidden').length === 0 ) {
					effect = get_effect_name( planeElements[i] );
					if ( effect && typeof window['trx_addons_image_effects_callback_' + effect] == 'function' ) {
						window['trx_addons_image_effects_callback_' + effect]( curtains, planeElements[i], i, planeElements.length );
						planeElements[i].classList.add("trx_addons_image_effects_inited");
					}
				}
			}
			// mark body as all planes are loaded
			if ( total === planeElements.length ) {
				document.body.classList.add("trx_addons_image_effects_planes_loaded");
				$document.trigger('action.trx_addons_image_effects_inited', [planeElements]);
			}
		} );
	}

	$document.on( 'action.got_ajax_response', function() {
		if ( firstLoad ) {
			create_planes();
		}
	} );

	$document.on( 'action.init_hidden_elements', function() {
		if ( firstLoad ) {
			create_planes();
		}
	} );

	$document.on( 'action.slider_inited', function() {
		if ( ! sliderInited ) {
			sliderInited = true;
			create_planes();
		}
	} );

	$document.on( 'action.init_lazy_load_elements', function( e, element ) {
		var parent = element.parents('[class*="trx_addons_image_effects_on_"]:not(.trx_addons_image_effects_inited)');
		if ( parent.length > 0 ) {
			parent.addClass('lazyload_inited');
			// Hide and load image
			parent.css({'opacity': 0, 'transition': 'opacity 0s ease'});
			// Re-create canvas elements
			create_planes();
			// Fade in image
			setTimeout(function(){
				// Show loaded image	
				parent.css({'opacity': 1, 'transition': 'opacity 0.3s ease'});
				// Remove styles
				setTimeout(function(){
					parent.css({'opacity': '', 'transition': ''});
				}, 300);
			}, 100);
		}	
	} );

	$document.on('action.before_remove_content action.deactivate_tab', function(e, cont) {
		cont.find( '.trx_addons_image_effects_inited' ).each( function() {
			var $self = jQuery( this ),
				$canvas = $self.find('.trx_addons_image_effects_canvas'),
				curtains = $self.data('curtains');
			if ( ! globalCanvas ) {
				if ( curtains ) {
					curtains.dispose();
					curtains = null;
					$self.removeData( 'curtains' );
				}
			} else {
				var plane = $self.data('curtains-plane');
				if ( plane ) {
					curtains.removePlane( plane );
					plane = null;
					$self.removeData('curtains-plane');
				}
			}
			$canvas.remove();
		});
	});

	$document.on('action.after_add_content action.activate_tab', function(e, cont) {
		cont.find( '.trx_addons_image_effects_inited' ).each( function() {
			jQuery( this )
				.removeClass('trx_addons_image_effects_inited')
				.find( '[id^="trx_addons_image_effects_canvas_"]' ).remove().end()
				.find( '.trx_addons_image_effects_holder' ).removeClass('trx_addons_image_effects_holder').end();
		});
	});


	// Utilities
	//-------------------------------------------

	// Return name of image effect for element
	function get_effect_name( elm ) {
		var name = '';
		for ( var i=0; i < elm.classList.length; i++ ) {
			if ( elm.classList[i].indexOf(planeClassPrefix) === 0 ) {
				name = elm.classList[i].substring( planeClassPrefix.length );
				break;
			}
		}
		return name;
	}

	// Return mouse coordinates relative to the current image
	function get_mouse_position_from_event( e ) {
		var mouse = {};
		// touch event
		if (e.targetTouches) {
			mouse.x = globalCanvas ? e.targetTouches[0].clientX : e.targetTouches[0].layerX;
			mouse.y = globalCanvas ? e.targetTouches[0].clientY : e.targetTouches[0].layerY;

		// mouse event
		} else {
												// In Chrome with custom scroll mouse coordinates in layerXY are incorrect
												// but in Mozilla mouse properties offsetXY always empty (equal to 0)
			mouse.x = globalCanvas ? e.clientX : ( e.offsetX !== 0 || e.offsetY !== 0 ? e.offsetX : e.layerX );
			mouse.y = globalCanvas ? e.clientY : ( e.offsetX !== 0 || e.offsetY !== 0 ? e.offsetY : e.layerY );
		}
		return mouse;
	}

	// Convert our mouse/touch position to coordinates relative to the vertices of the plane
	function mouse_to_plane_coords( plane, mpos ) {
		var w = plane.htmlElement.clientWidth, 	cw = w / 2,
			h = plane.htmlElement.clientHeight,	ch = h / 2;
		return {
				x:  ( mpos.x - cw ) / cw,
				y: -( mpos.y - ch ) / ch
			};
	}

	// Add canvas holder
	var total = 0;
	function create_canvas( item ) {

		// Append canvas holder to the item
		var id = 'trx_addons_image_effects_canvas_'+total++,
			div = document.createElement("div");
		div.setAttribute('id', id);
		div.setAttribute('class', 'trx_addons_image_effects_canvas');
		item.appendChild(div);

		// Set up our WebGL context and append the canvas to our wrapper
		var webGLCurtain = new Curtains({
			watchScroll: globalCanvas,
			premultipliedAlpha: true,	// to avoid gray edge on images in effects 'waves', 'smudge', etc. (who changes the image border geometry)
			container: id	// if not specified - library create own canvas container
		});

		// Handling errors
		webGLCurtain
			.onError(function() {
				// we will add a class to the document body to display original images
				document.body.classList.add("no-curtains", "trx_addons_image_effects_planes_loaded");
			})
			.onContextLost(function() {
				// on context lost, try to restore the context
				webGLCurtain.restoreContext();
			});

		return webGLCurtain;
	}



	// Effect 'Waves'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_waves = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		var waveForceMin = 0,
			waveForceMax = 7;

		var waveFactor = elm.getAttribute('data-image-effect-waves-factor') || 4;

		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var paddingOnHover = typeof trx_addons_apply_filters == 'function'
								? trx_addons_apply_filters( 'trx_addons_filter_image_effects_padding', 0.04, 'waves' )
								: 0.04;

		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );

		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'wavesTexture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// those are the mandatory attributes that the lib sets
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				// those are mandatory uniforms that the lib sets and that contain our model view and projection matrix
				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrix uniform
				uniform mat4 wavesTextureMatrix;

				// if you want to pass your vertex and texture coords to the fragment shader
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;

				// effect control vars declared inside our javascript
				uniform float uTime;
				uniform float uMouseMoveStrength;
				uniform float uEffectStrength;
				uniform float uWaveFactor;
				uniform vec2 uMousePosition;
				uniform float hoveringWaveForce;
				uniform float uPadding;

				void main() {
					vec3 vertexPosition = aVertexPosition;
					float distanceFromMouse = distance(uMousePosition, vec2(vertexPosition.x, vertexPosition.y));
					float waveSinusoid = cos(uWaveFactor * (distanceFromMouse - (uTime / 75.0)));
					float distanceStrength = 0.4 / (distanceFromMouse + 0.4);
					float distortionEffect = distanceStrength * waveSinusoid * uMouseMoveStrength / uEffectStrength;
					vertexPosition.z +=  distortionEffect;
					vertexPosition.x +=  distortionEffect * (uMousePosition.x - vertexPosition.x) * hoveringWaveForce * 3.0;
					vertexPosition.y +=  distortionEffect * (uMousePosition.y - vertexPosition.y) * hoveringWaveForce;
					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, (1.0 + uPadding));
					vTextureCoord = (wavesTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vVertexPosition = vertexPosition;
				}
			`;
			
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif
				// get our varying variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;

				// our texture sampler
				uniform sampler2D wavesTexture;

				// effect control vars
				uniform float displacement;

				void main() {
					if ( false ) {
						float intensity = 1.0;
						vec2 textureCoord = vTextureCoord;
						vec4 image1 = texture2D(wavesTexture, textureCoord);
						vec4 image2 = texture2D(wavesTexture, textureCoord);
						vec4 texture1 = texture2D(wavesTexture, vec2(textureCoord.x, textureCoord.y + displacement * (image2 * intensity)));
						vec4 texture2 = texture2D(wavesTexture, vec2(textureCoord.x, textureCoord.y + (1.0 - displacement) * (image1 * intensity)));
						vec4 result = mix(texture1, texture2, displacement);
						gl_FragColor = result;
					} else {
						vec2 textureCoord = vTextureCoord;
						gl_FragColor = texture2D(wavesTexture, textureCoord);
					}
				}
			`;

			return {
				vertexShader: vs,
				fragmentShader: fs,
				widthSegments: 10,
				heightSegments: 10,
				uniforms: {
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					},
					mousePosition: {
						name: "uMousePosition",
						type: "2f",
						value: [-.5, .5]
					},
					mouseMoveStrength: {
						name: "uMouseMoveStrength",
						type: "1f",
						value: .2
					},
					effectStrength: {
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					displacement: {
						name: "displacement",
						type: "1f",
						value: 0
					},
					waveFactor: {
						name: "uWaveFactor",
						type: "1f",
						value: waveFactor
					},
					hoveringWaveForce: {
						name: "hoveringWaveForce",
						type: "1f",
						value: waveForceMin
					},
					padding: {
						name: "uPadding",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log('Waves onLoading');
				})
				.onReady(function() {
//					console.log('Waves onReady: Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenForce = null;
					plane.tweenScale = null;
					plane.tweenPadding = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move() {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_wave_force( waveForceMax );
							change_scale( 1 );
							if ( paddingOnHover ) {
								change_padding( paddingOnHover );
							}
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_wave_force( waveForceMin );
							change_scale( 0 );
							if ( paddingOnHover ) {
								change_padding( 0 );
							}
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
//					if ( globalCanvas ) plane.updatePosition();
					plane.uniforms.time.value++;

				})
				.onAfterResize(function() {
//					console.log('Waves afterResize: Plane '+index+' of '+total+' is resized');
				});

			// Change wave force value
			function change_wave_force( to ) {
				if ( plane.tweenForce ) {
					trx_addons_tween_stop( plane.tweenForce );
				}
				plane.tweenForce = trx_addons_tween_value( {
					start: plane.uniforms.hoveringWaveForce.value,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							plane.uniforms.hoveringWaveForce.value = value;
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenForce );
							plane.tweenForce = null;
						}
					}
				} );
			}

			// Change padding value
			function change_padding( to ) {
				if ( plane.tweenPadding ) {
					trx_addons_tween_stop( plane.tweenPadding );
				}
				plane.tweenPadding = trx_addons_tween_value( {
					start: plane.uniforms.padding.value,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							plane.uniforms.padding.value = value;
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenPadding );
							plane.tweenPadding = null;
						}
					}
				} );
			}

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing && to === 0 ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}
	};



	// Effect 'Waves2'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_waves2 = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		var waveForceMin = 0,
			waveForceMax = 2;

		var waveFactor = elm.getAttribute('data-image-effect-waves-factor') || 4;

		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var paddingOnHover = typeof trx_addons_apply_filters == 'function'
								? trx_addons_apply_filters( 'trx_addons_filter_image_effects_padding', 0.04, 'waves2' )
								: 0.04;

		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );

		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'waves2Texture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params( elm, img, parent ) {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrix
				uniform mat4 waves2TextureMatrix;

				// custom variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				uniform float uTime;
				uniform vec2 uResolution;
				uniform vec2 uMousePosition;
				uniform float uMouseMoveStrength;
				uniform float uEffectStrength;
				uniform float uWaveFactor;
				uniform float uPadding;

				void main() {
					vec3 vertexPosition = aVertexPosition;
					// get the distance between our vertex and the mouse position
					float distanceFromMouse = distance(uMousePosition, vec2(vertexPosition.x, vertexPosition.y));
					// calculate our wave effect
					float waveSinusoid = cos(uWaveFactor * (distanceFromMouse - (uTime / 75.0)));
					// attenuate the effect based on mouse distance
					float distanceStrength = 0.4 / (distanceFromMouse + 0.4);
					// calculate our distortion effect
					float distortionEffect = distanceStrength * waveSinusoid * uMouseMoveStrength / uEffectStrength;
					// apply it to our vertex position
					vertexPosition.z += distortionEffect;
					vertexPosition.x += distortionEffect * (uMousePosition.x - vertexPosition.x) * (uResolution.x / uResolution.y);
					vertexPosition.y += distortionEffect * (uMousePosition.y - vertexPosition.y);
					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, (1.0 + uPadding));
					// varyings
					vTextureCoord = (waves2TextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vVertexPosition = vertexPosition;
				}
			`;
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				uniform sampler2D waves2Texture;

				void main() {
					// apply our texture
					vec4 finalColor = texture2D(waves2Texture, vTextureCoord);
					// fake shadows based on vertex position along Z axis
					finalColor.rgb -= clamp(-vVertexPosition.z, 0.0, 1.0);
					// fake lights based on vertex position along Z axis
					finalColor.rgb += clamp(vVertexPosition.z, 0.0, 1.0);
					// handling premultiplied alpha (useful if we were using a png with transparency)
					finalColor = vec4(finalColor.rgb * finalColor.a, finalColor.a);
					gl_FragColor = finalColor;
				}
			`;

			return {
				vertexShader: vs,
				fragmentShader: fs,
				widthSegments: 20,
				heightSegments: 20,
				uniforms: {
					resolution: { // resolution of our plane
						name: "uResolution",
						type: "2f", // notice this is an length 2 array of floats
						value: [ parent.clientWidth, parent.clientHeight ]
					},
					time: { // time uniform that will be updated at each draw call
						name: "uTime",
						type: "1f",
						value: 0
					},
					mousePosition: { // our mouse position
						name: "uMousePosition",
						type: "2f", // again an array of floats
						value: [-.5, .5]
					},
					mouseMoveStrength: { // the mouse move strength
						name: "uMouseMoveStrength",
						type: "1f",
						value: 0
					},
					effectStrength: { // the effect strength
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					waveFactor: {    // waves frequency
						name: "uWaveFactor",
						type: "1f",
						value: waveFactor
					},
					padding: { // padding around image on hover
						name: "uPadding",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
					//console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// set a fov of 35 to reduce perspective
					plane.setPerspective(35);
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tween = null;
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move() {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_wave_force( waveForceMax );
							change_scale( 1 );
							if ( paddingOnHover ) {
								change_padding( paddingOnHover );
							}
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_wave_force( waveForceMin );
							change_scale( 0 );
							if ( paddingOnHover ) {
								change_padding( 0 );
							}
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					// increment our time uniform
					plane.uniforms.time.value++;
				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
					/* Commented, because set width and height to 0 inside slider
					var planeBoundingRect = plane.getBoundingRect();
					plane.uniforms.resolution.value = [ planeBoundingRect.width, planeBoundingRect.height ];
					*/
				});
		}

		// Change wave force value
		function change_wave_force( to ) {
			if ( plane.tween ) {
				trx_addons_tween_stop( plane.tween );
			}
			plane.tween = trx_addons_tween_value( {
				start: plane.uniforms.mouseMoveStrength.value,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						plane.uniforms.mouseMoveStrength.value = value;
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tween );
						plane.tween = null
					}
				}
			} );
		}

		// Change padding value
		function change_padding( to ) {
			if ( plane.tweenPadding ) {
				trx_addons_tween_stop( plane.tweenPadding );
			}
			plane.tweenPadding = trx_addons_tween_value( {
				start: plane.uniforms.padding.value,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						plane.uniforms.padding.value = value;
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenPadding );
						plane.tweenPadding = null;
					}
				}
			} );
		}

		// Change scale
		function change_scale( to ) {
			if ( plane.tweenScale ) {
				trx_addons_tween_stop( plane.tweenScale );
			}
			if ( ! permanentDrawing ) {
				curtains.enableDrawing();
			}
			plane.tweenScale = trx_addons_tween_value( {
				start: scaleFactor,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						scaleFactor = value;
						if ( scaleOnHover ) {
							plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
						}
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenScale );
						plane.tweenScale = null;
						if ( ! permanentDrawing && to === 0 ) {
							curtains.disableDrawing();
						}
					}
				}
			} );
		}
	};



	// Effect 'Ripple'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_ripple = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		// Common vars
		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );

		// Effect-specific vars
		var wavesDirection = Math.max(0.0, Math.min(1.0, elm.getAttribute('data-image-effect-waves-direction') ) );

		// Curtains init
		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'rippleTexture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `

				#ifdef GL_ES
					precision mediump float;
				#endif

				// those are the mandatory attributes that the lib sets
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				// those are mandatory uniforms that the lib sets and that contain our model view and projection matrix
				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrix uniform
				uniform mat4 rippleTextureMatrix;

				// if you want to pass your vertex and texture coords to the fragment shader
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;

				void main() {
					// get the vertex position from its attribute
					vec3 vertexPosition = aVertexPosition;
					
					// set its position based on projection and model view matrix
					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, 1.0);

					// set the varying variables
					// thanks to the texture matrix we will be able to calculate accurate texture coords
					// so that our texture will always fit our plane without being distorted
					vTextureCoord = (rippleTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vVertexPosition = vertexPosition;
				}
			`;
			
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// get our varying variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;

				// our texture sampler
				uniform sampler2D rippleTexture;

				// effect control vars declared inside our javascript
				uniform float uTime;			// time iterator to change waves position
				uniform float uEffectStrength;	// 5.0 - 50.0 - waves amplitude
				uniform float uWavesForce;		// 0.0 - 1.0 - fadeIn/fadeOut on mouse hover
				uniform float uWavesDirection;	// 0 - horizontal, 1 - vertical

				void main() {
					// get our texture coords
					vec2 textureCoord = vTextureCoord;

					// displace our pixels along both axis based on our time uniform and texture UVs
					// this will create a kind of water surface effect
					// try to comment a line or change the constants to see how it changes the effect
					// reminder : textures coords are ranging from 0.0 to 1.0 on both axis
					const float PI = 3.141592;

					textureCoord.x += (
										sin(textureCoord.x * 10.0 * ( 2.0 - uWavesDirection ) + ((uTime * (PI / 3.0)) * 0.031))
										+ sin(textureCoord.y * 10.0 * ( 2.0 - uWavesDirection ) + ((uTime * (PI / 2.489)) * 0.017))
										) / uEffectStrength / ( 2.5 + 1.5 * uWavesDirection ) * uWavesForce;	// * 0.0075;

					textureCoord.y += (
										sin(textureCoord.y * 20.0 / ( 2.0 - uWavesDirection ) + ((uTime * (PI / 2.023)) * 0.023))
										+ sin(textureCoord.x * 20.0 / ( 2.0 - uWavesDirection ) + ((uTime * (PI / 3.1254)) * 0.037))
										) / uEffectStrength / ( 2.5 + 1.5 * ( 1.0 - uWavesDirection ) ) * uWavesForce;	// * 0.0125;

					gl_FragColor = texture2D(rippleTexture, textureCoord);
				}
			`;

			return {
				vertexShader: vs,		// our vertex shader ID
				fragmentShader: fs,		// our fragment shader ID
//				widthSegments: 10,
//				heightSegments: 10,
				uniforms: {				// variables passed to shaders
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					},
					effectStrength: {
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					wavesForce: {
						name: "uWavesForce",
						type: "1f",
						value: 0
					},
					wavesDirection: {
						name: "uWavesDirection",
						type: "1f",
						value: wavesDirection
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenForce = null;
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_waves_force( 1 );
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_waves_force( 0 );
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value++;

				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
				});

			// Change wave force value
			function change_waves_force( to ) {
				if ( plane.tweenForce ) {
					trx_addons_tween_stop( plane.tweenForce );
				}
				plane.tweenForce = trx_addons_tween_value( {
					start: plane.uniforms.wavesForce.value,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							plane.uniforms.wavesForce.value = value;
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenForce );
							plane.tweenForce = null;
						}
					}
				} );
			}

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing && to === 0 ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}
	};



	// Effect 'Ripple 2'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_ripple2 = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		// Common vars
		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );

		// Effect-specific vars
		var wavesDirection = Math.max(0.0, Math.min(1.0, elm.getAttribute('data-image-effect-waves-direction') ) );

		// Curtains init
		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'ripple2Texture');
			var displacement_url = elm.getAttribute('data-image-effect-displacement');
			if ( displacement_url ) {
				var displacement_img = document.createElement("img");
//				displacement_img.setAttribute('crossorigin', 'anonymous');
				displacement_img.setAttribute('src', displacement_url);
				displacement_img.setAttribute('data-sampler', 'ripple2Displacement');
				displacement_img.classList.add('trx_addons_image_effects_ripple_displacement');
				parent.appendChild(displacement_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_ripple_displacement' ).remove();
				});
			}
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrices
				// notice how it matches our data-sampler attributes + "Matrix"
				uniform mat4 ripple2TextureMatrix;

				// varying variables
				varying vec3 vVertexPosition;

				// our displacement texture will use original texture coords attributes
				varying vec2 vDisplacementCoord;

				// our image will use texture coords based on their texture matrices
				varying vec2 vTextureCoord;

				// custom uniforms
				uniform float uTime;

				void main() {
					vec3 vertexPosition = aVertexPosition;

					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, 1.0);

					// varying variables
					// texture coords attributes because we want our displacement texture to be contained
					vDisplacementCoord = aTextureCoord;
					// our image texture coords based on their texture matrices
					vTextureCoord = (ripple2TextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					// vertex position as usual
					vVertexPosition = vertexPosition;
				}
			`;
			
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// all our varying variables
				varying vec3 vVertexPosition;
				varying vec2 vDisplacementCoord;
				varying vec2 vTextureCoord;

				// our textures samplers
				// notice how it matches our data-sampler attributes
				uniform sampler2D ripple2Texture;
				uniform sampler2D ripple2Displacement;

				// effect control vars declared inside our javascript
				uniform float uTime;			// time iterator to change waves position
				uniform float uEffectStrength;	// 5.0 - 50.0 - waves amplitude
				uniform float uWavesForce;		// 0.0 - 1.0 - fadeIn/fadeOut on mouse hover
				uniform float uWavesDirection;	// 0 - horizontal, 1 - vertical

				void main( void ) {

					// our displacement texture
					vec2 displacementCoords = vDisplacementCoord;

					displacementCoords = vec2( mod(displacementCoords.x - (1.0 - uWavesDirection) * uTime / ( 400.0 + uEffectStrength * 5.0 ), 1.0),
											   mod(displacementCoords.y - uWavesDirection * uTime / ( 600.0 + uEffectStrength * 5.0 ), 1.0)
											);
					vec4 displacementTexture = texture2D(ripple2Displacement, displacementCoords);

					// image texture
					vec2 textureCoords = vTextureCoord;

					// displace our pixels along both axis based on our time uniform and texture UVs
					// this will create a kind of water surface effect
					// try to comment a line or change the constants to see how it changes the effect
					// reminder : textures coords are ranging from 0.0 to 1.0 on both axis
					const float PI = 3.141592;

					textureCoords.x += 1.0 / uEffectStrength / ( 2.5 + 1.5 * uWavesDirection ) * uWavesForce * displacementTexture.r;

					textureCoords.y -= 1.0 / uEffectStrength / ( 2.0 + 1.5 * ( 1.0 - uWavesDirection ) ) * uWavesForce * displacementTexture.r;

					vec4 finalColor = texture2D(ripple2Texture, textureCoords);

					// handling premultiplied alpha and apply displacement
					finalColor = vec4(finalColor.rgb * finalColor.a, finalColor.a);

					// apply our shader
					gl_FragColor = finalColor;
				}
			`;

			return {
				vertexShader: vs,		// our vertex shader ID
				fragmentShader: fs,		// our fragment shader ID
//				widthSegments: 10,
//				heightSegments: 10,
				imageCover: false,		// our displacement texture has to fit the plane
				uniforms: {				// variables passed to shaders
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					},
					effectStrength: {
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					wavesForce: {
						name: "uWavesForce",
						type: "1f",
						value: 0
					},
					wavesDirection: {
						name: "uWavesDirection",
						type: "1f",
						value: wavesDirection
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenForce = null;
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_waves_force( 1 );
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_waves_force( 0 );
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value++;

				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
				});

			// Change wave force value
			function change_waves_force( to ) {
				if ( plane.tweenForce ) {
					trx_addons_tween_stop( plane.tweenForce );
				}
				plane.tweenForce = trx_addons_tween_value( {
					start: plane.uniforms.wavesForce.value,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							plane.uniforms.wavesForce.value = value;
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenForce );
							plane.tweenForce = null;
						}
					}
				} );
			}

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing && to === 0 ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}
	};



	// Effect 'Smudge'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_smudge = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		var enableDrawing = false;

		// track the mouse positions to send it to the shaders
		var mousePosition = {
			x: 0,
			y: 0
		};

		// we will keep track of the last position in order to calculate the movement strength/delta
		var mouseLastPosition = {
			x: 0,
			y: 0
		};

		var mouseStrength = false;

		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var paddingOnHover = typeof trx_addons_apply_filters == 'function'
								? trx_addons_apply_filters( 'trx_addons_filter_image_effects_padding', 0.04, 'smudge' )
								: 0.04;

		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );

		var deltas = {
			max: 0,
			applied: 0
		};

		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'smudgeTexture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params( elm, img, parent ) {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrix
				uniform mat4 smudgeTextureMatrix;

				// custom variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				uniform float uTime;
				//uniform vec2 uResolution;
				uniform vec2 uMousePosition;
				uniform float uMouseMoveStrength;
				uniform float uEffectStrength;
				uniform float uPadding;

				void main() {
					vec3 vertexPosition = aVertexPosition;
					// get the distance between our vertex and the mouse position
					float distanceFromMouse = distance(uMousePosition, vec2(vertexPosition.x, vertexPosition.y));
					// attenuate the effect based on mouse distance
					float distanceStrength = 0.4 / (distanceFromMouse + 0.4);
					// calculate our distortion effect
					float distortionEffect = distanceStrength * uMouseMoveStrength / uEffectStrength;
					// apply it to our vertex position
					vertexPosition.z += distortionEffect;
					//vertexPosition.x += distortionEffect * (uMousePosition.x - vertexPosition.x) * (uResolution.x / uResolution.y);
					vertexPosition.x += distortionEffect * (uMousePosition.x - vertexPosition.x);
					vertexPosition.y += distortionEffect * (uMousePosition.y - vertexPosition.y);
					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, (1.0 + uPadding));
					// varyings
					vTextureCoord = (smudgeTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vVertexPosition = vertexPosition;
				}
			`;
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				uniform sampler2D smudgeTexture;

				void main() {
					// apply our texture
					vec4 finalColor = texture2D(smudgeTexture, vTextureCoord);
					// fake shadows based on vertex position along Z axis
					finalColor.rgb -= clamp(-vVertexPosition.z, 0.0, 1.0);
					// fake lights based on vertex position along Z axis
					finalColor.rgb += clamp(vVertexPosition.z, 0.0, 1.0);
					// handling premultiplied alpha (useful if we were using a png with transparency)
					finalColor = vec4(finalColor.rgb * finalColor.a, finalColor.a);
					gl_FragColor = finalColor;
				}
			`;

			return {
				vertexShader: vs,
				fragmentShader: fs,
				widthSegments: 20,
				heightSegments: 20,
				uniforms: {
					/*
					resolution: { // resolution of our plane
						name: "uResolution",
						type: "2f", // notice this is an length 2 array of floats
						value: [ parent.clientWidth, parent.clientHeight ]
					},
					*/
					time: { // time uniform that will be updated at each draw call
						name: "uTime",
						type: "1f",
						value: 0
					},
					mousePosition: { // our mouse position
						name: "uMousePosition",
						type: "2f", // again an array of floats
						value: [ mousePosition.x, mousePosition.y ]
					},
					mouseMoveStrength: { // the mouse move strength
						name: "uMouseMoveStrength",
						type: "1f",
						value: 0
					},
					effectStrength: { // the smudge strength
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					padding: { 		// paddings around image on hover
						name: "uPadding",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
					//console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// set a fov of 35 to reduce perspective
					plane.setPerspective(35);
					// apply a little effect once everything is ready
					deltas.max = 2;
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
						handle_movement(e, plane);
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_scale( 1 );
							if ( paddingOnHover ) {
								change_padding(paddingOnHover);
							}
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_scale( 0 );
							if ( paddingOnHover ) {
								change_padding(0);
							}
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					// increment our time uniform
					plane.uniforms.time.value++;
					// decrease both deltas by damping : if the user doesn't move the mouse, effect will fade away
					deltas.applied += (deltas.max - deltas.applied) * 0.02;
					deltas.max += (0 - deltas.max) * 0.01;
					// send the new mouse move strength value
					plane.uniforms.mouseMoveStrength.value = deltas.applied;
					if ( ! permanentDrawing && ! enableDrawing && Math.abs(deltas.applied - deltas.max) < 0.001 ) {
						curtains.disableDrawing();
					}
				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
					/*
					var planeBoundingRect = plane.getBoundingRect();
					plane.uniforms.resolution.value = [planeBoundingRect.width, planeBoundingRect.height];
					*/
				});
		}

		// handle the mouse move event
		function handle_movement(e, plane) {

			// update mouse last pos
			mouseLastPosition.x = mousePosition.x;
			mouseLastPosition.y = mousePosition.y;

			var mouse = {};

			// touch event
			if (e.targetTouches) {
				mouse.x = globalCanvas ? e.targetTouches[0].clientX : e.targetTouches[0].layerX;
				mouse.y = globalCanvas ? e.targetTouches[0].clientY : e.targetTouches[0].layerY;

			// mouse event
			} else {
													// In Chrome with custom scroll mouse coordinates in layerXY are incorrect
													// but in Mozilla mouse properties offsetXY always empty (equal to 0)
				mouse.x = globalCanvas ? e.clientX : ( e.offsetX !== 0 || e.offsetY !== 0 ? e.offsetX : e.layerX );
				mouse.y = globalCanvas ? e.clientY : ( e.offsetX !== 0 || e.offsetY !== 0 ? e.offsetY : e.layerY );
			}

			// lerp the mouse position a bit to smoothen the overall effect
			mousePosition.x = trx_addons_lerp(mousePosition.x, mouse.x, 0.3);
			mousePosition.y = trx_addons_lerp(mousePosition.y, mouse.y, 0.3);

			// convert our mouse/touch position to coordinates relative to the vertices of the plane
			var mouseCoords = globalCanvas
								? plane.mouseToPlaneCoords(mousePosition.x, mousePosition.y)
								: mouse_to_plane_coords( plane, mousePosition );
			

			// update our mouse position uniform
			plane.uniforms.mousePosition.value = [mouseCoords.x, mouseCoords.y];

			// calculate the mouse move strength
			if ( mouseStrength && mouseLastPosition.x && mouseLastPosition.y ) {
				var delta = Math.sqrt( Math.pow( mousePosition.x - mouseLastPosition.x, 2 ) + Math.pow( mousePosition.y - mouseLastPosition.y, 2 ) ) / 20;
				delta = Math.min(4, delta);
				// update max delta only if it increased
				if ( delta >= deltas.max ) {
					deltas.max = delta;
				}
			} else {
				deltas.max = 2;
			}
		}

		// Change padding value
		function change_padding( to ) {
			if ( plane.tweenPadding ) {
				trx_addons_tween_stop( plane.tweenPadding );
			}
			plane.tweenPadding = trx_addons_tween_value( {
				start: plane.uniforms.padding.value,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						plane.uniforms.padding.value = value;
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenPadding );
						plane.tweenPadding = null;
					}
				}
			} );
		}

		// Change scale
		function change_scale( to ) {
			if ( plane.tweenScale ) {
				trx_addons_tween_stop( plane.tweenScale );
			}
			if ( ! permanentDrawing ) {
				enableDrawing = true;
				curtains.enableDrawing();
			}
			plane.tweenScale = trx_addons_tween_value( {
				start: scaleFactor,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						scaleFactor = value;
						if ( scaleOnHover ) {
							plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
						}
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenScale );
						plane.tweenScale = null;
						if ( ! permanentDrawing && to === 0 ) {
							enableDrawing = false;
							//curtains.disableDrawing();
						}
					}
				}
			} );
		}

	};



	// Effect 'Tint'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_tint = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0,
			tintColor = elm.getAttribute('data-image-effect-tint-color') || TRX_ADDONS_STORAGE['theme_accent_color'];

		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'tintTexture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params( elm, img, parent ) {

			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				vec3 permute(vec3 x) {
					return mod((x*34.0+1.0)*x, 289.0);
				}
				
				float snoise(vec2 v) {
					const vec4 C = vec4(0.211325, 0.366025, -0.57735, 0.02439);
					vec2 i = floor(v + dot(v, C.yy)),
						 x0 = v - i + dot(i, C.xx),
						 i1;
					i1 = x0.x > x0.y ? vec2(1.0, 0.0) : vec2(0.0,1.0);
					vec4 x12 = x0.xyxy + C.xxzz;
					x12.xy -= i1,
					i = mod(i, 289.0);
					vec3 p = permute(permute(i.y + vec3(0.0, i1.y, 1.0)) + i.x + vec3(0.0,i1.x, 1.0)),
						 m = max(0.5 - vec3(dot(x0, x0), dot(x12.xy, x12.xy), dot(x12.zw, x12.zw)), 0.0);
					m = m * m, m = m * m;
					vec3 x = 2.0 * fract(p * C.www) - 1.0,
						 h = abs(x) - 0.5,
						 ox = floor(x + 0.5),
						 a0 = x - ox;
					m *= 1.792843 - 0.853735 * (a0 * a0 + h * h);
					vec3 g;
					g.x = a0.x * x0.x + h.x * x0.y,
					g.yz = a0.yz * x12.xz + h.yz * x12.yw;
					return 130.0 * dot(m, g);
				}
				
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix, uPMatrix, tintTextureMatrix;
				uniform float uTime, uMouseOver;

				varying vec2 vTintTextureCoord;
				varying vec3 vVertexPosition, vNoise;

				void main(){
					vec3 vP = aVertexPosition;
					vec2 sUV = vec2(vP.x * 0.75, vP.y * 0.75);
					vec3 sN = vec3(snoise(sUV * 2.0 - uTime / 360.0));
					vP.z = 0.0,
					gl_Position = uPMatrix * uMVMatrix * vec4(vP, 1.0),
					vVertexPosition = vP,
					vTintTextureCoord = (tintTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy,
					vNoise = sN;
				}
			`;
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif
				varying vec2 vTintTextureCoord;
				varying vec3 vVertexPosition;
				varying vec3 vNoise;

				uniform sampler2D tintTexture;
				uniform float uMouseOver;
				uniform vec3 uColor;

				void main(){
					vec4 f = texture2D(tintTexture, vTintTextureCoord);
					f.rgb -= clamp(-vVertexPosition.z / 10.0, 0.0, 1.0);
					f.rgb += clamp( vVertexPosition.z / 12.5, 0.0, 1.0);
					vec4 lC = vec4(0.299, 0.587, 0.114, 0.0),
						 l = vec4(1.0),
						 tint = vec4(uColor.r / 255.0, uColor.g / 255.0, uColor.b / 255.0, 1.0);
					float lM = dot(f, lC),
						  mN = clamp(uMouseOver * (1.0 - vNoise.r) - 1.0 + uMouseOver * 2.0, 0.0, 1.0);
					vec4 mC = (l + tint) / vec4(2.0),
						 dT = lM >= 0.45 ? mix(mC, l, smoothstep(0.45, 0.93125, lM)) : mix(tint, mC, smoothstep(-0.03125, 0.45, lM));
					f = mix(dT, f, step(0.9, mN));
					f = vec4(f.rgb * f.a, f.a);
					gl_FragColor = f;
				}
			`;

			return {
				vertexShader: vs,
				fragmentShader: fs,
				widthSegments: 5,
				heightSegments: 40,
				fov: 45,
				drawCheckMargins: {
					top: 15,
					right: 0,
					bottom: 15,
					left: 0
				},
				uniforms: {
					time: {
						name: "uTime",
						type: "1f",
						value: 0	// 180 * index
					},
					mouseOver: {
						name: "uMouseOver",
						type: "1f",
						value: 0
					},
					color: {
						name: "uColor",
						type: "3f",
						value: trx_addons_rgb2components(tintColor)
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tween = null;
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_mouse_over( 1 );
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_mouse_over( 0 );
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value++;
				});
		}

		// Change wave force value
		function change_mouse_over( to ) {
			if ( plane.tween ) {
				trx_addons_tween_stop( plane.tween );
			}
			plane.tween = trx_addons_tween_value( {
				start: plane.uniforms.mouseOver.value,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						plane.uniforms.mouseOver.value = value;
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tween );
						plane.tween = null
					}
				}
			} );
		}

		// Change scale
		function change_scale( to ) {
			if ( plane.tweenScale ) {
				trx_addons_tween_stop( plane.tweenScale );
			}
			if ( ! permanentDrawing ) {
				curtains.enableDrawing();
			}
			plane.tweenScale = trx_addons_tween_value( {
				start: scaleFactor,
				end: to,
				time: 1.25,
				callbacks: {
					onUpdate: function(value) {
						scaleFactor = value;
						if ( scaleOnHover ) {
							plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
						}
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenScale );
						plane.tweenScale = null;
						if ( ! permanentDrawing && to === 0 ) {
							curtains.disableDrawing();
						}
					}
				}
			} );
		}
	};



	// Effect 'Swap'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_swap = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		// Common vars
		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var activeImage = 0;

		// Curtains init
		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'swapTexture');
			var swap_url = elm.getAttribute('data-image-effect-swap-image');
			if ( swap_url ) {
				var swap_img = document.createElement("img");
//				swap_img.setAttribute('crossorigin', 'anonymous');
				swap_img.setAttribute('src', swap_url);
				swap_img.setAttribute('data-sampler', 'swap2Texture');
				swap_img.classList.add('trx_addons_image_effects_swap_image');
				parent.appendChild(swap_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_swap_image' ).remove();
				});
			}
			var displacement_url = elm.getAttribute('data-image-effect-displacement');
			if ( displacement_url ) {
				var displacement_img = document.createElement("img");
//				displacement_img.setAttribute('crossorigin', 'anonymous');
				displacement_img.setAttribute('src', displacement_url);
				displacement_img.setAttribute('data-sampler', 'swapDisplacement');
				displacement_img.classList.add('trx_addons_image_effects_swap_displacement');
				parent.appendChild(displacement_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_swap_displacement' ).remove();
				});
			}
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrices
				// notice how it matches our data-sampler attributes + "Matrix"
				uniform mat4 swapTextureMatrix;
				uniform mat4 swap2TextureMatrix;

				// varying variables
				varying vec3 vVertexPosition;

				// our displacement texture will use original texture coords attributes
				varying vec2 vTextureCoord;

				// our image will use texture coords based on their texture matrices
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				// custom uniforms
				uniform float uTime;

				void main() {
					vec3 vertexPosition = aVertexPosition;

					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, 1.0);

					// varying variables
					// texture coords attributes because we want our displacement texture to be contained
					vTextureCoord = aTextureCoord;
					// our image texture coords based on their texture matrices
					vSwapTextureCoord = (swapTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vSwap2TextureCoord = (swap2TextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					// vertex position as usual
					vVertexPosition = vertexPosition;
				}
			`;
			
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// all our varying variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				// custom uniforms
				uniform float uTime;

				// our textures samplers
				// notice how it matches our data-sampler attributes
				uniform sampler2D swapTexture;
				uniform sampler2D swap2Texture;
				uniform sampler2D swapDisplacement;

				void main( void ) {
					// our texture coords
					vec2 textureCoords = vTextureCoord;

					// our displacement texture
					vec4 displacementTexture = texture2D(swapDisplacement, textureCoords);

					// our displacement factor is a float varying from 1 to 0 based on the timer
					float displacementFactor = 1.0 - (cos(uTime / (60.0 / 3.141592)) + 1.0) / 2.0;

					// the effect factor will tell which way we want to displace our pixels
					// the farther from the center of the videos, the stronger it will be
					vec2 effectFactor = vec2((textureCoords.x - 0.5) * 0.75, (textureCoords.y - 0.5) * 0.75);

					// calculate our displaced coordinates of the first video
					vec2 firstDisplacementCoords = vec2(vSwapTextureCoord.x - displacementFactor * (displacementTexture.r * effectFactor.x), vSwapTextureCoord.y- displacementFactor * (displacementTexture.r * effectFactor.y));
					// opposite displacement effect on the second video
					vec2 secondDisplacementCoords = vec2(vSwap2TextureCoord.x - (1.0 - displacementFactor) * (displacementTexture.r * effectFactor.x), vSwap2TextureCoord.y - (1.0 - displacementFactor) * (displacementTexture.r * effectFactor.y));

					// apply the textures
					vec4 firstDistortedColor = texture2D(swapTexture, firstDisplacementCoords);
					vec4 secondDistortedColor = texture2D(swap2Texture, secondDisplacementCoords);

					// blend both textures based on our displacement factor
					vec4 finalColor = mix(firstDistortedColor, secondDistortedColor, displacementFactor);

					// handling premultiplied alpha
					finalColor = vec4(finalColor.rgb * finalColor.a, finalColor.a);

					// apply our shader
					gl_FragColor = finalColor;
				}
			`;

			return {
				vertexShader: vs,		// our vertex shader ID
				fragmentShader: fs,		// our fragment shader ID
//				widthSegments: 10,
//				heightSegments: 10,
				imageCover: false,		// our displacement texture has to fit the plane
				uniforms: {				// variables passed to shaders
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							activeImage = 1;
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							activeImage = 0;
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value = activeImage == 1 ? Math.min(60, plane.uniforms.time.value + 1) : Math.max(0, plane.uniforms.time.value - 1);

				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
				});

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}
	};



	// Effect 'Fish'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_fish = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		// Common vars
		var mouseIn = false;

		// track the mouse positions to send it to the shaders
		var mousePosition = {
			x: 0,
			y: 0
		};

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var activeImage = 0;

		// Curtains init
		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'swapTexture');
			var swap_url = elm.getAttribute('data-image-effect-swap-image');
			if ( swap_url ) {
				var swap_img = document.createElement("img");
//				swap_img.setAttribute('crossorigin', 'anonymous');
				swap_img.setAttribute('src', swap_url);
				swap_img.setAttribute('data-sampler', 'swap2Texture');
				swap_img.classList.add('trx_addons_image_effects_swap_image');
				parent.appendChild(swap_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_swap_image' ).remove();
				});
			}
			var displacement_url = elm.getAttribute('data-image-effect-displacement');
			if ( displacement_url ) {
				var displacement_img = document.createElement("img");
//				displacement_img.setAttribute('crossorigin', 'anonymous');
				displacement_img.setAttribute('src', displacement_url);
				displacement_img.setAttribute('data-sampler', 'swapDisplacement');
				displacement_img.classList.add('trx_addons_image_effects_swap_displacement');
				parent.appendChild(displacement_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_swap_displacement' ).remove();
				});
			}
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix;
				uniform mat4 uPMatrix;

				// our texture matrices
				// notice how it matches our data-sampler attributes + "Matrix"
				uniform mat4 swapTextureMatrix;
				uniform mat4 swap2TextureMatrix;

				// varying variables
				varying vec3 vVertexPosition;

				// our displacement texture will use original texture coords attributes
				varying vec2 vTextureCoord;

				// our image will use texture coords based on their texture matrices
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				// custom uniforms
				uniform float uTime;

				void main() {
					vec3 vertexPosition = aVertexPosition;

					gl_Position = uPMatrix * uMVMatrix * vec4(vertexPosition, 1.0);

					// varying variables
					// texture coords attributes because we want our displacement texture to be contained
					vTextureCoord = aTextureCoord;
					// our image texture coords based on their texture matrices
					vSwapTextureCoord = (swapTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vSwap2TextureCoord = (swap2TextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					// vertex position as usual
					vVertexPosition = vertexPosition;
				}
			`;
			
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// all our varying variables
				varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				// custom uniforms
				uniform float uPR;
				uniform float uScale;
				uniform float uEffect;
				uniform float uTime;

				uniform vec2 uRes;
				uniform vec2 uMousePosition;

				// our textures samplers
				// notice how it matches our data-sampler attributes
				uniform sampler2D swapTexture;
				uniform sampler2D swap2Texture;
				uniform sampler2D swapDisplacement;

				float circle( in vec2 _st, in float _radius, in float blurriness ) {
					vec2 dist = _st;
					return 1. - smoothstep( _radius - ( _radius * blurriness ), _radius + ( _radius * blurriness ), dot( dist, dist ) * 4.0 );
				}

				void main( void ) {
					// our texture coords
					vec2 textureCoords = vTextureCoord;

					//vec2 st = textureCoords - vec2(0.5);
					// Manage the device ratio
					vec2 res = uRes * uPR;
					vec2 st = gl_FragCoord.xy / res.xy - vec2(0.5);
					// tip: use the following formula to keep the good ratio of your coordinates
					st.y *= uRes.y / uRes.x;
					
					vec2 mouse = uMousePosition * -0.5;
					// tip2: do the same for your mouse
					mouse.y *= uRes.y / uRes.x;
					//mouse *= -1.;

					vec2 circlePos = st + mouse;
					float c = circle( circlePos, 0.3 * uScale, 2. );

					// our displacement texture
					vec4 displacementTexture = texture2D(swapDisplacement, textureCoords);

					// our displacement factor is a float varying from 1 to 0 based on the timer
					float displacementFactor = ( 1.0 - (cos(uTime / (60.0 / 3.141592)) + 1.0) / 2.0 * uEffect ) * c;

					// the effect factor will tell which way we want to displace our pixels
					// the farther from the center of the videos, the stronger it will be
					vec2 effectFactor = vec2( (textureCoords.x - 0.5) * 0.75 * uEffect, (textureCoords.y - 0.5) * 0.75 * uEffect );

					// calculate our displaced coordinates of the first video
					vec2 firstDisplacementCoords = vec2(vSwapTextureCoord.x - displacementFactor * (displacementTexture.r * effectFactor.x), vSwapTextureCoord.y - displacementFactor * (displacementTexture.r * effectFactor.y));
					// opposite displacement effect on the second video
					vec2 secondDisplacementCoords = vec2(vSwap2TextureCoord.x - (1.0 - displacementFactor) * (displacementTexture.r * effectFactor.x), vSwap2TextureCoord.y - (1.0 - displacementFactor) * (displacementTexture.r * effectFactor.y));

					// apply the textures
					vec4 firstDistortedColor = texture2D(swapTexture, firstDisplacementCoords);
					vec4 secondDistortedColor = texture2D(swap2Texture, secondDisplacementCoords);

					// blend both textures based on our displacement factor
					vec4 finalColor = mix(firstDistortedColor, secondDistortedColor, displacementFactor);

					// handling premultiplied alpha
					finalColor = vec4(finalColor.rgb * finalColor.a, finalColor.a);

					// apply our shader
					gl_FragColor = finalColor;
				}
			`;
			return {
				vertexShader: vs,		// our vertex shader ID
				fragmentShader: fs,		// our fragment shader ID
//				widthSegments: 10,
//				heightSegments: 10,
				imageCover: false,		// our displacement texture has to fit the plane
				uniforms: {				// variables passed to shaders
					mousePosition: { // our mouse position
						name: "uMousePosition",
						type: "2f",
						value: [ mousePosition.x, mousePosition.y ]
					},
					imageResolution: {
						name: "uRes",
						type: "2f",
						value: [ img.clientWidth, img.clientHeight ]
					},
					pixelRatio: {
						name: "uPR",
						type: "1f",
						value: window.devicePixelRatio.toFixed(1)
					},
					scaleFactor: {
						name: "uScale",
						type: "1f",
						value: 0
					},
					useEffect: {
						name: "uEffect",
						type: "1f",
						value: displacement_url != '' ? 1 : 0
					},
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
						handle_movement(e, plane);
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							activeImage = 1;
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							activeImage = 0;
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value = activeImage == 1 ? Math.min(60, plane.uniforms.time.value + 1) : Math.max(0, plane.uniforms.time.value - 1);
				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
				});

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing && to == 1 ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 1.25,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							plane.uniforms.scaleFactor.value = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing && to == 0 ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}

		// handle the mouse move event
		function handle_movement(e, plane) {

			var mouse = get_mouse_position_from_event( e );

			// lerp the mouse position a bit to smoothen the overall effect
			mousePosition.x = trx_addons_lerp(mousePosition.x, mouse.x, 0.3);
			mousePosition.y = trx_addons_lerp(mousePosition.y, mouse.y, 0.3);

			// convert our mouse/touch position to coordinates relative to the vertices of the plane
			var mouseCoords = globalCanvas
								? plane.mouseToPlaneCoords(mousePosition.x, mousePosition.y)
								: mouse_to_plane_coords( plane, mousePosition );
			

			// update our mouse position uniform
			plane.uniforms.mousePosition.value = [mouseCoords.x, mouseCoords.y];
		}
	};



	// Effect 'Blot'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_blot = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		// Common vars
		var mouseIn = false;

		// track the mouse positions to send it to the shaders
		var mousePosition = {
			x: 0,
			y: 0
		};

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0,
			tintColor = elm.getAttribute('data-image-effect-tint-color') || '';


		var effectStrength = Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30.0 ) );
		var wavesDirection = Math.max(0.0, Math.min(1.0, elm.getAttribute('data-image-effect-waves-direction') ) );

		var activeImage = 0;

		// Curtains init
		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'swapTexture');
			var swap_url = elm.getAttribute('data-image-effect-swap-image');
			if ( swap_url ) {
				var swap_img = document.createElement("img");
//				swap_img.setAttribute('crossorigin', 'anonymous');
				swap_img.setAttribute('src', swap_url);
				swap_img.setAttribute('data-sampler', 'swap2Texture');
				swap_img.classList.add('trx_addons_image_effects_swap_image');
				parent.appendChild(swap_img);
				$document.on('action.after_add_content', function(e, cont) {
					cont.find( '.trx_addons_image_effects_swap_image' ).remove();
				});
			}
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				if ( ! permanentDrawing ) curtains.disableDrawing();
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params() {
			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// default mandatory variables
				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uPMatrix;
				uniform mat4 uMVMatrix;

				// our texture matrices
				// notice how it matches our data-sampler attributes + "Matrix"
				uniform mat4 swapTextureMatrix;
				uniform mat4 swap2TextureMatrix;

				// our displacement texture will use original texture coords attributes
				varying vec2 vTextureCoord;

				// our image will use texture coords based on their texture matrices
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				void main() {
					// texture coords attributes because we want our displacement texture to be contained
					// our image texture coords based on their texture matrices
					vTextureCoord = aTextureCoord;
					vSwapTextureCoord = (swapTextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;
					vSwap2TextureCoord = (swap2TextureMatrix * vec4(aTextureCoord, 0.0, 1.0)).xy;

					gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
				}
			`;

			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// all our varying variables
				//varying vec3 vVertexPosition;
				varying vec2 vTextureCoord;
				varying vec2 vSwapTextureCoord;
				varying vec2 vSwap2TextureCoord;

				// custom uniforms
				uniform float uPR;
				uniform float uScale;
				uniform float uTint;
				uniform float uWavesTime;
				uniform float uTime;

				uniform vec2 uRes;
				uniform vec2 uMousePosition;
				uniform vec3 uColor;

				// our textures samplers
				// notice how it matches our data-sampler attributes
				uniform sampler2D swapTexture;
				uniform sampler2D swap2Texture;

				uniform float uEffectStrength;	// 5.0 - 50.0 - waves amplitude
				uniform float uWavesDirection;	// 0 - horizontal, 1 - vertical

				// noise 2d generator
				//---------------------------------------------------
				vec3 permute3(vec3 x) {
					return mod((x*34.0+1.0)*x, 289.0);
				}
				float snoise2(vec2 v) {
					const vec4 C = vec4(0.211325, 0.366025, -0.57735, 0.02439);
					vec2 i = floor(v + dot(v, C.yy)),
						 x0 = v - i + dot(i, C.xx),
						 i1;
					i1 = x0.x > x0.y ? vec2(1.0, 0.0) : vec2(0.0,1.0);
					vec4 x12 = x0.xyxy + C.xxzz;
					x12.xy -= i1,
					i = mod(i, 289.0);
					vec3 p = permute3(permute3(i.y + vec3(0.0, i1.y, 1.0)) + i.x + vec3(0.0,i1.x, 1.0)),
						 m = max(0.5 - vec3(dot(x0, x0), dot(x12.xy, x12.xy), dot(x12.zw, x12.zw)), 0.0);
					m = m * m, m = m * m;
					vec3 x = 2.0 * fract(p * C.www) - 1.0,
						 h = abs(x) - 0.5,
						 ox = floor(x + 0.5),
						 a0 = x - ox;
					m *= 1.792843 - 0.853735 * (a0 * a0 + h * h);
					vec3 g;
					g.x = a0.x * x0.x + h.x * x0.y,
					g.yz = a0.yz * x12.xz + h.yz * x12.yw;
					return 130.0 * dot(m, g);
				}

				// noise 3d generator
				//-------------------------------------------
				vec3 mod289(vec3 x) {
					return x - floor(x * (1.0 / 289.0)) * 289.0;
				}
				vec4 mod289(vec4 x) {
					return x - floor(x * (1.0 / 289.0)) * 289.0;
				}
				vec4 permute4(vec4 x) {
					return mod289(((x*34.0)+1.0)*x);
				}
				vec4 taylorInvSqrt(vec4 r) {
					return 1.79284291400159 - 0.85373472095314 * r;
				}
				float snoise3(vec3 v) {
					const vec2  C = vec2(1.0/6.0, 1.0/3.0) ;
					const vec4  D = vec4(0.0, 0.5, 1.0, 2.0);
					// First corner
					vec3 i  = floor(v + dot(v, C.yyy) );
					vec3 x0 =   v - i + dot(i, C.xxx) ;
					// Other corners
					vec3 g = step(x0.yzx, x0.xyz);
					vec3 l = 1.0 - g;
					vec3 i1 = min( g.xyz, l.zxy );
					vec3 i2 = max( g.xyz, l.zxy );

					//   x0 = x0 - 0.0 + 0.0 * C.xxx;
					//   x1 = x0 - i1  + 1.0 * C.xxx;
					//   x2 = x0 - i2  + 2.0 * C.xxx;
					//   x3 = x0 - 1.0 + 3.0 * C.xxx;
					vec3 x1 = x0 - i1 + C.xxx;
					vec3 x2 = x0 - i2 + C.yyy; // 2.0*C.x = 1/3 = C.y
					vec3 x3 = x0 - D.yyy;      // -1.0+3.0*C.x = -0.5 = -D.y

					// Permutations
					i = mod289(i); 
					vec4 p = permute4( permute4( permute4( 
					         i.z + vec4(0.0, i1.z, i2.z, 1.0 ))
					       + i.y + vec4(0.0, i1.y, i2.y, 1.0 )) 
					       + i.x + vec4(0.0, i1.x, i2.x, 1.0 ));

					// Gradients: 7x7 points over a square, mapped onto an octahedron.
					// The ring size 17*17 = 289 is close to a multiple of 49 (49*6 = 294)
					float n_ = 0.142857142857; // 1.0/7.0
					vec3  ns = n_ * D.wyz - D.xzx;

					vec4 j = p - 49.0 * floor(p * ns.z * ns.z);  //  mod(p,7*7)

					vec4 x_ = floor(j * ns.z);
					vec4 y_ = floor(j - 7.0 * x_ );    // mod(j,N)

					vec4 x = x_ *ns.x + ns.yyyy;
					vec4 y = y_ *ns.x + ns.yyyy;
					vec4 h = 1.0 - abs(x) - abs(y);

					vec4 b0 = vec4( x.xy, y.xy );
					vec4 b1 = vec4( x.zw, y.zw );

					//vec4 s0 = vec4(lessThan(b0,0.0))*2.0 - 1.0;
					//vec4 s1 = vec4(lessThan(b1,0.0))*2.0 - 1.0;
					vec4 s0 = floor(b0)*2.0 + 1.0;
					vec4 s1 = floor(b1)*2.0 + 1.0;
					vec4 sh = -step(h, vec4(0.0));

					vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy ;
					vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww ;

					vec3 p0 = vec3(a0.xy,h.x);
					vec3 p1 = vec3(a0.zw,h.y);
					vec3 p2 = vec3(a1.xy,h.z);
					vec3 p3 = vec3(a1.zw,h.w);

					//Normalise gradients
					vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2, p2), dot(p3,p3)));
					p0 *= norm.x;
					p1 *= norm.y;
					p2 *= norm.z;
					p3 *= norm.w;

					// Mix final noise value
					vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
					m = m * m;
					return 42.0 * dot( m*m, vec4( dot(p0,x0), dot(p1,x1), 
					                            dot(p2,x2), dot(p3,x3) ) );
				}

				// circle
				//-------------------------------------------
				float circle( in vec2 _st, in float _radius, in float blurriness ) {
					vec2 dist = _st;
					return 1. - smoothstep( _radius - ( _radius * blurriness ), _radius + ( _radius * blurriness ), dot( dist, dist ) * 4.0 );
				}

				void main( void ) {
					vec2 v_uv = vTextureCoord;
					vec2 v_sv = vSwapTextureCoord;

					// Manage the device ratio
					vec2 res = uRes * uPR;
					vec2 st = gl_FragCoord.xy / res.xy - vec2(0.5);
					// tip: use the following formula to keep the good ratio of your coordinates
					st.y *= uRes.y / uRes.x;
					
					vec2 mouse = uMousePosition * -0.5;
					// tip2: do the same for your mouse
					mouse.y *= uRes.y / uRes.x;
					//mouse *= -1.;

					vec2 circlePos = st + mouse;
					// float c = circle( circlePos, 0.3 * uScale, 2.0 ) * 2.5;
					float c = circle( circlePos, 0.3 * uScale, 2.5 ) * 2.25;

					float offx = v_uv.x + sin( v_uv.y + uTime * 0.1 );
					float offy = v_uv.y - uTime * 0.1 - cos( uTime * 0.001 ) * 0.01;
					float n = snoise3( vec3( offx, offy, uTime * 0.1 ) * 8.0 ) - 1.0;

					float finalMask = smoothstep( 0.4, 0.5, n + pow( c, 2.0 ) );

					// Add waves to the first image
					const float PI = 3.141592;

					v_sv.x += (
								sin(v_sv.x * 10.0 * ( 2.0 - uWavesDirection ) + ((uWavesTime * (PI / 3.0)) * 0.031))
								+ sin(v_sv.y * 10.0 * ( 2.0 - uWavesDirection ) + ((uWavesTime * (PI / 2.489)) * 0.017))
								) / uEffectStrength / ( 2.5 + 1.5 * uWavesDirection ) * uScale;	// * 0.0075;

					v_sv.y += (
								sin(v_sv.y * 20.0 / ( 2.0 - uWavesDirection ) + ((uWavesTime * (PI / 2.023)) * 0.023))
								+ sin(v_sv.x * 20.0 / ( 2.0 - uWavesDirection ) + ((uWavesTime * (PI / 3.1254)) * 0.037))
								) / uEffectStrength / ( 2.5 + 1.5 * ( 1.0 - uWavesDirection ) ) * uScale;	// * 0.0125;

					vec4 imageColor = texture2D( swapTexture, v_sv );

					// Add tint the second image
					vec4 hoverColor = texture2D( swap2Texture, vSwap2TextureCoord );
					vec4 tint = vec4( uColor.r / 255.0, uColor.g / 255.0, uColor.b / 255.0, 1.0 );
					hoverColor = uTint > 0.0 ? mix( hoverColor, tint, 0.5) : hoverColor;

					gl_FragColor = mix( imageColor, hoverColor, finalMask );
				}
			`;

			return {
				vertexShader: vs,		// our vertex shader ID
				fragmentShader: fs,		// our fragment shader ID
//				widthSegments: 10,
//				heightSegments: 10,
				imageCover: false,		// our displacement texture has to fit the plane
				uniforms: {				// variables passed to shaders
					mousePosition: { // our mouse position
						name: "uMousePosition",
						type: "2f",
						value: [ mousePosition.x, mousePosition.y ]
					},
					imageResolution: {
						name: "uRes",
						type: "2f",
						value: [ img.clientWidth, img.clientHeight ]
					},
					pixelRatio: {
						name: "uPR",
						type: "1f",
						value: window.devicePixelRatio.toFixed(1)
					},
					scaleFactor: {
						name: "uScale",
						type: "1f",
						value: 0
					},
					color: {
						name: "uColor",
						type: "3f",
						value: tintColor ? trx_addons_rgb2components( tintColor ) : [0, 0, 0]
					},
					useColor: {
						name: "uTint",
						type: "1f",
						value: tintColor ? 1 : 0
					},
					effectStrength: {
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					wavesDirection: {
						name: "uWavesDirection",
						type: "1f",
						value: wavesDirection
					},
					wavesTime: {
						name: "uWavesTime",
						type: "1f",
						value: 0
					},
					time: {
						name: "uTime",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onLoading(function() {
//					console.log(plane.loadingManager.sourcesLoaded);
				})
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// first render plane
					if ( ! permanentDrawing ) curtains.needRender();
					// init tweens
					plane.tweenScale = null;
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
						handle_movement(e, plane);
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							activeImage = 1;
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							activeImage = 0;
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					plane.uniforms.time.value += 0.01;
					plane.uniforms.wavesTime.value++;
				})
				.onAfterResize(function() {
//					console.log('Plane '+index+' of '+total+' is resized');
				});

			// Change scale
			function change_scale( to ) {
				if ( plane.tweenScale ) {
					trx_addons_tween_stop( plane.tweenScale );
				}
				if ( ! permanentDrawing && to == 1 ) {
					curtains.enableDrawing();
				}
				plane.tweenScale = trx_addons_tween_value( {
					start: scaleFactor,
					end: to,
					time: 0.6,
					callbacks: {
						onUpdate: function(value) {
							scaleFactor = value;
							plane.uniforms.scaleFactor.value = value;
							if ( scaleOnHover ) {
								plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
							}
						},
						onComplete: function() {
							trx_addons_tween_stop( plane.tweenScale );
							plane.tweenScale = null;
							if ( ! permanentDrawing && to == 0 ) {
								curtains.disableDrawing();
							}
						}
					}
				} );
			}
		}

		// handle the mouse move event
		function handle_movement(e, plane) {

			var mouse = get_mouse_position_from_event( e );

			// lerp the mouse position a bit to smoothen the overall effect
			mousePosition.x = trx_addons_lerp(mousePosition.x, mouse.x, 0.3);
			mousePosition.y = trx_addons_lerp(mousePosition.y, mouse.y, 0.3);

			// convert our mouse/touch position to coordinates relative to the vertices of the plane
			var mouseCoords = globalCanvas
								? plane.mouseToPlaneCoords(mousePosition.x, mousePosition.y)
								: mouse_to_plane_coords( plane, mousePosition );

			// update our mouse position uniform
			plane.uniforms.mousePosition.value = [mouseCoords.x, mouseCoords.y];
		}
	};



	// Effect 'Scroller'
	//-------------------------------------------
	
	// Create plane.
	// Attention! All callbacks must be in a global scope!
	window.trx_addons_image_effects_callback_scroller = function( curtains_global, elm, index, total ) {

		var curtains = curtains_global ? curtains_global : null;

		var mouseIn = false;

		var scaleOnHover = elm.getAttribute('data-image-effect-scale') > 0,
			scaleFactor = 0;

		var scrollDirection = 0,
			scrollY = -1;

		var effectStrength = 60 - Math.max( 5.0, Math.min( 50.0, elm.getAttribute('data-image-effect-strength') || 30 ) ),
			effectDirection = elm.getAttribute('data-image-effect-waves-direction') || 0;

		var plane = null,
			parent = null,
			img_all = elm.querySelectorAll( 'img:not([class*="trx_addons_image_effects_"])' ),
			img = img_all.length > 1
					? elm.querySelector( 'img:not([class*="avatar"]):not([class*="trx_addons_extended_taxonomy_img"])' )
					: elm.querySelector( 'img' );

		if ( img ) {
			if ( img_all.length > 1 ) {
				jQuery( img ).wrap( '<div class="trx_addons_image_effects_holder"></div>' );
			}
			parent = img.parentNode;
			if ( img_all.length == 1 ) {
				parent.classList.add('trx_addons_image_effects_holder');
			}
//			img.setAttribute('crossorigin', 'anonymous');
			img.setAttribute('data-sampler', 'scrollerTexture');
			if ( ! globalCanvas ) {
				curtains = create_canvas(parent);
				if ( curtains ) jQuery(elm).data('curtains', curtains);
			}
			if ( curtains ) {
				plane = curtains.addPlane( parent, get_params( elm, img, parent ) );
				if ( plane ) {
					jQuery(elm).data('curtains-plane', plane);
					handle_plane( plane );
				}
			}
		}

		// Return plane params
		function get_params( elm, img, parent ) {

			var vs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				attribute vec3 aVertexPosition;
				attribute vec2 aTextureCoord;

				uniform mat4 uMVMatrix, uPMatrix;

				uniform float uScrollSpeed;
				uniform float uEffectStrength;
				uniform float uEffectDirection;
				uniform float uPadding;

				// our texture matrices
				// notice how it matches our data-sampler attributes + "Matrix"
				uniform mat4 scrollerTextureMatrix;

				// if you want to pass your vertex and texture coords to the fragment shader
				varying vec3 vVertexPosition;
				varying vec2 vScrollerTextureCoord;

				void main(){
					vec3 vertexPosition = aVertexPosition;
					if ( uEffectDirection > 0.0 ) {
						// Vertical wave
						vertexPosition.x += sin( ( vertexPosition.y + 1.0 ) * 3.141592 ) * sin( uScrollSpeed * 3.141592 ) * 0.05 * ( uEffectStrength / 30.0 );
					} else {
						// Horizontal wave
						vertexPosition.y += sin( ( vertexPosition.x + 1.0 ) / 2.0 * 3.141592 ) * sin( uScrollSpeed * 3.141592 ) * 0.05 * ( uEffectStrength / 30.0 );
					}
					vVertexPosition = vertexPosition;
					gl_Position = uPMatrix * uMVMatrix * vec4( vertexPosition, 1.0 + uPadding * ( uEffectStrength / 30.0 ) );

					vScrollerTextureCoord = ( scrollerTextureMatrix * vec4( aTextureCoord, 0.0, 1.0 ) ).xy;
				}
			`;
			var fs = `
				#ifdef GL_ES
					precision mediump float;
				#endif

				// all our varying variables
				varying vec2 vScrollerTextureCoord;

				uniform sampler2D scrollerTexture;

				void main(){
					vec2 textureCoord = vScrollerTextureCoord;
					gl_FragColor = texture2D(scrollerTexture, textureCoord);
				}
			`;

			return {
				vertexShader: vs,
				fragmentShader: fs,
				widthSegments: 10,
				heightSegments: 10,
				uniforms: {
					padding: {
						name: "uPadding",
						type: "1f",
						value: trx_addons_apply_filters( 'trx_addons_filter_image_effects_padding', 0.05, 'scroller' )
					},
					effectStrength: {
						name: "uEffectStrength",
						type: "1f",
						value: effectStrength
					},
					effectDirection: {
						name: "uEffectDirection",
						type: "1f",
						value: effectDirection
					},
					scrollSpeed: {
						name: "uScrollSpeed",
						type: "1f",
						value: 0
					}
				}
			};
		}

		// handle plane
		function handle_plane( plane ) {
			plane
				.onReady(function() {
//					console.log('Plane '+index+' of '+total+' is ready');
					// init tweens
					plane.tween = null;
					plane.tweenScale = null;
					var scrollBusy = false,
						clear_scroll_busy = trx_addons_throttle( function() {
							scrollBusy = false;
						}, 300, true );

					function scroll_get() {
						return ( typeof window.scrollY != 'undefined' ? window.scrollY : window.pageYOffset ) || 0;
					}
					// now that our plane is ready we can listen to mouse move event
					function scroll_start(e) {
						clear_scroll_busy();
						if ( scrollY < 0 ) {
							scrollY = scroll_get();
						} else if ( ! scrollDirection ) {
							var scrollNew = scroll_get();
							scrollDirection = scrollNew > scrollY ? 1 : -1;
							scrollBusy = true;
							change_scroll( 1 );
						}
					}
					window.addEventListener("scroll",  scroll_start );
					// now that our plane is ready we can listen to mouse move event
					function mouse_move(e) {
						if ( ! mouseIn ) {
							mouse_enter();
						}
					}
					function mouse_enter() {
						if ( ! mouseIn ) {
							mouseIn = true;
							change_scale( 1 );
						}
					}
					function mouse_leave() {
						if ( mouseIn ) {
							mouseIn = false;
							change_scale( 0 );
						}
					}
					elm.addEventListener("mousemove",  mouse_move );
					elm.addEventListener("touchmove",  mouse_move );
					elm.addEventListener("mouseenter", mouse_enter );
					elm.addEventListener("touchstart", mouse_enter );
					elm.addEventListener("mouseleave", mouse_leave );
					elm.addEventListener("touchend",   mouse_leave );
				})
				.onRender(function() {
					//plane.uniforms.time.value++;
				});
		}

		// Change wave force value
		function change_scroll( to ) {
			if ( plane.tween ) {
				trx_addons_tween_stop( plane.tween );
			}
			plane.tween = trx_addons_tween_value( {
				start: plane.uniforms.scrollSpeed.value,
				end: to,
				time: 1.5,
				callbacks: {
					onUpdate: function(value) {
						plane.uniforms.scrollSpeed.value = value * scrollDirection;
					},
					onComplete: function() {
						scrollDirection = 0;
						scrollY = -1;
						plane.uniforms.scrollSpeed.value = 0;
						trx_addons_tween_stop( plane.tween );
						plane.tween = null;
					}
				}
			} );
		}

		// Change scale
		function change_scale( to ) {
			if ( plane.tweenScale ) {
				trx_addons_tween_stop( plane.tweenScale );
			}
			if ( ! permanentDrawing && to == 1 ) {
				curtains.enableDrawing();
			}
			plane.tweenScale = trx_addons_tween_value( {
				start: scaleFactor,
				end: to,
				time: 1.0,
				callbacks: {
					onUpdate: function(value) {
						scaleFactor = value;
						if ( scaleOnHover ) {
							plane.textures && plane.textures[0].setScale(1 + value / 12, 1 + value / 12);
						}
					},
					onComplete: function() {
						trx_addons_tween_stop( plane.tweenScale );
						plane.tweenScale = null;
					}
				}
			} );
		}
	};

})();