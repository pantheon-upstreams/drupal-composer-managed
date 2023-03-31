/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function ($, Drupal) {
  Drupal.behaviors.accordion = {
    attach: function attach(context) {
      $('.c-accordion', context).each(function (index, item) {
        var $this = $(this);
        var $buttons = $this.find('button[aria-expanded]');
        $buttons.each(function (index, item) {
          $(this).click(function () {
            var $button = $(this);
            if ($button.attr('aria-expanded') === 'true') {
              $button.attr('aria-expanded', 'false');
              // Hide the panel.
              $("#".concat($button.attr('aria-controls'))).attr('hidden', 'true');
              $button.removeAttr('hidden');
            } else {
              $button.attr('aria-expanded', 'true');
              // Show the panel.
              $("#".concat($button.attr('aria-controls'))).removeAttr('hidden');
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=accordion.js.map