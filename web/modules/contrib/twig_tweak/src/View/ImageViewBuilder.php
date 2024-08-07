<?php

namespace Drupal\twig_tweak\View;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\file\FileInterface;

/**
 * Image view builder.
 */
class ImageViewBuilder {

  /**
   * Builds an image.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file object.
   * @param string $style
   *   (optional) Image style.
   * @param array $attributes
   *   (optional) Image attributes.
   * @param bool $responsive
   *   (optional) Indicates that the provided image style is responsive.
   * @param bool $check_access
   *   (optional) Indicates that access check is required.
   *
   * @return array
   *   A renderable array to represent the image.
   */
  public function build(FileInterface $file, string $style = NULL, array $attributes = [], bool $responsive = FALSE, bool $check_access = TRUE): array {

    $access = $check_access ? $file->access('view', NULL, TRUE) : AccessResult::allowed();

    $build = [];
    if ($access->isAllowed()) {
      $build['#uri'] = $file->getFileUri();
      $build['#attributes'] = $attributes;
      if ($style) {
        if ($responsive) {
          $build['#type'] = 'responsive_image';
          $build['#responsive_image_style_id'] = $style;
        }
        else {
          $build['#theme'] = 'image_style';
          $build['#style_name'] = $style;
        }
      }
      else {
        $build['#theme'] = 'image';
      }
    }

    CacheableMetadata::createFromRenderArray($build)
      ->addCacheableDependency($access)
      ->addCacheableDependency($file)
      ->applyTo($build);

    return $build;
  }

}
