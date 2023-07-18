<?php

namespace Drupal\hearsay_custom_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayCommon;

/**
 * Custom Content Block.
 *
 * @Block(
 *   id = "hearsay_custom_content",
 *   admin_label = @Translation("Custom Content Block"),
 * )
*/
class HearsayCustomContent extends BlockBase
{
    /**
     * Block created for Custom Content Section.
     * {@inheritdoc}
    */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $hearsayCommon = new HearsayCommon();
        if ($hearsayCommon->getThemeIdByNode()['node_type'] == 'about') {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_CUSTOM_CONTENT, TRUE);
        } else {
            $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_CUSTOM_CONTENT, FALSE);
        }
        return [
            '#theme' => 'hearsay_custom_content',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
