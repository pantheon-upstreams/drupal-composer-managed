/**
 * Regions - Header Primary
 * Functionality to simply hide and show mobile menu.
 *
 * - 01 - Mobile Move
 * - 02 - Dropdown
 * - 03 - Drupal Attach
 */


/*------------------------------------*\
  01 - Mobile Move
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

      if (wrapper.classList.contains('menu__name--main')) {
        const branding = parent.querySelector('.site-branding');
        branding.after(wrapper);
      } else {
        parent.prepend(wrapper);
      }
    } else {
      state = 'mobile';

      if (wrapper.classList.contains('search-form')) {
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
  02 - Dropdown
  Simply changing a set of classes, attributes and states upon either a click,
  or keyboard action.
\*------------------------------------*/

const dropdown = (wrapper) => {
  // Constructor
  const openClass = 'is-open';
  let flyout;
  let trigger;

  const close = () => {
    wrapper.classList.remove(openClass);
    trigger.setAttribute('aria-expanded', 'false');
  };

  const open = () => {
    wrapper.classList.add(openClass);
    trigger.setAttribute('aria-expanded', 'true');
  }

  const onClickTrigger = (event) => {
    if (wrapper.classList.contains(openClass)) {
      close()
    } else {
      open()
    }
  };

  const onKeydownTrigger = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      if (wrapper.classList.contains(openClass)) {
        close()
      } else {
        open()
      }
    }

    if (event.key === 'Escape' || event.key === 'End') {
      close();
    }
  };

  const onClickWrapper = (event) => {
    if (event.target === flyout) {
      close();
    }
  }

  /**
   * Initialization
   * Add any and all functionality as a singular program, dynamically setting
   * element variables, states and event listeners.
   * @param wrapper - [HTMLObject] Element that contains all elements contained
   * inside the eventual modal.
   */
  const init = (wrapper) => {
    flyout = wrapper.querySelector('.mobile-menu__flyout');
    trigger = wrapper.querySelector('.mobile-menu__trigger');
    trigger.addEventListener('click', onClickTrigger);
    trigger.addEventListener('keydown', onKeydownTrigger);
    wrapper.addEventListener('click', onClickWrapper)
  };

  // Final Return
  init (wrapper);
};




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfMobileMenu = {
  attach(context) {
    const mobileMenu = once('surf-mobile-menu', context.querySelector('.mobile-menu'));

    if (mobileMenu.length !== 0) {
      dropdown(mobileMenu[0])
    }
  },
}

Drupal.behaviors.surfMobileMove = {
  attach(context) {
    const wrappers = [
      {
        'id': 'surf-move-mobile-01',
        'class': '.search-form',
        'breakpoint': 992,
      },
      {
        'id': 'surf-move-mobile-02',
        'class': '.menu__name--main',
        'breakpoint': 1400,
      },
      {
        'id': 'surf-move-mobile-03',
        'class': '.menu__name--utility-nav',
        'breakpoint': 992,
      },
    ];

    const destination = once('surf-move-mobile-destination', context.querySelector('.mobile-menu__flyout-content'));

    wrappers.forEach((wrapper) => {
      const element = once(wrapper.id, context.querySelector(wrapper.class));

      if (element.length !== 0 && destination.length !== 0) {
        mobileMove(element[0], destination[0], wrapper.breakpoint);
      }
    });
  }
}
