<?php

namespace Drupal\hearsay_contact\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Hearsay Contact Form Block.
 *
 * @Block(
 *  id = "hearsay_contact_us_block",
 *  admin_label = @Translation("Hearsay Contact Form Block"),
 * )
 */
class HearsayContactUsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\hearsay_contact\Form\HearsayContactForm');
    return $form;
  }

}
