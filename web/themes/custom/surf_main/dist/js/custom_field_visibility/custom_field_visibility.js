/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
(function ($) {
  Drupal.behaviors.customFieldVisibility = {
    attach: function attach(context, settings) {
      console.log('👍🏻');
      // Replace 'select-field' with the ID or class of your select field.
      var selectField = $('#edit-field-cards-0-subform-field-theme', context).once('customFieldVisibility');
      console.log({
        selectField: selectField
      });

      // Replace 'dependent-field' with the ID or class of your dependent field.
      var dependentField = $('#edit-field-cards-0-subform-field-icon', context).once('customFieldVisibility');
      console.log({
        dependentField: dependentField
      });

      // Hide the dependent field initially.
      dependentField.hide();

      // Add a change event listener to the select field.
      selectField.on('change', function () {
        var selectedValue = $(this).val();
        console.log({
          selectedValue: selectedValue
        });
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
/******/ })()
;
//# sourceMappingURL=custom_field_visibility.js.map