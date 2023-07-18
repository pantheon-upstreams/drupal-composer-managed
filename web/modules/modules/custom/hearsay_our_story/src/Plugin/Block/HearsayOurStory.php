<?php

namespace Drupal\hearsay_our_story\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayCommon;

/**
 * Our Story Block.
 *
 * @Block(
 *   id = "hearsay_our_story",
 *   admin_label = @Translation("Our Story Block"),
 * )
 */
class HearsayOurStory extends BlockBase
{
    /**
     * Block created for Our Story Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_OUR_STORY_MODULE, FALSE);
        return [
            '#theme' => 'hearsay_our_story',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
