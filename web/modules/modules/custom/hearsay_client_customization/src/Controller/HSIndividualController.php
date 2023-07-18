<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * Class Hearsay Individual Controller.
 */

class HSIndividualController extends ControllerBase
{
    /**
     * Get Individual Controller Theme IDs
     * @return array Array of client specific theme ids
     */

    protected $hearsayCommon;
    protected $hearsayClientCustomization;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hearsayCommon = new HearsayCommon();
        $this->hearsayClientCustomization = new HearsayClientCustomization();
    }

    /**
     * Get Profile Data for Individual Theme
     * @param array Profile Data received form API Response
     * @param string Module name constant from settings form
     * @return array Array of data to be populated inside module
     */
    public function getProfileDataForIndividual($profileData, $moduleName)
    {
        $moduleData = [];
        $config = $this->hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];
        switch ($moduleName) {
                // Office Info
            case HS_OFFICE_INFORMATION:
                $moduleData = $this->getOfficeInformationData($profileData, $config);
                break;
                // Hero Banner
            case HS_BANNER:
                $moduleData = $this->getHeroBannerData($profileData, $config);
                break;
                // Custom Content
            case HS_CUSTOM_CONTENT:
                $moduleData = $this->getCustomContentData($profileData, $config);
                break;
                // Events
            case HS_EVENTS_MODULE:
                $moduleData = $this->getEventsData($config);
                break;
                // Privacy Policy
            case HS_PRIVACY_POLICY:
                $moduleData = $this->getPrivacyPolicyData($profileData, $config);
                break;
                // Products
            case HS_PRODUCTS:
                $moduleData = $this->getProductsData($profileData, $config);
                break;
                // Insights
            case HS_INSIGHTS_MODULE:
                $moduleData = $this->getInsightsData($config);
                break;
                // Team Members
            case HS_TEAM_MEMBERS:
                $moduleData = $this->getTeamMembersData($profileData, $config);
                break;
                // Insights
            case HS_INSIGHTS_MODULE:
                $moduleData = $this->getInsightsData($config);
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
     * Get Office Information Data for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and Office data to be populated inside module
     */
    public function getOfficeInformationData($profileData, $config)
    {
        $officesData = [];
        $mainOfficeData = $this->getMainOfficeDetails($profileData);
        $subOfficeData = $this->getSubOfficeDetails($profileData);
        $officesData = array_merge($mainOfficeData, $subOfficeData);
        $moduleData = [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'office_data' => $officesData,
        ];
        return $moduleData;
    }

    /**
     * Get Main Office Data for Individual Theme
     * @param array Profile Data received form API Response
     * @return array Array of main Office data to be populated inside module
     */
    public function getMainOfficeDetails($profileData)
    {
        $mainOfficeData = $contacts = [];
        $latitude = $longitude = $json = '';
        $officeTitle = $profileData->office_title != '' ? $profileData->office_title : '';
        $address = $profileData->address != '' ? $profileData->address : '';
        $mapAddress = $address->street . '+' . $address->city . '+' . $address->state . ',+' . $address->zip_code;
        $json = file_get_contents(MAP_API_1 . str_replace(" ", "+", $mapAddress) . MAP_API_2);
        $json = json_decode($json);
        if ($json->results) {
            $latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        }
        $officeImage = $profileData->office_image != '' ? $profileData->office_image : '';
        $phones = $profileData->phones != '' ? $profileData->phones : '';
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

        $contacts['email'] = $profileData->email != '' ? $profileData->email : '';
        $officeHours = $profileData->office_hours != '' ? $profileData->office_hours : '';

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
                if (isset($office->office_title)) {
                    $address = [];
                    $latitude = $longitude = $json = '';
                    $officeTitle = $office->office_title != '' ? $office->office_title : '';
                    $officeImage = $office->office_image != '' ? $office->office_image : '';
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
        }
        return $subOfficeData;
    }

    /**
     * Get Custom Content section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and custom content data to be populated inside module
     */
    public function getCustomContentData($profileData, $config)
    {
        $moduleData = $data = $customContentData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        if ($nodeType == 'home') {
            $data = $profileData->home_custom_content ?? '';
        } elseif ($nodeType == 'solutions') {
            $data = $profileData->products_custom_content ?? '';
        } elseif ($nodeType == 'about') {
            $data = $profileData->about_me_custom_content ?? '';
        }

        if ($data) {
            foreach ($data as $customData) {
                $customContentData[] = [
                    'position_media' => isset($customData->position_media[0]) ? $customData->position_media[0] : '',
                    'is_container_fluid' => isset($customData->is_container_fluid[0]) ? $customData->is_container_fluid[0] : '',
                    'video' => isset($customData->video_id) ? KALTURA_LINK_1 . $customData->video_id . KALTURA_LINK_2 : '',
                    'show_media' => isset($customData->show_media[0]) ? $customData->show_media[0] : '',
                    'photo_custom' => isset($customData->photo) ? $customData->photo : '',
                    'size_media' => isset($customData->size_media[0]) ? $customData->size_media[0] : '',
                    'rich_text_content' => isset($customData->rich_text_content) ? $customData->rich_text_content : '',
                    'title' => isset($customData->title) ? $customData->title : '',
                ];
            }
        }

        $moduleData = [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'custom_content_data' => $customContentData
        ];
        return $moduleData;
    }

    /**
     * Get Team Member section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and team member data to be populated inside module
     */
    public function getTeamMembersData($profileData, $config)
    {
        $moduleData = $teamMembersData = [];
        $defaultTeamName = $config->get('team_logo') != '' ? $config->get('team_logo') : '';
        $defaultTeamDetails = $this->hearsayCommon->getCanonicalMediaDetails($defaultTeamName, 'field_image_canonical_name');
        $defaultTeamMedia = reset($defaultTeamDetails);
        $defaultTeamUrl = $this->hearsayCommon->getMediaImageUrl($defaultTeamMedia->field_media_image->target_id); // Get media image URL
        $node_type = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        if ($profileData->team_members) {
            foreach ($profileData->team_members as $teamMember) {
                $suboffice_0 = $suboffice_1 = $suboffice_2 = $suboffice_3 = $suboffice_4 = $suboffice_5 = $suboffice_6 = $suboffice_7 = $suboffice_8 = $suboffice_9 = '';
                // Get Designations
                if ($teamMember->designations) {
                    $designations = $this->hearsayCommon->getDesignations($config, $teamMember->designations);
                }

                // Condition for updating associated office in case of invalid associated office
                $firstCharOfOffice = substr($teamMember->associated_office, 0, 1);
                if (in_array($firstCharOfOffice, ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"])) {
                    $teamMember->associated_office = $firstCharOfOffice;
                } else {
                    $teamMember->associated_office = "0";
                }
                // Get offices
                if ($teamMember->associated_office === "0" || $teamMember->associated_office === "" || $teamMember->associated_office === "null" || $teamMember->associated_office === "Main Office") {
                    $teamMember->associated_office = "0";
                    $suboffice_0 = $profileData->office_title ? $profileData->office_title : $profileData->address['street'];
                } else {
                    switch ($teamMember->associated_office) {
                        case 1:
                            $suboffice_1 = isset($profileData->suboffices[0]) ? ($profileData->suboffices[0]->office_title ? $profileData->suboffices[0]->office_title : $profileData->suboffices[0]->street) : '';
                            break;
                        case 2:
                            $suboffice_2 = isset($profileData->suboffices[1]) ? ($profileData->suboffices[1]->office_title ? $profileData->suboffices[1]->office_title : $profileData->suboffices[1]->street) : '';
                            break;
                        case 3:
                            $suboffice_3 = isset($profileData->suboffices[2]) ? ($profileData->suboffices[2]->office_title ? $profileData->suboffices[2]->office_title : $profileData->suboffices[2]->street) : '';
                            break;
                        case 4:
                            $suboffice_4 = isset($profileData->suboffices[3]) ? ($profileData->suboffices[3]->office_title ? $profileData->suboffices[3]->office_title : $profileData->suboffices[3]->street) : '';
                            break;
                        case 5:
                            $suboffice_5 = isset($profileData->suboffices[4]) ? ($profileData->suboffices[4]->office_title ? $profileData->suboffices[4]->office_title : $profileData->suboffices[4]->street) : '';
                            break;
                        case 6:
                            $suboffice_6 = isset($profileData->suboffices[5]) ? ($profileData->suboffices[5]->office_title ? $profileData->suboffices[5]->office_title : $profileData->suboffices[5]->street) : '';
                            break;
                        case 7:
                            $suboffice_7 = isset($profileData->suboffices[6]) ? ($profileData->suboffices[6]->office_title ? $profileData->suboffices[6]->office_title : $profileData->suboffices[6]->street) : '';
                            break;
                        case 8:
                            $suboffice_8 = isset($profileData->suboffices[7]) ? ($profileData->suboffices[7]->office_title ? $profileData->suboffices[7]->office_title : $profileData->suboffices[7]->street) : '';
                            break;
                        case 9:
                            $suboffice_9 = isset($profileData->suboffices[8]) ? ($profileData->suboffices[8]->office_title ? $profileData->suboffices[8]->office_title : $profileData->suboffices[8]->street) : '';
                            break;
                        default:
                            $suboffice_0 = $profileData->office_title ? $profileData->office_title : $profileData->address->street;
                            break;
                    }
                }

                if ($suboffice_0 == '' && $suboffice_1 == '' && $suboffice_2 == '' && $suboffice_3 == '' && $suboffice_4 == '' && $suboffice_5 == '' && $suboffice_6 == '' && $suboffice_7 == '' && $suboffice_8 == '' && $suboffice_9 == '') {
                    $suboffice_0 = $profileData->office_title ? $profileData->office_title : $profileData->address->street;
                    $teamMember->associated_office = "0";
                }

                // -------------- process the phone numbers --------------
                // phone
                if (isset($teamMember->team_member_phone)) {
                    $contact = $this->hearsayClientCustomization->getProcessedContact($teamMember->team_member_phone);
                    $teamMember->team_member_phone = $contact['contact'];
                    $teamMember->team_member_phone_display = $contact['contact_display'];
                }
                // fax
                if (isset($teamMember->team_member_fax)) {
                    $contact = $this->hearsayClientCustomization->getProcessedContact($teamMember->team_member_fax);
                    $teamMember->team_member_fax = $contact['contact'];
                    $teamMember->team_member_fax_display = $contact['contact_display'];
                }

                // Create array
                $teamMembersData[] = [
                    'first_name' => $teamMember->team_member_first_name ?? NULL,
                    'last_name' => $teamMember->team_member_last_name ?? NULL,
                    'designations' => $designations ?? NULL,
                    'photo' => $teamMember->team_member_photo != '' ? $teamMember->team_member_photo : $defaultTeamUrl,
                    'bio' => $teamMember->team_member_bio ?? NULL,
                    'title' => $teamMember->team_member_title ?? NULL,
                    'licenses' => $teamMember->team_member_licenses ?? NULL,
                    'state_license_number_AR' => $teamMember->state_license_number_AR ?? NULL,
                    'state_license_number_CA' => $teamMember->state_license_number_CA ?? NULL,
                    'location' => $teamMember->team_member_location ?? NULL,
                    'website' => $teamMember->team_member_url ?? NULL,
                    'email' => $teamMember->team_member_email ?? NULL,
                    'phone' => $teamMember->team_member_phone ?? NULL,
                    'phone_display' => $teamMember->team_member_phone_display ?? NULL,
                    'fax' => $teamMember->team_member_fax ?? NULL,
                    'fax_display' => $teamMember->team_member_fax_display ?? NULL,
                    'facebook' => $teamMember->team_member_facebook ?? NULL,
                    'twitter' => $teamMember->team_member_twitter ?? NULL,
                    'linkedin' => $teamMember->team_member_linkedin ?? NULL,
                    'instagram' => $teamMember->team_member_instagram ?? NULL,
                    'associated_office' => $teamMember->associated_office,
                    'associated_office_0' => isset($suboffice_0) ? $suboffice_0 : '',
                    'associated_office_1' => isset($suboffice_1) ? $suboffice_1 : '',
                    'associated_office_2' => isset($suboffice_2) ? $suboffice_2 : '',
                    'associated_office_3' => isset($suboffice_3) ? $suboffice_3 : '',
                    'associated_office_4' => isset($suboffice_4) ? $suboffice_4 : '',
                    'associated_office_5' => isset($suboffice_5) ? $suboffice_5 : '',
                    'associated_office_6' => isset($suboffice_6) ? $suboffice_6 : '',
                    'associated_office_7' => isset($suboffice_7) ? $suboffice_7 : '',
                    'associated_office_8' => isset($suboffice_8) ? $suboffice_8 : '',
                    'associated_office_9' => isset($suboffice_9) ? $suboffice_9 : '',
                ];
            }
        }
        // $key_values = array_column($teamMembersData, 'associated_office');
        // array_multisort($key_values, SORT_ASC, $teamMembersData);
        $moduleData = [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'node_type' => $node_type,
            'module_title' => $config->get('team_module_title') ?? NULL,
            'module_description' => $config->get('team_module_description')['value'] ?? NULL,
            'suboffices' => $profileData->suboffices,
            'main_office_title' => $profileData->office_title,
            'team_members_data' => $teamMembersData
        ];
        return $moduleData;
    }

    /**
     * Get Footer section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and footer data to be populated inside module
     */
    public function getFooterData($profileData, $config)
    {
        $copyright = $config->get('footer_copyright') != '' ? $config->get('footer_copyright') : '';
        $logoName = $config->get('footer_logo') != '' ? $config->get('footer_logo') : '';
        $logoDetails = $this->hearsayCommon->getCanonicalMediaDetails($logoName, 'field_logo_canonical_name');
        $logoMedia = reset($logoDetails);
        $logoUrl = $this->hearsayCommon->getMediaImageUrl($logoMedia->field_media_image->target_id); // Get media image URL
        if (!empty($copyright)) {
            $copyright = str_replace('[year]', date('Y'), $copyright);
        }
        $moduleData = [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'text' => $config->get('footer_text')['value'] != '' ? $config->get('footer_text')['value'] : '',
            'copyright' => $copyright != '' ? $copyright : '',
            'designations_title' => $config->get('footer_designations_title') != '' ? $config->get('footer_designations_title') : '',
            'designations' => $config->get('footer_designations')['value'] != '' ? $config->get('footer_designations')['value'] : '',
            'disclaimer' => isset($profileData->disclaimer) ? $profileData->disclaimer : '',
            'logo_url' => $logoUrl  != '' ? $logoUrl : '',
            'logo_link' => $config->get('footer_logo_link') != '' ? $config->get('footer_logo_link') : '',
            'logo_alt_text' => $config->get('footer_alt_text') != '' ? $config->get('footer_alt_text') : '',
        ];
        return $moduleData;
    }

    /**
     * Get Hero Banner section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and solution section data to be populated inside module
     */
    public function getHeroBannerData($profileData, $config)
    {
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        if ($nodeType == 'home') {
            $designations = isset($profileData->designations) ? implode(', ', $profileData->designations) : '';
            $stateLicense = isset($profileData->states_licensing) ? implode(', ', $profileData->states_licensing) : '';
            $stateLicense = str_replace(['CA', 'AR'], ['CA Insurance Lic. #' . $profileData->state_license_number_CA, 'AR Insurance Lic. #' . $profileData->state_license_number_AR], $stateLicense);
            $languages = isset($profileData->languages) ? implode(', ', $profileData->languages) : '';
            $defaultTeamName = $config->get('team_logo') != '' ? $config->get('team_logo') : '';
            $defaultTeamDetails = $this->hearsayCommon->getCanonicalMediaDetails($defaultTeamName, 'field_image_canonical_name');
            $defaultTeamMedia = reset($defaultTeamDetails);
            $defaultTeamUrl = $this->hearsayCommon->getMediaImageUrl($defaultTeamMedia->field_media_image->target_id); // Get media image URL

            $arrDesignation = isset($profileData->designations) ? $profileData->designations : [];
            $strDesignation = $config->get('designation_value');
            $designations = [];

            if (!empty($strDesignation)) {
                $designations = explode("\r\n", $strDesignation);
            }

            $finalDesignation = [];

            foreach ($arrDesignation as $designationValue) {
                foreach ($designations as $piece) {
                    $designationData = explode('|', $piece);
                    if ($designationData[0] == $designationValue) {
                        $finalDesignation[] = $designationData[1];
                        break; // Exit the inner loop once a match is found.
                    }
                }
            }
            if (isset($profileData->phones->phone)) {
                $phoneNumber = $this->hearsayClientCustomization->getProcessedContact($profileData->phones->phone);
            }

            if(isset($profileData->schedule_appointment_link)) {
                $appointments[] = [
                    'appointment_type' => $profileData->schedule_appointment_link->type ?? '',
                    'appointment_text' => $profileData->schedule_appointment_link->text ?? '',
                    'appointment_link' => $profileData->schedule_appointment_link->url ?? '',
                ];
            }

            return [
                'theme' => HS_LIBRARY_INDIVIDUAL_AB,
                'node_type' => $nodeType,
                'advisor_name' => $profileData->first_name . ' ' . $profileData->last_name,
                'professional_summary' => $profileData->professional_summary ?? '',
                'designations' => $finalDesignation,
                'title' => $profileData->title ?? '',
                'email' => $profileData->email ?? '',
                'appointments' => $appointments ?? '',
                'state_license' => $stateLicense ?? '',
                'phone' => $phoneNumber['contact'] ?? '',
                'phone_display' => $phoneNumber['contact_display'] ?? '',
                'street' => $profileData->address->street ?? '',
                'suite' => $profileData->address->suite ?? '',
                'city' => $profileData->address->city ?? '',
                'state' => $profileData->address->state ?? '',
                'zip_code' => $profileData->address->zip_code ?? '',
                'profile_photo' => $profileData->profile_photo ?? $defaultTeamUrl,
                'languages' => $languages ?? '',
                'networks' => $profileData->networks ?? '',
            ];
        } elseif ($nodeType == 'solutions') {
            $solutionBannerName = $config->get('solution_image') != '' ? $config->get('solution_image') : '';
            $solutionBannerDetails = $this->hearsayCommon->getCanonicalMediaDetailsByTheme($solutionBannerName, 'field_banner_canonical_name', HS_LIBRARY_INDIVIDUAL_AB);
            $solutionBannerMedia = reset($solutionBannerDetails);
            $solutionBannerUrl = $this->hearsayCommon->getMediaImageUrl($solutionBannerMedia->field_media_image->target_id); // Get media image URL

            return [
                'theme' => HS_LIBRARY_INDIVIDUAL_AB,
                'node_type' => $nodeType,
                'solution_banner' => $solutionBannerUrl,
                'solution_alt_text' => $config->get('solutions_alt_text') != '' ? $config->get('solutions_alt_text') : '',
            ];
        }
    }

    /**
     * Get Products section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and products section data to be populated inside module
     */
    public function getProductsData($profileData, $config)
    {
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $productTitle = $config->get('product_title') != '' ? $config->get('product_title') : '';
        $productDescription = $config->get('product_description') != '' ? $config->get('product_description') : '';

        $allProducts = isset($profileData->products) ? $profileData->products : '';

        $media_details = [];
        foreach ($allProducts as $productName) {
            $canonicalDetails = $this->hearsayCommon->getCanonicalMediaDetails($productName, 'field_product_canonical_name');
            $canonicalName = reset($canonicalDetails);
            $canonicalUrl = $this->hearsayCommon->getMediaImageUrl($canonicalName->field_media_image->target_id);

            $media_details[] = [
                'icon' => $canonicalUrl,
                'alt_text' => $canonicalName->field_alt_text->value,
                'title' => $canonicalName->field_product_title->value,
                'description' => $canonicalName->field_product_description->value,
                'cta_link' => $canonicalName->field_product_link->uri,
                'cta_text' => $canonicalName->field_product_link->title,
            ];
        }

        return [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'node_type' => $nodeType ?? NULL,
            'product_title' => $productTitle,
            'product_description' => $productDescription,
            'products_details' => $media_details,
        ];
    }

    /**
     * Get Privacy Policy section for Individual Theme
     * @param array Profile Data received form API Response
     * @param object Settings form object
     * @return array Array of theme and Privacy Policy section data to be populated inside module
     */
    public function getPrivacyPolicyData($profileData, $config)
    {
        $privacyPolicy = $config->get('privacy_policy_text')['value'] != '' ? $config->get('privacy_policy_text')['value'] : '';

        return [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'privacy_policy' => $privacyPolicy,
        ];
    }

    /**
     * Get Events section for Individual Theme
     * @param object Settings form object
     * @return array Array of theme and events data to be populated inside module
     */
    public function getEventsData($config)
    {
        $moduleData = $responseData = $eventsData = [];
        $defaultEventImage = '';
        $responseData = $this->hearsayCommon->getEventsAPIData(TRUE);
        $disclaimerText = $config->get('events_disclaimer_text')['value'] != NULL ? $config->get('events_disclaimer_text')['value'] : '';
        $baseSlug = $this->hearsayCommon->getThemeIdByNode()['baseSlug'];
        $defaultEventName = $config->get('events_default_image') != '' ? $config->get('events_default_image') : '';
        $defaultEventDetails = $this->hearsayCommon->getCanonicalMediaDetails($defaultEventName, 'field_image_canonical_name');
        $defaultEventMedia = reset($defaultEventDetails);
        $defaultEventImage = $this->hearsayCommon->getMediaImageUrl($defaultEventMedia->field_media_image->target_id); // Get media image URL
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        if ($responseData) {
            foreach ($responseData as $event) {
                $phone_display = '';
                if ($event->rsvp_type == 'phone') {
                    $phone_display = $this->hearsayClientCustomization->getProcessedContact($event->rsvp_info);
                }
                $eventsData[] = [
                    'id' => $event->id ?? NULL,
                    'event_name' => $event->event_name ?? NULL,
                    'details' => $event->details ?? NULL,
                    'photo' => $event->photo != '' ? $event->photo : $defaultEventImage,
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

        $moduleData = [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'node_type' => $nodeType ?? NULL,
            'events_data' => $eventsData,
            'disclaimer_text' => $disclaimerText,
            'base_slug' => $baseSlug
        ];
        return $moduleData;
    }

    /**
     * Get Insights Data for Individual Theme
     * @param array Config Data received form Configuration form
     * @return array Array of Insights data to be populated inside module
     */
    public function getInsightsData($config)
    {
        $insightsResponseData = $insightsData = [];
        $nodeType = $this->hearsayCommon->getThemeIdByNode()['node_type'];
        $insightsResponseData = $this->hearsayCommon->getSocialPostData(FALSE);
        $pageTitle = $config->get('page_title') != '' ? $config->get('page_title') : '';
        $pageSummary = $config->get('page_summary')['value'] != '' ? $config->get('page_summary')['value'] : '';
        $counter = 0;
        foreach ($insightsResponseData as $insights) {
            if ($counter >= 10) {
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

        return [
            'theme' => HS_LIBRARY_INDIVIDUAL_AB,
            'node_type' => $nodeType,
            'page_title' => $pageTitle,
            'page_summary' => $pageSummary,
            'insights_data' => $insightsData,
        ];
    }
}
