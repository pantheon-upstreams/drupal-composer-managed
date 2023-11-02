/**
 *
 */

export const a11yAccordion = (wrapper) => {
  // Constructor
  const expandedClass = 'expanded';
  let accordionItems;
  let accordionItem;
  let accordionTrigger;

  const expand = (item, trigger) => {
    item.classList.add(expandedClass);
    trigger.setAttribute('aria-expanded', 'true');
  }

  const collapse = (item, trigger) => {
    item.classList.remove(expandedClass);
    trigger.setAttribute('aria-expanded', 'false');
  }

  const onClickTrigger = (event) => {
    accordionItem = event.currentTarget.parentElement.parentElement;
    accordionTrigger = event.currentTarget;

    if (accordionItem.classList.contains(expandedClass)) {
      collapse(accordionItem, accordionTrigger);
    } else {
      expand(accordionItem, accordionTrigger);
    }
  };

  const onKeydownTrigger = (event) => {
    accordionItem = event.currentTarget.parentElement.parentElement;
    accordionTrigger = event.currentTarget;

    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();

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
   * inside the eventual modal.
   */
  const init = (wrapper) => {
    accordionItems = wrapper.querySelectorAll('.accordion__item');

    accordionItems.forEach((item) => {
      const accordionTrigger = item.querySelector('.accordion__trigger');
      accordionTrigger.addEventListener('click', onClickTrigger);
      accordionTrigger.addEventListener('keydown', onKeydownTrigger);
    })
  };

  // Final Return
  init (wrapper);
};
