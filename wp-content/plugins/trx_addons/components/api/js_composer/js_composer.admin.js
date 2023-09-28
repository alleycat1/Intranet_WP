/* global jQuery */

jQuery(document).ready(function() {
	"use strict";

	// Allow insert containers inside inner columns
	// Attention! Used vc_map_update() instead this method
	// window.vc && window.vc.map && (vc.map['vc_column_inner'].allowed_container_element = true);
		
	// Create VC wrappers for the VcRowView and VcColumnView and for our shortcodes-containers
	// to wrap vc_admin_label to the container and move it after the title
	window.VcColumnView
		&& vc && vc.map && vc.map['vc_column_inner']
		&& (vc.map['vc_column_inner'].allowed_container_element = true)		// Allow insert containers inside inner columns
		&& vc.shortcode_view
		&& (vc.shortcode_view.prototype.renderContentOld = vc.shortcode_view.prototype.renderContent)
		&& (vc.shortcode_view.prototype.renderContent = function() {
				this.renderContentOld();
				if (this.$el.hasClass('wpb_content_element'))
					this.moveAdminLabelsAfterTitle();
			})
		&& (vc.shortcode_view.prototype.moveAdminLabelsAfterTitle = function() {
				var wrapper = this.$el.find('> .wpb_element_wrapper');
				if (wrapper.length == 0) return;
				var labels = wrapper.find('> .vc_admin_label');
				if (labels.length == 0) return;
				var labels_wrap, title = wrapper.find('> .wpb_element_title');
				// If title present
				if (title.length > 0) {
					// Single element
					if (this.$el.hasClass('wpb_content_element')) {
						var wpb_vc_param_value = wrapper.find('> .wpb_vc_param_value');
						// Single element with params - move params after labels
						if (wpb_vc_param_value.length == 1)
							wpb_vc_param_value.insertAfter(labels.eq(labels.length-1));
					// Container
					} else if (this.$el.hasClass('vc_shortcodes_container')) {
						labels_wrap = title.find('> .vc_admin_labels');
						if (labels_wrap.length == 0) {
							title.append('<div class="vc_admin_labels"></div>');
							labels_wrap = title.find('> .vc_admin_labels');
						} else
							labels_wrap.empty();
						labels.clone().appendTo(labels_wrap);
					}
				// Elements without title - just wrap labels
				} else {
					if (this.$el.hasClass('wpb_content_element')) {
						if (!this.$el.hasClass('wpb_content_element_without_title')) 
							this.$el.addClass('wpb_content_element_without_title');
						var wpb_vc_param_value = wrapper.find('> .wpb_vc_param_value');
						// Single element with params - move params before labels
						if (wpb_vc_param_value.length == 1)
							wpb_vc_param_value.insertBefore(labels.eq(0));
					}
					labels_wrap = wrapper.find('> .vc_admin_labels');
					if (labels_wrap.length == 0) {
						wrapper.append('<div class="vc_admin_labels"></div>');
						labels_wrap = wrapper.find('> .vc_admin_labels');
					} else
						labels_wrap.empty();
					labels.clone().appendTo(labels_wrap);
				}
			})
		&& (window.VcColumnView.prototype.buildDesignHelpersOld = window.VcColumnView.prototype.buildDesignHelpers)
		&& (window.VcColumnView.prototype.buildDesignHelpers = function() {
				this.buildDesignHelpersOld();
				this.moveAdminLabelsAfterTitle();
			})
		&& (window.VcColumnView.prototype.changeShortcodeParamsOld = window.VcColumnView.prototype.changeShortcodeParams)
		&& (window.VcColumnView.prototype.changeShortcodeParams = function(model) {
				this.changeShortcodeParamsOld(model);
				this.moveAdminLabelsAfterTitle();
			})
		&& (window.VcRowView.prototype.buildDesignHelpersOld = window.VcRowView.prototype.buildDesignHelpers)
		&& (window.VcRowView.prototype.buildDesignHelpers = function() {
				this.buildDesignHelpersOld();
				this.moveAdminLabelsAfterTitle();
			})
		&& (window.VcRowView.prototype.changeShortcodeParamsOld = window.VcRowView.prototype.changeShortcodeParams)
		&& (window.VcRowView.prototype.changeShortcodeParams = function(model) {
				this.changeShortcodeParamsOld(model);
				this.moveAdminLabelsAfterTitle();
			})				
		&& (window.VcTrxAddonsContainerView = window.VcColumnView.extend({
			}));
		
	// Refresh taxonomies and terms lists when post type is changed
	// In VC editor
	//---------------------------------------------------------------------------
	jQuery('body')
		.on('change', 'select[class*="post_type"],select[class*="taxonomy"]', function () {
			var refresh_obj = jQuery(this),
				refresh_post_type = (refresh_obj.attr('name').indexOf('post_type') == 0),
				cat_flds = refresh_post_type
					? refresh_obj.parents('#vc_edit-form-tabs').find('.vc_shortcode-param[data-vc-shortcode-param-name*="taxonomy"]').find('select')
					: refresh_obj.parents('.vc_shortcode-param').next().find('select');
			if (cat_flds.length > 0) {
				var num = 0;
				jQuery.each(cat_flds, function(index, cat_fld){
					cat_fld = jQuery(cat_fld);
					if (cat_fld.length === 0) return true;
					var cat_lbl = cat_fld.parents('.vc_shortcode-param').find('.wpb_element_label');
					setTimeout(function(){
						trx_addons_refresh_list(
							cat_fld.attr('class').indexOf('taxonomy') >= 0
								? 'taxonomies'
								: 'terms',
							refresh_obj.val(),
							cat_fld,
							cat_lbl
						);						
					}, 300*num);
					num++;
				});
			}
			return false;
		});

});