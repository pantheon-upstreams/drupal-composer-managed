(($, Drupal) => {

  Drupal.behaviors.accordion = {
    attach: function attach(context) {

      $('.c-accordion', context).each(function (index, item) {
        const $this = $(this);
        let $buttons = $this.find('button[aria-expanded]');

        $buttons.each(function(index, item){
          $(this).click(function () {
            const $button = $(this);
            if($button.attr('aria-expanded') === 'true'){
              $button.attr('aria-expanded', 'false');
              // Hide the panel.
              $(`#${$button.attr('aria-controls')}`).attr('hidden', 'true');
              $button.removeAttr('hidden');
            } else {
              $button.attr('aria-expanded', 'true');
              // Show the panel.
              $(`#${$button.attr('aria-controls')}`).removeAttr('hidden');
            }
          })
        })
      });

    },
  };
})(jQuery, Drupal)
