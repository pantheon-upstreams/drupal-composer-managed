/**
 * @file
 * Behaviors Layout Builder Blocks content tab scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";

  Drupal.behaviors.LayoutBuilderBlocksContentTab = {
    attach: function (context) {

      // Move the original block fields to content tab.
      $('form.layout-builder-configure-block > :not(#bs_ui):not(.form-submit)').each(function() {
        $('form.layout-builder-configure-block > #bs_ui > #bs_tabContent > .bs_tab-pane--content').append($(this));
      });

    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);
