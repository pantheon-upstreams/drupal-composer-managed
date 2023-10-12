<?php

namespace Drupal\surf_core\Plugin\WebformContentCreator\FieldMapping;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\webform_content_creator\Plugin\FieldMappingBase;

/**
 * Provides a name field mapping.
 *
 * @WebformContentCreatorFieldMapping(
 *   id = "name_mapping",
 *   label = @Translation("Name"),
 *   weight = 0,
 *   field_types = {
 *     "name"
 *   },
 * )
 */
class NameFieldMapping extends FieldMappingBase {

  public function getSupportedWebformFields($webform_id) {
    $supported_types = [
      "webform_name",
    ];

    return $this->filterWebformFields($webform_id, $supported_types);
  }

  public function mapEntityField(ContentEntityInterface &$content, array $webform_element, FieldDefinitionInterface $field_definition, array $data = [], array $attributes = []) {
    $field_id = $field_definition->getName();
    $field_value = $field_definition->getDefaultValue($content);
    $field_value = !empty($field_value[0]) ? reset($field_value) : [];

    $map = [
      'title' => 'title',
      'given' => 'first',
      'middle' => 'middle',
      'family' => 'last',
      'generational' => 'suffix',
      'credentials' => 'degree',
    ];

    foreach ($map as $destination => $source) {
      if (!empty($data[$field_id][$source])) {
        $field_value[$destination] = $data[$field_id][$source];
      }
    }

    $content->set($field_id, $field_value);
  }
}
