/**
 * Scrolled
 * Functionality to simply apply a class upon a scroll amount.
 */

export const scrolled = (wrapper, amount) => {
  // Constructor
  const scrolledClass = 'scrolled';

  /**
   * On Scroll
   * Used to determine scroll amount and apply functionality.
   * @see init
   */
  const onScroll = () => {
    if (window.scrollY > amount) {
      wrapper.classList.add(scrolledClass);
    } else {
      wrapper.classList.remove(scrolledClass);
    }
  };

  /**
   * Init
   * Used to fully initialize the entire object and it's elements, along with
   * add event listeners where necessary, and function initializations.
   * @param wrapper - [HTMLObject] Element for which to act upon.
   * @param amount - [Integer] Amount of pixels scrolled for which to apply scroll effect.
   */
  const init = (wrapper, amount) => {
    document.addEventListener('scroll', onScroll);
  };

  // Run Entire Program / Object
  init(wrapper, amount);
};