(function ($) {

  $(document).ready(function () {

    $('.js-mobile-nav-trigger').on('change', e => {
      $('body')[e.target.checked ? 'addClass' : 'removeClass']('js-prevent-scroll');
    });

  });

})(jQuery);
