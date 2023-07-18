<?php

namespace Drupal\hearsay_work_with_you\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Work With You Block.
 *
 * @Block(
 *   id = "hearsay_work_with_you",
 *   admin_label = @Translation("Work With You Block"),
 * )
 */
class HearsayWorkWithYou extends BlockBase
{
    /**
     * Block created for Work With You Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_HOW_WE_WORK, FALSE);
        return [
            '#theme' => 'hearsay_work_with_you',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
