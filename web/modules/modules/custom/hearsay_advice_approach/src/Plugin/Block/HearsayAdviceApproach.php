<?php

namespace Drupal\hearsay_advice_approach\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Our advice approach Block.
 *
 * @Block(
 *   id = "hearsay_advice_approach",
 *   admin_label = @Translation("Our advice approach Block"),
 * )
 */
class HearsayAdviceApproach extends BlockBase
{
    /**
     * Block created for Our advice approach Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_OUR_ADVICE_APPROACH, FALSE);
        return [
            '#theme' => 'hearsay_advice_approach',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
