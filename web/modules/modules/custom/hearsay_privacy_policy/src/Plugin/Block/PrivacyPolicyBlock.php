<?php

namespace Drupal\hearsay_privacy_policy\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Privacy Policy Block.
 *
 * @Block(
 *   id = "privacy_policy_block",
 *   admin_label = @Translation("Privacy Policy Block")
 * )
 */
class PrivacyPolicyBlock extends BlockBase {
    public function build() {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_PRIVACY_POLICY, FALSE);
        return [
            '#theme' => 'hearsay_privacy_policy',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge() {
        return 0;
    }
}
