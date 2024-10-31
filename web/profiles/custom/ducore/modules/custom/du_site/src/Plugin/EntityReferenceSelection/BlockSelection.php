<?php

namespace Drupal\du_site\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Component\Utility\Html;

/**
 * Filters the block listing for an entity reference field.
 *
 * @EntityReferenceSelection(
 *   id = "blocks",
 *   label = @Translation("Blocks: Filter by blocks in hidden region"),
 *   entity_types = {"block"},
 *   group = "blocks",
 *   weight = 1
 * )
 */
class BlockSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $target_type = $this->configuration['target_type'];

    $query = $this->buildEntityQuery($match, $match_operator);

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    $options = [];
    $entities = $this->entityTypeManager->getStorage($target_type)->loadMultiple($result);

    foreach ($entities as $entity_id => $entity) {
      $block_region = $entity->getRegion();

      if ($block_region == 'hidden') {
        $bundle = $entity->bundle();
        $options[$bundle][$entity_id] = Html::escape($this->entityRepository->getTranslationFromContext($entity)->label());
      }
    }

    // Sort the options alphabetically.
    asort($options['block']);

    return $options;
  }

}
