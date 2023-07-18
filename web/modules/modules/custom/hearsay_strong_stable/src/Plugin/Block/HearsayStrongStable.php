<?php

namespace Drupal\hearsay_strong_stable\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Strong and Stable Block.
 *
 * @Block(
 *   id = "hearsay_strong_stable",
 *   admin_label = @Translation("Strong and Stable Block"),
 * )
 */
class HearsayStrongStable extends BlockBase
{
    /**
     * Block created for Strong and Stable Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_STRONG_AND_STABLE, FALSE);
        return [
            '#theme' => 'hearsay_strong_stable',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
