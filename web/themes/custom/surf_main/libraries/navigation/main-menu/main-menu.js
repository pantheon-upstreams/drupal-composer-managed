/**
 * Blocks - Search API
 * Functionality to simply hide and show the search form.
 *
 * - 01 - Hide / Show
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Hide / Show
  Simply changing a set of classes, attributes and states upon either a click,
  or keyboard action.
\*------------------------------------*/

const menuSystem = (wrapper) => {
  // Constructor
  const openClass = 'is-open';
  let links;

  const closeDropdown = (element) => {
    element.parentElement.classList.remove(openClass);
    element.setAttribute('aria-expanded', 'false');
  };

  const openDropdown = (element) => {
    element.parentElement.classList.add(openClass);
    element.setAttribute('aria-expanded', 'true');
  }

  const closeAllDropdowns = () => {
    links.forEach((link) => {
      closeDropdown(link);
    });
  }

  const onClickDropdownLink = (event) => {
    if (event.currentTarget.parentElement.classList.contains(openClass)) {
      closeDropdown(event.currentTarget);
    } else {
      // To prevent multiple dropdown sub-menus from being open, first collapse all.
      closeAllDropdowns();
      openDropdown(event.currentTarget);
    }
  };

  const onKeydownDropdownLink = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      if (event.currentTarget.parentElement.classList.contains(openClass)) {
        closeDropdown(event.currentTarget);
      } else {
        // To prevent multiple dropdown sub-menus from being open, first collapse all.
        closeAllDropdowns();
        openDropdown(event.currentTarget);
      }
    }
  };

  const onKeydownLink = (event) => {
    // Close dropdown sub-menu and refocus on parent button, if available.
    if (event.key === 'Escape' || event.key === 'End') {
      if (event.currentTarget.closest(`.${openClass}`)) {
        const opened = event.currentTarget.closest(`.${openClass}`);
        const openedTrigger = opened.firstElementChild;

        opened.classList.remove(openClass);
        openedTrigger.setAttribute('aria-expanded', 'false');

        setTimeout(focus => {
          openedTrigger.focus();
        }, 1);
      }
    }

    // Collapse all menus if tabbing on the last link of the menu.
    if (!event.shiftKey && event.key === 'Tab') {
      if (event.currentTarget === links[links.length - 1]) {
        closeAllDropdowns();
      }
    }
  };

  /**
   * Window Click
   * Any events associated with clicking anywhere within the browser window.
   * @param event: [object] details regarding the specific event taken.
   * @see init
   */
  const onWindowClick = (event) => {
    // Close all dropdown sub-menus if clicking outside the menu system.
    if (!wrapper.contains(event.target)) {
      closeAllDropdowns();
    }
  };

  /**
   * Initialization
   * Add any and all functionality as a singular program, dynamically setting
   * element variables, states and event listeners.
   * @param wrapper: [HTMLObject] Element that contains all elements contained
   * inside the eventual modal.
   */
  const init = (wrapper) => {
    links = wrapper.querySelectorAll('.menu__link');

    links.forEach((link) => {
      if (link.getAttribute('aria-haspopup')) {
        link.addEventListener('click', onClickDropdownLink);
        link.addEventListener('keydown', onKeydownDropdownLink);
      } else {
        link.addEventListener('keydown', onKeydownLink);
      }
    });

    window.addEventListener('click', onWindowClick);
  };

  // Final Return
  init (wrapper);
};




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfMainMenu = {
  attach(context) {
    const mainMenu = once('surf-main-menu', context.querySelector('.menu.menu__name--main'));

    if (mainMenu.length !== 0) {
      menuSystem(mainMenu[0]);
    }
  },
}
