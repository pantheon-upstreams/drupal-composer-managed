
((Drupal, once) => {
  Drupal.behaviors.surf_teaser_collapse = {
    attach(context, _settings) {

      once('standards', '.c-teaser__tags', context).forEach((standardsElement) => {

        const trigger = standardsElement.querySelector('.c-teaser__trigger');

        const standardsCount = standardsElement.querySelector('.c-teaser__trigger').getAttribute('data-standards-count');

        var tags = standardsElement.querySelectorAll('.c-teaser__tag.standards');

        // Initially hide the tags
        tags.forEach(function(tag) {
          tag.classList.add('hidden');
        });

        // Initially hide the unordered list
        standardsElement.classList.add('hidden');


        trigger.addEventListener('click', function() {
          // Toggle the visibility of the tags
          tags.forEach(function(tag) {
            tag.classList.toggle('hidden');
          });

          var expanded = trigger.getAttribute('aria-expanded') === 'true' || false;
          trigger.setAttribute('aria-expanded', !expanded);

          // Change the trigger text based on the visibility state
          if (expanded) {
            trigger.textContent = 'View Standards' + ' (' + standardsCount + ')';
          } else {
            trigger.textContent = 'Hide Standards' + ' (' + standardsCount + ')';
          }

        });
      });
    }
  }
})(Drupal, once);
