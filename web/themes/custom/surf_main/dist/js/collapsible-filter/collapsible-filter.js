/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function (Drupal, once) {
  Drupal.behaviors.surf_collapsible_filter = {
    attach: function attach(context, _settings) {
      once('collapsible-filter', '.collapsible__container', context).forEach(function (collapsibleFilterElement) {
        // get the toggle button within the collapsible container
        var toggleButton = collapsibleFilterElement.querySelector('.collapsible__trigger');
        var contentContainer = collapsibleFilterElement.querySelector('.collapsible__container-content');
        toggleButton.addEventListener('click', function () {
          if (toggleButton.getAttribute('aria-expanded') === 'true') {
            toggleButton.setAttribute('aria-expanded', 'false');
            // contentContainer set hidden to true
            contentContainer.setAttribute('hidden', 'true');
          } else {
            toggleButton.setAttribute('aria-expanded', 'true');
            //  remove hidden attribute
            contentContainer.removeAttribute('hidden');
          }
        });

        // create a window resize event listener to check the window size is greater than or equal to 1620px
        window.addEventListener('resize', checkWindowSize);

        // custom function to check the window size
        function checkWindowSize() {
          var isDesktop = window.innerWidth >= 1400;
          if (isDesktop) {
            contentContainer.removeAttribute('hidden');
            toggleButton.setAttribute('aria-expanded', 'true');
          }
        }
        checkWindowSize(); // Initial check on page load
      });
    }
  };
})(Drupal, once);
/******/ })()
;
//# sourceMappingURL=collapsible-filter.js.map