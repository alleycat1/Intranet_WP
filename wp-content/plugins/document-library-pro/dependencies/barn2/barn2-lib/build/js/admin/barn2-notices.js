/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************************!*\
  !*** ./assets/js/admin/barn2-notices.js ***!
  \******************************************/
(function ($, window, document, undefined) {
  "use strict";

  $(document).ready(function () {
    $(document.body).on('click', '.barn2-notice .notice-dismiss', function () {
      var $notice = $(this).parent(),
          data = $notice.data();

      if (!data.id || !data.type) {
        return;
      }

      data.action = 'barn2_dismiss_notice';
      $.ajax({
        url: ajaxurl,
        // always defined when running in WP Admin
        type: 'POST',
        data: data,
        xhrFields: {
          withCredentials: true
        }
      });
    });
  });
})(jQuery, window, document);
/******/ })()
;
//# sourceMappingURL=barn2-notices.js.map