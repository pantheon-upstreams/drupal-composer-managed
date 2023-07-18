<?php

namespace Drupal\hearsay_office_information\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Office Information Block.
 *
 * @Block(
 *   id = "hearsay_office_information",
 *   admin_label = @Translation("Office Information Block"),
 * )
*/
class HearsayOfficeInformation extends BlockBase
{
    /**
     * Block created for Office Information Section.
     * {@inheritdoc}
    */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_OFFICE_INFORMATION, FALSE);
        return [
            '#theme' => 'hearsay_office_information',
            '#items' => $themeAndModuleData,
            '#attached' => array(
                'library' => array('hearsay_office_information/googlemapjs'),
            ),
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
