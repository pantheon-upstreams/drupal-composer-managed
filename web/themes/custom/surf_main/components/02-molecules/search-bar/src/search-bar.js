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

const hideShow = (wrapper) => {
  // Constructor
  const openedClass = 'm-search-bar--open';
  const closedClass = 'm-search-bar--closed';
  let trigger;

  const openedState = () => {
    wrapper.classList.remove(closedClass);
    wrapper.classList.add(openedClass);
    trigger.setAttribute('aria-expanded', 'true');
  };

  const closedState = (element) => {
    element.classList.add(closedClass);
    element.classList.remove(openedClass);
    trigger.setAttribute('aria-expanded', 'false');
  };

  const onClickTrigger = (event) => {
    if (wrapper.classList.contains(closedClass)) {
      openedState(wrapper);
    } else {
      closedState(wrapper);
    }
  };

  const onKeydownTrigger = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      if (wrapper.classList.contains(closedClass)) {
        openedState(wrapper);
      } else {
        closedState(wrapper);
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
    trigger = wrapper.querySelector('#search-trigger');
    trigger.addEventListener('click', onClickTrigger);
    trigger.addEventListener('keydown', onKeydownTrigger);
  };

  // Final Return
  init (wrapper);
};




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfSearchBar = {
  attach(context) {
    const searchBar = once('surf-search-api-form-block', context.querySelectorAll('.m-search-bar'));

    if (searchBar.length !== 0) {
      searchBar.forEach((block) => {
        hideShow(block);
      });
    }
  },
}
