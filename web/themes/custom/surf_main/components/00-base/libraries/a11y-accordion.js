/**
 * Accessible Accordion
 * Functionality for an Accordion or group of Accordions using accessible methods
 * as described by WCAG: https://www.w3.org/WAI/ARIA/apg/patterns/accordion/
 */

export const a11yAccordion = (wrapper) => {
  // Constructor
  const expandedClass = 'expanded';
  let accordionItems;
  let accordionItem;
  let accordionTrigger;

  /**
   * Expand
   * Any steps taken to consider an accordion element expanded.
   * @param item - [HTMLObject] accordion blade wrapper element.
   * @param trigger - [HTMLObject] accordion blade\\ button element.
   * @see onClickTrigger
   * @see onKeydownTrigger
   */
  const expand = (item, trigger) => {
    item.classList.add(expandedClass);
    trigger.setAttribute('aria-expanded', 'true');
  }

  /**
   * Collapse
   * Any steps taken to consider an accordion element collapsed.
   * @param item - [HTMLObject] accordion blade wrapper element.
   * @param trigger - [HTMLObject] accordion blade button element.
   * @see onClickTrigger
   * @see onKeydownTrigger
   */
  const collapse = (item, trigger) => {
    item.classList.remove(expandedClass);
    trigger.setAttribute('aria-expanded', 'false');
  }

  /**
   * Click Trigger
   * Any events surrounding interactions to the accordion trigger with a click.
   * @param event - [EventObject] All attributes associated with an event.
   * @see init
   */
  const onClickTrigger = (event) => {
    accordionItem = event.currentTarget.parentElement.parentElement;
    accordionTrigger = event.currentTarget;

    // Check to see if class exists first.
    if (accordionItem.classList.contains(expandedClass)) {
      collapse(accordionItem, accordionTrigger);
    } else {
      expand(accordionItem, accordionTrigger);
    }
  };

  /**
   * Keydown Trigger
   * Any events surrounding interactions to the accordion trigger with a keyboard.
   * @param event - [EventObject] All attributes associated with an event.
   * @see init
   */
  const onKeydownTrigger = (event) => {
    accordionItem = event.currentTarget.parentElement.parentElement;
    accordionTrigger = event.currentTarget;

    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

      // Check to see if class exists first.
      if (accordionItem.classList.contains(expandedClass)) {
        collapse(accordionItem, accordionTrigger);
      } else {
        expand(accordionItem, accordionTrigger);
      }
    }
  };

  /**
   * Initialization
   * Add any and all functionality as a singular program, dynamically setting
   * element variables, states and event listeners.
   * @param wrapper - [HTMLObject] Element that contains all elements contained
   * inside the eventual accordion.
   */
  const init = (wrapper) => {
    accordionItems = wrapper.querySelectorAll('.accordion__item');

    accordionItems.forEach((item) => {
      console.log(item, 'item');
      const accordionTrigger = item.querySelector('.accordion__trigger');
      accordionTrigger.addEventListener('click', onClickTrigger);
      accordionTrigger.addEventListener('keydown', onKeydownTrigger);
    })
  };

  // Final Program Run
  init (wrapper);
};
