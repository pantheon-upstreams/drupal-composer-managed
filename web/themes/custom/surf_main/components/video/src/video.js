(function ($, Drupal) {
  Drupal.behaviors.button = {
    attach: function attach(context) {
      $(".c-video__video-wrapper").fitVids();
    },
  };
})(jQuery, Drupal);
