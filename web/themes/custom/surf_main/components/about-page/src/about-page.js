(($, Drupal) => {

  Drupal.behaviors.accordion = {
    attach: function attach(context) {

      $('.c-about-page', context).each(function (index, item) {
        const $this = $(this);
        console.log('About page');
      });

    },
  };
})(jQuery, Drupal)
