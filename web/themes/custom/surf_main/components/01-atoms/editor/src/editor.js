/**
 * Components - Atom - Editor
 *
 * - 01 - Table Wrap
 * - 02 - Drupal Attach
 */


/*------------------------------------*\
  01 - Table Wrap
\*------------------------------------*/

const a11yTables = (element) => {
  // Constructor
  let wrapper;

  /**
   * Init
   * Used to fully initialize the entire object and it's elements, along with
   * add event listeners where necessary, and function initializations.
   * @param element - [HTMLObject] Element for which to act upon.
   */
  const init = (element) => {
    wrapper = document.createElement('div');
    wrapper.classList.add('a-editor__table');
    element.before(wrapper);
    wrapper.appendChild(element);
  };

  // Run Entire Program / Object
  init(element);
};




/*------------------------------------*\
  02 - Drupal Attach
  Attach any previously defined functionality into Drupal behaviors.
  https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview
\*------------------------------------*/

Drupal.behaviors.surfEditor = {
  attach(context) {
    const tables = once('surf-table', context.querySelectorAll('.a-editor table'));

    if (tables.length !== 0) {
      tables.forEach((table) => {
        a11yTables(table);
      })
    }
  }
}











// Drupal.behaviors.surfEditor = {
//   attach(context) {
//     const tables = once('surf-table', context.querySelectorAll('.a-editor table'));
//
//     if (tables.length !== 0) {
//       tables.forEach((table) => {
//         a11yTables(table);
//       });
//     }
//   },
// }
