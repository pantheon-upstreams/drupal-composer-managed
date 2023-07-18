<?php

namespace Drupal\hearsay_products\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Products Block.
 *
 * @Block(
 *   id = "products_block",
 *   admin_label = @Translation("Products Block")
 * )
 */
class ProductsBlock extends BlockBase {
    public function build() {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_PRODUCTS, FALSE);
        return [
            '#theme' => 'hearsay_products',
            '#items' => $themeAndModuleData,
        ];
    }

    public function getCacheMaxAge() {
        return 0;
    }
}