/**
 * @file
 * Behaviors Bootstrap styles input scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";

  // Adds value from input to the label to emulate a "preview" on our inputs.
  Drupal.behaviors.bootstrapStylesInputCircles = {
    attach: function (context) {
      $(".bs_input-circles input:radio", context).once('bs_input-circles').each(function () {

        // Add the [key] class from the backend to our forms input element (preview our colour).
        $(this).next('label').addClass($(this).val());

      });
    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);


