(function ($, Drupal) {

  Drupal.behaviors.du_widen_alt_text = {
    attach: function (context, settings) {
      if ($('div[class*=entity-browser]').length > 0) {
        // Check for alt text data.
        let data = sessionStorage.getItem('Drupal.du_widen.alt_text') || {};
        if (data != null && Object.keys(data).length > 0) {
          data = JSON.parse(data);
          for (id in data) {
            // Find images that match the ID from the widen image. If one is
            // found, add the alt text value if there isn't already alt text
            // provided.
            let image = $('img[src*=' + id + ']');
            if (image != null) {
              let altInput = $(image).parents('tr').find('div[class*=meta-alt] input');
              if (altInput != null && $(altInput).val() == '') {
                $(altInput).val(data[id]);
              }
            }
          }
        }
      }
    }
  };

})(jQuery, Drupal);
