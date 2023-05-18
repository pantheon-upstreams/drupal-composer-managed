(function ($, Drupal) {
  Drupal.behaviors.button = {
    attach: function attach(context) {
      $(".c-media__video-wrapper").fitVids();
    },
  };
})(jQuery, Drupal);
