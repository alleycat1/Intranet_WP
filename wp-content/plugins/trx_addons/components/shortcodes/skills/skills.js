/**
 * Shortcode Skills
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";

	var $document = jQuery( document );

	$document.on( 'action.init_trx_addons', function() {

		var $skills_items,
			$skills_canvas;

		// Update links and values after the new post added
		$document.on( 'action.init_hidden_elements', update_jquery_links );
		$document.on( 'action.got_ajax_response', update_jquery_links );
		var first_run = true;
		function update_jquery_links(e) {
			if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
				first_run = false;
				return; 
			}
			$skills_canvas = jQuery( '.sc_skills_pie canvas' );
			$skills_items  = jQuery( '.sc_skills_item' );
			if ($skills_items.length > 0 ) {
				trx_addons_intersection_observer_add( $skills_items, function( item, enter ) {
					if ( enter ) {
						trx_addons_intersection_observer_remove( item );
						trx_addons_sc_skills_init();
					}
				} );
			}
		}
		update_jquery_links();

		$document
			.on( 'action.init_hidden_elements', trx_addons_sc_skills_init )
			.on( 'action.scroll_trx_addons',    trx_addons_sc_skills_init )
			.on( 'action.resize_trx_addons',    trx_addons_sc_skills_resize );
		
		// Skills init
		function trx_addons_sc_skills_init(e, container) {
			if ( $skills_items.length === 0 ) return;
		
			$skills_items.each(function(idx) {
				var skillsItem = $skills_items.eq(idx);
				// If item now invisible or inited
				if ( skillsItem.hasClass('inited') || ! skillsItem.hasClass('trx_addons_in_viewport') || skillsItem.parents('div:hidden,article:hidden').length > 0 ) {
					return;
				}
				var init_ok = true;
				var skills = skillsItem.parents('.sc_skills').eq(0);
				var type = skills.data('type');
				var total = (type=='pie' && skills.hasClass('sc_skills_compact_on')) 
								? skillsItem.find('.sc_skills_data .pie') 
								: skillsItem.find('.sc_skills_total').eq(0);
				var start = parseFloat(total.data('start'));
				var stop = parseFloat(total.data('stop'));
				var maximum = parseInt(total.data('max'), 10);
				var startPercent = Math.round(start/maximum*100);
				var stopPercent = Math.round(stop/maximum*100);
				var ed = total.data('ed');
				var speed = parseInt(total.data('speed'), 10);
				var step = parseFloat(total.data('step'));
				var duration = parseInt(total.data('duration'), 10);
				if (isNaN(duration)) {
					duration = Math.ceil(maximum/step)*speed;
					total.data('duration', duration);
				}
				total.data('decimals', Math.max(
												0,
												start!=parseInt(start, 10) ? (''+start).length - 1 - (''+parseInt(start, 10)).length : 0,
												stop!=parseInt(stop, 10) ? (''+stop).length - 1 - (''+parseInt(stop, 10)).length : 0
												)
							);
				
				if (type == 'bar') {
					var dir = skills.data('dir');
					var count = skillsItem.find('.sc_skills_count').eq(0);
					if (dir=='horizontal') {
						count.css('width', startPercent + '%').animate({ width: stopPercent + '%' }, duration);
					} else if (dir=='vertical') {
						count.css('height', startPercent + '%').animate({ height: stopPercent + '%' }, duration);
					}
					trx_addons_sc_skills_animate_counter(start, stop, speed, step, ed, total);
				
				} else if (type == 'counter') {
					trx_addons_sc_skills_animate_counter(start, stop, speed, step, ed, total);

				} else if (type == 'pie') {
					if (window.ChartLegacy) {
						var steps = parseInt(total.data('steps'), 10);
						var bg_color = total.data('bg_color');
						var border_color = total.data('border_color');
						var cutout = parseInt(total.data('cutout'), 10);
						var easing = total.data('easing');
						var options = trx_addons_apply_filters('trx_addons_filter_skills_pie_options', {
							segmentShowStroke: border_color !== '',
							segmentStrokeColor: border_color,
							segmentStrokeWidth: border_color !== '' ? 1 : 0,
							percentageInnerCutout: cutout,
							animation: true,	//skillsItem.parents('.vc_row[data-vc-full-width="true"]').length==0,
							animationSteps: steps,
							animationEasing: easing,
							animateRotate: true,
							animateScale: true 	//skillsItem.parents('.vc_row[data-vc-full-width="true"]').length==0,
						} );
						var pieData = [];
						total.each(function() {
							var color = jQuery(this).data('color');
							var stop = parseInt(jQuery(this).data('stop'), 10);
							var stopPercent = Math.round(stop/maximum*100);
							pieData.push({
								value: stopPercent,
								color: color
							});
						});
						if (total.length == 1) {
							trx_addons_sc_skills_animate_counter(start, stop, Math.round(1500/steps), step, ed, total);
							pieData.push({
								value: 100-stopPercent,
								color: bg_color
							});
						}
						var canvas = skillsItem.find('canvas');
						canvas
							.data('pie-data', pieData)
							.data('pie-options', options)
							.attr({width: skillsItem.width(), height: skillsItem.width()})
							.css({width: skillsItem.width(), height: skillsItem.height()});
						new ChartLegacy(canvas.get(0).getContext("2d")).Doughnut(pieData, options);
					} else {
						init_ok = false;
					}
				}
				if (init_ok) skillsItem.addClass('inited');
			});
		}
	
		// Skills counter animation
		function trx_addons_sc_skills_animate_counter(start, stop, speed, step, ed, total) {
			start = Math.min(stop, start+step);
			// Example of format output number: leave 2 decimals and separate it with ',' and use dot '.' as thousands delimiter
			//total.text(Number(start).formatMoney(2, ',', '.')+ed);
			var style = total.data('style') || 'counter';
			if ( style == 'odometer' ) {
				var digits = total.find('.sc_skills_digit'),
					duration = total.data('duration');
				digits.each( function(idx, item) {
					var $self = digits.eq(idx);
					if ( ! $self.data('rounds') ) {
						var rounds = idx*10 + (''+stop).substring(idx, idx+1) * 1,
							delay = Math.floor( ( duration - 300 ) / Math.max( 1, rounds ) );
						$self.data( {
							'rounds': rounds,
							'round': 0,
							'skip': 0,
							'delay': delay
						} );
					}
					trx_addons_sc_skills_animate_digit($self, idx);
				} );
			} else {						// style == 'counter'
				total.text( trx_addons_round_number( start, total.data('decimals') ) + ed );
				if (start < stop) {
					setTimeout(function () {
						trx_addons_sc_skills_animate_counter(start, stop, speed, step, ed, total);
					}, speed);
				}
			}
		}

		// Animate one digit in the odometer
		function trx_addons_sc_skills_animate_digit($self, idx) {
			var $ribbon = $self.find('.sc_skills_digit_ribbon'),
				$value = $self.find('.sc_skills_digit_value'),
				rounds = $self.data('rounds'),
				round = $self.data('round'),
				skip = $self.data('skip'),
				delay = $self.data('delay') + ( round < rounds ? 0 : 300 );
			if ( round < rounds ) {
				if ( skip++ < idx ) {
					$self.data('skip', skip);
					setTimeout( function() {
						trx_addons_sc_skills_animate_digit($self, idx);
					}, delay );
				} else {
					round++;
					var $next = $value.clone();
					$next.html( round % 10 );
					$value.after( $next );
					$ribbon.animate( {
						'top': -$value.height()+'px'
						},
						delay,
						'linear',
						function() {
							$value.remove();
							$ribbon.css('top', 0);
							trx_addons_sc_skills_animate_digit($self, idx);
						}
					);
					$self.data('round', round);
				}
			}
		}

		// Resize Pie Skills
		function trx_addons_sc_skills_resize() {

			if ( $skills_canvas.length == 0 ) return;

			$skills_canvas.each(function () {
				var canvas = jQuery(this);
				// If item now invisible
				if ( ! window.ChartLegacy || canvas.parents('div:hidden,article:hidden').length > 0 ) {
					return;
				}
				var skillsItem = canvas.parent();
				if (skillsItem.width() != canvas.width()) {
					var data = canvas.data('pie-data');
					var opt = canvas.data('pie-options');
					if (data === undefined || opt === undefined) return;
					canvas.empty()
						.attr({width: skillsItem.width(), height: skillsItem.width()})
						.css({width: skillsItem.width(), height: skillsItem.height()});
					opt.animation = false;
					new ChartLegacy(canvas.get(0).getContext("2d")).Doughnut(data, opt);
				}
			});
		}

	} );

})();