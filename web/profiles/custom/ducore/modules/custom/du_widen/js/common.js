/**
 * @file common.js
 *
 * Common helper functions used by various parts of du_widen entity browser.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.du_widen = {};

  /**
   * Reacts on "entities selected" event.
   *
   * @param {object} event
   *   Event object.
   * @param {string} uuid
   *   Entity browser UUID.
   * @param {array} entities
   *   Array of selected entities.
   */
  Drupal.du_widen.selectionCompleted = function (event, uuid, entities) {
    var selected_entities = $.map(entities, function (item) {
      return item[2] + ':' + item[0];
    });
    // @todo Use uuid here. But for this to work we need to move eb uuid
    // generation from display to eb directly. When we do this, we can change
    // \Drupal\entity_browser\Plugin\Field\FieldWidget\EntityReferenceBrowserWidget::formElement
    // also.
    // Checking if cardinality is set - assume unlimited.
    var cardinality = isNaN(parseInt(drupalSettings['entity_browser'][uuid]['cardinality'])) ? -1 : parseInt(drupalSettings['entity_browser'][uuid]['cardinality']);

    // Get field widget selection mode.
    var selection_mode = drupalSettings['entity_browser'][uuid]['selection_mode'];

    // Update value form element with new entity IDs.
    var selector = drupalSettings['entity_browser'][uuid]['selector'] ? $(drupalSettings['entity_browser'][uuid]['selector']) : $(this).parent().parent().find('input[type*=hidden]');
    var entity_ids = selector.val();
    var existing_entities = (entity_ids.length !== 0) ? entity_ids.split(' ') : [];

    entity_ids = Drupal.entityBrowser.updateEntityIds(
      existing_entities,
      selected_entities,
      selection_mode,
      cardinality
    );

    selector.val(entity_ids);
    selector.trigger('entity_browser_value_updated');

    // Add the entities to all selected fields.
    let image_fields = entities[0][3];
    if (image_fields != null) {
      for (const field of image_fields) {
        let input = 'input[name="' + field + '[target_id]"]';
        if (selector.attr('name') != field + '[target_id]' && $(input).length > 0) {
          $(input).val(entity_ids);
          $(input).trigger('entity_browser_value_updated');
        }
      }
    }
  };

}(jQuery, Drupal, drupalSettings));
