/**
 * Components - Molecule - Accordion
 *
 * - 01 - Imports
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import { a11yAccordion } from '../../../00-base/libraries/a11y-accordion';




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfAccordion = {
  attach(context) {
    const accordion = once('surf-accordion', context.querySelectorAll('.m-accordion'));

    if (accordion.length !== 0) {
      accordion.forEach((element) => {
        a11yAccordion(element);
      });
    }
  },
}
