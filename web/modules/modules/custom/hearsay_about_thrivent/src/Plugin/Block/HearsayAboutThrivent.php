<?php

namespace Drupal\hearsay_about_thrivent\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * About Thrivent Block.
 *
 * @Block(
 *   id = "hearsay_about_thrivent",
 *   admin_label = @Translation("About Thrivent Block"),
 * )
 */
class HearsayAboutThrivent extends BlockBase
{
    /**
     * Block created for About Thrivent Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_ABOUT_THRIVENT, FALSE);
        return [
            '#theme' => 'hearsay_about_thrivent',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
