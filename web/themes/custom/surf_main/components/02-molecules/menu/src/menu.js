/**
 * Blocks - Search API
 * Functionality to simply hide and show the search form.
 *
 * - 01 - Imports
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import { a11yMenu } from '../../../00-base/libraries/a11y-menu';




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfPrimaryMenu = {
  attach(context) {
    const mainMenu = once('surf-primary-menu', context.querySelectorAll('.m-menu.m-menu--primary'));

    if (mainMenu.length !== 0) {
      mainMenu.forEach((menu) => {
        a11yMenu(menu, 1440);
      });
    }
  },
}
