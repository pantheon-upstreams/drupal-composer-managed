<?php

namespace Drupal\hearsay_short_bio\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Short Bio Block.
 *
 * @Block(
 *   id = "hearsay_short_bio",
 *   admin_label = @Translation("Short Bio Block"),
 * )
 */
class HearsayShortBio extends BlockBase
{
    /**
     * Block created for Short Bio Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_SHORT_BIO_MODULE, FALSE);
        return [
            '#theme' => 'hearsay_short_bio',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
