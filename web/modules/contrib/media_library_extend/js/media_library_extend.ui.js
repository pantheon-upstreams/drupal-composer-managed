
(function ($, Drupal, window) {

  Drupal.behaviors.MediaLibraryExtendPager = {
    attach: function attach(context) {
      var $pager = $('.js-media-library-extend-pager', context);

      $pager.find('a', context).once('media-library-extend-pager').on('keypress', function (e) {
        if (e.which === 32) {
          e.preventDefault();
          e.stopPropagation();
          $(e.currentTarget).trigger('click');
        }
      }).on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Collect form values of filter form so they don't get lost.
        var formData = $pager.closest('form').serializeArray().reduce(function (accumulator, item) {
          accumulator[item.name] = item.value;
          return accumulator;
        }, {});
        formData._triggering_element_name = 'filter';

        var ajaxObject = Drupal.ajax({
          wrapper: 'media-library-extend-form-content',
          url: e.currentTarget.href,
          submit: formData,
          dialogType: 'ajax',
          progress: {
            type: 'fullscreen',
            message: Drupal.t('Please wait...')
          }
        });

        ajaxObject.success = function (response, status) {
          var _this = this;

          if (this.progress.element) {
            $(this.progress.element).remove();
          }
          if (this.progress.object) {
            this.progress.object.stopMonitoring();
          }
          $(this.element).prop('disabled', false);

          Object.keys(response || {}).forEach(function (i) {
            if (response[i].command && _this.commands[response[i].command]) {
              _this.commands[response[i].command](_this, response[i], status);
            }
          });

          $('#media-library-content :tabbable:first').focus();

          this.settings = null;
        };
        ajaxObject.execute();
      });
    }
  };

})(jQuery, Drupal, window);