<?php

namespace Drupal\hearsay_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Footer Block.
 *
 * @Block(
 *   id = "hearsay_footer",
 *   admin_label = @Translation("Footer Block"),
 * )
 */
class HearsayFooter extends BlockBase
{
    /**
     * Block created for Footer Section.
     * {@inheritdoc}
     */

    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_FOOTER, FALSE);
        return [
            '#theme' => 'hearsay_footer',
            '#items' => $themeAndModuleData,
        ];
    }

    public function getCacheMaxAge()
    {
        return 0;
    }
}
