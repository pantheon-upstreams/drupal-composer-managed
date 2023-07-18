<?php

namespace Drupal\hearsay_banner\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayCommon;

/**
 * Banner Block.
 *
 * @Block(
 *   id = "hearsay_banner",
 *   admin_label = @Translation("Hearsay Banner")
 * )
 */
class BannerBlock extends BlockBase
{

    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $hearsayCommon = new HearsayCommon();
        $configThemeIds = $hearsayClientCustomization->getCsPlatformSettings();
    	$themeId = $hearsayCommon->getThemeIdByNode()['theme_id'];
        if($themeId == $configThemeIds['theme_id_lt'] || $themeId == $configThemeIds['theme_id_li']){
            $insightsData = $hearsayCommon->getSocialPostData(TRUE);
        }
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_BANNER, TRUE);
        return [
            '#theme' => 'hearsay_banner',
            '#items' => $themeAndModuleData,
        ];
    }

    public function getCacheMaxAge()
    {
        return 0;
    }
}
