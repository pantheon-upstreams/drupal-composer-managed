/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function ($, Drupal) {
  Drupal.behaviors.accordion = {
    attach: function attach(context) {
      $('.c-accordion').each(function (index, item) {
        var $this = $(this);
        var $buttons = $this.find('button[aria-expanded]');
        $buttons.each(function (index, item) {
          $(this).click(function () {
            if ($(this).attr('aria-expanded') === 'true') {
              $(this).attr('aria-expanded', 'false');
              $(this).parent().next('.c-accordion__panel').attr('hidden', 'true');
              $(this).removeAttr('hidden');
            } else {
              $(this).attr('aria-expanded', 'true');
              $(this).parent().next('.c-accordion__panel').removeAttr('hidden');
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=main.js.map