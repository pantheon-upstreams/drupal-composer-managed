<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HSTeamController;
use Drupal\hearsay_client_customization\Controller\HSIndividualController;
use Drupal\hearsay_client_customization\Controller\HSNPGController;
use Drupal\hearsay_client_customization\Controller\HSP3Controller;
use Drupal\hearsay_common\Controller\HearsayClientCustomizationInterface;

/**
 * HearsayClientCustomization class to extend and implement interface.
 */
class HearsayClientCustomization extends ControllerBase implements HearsayClientCustomizationInterface
{
    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_common\Controller\HearsayCommon
     */
    protected $hearsayCommon;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hearsayCommon = new HearsayCommon();
    }

    /**
     * Get Data for module.
     *
     * @param string $moduleName
     *   Module name constant from settings form.
     * @param string $isFirstCall
     *   String specifying if API call is first on page or not.
     *
     * @return array
     *   Array of theme and module data to be populated inside module.
     */
    public function getProcessedData($moduleName, $isFirstCall = FALSE)
    {
        $baseSlugData = $this->hearsayCommon->getThemeIdByNode();
        $configThemeIds = $this->getCsPlatformSettings();
        $profileData = $this->hearsayCommon->getProfileData($isFirstCall);
        $moduleData = [];
        $slugThemeId = $baseSlugData ? $baseSlugData['theme_id'] : NULL;
        switch ($slugThemeId) {
                // NPG
            case $configThemeIds['theme_id_npg']:
                $HSNPGController = new HSNPGController();
                $moduleData = $HSNPGController->getProfileDataForNPG($profileData, $moduleName);
                break;
                // P3
            case $configThemeIds['theme_id_p3']:
                $HSP3Controller = new HSP3Controller();
                $moduleData = $HSP3Controller->getProfileDataForP3($profileData, $moduleName);
                break;
                // TEAM
            case $configThemeIds['theme_id_lt']:
                $HSTeamController = new HSTeamController();
                $moduleData = $HSTeamController->getProfileDataForTeam($profileData, $moduleName);
                break;
                // INDIVIDUAL
            case $configThemeIds['theme_id_li']:
                $HSIndividualController = new HSIndividualController();
                $moduleData = $HSIndividualController->getProfileDataForIndividual($profileData, $moduleName);
                break;
                // DEFAULT
            default:
                $moduleData = [];
                break;
        }
        return $moduleData;
    }

    /**
     * Get custom config values from platform setting form.
     *
     * @return array
     *   Array of theme IDs.
     */
    public function getCsPlatformSettings()
    {
        $arrCSSettings = [];
        $config = \Drupal::config(HS_PLATFORM_SETTINGS);
        $arrCSSettings['theme_id_npg'] = $config->get('theme_id_npg');
        $arrCSSettings['theme_id_p3'] = $config->get('theme_id_p3');
        $arrCSSettings['theme_id_lt'] = $config->get('theme_id_lt');
        $arrCSSettings['theme_id_li'] = $config->get('theme_id_li');
        return $arrCSSettings;
    }

    /**
     * Get Site Configuration data.
     *
     * @return object
     *   Object of configuration variables.
     */
    public function getAdminContentConfigByThemeId()
    {
        $themeData = $this->hearsayCommon->getThemeIdByNode();
        $themeId = isset($themeData) ? $themeData['theme_id'] : NULL;
        $configThemeIds = $this->getCsPlatformSettings();
        $config = $themeName = '';
        // Determine the theme template based on the slug theme ID.
        switch ($themeId) {
                // NPG
            case $configThemeIds['theme_id_npg']:
                $config = \Drupal::config(HS_NPG_SETTINGS);
                $themeName = HS_NPG_AB;
                break;
                // P3
            case $configThemeIds['theme_id_p3']:
                $config = \Drupal::config(HS_P3_SETTINGS);
                $themeName = HS_P3_AB;
                break;
                // TEAM
            case $configThemeIds['theme_id_lt']:
                $config = \Drupal::config(HS_LIBRARY_TEAM_SETTINGS);
                $themeName = HS_LIBRARY_TEAM_AB;
                break;
                // INDIVIDUAL
            case $configThemeIds['theme_id_li']:
                $config = \Drupal::config(HS_LIBRARY_INDIVIDUAL_SETTINGS);
                $themeName = HS_LIBRARY_INDIVIDUAL_AB;
                break;
                // DEFAULT
            default:
                if (str_starts_with($_SERVER['REQUEST_URI'], '/user/login') || str_starts_with($_SERVER['REQUEST_URI'], '/user/password') || $_SERVER['REQUEST_URI'] == '/expired_page') {
                    $config = \Drupal::config(HS_LIBRARY_TEAM_SETTINGS);
                    $themeName = HS_LIBRARY_TEAM_AB;
                } else {
                    $config = '';
                    $themeName = '';
                }
                break;
        }
        return ['config' => $config, 'themeName' => $themeName];
    }

    /**
     * get node Processing for Sitemap
     */
    public function hideShowNodesInSitemap()
    {
        $profileData = $this->hearsayCommon->getProfileData(FALSE);
        $insightsData = $this->hearsayCommon->getSocialPostData(FALSE);
        $eventsData = $this->hearsayCommon->getEventsAPIData(FALSE);
        $themeData = $this->hearsayCommon->getThemeIdByNode();
        $currentTermId = isset($themeData) ? $themeData['term_id'] : NULL;

        if (empty($profileData->about_me_custom_content) && empty($profileData->team_members)) {
            $this->hearsayCommon->unpublishNodes(HS_ABOUT, $currentTermId);
        } else {
            $this->hearsayCommon->publishNodes(HS_ABOUT, $currentTermId);
        }
        if (empty($insightsData)) {
            $this->hearsayCommon->unpublishNodes(HS_NEWS, $currentTermId);
        } else {
            $this->hearsayCommon->publishNodes(HS_NEWS, $currentTermId);
        }
        if (empty($eventsData)) {
            $this->hearsayCommon->unpublishNodes(HS_EVENTS, $currentTermId);
        } else {
            $this->hearsayCommon->publishNodes(HS_EVENTS, $currentTermId);
        }
    }

    /**
     * Get processed contact data.
     *
     * @param string $contact
     *   Contact number.
     *
     * @return array
     *   Array of Contact to be linked and to be displayed.
     */
    public function getProcessedContact($contact)
    {
        $trimmedContact = '';
        $trimmedContact = preg_replace('/[^0-9]/', '', $contact);
        if (strlen($trimmedContact) <= 9 || strlen($trimmedContact) >= 12) {
            $contact_display = $contact;
            $contact = $trimmedContact;
        } else {
            $contact = $trimmedContact;
            $contact_display = $contact;
            if ($contact == '+') {
                $contact_display = $contact;
            } else {
                if (strlen($trimmedContact) == 10) {
                    $contact_display = '(' . substr($trimmedContact, 0, 3) . ') ' . substr($trimmedContact, 3, 3) . '-' . substr($trimmedContact, 6, 10);
                } elseif (strlen($trimmedContact) == 11) {
                    $contact_display = substr($trimmedContact, 0, 1) . ' (' . substr($trimmedContact, 1, 3) . ') ' . substr($trimmedContact, 4, 3) . '-' . substr($trimmedContact, 7, 11);
                }
            }
        }
        return ['contact' => $contact, 'contact_display' => $contact_display];
    }
}
