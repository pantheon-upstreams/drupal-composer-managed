/**
 * Accessible Menu
 * Functionality that is accessible to all users that's applied to menus.
 */

export const a11yMenu = (wrapper, breakpoint) => {
  // Constructor
  const mediaQuery = window.matchMedia(`(min-width: ${breakpoint}px)`);
  const openClass = 'is-open';
  let links;
  let state;

  /**
   * Close Dropdown
   * Applies any necessary measures to consider an element hidden.
   * @param element - [HTMLObject] element to act upon
   * @see closeAllDropdowns
   * @see onClickDropdownLink
   * @see onKeydownDropdownLink
   * @see onKeydownLink
   */
  const closeDropdown = (element) => {
    element.parentElement.classList.remove(openClass);
    element.setAttribute('aria-expanded', 'false');
  };

  /**
   * Open Dropdown
   * Applies any necessary measures to consider an element open.
   * @param element - [HTMLObject] element to act upon
   * @see onClickDropdownLink
   * @see onKeydownDropdownLink
   */
  const openDropdown = (element) => {
    element.parentElement.classList.add(openClass);
    element.setAttribute('aria-expanded', 'true');
  }

  /**
   * Close All Dropdowns
   * Applies any necessary measures to consider all opened dropdowns closed.
   * @see onClickDropdownLink
   * @see onKeydownDropdownLink
   * @see onKeydownLink
   * @see onWindowClick
   */
  const closeAllDropdowns = () => {
    links.forEach((link) => {
      closeDropdown(link);
    });
  }

  /**
   * Dropdown Link Click
   * Any measures that need to be taken upon a click event.
   * @param event - [object] the exact action take by a user.
   * @see init
   */
  const onClickDropdownLink = (event) => {
    if (event.currentTarget.parentElement.classList.contains(openClass)) {
      closeDropdown(event.currentTarget);
    } else {
      if (state === 'desktop') {
        // To prevent multiple dropdown sub-menus from being open, first collapse all.
        closeAllDropdowns();
      }

      openDropdown(event.currentTarget);
    }
  };

  /**
   * Dropdown Link Keydown
   * Any measures that need to be taken upon a keyboard event.
   * @param event - [object] the exact action take by a user.
   * @see init
   */
  const onKeydownDropdownLink = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      if (event.currentTarget.parentElement.classList.contains(openClass)) {
        closeDropdown(event.currentTarget);
      } else {
        if (state === 'desktop') {
          // To prevent multiple dropdown sub-menus from being open, first collapse all.
          closeAllDropdowns();
        }

        openDropdown(event.currentTarget);
      }
    }
  };

  /**
   * Link Keydown
   * Any measures that need to be taken on a link keyboard event.
   * @param event - [object] the exact action take by a user.
   * @see init
   */
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
   * @param event - [object] details regarding the specific event taken.
   * @see init
   */
  const onWindowClick = (event) => {
    if (state === 'desktop') {
      // Close all dropdown sub-menus if clicking outside the menu system.
      if (!wrapper.contains(event.target)) {
        closeAllDropdowns();
      }
    }
  };

  /**
   * Media Query Change
   * Any events needing to be taken when a specific breakpoint has been activated.
   * @see init
   */
  const onMediaQueryChange = () => {
    if (mediaQuery.matches) {
      state = 'desktop';
    } else {
      state = 'mobile';
    }
  };

  /**
   * Initialization
   * Add any and all functionality as a singular program, dynamically setting
   * element variables, states and event listeners.
   * @param wrapper - [HTMLObject] Element that contains all elements contained
   * inside the eventual modal.
   * @param breakpoint - [integer] Window/media size in which to perform an action.
   */
  const init = (wrapper, breakpoint) => {
    links = wrapper.querySelectorAll('.m-menu__link');

    links.forEach((link) => {
      if (link.getAttribute('aria-haspopup')) {
        link.addEventListener('click', onClickDropdownLink);
        link.addEventListener('keydown', onKeydownDropdownLink);
      } else {
        link.addEventListener('keydown', onKeydownLink);
      }
    });

    window.addEventListener('click', onWindowClick);

    // Device or viewport change listener and on load.
    mediaQuery.addEventListener('change', onMediaQueryChange);
    onMediaQueryChange();
  };

  // Final Return
  init (wrapper, breakpoint);
};
