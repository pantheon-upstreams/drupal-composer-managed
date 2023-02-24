(($, Drupal) => {

  Drupal.behaviors.accordion = {
    attach: function attach(context) {

      $('.c-accordion').each(function (index, item) {
        const $this = $(this);
        let $buttons = $this.find('button[aria-expanded]');

        $buttons.each(function(index, item){
          $(this).click(function () {
            if($(this).attr('aria-expanded') === 'true'){
              $(this).attr('aria-expanded', 'false');
              $(this).parent().next('.c-accordion__panel').attr('hidden', 'true');
              $(this).removeAttr('hidden');
            } else {
              $(this).attr('aria-expanded', 'true');
              $(this).parent().next('.c-accordion__panel').removeAttr('hidden');
            }
          })
        })
      });

    },
  };
})(jQuery, Drupal)
