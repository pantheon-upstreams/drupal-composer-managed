<?php

namespace Drupal\devel;

/**
 * Interface for DevelDumper manager.
 *
 * @package Drupal\devel
 */
interface DevelDumperManagerInterface {

  /**
   * Dumps information about a variable.
   *
   * @param mixed $input
   *   The variable to dump.
   * @param string $name
   *   (optional) The label to output before variable, defaults to NULL.
   * @param string $plugin_id
   *   (optional) The plugin ID, defaults to NULL.
   */
  public function dump($input, $name = NULL, $plugin_id = NULL);

  /**
   * Returns a string representation of a variable.
   *
   * @param mixed $input
   *   The variable to dump.
   * @param string $name
   *   (optional) The label to output before variable.
   * @param string $plugin_id
   *   (optional) The plugin ID, defaults to NULL.
   * @param bool $load_references
   *   If the input is an entity, load the referenced entities.
   *
   * @return string
   *   String representation of a variable.
   */
  public function export($input, $name = NULL, $plugin_id = NULL, $load_references = FALSE);

  /**
   * Sets a message with a string representation of a variable.
   *
   * @param mixed $input
   *   The variable to dump.
   * @param string $name
   *   The label to output before variable.
   * @param string $type
   *   The message's type.
   * @param string $plugin_id
   *   The plugin ID.
   * @param bool $load_references
   *   If the input is an entity, load the referenced entities.
   */
  public function message($input, $name = NULL, $type = 'status', $plugin_id = NULL, $load_references = FALSE);

  /**
   * Logs a variable to a drupal_debug.txt in the site's temp directory.
   *
   * @param mixed $input
   *   The variable to log to the drupal_debug.txt log file.
   * @param string $name
   *   (optional) If set, a label to output before $data in the log file.
   * @param string $plugin_id
   *   (optional) The plugin ID, defaults to NULL.
   *
   * @return void|false
   *   Empty if successful, FALSE if the log file could not be written.
   *
   * @see dd()
   * @see http://drupal.org/node/314112
   */
  public function debug($input, $name = NULL, $plugin_id = NULL);

  /**
   * Wrapper for ::dump() and ::export().
   *
   * @param mixed $input
   *   The variable to dump.
   * @param string $name
   *   (optional) The label to output before variable, defaults to NULL.
   * @param bool $export
   *   (optional) Whether return string representation of a variable.
   * @param string $plugin_id
   *   (optional) The plugin ID, defaults to NULL.
   *
   * @return string|null
   *   String representation of a variable if $export is set to TRUE,
   *   NULL otherwise.
   */
  public function dumpOrExport($input, $name = NULL, $export = TRUE, $plugin_id = NULL);

  /**
   * Returns a render array representation of a variable.
   *
   * @param mixed $input
   *   The variable to export.
   * @param string $name
   *   The label to output before variable.
   * @param string $plugin_id
   *   The plugin ID.
   * @param bool $load_references
   *   If the input is an entity, also load the referenced entities.
   *
   * @return array
   *   String representation of a variable wrapped in a render array.
   */
  public function exportAsRenderable($input, $name = NULL, $plugin_id = NULL, $load_references = FALSE);

}
