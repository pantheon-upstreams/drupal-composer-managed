<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayHeaderInterface;

/**
 * Class Hearsay Menu Controller.
 */

class HSHeaderController extends ControllerBase implements HearsayHeaderInterface
{
    /**
     * Get the Sitemap and generate a menu tree.
     *
     * @param object $headerMenuBlock
     *   Received from Header Menu Block.
     *
     * @return array
     *   Array of themes with variables.
     */
    public function buildHeader($headerMenuBlock)
    {
        // Creating Objects of Hearsay Common and Client Customization
        $hearsayCommon = new HearsayCommon();
        $hearsayClientCustomization = new HearsayClientCustomization();

        $vocabulary_config = $headerMenuBlock->getConfiguration()['vocabulary'];
        $vocabulary_config = explode('|', $vocabulary_config);
        $vocabulary = isset($vocabulary_config[0]) ? $vocabulary_config[0] : null;
        $base_term = $headerMenuBlock->getVocabularyBaseTerm($headerMenuBlock->getConfiguration()['base_term'], $headerMenuBlock->getConfiguration()['dynamic_base_term']);
        $max_depth = $headerMenuBlock->getConfiguration()['max_depth'];
        $vocabulary_tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
            ->loadTree($vocabulary, $base_term, $max_depth + 1);

        if ($headerMenuBlock->getConfiguration()['hide_block'] && !$vocabulary_tree) {
            return [];
        }

        $image_field = isset($vocabulary_config[1]) ? $vocabulary_config[1] : null;
        $use_image_style = $headerMenuBlock->getConfiguration()['use_image_style'];
        $image_height = $headerMenuBlock->getConfiguration()['image_height'];
        $image_width = $headerMenuBlock->getConfiguration()['image_width'];
        $image_style = $use_image_style == true ? $headerMenuBlock->getConfiguration()['image_style'] : null;
        $route_tid = $headerMenuBlock->getCurrentRoute();
        $max_age = $headerMenuBlock->getMaxAge($headerMenuBlock->getConfiguration()['max_age']);
        $interactive_parent = $headerMenuBlock->getConfiguration()['collapsible'] ? $headerMenuBlock->getConfiguration()['interactive_parent'] : 0;
        $show_count = $headerMenuBlock->getConfiguration()['show_count'];
        $referencing_field = $headerMenuBlock->getConfiguration()['referencing_field'];

        $vocabulary_tree_array = [];
        foreach ($vocabulary_tree as $item) {
            $vocabulary_tree_array[] = [
                'tid' => $item->tid,
                'status' => $headerMenuBlock->getStatusFromTid($item->tid),
                'name' => $headerMenuBlock->getNameFromTid($item->tid),
                'url' => $headerMenuBlock->getLinkFromTid($item->tid),
                'custom_path' => $headerMenuBlock->getCurrentPath($item->tid),
                'parents' => $item->parents,
                'use_image_style' => $use_image_style,
                'image' => $headerMenuBlock->getImageFromTid($item->tid, $image_field, $image_style),
                'height' => $image_height != '' ? $image_height : 16,
                'width' => $image_width != '' ? $image_width : 16,
                'interactive_parent' => $interactive_parent,
                'show_count' => $show_count,
                'entities' => !empty($show_count) ? $headerMenuBlock->getEntityIds($headerMenuBlock->entitiesMap[$show_count], $referencing_field, $item->tid, $vocabulary, $headerMenuBlock->getConfiguration()['calculate_count_recursively']) : [],
            ];
        }

        // Generate a tree with specific theme IDs
        $tree = null;
        $slugThemeId = $hearsayCommon->getThemeIdByNode()['theme_id'];
        $utilityObj = \Drupal::service('hearsay_automation_process_service.utility');
        $config = $hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];

        $vocabularyTreeArray = $headerMenuBlock->generateTree($vocabulary_tree_array, $base_term);

