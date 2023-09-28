/* global jQuery */

(function() {

	"use strict";

	var requestAnimationFrame = trx_addons_request_animation_frame();

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$smokes   = jQuery( '.trx_addons_smoke_type_smoke' );

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
			init_smoke( jQuery( this ) );
		} );

	} );


	// Effect: Smoke
	//-------------------------------------------------

	// Init effect
	function init_smoke( $smoke ) {
		var args = $smoke.data('trx-addons-smoke'),
			$smoke_container = $smoke.data('smoke-container'),
			smoke_offset = $smoke.offset(),
			config = {
				image: args['image'] || '',
				container: $smoke_container,
				global: args['place'] == 'body',
				viewport: {
					mobiledevice: trx_addons_browser_is_mobile(),
					size:         [ $smoke_container.width(), $smoke_container.height() ],
					offset:       [
									args['place'] == 'body' ? 0 : smoke_offset.left,
									args['place'] == 'body' ? 0 : smoke_offset.top
								  ]
				}
			};
		if ( args['place_class'] && args['place'] != 'body' ) {
			$smoke_container.addClass( args['place_class'] );
		}
		if ( config.global ) {
			window.trx_addons_smoke_set_bg_color = setBgColor;
			window.trx_addons_smoke_set_tint_color = setTintColor;
		}
		$document.on( 'action.resize_trx_addons', function() {
			config.viewport.size = [ config.container.width(), config.container.height() ];
			var smoke_offset = $smoke.offset();
			config.viewport.offset = [
										config.global ? 0 : smoke_offset.left,
										config.global ? 0 : smoke_offset.top
									];
		} );

		var SETTINGS = {
			SYM_SIZE: 128,
			DYE_SIZE: 512,
			CURL: 5,
			DENSITY_DIFFUSION: 0.97,
			VELOCITY_DIFFUSION: 0.98,
			PRESSURE_DIFFUSION: 0.8,
			PRESSURE_ITERATIONS: 10,
			SLAP_RADIUS: 0.6,
			SHADING: true,
			COLORFUL: true,
			TINT_COLOR: { r: 228, g: 228, b: 228 },
			BACK_COLOR: { r: 0, g: 0, b: 0 },
			TRANSPARENT: false,
			FLASH: false,
			FLASH_ITERATIONS: 8,
			FLASH_SIZE: 256,
			FLASH_INTENSITY: 0.8,
			FLASH_LIMIT: 0.6,
			FLASH_CURVE: 0.7
		};

		if ( args['smoke_curls'] ) SETTINGS.CURL = args['smoke_curls'];
		if ( args['smoke_density'] ) SETTINGS.DENSITY_DIFFUSION = args['smoke_density'];
		if ( args['smoke_velosity'] ) SETTINGS.VELOSITY_DIFFUSION = args['smoke_velosity'];
		if ( args['smoke_pressure'] ) SETTINGS.PRESSURE_DIFFUSION = args['smoke_pressure'];
		if ( args['smoke_iterations'] ) SETTINGS.PRESSURE_ITERATIONS = args['smoke_iterations'];
		if ( args['smoke_slap'] ) SETTINGS.SLAP_RADIUS = args['smoke_slap'];
		if ( args['use_image'] ) SETTINGS.FLASH = args['use_image'];
		if ( args['bg_color'] ) setBgColor( args['bg_color'] );
		if ( args['tint_color'] ) setTintColor( args['tint_color'] );

		function setBgColor( bg_color ) {
			if ( bg_color ) {
				var bg = trx_addons_hex2rgb( bg_color );
				SETTINGS.BACK_COLOR = { r: bg.r, g: bg.g, b: bg.b };
			}
		}

		function setTintColor( tint_color ) {
			if ( tint_color ) {
				var bg = trx_addons_hex2rgb( tint_color );
				SETTINGS.TINT_COLOR = { r: bg.r, g: bg.g, b: bg.b };
				SETTINGS.COLORFUL = false;
			}
		}

		function addProps( obj, props ) {
			for( var n = 0; n < props.length; n++ ) {
				var prop = props[n];
				prop.enumerable = prop.enumerable || false;
				prop.configurable = true;
				"value" in prop && ( prop.writable = true );
				Object.defineProperty( obj, prop.key, prop );
			}
		}

		function prepareCanvas( canvas_dom ) {
			canvas_dom.width = canvas_dom.clientWidth / 2;
			canvas_dom.height = canvas_dom.clientHeight / 2;

			function state() {
				this.id = -1;
				this.x = 0;
				this.y = 0;
				this.dx = 0;
				this.dy = 0;
				this.down = false;
				this.moved = false;
				this.color = { r: SETTINGS.TINT_COLOR.r / 255, g: SETTINGS.TINT_COLOR.g / 255, b: SETTINGS.TINT_COLOR.b / 255 };
			}

			var states = [],
				impulses = [],
				flashes = [];

			states.push( new state );

			var canvas_obj = function(cnv_dom ) {
					var oes_half, oes_linear,
						gl_options = {
							alpha: true,
							depth: false,
							stencil: false,
							antialias: false,
							preserveDrawingBuffer: false
						},
						gl_context = cnv_dom.getContext("webgl2", gl_options),
						gl_available = !!gl_context;
					gl_available
					|| ( gl_context = cnv_dom.getContext("webgl", gl_options)
							|| cnv_dom.getContext("experimental-webgl", gl_options)
					);
					gl_available
						? ( gl_context.getExtension("EXT_color_buffer_float"), oes_linear = gl_context.getExtension("OES_texture_float_linear") )
						: ( oes_half = gl_context.getExtension("OES_texture_half_float"), oes_linear = gl_context.getExtension("OES_texture_half_float_linear") );
					gl_context.clearColor(0, 0, 0, 1);
					var rgba, rg, r, half_float = gl_available ? gl_context.HALF_FLOAT : oes_half.HALF_FLOAT_OES;
					gl_available
						? ( rgba = getTextureFormat(gl_context, gl_context.RGBA16F, gl_context.RGBA, half_float), rg = getTextureFormat(gl_context, gl_context.RG16F, gl_context.RG, half_float),  r = getTextureFormat(gl_context, gl_context.R16F, gl_context.RED, half_float) )
						: ( rgba = getTextureFormat(gl_context, gl_context.RGBA, gl_context.RGBA, half_float),    rg = getTextureFormat(gl_context, gl_context.RGBA, gl_context.RGBA, half_float), r = getTextureFormat(gl_context, gl_context.RGBA, gl_context.RGBA, half_float) );
					return {
						gl: gl_context,
						ext: {
							formatR: r,
							formatRG: rg,
							formatRGBA: rgba,
							halfFloatTexType: half_float,
							supportLinearFiltering: oes_linear
						}
					};
				}( canvas_dom );
			var canvas_gl = canvas_obj.gl,
				canvas_ext = canvas_obj.ext;

			function getTextureFormat( gl_context, rgba_float, rgba, half_float ) {
				if ( ! function( gl_context, rgba_float, rgba, half_float ) {
						var tex = gl_context.createTexture();
						gl_context.bindTexture(gl_context.TEXTURE_2D, tex);
						gl_context.texParameteri(gl_context.TEXTURE_2D, gl_context.TEXTURE_MIN_FILTER, gl_context.NEAREST);
						gl_context.texParameteri(gl_context.TEXTURE_2D, gl_context.TEXTURE_MAG_FILTER, gl_context.NEAREST);
						gl_context.texParameteri(gl_context.TEXTURE_2D, gl_context.TEXTURE_WRAP_S, gl_context.CLAMP_TO_EDGE);
						gl_context.texParameteri(gl_context.TEXTURE_2D, gl_context.TEXTURE_WRAP_T, gl_context.CLAMP_TO_EDGE);
						gl_context.texImage2D(gl_context.TEXTURE_2D, 0, rgba_float, 4, 4, 0, rgba, half_float, null);
						var buff = gl_context.createFramebuffer();
						gl_context.bindFramebuffer(gl_context.FRAMEBUFFER, buff);
						gl_context.framebufferTexture2D(gl_context.FRAMEBUFFER, gl_context.COLOR_ATTACHMENT0, gl_context.TEXTURE_2D, tex, 0);
						if ( gl_context.checkFramebufferStatus(gl_context.FRAMEBUFFER) !== gl_context.FRAMEBUFFER_COMPLETE ) return 0;
						return 1;
					} ( gl_context, rgba_float, rgba, half_float )
				) {
					switch( rgba_float ) {
						case gl_context.R16F:
							return getTextureFormat( gl_context, gl_context.RG16F, gl_context.RG, half_float );
						case gl_context.RG16F:
							return getTextureFormat( gl_context, gl_context.RGBA16F, gl_context.RGBA, half_float );
						default:
							return null;
					}
				}
				return {
					internalFormat: rgba_float,
					format: rgba
				};
			}

			if ( config.viewport.mobiledevice ) {
				SETTINGS.SHADING = false;
			}
			if ( ! canvas_ext.supportLinearFiltering ) {
				SETTINGS.SHADING = false;
				SETTINGS.FLASH = false;
			}

			var createCanvasProgramm = function() {
				function createProgramm(vertex_shader, fragment_shader) {
					( function(obj, className) {
						if ( ! (obj instanceof className) ) {
							throw new TypeError("Cannot call a class as a function");
						}
					} )(this, createProgramm);
					this.uniforms = {};
					this.program = canvas_gl.createProgram();
					canvas_gl.attachShader(this.program, vertex_shader);
					canvas_gl.attachShader(this.program, fragment_shader);
					canvas_gl.linkProgram(this.program);
					if ( ! canvas_gl.getProgramParameter(this.program, canvas_gl.LINK_STATUS) ) {
						throw canvas_gl.getProgramInfoLog(this.program);
					}
					for (var r = canvas_gl.getProgramParameter(this.program, canvas_gl.ACTIVE_UNIFORMS), i = 0; i < r; i++ ) {
						var uni_name = canvas_gl.getActiveUniform(this.program, i).name;
						this.uniforms[uni_name] = canvas_gl.getUniformLocation(this.program, uni_name);
					}
				}
				var programm, n, r;
				programm = createProgramm;
				addProps( programm.prototype, [ {
						key: "bind",
						value: function() {
							canvas_gl.useProgram(this.program);
						}
					} ]
				);
				r && addProps( programm, r );
				return createProgramm;
			}();

			function addShader( type, src ) {
				var shader = canvas_gl.createShader( type );
				canvas_gl.shaderSource( shader, src );
				canvas_gl.compileShader( shader );
				if ( ! canvas_gl.getShaderParameter( shader, canvas_gl.COMPILE_STATUS ) ) {
					throw canvas_gl.getShaderInfoLog( shader );
				}
				return shader;
			}

			canvas_gl.bindBuffer( canvas_gl.ARRAY_BUFFER, canvas_gl.createBuffer() );
			canvas_gl.bufferData( canvas_gl.ARRAY_BUFFER, new Float32Array([-1, -1, -1, 1, 1, 1, 1, -1]), canvas_gl.STATIC_DRAW );
			canvas_gl.bindBuffer( canvas_gl.ELEMENT_ARRAY_BUFFER, canvas_gl.createBuffer() );
			canvas_gl.bufferData( canvas_gl.ELEMENT_ARRAY_BUFFER, new Uint16Array([0, 1, 2, 0, 2, 3]), canvas_gl.STATIC_DRAW );
			canvas_gl.vertexAttribPointer( 0, 2, canvas_gl.FLOAT, false, 0, 0 );
			canvas_gl.enableVertexAttribArray( 0 );

			var simWidth, simHeight, simBuffer, simBuffer2, simBuffer3, simBufferTwin,
				dyeWidth, dyeHeight, dyeBuffer,
				flashBuffer,
				SH_VRT = addShader(canvas_gl.VERTEX_SHADER,   "\n    precision highp float;\n    attribute vec2 aPosition;\n    varying vec2 vUv;\n    varying vec2 vL;\n    varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform vec2 texelSize;\n    void main () {\n        vUv = aPosition * 0.5 + 0.5;\n        vL = vUv - vec2(texelSize.x, 0.0);\n        vR = vUv + vec2(texelSize.x, 0.0);\n        vT = vUv + vec2(0.0, texelSize.y);\n        vB = vUv - vec2(0.0, texelSize.y);\n        gl_Position = vec4(aPosition, 0.0, 1.0);\n    }\n"),
				SH_F01 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n    varying highp vec2 vUv;\n    uniform sampler2D uTexture;\n    uniform float value;\n    void main () {\n        gl_FragColor = value * texture2D(uTexture, vUv);\n    }\n"),
				SH_F02 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  uniform vec4 color;\n    void main () {\n        gl_FragColor = color;\n    }\n"),
				SH_F03 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uTexture;\n    uniform float aspectRatio;\n    #define SCALE 25.0\n    void main () {\n        vec2 uv = floor(vUv * SCALE * vec2(aspectRatio, 1.0));\n        float v = mod(uv.x + uv.y, 2.0);\n        v = v * 0.1 + 0.8;\n        gl_FragColor = vec4(vec3(v), 1.0);\n    }\n"),
				SH_F04 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uTexture;\n    void main () {\n        vec3 C = texture2D(uTexture, vUv).rgb;\n        float a = max(C.r, max(C.g, C.b));\n        gl_FragColor = vec4(C, a);\n    }\n"),
				SH_F05 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uTexture;\n    uniform sampler2D uBloom;\n    uniform sampler2D uDithering;\n    uniform vec2 ditherScale;\n    void main () {\n        vec3 C = texture2D(uTexture, vUv).rgb;\n        vec3 flash = texture2D(uBloom, vUv).rgb;\n        vec3 noise = texture2D(uDithering, vUv * ditherScale).rgb;\n        noise = noise * 2.0 - 1.0;\n        flash += noise / 800.0;\n        flash = pow(flash.rgb, vec3(1.0 / 2.2));\n        C += flash;\n        float a = max(C.r, max(C.g, C.b));\n        gl_FragColor = vec4(C, a);\n    }\n"),
				SH_F06 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       varying vec2 vL;\n    varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform sampler2D uTexture;\n    uniform vec2 texelSize;\n    void main () {\n        vec3 L = texture2D(uTexture, vL).rgb;\n        vec3 R = texture2D(uTexture, vR).rgb;\n        vec3 T = texture2D(uTexture, vT).rgb;\n        vec3 B = texture2D(uTexture, vB).rgb;\n        vec3 C = texture2D(uTexture, vUv).rgb;\n        float dx = length(R) - length(L);\n        float dy = length(T) - length(B);\n        vec3 n = normalize(vec3(dx, dy, length(texelSize)));\n        vec3 l = vec3(0.0, 0.0, 1.0);\n        float diffuse = clamp(dot(n, l) + 0.7, 0.7, 1.0);\n        C.rgb *= diffuse;\n        float a = max(C.r, max(C.g, C.b));\n        gl_FragColor = vec4(C, a);\n    }\n"),
				SH_F07 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       varying vec2 vL;\n    varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform sampler2D uTexture;\n    uniform sampler2D uBloom;\n    uniform sampler2D uDithering;\n    uniform vec2 ditherScale;\n    uniform vec2 texelSize;\n    void main () {\n        vec3 L = texture2D(uTexture, vL).rgb;\n        vec3 R = texture2D(uTexture, vR).rgb;\n        vec3 T = texture2D(uTexture, vT).rgb;\n        vec3 B = texture2D(uTexture, vB).rgb;\n        vec3 C = texture2D(uTexture, vUv).rgb;\n        float dx = length(R) - length(L);\n        float dy = length(T) - length(B);\n        vec3 n = normalize(vec3(dx, dy, length(texelSize)));\n        vec3 l = vec3(0.0, 0.0, 1.0);\n        float diffuse = clamp(dot(n, l) + 0.7, 0.7, 1.0);\n        C *= diffuse;\n        vec3 flash = texture2D(uBloom, vUv).rgb;\n        vec3 noise = texture2D(uDithering, vUv * ditherScale).rgb;\n        noise = noise * 2.0 - 1.0;\n        flash += noise / 800.0;\n        flash = pow(flash.rgb, vec3(1.0 / 2.2));\n        C += flash;\n        float a = max(C.r, max(C.g, C.b));\n        gl_FragColor = vec4(C, a);\n    }\n"),
				SH_F08 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying vec2 vUv;\n       uniform sampler2D uTexture;\n    uniform vec3 curve;\n    uniform float limit;\n    void main () {\n        vec3 c = texture2D(uTexture, vUv).rgb;\n        float br = max(c.r, max(c.g, c.b));\n        float rq = clamp(br - curve.x, 0.0, curve.y);\n        rq = curve.z * rq * rq;\n        c *= max(rq, br - limit) / max(br, 0.0001);\n        gl_FragColor = vec4(c, 0.0);\n    }\n"),
				SH_F09 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying vec2 vL;\n        varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform sampler2D uTexture;\n    void main () {\n        vec4 sum = vec4(0.0);\n        sum += texture2D(uTexture, vL);\n        sum += texture2D(uTexture, vR);\n        sum += texture2D(uTexture, vT);\n        sum += texture2D(uTexture, vB);\n        sum *= 0.25;\n        gl_FragColor = sum;\n    }\n"),
				SH_F10 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying vec2 vL;\n        varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform sampler2D uTexture;\n    uniform float intensity;\n    void main () {\n        vec4 sum = vec4(0.0);\n        sum += texture2D(uTexture, vL);\n        sum += texture2D(uTexture, vR);\n        sum += texture2D(uTexture, vT);\n        sum += texture2D(uTexture, vB);\n        sum *= 0.25;\n        gl_FragColor = sum * intensity;\n    }\n"),
				SH_F11 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uTarget;\n    uniform float aspectRatio;\n    uniform vec3 color;\n    uniform vec2 point;\n    uniform float radius;\n    void main () {\n        vec2 p = vUv - point.xy;\n        p.x *= aspectRatio;\n        vec3 splat = exp(-dot(p, p) / radius) * color;\n        vec3 base = texture2D(uTarget, vUv).xyz;\n        gl_FragColor = vec4(base + splat, 1.0);\n    }\n"),
				SH_F12 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uVelocity;\n    uniform sampler2D uSource;\n    uniform vec2 texelSize;\n    uniform vec2 dyeTexelSize;\n    uniform float dt;\n    uniform float diffusion;\n    vec4 bilerp (sampler2D sam, vec2 uv, vec2 tsize) {\n        vec2 st = uv / tsize - 0.5;\n        vec2 iuv = floor(st);\n        vec2 fuv = fract(st);\n        vec4 a = texture2D(sam, (iuv + vec2(0.5, 0.5)) * tsize);\n        vec4 b = texture2D(sam, (iuv + vec2(1.5, 0.5)) * tsize);\n        vec4 c = texture2D(sam, (iuv + vec2(0.5, 1.5)) * tsize);\n        vec4 d = texture2D(sam, (iuv + vec2(1.5, 1.5)) * tsize);\n        return mix(mix(a, b, fuv.x), mix(c, d, fuv.x), fuv.y);\n    }\n    void main () {\n        vec2 coord = vUv - dt * bilerp(uVelocity, vUv, texelSize).xy * texelSize;\n        gl_FragColor = diffusion * bilerp(uSource, coord, dyeTexelSize);\n        gl_FragColor.a = 1.0;\n    }\n"),
				SH_F13 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       uniform sampler2D uVelocity;\n    uniform sampler2D uSource;\n    uniform vec2 texelSize;\n    uniform float dt;\n    uniform float diffusion;\n    void main () {\n        vec2 coord = vUv - dt * texture2D(uVelocity, vUv).xy * texelSize;\n        gl_FragColor = diffusion * texture2D(uSource, coord);\n        gl_FragColor.a = 1.0;\n    }\n"),
				SH_F14 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying highp vec2 vUv;\n varying highp vec2 vL;\n    varying highp vec2 vR;\n    varying highp vec2 vT;\n    varying highp vec2 vB;\n    uniform sampler2D uVelocity;\n    void main () {\n        float L = texture2D(uVelocity, vL).x;\n        float R = texture2D(uVelocity, vR).x;\n        float T = texture2D(uVelocity, vT).y;\n        float B = texture2D(uVelocity, vB).y;\n        vec2 C = texture2D(uVelocity, vUv).xy;\n        if (vL.x < 0.0) { L = -C.x; }\n        if (vR.x > 1.0) { R = -C.x; }\n        if (vT.y > 1.0) { T = -C.y; }\n        if (vB.y < 0.0) { B = -C.y; }\n        float div = 0.5 * (R - L + T - B);\n        gl_FragColor = vec4(div, 0.0, 0.0, 1.0);\n    }\n"),
				SH_F15 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying highp vec2 vUv;\n varying highp vec2 vL;\n    varying highp vec2 vR;\n    varying highp vec2 vT;\n    varying highp vec2 vB;\n    uniform sampler2D uVelocity;\n    void main () {\n        float L = texture2D(uVelocity, vL).y;\n        float R = texture2D(uVelocity, vR).y;\n        float T = texture2D(uVelocity, vT).x;\n        float B = texture2D(uVelocity, vB).x;\n        float vorticity = R - L - T + B;\n        gl_FragColor = vec4(0.5 * vorticity, 0.0, 0.0, 1.0);\n    }\n"),
				SH_F16 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision highp float;\n    precision highp sampler2D;\n    varying vec2 vUv;\n       varying vec2 vL;\n    varying vec2 vR;\n    varying vec2 vT;\n    varying vec2 vB;\n    uniform sampler2D uVelocity;\n    uniform sampler2D uCurl;\n    uniform float curl;\n    uniform float dt;\n    void main () {\n        float L = texture2D(uCurl, vL).x;\n        float R = texture2D(uCurl, vR).x;\n        float T = texture2D(uCurl, vT).x;\n        float B = texture2D(uCurl, vB).x;\n        float C = texture2D(uCurl, vUv).x;\n        vec2 force = 0.5 * vec2(abs(T) - abs(B), abs(R) - abs(L));\n        force /= length(force) + 0.0001;\n        force *= curl * C;\n        force.y *= -1.0;\n        vec2 vel = texture2D(uVelocity, vUv).xy;\n        gl_FragColor = vec4(vel + force * dt, 0.0, 1.0);\n    }\n"),
				SH_F17 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying highp vec2 vUv;\n varying highp vec2 vL;\n    varying highp vec2 vR;\n    varying highp vec2 vT;\n    varying highp vec2 vB;\n    uniform sampler2D uPressure;\n    uniform sampler2D uDivergence;\n    vec2 boundary (vec2 uv) {\n        return uv;\n        // uncomment if you use wrap or repeat texture mode\n        // uv = min(max(uv, 0.0), 1.0);\n        // return uv;\n    }\n    void main () {\n        float L = texture2D(uPressure, boundary(vL)).x;\n        float R = texture2D(uPressure, boundary(vR)).x;\n        float T = texture2D(uPressure, boundary(vT)).x;\n        float B = texture2D(uPressure, boundary(vB)).x;\n        float C = texture2D(uPressure, vUv).x;\n        float divergence = texture2D(uDivergence, vUv).x;\n        float pressure = (L + R + B + T - divergence) * 0.25;\n        gl_FragColor = vec4(pressure, 0.0, 0.0, 1.0);\n    }\n"),
				SH_F18 = addShader(canvas_gl.FRAGMENT_SHADER, "\n    precision mediump float;\n  precision mediump sampler2D;\n  varying highp vec2 vUv;\n varying highp vec2 vL;\n    varying highp vec2 vR;\n    varying highp vec2 vT;\n    varying highp vec2 vB;\n    uniform sampler2D uPressure;\n    uniform sampler2D uVelocity;\n    vec2 boundary (vec2 uv) {\n        return uv;\n        // uv = min(max(uv, 0.0), 1.0);\n        // return uv;\n    }\n    void main () {\n        float L = texture2D(uPressure, boundary(vL)).x;\n        float R = texture2D(uPressure, boundary(vR)).x;\n        float T = texture2D(uPressure, boundary(vT)).x;\n        float B = texture2D(uPressure, boundary(vB)).x;\n        vec2 velocity = texture2D(uVelocity, vUv).xy;\n        velocity.xy -= vec2(R - L, T - B);\n        gl_FragColor = vec4(velocity, 0.0, 1.0);\n    }\n"),
				drawElements = function( buff ) {
					canvas_gl.bindFramebuffer( canvas_gl.FRAMEBUFFER, buff );
					canvas_gl.drawElements( canvas_gl.TRIANGLES, 6, canvas_gl.UNSIGNED_SHORT, 0 );
				},
				TEXTURE_OBJ = function( img ) {
					var tex = canvas_gl.createTexture();
					canvas_gl.bindTexture(   canvas_gl.TEXTURE_2D, tex );
					canvas_gl.texParameteri( canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_MIN_FILTER, canvas_gl.LINEAR );
					canvas_gl.texParameteri( canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_MAG_FILTER, canvas_gl.LINEAR );
					canvas_gl.texParameteri( canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_WRAP_S, canvas_gl.REPEAT );
					canvas_gl.texParameteri( canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_WRAP_T, canvas_gl.REPEAT );
					canvas_gl.texImage2D( canvas_gl.TEXTURE_2D, 0, canvas_gl.RGB, 1, 1, 0, canvas_gl.RGB, canvas_gl.UNSIGNED_BYTE, new Uint8Array( [255, 255, 255] ) );
					var tex_obj = {
							texture: tex,
							width: 1,
							height: 1,
							attach: function(t) {
								canvas_gl.activeTexture(canvas_gl.TEXTURE0 + t);
								canvas_gl.bindTexture(canvas_gl.TEXTURE_2D, tex);
								return t;
							}
						};
					var img_obj = new Image;
					img_obj.crossOrigin = "anonymous";
					img_obj.onload = function() {
						tex_obj.width = img_obj.width;
						tex_obj.height = img_obj.height;
						canvas_gl.bindTexture(canvas_gl.TEXTURE_2D, tex);
						canvas_gl.texImage2D(canvas_gl.TEXTURE_2D, 0, canvas_gl.RGB, canvas_gl.RGB, canvas_gl.UNSIGNED_BYTE, img_obj);
					};
					img_obj.src = img;
					return tex_obj;
				}( config.image ),
				SH_P01 = new createCanvasProgramm(SH_VRT, SH_F01),
				SH_P02 = new createCanvasProgramm(SH_VRT, SH_F02),
				SH_P03 = new createCanvasProgramm(SH_VRT, SH_F03),
				SH_P04 = new createCanvasProgramm(SH_VRT, SH_F04),
				SH_P05 = new createCanvasProgramm(SH_VRT, SH_F05),
				SH_P06 = new createCanvasProgramm(SH_VRT, SH_F06),
				SH_P07 = new createCanvasProgramm(SH_VRT, SH_F07),
				SH_P08 = new createCanvasProgramm(SH_VRT, SH_F08),
				SH_P09 = new createCanvasProgramm(SH_VRT, SH_F09),
				SH_P10 = new createCanvasProgramm(SH_VRT, SH_F10),
				SH_P11 = new createCanvasProgramm(SH_VRT, SH_F11),
				SH_P12 = new createCanvasProgramm(SH_VRT, canvas_ext.supportLinearFiltering ? SH_F13 : SH_F12),
				SH_P14 = new createCanvasProgramm(SH_VRT, SH_F14),
				SH_P15 = new createCanvasProgramm(SH_VRT, SH_F15),
				SH_P16 = new createCanvasProgramm(SH_VRT, SH_F16),
				SH_P17 = new createCanvasProgramm(SH_VRT, SH_F17),
				SH_P18 = new createCanvasProgramm(SH_VRT, SH_F18);

			function initTextures() {
				var simDim = getBufferDimensions(SETTINGS.SYM_SIZE),
					dyeDim = getBufferDimensions(SETTINGS.DYE_SIZE);

				simWidth  = simDim.width;
				simHeight = simDim.height;
				dyeWidth  = dyeDim.width;
				dyeHeight = dyeDim.height;

				var half_float = canvas_ext.halfFloatTexType,
					f_rgba = canvas_ext.formatRGBA,
					f_rg = canvas_ext.formatRG,
					f_r = canvas_ext.formatR,
					filter_type = canvas_ext.supportLinearFiltering ? canvas_gl.LINEAR : canvas_gl.NEAREST;

				// Dye
				dyeBuffer = null == dyeBuffer
					? createTwinTextureBuffer( dyeWidth, dyeHeight, f_rgba.internalFormat, f_rgba.format, half_float, filter_type )
					: createDrawTextureBuffer( dyeBuffer, dyeWidth, dyeHeight, f_rgba.internalFormat, f_rgba.format, half_float, filter_type );
				// Simbol
				simBuffer = null == simBuffer
					? createTwinTextureBuffer( simWidth, simHeight, f_rg.internalFormat, f_rg.format, half_float, filter_type )
					: createDrawTextureBuffer( simBuffer, simWidth, simHeight, f_rg.internalFormat, f_rg.format, half_float, filter_type );
				simBuffer2 = createTextureBuffer( simWidth, simHeight, f_r.internalFormat, f_r.format, half_float, canvas_gl.NEAREST );
				simBuffer3 = createTextureBuffer( simWidth, simHeight, f_r.internalFormat, f_r.format, half_float, canvas_gl.NEAREST );
				simBufferTwin = createTwinTextureBuffer( simWidth, simHeight, f_r.internalFormat, f_r.format, half_float, canvas_gl.NEAREST );
				// Bloom
				( function() {
					var flashDim = getBufferDimensions(SETTINGS.FLASH_SIZE),
						half_float = canvas_ext.halfFloatTexType,
						f_rgba = canvas_ext.formatRGBA,
						filter_type = canvas_ext.supportLinearFiltering ? canvas_gl.LINEAR : canvas_gl.NEAREST;
					flashBuffer = createTextureBuffer( flashDim.width, flashDim.height, f_rgba.internalFormat, f_rgba.format, half_float, filter_type );
					flashes.length = 0;
					for (var i = 0; i < SETTINGS.FLASH_ITERATIONS; i++ ) {
						var bw = flashDim.width >> i + 1,
							bh = flashDim.height >> i + 1;
						if ( bw < 2 || bh < 2 ) break;
						var tex = createTextureBuffer(bw, bh, f_rgba.internalFormat, f_rgba.format, half_float, filter_type);
						flashes.push(tex);
					}
				})();
			}

			function createTextureBuffer(width, height, int_format, format, half_float, filter_type ) {
				canvas_gl.activeTexture(canvas_gl.TEXTURE0);
				var tex = canvas_gl.createTexture();
				canvas_gl.bindTexture(  canvas_gl.TEXTURE_2D, tex);
				canvas_gl.texParameteri(canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_MIN_FILTER, filter_type);
				canvas_gl.texParameteri(canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_MAG_FILTER, filter_type);
				canvas_gl.texParameteri(canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_WRAP_S, canvas_gl.CLAMP_TO_EDGE);
				canvas_gl.texParameteri(canvas_gl.TEXTURE_2D, canvas_gl.TEXTURE_WRAP_T, canvas_gl.CLAMP_TO_EDGE);
				canvas_gl.texImage2D(canvas_gl.TEXTURE_2D, 0, int_format, width, height, 0, format, half_float, null);
				var buff = canvas_gl.createFramebuffer();
				canvas_gl.bindFramebuffer(canvas_gl.FRAMEBUFFER, buff);
				canvas_gl.framebufferTexture2D(canvas_gl.FRAMEBUFFER, canvas_gl.COLOR_ATTACHMENT0, canvas_gl.TEXTURE_2D, tex, 0);
				canvas_gl.viewport(0, 0, width, height);
				canvas_gl.clear(canvas_gl.COLOR_BUFFER_BIT);
				return  {
					texture: tex,
					fbo: buff,
					width: width,
					height: height,
					attach: function(t) {
						canvas_gl.activeTexture(canvas_gl.TEXTURE0 + t);
						canvas_gl.bindTexture(canvas_gl.TEXTURE_2D, tex);
						return t;
					}
				};
			}

			function createTwinTextureBuffer(width, height, int_format, format, half_float, filter_type ) {
				var tex1 = createTextureBuffer(width, height, int_format, format, half_float, filter_type),
					tex2 = createTextureBuffer(width, height, int_format, format, half_float, filter_type);
				return {
					get read() {
						return tex1;
					},
					set read(t) {
						tex1 = t;
					},
					get write() {
						return tex2;
					},
					set write(t) {
						tex2 = t;
					},
					swap: function() {
						var t = tex1;
						tex1 = tex2;
						tex2 = t;
					}
				};
			}

			function createDrawTextureBuffer( buff_obj, width, height, int_format, format, half_float, filter_type ) {
				
				buff_obj.read = function( tex, width, height, int_format, format, half_float, filter_type) {
					var buff = createTextureBuffer(width, height, int_format, format, half_float, filter_type);
					SH_P01.bind();
					canvas_gl.uniform1i(SH_P01.uniforms.uTexture, tex.attach(0));
					canvas_gl.uniform1f(SH_P01.uniforms.value, 1);
					drawElements(buff.fbo);
					return buff;
				}( buff_obj.read, width, height, int_format, format, half_float, filter_type );
				
				buff_obj.write = createTextureBuffer( width, height, int_format, format, half_float, filter_type );
				
				return buff_obj;
			}

			initTextures();

			var start_time = Date.now(),
				canvas_dim = [1, 1];

			function drawTwinBuffer(n, r, i, o, s) {
				canvas_gl.viewport(0, 0, simWidth, simHeight);
				SH_P11.bind();
				canvas_gl.uniform1i(SH_P11.uniforms.uTarget, simBuffer.read.attach(0));
				canvas_gl.uniform1f(SH_P11.uniforms.aspectRatio, canvas_dom.width / canvas_dom.height);
				canvas_gl.uniform2f(SH_P11.uniforms.point, n / canvas_dom.width, 1 - r / canvas_dom.height);
				canvas_gl.uniform3f(SH_P11.uniforms.color, i, -o, 1);
				canvas_gl.uniform1f(SH_P11.uniforms.radius, SETTINGS.SLAP_RADIUS / 100);
				drawElements(simBuffer.write.fbo);
				simBuffer.swap();
				canvas_gl.viewport(0, 0, dyeWidth, dyeHeight);
				canvas_gl.uniform1i(SH_P11.uniforms.uTarget, dyeBuffer.read.attach(0));
				canvas_gl.uniform3f(SH_P11.uniforms.color, s.r, s.g, s.b);
				drawElements(dyeBuffer.write.fbo);
				dyeBuffer.swap();
			}

			function colorizer() {
				var t = function(c1, c2, c3) {
					var r, g, b;
					var c_max, c_diff, m1, m2, m3;
					c_max = Math.floor(6 * c1);
					m1 = c3 * (1 - c2);
					m2 = c3 * (1 - (c_diff = 6 * c1 - c_max) * c2);
					m3 = c3 * (1 - (1 - c_diff) * c2);
					switch( c_max % 6 ) {
						case 0:
							r = c3; g = m3; b = m1;
							break;
						case 1:
							r = m2; g = c3; b = m1;
							break;
						case 2:
							r = m1; g = c3; b = m3;
							break;
						case 3:
							r = m1; g = m2; b = c3;
							break;
						case 4:
							r = m3; g = m1; b = c3;
							break;
						case 5:
							r = c3; g = m1; b = m2;
					}
					return {
						r: r,
						g: g,
						b: b
					};
				}( Math.random(), 1, 1 );
				t.r *= 0.15;
				t.g *= 0.15;
				t.b *= 0.15;
				return t;
			}

			function getBufferDimensions(size) {
				var ratio = canvas_gl.drawingBufferWidth / canvas_gl.drawingBufferHeight;
				ratio < 1 && (ratio = 1 / ratio);
				var w = Math.round(size * ratio),
					h = Math.round(size);
				return canvas_gl.drawingBufferWidth > canvas_gl.drawingBufferHeight
					? { width: w, height: h }
					: {	width: h, height: w };
			}

			function positionToCoords(cnv, x, y) {
				return {
					x: x / cnv.width,
					y: y / cnv.height
				};
			}

			states[0].down = true;
			if ( SETTINGS.COLORFUL ) {
				states[0].color = colorizer();
			}

			window.addEventListener( "mousemove", function( event ) {
				var x = event.clientX + ( config.global ? 0 : trx_addons_window_scroll_left() ) - config.viewport.offset[0],
					y = event.clientY + ( config.global ? 0 : trx_addons_window_scroll_top() ) - config.viewport.offset[1];
				states[0].moved = states[0].down;
				states[0].dx = 5 * ( x - states[0].x );
				states[0].dy = 5 * ( y - states[0].y );
				states[0].x = x;
				states[0].y = y;
			} );

			window.addEventListener( "touchmove", function( event ) {
				for ( var touches = event.targetTouches, n = 0; n < touches.length; n++ ) {
					var x = touches[n].clientX  + ( config.global ? 0 : trx_addons_window_scroll_left() ) - config.viewport.offset[0],
						y = touches[n].clientY  + ( config.global ? 0 : trx_addons_window_scroll_top() ) - config.viewport.offset[1];
					var r = states[n];
					r.moved = r.down;
					r.dx = 8 * ( x - r.x );
					r.dy = 8 * ( y - r.y );
					r.x = x;
					r.y = y;
				}
			}, false );

			window.addEventListener( "touchstart", function( event ) {
				for ( var touches = event.targetTouches, r = 0; r < touches.length; r++ ) {
					var x = touches[r].clientX + ( config.global ? 0 : trx_addons_window_scroll_left() ) - config.viewport.offset[0],
						y = touches[r].clientY + ( config.global ? 0 : trx_addons_window_scroll_top() ) - config.viewport.offset[1];
					r >= states.length && states.push(new state);
					states[r].id = touches[r].identifier;
					states[r].down = true;
					states[r].x = x;
					states[r].y = y;
					states[r].color = colorizer();
				}
			} );

			window.addEventListener( "touchend", function( event ) {
				for ( var touches = event.changedTouches, n = 0; n < touches.length; n++ ) {
					for (var r = 0; r < states.length; r++ ) {
						touches[n].identifier === states[r].id && (states[r].down = false);
					}
				}
			} );

			return {
				update: function() {
					( function() {
						var dim = config.viewport.size;
						canvas_dim[0] === dim[0] && canvas_dim[1] === dim[1] || ( canvas_dom.width = dim[0], canvas_dom.height = dim[1], initTextures() );
						canvas_dim = dim.slice(0);
					} )();

					( function() {
						if ( impulses.length > 0 ) {
							( function( e ) {
								for ( var n = 0; n < e; n++ ) {
									var r = colorizer();
									r.r *= 10;
									r.g *= 10;
									r.b *= 10;
									drawTwinBuffer( canvas_dom.width * Math.random(),
										canvas_dom.height * Math.random(),
										1000 * ( Math.random() - 0.5 ),
										1000 * ( Math.random() - 0.5 ),
										r
									);
								}
							})( impulses.pop() );
						}
						for(var n = 0; n < states.length; n++ ) {
							var r = states[n];
							if ( r.moved ) {
								drawTwinBuffer( r.x, r.y, r.dx, r.dy, r.color );
								r.moved = false;
							}
						}
						if ( ! SETTINGS.COLORFUL ) {
							return;
						}
						if ( start_time + 100 < Date.now() ) {
							start_time = Date.now();
							for (var s = 0; s < states.length; s++ ) {
								states[s].color = colorizer();
							}
						}
					} )();

					( function( dt ) {

						canvas_gl.disable(canvas_gl.BLEND);

						canvas_gl.viewport(0, 0, simWidth, simHeight);

						// Add curves
						SH_P15.bind();
						canvas_gl.uniform2f(SH_P15.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_gl.uniform1i(SH_P15.uniforms.uVelocity, simBuffer.read.attach(0));
						drawElements(simBuffer3.fbo);

						// Add velosity
						SH_P16.bind();
						canvas_gl.uniform2f(SH_P16.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_gl.uniform1i(SH_P16.uniforms.uVelocity, simBuffer.read.attach(0));
						canvas_gl.uniform1i(SH_P16.uniforms.uCurl, simBuffer3.attach(1));
						canvas_gl.uniform1f(SH_P16.uniforms.curl, SETTINGS.CURL);
						canvas_gl.uniform1f(SH_P16.uniforms.dt, dt);
						drawElements(simBuffer.write.fbo);
						simBuffer.swap();

						SH_P14.bind();
						canvas_gl.uniform2f(SH_P14.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_gl.uniform1i(SH_P14.uniforms.uVelocity, simBuffer.read.attach(0));
						drawElements(simBuffer2.fbo);

						// Add Pressure
						SH_P01.bind();
						canvas_gl.uniform1i(SH_P01.uniforms.uTexture, simBufferTwin.read.attach(0));
						canvas_gl.uniform1f(SH_P01.uniforms.value, SETTINGS.PRESSURE_DIFFUSION);
						drawElements(simBufferTwin.write.fbo);
						simBufferTwin.swap();

						SH_P17.bind();
						canvas_gl.uniform2f(SH_P17.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_gl.uniform1i(SH_P17.uniforms.uDivergence, simBuffer2.attach(0));
						for (var n = 0; n < SETTINGS.PRESSURE_ITERATIONS; n++ ) {
							canvas_gl.uniform1i(SH_P17.uniforms.uPressure, simBufferTwin.read.attach(1));
							drawElements(simBufferTwin.write.fbo);
							simBufferTwin.swap();
						}

						SH_P18.bind();
						canvas_gl.uniform2f(SH_P18.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_gl.uniform1i(SH_P18.uniforms.uPressure, simBufferTwin.read.attach(0));
						canvas_gl.uniform1i(SH_P18.uniforms.uVelocity, simBuffer.read.attach(1));
						drawElements(simBuffer.write.fbo);
						simBuffer.swap();

						SH_P12.bind();
						canvas_gl.uniform2f(SH_P12.uniforms.texelSize, 1 / simWidth, 1 / simHeight);
						canvas_ext.supportLinearFiltering || canvas_gl.uniform2f(SH_P12.uniforms.dyeTexelSize, 1 / simWidth, 1 / simHeight);
						var r = simBuffer.read.attach(0);
						canvas_gl.uniform1i(SH_P12.uniforms.uVelocity, r);
						canvas_gl.uniform1i(SH_P12.uniforms.uSource, r);
						canvas_gl.uniform1f(SH_P12.uniforms.dt, dt);
						canvas_gl.uniform1f(SH_P12.uniforms.diffusion, SETTINGS.VELOCITY_DIFFUSION);
						drawElements(simBuffer.write.fbo);
						simBuffer.swap();
						canvas_gl.viewport(0, 0, dyeWidth, dyeHeight);
						canvas_ext.supportLinearFiltering || canvas_gl.uniform2f(SH_P12.uniforms.dyeTexelSize, 1 / dyeWidth, 1 / dyeHeight);
						canvas_gl.uniform1i(SH_P12.uniforms.uVelocity, simBuffer.read.attach(0));
						canvas_gl.uniform1i(SH_P12.uniforms.uSource, dyeBuffer.read.attach(1));
						canvas_gl.uniform1f(SH_P12.uniforms.diffusion, SETTINGS.DENSITY_DIFFUSION);
						drawElements(dyeBuffer.write.fbo);
						dyeBuffer.swap();

					} )( 0.016 );

					( function( bg_buff ) {
						if ( SETTINGS.FLASH ) {
							( function( dyeBuff, flashBuff ) {
								if ( flashes.length < 2 ) {
									return;
								}
								var bb = flashBuff;
								canvas_gl.disable(canvas_gl.BLEND);
								SH_P08.bind();
								var i = SETTINGS.FLASH_LIMIT * SETTINGS.FLASH_CURVE + 1e-4;
								canvas_gl.uniform3f(SH_P08.uniforms.curve, SETTINGS.FLASH_LIMIT - i, 2 * i, 0.25 / i);
								canvas_gl.uniform1f(SH_P08.uniforms.limit, SETTINGS.FLASH_LIMIT);
								canvas_gl.uniform1i(SH_P08.uniforms.uTexture, dyeBuff.attach(0));
								canvas_gl.viewport(0, 0, bb.width, bb.height);
								drawElements(bb.fbo);
								SH_P09.bind();
								for ( i = 0; i < flashes.length; i++ ) {
									var h = flashes[i];
									canvas_gl.uniform2f(SH_P09.uniforms.texelSize, 1 / bb.width, 1 / bb.height);
									canvas_gl.uniform1i(SH_P09.uniforms.uTexture, bb.attach(0));
									canvas_gl.viewport(0, 0, h.width, h.height);
									drawElements(h.fbo);
									bb = h;
								}
								canvas_gl.blendFunc(canvas_gl.ONE, canvas_gl.ONE);
								canvas_gl.enable(canvas_gl.BLEND);
								for ( i = flashes.length - 2; i >= 0; i-- ) {
									var f = flashes[i];
									canvas_gl.uniform2f(SH_P09.uniforms.texelSize, 1 / bb.width, 1 / bb.height);
									canvas_gl.uniform1i(SH_P09.uniforms.uTexture, bb.attach(0));
									canvas_gl.viewport(0, 0, f.width, f.height);
									drawElements(f.fbo);
									bb = f;
								}
								canvas_gl.disable(canvas_gl.BLEND);
								SH_P10.bind();
								canvas_gl.uniform2f(SH_P10.uniforms.texelSize, 1 / bb.width, 1 / bb.height);
								canvas_gl.uniform1i(SH_P10.uniforms.uTexture, bb.attach(0));
								canvas_gl.uniform1f(SH_P10.uniforms.intensity, SETTINGS.FLASH_INTENSITY);
								canvas_gl.viewport(0, 0, flashBuff.width, flashBuff.height);
								drawElements(flashBuff.fbo);
							} )( dyeBuffer.read, flashBuffer );
						}
						if ( null != bg_buff && SETTINGS.TRANSPARENT ) {
							canvas_gl.disable(canvas_gl.BLEND);
						} else {
							canvas_gl.blendFunc(canvas_gl.ONE, canvas_gl.ONE_MINUS_SRC_ALPHA);
							canvas_gl.enable(canvas_gl.BLEND);
						}
						var width = null == bg_buff ? canvas_gl.drawingBufferWidth : dyeWidth,
							height = null == bg_buff ? canvas_gl.drawingBufferHeight : dyeHeight;
						canvas_gl.viewport(0, 0, width, height);
						if ( ! SETTINGS.TRANSPARENT) {
							SH_P02.bind();
							var bg_color = SETTINGS.BACK_COLOR;
							canvas_gl.uniform4f(SH_P02.uniforms.color, bg_color.r / 255, bg_color.g / 255, bg_color.b / 255, 1);
							drawElements(bg_buff);
						}
						if ( null == bg_buff && SETTINGS.TRANSPARENT ) {
							SH_P03.bind();
							canvas_gl.uniform1f(SH_P03.uniforms.aspectRatio, canvas_dom.width / canvas_dom.height);
							drawElements(null);
						}
						var shader = SETTINGS.SHADING
										? ( SETTINGS.FLASH ? SH_P07 : SH_P06 )
										: ( SETTINGS.FLASH ? SH_P05 : SH_P04 );
						shader.bind();
						if ( SETTINGS.SHADING ) {
							canvas_gl.uniform2f(shader.uniforms.texelSize, 1 / width, 1 / height);
						}
						canvas_gl.uniform1i(shader.uniforms.uTexture, dyeBuffer.read.attach(0));
						if ( SETTINGS.FLASH ) {
							canvas_gl.uniform1i(shader.uniforms.uBloom, flashBuffer.attach(1));
							canvas_gl.uniform1i(shader.uniforms.uDithering, TEXTURE_OBJ.attach(2));
							var coords = positionToCoords(TEXTURE_OBJ, width, height);
							canvas_gl.uniform2f(shader.uniforms.ditherScale, coords.x, coords.y);
						}
						drawElements(bg_buff);
					} )( null );

				}.bind( this ),

				splat: function() {
					impulses.push(parseInt(20 * Math.random(), 10) + 5);
				}
			};
		}

		var _contextLost = false;

		var _canvas = $smoke.get(0);
		_canvas.addEventListener( "webglcontextlost", function() {
			_contextLost = true;
			//_toggleRendering( false );
		}, false );

		var _bg = prepareCanvas( _canvas );

		function renderFog() {
			if ( _bg && ! _contextLost ) {
				_bg.update();
			}
		}

		(function renderLoop() {
			if ( _bg ) {
				renderFog();
				requestAnimationFrame( renderLoop );
			}
		})();

	}

})();