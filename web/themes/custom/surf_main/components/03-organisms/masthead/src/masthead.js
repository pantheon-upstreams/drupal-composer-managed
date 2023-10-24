/**
 * Components - Organism - Masthead
 * Functionality to simply hide and show mobile menu.
 *
 * - 01 - Imports
 * - 02 - Mobile Move
 * - 03 - Drupal Attach
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

import { a11yDropdown } from '../../../00-base/libraries/a11y-dropdown';
import { scrolled } from '../../../00-base/libraries/scrolled';




/*------------------------------------*\
  02 - Mobile Move
  Simply changing a set of classes, attributes and states upon either a click,
  or keyboard action.
\*------------------------------------*/

const mobileMove = (wrapper, destination, breakpoint) => {
  // Constructor
  const mediaQuery = window.matchMedia(`(min-width: ${breakpoint}px)`);
  let parent;
  let state;

  /**
   * Media Query Change
   * Any events needing to be taken when a specific breakpoint has been activated.
   * @see init
   */
  const onMediaQueryChange = () => {
    if (mediaQuery.matches) {
      state = 'desktop';

      if (wrapper.classList.contains('m-menu--primary-menu')) {
        const branding = parent.querySelector('.site-branding');
        branding.after(wrapper);
      } else {
        parent.prepend(wrapper);
      }
    } else {
      state = 'mobile';

      if (wrapper.classList.contains('m-search-bar')) {
        destination.prepend(wrapper);
      } else {
        destination.append(wrapper);
      }
    }
  };

  /**
   * Initialization
   * Add any and all functionality as a singular program, dynamically setting
   * element variables, states and event listeners.
   * @param wrapper - [HTMLObject] Element that contains all elements contained
   * inside the eventual modal.
   */
  const init = (wrapper) => {
    parent = wrapper.parentElement;

    // Device or viewport change listener and on load.
    mediaQuery.addEventListener('change', onMediaQueryChange);
    onMediaQueryChange();
  };

  // Final Return
  init (wrapper, destination, breakpoint);
};



/*------------------------------------*\
  03 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfMasthead = {
  attach(context) {
    const masthead = once('surf-masthead', context.querySelector('.o-masthead'));
    const mastheadFlyout = once(
      'surf-masthead-flyout',
      context.querySelector('.o-masthead__flyout')
    );
    const mastheadFlyoutContent = once('surf-masthead-destination', context.querySelector('.o-masthead__flyout-content'));
    const mastheadMoveElements = [
      {
        'id': 'surf-move-mobile-01',
        'class': '.m-search-bar',
        'breakpoint': 992,
      },
      {
        'id': 'surf-move-mobile-02',
        'class': '.m-menu--primary-menu',
        'breakpoint': 1400,
      },
      {
        'id': 'surf-move-mobile-03',
        'class': '.m-menu--utility-menu',
        'breakpoint': 992,
      },
    ];

    if (mastheadFlyout.length !== 0) {
      a11yDropdown(mastheadFlyout[0]);
    }

    if (masthead.length !== 0) {
      scrolled(masthead[0], 60);
    }

    mastheadMoveElements.forEach((element) => {
      const moveElement = once(element.id, context.querySelector(element.class));

      if (element.length !== 0 && mastheadFlyoutContent.length !== 0) {
        mobileMove(moveElement[0], mastheadFlyoutContent[0], element.breakpoint);
      }
    });
  },
}