        foreach ($vocabularyTreeArray as $treeValue) {

            $allTreeThemeIds = $treeValue['url']->getOptions()['entity']->get('field_theme_ids')->value;

            if ($utilityObj->verifyAllowedNode($slugThemeId, $allTreeThemeIds)) {
                if ($tree === null) {
                    $tree = [];
                }
                // Update Menu Item Label
                switch ($treeValue['content_type']) {
                    case HS_HOME:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('home_menu_label') ? $config->get('home_menu_label') : $rootName;
                        break;
                    case HS_ABOUT:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('about_menu_label') ? $config->get('about_menu_label') : $rootName;
                        break;
                    case HS_SOLUTIONS:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('solutions_menu_label') ? $config->get('solutions_menu_label') : $rootName;
                        break;
                    case HS_NEWS:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('insights_menu_label') ? $config->get('insights_menu_label') : $rootName;
                        break;
                    case HS_EVENTS:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('events_menu_label') ? $config->get('events_menu_label') : $rootName;
                        break;
                    case HS_RESOURCES:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('resources_menu_label') ? $config->get('resources_menu_label') : $rootName;
                        break;
                    case HS_LOCATION:
                        $rootName = $treeValue['name'];
                        $treeValue['name'] = $config->get('location_menu_label') ? $config->get('location_menu_label') : $rootName;
                        break;
                    default:
                        break;
                }
                $tree[] = $treeValue;
            }
        }

        $themeName = '';
        $allThemeIds = $hearsayClientCustomization->getCsPlatformSettings();
        $base_slug = $hearsayCommon->getThemeIdByNode()['baseSlug']; // get current base slug

        // Get the Theme Name
        switch ($slugThemeId) {
            case $allThemeIds['theme_id_lt']:
                $themeName = 'team';
                break;
            case $allThemeIds['theme_id_li']:
                $themeName = 'individual';
                break;
            case $allThemeIds['theme_id_npg']:
                $themeName = 'npg';
                break;
            case $allThemeIds['theme_id_p3']:
                $themeName = 'P3';
                break;
            default:
                $themeName = '';
                break;
        }

        if ($slugThemeId == $allThemeIds['theme_id_lt'] || $slugThemeId == $allThemeIds['theme_id_li']) {
            $hearsayClientCustomization->hideShowNodesInSitemap();
        }

        $node = \Drupal::routeMatch()->getParameter('node');
        // $currentUrl = $_SERVER['REQUEST_URI'];

        if ($node || str_starts_with($_SERVER['REQUEST_URI'], '/user/login') || str_starts_with($_SERVER['REQUEST_URI'], '/user/password') || $_SERVER['REQUEST_URI'] == '/expired_page') {
            // Get the Utility Nav data from the config form.
            if ($slugThemeId == $allThemeIds['theme_id_npg'] || $slugThemeId == $allThemeIds['theme_id_p3']) {
                $utilityNav = [
                    'client_company_label' => $config->get('client_portal_label') ?? '',
                    'client_company_link' => $config->get('client_portal_link') ? $config->get('client_portal_link') . '?referenceid=' . $base_slug : '',
                    'broker_check_label' => $config->get('broker_check_label') ?? '',
                    'broker_check_link' => $config->get('broker_check_link') ? $config->get('broker_check_link') . '?referenceid=' . $base_slug : '',
                ];
            } elseif ($slugThemeId == $allThemeIds['theme_id_lt'] || $slugThemeId == $allThemeIds['theme_id_li']) {
                $utilityNav = [
                    'parent_company_label' => $config->get('parent_company_label') ?? '',
                    'parent_company_link' => $config->get('parent_company_link') ? $config->get('parent_company_link') . '?referenceid=' . $base_slug : '',
                    'client_company_label' => $config->get('client_portal_label') ?? '',
                    'client_company_link' => $config->get('client_portal_link') ? $config->get('client_portal_link') . '?referenceid=' . $base_slug : '',
                    'broker_check_label' => $config->get('broker_check_label') ?? '',
                    'broker_check_link' => $config->get('broker_check_link') ? $config->get('broker_check_link') . '?referenceid=' . $base_slug : '',
                ];
            }

            // Get the site logo from config
            $logoImages = $config->get('header_logo') ? $config->get('header_logo') : '';
            $logoDetails = $hearsayCommon->getCanonicalMediaDetails($logoImages, 'field_logo_canonical_name');
            $logoMedia = reset($logoDetails);
            $siteLogo = $hearsayCommon->getMediaImageUrl($logoMedia->field_media_image->target_id); // Get media image URL
            $logoAltText = $config->get('header_alt_text') ? $config->get('header_alt_text') : '';

            // Get the Theme template based on the Theme IDs.
            if ($slugThemeId == $allThemeIds['theme_id_npg'] || $slugThemeId == $allThemeIds['theme_id_p3']) {
                $logoLink = $config->get('header_logo_link') ? $config->get('header_logo_link') . '?referenceid=' . $base_slug : '';
            } elseif ($slugThemeId == $allThemeIds['theme_id_lt'] || $slugThemeId == $allThemeIds['theme_id_li']) {
                $logoLink = $config->get('header_logo_link') ? $config->get('header_logo_link') . '?referenceid=' . $base_slug : '';
            }
        }

