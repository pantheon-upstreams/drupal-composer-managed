<?php

namespace Drupal\du_widen;

use Drupal\twig_tweak\TwigTweakExtension as TwigTweakExtension;

/**
 * Twig extension with some useful functions and filters.
 */
class TwigExtension extends TwigTweakExtension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'du_widen_twig_tweak';
  }

  /**
   * Returns the URL of this image derivative for an original image path or URI.
   *
   * Overrides the imageStyle() function in twig_tweak's TwigExtension class so
   * that if the image is a widen image, it'll get the correct image style URL
   * for the image.
   *
   * @param string $path
   *   The path or URI to the original image.
   * @param string $style
   *   The image style.
   *
   * @return string
   *   The absolute URL where a style image can be downloaded, suitable for use
   *   in an <img> tag. Requesting the URL will cause the image to be created.
   */
  public function imageStyle($path, $style) {
    $url = du_widen_get_image_style_url($path, $style);
    return \Drupal::service('file_url_generator')->transformRelative($url);
  }

}
