/**
 * Components - Molecule - Gallery
 *
 * - 01 - Imports
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import 'particles.js';




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfSection = {
  attach(context) {
    const sections = once('surf-section', context.querySelectorAll('.l-section--blue-gradient'));

    if (sections.length !== 0) {
      sections.forEach((section) => {
        particlesJS.load("particles-js", 'themes/custom/surf_main/components/03-organisms/section/src/particlesjs-config.json')
      });
    }
  },
}
