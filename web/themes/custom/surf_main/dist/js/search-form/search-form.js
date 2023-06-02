/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
// This is a workaround solution for a known Drupal core caching issue:
// https://www.drupal.org/project/drupal/issues/1852090

(function ($) {
  Drupal.behaviors.search_keys = {
    attach: function attach(context, settings) {
      $("#search-api-form input").each(function (index) {
        //Update two input fields for search-api-form field to resolve duplicate conflict
        var idNum = Math.round(Math.random(8));
        var $inputid = $(this).attr("id");
        $(this).attr("id", $inputid + idNum);
        $(this).prev("label").attr("for", $inputid + idNum);
      });
    }
  };
})(jQuery);
/******/ })()
;
//# sourceMappingURL=search-form.js.map