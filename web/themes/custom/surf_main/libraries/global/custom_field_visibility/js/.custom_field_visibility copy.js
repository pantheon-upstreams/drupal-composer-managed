(function ($, Drupal) {
  Drupal.behaviors.customFieldVisibility = {
    attach: function (context) {
      $('#edit-select-field', context).once('customFieldVisibility').change(function () {
        var selectValue = $(this).val();

        $.ajax({
          url: '/custom-field-visibility/field-visibility-ajax',
          method: 'POST',
          data: {
            select_value: selectValue,
          },
          success: function (response) {
            $('#dependent-field-wrapper', context).replaceWith(response);
          },
        });
      });
    },
  };
})(jQuery, Drupal);
