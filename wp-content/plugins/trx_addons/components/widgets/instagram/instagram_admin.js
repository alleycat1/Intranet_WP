/**
 * Widget Instagram
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {
	"use strict";
	// Callback for get access token
	window.trx_addons_api_instagram_get_access_token = function() {
		window.location.href = TRX_ADDONS_STORAGE['api_instagram_get_code_uri'];
	};
})();