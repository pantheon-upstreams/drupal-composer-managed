<?php

namespace Drupal\imce\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;

/**
 * Defines Imce link plugin for CKEditor5.
 *
 * @CKEditor5Plugin(
 *   id = "imce_link",
 *   ckeditor5 = @CKEditor5AspectsOfCKEditor5Plugin(
 *     plugins = { "imce.ImceLink" },
 *   ),
 *   drupal = @DrupalAspectsOfCKEditor5Plugin(
 *     label = @Translation("Imce Link"),
 *     library = "imce/drupal.imce.ckeditor5",
 *     admin_library = "imce/drupal.imce.admin",
 *     elements = { "<a>", "<a href>" },
 *     toolbar_items = {
 *       "imce_link" = {
 *         "label" = "Insert file links using Imce File Manager",
 *       },
 *     },
 *   ),
 * )
 */
class ImceCKEditor5Link extends CKEditor5PluginDefault {

}
