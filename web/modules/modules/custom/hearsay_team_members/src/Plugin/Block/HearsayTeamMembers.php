<?php

namespace Drupal\hearsay_team_members\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Team Members Block.
 *
 * @Block(
 *   id = "hearsay_team_members",
 *   admin_label = @Translation("Team Members Block"),
 * )
*/
class HearsayTeamMembers extends BlockBase
{
    /**
     * Block created for Team Members Section.
     * {@inheritdoc}
    */
    public function build()
    {
        $themeAndModuleData = [];
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeAndModuleData = $hearsayClientCustomization->getProcessedData(HS_TEAM_MEMBERS, FALSE);
        return [
            '#theme' => 'hearsay_team_members',
            '#items' => $themeAndModuleData,
        ];
    }
    public function getCacheMaxAge()
    {
        return 0;
    }
}
