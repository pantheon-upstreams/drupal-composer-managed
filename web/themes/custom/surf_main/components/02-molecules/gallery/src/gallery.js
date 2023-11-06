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
        const splide = new Splide(element, {
          breakpoints: {
            768: {
              gap: 24,
            },
            1200: {
              gap: 32,
            },
          },
          gap: 40,
          pagination: false,
          type: 'loop',
        });

        splide.mount();
      });
    }
  },
}
