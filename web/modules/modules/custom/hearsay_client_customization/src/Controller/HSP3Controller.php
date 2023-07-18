<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\media\Entity\Media;

/**
 * Class Hearsay P3 Controller.
 */

class HSP3Controller extends ControllerBase
{
    /**
     * get P3 Controller Theme IDs
     * @return array Array of client specific theme ids
     */

    protected $hearsayCommon;
    protected $hearsayClientCustomization;
    protected $siteTools;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hearsayCommon = new HearsayCommon();
        $this->hearsayClientCustomization = new HearsayClientCustomization();
        $this->siteTools = \Drupal::service('hearsay_preview.site_tools');
    }

    /**
     * Get Profile Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getProfileDataForP3($profileData, $moduleName)
    {
        $moduleData = [];
        $config = $this->hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];
        switch ($moduleName) {
                // Hero Banner
            case HS_BANNER:
                $moduleData = $this->getHeroBannerData($profileData, $config);
                break;
                // Our Story
            case HS_OUR_STORY_MODULE:
                $moduleData = $this->getOurStoryData($profileData, $config);
                break;
                // How we Work with you
            case HS_HOW_WE_WORK:
                $moduleData = $this->getWorkWithYouData($profileData, $config);
                break;
                // About Thrivent
            case HS_ABOUT_THRIVENT:
                $moduleData = $this->getAboutThriventData($profileData, $config);
                break;
                // Membership Benefits
            case HS_THRIVENT_MEMBERSHIP:
                $moduleData = $this->getMembershipBenefitsData($profileData, $config);
                break;
                // Our advice approach
            case HS_OUR_ADVICE_APPROACH:
                $moduleData = $this->getAdviceApproachData($profileData, $config);
                break;
                // Community Impact
            case HS_COMMUNITY_IMPACT:
                $moduleData = $this->getCommunityImpactData($profileData, $config);
                break;
                // Office Info
            case HS_OFFICE_INFORMATION:
                $moduleData = $this->getOfficeInformationData($profileData, $config);
                break;
                // Team Members
            case HS_TEAM_MEMBERS:
                $moduleData = $this->getTeamMembersData($profileData, $config);
                break;
                // Products
            case HS_PRODUCTS:
                $moduleData = $this->getProductsData($profileData, $config);
                break;
                // Short Bio
            case HS_SHORT_BIO_MODULE:
                $moduleData = $this->getShortBioData($profileData, $config);
                break;
                // Additional Information
            case HS_ADDITIONAL_INFO:
                $moduleData = $this->getAdditionalInformationData($profileData, $config);
                break;
                // Insights
            case HS_INSIGHTS_MODULE:
                $moduleData = $this->getInsightsData($profileData, $config);
                break;
                // Events
            case HS_EVENTS_MODULE:
                $moduleData = $this->getEventsData($profileData, $config);
                break;
                // Footer
            case HS_FOOTER:
                $moduleData = $this->getFooterData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Hero Banner section for Team Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and solution section data to be populated inside module
     */
    public function getHeroBannerData($profileData, $config)
    {
        $moduleData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        if ($nodeType == 'home') {
            $bannerMedia = $bannerUrl = '';

            // Get Default Banner Image Link
            $defaultBanner = $config->get('header_banner_image') ?? '';
            $defaultBannerDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($defaultBanner, 'field_banner_canonical_name', HS_P3_AB);
            $defaultBannerMedia = reset($defaultBannerDetails);
            $defaultBannerUrl = $this->hearsayCommon->getMediaImageUrl($defaultBannerMedia->field_media_image->target_id); // Get media image URL

            // Banner Image Link from PE
            if ($profileData->marketing_hero) {
                if ($profileData->marketing_hero->marketing_hero_image) { // If Default Override image is provided
                    $bannerUrl = $profileData->marketing_hero->marketing_hero_image;
                } else {
                    if ($profileData->marketing_hero->default_marketing_hero_image) { // If Default image selected form dropdown
                        $bannerDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($profileData->marketing_hero->default_marketing_hero_image[0], 'field_banner_canonical_name', HS_P3_AB);
                        $bannerMedia = reset($bannerDetails);
                        $bannerUrl = $this->hearsayCommon->getMediaImageUrl($bannerMedia->field_media_image->target_id); // Get media image URL
                    } else {
                        $bannerUrl = $defaultBannerUrl ?? '';
                    }
                }
            } else { // Else set Admin Content Default Image
                $bannerUrl = $defaultBannerUrl ?? '';
            }

            // Default Team Photo Image Link
            $defaultTeamBanner = $config->get('banner_team_image') ?? '';
            $defaultTeamBannerDetails = $this->hearsayCommon->getCanonicalMediaDetails($defaultTeamBanner, 'field_image_canonical_name', HS_P3_AB);
            $defaultTeamBannerMedia = reset($defaultTeamBannerDetails);
            $defaultTeamBannerUrl = $this->hearsayCommon->getMediaImageUrl($defaultTeamBannerMedia->field_media_image->target_id); // Get media image URL

            $moduleData = [
                'theme' => HS_P3_AB,
                'node_type' => $nodeType,
                'team_name' => $profileData->team_name ?? NULL,
                'banner_layout' => $profileData->homepage_hero_layout[0] ?? NULL,
                'banner_url' => $bannerUrl ?? NULL,
                'marketing_hero_title' => isset($profileData->marketing_hero) ? $profileData->marketing_hero->marketing_hero_title : '',
                'marketing_hero_text' => isset($profileData->marketing_hero) ? $profileData->marketing_hero->marketing_hero_text : '',
                'team_photo' => $profileData->profile_photo ?? $defaultTeamBannerUrl ?? NULL,
                'phone' => $this->hearsayClientCustomization->getProcessedContact($profileData->phones->phone),
                'email' => $profileData->email ?? NULL,
            ];
        } else {
            if (in_array($nodeType, [HS_ABOUT, HS_OUR_TEAM, HS_OUR_STORY, HS_HOW_WE_WORK, HS_COMMUNITY_IMPACT, HS_COMMUNITY_IMPACT_DETAILS, HS_ABOUT_THRIVENT, HS_THRIVENT_MEMBERSHIP, HS_STRONG_AND_STABLE, HS_OUR_ADVICE_APPROACH])) {
                $bannerVar = HS_ABOUT . '_banner_image';
            } elseif (in_array($nodeType, [HS_SOLUTIONS, HS_SOLUTIONS_DETAILS])) {
                $bannerVar = HS_SOLUTIONS . '_banner_image';
            } elseif (in_array($nodeType, [HS_EVENTS, HS_EVENTS_DETAIL])) {
                $bannerVar = HS_EVENTS . '_banner_image';
            } elseif (in_array($nodeType, [HS_RESOURCES, HS_NEWS_INSIGHTS, HS_NEWS_INSIGHTS_DETAILS, HS_ADDITIONAL_INFO])) {
                $bannerVar = HS_RESOURCES . '_banner_image';
            } elseif ($nodeType == HS_LOCATION) {
                $bannerVar = HS_LOCATION . '_banner_image';
            }
            $titleVar = $nodeType . '_page_title';
            $defaultBanner = $config->get($bannerVar) ?? '';
            $defaultBannerDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($defaultBanner, 'field_banner_canonical_name', HS_P3_AB);
            $defaultBannerMedia = reset($defaultBannerDetails);
            $defaultBannerUrl = $defaultBannerMedia ? $this->hearsayCommon->getMediaImageUrl($defaultBannerMedia->field_media_image->target_id) : null; // Get media image URL

            $moduleData = [
                'theme' => HS_P3_AB,
                'node_type' => $nodeType,
                'banner_url' => $defaultBannerUrl ?? NULL,
                'page_title' => $config->get($titleVar) ?? '',
            ];
        }
        return $moduleData;
    }

    /**
     * Get Our Story Data for P3 Theme all nodes
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getOurStoryData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeOurStoryData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutOurStoryData($profileData, $config);
                break;
                // Our Story
            case HS_OUR_STORY:
                $moduleData = $this->getDetailOurStoryData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Our Team Data for P3 Theme all nodes
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getTeamMembersData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeOurTeamData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutOurTeamData($config);
                break;
                // Our Team
            case HS_OUR_TEAM:
                $moduleData = $this->getDetailOurTeamData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Our Stroy Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeOurStoryData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'our_story_home_page' => isset($profileData->homepage_layouts) ? (isset($profileData->homepage_layouts->our_story_hp[0]) ? $profileData->homepage_layouts->our_story_hp[0] : TRUE) : TRUE,
            'our_story_layout' => isset($profileData->homepage_layouts) ? (isset($profileData->homepage_layouts->our_story_layout_options[0]) ? $profileData->homepage_layouts->our_story_layout_options[0] : $config->get('our_story_layout')) : $config->get('our_story_layout'),
            'title' => $profileData->custom_data_1->thumbnail_title ?? NULL,
            'sub_headline' => $profileData->custom_data_1->thumbnail_short_description ?? NULL,
            'short_copy' => $profileData->custom_data_1->thumbnail_long_description ?? NULL,
            'thumbnail_image' => $profileData->custom_data_1->thumbnail_featured_image ?? NULL,
            'video' => $profileData->custom_data_1->thumbnail_video ? KALTURA_LINK_1 . $profileData->custom_data_1->thumbnail_video . KALTURA_LINK_2 : '',
            'media_alt' => $profileData->custom_data_1->thumbnail_media_alt ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_STORY)['link'],
            'link_text' => $config->get('our_story_homepage_link_text') ?? NULL,
            // Condition for hiding Learn more button depending on internal page description
            'is_detail_available' => isset($profileData->custom_data_1->page_long_description) ? true : false,
        ];
        return $moduleData;
    }

    /**
     * Get Our Stroy Data for P3 Theme About page
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAboutOurStoryData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('our_story_about_title') ?? NULL,
            'description' => $config->get('our_story_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_STORY)['link'],
            'link_text' => $config->get('our_story_about_link_text') ?? NULL,
            'short_copy' => $profileData->custom_data_1->page_long_description ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Our Stroy Data for P3 Theme Our Story page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getDetailOurStoryData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_OUR_STORY,
            'page_title' => $config->get('our_story_page_title') ?? NULL,
            'title' => $profileData->custom_data_1->page_title ?? NULL,
            'sub_headline' => $profileData->custom_data_1->page_short_description ?? NULL,
            'short_copy' => $profileData->custom_data_1->page_long_description ?? NULL,
            'page_image' => $profileData->custom_data_1->page_featured_image ?? NULL,
            'video' => $profileData->custom_data_1->page_video ? KALTURA_LINK_1 . $profileData->custom_data_1->page_video . KALTURA_LINK_2 : '',
            'media_alt' => $profileData->custom_data_1->page_media_alt ?? NULL,
            'disclaimer' => $config->get('our_story_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Our Team Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeOurTeamData($profileData, $config)
    {
        $moduleData = $teamDetails = [];
        $teamLogoName = $config->get('team_logo') != '' ? $config->get('team_logo') : '';
        $teamLogoDetails = $this->hearsayCommon->getCanonicalMediaDetails($teamLogoName, 'field_image_canonical_name');
        $teamLogoMedia = reset($teamLogoDetails);
        $teamLogoUrl = $this->hearsayCommon->getMediaImageUrl($teamLogoMedia->field_media_image->target_id); // Get media image URL
        foreach ($profileData->team_members as $teamMember) {
            if ($teamMember->show_on_front_page == 'true') {
                $teamDetails[] = [
                    'show_on_front_page' => $teamMember->show_on_front_page ?? NULL,
                    'photo' => isset($teamMember->photo) ? $teamMember->photo : $teamLogoUrl,
                    'name' => $teamMember->name ?? NULL,
                    'qualifications' => $teamMember->qualifications ?? NULL,
                    'title' => $teamMember->title ?? NULL,
                ];
            }
        }
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'our_team_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->our_team_hp[0] ? $profileData->homepage_layouts->our_team_hp[0] : TRUE) : TRUE,
            'team_details' => $teamDetails,
            'section_title' => $config->get('our_team_homepage_title') ?? NULL,
            'link_text' => $config->get('our_team_homepage_link_text') ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_TEAM)['link'],
        ];
        return $moduleData;
    }

    /**
     * Get Our Team Data for P3 Theme About page
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAboutOurTeamData($config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('our_team_about_title') ?? NULL,
            'description' => $config->get('our_team_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_TEAM)['link'],
            'link_text' => $config->get('our_team_about_link_text') ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Our Team Data for P3 Theme Our Team page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getDetailOurTeamData($profileData, $config)
    {
        $moduleData = $teamDetails = [];
        $teamLogoName = $config->get('team_logo') != '' ? $config->get('team_logo') : '';
        $teamLogoDetails = $this->hearsayCommon->getCanonicalMediaDetails($teamLogoName, 'field_image_canonical_name');
        $teamLogoMedia = reset($teamLogoDetails);
        $teamLogoUrl = $this->hearsayCommon->getMediaImageUrl($teamLogoMedia->field_media_image->target_id); // Get media image URL

        foreach ($profileData->team_members as $teamMember) {
            $contact = $this->hearsayClientCustomization->getProcessedContact($teamMember->phone);
            $contacts['phone'] = $contact['contact'];
            $contacts['phone_display'] = $contact['contact_display'];
            $teamDetails[] = [
                'photo' => isset($teamMember->photo) ? $teamMember->photo : $teamLogoUrl,
                'twitter' => !empty($teamMember->twitter) ? 'https://www.twitter.com/' .  $teamMember->twitter : '',
                'linkedin' => !empty($teamMember->linkedin) ? 'https://www.linkedin.com/' . $teamMember->linkedin : '',
                'facebook' => !empty($teamMember->facebook) ? 'https://www.facebook.com/' . $teamMember->facebook : '',
                'instagram' => !empty($teamMember->instagram) ? 'https://www.instagram.com/' . $teamMember->instagram : '',
                'name' => $teamMember->name ?? NULL,
                'qualifications' => $teamMember->qualifications ?? NULL,
                'title' => $teamMember->title ?? NULL,
                'bio' => $teamMember->bio ?? NULL,
                'licensing_info' => $teamMember->licensing_info ?? NULL,
                'email' => $teamMember->email ?? NULL,
                'phone' => $contacts['phone'] ?? NULL,
                'phone_display' => $contacts['phone_display'] ?? NULL,
                'office_location' => $teamMember->office_location ?? NULL,
            ];
        }
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_OUR_TEAM,
            'page_title' => $config->get('our_team_page_title') ?? NULL,
            'team_details' => $teamDetails,
            'disclaimer' => $config->get('our_team_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Office Information Data for Team Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and Office data to be populated inside module
     */
    public function getOfficeInformationData($profileData, $config)
    {
        $officesData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $mainOfficeData = $this->getMainOfficeDetails($profileData, $config);
        $subOfficeData = $this->getSubOfficeDetails($profileData);
        $officesData = array_merge($mainOfficeData, $subOfficeData);
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType,
            'module_title' => $config->get('office_information_title') ?? NULL,
            'office_data' => $officesData,
        ];
        return $moduleData;
    }

    /**
     * Get Main Office Data for Individual Theme
     * @param array Profile Data received form API Response
     * @return array Array of main Office data to be populated inside module
     */
    public function getMainOfficeDetails($profileData, $config)
    {
        $mainOfficeData = $contacts = [];
        $latitude = $longitude = $json = '';
        $officeTitle = isset($profileData->main_office_title) ? $profileData->main_office_title : $config->get('main_office_title');
        $address = $profileData->address != '' ? $profileData->address : '';
        $mapAddress = $address->street . '+' . $address->city . '+' . $address->state . ',+' . $address->zip_code;
        $json = file_get_contents(MAP_API_1 . str_replace(" ", "+", $mapAddress) . MAP_API_2);
        $json = json_decode($json);
        if ($json->results) {
            $latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        }
        $officeImage = isset($profileData->office_photo) ? $profileData->office_photo : '';
        $phones = isset($profileData->phones) ? $profileData->phones : '';
        foreach ($phones as $key => $value) {
            $trimmedContact = preg_replace('/[^0-9]/', '', $value);
            $contacts[$key] = $trimmedContact;
            $contacts[$key . '_display'] = $value;
            if ($value[0] == '+') {
                $contacts[$key . '_display'] = $value;
            } else {
                if (strlen($trimmedContact) == 10) {
                    $contacts[$key . '_display'] = '(' . substr($trimmedContact, 0, 3) . ') ' . substr($trimmedContact, 3, 3) . '-' . substr($trimmedContact, 6, 10);
                } elseif (strlen($trimmedContact) == 11) {
                    $contacts[$key . '_display'] = substr($trimmedContact, 0, 1) . ' (' . substr($trimmedContact, 1, 3) . ') ' . substr($trimmedContact, 4, 3) . '-' . substr($trimmedContact, 7, 11);
                }
            }
        }

        $contacts['email'] = isset($profileData->email) ? $profileData->email : '';
        $officeHours = isset($profileData->office_hours) ? $profileData->office_hours : '';

        $mainOfficeData[] = [
            'office_title' => $officeTitle,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'office_image' => $officeImage,
            'contacts' => $contacts != '' ? $contacts : NULL,
            'office_hours' => $officeHours,
            'address' => $address
        ];
        return $mainOfficeData;
    }

    /**
     * Get Sub Offices Data for Individual Theme
     * @param array Profile Data received form API Response
     * @return array Array of Sub Offices data to be populated inside module
     */
    public function getSubOfficeDetails($profileData)
    {
        $subOfficeData = $contacts = [];
        $subOffices = isset($profileData->suboffices) ? $profileData->suboffices : '';
        if ($subOffices) {
            foreach ($subOffices as $office) {
                $address = [];
                $latitude = $longitude = $json = '';
                $officeTitle = $office->name != '' ? $office->name : '';
                $officeImage = $office->image != '' ? $office->image : '';
                $officeHours = $office->office_hours != '' ? $office->office_hours : '';
                $address = [
                    'street' => $office->street != '' ? $office->street : '',
                    'suite' => $office->suite != ''  ? $office->suite : '',
                    'city' => $office->city != ''  ? $office->city : '',
                    'state' => $office->state != ''  ? $office->state : '',
                    'zip_code' => $office->zip_code != ''  ? $office->zip_code : '',
                ];
                $mapAddress = $office->street . '+' . $office->suite . '+' . $office->city . '+' . $office->state . '+' . $office->zip_code;
                $json = file_get_contents(MAP_API_1 . str_replace(" ", "+", str_replace(" ", "+", $mapAddress)) . MAP_API_2);
                $json = json_decode($json);
                if ($json->results) {
                    $latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                    $longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                }

                // -------------- process the phone numbers --------------
                // phone
                if (isset($office->phone)) {
                    $contact = $this->hearsayClientCustomization->getProcessedContact($office->phone);
                    $contacts['phone'] = $contact['contact'];
                    $contacts['phone_display'] = $contact['contact_display'];
                }

                // fax
                if (isset($office->fax)) {
                    $contact = $this->hearsayClientCustomization->getProcessedContact($office->fax);
                    $contacts['fax'] = $contact['contact'];
                    $contacts['fax_display'] = $contact['contact_display'];
                }

                // toll_free
                if (isset($office->toll_free)) {
                    $contact = $this->hearsayClientCustomization->getProcessedContact($office->toll_free);
                    $contacts['toll_free'] = $contact['contact'];
                    $contacts['toll_free_display'] = $contact['contact_display'];
                }

                $subOfficeData[] = [
                    'office_title' => $officeTitle,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'office_image' => $officeImage,
                    'contacts' => $contacts != '' ? $contacts : NULL,
                    'office_hours' => $officeHours,
                    'address' => $address != '' ? $address : NULL,
                ];
            }
        }
        return $subOfficeData;
    }

    /**
     * Get How We Work With You Data for P3 Theme all nodes
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getWorkWithYouData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeWorkWithYouData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutWorkWithYouData($profileData, $config);
                break;
                // Our Story
            case HS_HOW_WE_WORK:
                $moduleData = $this->getDetailWorkWithYouData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get How We Work With You Data Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeWorkWithYouData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'work_with_you_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->how_we_work_hp[0] ? $profileData->homepage_layouts->how_we_work_hp[0] : TRUE) : TRUE,
            'work_with_you_layout' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->how_we_work_layout_options[0] ? $profileData->homepage_layouts->how_we_work_layout_options[0] : $config->get('work_with_you_layout')) : $config->get('work_with_you_layout'),
            'title' => $profileData->custom_data_2->thumbnail_title ?? NULL,
            'sub_headline' => $profileData->custom_data_2->thumbnail_short_description ?? NULL,
            'short_copy' => $profileData->custom_data_2->thumbnail_long_description ?? NULL,
            'thumbnail_image' => $profileData->custom_data_2->thumbnail_featured_image ?? NULL,
            'video' => $profileData->custom_data_2->thumbnail_video ? KALTURA_LINK_1 . $profileData->custom_data_2->thumbnail_video . KALTURA_LINK_2 : '',
            'media_alt' => $profileData->custom_data_2->thumbnail_media_alt ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_HOW_WE_WORK)['link'],
            'link_text' => $config->get('work_with_you_homepage_link_text') ?? NULL,
            // Condition for hiding Learn more button depending on internal page description
            'is_detail_available' => isset($profileData->custom_data_2->page_long_description) ? true : false,
        ];
        return $moduleData;
    }

    /**
     * Get How We Work With You Data Data for P3 Theme About page
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAboutWorkWithYouData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('work_with_you_about_title') ?? NULL,
            'description' => $config->get('work_with_you_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_HOW_WE_WORK)['link'],
            'link_text' => $config->get('work_with_you_about_link_text') ?? NULL,
            'short_copy' => $profileData->custom_data_2->page_long_description ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get How We Work With You Data Data for P3 Theme How We Work With You page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getDetailWorkWithYouData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOW_WE_WORK,
            'page_title' => $config->get('work_with_you_page_title') ?? NULL,
            'title' => $profileData->custom_data_2->page_title ?? NULL,
            'sub_headline' => $profileData->custom_data_2->page_short_description ?? NULL,
            'short_copy' => $profileData->custom_data_2->page_long_description ?? NULL,
            'page_image' => $profileData->custom_data_2->page_featured_image ?? NULL,
            'video' => $profileData->custom_data_2->page_video ? KALTURA_LINK_1 . $profileData->custom_data_2->page_video . KALTURA_LINK_2 : '',
            'media_alt' => $profileData->custom_data_2->page_media_alt ?? NULL,
            'disclaimer' => $config->get('work_with_you_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get About Thrivent Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAboutThriventData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $profileData->homepage_layouts = $profileData->homepage_layouts;
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeAboutThriventData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutPgAboutThriventData($config);
                break;
                // About Thrivent
            case HS_ABOUT_THRIVENT:
                $moduleData = $this->getDetailAboutThriventData($config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get About Thrivent Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeAboutThriventData($profileData, $config)
    {
        $moduleData = [];
        $thumbnailImgMedia = $thumbnailImgName = $thumbnailImgUrl = $thumbnailVideoName = $thumbnailVideoUrl = $thumbnailVideoMedia = '';
        // Get Thumbnail Image URL
        $thumbnailImgName = $config->get('about_thrivent_homepage_thumbnail') != '' ? $config->get('about_thrivent_homepage_thumbnail') : '';
        $thumbnailImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailImgName, 'field_image_canonical_name');
        $thumbnailImgMedia = reset($thumbnailImgDetails);
        $thumbnailImgUrl = $this->hearsayCommon->getMediaImageUrl($thumbnailImgMedia->field_media_image->target_id); // Get media image URL

        // Get Thumbnail Video URL
        $thumbnailVideoName = $config->get('about_thrivent_homepage_video') != '' ? $config->get('about_thrivent_homepage_video') : '';
        $thumbnailVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailVideoName, 'field_video_canonical_name');
        $thumbnailVideoMedia = reset($thumbnailVideoDetails);
        $thumbnailVideoUrl = $this->hearsayCommon->getMediaImageUrl($thumbnailVideoMedia->field_media_video_file->target_id); // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'about_thrivent_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->about_thrivent_hp[0] ? $profileData->homepage_layouts->about_thrivent_hp[0] : TRUE) : TRUE,
            'about_thrivent_layout' => isset($profileData->homepage_layouts) ? (isset($profileData->homepage_layouts->about_thrivent_layout_options[0]) ? $profileData->homepage_layouts->about_thrivent_layout_options[0] : $config->get('about_thrivent_layout')) : $config->get('about_thrivent_layout'),
            'title' => $config->get('about_thrivent_homepage_title') ?? NULL,
            'sub_headline' => $config->get('about_thrivent_homepage_subheadline') ?? NULL,
            'short_copy' => $config->get('about_thrivent_homepage_short_copy')['value'] ?? NULL,
            'thumbnail_image' => $thumbnailImgUrl ?? NULL,
            'video' => $thumbnailVideoUrl != '' ? $thumbnailVideoUrl : $config->get('about_thrivent_homepage_video_link'),
            'media_alt' => $config->get('about_thrivent_homepage_alt') ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_ABOUT_THRIVENT)['link'],
            'link_text' => $config->get('about_thrivent_homepage_link_text') ?? NULL,
            // Condition for hiding Learn more button depending on internal page description
            'is_detail_available' => $config->get('about_thrivent_detailpage_short_copy') ? ($config->get('about_thrivent_detailpage_short_copy')['value'] != NULL ? true : false) : false,
        ];
        return $moduleData;
    }

    /**
     * Get About Thrivent Data for P3 Theme About page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getAboutPgAboutThriventData($config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('about_thrivent_about_title') ?? NULL,
            'description' => $config->get('about_thrivent_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_ABOUT_THRIVENT)['link'],
            'link_text' => $config->get('about_thrivent_about_link_text') ?? NULL,
            'short_copy' => $config->get('about_thrivent_detailpage_short_copy') ? ($config->get('about_thrivent_detailpage_short_copy')['value'] != NULL ? true : false) : false,
        ];
        return $moduleData;
    }

    /**
     * Get About Thrivent Data for P3 Theme About Thrivent page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getDetailAboutThriventData($config)
    {
        $moduleData = [];
        $pageImgMedia = $pageImgName = $pageImgUrl = $pageVideoName = $pageVideoUrl = $pageVideoMedia = '';
        // Get Thumbnail Image URL
        $pageImgName = $config->get('about_thrivent_detailpage_thumbnail') != '' ? $config->get('about_thrivent_detailpage_thumbnail') : '';
        $pageImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageImgName, 'field_image_canonical_name');
        $pageImgMedia = reset($pageImgDetails);
        $pageImgUrl = $this->hearsayCommon->getMediaImageUrl($pageImgMedia->field_media_image->target_id); // Get media image URL

        // Get Thumbnail Video URL
        $pageVideoName = $config->get('about_thrivent_detailpage_video') != '' ? $config->get('about_thrivent_detailpage_video') : '';
        $pageVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageVideoName, 'field_video_canonical_name');
        $pageVideoMedia = reset($pageVideoDetails);
        $pageVideoUrl = $pageVideoMedia ? $this->hearsayCommon->getMediaImageUrl($pageVideoMedia->field_media_video_file->target_id) : null; // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT_THRIVENT,
            'page_title' => $config->get('about_thrivent_page_title') ?? NULL,
            'title' => $config->get('about_thrivent_detailpage_title') ?? NULL,
            'sub_headline' => $config->get('about_thrivent_detailpage_subheadline') ?? NULL,
            'short_copy' => $config->get('about_thrivent_detailpage_short_copy')['value'] ?? NULL,
            'page_image' => $pageImgUrl ?? NULL,
            'video' => $pageVideoUrl != '' ? $pageVideoUrl : $config->get('about_thrivent_detailpage_video_link'),
            'media_alt' => $config->get('about_thrivent_detailpage_alt') ?? NULL,
            'disclaimer' => $config->get('about_thrivent_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Thrivent Membership Benefits Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getMembershipBenefitsData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeMembershipBenefitsData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutPgMembershipBenefitsData($config);
                break;
                // Thrivent Membership Benefits
            case HS_THRIVENT_MEMBERSHIP:
                $moduleData = $this->getDetailMembershipBenefitsData($config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Thrivent Membership Benefits Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeMembershipBenefitsData($profileData, $config)
    {
        $moduleData = [];
        $thumbnailImgMedia = $thumbnailImgName = $thumbnailImgUrl = $thumbnailVideoName = $thumbnailVideoUrl = $thumbnailVideoMedia = '';
        // Get Thumbnail Image URL
        $thumbnailImgName = $config->get('membership_benefits_homepage_thumbnail') != '' ? $config->get('membership_benefits_homepage_thumbnail') : '';
        $thumbnailImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailImgName, 'field_image_canonical_name');
        $thumbnailImgMedia = reset($thumbnailImgDetails);
        $thumbnailImgUrl = $this->hearsayCommon->getMediaImageUrl($thumbnailImgMedia->field_media_image->target_id); // Get media image URL

        // Get Thumbnail Video URL
        $thumbnailVideoName = $config->get('membership_benefits_homepage_video') != '' ? $config->get('membership_benefits_homepage_video') : '';
        $thumbnailVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailVideoName, 'field_video_canonical_name');
        $thumbnailVideoMedia = reset($thumbnailVideoDetails);
        $thumbnailVideoUrl = $thumbnailVideoMedia ? $this->hearsayCommon->getMediaImageUrl($thumbnailVideoMedia->field_media_video_file->target_id) : null; // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'membership_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->thrivent_membership_hp[0] ? $profileData->homepage_layouts->thrivent_membership_hp[0] : TRUE) : TRUE,
            'membership_layout' => isset($profileData->homepage_layouts) ? (isset($profileData->homepage_layouts->thrivent_membership_layout_options[0]) ? $profileData->homepage_layouts->thrivent_membership_layout_options[0] : $config->get('membership_benefits_layout')) : $config->get('membership_benefits_layout'),
            'title' => $config->get('membership_benefits_homepage_title') ?? NULL,
            'sub_headline' => $config->get('membership_benefits_homepage_subheadline') ?? NULL,
            'short_copy' => $config->get('membership_benefits_homepage_short_copy')['value'] ?? NULL,
            'thumbnail_image' => $thumbnailImgUrl ?? NULL,
            'video' => $thumbnailVideoUrl != '' ? $thumbnailVideoUrl : $config->get('membership_benefits_homepage_video_link'),
            'media_alt' => $config->get('membership_benefits_homepage_alt') ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_THRIVENT_MEMBERSHIP)['link'],
            'link_text' => $config->get('membership_benefits_homepage_link_text') ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Thrivent Membership Benefits Data for P3 Theme About page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getAboutPgMembershipBenefitsData($config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('membership_benefits_about_title') ?? NULL,
            'description' => $config->get('membership_benefits_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_THRIVENT_MEMBERSHIP)['link'],
            'link_text' => $config->get('membership_benefits_about_link_text') ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Thrivent Membership Benefits Data for P3 Theme Thrivent Membership Benefits page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getDetailMembershipBenefitsData($config)
    {
        $moduleData = [];
        $pageImgMedia = $pageImgName = $pageImgUrl = $pageVideoName = $pageVideoUrl = $pageVideoMedia = '';
        // Get Thumbnail Image URL
        $pageImgName = $config->get('membership_benefits_detailpage_thumbnail') != '' ? $config->get('membership_benefits_detailpage_thumbnail') : '';
        $pageImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageImgName, 'field_image_canonical_name');
        $pageImgMedia = reset($pageImgDetails);
        $pageImgUrl = $this->hearsayCommon->getMediaImageUrl($pageImgMedia->field_media_image->target_id); // Get media image URL

        // Get Thumbnail Video URL
        $pageVideoName = $config->get('membership_benefits_detailpage_video') != '' ? $config->get('membership_benefits_detailpage_video') : '';
        $pageVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageVideoName, 'field_video_canonical_name');
        $pageVideoMedia = reset($pageVideoDetails);
        $pageVideoUrl = $pageVideoMedia ? $this->hearsayCommon->getMediaImageUrl($pageVideoMedia->field_media_video_file->target_id) : null; // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_THRIVENT_MEMBERSHIP,
            'page_title' => $config->get('membership_benefits_page_title') ?? NULL,
            'title' => $config->get('membership_benefits_detailpage_title') ?? NULL,
            'sub_headline' => $config->get('membership_benefits_detailpage_subheadline') ?? NULL,
            'short_copy' => $config->get('membership_benefits_detailpage_short_copy')['value'] ?? NULL,
            'page_image' => $pageImgUrl ?? NULL,
            'video' => $pageVideoUrl != '' ? $pageVideoUrl : $config->get('membership_benefits_detailpage_video_link'),
            'media_alt' => $config->get('membership_benefits_detailpage_alt') ?? NULL,
            'disclaimer' => $config->get('membership_benefits_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Our advice approach Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAdviceApproachData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeAdviceApproachData($profileData, $config);
                break;
                // About Us
            case HS_ABOUT:
                $moduleData = $this->getAboutPgAdviceApproachData($config);
                break;
                // Our advice approach
            case HS_OUR_ADVICE_APPROACH:
                $moduleData = $this->getDetailAdviceApproachData($config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     *  Get Our advice approach Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeAdviceApproachData($profileData, $config)
    {
        $moduleData = [];
        $thumbnailImgMedia = $thumbnailImgName = $thumbnailImgUrl = $thumbnailVideoName = $thumbnailVideoUrl = $thumbnailVideoMedia = '';
        // Get Thumbnail Image URL
        $thumbnailImgName = $config->get('our_advice_approach_homepage_thumbnail') != '' ? $config->get('our_advice_approach_homepage_thumbnail') : '';
        $thumbnailImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailImgName, 'field_image_canonical_name');
        $thumbnailImgMedia = reset($thumbnailImgDetails);
        $thumbnailImgUrl = $thumbnailImgMedia ? $this->hearsayCommon->getMediaImageUrl($thumbnailImgMedia->field_media_image->target_id) : null; // Get media image URL

        // Get Thumbnail Video URL
        $thumbnailVideoName = $config->get('our_advice_approach_homepage_video') != '' ? $config->get('our_advice_approach_homepage_video') : '';
        $thumbnailVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($thumbnailVideoName, 'field_video_canonical_name');
        $thumbnailVideoMedia = reset($thumbnailVideoDetails);
        $thumbnailVideoUrl = $this->hearsayCommon->getMediaImageUrl($thumbnailVideoMedia->field_media_video_file->target_id); // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'our_advice_approach_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->advice_approach_hp[0] ? $profileData->homepage_layouts->advice_approach_hp[0] : TRUE) : TRUE,
            'our_advice_approach_layout' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->advice_approach_layout_options[0] ? $profileData->homepage_layouts->advice_approach_layout_options[0] : $config->get('our_advice_approach_layout')) : $config->get('our_advice_approach_layout'),
            'title' => $config->get('our_advice_approach_homepage_title') ?? NULL,
            'sub_headline' => $config->get('our_advice_approach_homepage_subheadline') ?? NULL,
            'short_copy' => $config->get('our_advice_approach_homepage_short_copy')['value'] ?? NULL,
            'thumbnail_image' => $thumbnailImgUrl ?? NULL,
            'video' => $thumbnailVideoUrl != '' ? $thumbnailVideoUrl : $config->get('our_advice_approach_homepage_video_link'),
            'media_alt' => $config->get('our_advice_approach_homepage_alt') ?? NULL,
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_ADVICE_APPROACH)['link'],
            'link_text' => $config->get('our_advice_approach_homepage_link_text') ?? NULL,
            // Condition for hiding Learn more button depending on internal page description
            'is_detail_available' => $config->get('our_advice_approach_detailpage_short_copy') ? ($config->get('our_advice_approach_detailpage_short_copy')['value'] != NULL ? true : false) : false,
        ];
        return $moduleData;
    }

    /**
     * Get Our advice approach Data for P3 Theme About page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getAboutPgAdviceApproachData($config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('our_advice_approach_about_title') ?? NULL,
            'description' => $config->get('our_advice_approach_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_OUR_ADVICE_APPROACH)['link'],
            'link_text' => $config->get('our_advice_approach_about_link_text') ?? NULL,
            'short_copy' => $config->get('our_advice_approach_detailpage_short_copy') ? ($config->get('our_advice_approach_detailpage_short_copy')['value'] != NULL ? true : false) : false,
        ];
        return $moduleData;
    }

    /**
     * Get Our advice approach Data for P3 Theme Our advice approach page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getDetailAdviceApproachData($config)
    {
        $moduleData = [];
        $pageImgMedia = $pageImgName = $pageImgUrl = $pageVideoName = $pageVideoUrl = $pageVideoMedia = '';
        // Get Thumbnail Image URL
        $pageImgName = $config->get('our_advice_approach_detailpage_thumbnail') != '' ? $config->get('our_advice_approach_detailpage_thumbnail') : '';
        $pageImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageImgName, 'field_image_canonical_name');
        $pageImgMedia = reset($pageImgDetails);
        $pageImgUrl = $this->hearsayCommon->getMediaImageUrl($pageImgMedia->field_media_image->target_id); // Get media image URL

        // Get Thumbnail Video URL
        $pageVideoName = $config->get('our_advice_approach_detailpage_video') != '' ? $config->get('our_advice_approach_detailpage_video') : '';
        $pageVideoDetails = $this->hearsayCommon->getCanonicalMediaDetails($pageVideoName, 'field_video_canonical_name');
        $pageVideoMedia = reset($pageVideoDetails);
        $pageVideoUrl = $pageVideoMedia ? $this->hearsayCommon->getMediaImageUrl($pageVideoMedia->field_media_video_file->target_id) : null; // Get media video URL

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_OUR_ADVICE_APPROACH,
            'page_title' => $config->get('our_advice_approach_page_title') ?? NULL,
            'title' => $config->get('our_advice_approach_detailpage_title') ?? NULL,
            'sub_headline' => $config->get('our_advice_approach_detailpage_subheadline') ?? NULL,
            'short_copy' => $config->get('our_advice_approach_detailpage_short_copy')['value'] ?? NULL,
            'page_image' => $pageImgUrl ?? NULL,
            'video' => $pageVideoUrl != '' ? $pageVideoUrl : $config->get('our_advice_approach_detailpage_video_link'),
            'media_alt' => $config->get('our_advice_approach_detailpage_alt') ?? NULL,
            'disclaimer' => $config->get('our_advice_approach_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /* Get Community Impact Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getCommunityImpactData($profileData, $config)
    {
        $moduleData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($nodeType) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeCommunityImpactData($profileData, $config);
                break;
            case HS_ABOUT:
                $moduleData = $this->getAboutCommunityImpactData($config);
                break;
            case HS_COMMUNITY_IMPACT:
                $moduleData = $this->getListCommunityImpactData($profileData, $config);
                break;
            case HS_COMMUNITY_IMPACT_DETAILS:
                $moduleData = $this->getDetailsCommunityImpactData($profileData, $config);
                break;
        }
        return $moduleData;
    }

    /* Get Community Impacts Data and generate Node aliases for parent node as per each community impact data.
     * @param array Profile Data received form API Response
     * @return array Array of Community Impact elements
     */
    public function getCommunityImpacts($profileData)
    {
        $communityInvolvements = [];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_COMMUNITY_IMPACT)['link'];

        // Get Node ID for Details Page
        $detailsNodeId = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_COMMUNITY_IMPACT_DETAILS)['node_id'];

        // Get all existing Details Node Aliases
        $pathAliasStorage = \Drupal::entityTypeManager()->getStorage('path_alias');
        $aliasObjects = $pathAliasStorage->loadByProperties(['path' => '/node/' . $detailsNodeId]);

        // Delete Existing Aliases Except Root alias
        foreach ($aliasObjects as $aliasObject) {
            if ($aliasObject->get('alias')->value != $listingNodeLink . '/community-impact-detail') {
                $aliasObject->delete();
            }
        }

        // Get all Community Involvement Data
        foreach ($profileData->community_involvements as $communityImpact) {
            // Create Alias for Community Involvement
            $pathAlias = \Drupal\path_alias\Entity\PathAlias::create(
                [
                    'path' => '/node' . '/' . $detailsNodeId,
                    'alias' => $listingNodeLink . '/' . $communityImpact->canonical_name,
                ]
            );
            $pathAlias->save();

            $communityInvolvements[] = [
                'canonical_name' => $communityImpact->canonical_name ?? NULL,
                'card_link' => $listingNodeLink . '/' . urlencode($communityImpact->canonical_name),
                'title' => $communityImpact->title ?? NULL,
                'summary' => $communityImpact->summary ?? NULL,
                'long_description' => $communityImpact->long_description ?? NULL,
                'photo' => $communityImpact->photo ?? NULL,
                'video' => $communityImpact->video ? KALTURA_LINK_1 . $communityImpact->video . KALTURA_LINK_2 : '',
            ];
        }
        return $communityInvolvements;
    }

    /**
     * Get Community Impact Data for P3 Theme on Home Page
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getHomeCommunityImpactData($profileData, $config)
    {
        $moduleData = $communityInvolvements = [];
        $communityInvolvements = $this->getCommunityImpacts($profileData);
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_COMMUNITY_IMPACT)['link'];

        // Create Module Data Array
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'community_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->community_hp[0] ? $profileData->homepage_layouts->community_hp[0] : TRUE) : TRUE,
            'home_module_title' => $config->get('community_impact_homepage_title'),
            'home_link_text' => $config->get('community_impact_homepage_link_text'),
            'listing_link' => $listingNodeLink ?? NULL,
            'community_involvements' => $communityInvolvements,
        ];
        return $moduleData;
    }

    /* Get Community Impact Data for P3 Theme on About Page
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getAboutCommunityImpactData($config)
    {
        $moduleData = [];
        // Create Module Data Array
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ABOUT,
            'title' => $config->get('community_impact_about_title') ?? NULL,
            'description' => $config->get('community_impact_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_COMMUNITY_IMPACT)['link'],
            'link_text' => $config->get('community_impact_about_link_text') ?? NULL,
        ];
        return $moduleData;
    }

    /* Get Community Impact Data for P3 Theme on Listing Page
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getListCommunityImpactData($profileData, $config)
    {
        $moduleData = $communityInvolvements = [];
        $emptyStateImage = $config->get('community_impact_empty_image') ?? NULL;
        $emptyStateImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($emptyStateImage, 'field_image_canonical_name');
        $emptyStateImgMedia = reset($emptyStateImgDetails);
        $emptyStateImgUrl = $this->hearsayCommon->getMediaImageUrl($emptyStateImgMedia->field_media_image->target_id); // Get media image URL
        $communityInvolvements = $this->getCommunityImpacts($profileData);
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'community_involvements' => $communityInvolvements,
            'community_empty_state_msg' => $config->get('community_impact_empty_text') ?? NULL,
            'community_empty_state_img' => $emptyStateImgUrl,
            'disclaimer' => $config->get('community_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Community Impact Data for P3 Theme on Details Page
     * @param array Profile Data received form API Response
     * @return array Array of data to be populated inside module
     */
    public function getDetailsCommunityImpactData($profileData, $config)
    {
        $moduleData = [];
        $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
        $currentCommunityImpactCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
        if ($this->siteTools->isPreview() == true) {
            $currentCommunityImpactCanonical = explode("?", $currentCommunityImpactCanonical)[0];
        }
        foreach ($profileData->community_involvements as $communityImpact) {
            if ($communityImpact->canonical_name == $currentCommunityImpactCanonical) {
                $moduleData = [
                    'theme' => HS_P3_AB,
                    'node_type' => HS_COMMUNITY_IMPACT_DETAILS,
                    'title' => $communityImpact->title ?? NULL,
                    'summary' => $communityImpact->summary ?? NULL,
                    'long_description' => $communityImpact->long_description ?? NULL,
                    'photo' => $communityImpact->photo ?? NULL,
                    'video' => $communityImpact->video ? KALTURA_LINK_1 . $communityImpact->video . KALTURA_LINK_2 : '',
                    'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_COMMUNITY_IMPACT)['link'],
                    'link_text' => $config->get('community_detail_link_text') ?? NULL,
                    'disclaimer' => $config->get('community_detail_disclaimer_text')['value'] ?? NULL,
                ];
            }
        }
        return $moduleData;
    }

    /**
     * Get Products Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getProductsData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeProductsData($profileData, $config);
                break;
                // Solution Internal
            case HS_SOLUTIONS:
                $moduleData = $this->getListProductsData($profileData, $config);
                break;
                // Solution Details
            case HS_SOLUTIONS_DETAILS:
                $moduleData = $this->getDetailsProductsData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Products Data and generate Node aliases for parent node as per each product data.
     * @param array Profile Data received form API Response
     * @return array Array of Products elements
     */
    public function getProducts($profileData, $config)
    {
        $productInvolvements = [];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_SOLUTIONS)['link'];

        // Get Node ID for Details Page
        $detailsNodeId = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_SOLUTIONS_DETAILS)['node_id'];

        $productDetails = $advisorProductDetails = [];

        // Additional Products to be added for specific advisors.
        $additionalProducts = isset($profileData->additional_services) ? $profileData->additional_services : '';
        foreach ($additionalProducts as $additionalProduct) {
            $advisorProductDetails[] = [
                'image' => $additionalProduct->photo ?? null,
                'title' => $additionalProduct->title ?? null,
                'summary' => $additionalProduct->summary ?? null,
                'text' => $additionalProduct->long_description ?? null,
                'video' => $additionalProduct->video ?? null,
                'canonical_name' => $additionalProduct->canonical_name ?? null,
                'card_link' => $listingNodeLink . '/' . urlencode($additionalProduct->canonical_name),
            ];
        }

        // All products pulled from Admin.
        $allAdminProducts = isset($profileData->services_to_display) ? $profileData->services_to_display : '';
        foreach ($allAdminProducts as $productName) {
            $media_elements = \Drupal::entityQuery('media')->condition('bundle', 'npg_p3_solutions')->condition('field_solution_canonical_name', $productName)->condition('field_select_category', 'p3')->execute();
            $canonicalDetails = Media::loadMultiple($media_elements);
            $canonicalName = reset($canonicalDetails);
            $canonicalUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_media_image->target_id);
            $videoUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_solution_video->target_id);
            if (in_array($canonicalName->field_solution_canonical_name->value, $allAdminProducts)) {
                $productDetails[] = [
                    'image' => $canonicalUrl,
                    'alt_text' => $canonicalName->field_alt_text->value,
                    'title' => $canonicalName->field_solution_title->value,
                    'summary' => $canonicalName->field_solution_summary->value,
                    'text' => $canonicalName->field_solution_text->value,
                    'video' => $videoUrl != '' ? $videoUrl : $canonicalName->field_video_link->value,
                    'canonical_name' => $canonicalName->field_solution_canonical_name->value,
                    'category' => $canonicalName->field_select_category->value,
                    'card_link' => $listingNodeLink . '/' . urlencode($canonicalName->field_solution_canonical_name->value),
                ];
            }
        }

        $allProducts = array_merge($advisorProductDetails, $productDetails);

        // Get all existing Details Node Aliases
        $pathAliasStorage = \Drupal::entityTypeManager()->getStorage('path_alias');
        $aliasObjects = $pathAliasStorage->loadByProperties(['path' => '/node/' . $detailsNodeId]);

        // Delete Existing Aliases Except Root alias
        foreach ($aliasObjects as $aliasObject) {
            if ($aliasObject->get('alias')->value != $listingNodeLink . '/solutions-detail') {
                $aliasObject->delete();
            }
        }

        foreach ($allProducts as $product) {
            $product = (object)$product;
            // Create Alias for Product
            $pathAlias = \Drupal\path_alias\Entity\PathAlias::create(
                [
                    'path' => '/node' . '/' . $detailsNodeId,
                    'alias' => $listingNodeLink . '/' . $product->canonical_name,
                ]
            );
            $pathAlias->save();

            $productInvolvements[] = [
                'image' => $product->image ?? NULL,
                'alt_text' => $product->alt_text ?? NULL,
                'title' => $product->title ?? NULL,
                'summary' => $product->summary ?? NULL,
                'text' => $product->text ?? NULL,
                'video' => $product->video ?? NULL,
                'canonical_name' => $product->canonical_name ?? NULL,
                'card_link' => $product->card_link ?? NULL,
            ];
        }

        return $productInvolvements;
    }

    /**
     * Get Products Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeProductsData($profileData, $config)
    {
        $moduleData = $productInvolvements = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_SOLUTIONS)['link'];
        $productInvolvements = $this->getProducts($profileData, $config);
        $productTitle = $config->get('our_solutions_homepage_title') != '' ? $config->get('our_solutions_homepage_title') : '';

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'solution_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->solutions_hp[0] ? $profileData->homepage_layouts->solutions_hp[0] : TRUE) : TRUE,
            'product_title' => $productTitle ?? NULL,
            'home_link_text' => $config->get('our_solutions_homepage_link_text'),
            'listing_link' => $listingNodeLink ?? NULL,
            'product_involvements' => $productInvolvements,
        ];
        return $moduleData;
    }

    /**
     * Get Products Internal Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getListProductsData($profileData, $config)
    {
        $moduleData = $productInvolvements = [];
        $disclaimerText = $config->get('solutions_disclaimer_text')['value'] ?? NULL;
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $productInvolvements = $this->getProducts($profileData, $config);

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'disclaimer_text' => $disclaimerText ?? NULL,
            'product_involvements' => $productInvolvements,
        ];
        return $moduleData;
    }

    /**
     * Get Products Data for P3 Theme on Details Page
     * @param array Profile Data received form API Response
     * @return array Array of data to be populated inside module
     */
    public function getDetailsProductsData($profileData, $config)
    {
        $moduleData = [];
        $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
        $currentProductsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
        if ($this->siteTools->isPreview() == true) {
            $currentProductsCanonical = explode("?", $currentProductsCanonical)[0];
        }
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_SOLUTIONS)['link'];

        $allAdminProducts = isset($profileData->services_to_display) ? $profileData->services_to_display : '';
        foreach ($allAdminProducts as $productName) {
            $canonicalDetails = $this->hearsayCommon->getCanonicalMediaDetails($productName, 'field_solution_canonical_name');
            $canonicalName = reset($canonicalDetails);
            $canonicalUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_media_image->target_id);
            $videoUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_solution_video->target_id);
            if ($canonicalName->field_solution_canonical_name->value == $currentProductsCanonical) {
                $moduleData = [
                    'theme' => HS_P3_AB,
                    'node_type' => HS_SOLUTIONS_DETAILS,
                    'image' => $canonicalUrl,
                    'alt_text' => $canonicalName->field_alt_text->value,
                    'title' => $canonicalName->field_solution_title->value,
                    'summary' => $canonicalName->field_solution_summary->value,
                    'text' => $canonicalName->field_solution_text->value,
                    'video' => $videoUrl != '' ? $videoUrl : $canonicalName->field_video_link->value,
                    'listing_link' => $listingNodeLink ?? '',
                    'disclaimer_text' => $config->get('solutions_disclaimer_text')['value'] ?? NULL,
                    'link_text' => $config->get('solutions_detail_link_text') ?? NULL,
                ];
            }
        }

        $additionalProducts = isset($profileData->additional_services) ? $profileData->additional_services : '';
        foreach ($additionalProducts as $additionalProduct) {

            if ($additionalProduct->canonical_name == $currentProductsCanonical) {
                $moduleData = [
                    'theme' => HS_P3_AB,
                    'node_type' => HS_SOLUTIONS_DETAILS,
                    'image' => $additionalProduct->photo,
                    'title' => $additionalProduct->title,
                    'alt_text' => $additionalProduct->title,
                    'summary' => $additionalProduct->summary,
                    'text' => $additionalProduct->long_description,
                    'video' => isset($additionalProduct->video) ? KALTURA_LINK_1 . $additionalProduct->video . KALTURA_LINK_2 : '',
                    'canonical_name' => $additionalProduct->canonical_name,
                    'card_link' => $listingNodeLink . '/' . urlencode($additionalProduct->canonical_name),
                    'listing_link' => $listingNodeLink ?? '',
                    'disclaimer_text' => $config->get('solutions_disclaimer_text')['value'] ?? NULL,
                    'link_text' => $config->get('solutions_detail_link_text') ?? NULL,
                ];
            }
        }

        return $moduleData;
    }

    /**
     * Get Short Bio Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and Office data to be populated inside module
     */
    public function getShortBioData($profileData, $config)
    {
        $moduleData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType,
            'email' => $profileData->email ?? '',
            'phone' => isset($profileData->phones) ? $this->hearsayClientCustomization->getProcessedContact($profileData->phones->phone) : '',
            'welcome_text' => $profileData->welcome_text ?? '',
            'facebook' => $profileData->plain_social_networks->facebook ? HS_FACEBOOK . $profileData->plain_social_networks->facebook : '',
            'twitter' => $profileData->plain_social_networks->twitter ? HS_TWITTER . $profileData->plain_social_networks->twitter : '',
            'instagram' => $profileData->plain_social_networks->instagram ? HS_INSTAGRAM . $profileData->plain_social_networks->instagram : '',
            'linkedin' => $profileData->plain_social_networks->linkedin ? HS_LINKEDIN . $profileData->plain_social_networks->linkedin : '',
        ];
        return $moduleData;
    }

    /* Get Additional Information Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getAdditionalInformationData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeAdditionalInformationData($profileData, $config);
                break;
                // Resources
            case HS_RESOURCES:
                $moduleData = $this->getResourcePgAdditionalInformationData($profileData, $config);
                break;
                // Additional Information
            case HS_ADDITIONAL_INFO:
                $moduleData = $this->getDetailAdditionalInformationData($profileData, $config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Additional Information Data for P3 Theme
     * Get News and Insights Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getInsightsData($profileData, $config)
    {
        $moduleData = [];
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($node_type) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeInsightsData($profileData, $config);
                break;
                // Resources
            case HS_RESOURCES:
                $moduleData = $this->getAboutPgInsightsData($config);
                break;
                // News and Insights
            case HS_NEWS_INSIGHTS:
                $moduleData = $this->getDetailInsightsData($config);
                break;
            default:
                $moduleData = '';
        }
        return $moduleData;
    }

    /**
     * Get Additional Information Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getHomeAdditionalInformationData($profileData, $config)
    {
        $moduleData = [];

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_HOME,
            'additional_information_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->additional_resources_hp[0] ? $profileData->homepage_layouts->additional_resources_hp[0] : 'yes') : 'yes',
            'additional_information_layout' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->additional_resources_layout_options[0] ? $profileData->homepage_layouts->additional_resources_layout_options[0] : $config->get('additional_resources_layout_options')) : $config->get('additional_resources_layout_options'),
            'title' => $profileData->custom_data_3->thumbnail_title ?? '',
            'short_description' => $profileData->custom_data_3->thumbnail_short_description ?? '',
            'long_description' => $profileData->custom_data_3->thumbnail_long_description ?? '',
            'image' => $profileData->custom_data_3->thumbnail_featured_image ?? '',
            'video' => isset($profileData->custom_data_3->thumbnail_video) ? KALTURA_LINK_1 . $profileData->custom_data_3->thumbnail_video . KALTURA_LINK_2 : '',
            'alt_text' => $profileData->custom_data_3->thumbnail_media_alt ?? '',
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_ADDITIONAL_INFO)['link'],
            'link_text' => $config->get('additional_information_homepage_link_text') ?? NULL,
            // Condition for hiding Learn more button depending on internal page description
            'is_detail_available' => isset($profileData->custom_data_3->thumbnail_long_description) ? true : false,
        ];
        return $moduleData;
    }

    /* Get Insights Data for P3 Theme
     * @param array Profile Data received form API Response
     * @param array Config Data received form Configuration form
     * @return array Array of Insights data to be populated inside module
     */
    public function getHomeInsightsData($profileData, $config)
    {
        $insightsResponseData = $insightsData = $moduleData = [];
        $insightsResponseData = $this->hearsayCommon->getSocialPostData(FALSE);
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $counter = 0;
        foreach ($insightsResponseData as $insights) {
            if ($counter >= 3) {
                break;
            }
            $videoCaptions = $insights->video_captions ?? '';
            $videoCaptionArr = json_decode($videoCaptions, true);
            $insightsData[] = [
                'id' => $insights->id ?? '',
                'image' => $insights->image ?? '',
                'link' => $insights->link ?? '',
                'link_summary' => $insights->link_summary ?? '',
                'link_title' => $insights->link_title ?? '',
                'message' => $insights->message ?? '',
                'publish_date' => $insights->publish_date ?? '',
                'video_url' => $insights->video_url ?? '',
                'video_thumbnail' => $insights->video_thumbnail ?? '',
                'video_title' => $insights->video_title ?? '',
                'video_captions' => $videoCaptionArr,
            ];
            $counter++;
        }

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType,
            'news_insights_home_page' => $profileData->homepage_layouts ? ($profileData->homepage_layouts->news_hp[0] ? $profileData->homepage_layouts->news_hp[0] : TRUE) : TRUE,
            'news_insights_title' => $config->get('news_insights_homepage_title') != '' ? $config->get('news_insights_homepage_title') : '',
            'link_text' => $config->get('news_insights_homepage_link_text') != '' ? $config->get('news_insights_homepage_link_text') : '',
            'details_page_link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_NEWS_INSIGHTS)['link'],
            'insights_data' => $insightsData,
        ];
        return $moduleData;
    }

    /**
     * Get Additional Information Data for P3 Theme Resources page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getResourcePgAdditionalInformationData($profileData, $config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_RESOURCES,
            'title' => $config->get('additional_information_resource_title') ?? NULL,
            'description' => $config->get('additional_information_resource_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_ADDITIONAL_INFO)['link'],
            'link_text' => $config->get('additional_information_resource_link_text') ?? NULL,
            'long_description' => $profileData->custom_data_3->thumbnail_long_description ?? '',
        ];
        return $moduleData;
    }

    /* Get News and Insights Data for P3 Theme About page
     * @param array Array of settings form data
     * @return array Array of data to be populated inside module
     */
    public function getAboutPgInsightsData($config)
    {
        $moduleData = [];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_RESOURCES,
            'title' => $config->get('news_insights_about_title') ?? NULL,
            'description' => $config->get('news_insights_about_desc') ?? NULL,
            'link' => $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_NEWS_INSIGHTS)['link'],
            'link_text' => $config->get('news_insights_about_link_text') ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Additional Information Data for P3 Theme Home page
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getDetailAdditionalInformationData($profileData, $config)
    {
        $moduleData = [];

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => HS_ADDITIONAL_INFO,
            'title' => $profileData->custom_data_3->page_title ?? '',
            'short_description' => $profileData->custom_data_3->page_short_description ?? '',
            'long_description' => $profileData->custom_data_3->page_long_description ?? '',
            'image' => $profileData->custom_data_3->page_featured_image ?? '',
            'video' => isset($profileData->custom_data_3->page_video) ? KALTURA_LINK_1 . $profileData->custom_data_3->page_video . KALTURA_LINK_2 : '',
            'alt_text' => $profileData->custom_data_3->page_media_alt ?? '',
            'disclaimer' => $config->get('additional_information_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /* Get News and Insights Data for NP3 Theme
     * @param array Config Data received form Configuration form
     * @return array Array of Insights data to be populated inside module
     */
    public function getDetailInsightsData($config)
    {
        $insightsResponseData = $insightsData = $moduleData = [];
        $insightsResponseData = $this->hearsayCommon->getSocialPostData(FALSE);
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];

        $newsInsightImageName = $config->get('news_insights_empty_image') != '' ? $config->get('news_insights_empty_image') : '';
        $newsInsightDetails = $this->hearsayCommon->getCanonicalMediaDetails($newsInsightImageName, 'field_image_canonical_name');
        $newsInsightMedia = reset($newsInsightDetails);
        $newsInsightsUrl = $this->hearsayCommon->getMediaImageUrl($newsInsightMedia->field_media_image->target_id); // Get media image URL

        foreach ($insightsResponseData as $insights) {
            $videoCaptions = $insights->video_captions ?? '';
            $videoCaptionArr = json_decode($videoCaptions, true);
            $insightsData[] = [
                'id' => $insights->id ?? '',
                'image' => $insights->image ?? '',
                'link' => $insights->link ?? '',
                'link_summary' => $insights->link_summary ?? '',
                'link_title' => $insights->link_title ?? '',
                'message' => $insights->message ?? '',
                'publish_date' => $insights->publish_date ?? '',
                'video_url' => $insights->video_url ?? '',
                'video_thumbnail' => $insights->video_thumbnail ?? '',
                'video_title' => $insights->video_title ?? '',
                'video_captions' => $videoCaptionArr,
            ];
        }

        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType,
            'empty_icon' => $newsInsightsUrl,
            'empty_text' => $config->get('news_insights_empty_text') ?? NULL,
            'insights_data' => $insightsData,
            'disclaimer' => $config->get('news_insights_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Events section for Team Theme
     * @param object Settings form object
     * @return array Array of theme and events data to be populated inside module
     */
    public function getEventsData($profileData, $config)
    {
        $moduleData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        switch ($nodeType) {
                // Home
            case HS_HOME:
                $moduleData = $this->getHomeEventsData($profileData, $config);
                break;
                // Events
            case HS_EVENTS:
                $moduleData = $this->getListEventsData($config);
                break;
                // Events Detail
            case HS_EVENTS_DETAIL:
                $moduleData = $this->getDetailsEventsData($config);
                break;
        }
        return $moduleData;
    }

    /**
     * Get Events section for Team Theme
     * @param object Settings form object
     * @return array Array of theme and events data to be populated inside module
     */
    public function getAllEvents()
    {
        $eventsData = [];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_EVENTS)['link'];

        // Get Node ID for Details Page
        $detailsNodeId = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_EVENTS_DETAIL)['node_id'];

        // Get all existing Details Node Aliases
        $pathAliasStorage = \Drupal::entityTypeManager()->getStorage('path_alias');
        $aliasObjects = $pathAliasStorage->loadByProperties(['path' => '/node/' . $detailsNodeId]);

        // Delete Existing Aliases Except Root alias
        foreach ($aliasObjects as $aliasObject) {
            if ($aliasObject->get('alias')->value != $listingNodeLink . '/events-detail') {
                $aliasObject->delete();
            }
        }

        $responseData = $this->hearsayCommon->getEventsAPIData(TRUE);

        // Get all Community Involvement Data
        if ($responseData) {
            foreach ($responseData as $event) {
                // Create Alias for Community Involvement
                $pathAlias = \Drupal\path_alias\Entity\PathAlias::create(
                    [
                        'path' => '/node' . '/' . $detailsNodeId,
                        'alias' => $listingNodeLink . '/' . $event->slug,
                    ]
                );
                $pathAlias->save();

                $phone_display = '';
                if ($event->rsvp_type == 'phone') {
                    $phone_display = $this->hearsayClientCustomization->getProcessedContact($event->rsvp_info);
                }
                $eventsData[] = [
                    'id' => $event->id ?? NULL,
                    'link_url' => $listingNodeLink . '/' . $event->slug,
                    'slug' => $event->slug,
                    'event_name' => $event->event_name ?? NULL,
                    'details' => $event->details ?? NULL,
                    'photo' => $event->photo != '' ? $event->photo : '',
                    'location' => $event->location_text ?? NULL,
                    'co_ordinates' => $event->location_coords ? ($event->location_coords[0] ? $event->location_coords : NULL) : NULL,
                    'location_title' => $event->location_displayed ?? NULL,
                    'rsvp_type' => $event->rsvp_type ?? NULL,
                    'rsvp_info' => $event->rsvp_info ?? NULL,
                    'start_date' => $event->event_date ?? NULL,
                    'end_date' => $event->event_date_end ?? NULL,
                    'phone_link' => $event->rsvp_type == 'phone' ? $phone_display['contact'] : '',
                ];
            }
        }
        return $eventsData;
    }

    /**
     * Get Community Impact Data for P3 Theme on Home Page
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getHomeEventsData($profileData, $config)
    {
        $moduleData = $allEvents = [];
        $allEvents = $this->getAllEvents();
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $listingNodeLink = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_EVENTS)['link'];

        // Create Module Data Array
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'events_home_page' => isset($profileData->homepage_layouts) ? (isset($profileData->homepage_layouts->events_hp[0]) ? $profileData->homepage_layouts->events_hp[0] : 'yes') : 'yes',
            'home_module_title' => $config->get('events_homepage_title') ?? NULL,
            'home_link_text' => $config->get('events_homepage_link_text') ?? NULL,
            'listing_link' => $listingNodeLink ?? NULL,
            'events' => $allEvents,
        ];
        return $moduleData;
    }

    /** 
     * Get Events Data for listing page
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of data to be populated inside module
     */
    public function getListEventsData($config)
    {
        $moduleData = $events = [];
        $emptyStateImage = $config->get('events_empty_image') ?? NULL;
        $emptyStateImgDetails = $this->hearsayCommon->getCanonicalMediaDetails($emptyStateImage, 'field_image_canonical_name');
        $emptyStateImgMedia = reset($emptyStateImgDetails);
        $emptyStateImgUrl = $this->hearsayCommon->getMediaImageUrl($emptyStateImgMedia->field_media_image->target_id); // Get media image URL
        $events = $this->getAllEvents();
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $moduleData = [
            'theme' => HS_P3_AB,
            'node_type' => $nodeType ?? NULL,
            'events' => $events,
            'page_title' => $config->get('events_page_title') ?? NULL,
            'events_empty_state_msg' => $config->get('events_empty_text') ?? NULL,
            'events_empty_state_img' => $emptyStateImgUrl,
            'disclaimer' => $config->get('events_disclaimer_text')['value'] ?? NULL,
        ];
        return $moduleData;
    }

    /**
     * Get Event detail for each event
     * @param array Profile Data received form API Response
     * @return array Array of data to be populated inside module
     */
    public function getDetailsEventsData($config)
    {
        $moduleData = array();
        $events = $this->getAllEvents();
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $aliasArray = explode("/", $_SERVER['REQUEST_URI']);
        $currentEventsCanonical = urldecode($aliasArray[count($aliasArray) - 1]);
        if ($this->siteTools->isPreview() == true) {
            $currentEventsCanonical = explode("?", $currentEventsCanonical)[0];
        }
        foreach ($events as $event) {
            $event = (object)$event;
            $contact = [];
            if ($event->rsvp_type == 'phone') {
                $contact = $this->hearsayClientCustomization->getProcessedContact($event->rsvp_info);
            }
            if ($event->slug == $currentEventsCanonical) {
                $moduleData['id'] = $event->id;
                $moduleData['link_url'] = $event->link_url;
                $moduleData['slug'] = $event->slug;
                $moduleData['event_name'] = $event->event_name;
                $moduleData['details'] = $event->details;
                $moduleData['photo'] = $event->photo;
                $moduleData['location'] = $event->location;
                $moduleData['co_ordinates'] = $event->co_ordinates;
                $moduleData['location_title'] = $event->location_title;
                $moduleData['rsvp_type'] = $event->rsvp_type;
                $moduleData['rsvp_info'] = $event->rsvp_info;
                $moduleData['phone'] = $contact['contact'] ?? null;
                $moduleData['phone_display'] = $contact['contact_display'] ?? null;
                $moduleData['start_date'] = $event->start_date;
                $moduleData['end_date'] = $event->end_date;
                $moduleData['phone_link'] = $event->phone_link;
            }
        }

        $moduleData['listing_link'] = $this->hearsayCommon->getNodeLinkForCurrentSlug(HS_EVENTS)['link'];
        $moduleData['listing_text'] = $config->get('events_detail_link_text') ?? NULL;
        $moduleData['disclaimer'] = $config->get('events_detail_disclaimer_text')['value'] ?? NULL;
        $moduleData['rsvp_title'] = $config->get('event_rsvp_info_title') ?? NULL;
        $moduleData['email_rsvp_text'] = $config->get('event_rsvp_info_email_text') ?? NULL;
        $moduleData['phone_rsvp_text'] = $config->get('event_rsvp_info_phone_text') ?? NULL;
        $moduleData['link_rsvp_text'] = $config->get('event_rsvp_info_link_text') ?? NULL;
        $moduleData['form_rsvp_text'] = $config->get('event_rsvp_info_form_text') ?? NULL;
        $moduleData['theme'] = HS_P3_AB;
        $moduleData['node_type'] = $nodeType ?? NULL;
        return $moduleData;
    }

    /*
     * Get Footer section for P3 Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and footer data to be populated inside module
     */
    public function getFooterData($profileData, $config)
    {
        $moduleData = [];
        $designations = $profileData->custom_data_4->thumbnail_long_description;
        $copyright = $config->get('footer_copyright') != '' ? $config->get('footer_copyright') : '';
        $logoName = $config->get('footer_logo') != '' ? $config->get('footer_logo') : '';
        $logoDetails = $this->hearsayCommon->getCanonicalMediaDetails($logoName, 'field_logo_canonical_name');
        $logoMedia = reset($logoDetails);
        $logoUrl = $this->hearsayCommon->getMediaImageUrl($logoMedia->field_media_image->target_id); // Get media image URL
        if (!empty($copyright)) {
            $copyright = str_replace('[year]', date('Y'), $copyright);
        }
        $moduleData = [
            'theme' => HS_P3_AB,
            'designations' => $designations,
            'text' => $config->get('footer_text')['value'] != '' ? $config->get('footer_text')['value'] : '',
            'copyright' => $copyright != '' ? $copyright : '',
            'logo_url' => $logoUrl  != '' ? $logoUrl : '',
            'logo_link' => $config->get('footer_logo_link') != '' ? $config->get('footer_logo_link') : '',
            'logo_alt_text' => $config->get('footer_alt_text') != '' ? $config->get('footer_alt_text') : '',
            'plain_social_network' => $profileData->plain_social_networks ?? '',
        ];
        return $moduleData;
    }
}
