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
        const slides = element.querySelectorAll('.m-gallery__slide');

        // Instantiating Splide with options.
        const splide = new Splide(element, {
          autoWidth: true,
          height: '660px',
          breakpoints: {
            768: {
              height: '330px',
              gap: 24,
            },
            1200: {
              height: '434px',
              gap: 32,
            },
          },
          focus: 'center',
          gap: 40,
          pagination: false,
          type: 'loop',
        });

        splide.on('ready', () => {
          // Because we are using the Splide `autoWidth` feature, we need to set
          // the width of each slide to be the width of the image.
          // slides.forEach((slide) => {
          //   const image = slide.querySelector('img');
          //   const imageWidth = image.width;
          //   slide.style.width = `${imageWidth}px`;
          // });
        });

        // Final mount of Splide gallery.
        splide.mount();



        // Because we are using the Splide `autoWidth` feature, we need to set
        // the width of each slide to be the width of the image.
        slides.forEach((slide) => {
          const image = slide.querySelector('img');
          const imageWidth = image.width;
          slide.style.width = `${imageWidth}px`;
        });
      });
    }
  },
}
