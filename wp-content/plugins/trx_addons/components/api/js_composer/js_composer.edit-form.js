/* global jQuery */

(function() {
	"use strict";

	// Add Background position to the CSS Editor
	window.vc && window.vc.CssEditor
		&& (vc.CssEditor.prototype.getBackgroundPosition = function() {
				return this.$el.find("[name=background_position]").val()
			})
		&& (vc.CssEditor.prototype.getBackgroundOld = vc.CssEditor.prototype.getBackground)
		&& (vc.CssEditor.prototype.getBackground = function() {
				this.getBackgroundOld();
				if (this.attrs["background-image"] || (this.attrs["background"] && this.attrs["background"].indexOf('url') >= 0))
					this.attrs["background-position"] = this.getBackgroundPosition();
				else {
					delete this.attrs["background-image"];
					delete this.attrs["background-repeat"];
					delete this.attrs["background-position"];
				}
			});

})();