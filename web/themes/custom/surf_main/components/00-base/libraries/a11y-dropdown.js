/**
 *
 */

export const a11yDropdown = (wrapper) => {
  // Constructor
  const openedClass = 'is-open';
  let overlay;
  let trigger;

  const open = () => {
    wrapper.classList.add(openedClass);
    trigger.setAttribute('aria-expanded', 'true');
  };

  const close = () => {
    wrapper.classList.remove(openedClass);
    trigger.setAttribute('aria-expanded', 'false');
  };

  const onClickTrigger = (event) => {
    if (wrapper.classList.contains(openedClass)) {
      close(wrapper);
    } else {
      open(wrapper);
    }
  };

  const onKeydownTrigger = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      if (wrapper.classList.contains(openedClass)) {
        close(wrapper);
      } else {
        open(wrapper);
      }
    }
  };

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

  // Final Return
  init (wrapper);
};
