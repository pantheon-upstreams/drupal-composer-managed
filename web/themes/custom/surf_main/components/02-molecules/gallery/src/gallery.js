/**
 * Components - Molecule - Gallery
 *
 * - 01 - Imports
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import Splide from '@splidejs/splide';




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfGallery = {
  attach(context) {
    const galleries = once('surf-gallery', context.querySelectorAll('.m-gallery'));

    if (galleries.length !== 0) {
      galleries.forEach((element) => {
        // Instantiating Splide with options.
        const splide = new Splide(element, {
          breakpoints: {
            768: {
              gap: 8,
            },
            1200: {
              gap: 16,
            },
            1440: {
              gap: 24,
            }
          },
          focus: 'center',
          gap: 40,
          pagination: false,
          type: 'loop',
        });

        splide.mount();
      });
    }
  },
}
