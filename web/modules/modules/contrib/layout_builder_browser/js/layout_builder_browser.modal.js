(function ($, Drupal) {
  var debounce = Drupal.debounce,
      announce = Drupal.announce,
      formatPlural = Drupal.formatPlural;

  var layoutBuilderBlocksFiltered = false;

  Drupal.behaviors.layoutBuilderBrowser = {
    attach: function attach(context) {

      // Override core behaviors.layoutBuilderBlockFilter.attach().
      Drupal.behaviors.layoutBuilderBlockFilter.attach =
        function attach(context) {

          // Custom selector, remove context to ensure filter works in modal.
          var $categories = $('.js-layout-builder-categories');
          var $filterLinks = $categories.find('.js-layout-builder-block-link');

          var filterBlockList = function filterBlockList(e) {
            var query = $(e.target).val().toLowerCase();

            var toggleBlockEntry = function toggleBlockEntry(index, link) {
              var $link = $(link);
              var textMatch = $link.text().toLowerCase().indexOf(query) !== -1;
              $link.toggle(textMatch);
            };

            if (query.length >= 2) {
              $categories.find('.js-layout-builder-category:not([open])').attr('remember-closed', '');

              $categories.find('.js-layout-builder-category').attr('open', '');

              $filterLinks.each(toggleBlockEntry);

              $categories.find('.js-layout-builder-category:not(:has(.js-layout-builder-block-link:visible))').hide();

              announce(formatPlural($categories.find('.js-layout-builder-block-link:visible').length, '1 block is available in the modified list.', '@count blocks are available in the modified list.'));
              layoutBuilderBlocksFiltered = true;
            } else if (layoutBuilderBlocksFiltered) {
              layoutBuilderBlocksFiltered = false;

              $categories.find('.js-layout-builder-category[remember-closed]').removeAttr('open').removeAttr('remember-closed');
              $categories.find('.js-layout-builder-category').show();
              $filterLinks.show();
              announce(Drupal.t('All available blocks are listed.'));
            }
          };

          $('input.js-layout-builder-filter', context).once('block-filter-text').on('keyup', debounce(filterBlockList, 200));
        }
    }
  };


})(jQuery, Drupal);
