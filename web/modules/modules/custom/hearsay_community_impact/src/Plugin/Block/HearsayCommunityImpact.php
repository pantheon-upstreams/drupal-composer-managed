<?php

namespace Drupal\hearsay_community_impact\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Community Impact Block.
 *
 * @Block(
 *   id = "hearsay_community_impact",
 *   admin_label = @Translation("Community Impact Block"),
 * )
 */
class HearsayCommunityImpact extends BlockBase
{
    /**
     * Block created for Community Impact Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_COMMUNITY_IMPACT, FALSE);
        return [
            '#theme' => 'hearsay_community_impact',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
