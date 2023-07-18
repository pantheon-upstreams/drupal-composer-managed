<?php

/**
 * @file
 * Contains Drupal\hearsay_content_configurations\Form\HsP3Settings.
 */

namespace Drupal\hearsay_content_configurations\Form;

use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HsP3Settings.
 *
 * @package Drupal\hearsay_content_configurations\Form
 */
class HsP3Settings extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'hearsay_admin_settings_p3.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'hearsay_admin_settings_p3_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('hearsay_admin_settings_p3.settings');
        $hearsayCommon = new HearsayCommon();
        $logoMediaDetails = $siteLogos = $heroBannerMediaDetails = $heroBannerImages = $adviceApproachDefaultImage = $adviceApproachDefaultVideo = $newsInsightsBannerMediaDetails = $newsInsightsBannerImages = [];
        $logoMediaDetails = $hearsayCommon->getMediaDetails('site_logo');
        $heroBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_HOME, HS_P3_AB);
        $aboutBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_ABOUT, HS_P3_AB);
        $solutionsBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_SOLUTIONS, HS_P3_AB);
        $eventsBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_EVENTS, HS_P3_AB);
        $newsInsightsBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_NEWS_INSIGHTS, HS_NPG_AB);
        $resourceBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_RESOURCES, HS_P3_AB);
        $locationBannerMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_LOCATION, HS_P3_AB);
        $defaultAboutThriventImages = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', HS_ABOUT_THRIVENT);
        $defaultAboutThriventVideos = $hearsayCommon->getMediaDetailsByCategory('default_videos', 'field_video_category', HS_ABOUT_THRIVENT);
        $defaultAdviceApproachImages = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', HS_OUR_ADVICE_APPROACH);
        $defaultAdviceApproachVideos = $hearsayCommon->getMediaDetailsByCategory('default_videos', 'field_video_category', HS_OUR_ADVICE_APPROACH);
        $defaultTeamBannerImages = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', 'banner_team_photo');
        $defaultTeamMemberImages = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', 'team_member');
        $defaultMembershipBenefitsImages = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', HS_THRIVENT_MEMBERSHIP);
        $defaultMembershipBenefitsVideos = $hearsayCommon->getMediaDetailsByCategory('default_videos', 'field_video_category', HS_THRIVENT_MEMBERSHIP);
        $defaultEmptyStateImage = $hearsayCommon->getMediaDetailsByCategory('default_images', 'field_image_category', 'empty_state_image');

        // Get Dropdown details for Header Logo
        if ($logoMediaDetails) {
            foreach ($logoMediaDetails as $logoDetails) {
                $siteLogos[$logoDetails->field_logo_canonical_name->value] = $this->t($logoDetails->getName());
            }
        }

        // Get Dropdown details for Hero Banner Image
        $heroBannerImages = $hearsayCommon->getGetBannerDropdownDetails($heroBannerMediaDetails);

        // Get Dropdown details for About Banner Image
        $aboutBannerImages = $hearsayCommon->getGetBannerDropdownDetails($aboutBannerMediaDetails);

        // Get Dropdown details for Solutions Banner Image
        $solutionsBannerImages = $hearsayCommon->getGetBannerDropdownDetails($solutionsBannerMediaDetails);

        // Get Dropdown details for Events Banner Image
        $eventsBannerImages = $hearsayCommon->getGetBannerDropdownDetails($eventsBannerMediaDetails);

        // Get Dropdown details for News and Insights Banner Image
        $newsInsightsBannerImages = $hearsayCommon->getGetBannerDropdownDetails($newsInsightsBannerMediaDetails);

        // Get Dropdown details for Resources Banner Image
        $resourceBannerImages = $hearsayCommon->getGetBannerDropdownDetails($resourceBannerMediaDetails);

        // Get Dropdown details for Location Banner Image
        $locationBannerImages = $hearsayCommon->getGetBannerDropdownDetails($locationBannerMediaDetails);

        // Get Dropdown details for Default About Thrivent Image and video
        $aboutDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultAboutThriventImages);
        $aboutDefaultVideo = $hearsayCommon->getDefaultVideoDropdownDetails($defaultAboutThriventVideos);
        $adviceApproachDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultAdviceApproachImages);
        $adviceApproachDefaultVideo = $hearsayCommon->getDefaultVideoDropdownDetails($defaultAdviceApproachVideos);
        $teamBannerDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultTeamBannerImages);
        $teamMemberDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultTeamMemberImages);
        $membershipDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultMembershipBenefitsImages);
        $membershipDefaultVideo = $hearsayCommon->getDefaultVideoDropdownDetails($defaultMembershipBenefitsVideos);
        $emptyStateImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultEmptyStateImage);

        // Form Vertical Tabs
        $form['hs_thrivent_config'] = [
            '#type' => 'vertical_tabs',
            '#default_tab' => 'edit-content',
        ];

        $form['global_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Global Elements'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['home_page_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Home Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['about_us_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('About Us Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['our_story_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Our Story Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['our_team_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Our Team Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['advice_approach_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Our Advice Approach Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['how_we_work_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('How We Work With You Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['solutions_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Solutions Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['about_thrivent_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('About Thrivent Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['community_impact_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Community Impact Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['thrivent_membership_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Thrivent Membership Benefits Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['events_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Events Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['resources_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Resources Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['news_insights_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('News and Insights Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['additional_info_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Additional Information Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['location_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Location Page'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        // Favicon - Start

            $form['global_elements']['favicon_info'] = [
                '#type' => 'details',
                '#title' => $this->t('Favicon'),
                '#open' => false,
            ];

            $form['global_elements']['favicon_info']['favicon_icon'] = [
                '#type' => 'media_library',
                '#allowed_bundles' => [
                    'image' => "image",
                ],
                '#title' => $this->t('Favicon'),
                '#default_value' => $config->get('favicon_icon'),
            ];

        // Favicon - End

        // Header Section - Start

            $form['global_elements']['header'] = [
                '#type' => 'details',
                '#title' => $this->t('Header'),
                '#open' => false,
            ];

            $form['global_elements']['header']['header_logos'] = [
                '#type' => 'details',
                '#title' => $this->t('Header Logo'),
                '#open' => false,
            ];

            $form['global_elements']['header']['header_logos']['header_logo'] = [
                '#type' => 'select',
                '#title' => $this->t('Logo Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $siteLogos,
                '#default_value' => $config->get('header_logo'),
            ];

            $form['global_elements']['header']['header_logos']['header_logo_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Logo Link'),
                '#default_value' => $config->get('header_logo_link'),
                '#placeholder' => 'Enter the Link for Header Logo.'
            ];

            $form['global_elements']['header']['header_logos']['header_alt_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('ALT Text'),
                '#default_value' => $config->get('header_alt_text'),
                '#placeholder' => 'Enter the ALT text Header Logo.'
            ];

        // Header Section - End

        // Utility Nav Section - Start

            $form['global_elements']['header']['utility_nav'] = [
                '#type' => 'details',
                '#title' => $this->t('Utility Nav'),
                '#open' => false,
            ];

            $form['global_elements']['header']['utility_nav']['broker_check_label'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Broker Check Label'),
                '#default_value' => $config->get('broker_check_label'),
                '#placeholder' => 'Enter the Broker Check Label.'
            ];

            $form['global_elements']['header']['utility_nav']['broker_check_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Broker Check Link'),
                '#default_value' => $config->get('broker_check_link'),
                '#placeholder' => 'Enter the Broker Check Link.'
            ];

            $form['global_elements']['header']['utility_nav']['client_portal_label'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Client Portal Label'),
                '#default_value' => $config->get('client_portal_label'),
                '#placeholder' => 'Enter the Client Portal Label.'
            ];

            $form['global_elements']['header']['utility_nav']['client_portal_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Client Portal Link'),
                '#default_value' => $config->get('client_portal_link'),
                '#placeholder' => 'Enter the Client Portal Link.'
            ];

        // Utility Nav Section - End

        // Global Header Nav Menu Labels - Start

        $form['global_elements']['header']['navigation_menu'] = [
            '#type' => 'details',
            '#title' => $this->t('Header Navigation Menu'),
            '#open' => false,
        ];

        $form['global_elements']['header']['navigation_menu']['home_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Home Menu Label'),
            '#default_value' => $config->get('home_menu_label'),
            '#placeholder' => 'Enter the Label for Home page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['about_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('About Menu Label'),
            '#default_value' => $config->get('about_menu_label'),
            '#placeholder' => 'Enter the Label for About page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['solutions_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Solutions Menu Label'),
            '#default_value' => $config->get('solutions_menu_label'),
            '#placeholder' => 'Enter the Label for Solutions page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['events_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Events Menu Label'),
            '#default_value' => $config->get('events_menu_label'),
            '#placeholder' => 'Enter the Label for Events page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['resources_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Resources Menu Label'),
            '#default_value' => $config->get('resources_menu_label'),
            '#placeholder' => 'Enter the Label for Resources page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['location_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Location Menu Label'),
            '#default_value' => $config->get('location_menu_label'),
            '#placeholder' => 'Enter the Label for Location page menu link.'
        ];

        // Global Header Nav Menu Labels - End

        // Footer Section - Start

            $form['global_elements']['footer'] = [
                '#type' => 'details',
                '#title' => $this->t('Footer'),
                '#open' => false,
            ];

            $form['global_elements']['footer']['footer_logos'] = [
                '#type' => 'details',
                '#title' => $this->t('Footer Logo'),
                '#open' => false,
            ];

            $form['global_elements']['footer']['footer_logos']['footer_logo'] = [
                '#type' => 'select',
                '#title' => $this->t('Logo Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $siteLogos,
                '#default_value' => $config->get('footer_logo'),
            ];

            $form['global_elements']['footer']['footer_logos']['footer_logo_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Logo Link'),
                '#default_value' => $config->get('footer_logo_link'),
                '#placeholder' => 'Enter the Link for Footer Logo.'
            ];

            $form['global_elements']['footer']['footer_logos']['footer_alt_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('ALT Text'),
                '#default_value' => $config->get('footer_alt_text'),
                '#placeholder' => 'Enter the ALT text Footer Logo.'
            ];

            $form['global_elements']['footer']['footer_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Footer Text'),
                '#open' => false,
            ];

            $form['global_elements']['footer']['footer_data']['footer_text'] = [
                '#type' => 'text_format',
                '#title' => 'Footer Text',
                '#default_value' => isset($config->get('footer_text')['value']) ? $config->get('footer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['global_elements']['footer']['footer_data']['footer_copyright'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Copyright Text'),
                '#default_value' => $config->get('footer_copyright'),
                '#placeholder' => 'Enter the text for Copyright.',
                '#description' => 'Use [year] for displaying current year.'
            ];

        // Footer Section - End

        // Home Page Meta Data - Start

            $form['home_page_elements']['meta_data_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['home_page_elements']['meta_data_home']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_title1_home'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Home Page'),
                '#default_value' => $config->get('meta_title1_home'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Home Page.'
            ];

            $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_title2_home'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Home Page'),
                '#default_value' => $config->get('meta_title2_home'),
                '#placeholder' => 'Enter the Meta Title (After City, State) for Home Page.'
            ];

            $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_description1_home'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description 1 for Home Page'),
                '#default_value' => $config->get('meta_description1_home'),
                '#placeholder' => 'Enter the Meta Description (Between First Name, Last Name and City, State) for Home Page.',
                '#description' => 'First Name, Last Name and City, State will be dynamically filled in between the two parts of the Meta Description text.',
            ];

            $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_description2_home'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description 2 for Home Page'),
                '#default_value' => $config->get('meta_description2_home'),
                '#placeholder' => 'Enter the Meta Description (Text after City and State) for Home Page.',
                '#description' => 'First Name, Last Name and City, State will be dynamically filled in between the two parts of the Meta Description text.',
            ];

            $form['home_page_elements']['meta_data_home']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['home_page_elements']['meta_data_home']['og_details']['og_description_home'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Home Page'),
                '#default_value' => $config->get('og_description_home'),
                '#placeholder' => 'Enter the OG Description for Home Page.'
            ];

            $form['home_page_elements']['meta_data_home']['og_details']['og_type_home'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Home Page'),
                '#default_value' => $config->get('og_type_home'),
                '#placeholder' => 'Enter the OG Type for Home Page.'
            ];

            $form['home_page_elements']['meta_data_home']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['home_page_elements']['meta_data_home']['twitter_details']['twitter_description_home'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Home Page'),
                '#default_value' => $config->get('twitter_description_home'),
                '#placeholder' => 'Enter the Twitter Description for Home Page.'
            ];

            $form['home_page_elements']['meta_data_home']['twitter_details']['twitter_card_home'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Home Page'),
                '#default_value' => $config->get('twitter_card_home'),
                '#placeholder' => 'Enter the Twitter Card for Home Page.'
            ];

        // Home Page Meta Data - End

        // Home page Banner - Start

            $form['home_page_elements']['hero_banner'] = [
                '#type' => 'details',
                '#title' => $this->t('Banner Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['hero_banner']['header_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $heroBannerImages,
                '#default_value' => $config->get('header_banner_image'),
            ];

            $form['home_page_elements']['hero_banner']['banner_team_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Team Photo'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $teamBannerDefaultImage,
                '#default_value' => $config->get('banner_team_image'),
            ];

        // Home page Banner - End

        // Home Page Our Story - Start

            $form['home_page_elements']['our_story_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Our Story Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['our_story_home']['our_story_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('our_story_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['our_story_home']['our_story_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_story_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Our Story - End

        // Home Page Our Team - Start

            $form['home_page_elements']['our_team_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Our Team Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['our_team_home']['our_team_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('our_team_homepage_title'),
                '#placeholder' => 'Enter the Module Title'
            ];

            $form['home_page_elements']['our_team_home']['our_team_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_team_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Our Team - Ends

        // Home Page Our advice approach - Start

            $form['home_page_elements']['our_advice_approach_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Our advice approach Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['our_advice_approach_home']['our_advice_approach_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('our_advice_approach_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['our_advice_approach_home']['our_advice_approach_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_advice_approach_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Our advice approach - End

        // Home Page How We Work With You - Start 

            $form['home_page_elements']['work_with_you_home'] = [
                '#type' => 'details',
                '#title' => $this->t('How We Work With You Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['work_with_you_home']['work_with_you_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('work_with_you_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['work_with_you_home']['work_with_you_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('work_with_you_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page How We Work With You - Start 

        // Home Page Our Solutions - Start

            $form['home_page_elements']['our_solutions_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Our Solutions Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['our_solutions_home']['our_solutions_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('our_solutions_homepage_title'),
                '#placeholder' => 'Enter the Module Title'
            ];

            $form['home_page_elements']['our_solutions_home']['our_solutions_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_solutions_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Our Solutions - End

        // Home Page About Thrivent - Start 

            $form['home_page_elements']['about_thrivent_home'] = [
                '#type' => 'details',
                '#title' => $this->t('About Thrivent Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['about_thrivent_home']['about_thrivent_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('about_thrivent_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['about_thrivent_home']['about_thrivent_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('about_thrivent_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page About Thrivent - End

        // Home Page Community Impact - Start 

            $form['home_page_elements']['community_impact_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Community Impact Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['community_impact_home']['community_impact_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('community_impact_homepage_title'),
                '#placeholder' => 'Enter the Module Title'
            ];

            $form['home_page_elements']['community_impact_home']['community_impact_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('community_impact_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Community Impact - End

        // Home Page Thrivent Membership Benefits - Start 

            $form['home_page_elements']['membership_benefits_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Thrivent Membership Benefits Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['membership_benefits_home']['membership_benefits_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('membership_benefits_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['membership_benefits_home']['membership_benefits_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('membership_benefits_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Thrivent Membership Benefits - End

        // Home Page Additional Information - Start 

            $form['home_page_elements']['additional_information_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Additional Information Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['additional_information_home']['additional_information_layout'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Homepage Layout'),
                '#default_value' => $config->get('additional_information_layout'),
                '#options' => HOMEPAGE_LAYOUTS
            ];

            $form['home_page_elements']['additional_information_home']['additional_information_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('additional_information_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Additional Information - End

        // Home Page Events - Start 

            $form['home_page_elements']['events_home'] = [
                '#type' => 'details',
                '#title' => $this->t('Events Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['events_home']['events_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('events_homepage_title'),
                '#placeholder' => 'Enter the Module Title'
            ];

            $form['home_page_elements']['events_home']['events_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('events_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page Events - End
        
        // Home Page News and Insights - Start 

            $form['home_page_elements']['news_insights_home'] = [
                '#type' => 'details',
                '#title' => $this->t('News and Insights Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['news_insights_home']['news_insights_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('news_insights_homepage_title'),
                '#placeholder' => 'Enter the Module Title'
            ];

            $form['home_page_elements']['news_insights_home']['news_insights_homepage_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('news_insights_homepage_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Home Page News and Insights - End
        
        // Home Page Contact Form - Start

            $form['home_page_elements']['contact_form'] = [
                '#type' => 'details',
                '#title' => $this->t('Contact Form'),
                '#open' => false,
            ];

            $form['home_page_elements']['contact_form']['form_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Form Title'),
                '#default_value' => $config->get('form_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['home_page_elements']['contact_form']['first_name_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('First Name Placeholder'),
                '#default_value' => $config->get('first_name_placeholder'),
                '#placeholder' => 'Enter the First Name Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['last_name_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Last Name Placeholder'),
                '#default_value' => $config->get('last_name_placeholder'),
                '#placeholder' => 'Enter the Last Name Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['email_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Email Placeholder'),
                '#default_value' => $config->get('email_placeholder'),
                '#placeholder' => 'Enter the Email Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['phone_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Phone Placeholder'),
                '#default_value' => $config->get('phone_placeholder'),
                '#placeholder' => 'Enter the Phone Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['zip_code_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Zip Code Placeholder'),
                '#default_value' => $config->get('zip_code_placeholder'),
                '#placeholder' => 'Enter the Zip Code Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['message_placeholder'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Message Placeholder'),
                '#default_value' => $config->get('message_placeholder'),
                '#placeholder' => 'Enter the Message Placeholder.'
            ];

            $form['home_page_elements']['contact_form']['legal_text'] = [
                '#type' => 'text_format',
                '#title' => 'Legal Text',
                '#default_value' => isset($config->get('legal_text')['value']) ? $config->get('legal_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['home_page_elements']['contact_form']['submit_button_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Submit Button Text'),
                '#default_value' => $config->get('submit_button_text'),
                '#placeholder' => 'Enter the Submit button Text.'
            ];

            $form['home_page_elements']['contact_form']['successful_submission_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Successful Submission Title'),
                '#default_value' => $config->get('successful_submission_title'),
                '#placeholder' => 'Enter the Successful Submission Title.'
            ];

            $form['home_page_elements']['contact_form']['successful_submission_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Successful Submission Text'),
                '#default_value' => $config->get('successful_submission_text'),
                '#placeholder' => 'Enter the Successful Submission Text.'
            ];

            $form['home_page_elements']['contact_form']['empty_state_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Empty State Error Message'),
                '#default_value' => $config->get('empty_state_error'),
                '#placeholder' => 'Enter the Empty State Error Message'
            ];

            $form['home_page_elements']['contact_form']['phone_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Phone Error Message'),
                '#default_value' => $config->get('phone_error'),
                '#placeholder' => 'Enter the Phone Error Message'
            ];

            $form['home_page_elements']['contact_form']['email_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Email Error Message'),
                '#default_value' => $config->get('email_error'),
                '#placeholder' => 'Enter the Email Error Message'
            ];

        // Home Page Contact Form - End

        // Home Page Office Information - Start

            $form['home_page_elements']['office_information'] = [
                '#type' => 'details',
                '#title' => $this->t('Office Information Module'),
                '#open' => false,
            ];

            $form['home_page_elements']['office_information']['office_information_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('office_information_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['home_page_elements']['office_information']['main_office_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Default Name for Main Office'),
                '#default_value' => $config->get('main_office_title'),
                '#placeholder' => 'Enter the Main Office default name.'
            ];

        // Home Page Office Information - End

        // About Page Meta Data - Start

            $form['about_us_elements']['meta_data_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['about_us_elements']['meta_data_about']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['about_us_elements']['meta_data_about']['meta_title_des']['meta_title1_about'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for About Page'),
                '#default_value' => $config->get('meta_title1_about'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['meta_title_des']['meta_title2_about'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for About Page'),
                '#default_value' => $config->get('meta_title2_about'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['meta_title_des']['meta_title3_about'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for About Page'),
                '#default_value' => $config->get('meta_title3_about'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['meta_title_des']['meta_description_about'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for About Page'),
                '#default_value' => $config->get('meta_description_about'),
                '#placeholder' => 'Enter the Meta Description (Text before City and State) for About Page on English site.',
            ];

            $form['about_us_elements']['meta_data_about']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['about_us_elements']['meta_data_about']['og_details']['og_description_about'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for About Page'),
                '#default_value' => $config->get('og_description_about'),
                '#placeholder' => 'Enter the OG Description for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['og_details']['og_type_about'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for About Page'),
                '#default_value' => $config->get('og_type_about'),
                '#placeholder' => 'Enter the OG Type for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['about_us_elements']['meta_data_about']['twitter_details']['twitter_description_about'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for About Page'),
                '#default_value' => $config->get('twitter_description_about'),
                '#placeholder' => 'Enter the Twitter Description for About Page.'
            ];

            $form['about_us_elements']['meta_data_about']['twitter_details']['twitter_card_about'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for About Page'),
                '#default_value' => $config->get('twitter_card_about'),
                '#placeholder' => 'Enter the Twitter Card for About Page.'
            ];

        // About Page Meta Data - End

        // About Page Banner - Start

            $form['about_us_elements']['about_banner'] = [
                '#type' => 'details',
                '#title' => $this->t('Banner Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['about_banner']['about_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $aboutBannerImages,
                '#default_value' => $config->get('about_banner_image'),
                '#description' => $this->t('Select default Banner image for About page and all its internal pages.')
            ];

            $form['about_us_elements']['about_banner']['about_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('about_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

        // About Page Banner - End

        // About Page Our Story - Start

            $form['about_us_elements']['our_story_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Our Story Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['our_story_about']['our_story_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('our_story_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['our_story_about']['our_story_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('our_story_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['our_story_about']['our_story_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_story_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page Our Story - End

        // About Us Our Team - Start

            $form['about_us_elements']['our_team_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Our Team Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['our_team_about']['our_team_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('our_team_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['our_team_about']['our_team_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('our_team_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['our_team_about']['our_team_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_team_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Us Our Team - Ends

        // About Page Our advice approach - Start

            $form['about_us_elements']['our_advice_approach_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Our advice approach Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['our_advice_approach_about']['our_advice_approach_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('our_advice_approach_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['our_advice_approach_about']['our_advice_approach_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('our_advice_approach_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['our_advice_approach_about']['our_advice_approach_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('our_advice_approach_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page Our advice approach - End

        // About Page How We Work With You - Start

            $form['about_us_elements']['work_with_you_about'] = [
                '#type' => 'details',
                '#title' => $this->t('How We Work With You Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['work_with_you_about']['work_with_you_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('work_with_you_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['work_with_you_about']['work_with_you_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('work_with_you_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['work_with_you_about']['work_with_you_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('work_with_you_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page How We Work With You - End

        // About Page About Thrivent - Start

            $form['about_us_elements']['about_thrivent_about'] = [
                '#type' => 'details',
                '#title' => $this->t('About Thrivent Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['about_thrivent_about']['about_thrivent_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('about_thrivent_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['about_thrivent_about']['about_thrivent_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('about_thrivent_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['about_thrivent_about']['about_thrivent_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('about_thrivent_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page About Thrivent - End

        // About Page Community Impact - Start

            $form['about_us_elements']['community_impact_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Community Impact Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['community_impact_about']['community_impact_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('community_impact_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['community_impact_about']['community_impact_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('community_impact_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['community_impact_about']['community_impact_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('community_impact_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page Community Impact - End

        // About Page Membership Benefits - Start

            $form['about_us_elements']['membership_benefits_about'] = [
                '#type' => 'details',
                '#title' => $this->t('Thrivent Membership Benefits Module'),
                '#open' => false,
            ];

            $form['about_us_elements']['membership_benefits_about']['membership_benefits_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('membership_benefits_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['about_us_elements']['membership_benefits_about']['membership_benefits_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('membership_benefits_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['about_us_elements']['membership_benefits_about']['membership_benefits_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('membership_benefits_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // About Page Membership Benefits - End

        // Our Story Page Meta Data - Start

            $form['our_story_elements']['meta_data_our_story'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_title1_our_story'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Our Story Page.'),
                '#default_value' => $config->get('meta_title1_our_story'),
                '#placeholder' => 'Enter the Meta Title (Before Team Name) for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_title2_our_story'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Our Story Page.'),
                '#default_value' => $config->get('meta_title2_our_story'),
                '#placeholder' => 'Enter the Meta Title (Between Team Name and City, State) for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_title3_our_story'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Our Story Page.'),
                '#default_value' => $config->get('meta_title3_our_story'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_description1_our_story'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description 1 for Our Story Page.'),
                '#default_value' => $config->get('meta_description1_our_story'),
                '#placeholder' => 'Enter the Meta Description (Text before First Name and Last Name) for Our Story Page.',
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_description2_our_story'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description 2 for Our Story Page.'),
                '#default_value' => $config->get('meta_description2_our_story'),
                '#placeholder' => 'Enter the Meta Description (Text before First Name, Last Name and City, State) for Our Story Page.',
            ];

            $form['our_story_elements']['meta_data_our_story']['meta_title_des']['meta_description3_our_story'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description 3 for Our Story Page.'),
                '#default_value' => $config->get('meta_description3_our_story'),
                '#placeholder' => 'Enter the Meta Description (Text after City and State) for Our Story Page.',
            ];

            $form['our_story_elements']['meta_data_our_story']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['our_story_elements']['meta_data_our_story']['og_details']['og_description_our_story'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Our Story Page.'),
                '#default_value' => $config->get('og_description_our_story'),
                '#placeholder' => 'Enter the OG Description for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['og_details']['og_type_our_story'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Our Story Page.'),
                '#default_value' => $config->get('og_type_our_story'),
                '#placeholder' => 'Enter the OG Type for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['our_story_elements']['meta_data_our_story']['twitter_details']['twitter_description_our_story'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Our Story Page.'),
                '#default_value' => $config->get('twitter_description_our_story'),
                '#placeholder' => 'Enter the Twitter Description for Our Story Page.'
            ];

            $form['our_story_elements']['meta_data_our_story']['twitter_details']['twitter_card_our_story'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Our Story Page.'),
                '#default_value' => $config->get('twitter_card_our_story'),
                '#placeholder' => 'Enter the Twitter Card for Our Story Page.'
            ];

        // Our Story Page Meta Data - End

        // Our Story Details - Start

            $form['our_story_elements']['our_story_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['our_story_elements']['our_story_details']['our_story_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('our_story_page_title'),
                '#placeholder' => 'Enter the Page title.'
            ];

            $form['our_story_elements']['our_story_details']['our_story_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('our_story_disclaimer_text')['value']) ? $config->get('our_story_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Our Story Details - End
        
        // Our Team Page Meta Data - Start

            $form['our_team_elements']['meta_data_our_team'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['our_team_elements']['meta_data_our_team']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['our_team_elements']['meta_data_our_team']['meta_title_des']['meta_title1_our_team'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Our Team Page.'),
                '#default_value' => $config->get('meta_title1_our_team'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['meta_title_des']['meta_title2_our_team'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Our Team Page.'),
                '#default_value' => $config->get('meta_title2_our_team'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['meta_title_des']['meta_title3_our_team'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Our Team Page.'),
                '#default_value' => $config->get('meta_title3_our_team'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['meta_title_des']['meta_description_our_team'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Our Team Page.'),
                '#default_value' => $config->get('meta_description_our_team'),
                '#placeholder' => 'Enter the Meta Description (Text before City and State) for Our Team Page.',
            ];

            $form['our_team_elements']['meta_data_our_team']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['our_team_elements']['meta_data_our_team']['og_details']['og_description_our_team'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Our Team Page.'),
                '#default_value' => $config->get('og_description_our_team'),
                '#placeholder' => 'Enter the OG Description for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['og_details']['og_type_our_team'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Our Team Page.'),
                '#default_value' => $config->get('og_type_our_team'),
                '#placeholder' => 'Enter the OG Type for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['our_team_elements']['meta_data_our_team']['twitter_details']['twitter_description_our_team'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Our Team Page.'),
                '#default_value' => $config->get('twitter_description_our_team'),
                '#placeholder' => 'Enter the Twitter Description for Our Team Page..'
            ];

            $form['our_team_elements']['meta_data_our_team']['twitter_details']['twitter_card_our_team'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Our Team Page.'),
                '#default_value' => $config->get('twitter_card_our_team'),
                '#placeholder' => 'Enter the Twitter Card for Our Team Page..'
            ];

        // Our Team Page Meta Data - End

        // Our Team Details - Start

            $form['our_team_elements']['our_team_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['our_team_elements']['our_team_details']['our_team_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('our_team_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['our_team_elements']['our_team_details']['our_team_page']['team_logo'] = [
                '#type' => 'select',
                '#title' => $this->t('Default Team Member Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $teamMemberDefaultImage,
                '#default_value' => $config->get('team_logo'),
            ];

            $form['our_team_elements']['our_team_details']['our_team_page']['our_team_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('our_team_disclaimer_text')['value']) ? $config->get('our_team_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Our Team Details - End

        // Our advice approach Homepage Details - Start

            $form['advice_approach_elements']['our_advice_approach_homepage'] = [
                '#type' => 'details',
                '#title' => $this->t('Home Page Component'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Homepage component'),
                '#default_value' => $config->get('our_advice_approach_homepage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Homepage component'),
                '#default_value' => $config->get('our_advice_approach_homepage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Homepage component',
                '#default_value' => isset($config->get('our_advice_approach_homepage_short_copy')['value']) ? $config->get('our_advice_approach_homepage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $adviceApproachDefaultImage,
                '#default_value' => $config->get('our_advice_approach_homepage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Homepage components.')
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $adviceApproachDefaultVideo,
                '#default_value' => $config->get('our_advice_approach_homepage_video'),
                '#description' => $this->t('Select Video for Homepage components.')
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Homepage component'),
                '#default_value' => $config->get('our_advice_approach_homepage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_homepage']['our_advice_approach_homepage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Homepage component'),
                '#default_value' => $config->get('our_advice_approach_homepage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage'] = [
                '#type' => 'details',
                '#title' => $this->t('Detail Page Component'),
                '#open' => false,
            ];

        // Our advice approach Homepage Details - End

        // Our advice approach Page Meta Data - Start

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['meta_title_des']['meta_title1_our_advice_approach'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Our advice approach Page.'),
                '#default_value' => $config->get('meta_title1_our_advice_approach'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['meta_title_des']['meta_title2_our_advice_approach'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Our advice approach Page.'),
                '#default_value' => $config->get('meta_title2_our_advice_approach'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['meta_title_des']['meta_title3_our_advice_approach'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Our advice approach Page.'),
                '#default_value' => $config->get('meta_title3_our_advice_approach'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['meta_title_des']['meta_description_our_advice_approach'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Our advice approach Page.'),
                '#default_value' => $config->get('meta_description_our_advice_approach'),
                '#placeholder' => 'Enter the Meta Description for Our advice approach Page.',
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['og_details']['og_description_our_advice_approach'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Our advice approach Page.'),
                '#default_value' => $config->get('og_description_our_advice_approach'),
                '#placeholder' => 'Enter the OG Description for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['og_details']['og_type_our_advice_approach'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Our advice approach Page.'),
                '#default_value' => $config->get('og_type_our_advice_approach'),
                '#placeholder' => 'Enter the OG Type for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['twitter_details']['twitter_description_our_advice_approach'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Our advice approach Page.'),
                '#default_value' => $config->get('twitter_description_our_advice_approach'),
                '#placeholder' => 'Enter the Twitter Description for Our advice approach Page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['meta_data_our_advice_approach']['twitter_details']['twitter_card_our_advice_approach'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Our advice approach Page.'),
                '#default_value' => $config->get('twitter_card_our_advice_approach'),
                '#placeholder' => 'Enter the Twitter Card for Our advice approach Page.'
            ];

        // Our advice approach Page Meta Data - End

        // Our advice approach DetailsPage Details - Start

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('our_advice_approach_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Detail Page component'),
                '#default_value' => $config->get('our_advice_approach_detailpage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Detail Page component'),
                '#default_value' => $config->get('our_advice_approach_detailpage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Detail Page component',
                '#default_value' => isset($config->get('our_advice_approach_detailpage_short_copy')['value']) ? $config->get('our_advice_approach_detailpage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $adviceApproachDefaultImage,
                '#default_value' => $config->get('our_advice_approach_detailpage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Detail Page components.')
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $adviceApproachDefaultVideo,
                '#default_value' => $config->get('our_advice_approach_detailpage_video'),
                '#description' => $this->t('Select Video for Detail Page components.')
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Detail Page component'),
                '#default_value' => $config->get('our_advice_approach_detailpage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_detailpage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Detail Page component'),
                '#default_value' => $config->get('our_advice_approach_detailpage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['advice_approach_elements']['our_advice_approach_detailpage']['our_advice_approach_detailpage_page_data']['our_advice_approach_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('our_advice_approach_disclaimer_text')['value']) ? $config->get('our_advice_approach_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Our advice approach DetailsPage Details - End

        // How we work with you Meta Data - Start

            $form['how_we_work_elements']['meta_data_work_with_you'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['meta_title_des']['meta_title1_work_with_you'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for How we work with you Page.'),
                '#default_value' => $config->get('meta_title1_work_with_you'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['meta_title_des']['meta_title2_work_with_you'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for How we work with you Page.'),
                '#default_value' => $config->get('meta_title2_work_with_you'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['meta_title_des']['meta_title3_work_with_you'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for How we work with you Page.'),
                '#default_value' => $config->get('meta_title3_work_with_you'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['meta_title_des']['meta_description_work_with_you'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for How we work with you Page.'),
                '#default_value' => $config->get('meta_description_work_with_you'),
                '#placeholder' => 'Enter the Meta Description (Text before First Name and Last Name) for How we work with you Page.',
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['og_details']['og_description_work_with_you'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for How we work with you Page.'),
                '#default_value' => $config->get('og_description_work_with_you'),
                '#placeholder' => 'Enter the OG Description for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['og_details']['og_type_work_with_you'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for How we work with you Page.'),
                '#default_value' => $config->get('og_type_work_with_you'),
                '#placeholder' => 'Enter the OG Type for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['twitter_details']['twitter_description_work_with_you'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for How we work with you Page.'),
                '#default_value' => $config->get('twitter_description_work_with_you'),
                '#placeholder' => 'Enter the Twitter Description for How we work with you Page.'
            ];

            $form['how_we_work_elements']['meta_data_work_with_you']['twitter_details']['twitter_card_work_with_you'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for How we work with you Page.'),
                '#default_value' => $config->get('twitter_card_work_with_you'),
                '#placeholder' => 'Enter the Twitter Card for How we work with you Page.'
            ];

        // How we work with you Meta Data - End

        // How We Work With You Details - Start

            $form['how_we_work_elements']['how_we_work_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['how_we_work_elements']['how_we_work_details']['work_with_you_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('work_with_you_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['how_we_work_elements']['how_we_work_details']['work_with_you_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('work_with_you_disclaimer_text')['value']) ? $config->get('work_with_you_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // How We Work With You Details - End

        // Solutions Page - Start

        $form['solutions_elements']['solutions_listing'] = [
            '#type' => 'details',
            '#title' => $this->t('Listing Page'),
            '#open' => false,
        ];

        // Solution Page Meta Data - Start

            $form['solutions_elements']['solutions_listing']['meta_data_solutions'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['meta_title_des']['meta_title1_solutions'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Solution Page.'),
                '#default_value' => $config->get('meta_title1_solutions'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['meta_title_des']['meta_title2_solutions'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Solution Page.'),
                '#default_value' => $config->get('meta_title2_solutions'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['meta_title_des']['meta_title3_solutions'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Solution Page.'),
                '#default_value' => $config->get('meta_title3_solutions'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['meta_title_des']['meta_description_solutions'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Solution Page.'),
                '#default_value' => $config->get('meta_description_solutions'),
                '#placeholder' => 'Enter the Meta Description for Solution Page.',
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['og_details']['og_description_solutions'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Solution Page.'),
                '#default_value' => $config->get('og_description_solutions'),
                '#placeholder' => 'Enter the OG Description for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['og_details']['og_type_solutions'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Solution Page.'),
                '#default_value' => $config->get('og_type_solutions'),
                '#placeholder' => 'Enter the OG Type for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['twitter_details']['twitter_description_solutions'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Solution Page.'),
                '#default_value' => $config->get('twitter_description_solutions'),
                '#placeholder' => 'Enter the Twitter Description for Solution Page.'
            ];

            $form['solutions_elements']['solutions_listing']['meta_data_solutions']['twitter_details']['twitter_card_solutions'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Solution Page.'),
                '#default_value' => $config->get('twitter_card_solutions'),
                '#placeholder' => 'Enter the Twitter Card for Solution Page.'
            ];

        // Solution Page Meta Data - End

        // Solution Page Details - Start

            $form['solutions_elements']['solutions_listing']['solutions_listing_page_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_listing']['solutions_listing_page_details']['solutions_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $solutionsBannerImages,
                '#default_value' => $config->get('solutions_banner_image'),
                '#description' => $this->t('Select default Banner image for Solutions page and all its internal pages.')
            ];

            $form['solutions_elements']['solutions_listing']['solutions_listing_page_details']['solutions_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('solutions_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['solutions_elements']['solutions_listing']['solutions_listing_page_details']['solutions_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('solutions_disclaimer_text')['value']) ? $config->get('solutions_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Solution Page Details - End

        $form['solutions_elements']['solutions_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Details Page'),
            '#open' => false,
        ];

        // Solution Detail Page Meta Data - Start

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['meta_title_des']['meta_title1_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Solution Detail Page.'),
                '#default_value' => $config->get('meta_title1_solutions_detail'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Solution Detail Page.'
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['meta_title_des']['meta_title2_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Solution Detail Page.'),
                '#default_value' => $config->get('meta_title2_solutions_detail'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Solution Detail Page.'
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['og_details']['og_title1_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Title 1 for Solution Detail Page.'),
                '#default_value' => $config->get('og_title1_solutions_detail'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Solution Detail Page.'
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['og_details']['og_title2_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Title 2 for Solution Detail Page.'),
                '#default_value' => $config->get('og_title2_solutions_detail'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Solution Detail Page.'
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['og_details']['og_type_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Solution Detail Page.'),
                '#default_value' => $config->get('og_type_solutions_detail'),
                '#placeholder' => 'Enter the OG Type for Solution Detail Page.'
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_details']['meta_data_solutions_detail']['twitter_details']['twitter_card_solutions_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Solution Detail Page.'),
                '#default_value' => $config->get('twitter_card_solutions_detail'),
                '#placeholder' => 'Enter the Twitter Card for Solution Detail Page.'
            ];

        // Solution Detail Page Meta Data - End

        // Solution Detail Page Details - Start

            $form['solutions_elements']['solutions_details']['solutions_info'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['solutions_elements']['solutions_details']['solutions_info']['solutions_detail_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('solutions_detail_link_text'),
                '#placeholder' => 'Enter the Link Text'
            ];

            $form['solutions_elements']['solutions_details']['solutions_info']['solutions_detail_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('solutions_detail_disclaimer_text')['value']) ? $config->get('solutions_detail_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Solution Detail Page Details - End

        // About Thrivent Homepage Details - Start

            $form['about_thrivent_elements']['about_thrivent_homepage'] = [
                '#type' => 'details',
                '#title' => $this->t('Home Page Component'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Homepage component'),
                '#default_value' => $config->get('about_thrivent_homepage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Homepage component'),
                '#default_value' => $config->get('about_thrivent_homepage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Homepage component',
                '#default_value' => isset($config->get('about_thrivent_homepage_short_copy')['value']) ? $config->get('about_thrivent_homepage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $aboutDefaultImage,
                '#default_value' => $config->get('about_thrivent_homepage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Homepage components.')
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $aboutDefaultVideo,
                '#default_value' => $config->get('about_thrivent_homepage_video'),
                '#description' => $this->t('Select Video for Homepage components.')
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Homepage component'),
                '#default_value' => $config->get('about_thrivent_homepage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_homepage']['about_thrivent_homepage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Homepage component'),
                '#default_value' => $config->get('about_thrivent_homepage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage'] = [
                '#type' => 'details',
                '#title' => $this->t('Detail Page Component'),
                '#open' => false,
            ];

        // About Thrivent Homepage Details - End

        // About Thrivent Page Meta Data - Start

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['meta_title_des']['meta_title1_about_thrivent'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for About Thrivent Page.'),
                '#default_value' => $config->get('meta_title1_about_thrivent'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['meta_title_des']['meta_title2_about_thrivent'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for About Thrivent Page.'),
                '#default_value' => $config->get('meta_title2_about_thrivent'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['meta_title_des']['meta_title3_about_thrivent'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for About Thrivent Page.'),
                '#default_value' => $config->get('meta_title3_about_thrivent'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['meta_title_des']['meta_description_about_thrivent'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for About Thrivent Page.'),
                '#default_value' => $config->get('meta_description_about_thrivent'),
                '#placeholder' => 'Enter the Meta Description for About Thrivent Page.',
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['og_details']['og_description_about_thrivent'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for About Thrivent Page.'),
                '#default_value' => $config->get('og_description_about_thrivent'),
                '#placeholder' => 'Enter the OG Description for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['og_details']['og_type_about_thrivent'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for About Thrivent Page.'),
                '#default_value' => $config->get('og_type_about_thrivent'),
                '#placeholder' => 'Enter the OG Type for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['twitter_details']['twitter_description_about_thrivent'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for About Thrivent Page.'),
                '#default_value' => $config->get('twitter_description_about_thrivent'),
                '#placeholder' => 'Enter the Twitter Description for About Thrivent Page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['meta_data_about_thrivent']['twitter_details']['twitter_card_about_thrivent'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for About Thrivent Page.'),
                '#default_value' => $config->get('twitter_card_about_thrivent'),
                '#placeholder' => 'Enter the Twitter Card for About Thrivent Page.'
            ];

        // About Thrivent Page Meta Data - End

        // About Thrivent DetailsPage Details - Start

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('about_thrivent_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Detail Page component'),
                '#default_value' => $config->get('about_thrivent_detailpage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Detail Page component'),
                '#default_value' => $config->get('about_thrivent_detailpage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Detail Page component',
                '#default_value' => isset($config->get('about_thrivent_detailpage_short_copy')['value']) ? $config->get('about_thrivent_detailpage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $aboutDefaultImage,
                '#default_value' => $config->get('about_thrivent_detailpage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Detail Page components.')
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $aboutDefaultVideo,
                '#default_value' => $config->get('about_thrivent_detailpage_video'),
                '#description' => $this->t('Select Video for Detail Page components.')
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Detail Page component'),
                '#default_value' => $config->get('about_thrivent_detailpage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_detailpage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Detail Page component'),
                '#default_value' => $config->get('about_thrivent_detailpage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['about_thrivent_elements']['about_thrivent_detailpage']['about_thrivent_detailpage_page_data']['about_thrivent_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('about_thrivent_disclaimer_text')['value']) ? $config->get('about_thrivent_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // About Thrivent DetailsPage Details - End

        // Community Impact Page - Start

        $form['community_impact_elements']['community_impact_listing'] = [
            '#type' => 'details',
            '#title' => $this->t('Listing Page'),
            '#open' => false,
        ];

        // Community Impact Page Meta Data - Start

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['meta_title_des']['meta_title1_community_impact'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Community Impact Page.'),
                '#default_value' => $config->get('meta_title1_community_impact'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['meta_title_des']['meta_title2_community_impact'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Community Impact Page.'),
                '#default_value' => $config->get('meta_title2_community_impact'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['meta_title_des']['meta_title3_community_impact'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Community Impact Page.'),
                '#default_value' => $config->get('meta_title3_community_impact'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['meta_title_des']['meta_description_community_impact'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Community Impact Page.'),
                '#default_value' => $config->get('meta_description_community_impact'),
                '#placeholder' => 'Enter the Meta Description for Community Impact Page.',
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['og_details']['og_description_community_impact'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Community Impact Page.'),
                '#default_value' => $config->get('og_description_community_impact'),
                '#placeholder' => 'Enter the OG Description for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['og_details']['og_type_community_impact'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Community Impact Page.'),
                '#default_value' => $config->get('og_type_community_impact'),
                '#placeholder' => 'Enter the OG Type for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['twitter_details']['twitter_description_community_impact'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Community Impact Page.'),
                '#default_value' => $config->get('twitter_description_community_impact'),
                '#placeholder' => 'Enter the Twitter Description for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['meta_data_community_impact']['twitter_details']['twitter_card_community_impact'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Community Impact Page.'),
                '#default_value' => $config->get('twitter_card_community_impact'),
                '#placeholder' => 'Enter the Twitter Card for Community Impact Page.'
            ];

        // Community Impact Page Meta Data - End

        // Community Impact Page Details - Start

            $form['community_impact_elements']['community_impact_listing']['community_info'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_listing']['community_info']['community_impact_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('community_impact_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['community_impact_elements']['community_impact_listing']['community_info']['community_impact_empty_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Empty State Image for Community Involvements'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $emptyStateImage,
                '#default_value' => $config->get('community_impact_empty_image'),
            ];

            $form['community_impact_elements']['community_impact_listing']['community_info']['community_impact_empty_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Empty State Text'),
                '#default_value' => $config->get('community_impact_empty_text'),
                '#placeholder' => 'Enter the Empty State Text for Community Involvements.'
            ];

            $form['community_impact_elements']['community_impact_listing']['community_info']['community_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('community_disclaimer_text')['value']) ? $config->get('community_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];
        
        // Community Impact Page Details - End

        $form['community_impact_elements']['community_impact_detail'] = [
            '#type' => 'details',
            '#title' => $this->t('Details Page'),
            '#open' => false,
        ];

        // Community Impact Detail Page Meta Data - Start

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['meta_title_des']['meta_description_community_impact_details'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Community Impact Page.'),
                '#default_value' => $config->get('meta_description_community_impact_details'),
                '#placeholder' => 'Enter the Meta Description for Community Impact Page.',
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['og_details']['og_description_community_impact_details'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Community Impact Page.'),
                '#default_value' => $config->get('og_description_community_impact_details'),
                '#placeholder' => 'Enter the OG Description for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['og_details']['og_type_community_impact_details'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Community Impact Page.'),
                '#default_value' => $config->get('og_type_community_impact_details'),
                '#placeholder' => 'Enter the OG Type for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['twitter_details']['twitter_description_community_impact_details'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Community Impact Page.'),
                '#default_value' => $config->get('twitter_description_community_impact_details'),
                '#placeholder' => 'Enter the Twitter Description for Community Impact Page.'
            ];

            $form['community_impact_elements']['community_impact_detail']['meta_data_community_impact_details']['twitter_details']['twitter_card_community_impact_details'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Community Impact Page.'),
                '#default_value' => $config->get('twitter_card_community_impact_details'),
                '#placeholder' => 'Enter the Twitter Card for Community Impact Page.'
            ];

        // Community Impact Detail Page Meta Data - End

        // Community Impact Detail Page Details - Start

            $form['community_impact_elements']['community_impact_detail']['community_detail_info'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['community_impact_elements']['community_impact_detail']['community_detail_info']['community_detail_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('community_detail_link_text'),
                '#placeholder' => 'Enter the Link Text'
            ];

            $form['community_impact_elements']['community_impact_detail']['community_detail_info']['community_detail_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('community_detail_disclaimer_text')['value']) ? $config->get('community_detail_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Community Impact Detail Page Details - End

        // Thrivent Membership Homepage Details - Start

            $form['thrivent_membership_elements']['membership_benefits_homepage'] = [
                '#type' => 'details',
                '#title' => $this->t('Home Page Component'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Homepage component'),
                '#default_value' => $config->get('membership_benefits_homepage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Homepage component'),
                '#default_value' => $config->get('membership_benefits_homepage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Homepage component',
                '#default_value' => isset($config->get('membership_benefits_homepage_short_copy')['value']) ? $config->get('membership_benefits_homepage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $membershipDefaultImage,
                '#default_value' => $config->get('membership_benefits_homepage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Homepage components.')
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Homepage component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $membershipDefaultVideo,
                '#default_value' => $config->get('membership_benefits_homepage_video'),
                '#description' => $this->t('Select Video for Homepage components.')
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Homepage component'),
                '#default_value' => $config->get('membership_benefits_homepage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_homepage']['membership_benefits_homepage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Homepage component'),
                '#default_value' => $config->get('membership_benefits_homepage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage'] = [
                '#type' => 'details',
                '#title' => $this->t('Detail Page Component'),
                '#open' => false,
            ];

        // Thrivent Membership Homepage Details - Start

        // Thrivent Membership Page Meta Data - Start

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['meta_title_des']['meta_title1_membership_benefits'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Thrivent Membership Page.'),
                '#default_value' => $config->get('meta_title1_membership_benefits'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['meta_title_des']['meta_title2_membership_benefits'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Thrivent Membership Page.'),
                '#default_value' => $config->get('meta_title2_membership_benefits'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['meta_title_des']['meta_title3_membership_benefits'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Thrivent Membership Page.'),
                '#default_value' => $config->get('meta_title3_membership_benefits'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['meta_title_des']['meta_description_membership_benefits'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Thrivent Membership Page.'),
                '#default_value' => $config->get('meta_description_membership_benefits'),
                '#placeholder' => 'Enter the Meta Description for Thrivent Membership Page.',
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['og_details']['og_description_membership_benefits'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Thrivent Membership Page.'),
                '#default_value' => $config->get('og_description_membership_benefits'),
                '#placeholder' => 'Enter the OG Description for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['og_details']['og_type_membership_benefits'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Thrivent Membership Page.'),
                '#default_value' => $config->get('og_type_membership_benefits'),
                '#placeholder' => 'Enter the OG Type for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['twitter_details']['twitter_description_membership_benefits'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Thrivent Membership Page.'),
                '#default_value' => $config->get('twitter_description_membership_benefits'),
                '#placeholder' => 'Enter the Twitter Description for Thrivent Membership Page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['meta_data_membership_benefits']['twitter_details']['twitter_card_membership_benefits'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Thrivent Membership Page.'),
                '#default_value' => $config->get('twitter_card_membership_benefits'),
                '#placeholder' => 'Enter the Twitter Card for Thrivent Membership Page.'
            ];

        // Thrivent Membership Page Meta Data - End

        // Thrivent Membership DetailsPage Details - Start

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('membership_benefits_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title for Detail Page component'),
                '#default_value' => $config->get('membership_benefits_detailpage_title'),
                '#placeholder' => 'Enter the Title for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_subheadline'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Subheadline for Detail Page component'),
                '#default_value' => $config->get('membership_benefits_detailpage_subheadline'),
                '#placeholder' => 'Enter the Subheadline for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_short_copy'] = [
                '#type' => 'text_format',
                '#title' => 'Short copy for Detail Page component',
                '#default_value' => isset($config->get('membership_benefits_detailpage_short_copy')['value']) ? $config->get('membership_benefits_detailpage_short_copy')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_thumbnail'] = [
                '#type' => 'select',
                '#title' => $this->t('Thumbnail Image for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $membershipDefaultImage,
                '#default_value' => $config->get('membership_benefits_detailpage_thumbnail'),
                '#description' => $this->t('Select Thumbnail Image for Detail Page components.')
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_video'] = [
                '#type' => 'select',
                '#title' => $this->t('Video for Detail Page component'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $membershipDefaultVideo,
                '#default_value' => $config->get('membership_benefits_detailpage_video'),
                '#description' => $this->t('Select Video for Detail Page components.')
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_video_link'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Video Link for Detail Page component'),
                '#default_value' => $config->get('membership_benefits_detailpage_video_link'),
                '#placeholder' => 'Enter the Video Link for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_detailpage_alt'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Media ALT Text for Detail Page component'),
                '#default_value' => $config->get('membership_benefits_detailpage_alt'),
                '#placeholder' => 'Enter the Media ALT Text for Home Page Component.'
            ];

            $form['thrivent_membership_elements']['membership_benefits_detailpage']['membership_benefits_detailpage_page_data']['membership_benefits_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('membership_benefits_disclaimer_text')['value']) ? $config->get('membership_benefits_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Thrivent Membership DetailsPage Details - End

        // Events Page - Start

        $form['events_elements']['events_listing'] = [
            '#type' => 'details',
            '#title' => $this->t('Listing Page'),
            '#open' => false,
        ];

        // Events Page Meta Data - Start

            $form['events_elements']['events_listing']['meta_data_events'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['events_elements']['events_listing']['meta_data_events']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['events_elements']['events_listing']['meta_data_events']['meta_title_des']['meta_title1_events'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Events Page.'),
                '#default_value' => $config->get('meta_title1_events'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Events Page.'
            ];

            $form['events_elements']['events_listing']['meta_data_events']['meta_title_des']['meta_title2_events'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Events Page.'),
                '#default_value' => $config->get('meta_title2_events'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Events Page.'
            ];

            $form['events_elements']['events_listing']['meta_data_events']['meta_title_des']['meta_description_events'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Events Page.'),
                '#default_value' => $config->get('meta_description_events'),
                '#placeholder' => 'Enter the Meta Description for Events Page.',
            ];

            $form['events_elements']['events_listing']['meta_data_events']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_listing']['meta_data_events']['og_details']['og_description_events'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Events Page.'),
                '#default_value' => $config->get('og_description_events'),
                '#placeholder' => 'Enter the OG Description for Events Page.'
            ];

            $form['events_elements']['events_listing']['meta_data_events']['og_details']['og_type_events'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Events Page.'),
                '#default_value' => $config->get('og_type_events'),
                '#placeholder' => 'Enter the OG Type for Events Page.'
            ];

            $form['events_elements']['events_listing']['meta_data_events']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_listing']['meta_data_events']['twitter_details']['twitter_description_events'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Events Page.'),
                '#default_value' => $config->get('twitter_description_events'),
                '#placeholder' => 'Enter the Twitter Description for Events Page.'
            ];

            $form['events_elements']['events_listing']['meta_data_events']['twitter_details']['twitter_card_events'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Events Page.'),
                '#default_value' => $config->get('twitter_card_events'),
                '#placeholder' => 'Enter the Twitter Card for Events Page.'
            ];

        // Events Page Meta Data - End

        // Events Page Details - Start

            $form['events_elements']['events_listing']['events_listing_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_listing']['events_listing_data']['events_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $eventsBannerImages,
                '#default_value' => $config->get('events_banner_image'),
                '#description' => $this->t('Select default Banner image for Events page and all its internal pages.')
            ];

            $form['events_elements']['events_listing']['events_listing_data']['events_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('events_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['events_elements']['events_listing']['events_listing_data']['events_empty_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Empty State Image for Events'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $emptyStateImage,
                '#default_value' => $config->get('events_empty_image'),
            ];

            $form['events_elements']['events_listing']['events_listing_data']['events_empty_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Empty State Text'),
                '#default_value' => $config->get('events_empty_text'),
                '#placeholder' => 'Enter the Empty State Text for Events.'
            ];

            $form['events_elements']['events_listing']['events_listing_data']['events_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('events_disclaimer_text')['value']) ? $config->get('events_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Events Page Details - End

        $form['events_elements']['events_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Details Page'),
            '#open' => false,
        ];

        // Events Detail Page Meta Data - Start

            $form['events_elements']['events_details']['meta_data_events_detail'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['meta_title_des']['meta_title1_events_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Events Detail Page.'),
                '#default_value' => $config->get('meta_title1_events_detail'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Events Detail Page.'
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['meta_title_des']['meta_title2_events_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Events Detail Page.'),
                '#default_value' => $config->get('meta_title2_events_detail'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Events Detail Page.'
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['og_details']['og_type_events_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Events Detail Page.'),
                '#default_value' => $config->get('og_type_events_detail'),
                '#placeholder' => 'Enter the OG Type for Events Detail Page.'
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['meta_data_events_detail']['twitter_details']['twitter_card_events_detail'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Events Detail Page.'),
                '#default_value' => $config->get('twitter_card_events_detail'),
                '#placeholder' => 'Enter the Twitter Card for Events Detail Page.'
            ];

        // Events Detail Page Meta Data - End

        // Events Detail Page Details - Start

            $form['events_elements']['events_details']['events_detail_info'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['events_detail_info']['event_rsvp_info_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('RSVP Title'),
                '#default_value' => $config->get('event_rsvp_info_title'),
                '#placeholder' => 'Enter the RSVP title.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['event_rsvp_info_phone_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Phone RSVP Text'),
                '#default_value' => $config->get('event_rsvp_info_phone_text'),
                '#placeholder' => 'Enter the Phone RSVP text.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['event_rsvp_info_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link RSVP Text'),
                '#default_value' => $config->get('event_rsvp_info_link_text'),
                '#placeholder' => 'Enter the Link RSVP text.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['event_rsvp_info_email_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Email RSVP Text'),
                '#default_value' => $config->get('event_rsvp_info_email_text'),
                '#placeholder' => 'Enter the Email RSVP text.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['event_rsvp_info_form_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Form RSVP Button Text'),
                '#default_value' => $config->get('event_rsvp_info_form_text'),
                '#placeholder' => 'Enter the Form RSVP Button Text.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['events_detail_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('See All button text'),
                '#default_value' => $config->get('events_detail_link_text'),
                '#placeholder' => 'Enter the See All button text.'
            ];

            $form['events_elements']['events_details']['events_detail_info']['events_detail_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('events_detail_disclaimer_text')['value']) ? $config->get('events_detail_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Events Detail Page Details - End

        // Event Page RSVP Form - Start

            $form['events_elements']['events_details']['rsvp_form'] = [
                '#type' => 'details',
                '#title' => $this->t('RSVP Form'),
                '#open' => false,
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_form_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Form Title'),
                '#default_value' => $config->get('rsvp_form_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_first_name_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('First Name Placeholder'),
                '#default_value' => $config->get('rsvp_first_name_placeholder'),
                '#placeholder' => 'Enter the First Name Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_last_name_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Last Name Placeholder'),
                '#default_value' => $config->get('rsvp_last_name_placeholder'),
                '#placeholder' => 'Enter the Last Name Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_email_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Email Placeholder'),
                '#default_value' => $config->get('rsvp_email_placeholder'),
                '#placeholder' => 'Enter the Email Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_phone_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Phone Placeholder'),
                '#default_value' => $config->get('rsvp_phone_placeholder'),
                '#placeholder' => 'Enter the Phone Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_zip_code_placeholder'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Zip Code Placeholder'),
                '#default_value' => $config->get('rsvp_zip_code_placeholder'),
                '#placeholder' => 'Enter the Zip Code Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_message_placeholder'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Message Placeholder'),
                '#default_value' => $config->get('rsvp_message_placeholder'),
                '#placeholder' => 'Enter the Message Placeholder.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_legal_text'] = [
                '#type' => 'text_format',
                '#title' => 'Legal Text',
                '#default_value' => isset($config->get('rsvp_legal_text')['value']) ? $config->get('rsvp_legal_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_submit_button_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Submit Button Text'),
                '#default_value' => $config->get('rsvp_submit_button_text'),
                '#placeholder' => 'Enter the Submit button Text.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_successful_submission_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Successful Submission Title'),
                '#default_value' => $config->get('rsvp_successful_submission_title'),
                '#placeholder' => 'Enter the Successful Submission Title.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_successful_submission_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Successful Submission Text'),
                '#default_value' => $config->get('rsvp_successful_submission_text'),
                '#placeholder' => 'Enter the Successful Submission Text.'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_empty_state_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Empty State Error Message'),
                '#default_value' => $config->get('rsvp_empty_state_error'),
                '#placeholder' => 'Enter the Empty State Error Message'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_phone_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Phone Error Message'),
                '#default_value' => $config->get('rsvp_phone_error'),
                '#placeholder' => 'Enter the Phone Error Message'
            ];

            $form['events_elements']['events_details']['rsvp_form']['rsvp_email_error'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Email Error Message'),
                '#default_value' => $config->get('rsvp_email_error'),
                '#placeholder' => 'Enter the Email Error Message'
            ];

        // Event Page RSVP Form - End

        // Resources Page Meta Data - Start

            $form['resources_elements']['meta_data_resources'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['resources_elements']['meta_data_resources']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['resources_elements']['meta_data_resources']['meta_title_des']['meta_title1_resources'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Resources Page.'),
                '#default_value' => $config->get('meta_title1_resources'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['meta_title_des']['meta_title2_resources'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Resources Page.'),
                '#default_value' => $config->get('meta_title2_resources'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['meta_title_des']['meta_title3_resources'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Resources Page.'),
                '#default_value' => $config->get('meta_title3_resources'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['meta_title_des']['meta_description_resources'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Resources Page.'),
                '#default_value' => $config->get('meta_description_resources'),
                '#placeholder' => 'Enter the Meta Description for Resources Page.',
            ];

            $form['resources_elements']['meta_data_resources']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['resources_elements']['meta_data_resources']['og_details']['og_description_resources'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Resources Page.'),
                '#default_value' => $config->get('og_description_resources'),
                '#placeholder' => 'Enter the OG Description for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['og_details']['og_type_resources'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Resources Page.'),
                '#default_value' => $config->get('og_type_resources'),
                '#placeholder' => 'Enter the OG Type for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['resources_elements']['meta_data_resources']['twitter_details']['twitter_description_resources'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Resources Page.'),
                '#default_value' => $config->get('twitter_description_resources'),
                '#placeholder' => 'Enter the Twitter Description for Resources Page.'
            ];

            $form['resources_elements']['meta_data_resources']['twitter_details']['twitter_card_resources'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Resources Page.'),
                '#default_value' => $config->get('twitter_card_resources'),
                '#placeholder' => 'Enter the Twitter Card for Resources Page.'
            ];

        // Resources Page Meta Data - End

        // Resources Banner - Start

            $form['resources_elements']['resources_banner'] = [
                '#type' => 'details',
                '#title' => $this->t('Banner Module'),
                '#open' => false,
            ];

            $form['resources_elements']['resources_banner']['resources_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $resourceBannerImages,
                '#default_value' => $config->get('resources_banner_image'),
                '#description' => $this->t('Select default Banner image for Resources page and all its internal pages.')
            ];

            $form['resources_elements']['resources_banner']['resources_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('resources_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

        // Resources Banner - End

        // Resources Page News and Insights - Start

            $form['resources_elements']['news_insights_about'] = [
                '#type' => 'details',
                '#title' => $this->t('News and Insights Module'),
                '#open' => false,
            ];

            $form['resources_elements']['news_insights_about']['news_insights_about_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('news_insights_about_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['resources_elements']['news_insights_about']['news_insights_about_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('news_insights_about_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['resources_elements']['news_insights_about']['news_insights_about_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('news_insights_about_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Resources Page News and Insights - End

        // Resources Page Additional Information - Start

            $form['resources_elements']['additional_information_resource'] = [
                '#type' => 'details',
                '#title' => $this->t('Additional Information Module'),
                '#open' => false,
            ];

            $form['resources_elements']['additional_information_resource']['additional_information_resource_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Title'),
                '#default_value' => $config->get('additional_information_resource_title'),
                '#placeholder' => 'Enter the Module title.'
            ];

            $form['resources_elements']['additional_information_resource']['additional_information_resource_desc'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Description'),
                '#default_value' => $config->get('additional_information_resource_desc'),
                '#placeholder' => 'Enter the Module Description.'
            ];

            $form['resources_elements']['additional_information_resource']['additional_information_resource_link_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Link Text'),
                '#default_value' => $config->get('additional_information_resource_link_text'),
                '#placeholder' => 'Enter the Link Text to Details Page.'
            ];

        // Resources Page Additional Information - End

        $form['news_insights_elements']['news_insights_listing'] = [
            '#type' => 'details',
            '#title' => $this->t('Listing Page'),
            '#open' => false,
        ];
        
        // News and Insights Page Meta Data - Start

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['meta_title_des']['meta_title1_news_and_insights'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for News and Insights Page.'),
                '#default_value' => $config->get('meta_title1_news_and_insights'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['meta_title_des']['meta_title2_news_and_insights'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for News and Insights Page.'),
                '#default_value' => $config->get('meta_title2_news_and_insights'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['meta_title_des']['meta_title3_news_and_insights'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for News and Insights Page.'),
                '#default_value' => $config->get('meta_title3_news_and_insights'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['meta_title_des']['meta_description_news_and_insights'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for News and Insights Page.'),
                '#default_value' => $config->get('meta_description_news_and_insights'),
                '#placeholder' => 'Enter the Meta Description for News and Insights Page.',
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['og_details']['og_description_news_and_insights'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for News and Insights Page.'),
                '#default_value' => $config->get('og_description_news_and_insights'),
                '#placeholder' => 'Enter the OG Description for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['og_details']['og_type_news_and_insights'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for News and Insights Page.'),
                '#default_value' => $config->get('og_type_news_and_insights'),
                '#placeholder' => 'Enter the OG Type for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['twitter_details']['twitter_description_news_and_insights'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for News and Insights Page.'),
                '#default_value' => $config->get('twitter_description_news_and_insights'),
                '#placeholder' => 'Enter the Twitter Description for News and Insights Page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['meta_data_news_and_insights']['twitter_details']['twitter_card_news_and_insights'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for News and Insights Page.'),
                '#default_value' => $config->get('twitter_card_news_and_insights'),
                '#placeholder' => 'Enter the Twitter Card for News and Insights Page.'
            ];

        // News and Insights Page Meta Data - End

        // News and Insights Page Details - Start

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data']['news_insights_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $newsInsightsBannerImages,
                '#default_value' => $config->get('news_insights_banner_image'),
                '#description' => $this->t('Select default Banner image for News page and all its internal pages.')
            ];

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data']['news_and_insights_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('news_and_insights_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data']['news_insights_empty_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Empty State Image for News and Insights'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $emptyStateImage,
                '#default_value' => $config->get('news_insights_empty_image'),
            ];

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data']['news_insights_empty_text'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Empty State Text'),
                '#default_value' => $config->get('news_insights_empty_text'),
                '#placeholder' => 'Enter the Empty State Text for News and Insights.'
            ];

            $form['news_insights_elements']['news_insights_listing']['news_insights_listing_data']['news_insights_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('news_insights_disclaimer_text')['value']) ? $config->get('news_insights_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // News and Insights Page Details - End

        // Additional Information Page Meta Data - Start

            $form['additional_info_elements']['meta_data_additional_information'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['additional_info_elements']['meta_data_additional_information']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['additional_info_elements']['meta_data_additional_information']['meta_title_des']['meta_title1_additional_information'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 1 for Additional Information Page.'),
                '#default_value' => $config->get('meta_title1_additional_information'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['meta_title_des']['meta_title2_additional_information'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 2 for Additional Information Page.'),
                '#default_value' => $config->get('meta_title2_additional_information'),
                '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['meta_title_des']['meta_title3_additional_information'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title 3 for Additional Information Page.'),
                '#default_value' => $config->get('meta_title3_additional_information'),
                '#placeholder' => 'Enter the Meta Title (After City and State) for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['meta_title_des']['meta_description_additional_information'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Additional Information Page.'),
                '#default_value' => $config->get('meta_description_additional_information'),
                '#placeholder' => 'Enter the Meta Description for Additional Information Page.',
            ];

            $form['additional_info_elements']['meta_data_additional_information']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['additional_info_elements']['meta_data_additional_information']['og_details']['og_description_additional_information'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Additional Information Page.'),
                '#default_value' => $config->get('og_description_additional_information'),
                '#placeholder' => 'Enter the OG Description for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['og_details']['og_type_additional_information'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Additional Information Page.'),
                '#default_value' => $config->get('og_type_additional_information'),
                '#placeholder' => 'Enter the OG Type for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['additional_info_elements']['meta_data_additional_information']['twitter_details']['twitter_description_additional_information'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Additional Information Page.'),
                '#default_value' => $config->get('twitter_description_additional_information'),
                '#placeholder' => 'Enter the Twitter Description for Additional Information Page.'
            ];

            $form['additional_info_elements']['meta_data_additional_information']['twitter_details']['twitter_card_additional_information'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Additional Information Page.'),
                '#default_value' => $config->get('twitter_card_additional_information'),
                '#placeholder' => 'Enter the Twitter Card for Additional Information Page.'
            ];

        // Additional Information Page Meta Data - End

        // Additional Information Details - Start

            $form['additional_info_elements']['additional_information_detail'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['additional_info_elements']['additional_information_detail']['additional_information_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('additional_information_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

            $form['additional_info_elements']['additional_information_detail']['additional_information_disclaimer_text'] = [
                '#type' => 'text_format',
                '#title' => $this->t('Disclaimer'),
                '#default_value' => isset($config->get('additional_information_disclaimer_text')['value']) ? $config->get('additional_information_disclaimer_text')['value'] : '',
                '#format' => 'basic_html',
                '#base_type' => 'textarea',
            ];

        // Additional Information Details - End

        // Location Page Meta Data - Start

            $form['location_elements']['meta_data_location'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Data'),
                '#open' => false,
            ];

            $form['location_elements']['meta_data_location']['meta_title_des'] = [
                '#type' => 'details',
                '#title' => $this->t('Meta Title and Description'),
                '#open' => false,
            ];

            $form['location_elements']['meta_data_location']['meta_title_des']['meta_title1_location'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Meta Title for Location Page.'),
                '#default_value' => $config->get('meta_title1_location'),
                '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Location Page.'
            ];

            $form['location_elements']['meta_data_location']['meta_title_des']['meta_description_location'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Meta Description for Location Page.'),
                '#default_value' => $config->get('meta_description_location'),
                '#placeholder' => 'Enter the Meta Description for Location Page.',
            ];

            $form['location_elements']['meta_data_location']['og_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Open Graph Details'),
                '#open' => false,
            ];

            $form['location_elements']['meta_data_location']['og_details']['og_description_location'] = [
                '#type' => 'textarea',
                '#title' => $this->t('OG Description for Location Page.'),
                '#default_value' => $config->get('og_description_location'),
                '#placeholder' => 'Enter the OG Description for Location Page.'
            ];

            $form['location_elements']['meta_data_location']['og_details']['og_type_location'] = [
                '#type' => 'textfield',
                '#title' => $this->t('OG Type for Location Page.'),
                '#default_value' => $config->get('og_type_location'),
                '#placeholder' => 'Enter the OG Type for Location Page.'
            ];

            $form['location_elements']['meta_data_location']['twitter_details'] = [
                '#type' => 'details',
                '#title' => $this->t('Twitter Details'),
                '#open' => false,
            ];

            $form['location_elements']['meta_data_location']['twitter_details']['twitter_description_location'] = [
                '#type' => 'textarea',
                '#title' => $this->t('Twitter Description for Location Page.'),
                '#default_value' => $config->get('twitter_description_location'),
                '#placeholder' => 'Enter the Twitter Description for Location Page.'
            ];

            $form['location_elements']['meta_data_location']['twitter_details']['twitter_card_location'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Twitter Card for Location Page.'),
                '#default_value' => $config->get('twitter_card_location'),
                '#placeholder' => 'Enter the Twitter Card for Location Page.'
            ];

        // Location Page Meta Data - End

        // Location Details - Start

            $form['location_elements']['location_page_data'] = [
                '#type' => 'details',
                '#title' => $this->t('Page Details'),
                '#open' => false,
            ];

            $form['location_elements']['location_page_data']['location_banner_image'] = [
                '#type' => 'select',
                '#title' => $this->t('Banner Image'),
                '#empty_option' => $this->t('--Select--'),
                '#options' => $locationBannerImages,
                '#default_value' => $config->get('location_banner_image'),
                '#description' => $this->t('Select default Banner image for Locations page and all its internal pages.')
            ];

            $form['location_elements']['location_page_data']['location_page_title'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Page Title for Banner'),
                '#default_value' => $config->get('location_page_title'),
                '#placeholder' => 'Enter the Title for page.'
            ];

        // Location Details - End

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);
        $config = $this->config('hearsay_admin_settings_p3.settings');
        foreach ($form_state->getValues() as $id => $value) {
            $config->set($id, $value);
        }
        $config->save();
    }
}