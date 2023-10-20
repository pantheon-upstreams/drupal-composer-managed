/**
 * Components - Molecule - Search Bar
 *
 * - 01 - Imports
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import { a11yDropdown } from '../../../00-base/libraries/a11y-dropdown';




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfSearchBar = {
  attach(context) {
    const searchBar = once('surf-search-bar', context.querySelectorAll('.m-search-bar'));

    if (searchBar.length !== 0) {
      searchBar.forEach((element) => {
        a11yDropdown(element);
      });
    }
  },
}
