/**
 * @file
 * Behaviors Border plugin layout builder form scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";
  
  // Border.
  Drupal.behaviors.borderLayoutBuilderForm = {
    attach: function (context) {
      // The default border color.
      $('input.bs-field-border-color', context).once('blb_border').each(function () {
        var border_color = '';
        if ($(this).val() !='_none' && typeof $(this).next('label').css('border-color') != 'undefined') {
          border_color = $(this).next('label').css('border-color');
          $(this).next('label').attr('style', 'background-color: ' + border_color + ' !important; border-color: white !important;');
        }
      });

      // Assign border style.
      var directions = ['left', 'top', 'right', 'bottom'];
      // Loop through the directions.
      for (var i = 0; i < directions.length; i++) {
        $('input.bs-field-border-style-' + directions[i], context).once('blb_border').each(function () {
          var border_style = '';
          if ($(this).val() !='_none' && typeof $(this).next('label').css('border-style') != 'undefined') {
            border_style = $(this).next('label').css('border-' + directions[i] + '-style');
            $(this).next('label').css('border-style', border_style);
          }
        });

        // Switch border color to background color.
        $('input.bs-field-border-color-' + directions[i], context).once('blb_border').each(function () {
          var border_color = '';
          if ($(this).val() !='_none' && typeof $(this).next('label').css('border-color') != 'undefined') {
            border_color = $(this).next('label').css('border-' + directions[i] + '-color');
            $(this).next('label').attr('style', 'background-color: ' + border_color + ' !important; border-color: white !important;');
          }
        });

      }
    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);
