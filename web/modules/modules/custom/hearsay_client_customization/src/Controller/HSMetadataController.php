<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Class Hearsay Metadata Controller.
 */

class HSMetadataController extends ControllerBase
{
    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_common\Controller\HearsayCommon
     */
    protected $hearsayCommon;

    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_client_customization\Controller\HearsayClientCustomization
     */
    protected $hearsayClientCustomization;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hearsayCommon = new HearsayCommon();
        $this->hearsayClientCustomization = new HearsayClientCustomization();
    }

    /**
     * Set Meta Data variables for nodes.
     *
     * @param array $variables
     *   Object of form data.
     */
    public function getMetaTagsForNode(&$variables)
    {
        $response = $this->hearsayCommon->getProfileData(FALSE);
        $config = $this->hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];
        $configThemeIds = $this->hearsayClientCustomization->getCsPlatformSettings();
        $themeId = $this->hearsayCommon->getThemeIdByNode()['theme_id'];
        $node = \Drupal::routeMatch()->getParameter('node');

        if ($node) {
            $nodeType = $node->bundle();
        }

        switch ($themeId) {
                // NPG
            case $configThemeIds['theme_id_npg']:
                $this->setMetaTagsForNpgNodes($variables, $config, $response, $nodeType);
                break;
                // P3
            case $configThemeIds['theme_id_p3']:
                $this->setMetaTagsForP3Nodes($variables, $config, $response, $nodeType);
                break;
                // TEAM
            case $configThemeIds['theme_id_lt']:
                $this->setMetaTagsForTeamNodes($variables, $config, $response, $nodeType);
                break;
                // INDIVIDUAL
            case $configThemeIds['theme_id_li']:
                $this->setMetaTagsForIndividualNodes($variables, $config, $response, $nodeType);
                break;
        }
    }

    /**
     * Set Meta Data variables for nodes for Library Individual Theme.
     *
     * @param array $variables
     *   Array of Drupal variables.
     * @param object $config
     *   Object of Form Config Data.
     * @param object $response
     *   Object of Profile Data.
     * @param string $nodeType
     *   Current Node Type.
     */
    public function setMetaTagsForIndividualNodes(&$variables, $config, $response, $nodeType)
    {
        switch ($nodeType) {
            case HS_HOME:
                $variables['meta_title'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $twitterCard = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_ABOUT:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . '.';
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_SOLUTIONS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_NEWS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_EVENTS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
        }
    }

    /**
     * Set Meta Data variables for nodes for Library Team Theme.
     *
     * @param array $variables
     *   Array of Drupal variables.
     * @param object $config
     *   Object of Form Config Data.
     * @param object $response
     *   Object of Profile Data.
     * @param string $nodeType
     *   Current Node Type.
     */
    public function setMetaTagsForTeamNodes(&$variables, $config, $response, $nodeType)
    {
        switch ($nodeType) {
            case HS_HOME:
                $variables['meta_title'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $twitterCard = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_ABOUT:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . '.';
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_SOLUTIONS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_NEWS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
            case HS_EVENTS:
                $variables['meta_title'] = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_description'] = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $config->get('og_title_' . $nodeType) ?? '';
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $config->get('og_image_' . $nodeType) ?? '';
                $variables['og_url'] = $config->get('og_url_' . $nodeType) ?? '';
                $variables['twitter_title'] = $config->get('twitter_title_' . $nodeType) ?? '';
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $config->get('twitter_image_' . $nodeType) ?? '';
                $variables['twitter_url'] = $config->get('twitter_url_' . $nodeType) ?? '';
                break;
        }
    }

    /**
     * Set Meta Data variables for nodes for NPG Theme.
     *
     * @param array $variables
     *   Array of Drupal variables.
     * @param object $config
     *   Object of Form Config Data.
     * @param object $response
     *   Object of Profile Data.
     * @param string $nodeType
     *   Current Node Type.
     */
    public function setMetaTagsForNpgNodes(&$variables, $config, $response, $nodeType)
    {
        switch ($nodeType) {
            case HS_HOME:
                $metaTitle = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $response->profile_photo ?? '';
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $response->profile_photo ?? '';
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ABOUT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_OUR_TEAM:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_OUR_STORY:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->team_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $metaDescription = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $metaDescription;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_HOW_WE_WORK:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_COMMUNITY_IMPACT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_COMMUNITY_IMPACT_DETAILS:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentCommunityImpactCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentCommunityImpactCanonical = explode("?", $currentCommunityImpactCanonical)[0];
                }
                foreach ($response->community_involvements as $communityImpact) {
                    $communityImpact = (object)$communityImpact;
                    if ($communityImpact->canonical_name == $currentCommunityImpactCanonical) {
                        $communityTitle = $communityImpact->title;
                    }
                }
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $communityTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $variables['meta_title'];
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $variables['meta_title'];
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ABOUT_THRIVENT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_THRIVENT_MEMBERSHIP:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_STRONG_AND_STABLE:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $metaDescription = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_SOLUTIONS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('solutions_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_SOLUTIONS_DETAILS:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentProductsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentProductsCanonical = explode("?", $currentProductsCanonical)[0];
                }
                $allAdminProducts = isset($response->services_to_display) ? $response->services_to_display : '';
                foreach ($allAdminProducts as $productName) {
                    $canonicalDetails = $this->hearsayCommon->getCanonicalMediaDetails($productName, 'field_solution_canonical_name');
                    $canonicalName = reset($canonicalDetails);
                    $canonicalUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_media_image->target_id);
                    if ($canonicalName->field_solution_canonical_name->value == $currentProductsCanonical) {
                        $metaTitle = $canonicalName->field_solution_title->value;
                        $metaDescription = $canonicalName->field_solution_summary->value;
                    }
                }
                $additionalProducts = isset($response->additional_services) ? $response->additional_services : '';
                foreach ($additionalProducts as $additionalProduct) {
                    if ($additionalProduct->canonical_name == $currentProductsCanonical) {
                        $metaTitle = $additionalProduct->title;
                        $metaDescription = $additionalProduct->summary;
                    }
                }

                $metaTitle1 = $config->get('meta_title1_' . $nodeType) ?? '';
                $metaTitle2 = $config->get('meta_title2_' . $nodeType) ?? '';
                $image = $config->get('solutions_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $ogTitle1 = $config->get('og_title1_' . $nodeType) ?? '';
                $ogTitle2 = $config->get('og_title2_' . $nodeType) ?? '';

                $variables['meta_title'] = $metaTitle . ' - ' . $response->first_name . ' ' . $response->last_name . ', ' . $metaTitle1 . ' ' . $response->address->city . ', ' . $response->address->state . ' | ' . $metaTitle2;
                $variables['meta_description'] = $metaDescription;
                $variables['og_title'] = $response->first_name . ' ' . $response->last_name . ' | ' . $ogTitle1 . ' | ' . $metaTitle . ' | ' . $ogTitle2;
                $variables['og_description'] = $metaDescription;
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $variables['og_title'];
                $variables['twitter_description'] = $metaDescription;
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_EVENTS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_EVENTS_DETAIL:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $responseEvents = $this->hearsayCommon->getEventsAPIData(FALSE);
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentEventsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentEventsCanonical = explode("?", $currentEventsCanonical)[0];
                }
                foreach ($responseEvents as $event) {
                    $event = (object)$event;
                    if ($event->slug == $currentEventsCanonical) {
                        $eventName = $event->event_name;
                        $eventDetails = $event->details;
                    }
                }
                $metaTitle = $eventName . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $image = $config->get('events_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $eventDetails;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $eventDetails;
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $eventDetails;
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_RESOURCES:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' . $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_NEWS_INSIGHTS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ADDITIONAL_INFO:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_NPG_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_LOCATION:
                // $baseUrl = Url::fromRoute('<front>')->setAbsolute()->toString();
                $metaTitle = $config->get('meta_title1_' . $nodeType) ?? '';
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $response->profile_photo ?? '';
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $response->profile_photo ?? '';
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
        }
    }

    /**
     * Set Meta Data variables for nodes for P3 Theme.
     *
     * @param array $variables
     *   Array of Drupal variables.
     * @param object $config
     *   Object of Form Config Data.
     * @param object $response
     *   Object of Profile Data.
     * @param string $nodeType
     *   Current Node Type.
     */
    public function setMetaTagsForP3Nodes(&$variables, $config, $response, $nodeType)
    {
        switch ($nodeType) {
            case HS_HOME:
                $metaTitle = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $response->first_name . ' ' .
                    $response->last_name . ' ' .
                    ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '');
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $response->profile_photo ?? '';
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $response->profile_photo ?? '';
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ABOUT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_OUR_TEAM:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_OUR_STORY:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->team_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $metaDescription = ($config->get('meta_description1_' . $nodeType) ?? '') . ' ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_description2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' ' .
                    ($config->get('meta_description3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $metaDescription;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_HOW_WE_WORK:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_COMMUNITY_IMPACT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_COMMUNITY_IMPACT_DETAILS:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentCommunityImpactCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentCommunityImpactCanonical = explode("?", $currentCommunityImpactCanonical)[0];
                }
                foreach ($response->community_involvements as $communityImpact) {
                    $communityImpact = (object)$communityImpact;
                    if ($communityImpact->canonical_name == $currentCommunityImpactCanonical) {
                        $communityTitle = $communityImpact->title;
                    }
                }
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $communityTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $variables['meta_title'];
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $variables['meta_title'];
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ABOUT_THRIVENT:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_THRIVENT_MEMBERSHIP:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_OUR_ADVICE_APPROACH:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_SOLUTIONS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('solutions_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_SOLUTIONS_DETAILS:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentProductsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentProductsCanonical = explode("?", $currentProductsCanonical)[0];
                }
                $allAdminProducts = isset($response->services_to_display) ? $response->services_to_display : '';
                foreach ($allAdminProducts as $productName) {
                    $canonicalDetails = $this->hearsayCommon->getCanonicalMediaDetails($productName, 'field_solution_canonical_name');
                    $canonicalName = reset($canonicalDetails);
                    $canonicalUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_media_image->target_id);
                    if ($canonicalName->field_solution_canonical_name->value == $currentProductsCanonical) {
                        $metaTitle = $canonicalName->field_solution_title->value;
                        $metaDescription = $canonicalName->field_solution_summary->value;
                    }
                }
                $additionalProducts = isset($response->additional_services) ? $response->additional_services : '';
                foreach ($additionalProducts as $additionalProduct) {
                    if ($additionalProduct->canonical_name == $currentProductsCanonical) {
                        $metaTitle = $additionalProduct->title;
                        $metaDescription = $additionalProduct->summary;
                    }
                }

                $metaTitle1 = $config->get('meta_title1_' . $nodeType) ?? '';
                $metaTitle2 = $config->get('meta_title2_' . $nodeType) ?? '';
                $image = $config->get('solutions_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $ogTitle1 = $config->get('og_title1_' . $nodeType) ?? '';
                $ogTitle2 = $config->get('og_title2_' . $nodeType) ?? '';

                $variables['meta_title'] = $metaTitle . ' - ' . $response->first_name . ' ' . $response->last_name . ', ' . $metaTitle1 . ' ' . $response->address->city . ', ' . $response->address->state . ' | ' . $metaTitle2;
                $variables['meta_description'] = $metaDescription;
                $variables['og_title'] = $response->first_name . ' ' . $response->last_name . ' | ' . $ogTitle1 . ' | ' . $metaTitle . ' | ' . $ogTitle2;
                $variables['og_description'] = $metaDescription;
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $variables['og_title'];
                $variables['twitter_description'] = $metaDescription;
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_EVENTS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $image = $config->get('about_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_EVENTS_DETAIL:
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                $responseEvents = $this->hearsayCommon->getEventsAPIData(FALSE);
                $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
                $currentEventsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
                if ($siteTools->isPreview() == true) {
                    $currentEventsCanonical = explode("?", $currentEventsCanonical)[0];
                }
                foreach ($responseEvents as $event) {
                    $event = (object)$event;
                    if ($event->slug == $currentEventsCanonical) {
                        $eventName = $event->event_name;
                        $eventDetails = $event->details;
                    }
                }
                $metaTitle = $eventName . ' ' .
                    ($config->get('meta_title1_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '');
                $image = $config->get('events_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id);
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $eventDetails;
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $eventDetails;
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $eventDetails;
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_RESOURCES:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' . $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_NEWS_INSIGHTS:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_ADDITIONAL_INFO:
                $metaTitle = ($config->get('meta_title1_' . $nodeType) ?? '') . ' - ' .
                    $response->first_name . ' ' .
                    $response->last_name . ', ' .
                    ($config->get('meta_title2_' . $nodeType) ?? '') . ' ' .
                    $response->address->city . ', ' .
                    $response->address->state . ' | ' .
                    ($config->get('meta_title3_' . $nodeType) ?? '');
                $image = $config->get('resources_banner_image') ?? '';
                $imageDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($image, 'field_banner_canonical_name', HS_P3_AB);
                $imageMedia = reset($imageDetails);
                $imageUrl = $imageMedia ? $this->hearsayCommon->getMediaImageUrl($imageMedia->field_media_image->target_id) : null;
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $imageUrl;
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $imageUrl;
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
            case HS_LOCATION:
                // $baseUrl = Url::fromRoute('<front>')->setAbsolute()->toString();
                $metaTitle = $config->get('meta_title1_' . $nodeType) ?? '';
                $variables['meta_title'] = $metaTitle;
                $variables['meta_description'] = $config->get('meta_description_' . $nodeType) ?? '';
                $variables['og_title'] = $metaTitle;
                $variables['og_description'] = $config->get('og_description_' . $nodeType) ?? '';
                $variables['og_type'] = $config->get('og_type_' . $nodeType) ?? '';
                $variables['og_image'] = $response->profile_photo ?? '';
                $variables['og_url'] = \Drupal::request()->getUri();
                $variables['twitter_title'] = $metaTitle;
                $variables['twitter_description'] = $config->get('twitter_description_' . $nodeType) ?? '';
                $variables['twitter_card'] = $config->get('twitter_card_' . $nodeType) ?? '';
                $variables['twitter_image'] = $response->profile_photo ?? '';
                $variables['twitter_url'] = \Drupal::request()->getUri();
                break;
        }
    }
}
