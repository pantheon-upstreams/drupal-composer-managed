<?php

namespace Drupal\hearsay_insights\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayCommon;

/**
 * Insights Block.
 *
 * @Block(
 *   id = "hearsay_insights",
 *   admin_label = @Translation("Insights Block"),
 * )
 */
class HearsayInsights extends BlockBase
{
    /**
     * Block created for Insights Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $hearsayCommon = new HearsayCommon();
        $insightsData = $hearsayCommon->getSocialPostData(TRUE);
        if ($hearsayCommon->getThemeIdByNode()['node_type'] == 'news') {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_INSIGHTS_MODULE, TRUE);
        } else {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_INSIGHTS_MODULE, FALSE);
        }
        
        return [
            '#theme' => 'hearsay_insights',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
