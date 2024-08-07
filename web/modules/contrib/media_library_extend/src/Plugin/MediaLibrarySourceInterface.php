<?php

namespace Drupal\media_library_extend\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an interface for media library pane plugins.
 */
interface MediaLibrarySourceInterface {

  /**
   * Sets values for the current display state to be used in the plugin.
   *
   * @param array $values
   *   The values to store.
   */
  public function setValues(array $values);

  /**
   * Sets values for the current display state to be used in the plugin.
   *
   * @param string $key
   *   The key to set a value for.
   * @param mixed $value
   *   The value to store.
   */
  public function setValue(string $key, $value);

  /**
   * Gets a value for the plugin's display state.
   *
   * @param string $key
   *   The key to get a value for.
   *
   * @return mixed
   *   The value as stored in self::setValues().
   */
  public function getValue(string $key);

  /**
   * Sets the entity bundle that should be returned by this plugin.
   *
   * @param string $bundle
   *   The target bundle id for this plugin.
   */
  public function setTargetBundle(string $bundle);

  /**
   * Gets the entity bundle that should be returned by this plugin.
   *
   * @return string
   *   The target bundle id for this plugin.
   */
  public function getTargetBundle();

  /**
   * Gets this plugin's label.
   *
   * @return string
   *   The plugin's label.
   */
  public function label();

  /**
   * Returns a render array summarizing the configuration of the plugin.
   *
   * @return array
   *   A render array.
   */
  public function getSummary();

  /**
   * Gets the total result count for this plugin.
   */
  public function getCount();

  /**
   * Gets result items to be previewed.
   *
   * @return array
   *   An array of result items using the following keys:
   *   - id: The result item's id, to be passed to getEntityId when this item is
   *     selected.
   *   - label: The result item's label.
   *   - preview: A rendered preview of the result item.
   */
  public function getResults();

  /**
   * Builds the plugin's filter form.
   *
   * If no elements are added to the form, no filters will be shown.
   *
   * @param array $form
   *   The filter form to modify.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the filter form.
   *
   * @return array
   *   The modified filter form.
   */
  public function buildForm(array &$form, FormStateInterface $form_state);

  /**
   * Converts plugin result ids to Drupal entity ids.
   *
   * @param string $selected_id
   *   The id selected in the form as supplied by self::getResults().
   *
   * @return int
   *   The id of a Drupal entity that represents the selected item.
   */
  public function getEntityId(string $selected_id);

}