        // Initializing Profile Data to get the data from the API response
        $profileData = $hearsayCommon->getProfileData();
        $profileInfo = [
            'team_name' => $profileData->team_name ?? '',
            'plain_social_network' => $profileData->plain_social_networks ?? '',
            'theme_name' => $themeName,
        ];

        $currentPath = \Drupal::service('path.current')->getPath();
        $variables['current_path'] = $currentPath;

        $hiddenMenuItems = $this->showMenuItems();

        return [
            '#theme' => 'hearsay_header_menu',
            '#utility_nav' => $utilityNav,
            '#profile_info' => $profileInfo,
            '#site_logo' => $siteLogo,
            '#current_path' => $currentPath,
            '#logo_alt_text' => $logoAltText,
            '#hidden_item' => $hiddenMenuItems,
            '#logo_link' => $logoLink,
            '#menu_tree' => $tree,
            '#route_tid' => $route_tid,
            '#cache' => [
                'max-age' => $max_age,
                'tags' => [
                    'taxonomy_term_list',
                ],
            ],
            '#current_depth' => 0,
            '#vocabulary' => $vocabulary,
            '#max_depth' => $max_depth,
            '#collapsible' => $headerMenuBlock->getConfiguration()['collapsible'],
            '#attached' => [
                'library' => [
                    'hearsay_header_menu/hearsay_header_menu',
                ],
                'drupalSettings' => [
                    'stayOpen' => $headerMenuBlock->getConfiguration()['stay_open'],
                    'interactiveParentMenu' => $headerMenuBlock->getConfiguration()['interactive_parent'],
                ],
            ],
        ];
    }

    /**
     * Show menu items if data is present in About, Insight and Events
     */
    function showMenuItems()
    {
        $hideMenuItems = [];
        $hearsayCommon = new HearsayCommon();
        $profileData = $hearsayCommon->getProfileData(FALSE);
        $insightsData = $hearsayCommon->getSocialPostData(FALSE);
        $eventsData = $hearsayCommon->getEventsAPIData(FALSE);

        if (empty($profileData->about_me_custom_content) && empty($profileData->team_members)) {
            $hideMenuItems[] = HS_ABOUT;
        }
        if (empty($insightsData)) {
            $hideMenuItems[] = HS_NEWS;
        }
        if (empty($eventsData)) {
            $hideMenuItems[] = HS_EVENTS;
        }
        return $hideMenuItems;
    }

    /**
     * Define theme variables for Header.
     *
     * @return array
     *   Array of common theme variables.
     */
    public function defineThemeVariablesForHeader()
    {
        // Define the common variables for both themes
        $commonVariables = [
            'menu_tree' => [],
            'utility_nav' => [],
            'site_logo' => [],
            'current_path' => [],
            'logo_alt_text' => [],
            'logo_link' => [],
            'profile_info' => [],
            'hidden_item' => [],
            'menu_name' => [],
            'route_tid' => null,
            'vocabulary' => null,
            'current_depth' => 0,
            'max_depth' => 0,
            'collapsible' => null,
        ];
        return $commonVariables;
    }
}
