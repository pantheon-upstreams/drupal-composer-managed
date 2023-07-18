<?php

namespace Drupal\hearsay_additional_information\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Additional Information Block.
 *
 * @Block(
 *   id = "hearsay_additional_information",
 *   admin_label = @Translation("Additional Information Block"),
 * )
 */
class HearsayAdditionalInformation extends BlockBase
{
    /**
     * Block created for Additional Information Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_ADDITIONAL_INFO, FALSE);
        return [
            '#theme' => 'hearsay_additional_information',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
