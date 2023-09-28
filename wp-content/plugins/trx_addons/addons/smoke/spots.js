/* global jQuery */

(function() {

	"use strict";

	var requestAnimationFrame = trx_addons_request_animation_frame();

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$smokes   = jQuery( '.trx_addons_smoke_type_spots' );

	if ( $smokes.length ) {
		$smokes.each( function() {
			var $self = jQuery( this ),
				$container;
			if ( $self.hasClass( 'trx_addons_smoke_place_body' ) ) {
				jQuery( 'body' ).addClass( 'trx_addons_smoke_present' );
				$self.data( 'smoke-container', $window );
			} else if ( $self.hasClass( 'trx_addons_smoke_place_section' ) ) {
				$container = $self.closest( '.elementor-section' ).addClass( 'trx_addons_smoke_present' );
				$self
					.prependTo( $container )
					.data( 'smoke-container', $container );
			} else if ( $self.hasClass( 'trx_addons_smoke_place_column' ) ) {
				$container = $self.closest( '.elementor-column' ).addClass( 'trx_addons_smoke_present' );
				$self.closest( '.elementor-section' ).addClass( 'trx_addons_smoke_present_in_column' );
				$self
					.prependTo( $container )
					.data( 'smoke-container', $container );
			}
		} );
	}


	// Init effects
	//-------------------------------------------------
	$document.on('action.init_trx_addons', function() {

		if ( $smokes.length === 0 || ! requestAnimationFrame ) return;

		$smokes.each( function() {
			init_spots( jQuery( this ) );
		} );

	} );


	// Effect: Spots
	//-------------------------------------------
	function Spots( scene ) {
		this.meshes = [];
		this.args = null;
		this.scene = scene;
		this.itemSize = 400;
		this.baseWidth = 1920;
		this.baseHeight = 1080;
	}

	Spots.prototype.createMeshes = function( args ) {
		// Define Material
		var utils = `
			#ifdef GL_ES
				precision mediump float;
			#endif
			// noise 2d generator
			//---------------------------------------------------
			vec3 permute(vec3 x) {
				return mod(((x*34.0)+1.0)*x, 289.0);
			}
			float snoise2(vec2 v){
				const vec4 C = vec4(0.211324865405187, 0.366025403784439, -0.577350269189626, 0.024390243902439);
				vec2 i  = floor(v + dot(v, C.yy));
				vec2 x0 = v - i + dot(i, C.xx);
				vec2 i1;
				i1 = (x0.x > x0.y) ? vec2(1.0, 0.0) : vec2(0.0, 1.0);
				vec4 x12 = x0.xyxy + C.xxzz;
				x12.xy -= i1;
				i = mod(i, 289.0);
				vec3 p = permute( permute( i.y + vec3(0.0, i1.y, 1.0 )) + i.x + vec3(0.0, i1.x, 1.0 ));
				vec3 m = max(0.5 - vec3(dot(x0,x0), dot(x12.xy,x12.xy), dot(x12.zw,x12.zw)), 0.0);
				m = m*m;
				m = m*m;
				vec3 x = 2.0 * fract(p * C.www) - 1.0;
				vec3 h = abs(x) - 0.5;
				vec3 ox = floor(x + 0.5);
				vec3 a0 = x - ox;
				m *= 1.79284291400159 - 0.85373472095314 * ( a0*a0 + h*h );
				vec3 g;
				g.x = a0.x * x0.x + h.x * x0.y;
				g.yz = a0.yz * x12.xz + h.yz * x12.yw;
				return 130.0 * dot(m, g);
			}
		`;
		var vs = `
			attribute vec3 position;
			attribute vec2 uv;
			uniform mat4 projectionMatrix;
			uniform mat4 modelViewMatrix;
			uniform float u_time;
			uniform float u_motion;
			uniform float u_shape;
			uniform float u_rotation;
			varying vec2 vUv;
			varying vec3 pos;
			
			void main() {
				if ( u_rotation == 0.0 ) {
					vUv = uv;
				} else {
					float rx = sin(u_time * u_rotation), ry = cos(u_time * u_rotation);
					float cx = 0.5, cy = 0.5;
					vUv = vec2( cx + ( uv.x - cx ) * ry - ( uv.y - cy ) * rx,
								cy + ( uv.x - cx ) * rx + ( uv.y - cy ) * ry );
				}
				pos = position;
				gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
			}
		`;
		var fs = `
			uniform vec3 u_bg;
			uniform vec3 u_color1;
			uniform vec3 u_color2;
			uniform float u_time;
			uniform float u_motion;
			uniform float u_shape;
			uniform float u_mix;

			varying vec2 vUv;

			void main() {
				float shape = u_shape + sin(u_time * 0.2) * 0.5;
				float noise = snoise2(vUv * shape + sin(u_time * 0.5) * 0.1); // * u_motion
				float colorMix = smoothstep(1.0, 2.0, vUv.x);
				float d1 = distance(vUv, vec2(0.5, 0.5)) * 2.0;
				float d2 = distance(vUv, vec2(0.5, 0.5)) * ( 3.5 + shape );
				float alpha = 1.0 - smoothstep(0.0, 1.0, d1 * 1.1);
				vec3 color1, color2, color;
				if ( u_mix > 0.0 ) {
					// Make a transition between colors with a time dependency
					color = mix(u_color1, u_color2, abs(sin(u_time * 0.1 * (u_motion + 1.0))));
					color1 = mix(color, u_bg, d1);
					color2 = mix(color, u_bg, d2);
				} else {
					// Original case - use two colors together: color1 in the center of the spot
					//                 and color2 near the edge
					color1 = mix(u_color1, u_bg, d1);
					color2 = mix(u_color2, u_bg, d2);
				}
				// Add a shape with noise
				color = mix(color1, color2, colorMix + noise / 2.5 );
				// Make a transition between bg and color with a time dependency
				float base = ( u_motion > 1.0 ? 0.2 : 0.4 );
				float freq = ( u_motion > 1.0 ? 0.25 : 0.15 );
				//color = mix(u_bg, color, min( 1.0, base + abs(sin(u_time * freq))));
				alpha = alpha * min( 1.0, base + abs(sin(u_time * freq)) * shape * min( 1.0, 0.5 + u_motion ) );
				// Add opacity and make a result color
				gl_FragColor = vec4(color, alpha);
			}
		`;
		// Save args
		this.args = args;
		// Prepare geometry
		var baseGeometry = new THREE.PlaneBufferGeometry( this.itemSize, this.itemSize, 1, 1 ),
			material, color_rgb;
		// Create meshes
		for ( var i = 0; i < args['spots'].length; i++ ) {
			material = new THREE.RawShaderMaterial( {
				uniforms: {
					u_bg: {
						type: "v3",
						value: this.rgb( args['bg_color'] || '#FFFFFF' )
					},
					u_color1: {
						type: "v3",
						value: this.rgb( args['spots'][i]['color_1'] || '#434CFF' )
					},
					u_color2: {
						type: "v3",
						value: this.rgb( args['spots'][i]['color_2'] || '#434CFF' )
					},
					u_mix: {
						type: "f",
						value: parseFloat( args['spots'][i]['mix'] || 0.0 )
					},
					u_time: {
						type: "f",
						value: 0
					},
					u_motion: {
						type: "f",
						value: parseFloat( args['spots'][i]['motion'] )
					},
					u_shape: {
						type: "f",
						value: parseFloat( args['spots'][i]['shape'] )
					},
					u_rotation: {
						type: "f",
						value: parseFloat( args['spots'][i]['rotation'] )
					}
				},
				vertexShader: utils + vs,
				fragmentShader: utils + fs,
				blending: THREE.NormalBlending,
				transparent: true,
				depthTest: true
			} );

			this.meshes.push( new THREE.Mesh( baseGeometry, material ) );
			this.meshes[i].position.set( window.innerWidth * ( args['spots'][i]['pos_x'] / 100 - 0.5 ), window.innerHeight * ( args['spots'][i]['pos_y'] / 100 - 0.5 ), 0);
			this.meshes[i].scale.multiplyScalar( parseFloat( args['spots'][i]['scale'] ) * this.getScaleFactor() );
			this.meshes[i].rotationX = -1;
			this.meshes[i].rotationY = 0;
			this.meshes[i].rotationZ = 0;
			this.scene.add( this.meshes[i] );
//			this.motion(i);
		}
	};

	Spots.prototype.getScaleFactor = function() {
		return Math.sqrt( window.innerWidth * window.innerWidth + window.innerHeight * window.innerHeight )
				/ 
				Math.sqrt( this.baseWidth * this.baseWidth + this.baseHeight * this.baseHeight );
	};

	Spots.prototype.motion = function( i ) {
		if ( this.args['spots'][i]['motion'] > 0 ) {
			if ( this.args['spots'][i]['tween'] ) {
				this.args['spots'][i]['tween'].kill();
				this.args['spots'][i]['tween'] = null;
			}
			var pos_x = window.innerWidth * ( this.args['spots'][i]['pos_x'] / 100 - 0.5 ),
				pos_y = window.innerHeight * ( this.args['spots'][i]['pos_y'] / 100 - 0.5 ),
				dx = window.innerWidth / 20,
				dy = window.innerHeight / 10,
				k = this.getScaleFactor();
			this.args['spots'][i]['tween'] = TweenMax.to( this.meshes[i].position, {
				x: this.args['spots'][i]['motion'] > 1
					? "random(-" + ( window.innerWidth - this.itemSize * k ) / 2 + "," + ( window.innerWidth - this.itemSize * k ) / 2 + ",10)"
					: "random(" + ( pos_x - dx ) + "," + ( pos_x + dx ) + ",10)",
				y: this.args['spots'][i]['motion'] > 1
					? "random(-" + ( window.innerHeight - this.itemSize * k ) / 2 + "," + ( window.innerHeight - this.itemSize * k ) / 2 + ",10)"
					: "random(" + ( pos_y - dy ) + "," + ( pos_y + dy ) + ",10)",
				duration: 4,
				//yoyo: true,
				overwrite: true,
				repeat: -1,
				repeatRefresh: true,
				ease: "power1.inOut"
			} );
		}
	};

	Spots.prototype.rgb = function( r, g, b ) {
		if ( typeof r == 'string' && ! g && ! b ) {
			var rgb = trx_addons_hex2rgb( r );
			r = rgb.r;
			g = rgb.g;
			b = rgb.b;
		}
		return new THREE.Color( r / 255, g / 255, b / 255 )
	};

	Spots.prototype.render = function( time ) {
		for( var i = 0; i < this.meshes.length; i++ ) {
			this.meshes[i].material.uniforms.u_time.value = time;
		}
	};

	// Init effect
	function init_spots( $smoke ) {

		var args = $smoke.data('trx-addons-smoke');
		var $smoke_container = $smoke.data('smoke-container');
		var smoke_global = args['place'] == 'body';
		var smoke_offset = $smoke.offset();

		if ( args['place_class'] && args['place'] != 'body' ) {
			$smoke_container.addClass( args['place_class'] );
		}

		var resolution = new THREE.Vector2();
		var bg_color = args['bg_color'] || '#FFFFFF';
		var bg_rgb = trx_addons_hex2rgb( bg_color );

		var canvas = $smoke.get(0);	//document.getElementById('canvas-webgl');

		var renderer = new THREE.WebGLRenderer( {
			alpha: true,
			antialias: true,
			canvas: canvas
		} );

		var clock = new THREE.Clock();

		var scene = null,
			spots = null,
			camera = null;

		scene = new THREE.Scene();
		scene.background = new THREE.Color( bg_rgb.r / 255, bg_rgb.g / 255, bg_rgb.b / 255 );

		resolution.set( $smoke_container.width(), $smoke_container.height() );

		camera = new THREE.OrthographicCamera( resolution.x / -2, resolution.x / 2, resolution.y / 2, resolution.y / -2, 1, 2000 );

		spots = new Spots( scene );
		spots.createMeshes( args );

		if ( args['place'] == 'body' ) {
			window.trx_addons_smoke_set_bg_color = function( bg_color ) {
				if ( bg_color ) {
					var bg_rgb = trx_addons_hex2rgb( bg_color );
					scene.background = new THREE.Color( bg_rgb.r / 255, bg_rgb.g / 255, bg_rgb.b / 255 );
				}
			};
		}

		function render() {
			var time = clock.getElapsedTime();
			spots.render( time );
			renderer.render( scene, camera );
		}

		function renderLoop() {
			render();
			requestAnimationFrame( renderLoop );
		}

		function resizeCamera() {
			camera.left = resolution.x / -2;
			camera.right = resolution.x / 2;
			camera.top = resolution.y / 2;
			camera.bottom = resolution.y / -2;
			camera.updateProjectionMatrix();
		}

		function resizeSpots() {
			for( var i = 0; i < spots.meshes.length; i++ ) {
				spots.meshes[i].scale.x = spots.meshes[i].scale.y = parseFloat( args['spots'][i]['scale'] ) * spots.getScaleFactor();
				spots.motion(i);
			}
		}

		function resizeWindow() {
			resolution.set( $smoke_container.width(), $smoke_container.height() );
			canvas.width = resolution.x;
			canvas.height = resolution.y;
			resizeCamera();
			renderer.setSize( resolution.x, resolution.y );
			resizeSpots();
		}

		function addListeners() {
			window.addEventListener('resize', trx_addons_debounce( resizeWindow, 300 ) );
		}

		( function() {

//			renderer.setClearColor( 0xffffff, 1.0 );
			camera.position.set( 0, 0, 1000 );
			camera.lookAt( new THREE.Vector3() );

			clock.start();

			addListeners();
			resizeWindow();

			renderLoop();

		} )();

	}

})();