(function ($) {
  Drupal.behaviors.customFieldVisibility = {
    attach: function (context, settings) {
      console.log('👍🏻')
      // Replace 'select-field' with the ID or class of your select field.
      var selectField = $('#edit-field-cards-0-subform-field-theme', context).once('customFieldVisibility');
      console.log({selectField})

      // Replace 'dependent-field' with the ID or class of your dependent field.
      var dependentField = $('#edit-field-cards-0-subform-field-icon', context).once('customFieldVisibility');
      console.log({dependentField})

      // Hide the dependent field initially.
      dependentField.hide();

      // Add a change event listener to the select field.
      selectField.on('change', function () {
        var selectedValue = $(this).val();

        // Replace 'icon' with the value that should trigger the field visibility.
        if (selectedValue === 'icon') {
          dependentField.show();
        } else {
          dependentField.hide();
        }
      });
    }
  };
})(jQuery);
