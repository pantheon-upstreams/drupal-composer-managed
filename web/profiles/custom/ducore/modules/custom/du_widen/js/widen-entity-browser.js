(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.widen_entity_browser = {
    attach: function (context, settings) {
      // The user selects an image.
      $('.views-field-thumbnail img').click(function() {
        // Remove highlights from images.
        $('.view-widen .view-content img').css('border', '0px solid black');

        // Highlight the selected image.
        $(this).css('border', '2px solid black');
      });

      // If there is alt text available, store it in session storage so we can
      // load it once an image is selected.
      let alt_text = drupalSettings.du_widen['alt_text'];
      if (alt_text != null && Object.keys(alt_text).length > 0) {
        let current_alt_text = sessionStorage.getItem('Drupal.du_widen.alt_text') || {};
        if (current_alt_text != null && Object.keys(current_alt_text).length > 0) {
          current_alt_text = JSON.parse(current_alt_text);
          alt_text = $.extend({}, alt_text, current_alt_text);
        }
        sessionStorage.setItem('Drupal.du_widen.alt_text', JSON.stringify(alt_text));
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
