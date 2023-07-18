<?php

namespace Drupal\hearsay_events\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Events Block.
 *
 * @Block(
 *   id = "hearsay_events",
 *   admin_label = @Translation("Events Block"),
 * )
*/
class HearsayEvents extends BlockBase
{
    /**
     * Block created for Events Section.
     * {@inheritdoc}
    */
    public function build()
    {
        $hearsayCommon = new HearsayCommon();
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        if ($hearsayCommon->getThemeIdByNode()['node_type'] == 'events') {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_EVENTS_MODULE, TRUE);
        } else {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_EVENTS_MODULE, FALSE);
        }
        return [
            '#theme' => 'hearsay_events',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}