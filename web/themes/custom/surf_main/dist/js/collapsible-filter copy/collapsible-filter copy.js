/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
console.log('collapsible-filter.js');
(function (Drupal, once) {
  Drupal.behaviors.surf_collapsible_filter = {
    attach: function attach(context, _settings) {
      once('collapsible-filter', '.collapsible', context).forEach(function (collapsibleFilterElement) {
        console.log({
          collapsibleFilterElement: collapsibleFilterElement
        });
        function toggleCollapsibleContainer() {
          var container = collapsibleFilterElement;
          container.classList.toggle('open');
        }
        function checkWindowSize() {
          console.log('checkWindowSize');
          var container = collapsibleFilterElement;
          var toggleButton = context.getElementById('toggleButton');
          var isMobile = window.innerWidth <= 768; // Adjust the breakpoint according to your needs

          if (isMobile) {
            toggleButton.addEventListener('click', toggleCollapsibleContainer);
            container.classList.add('open'); // Expand container by default on mobile
          } else {
            toggleButton.removeEventListener('click', toggleCollapsibleContainer);
            container.classList.remove('open');
          }
        }
        window.addEventListener('resize', checkWindowSize);
        checkWindowSize(); // Initial check on page load

        // const trigger = standardsElement.querySelector('.c-teaser__trigger');

        // const standardsCount = standardsElement.querySelector('.c-teaser__trigger').getAttribute('data-standards-count');

        // let tags = standardsElement.querySelectorAll('.c-teaser__tag.standards');

        // // Initially hide the tags
        // tags.forEach(function(tag) {
        //   tag.classList.add('hidden');
        // });

        // // Initially hide the unordered list
        // standardsElement.classList.add('hidden');

        // trigger.addEventListener('click', function() {
        //   // Toggle the visibility of the tags
        //   tags.forEach(function(tag) {
        //     tag.classList.toggle('hidden');
        //   });

        //   let expanded = trigger.getAttribute('aria-expanded') === 'true' || false;
        //   trigger.setAttribute('aria-expanded', !expanded);

        //   // Change the trigger text based on the visibility state
        //   if (expanded) {
        //     trigger.textContent = 'View Standards' + ' (' + standardsCount + ')';
        //   } else {
        //     trigger.textContent = 'Hide Standards' + ' (' + standardsCount + ')';
        //   }

        // });
      });
    }
  };
})(Drupal, once);
/******/ })()
;
//# sourceMappingURL=collapsible-filter copy.js.map