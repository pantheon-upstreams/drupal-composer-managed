<?php

namespace Drupal\hearsay_membership_benefits\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Membership Benefits Block.
 *
 * @Block(
 *   id = "hearsay_membership_benefits",
 *   admin_label = @Translation("Membership Benefits Block"),
 * )
 */
class HearsayMembershipBenefits extends BlockBase
{
    /**
     * Block created for Membership Benefits Section.
     * {@inheritdoc}
     */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_THRIVENT_MEMBERSHIP, FALSE);
        return [
            '#theme' => 'hearsay_membership_benefits',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
