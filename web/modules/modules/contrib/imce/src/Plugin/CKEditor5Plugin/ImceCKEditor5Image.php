<?php

namespace Drupal\imce\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;

/**
 * Defines Imce image plugin for CKEditor5.
 *
 * @CKEditor5Plugin(
 *   id = "imce_image",
 *   ckeditor5 = @CKEditor5AspectsOfCKEditor5Plugin(
 *     plugins = { "imce.ImceImage" },
 *   ),
 *   drupal = @DrupalAspectsOfCKEditor5Plugin(
 *     label = @Translation("Imce Image"),
 *     library = "imce/drupal.imce.ckeditor5",
 *     admin_library = "imce/drupal.imce.admin",
 *     elements = {
 *       "<img>",
 *       "<img src alt height width data-entity-type data-entity-uuid>",
 *     },
 *     toolbar_items = {
 *       "imce_image" = {
 *         "label" = "Insert images using Imce File Manager",
 *       },
 *     },
 *   ),
 * )
 */
class ImceCKEditor5Image extends CKEditor5PluginDefault {

}
