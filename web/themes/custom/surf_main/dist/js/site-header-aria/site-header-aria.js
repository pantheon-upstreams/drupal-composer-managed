/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function ($, Drupal) {
  Drupal.behaviors.ariaControls = {
    attach: function attach(context) {
      once("ariaControls", "#block-surf-main-main-menu", context).forEach(function (menu) {
        // Find top level menu buttons
        var buttons = $(menu).find("ul[data-depth='0'].menu--main > li > button.menu__link");
        // Add aria controls for buttons & related submenus
        // For each primary level for main menu, attach aria label
        buttons.each(function () {
          var id = $(this).text().toLowerCase().replace(/\s/g, "");
          $(this).attr("aria-haspopup", "true").attr("aria-controls", "pannel-".concat(id)).next().attr("id", "pannel-".concat(id)).attr("aria-label", $(this).text());
        });
      });
    }
  };
})(jQuery, Drupal);
(function ($, Drupal) {
  Drupal.behaviors.userLogin = {
    attach: function attach(context) {
      once("userLogin", "#mainMenuControl", context).forEach(function (menu) {
        // Find login menu item
        var loginLinks = $(menu).find("ul[data-depth='0'] >.menu__item > a[href$='/login'].menu__link");
        // Add class for login menu items in utility nav & mobile nav
        loginLinks.each(function () {
          $(this).addClass("menu__login");
        });
      });
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=site-header-aria.js.map