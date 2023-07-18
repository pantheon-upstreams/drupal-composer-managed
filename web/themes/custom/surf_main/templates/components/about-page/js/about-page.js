/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function ($, Drupal) {
  Drupal.behaviors.accordion = {
    attach: function attach(context) {
      $('.c-about-page', context).each(function (index, item) {
        var $this = $(this);
        console.log('About page');
      });
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=about-page.js.map