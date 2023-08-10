<?php

namespace Drupal\imce\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;

/**
 * Defines Imce selector plugin for CKEditor5.
 *
 * @CKEditor5Plugin(
 *   id = "imce_selector",
 *   ckeditor5 = @CKEditor5AspectsOfCKEditor5Plugin(
 *     plugins = { "imce.ImceSelector" },
 *   ),
 *   drupal = @DrupalAspectsOfCKEditor5Plugin(
 *     label = @Translation("Imce Selector"),
 *     library = "imce/drupal.imce.ckeditor5",
 *     elements = FALSE,
 *   ),
 * )
 */
class ImceCKEditor5Selector extends CKEditor5PluginDefault {

}
