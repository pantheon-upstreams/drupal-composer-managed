<?php

namespace Drupal\du_site;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\Core\Entity\EntityAutocompleteMatcherInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Matcher class to get autocompletion results for entity reference.
 */
class EntityAutocompleteMatcher implements EntityAutocompleteMatcherInterface {

  /**
   * The entity reference selection handler plugin manager.
   *
   * @var \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface
   */
  protected $selectionManager;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a EntityAutocompleteMatcher object.
   *
   * @param \Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface $selection_manager
   *   The entity reference selection handler plugin manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The date formatter.
   */
  public function __construct(SelectionPluginManagerInterface $selection_manager, DateFormatterInterface $date_formatter, EntityTypeManagerInterface $entity_type_manager) {
    $this->selectionManager = $selection_manager;
    $this->dateFormatter = $date_formatter;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {
    $matches = [];
    $file_storage = $this->entityTypeManager->getStorage('file');

    if ($target_type == 'file') {
      $options = $selection_settings + [
        'target_type' => 'media',
        'handler' => $selection_handler,
      ];
      $handler = $this->selectionManager->getInstance($options);

      if (isset($string)) {
        $media_storage = $this->entityTypeManager->getStorage('media');

        // Get an array of matching entities.
        $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
        $match_limit = isset($selection_settings['match_limit']) ? (int) $selection_settings['match_limit'] : 10;
        $entity_labels = $handler->getReferenceableEntities($string, $match_operator, $match_limit);

        // Loop through the entities and convert them into autocomplete output.
        foreach ($entity_labels as $bundle => $values) {
          if (in_array($bundle, ['image', 'document'])) {
            $entities = $media_storage->loadMultiple(array_keys($values));
            foreach ($values as $entity_id => $label) {
              // Load the attached file.
              if ($bundle == 'image') {
                $file = $file_storage->load($entities[$entity_id]->image->target_id);
              }
              elseif ($bundle == 'document') {
                $file = $file_storage->load($entities[$entity_id]->field_document->target_id);
              }

              // Set the key to whatever the attached file is since we're not
              // actually trying to select the media entity, but the file
              // entity.
              $filename = $file->filename->value;
              $fid = $file->id();
              $key = "$filename ($fid)";

              // Get the file last changed date.
              $timestamp = $file->get('changed')->value;
              $format_date = $this->dateFormatter->format($timestamp, 'short', NULL, 'America/Denver');

              // Set the label to show the media name, filename, and date.
              $label .= ' - ' . $filename;
              if (!empty($format_date)) {
                $label .= " ($fid, $format_date)";
              }

              // Strip things like starting/trailing white spaces, line breaks
              // and tags.
              $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));

              // Names containing commas or quotes must be wrapped in quotes.
              $key = Tags::encode($key);
              $matches[] = ['value' => $key, 'label' => $label];
            }
          }
        }
      }
    }

    $options = $selection_settings + [
      'target_type' => $target_type,
      'handler' => $selection_handler,
    ];
    $handler = $this->selectionManager->getInstance($options);

    if (isset($string)) {
      // Get an array of matching entities.
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $match_limit = isset($selection_settings['match_limit']) ? (int) $selection_settings['match_limit'] : 10;
      $entity_labels = $handler->getReferenceableEntities($string, $match_operator, $match_limit);

      // Loop through the entities and convert them into autocomplete output.
      foreach ($entity_labels as $values) {
        $entities = $file_storage->loadMultiple(array_keys($values));
        foreach ($values as $entity_id => $label) {
          $file = $entities[$entity_id];
          $key = "$label ($entity_id)";

          // When on files we want to add the last changed date to the label.
          if ($target_type == 'file') {
            $timestamp = $file->get('changed')->value;
            $format_date = $this->dateFormatter->format($timestamp, 'short', NULL, 'America/Denver');

            // Set the label to show the date.
            if (!empty($format_date)) {
              $label .= " ($entity_id, $format_date)";
            }
          }

          // Strip things like starting/trailing white spaces, line breaks and
          // tags.
          $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));

          // Names containing commas or quotes must be wrapped in quotes.
          $key = Tags::encode($key);
          $matches[] = ['value' => $key, 'label' => $label];
        }
      }
    }

    return $matches;
  }

}
