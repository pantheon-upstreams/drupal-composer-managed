/**
 * Accessible Dropdown
 * Functionality for show/hide features based upon accessible patterns as
 * described on W3C: https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
 */

export const a11yDropdown = (wrapper) => {
  // Constructor
  const openedClass = 'is-open';
  let overlay;
  let trigger;

  /**
   * Open
   * Any steps taken to consider a dropdown element open.
   * @see onClickTrigger
   * @see onKeydownTrigger
   */
  const open = () => {
    wrapper.classList.add(openedClass);
    trigger.setAttribute('aria-expanded', 'true');
  };

  /**
   * Close
   * Any steps taken to consider a dropdown element closed.
   * @see onClickTrigger
   * @see onKeydownTrigger
   */
  const close = () => {
    wrapper.classList.remove(openedClass);
    trigger.setAttribute('aria-expanded', 'false');
  };

  /**
   * Click Trigger
   * Any events surrounding interactions to the dropdown trigger with a click.
   * @param event - [EventObject] All attributes associated with an event.
   * @see init
   */
  const onClickTrigger = (event) => {
    // Check to see if class exists first.
    if (wrapper.classList.contains(openedClass)) {
      close(wrapper);
    } else {
      open(wrapper);
    }
  };

  /**
   * Keydown Trigger
   * Any events surrounding interactions to the dropdown trigger with a keyboard.
   * @param event - [EventObject] All attributes associated with an event.
   * @see init
   */
  const onKeydownTrigger = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      // Check to see if class exists first.
      if (wrapper.classList.contains(openedClass)) {
        close(wrapper);
      } else {
        open(wrapper);
      }
    }
  };

  /**
   * Overlay Click
   * Any events surrounding interactions on the overlay of the dropdown, but not
   * the dropdown itself.
   * @param event - [EventObject] All attributes associated with an event.
   * @see init
   */
  const onOverlayClick = (event) => {
    if (event.target === overlay) {
      close(wrapper);
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
    overlay = wrapper.querySelector('.dropdown__content-wrapper');
    trigger = wrapper.querySelector('.dropdown__trigger');
    trigger.addEventListener('click', onClickTrigger);
    trigger.addEventListener('keydown', onKeydownTrigger);

    if (overlay) {
      overlay.addEventListener('click', onOverlayClick);
    }
  };

  // Final Program Run
  init (wrapper);
};
