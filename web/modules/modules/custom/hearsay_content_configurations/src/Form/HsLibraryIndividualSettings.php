<?php

/**
 * @file
 * Contains Drupal\hearsay_content_configurations\Form\HsLibraryIndividualSettings.
 */

namespace Drupal\hearsay_content_configurations\Form;

use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HsLibraryIndividualSettings.
 *
 * @package Drupal\hearsay_content_configurations\Form
 */
class HsLibraryIndividualSettings extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'hearsay_admin_settings_library_individual.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'hearsay_admin_settings_library_individual_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('hearsay_admin_settings_library_individual.settings');
        $hearsayCommon = new HearsayCommon();
        $logoMediaDetails = $siteLogos = $solutionBanner = $solutionMediaDetails = $defaultTeamMemberImages = $defaultEventsImages = $teamMemberDefaultImage = $eventsDefaultImage = [];
        $logoMediaDetails = $hearsayCommon->getMediaDetails('site_logo');
        $solutionMediaDetails = $hearsayCommon->getMediaByNodeAndTheme('banner', HS_SOLUTIONS, HS_LIBRARY_INDIVIDUAL_AB);
        $defaultTeamMemberImages = $hearsayCommon->getMediaDetailsByCategory('default_images','field_image_category','team_member');
        $defaultEventsImages = $hearsayCommon->getMediaDetailsByCategory('default_images','field_image_category','events');
        // Get Dropdown details for Header Logo
        if ($logoMediaDetails) {
            foreach ($logoMediaDetails as $logoDetails) {
                $siteLogos[$logoDetails->field_logo_canonical_name->value] = $this->t($logoDetails->getName());
            }
        }

        // Get Dropdown details for Solution Banner
        if ($solutionMediaDetails) {
            foreach ($solutionMediaDetails as $solutionDetails) {
                $solutionBanner[$solutionDetails->field_banner_canonical_name->value] = $this->t($solutionDetails->getName());
            }
        }

        $teamMemberDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultTeamMemberImages);
        $eventsDefaultImage = $hearsayCommon->getDefaultImageDropdownDetails($defaultEventsImages);

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

        $form['about_page_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('About'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['solutions_page_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Solutions'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['news_page_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Insights'),
            '#open' => true,
            '#group' => 'hs_thrivent_config',
        ];

        $form['events_page_elements'] = [
            '#type' => 'details',
            '#title' => $this->t('Events'),
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

        // Global Header Logo - Start

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

        // Global Header Logo - End

        // Global Header Utility Nav - Start

        $form['global_elements']['header']['utility_nav'] = [
            '#type' => 'details',
            '#title' => $this->t('Utility Nav'),
            '#open' => false,
        ];

        $form['global_elements']['header']['utility_nav']['parent_company_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Parent Company Label'),
            '#default_value' => $config->get('parent_company_label'),
            '#placeholder' => 'Enter the Parent Company Label.'
        ];

        $form['global_elements']['header']['utility_nav']['parent_company_link'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Parent Company Link'),
            '#default_value' => $config->get('parent_company_link'),
            '#placeholder' => 'Enter the Parent Company Link.'
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

        // Global Header Utility Nav - End

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

        $form['global_elements']['header']['navigation_menu']['insights_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Insights Menu Label'),
            '#default_value' => $config->get('insights_menu_label'),
            '#placeholder' => 'Enter the Label for Insights page menu link.'
        ];

        $form['global_elements']['header']['navigation_menu']['events_menu_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Events Menu Label'),
            '#default_value' => $config->get('events_menu_label'),
            '#placeholder' => 'Enter the Label for Events page menu link.'
        ];

        // Global Header Nav Menu Labels - End

        // Global Footer - Start

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
            '#title' => $this->t('Data'),
            '#open' => false,
        ];

        $form['global_elements']['footer']['footer_data']['footer_text'] = [
            '#type' => 'text_format',
            '#title' => 'Footer Text',
            '#default_value' => isset($config->get('footer_text')['value']) ? $config->get('footer_text')['value'] : '',
            '#format' => 'basic_html',
            '#base_type' => 'textarea',
        ];

        $form['global_elements']['footer']['footer_data']['footer_designations_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Designations Title'),
            '#default_value' => $config->get('footer_designations_title'),
            '#placeholder' => 'Enter the title for Designations.'
        ];

        $form['global_elements']['footer']['footer_data']['footer_designations'] = [
            '#type' => 'text_format',
            '#title' => 'Designations Text',
            '#default_value' => isset($config->get('footer_designations')['value']) ? $config->get('footer_designations')['value'] : '',
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

        // Global Footer - End

        // Global Designations - Start

        $form['global_elements']['designations'] = [
            '#type' => 'details',
            '#title' => $this->t('Designations'),
            '#open' => false,
        ];

        $form['global_elements']['designations']['designation_value'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Designation'),
            '#description' => $this->t('Enter label and value of Designation title. It should follow <b>value|label</b> format'),
            '#default_value' => $config->get('designation_value'),
        ];

        // Global Designations - End

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
            '#title' => $this->t('Meta Title for Home Page'),
            '#default_value' => $config->get('meta_title1_home'),
            '#placeholder' => 'Enter the Meta Title (Between First Name, Last Name and City, State) for Home Page.'
        ];

        $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_title2_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Home Page'),
            '#default_value' => $config->get('meta_title2_home'),
            '#placeholder' => 'Enter the Meta Title (After City and State) for Home Page.'
        ];

        $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_description1_home'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 1 for Home Page'),
            '#default_value' => $config->get('meta_description1_home'),
            '#placeholder' => 'Enter the Meta Description (Text before City and State) for Home Page on English site.',
            '#description' => 'City and State of office location will be dynamically filled in between the two parts of the Meta Description text.',
        ];

        $form['home_page_elements']['meta_data_home']['meta_title_des']['meta_description2_home'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 2 for Home Page'),
            '#default_value' => $config->get('meta_description2_home'),
            '#placeholder' => 'Enter the Meta Description (Text after City and State) for Home Page on English site.'
        ];

        $form['home_page_elements']['meta_data_home']['og_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Open Graph Details'),
            '#open' => false,
        ];

        $form['home_page_elements']['meta_data_home']['og_details']['og_title_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Title for Home Page'),
            '#default_value' => $config->get('og_title_home'),
            '#placeholder' => 'Enter the OG Title for Home Page.'
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

        $form['home_page_elements']['meta_data_home']['og_details']['og_image_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Image Url for Home Page'),
            '#default_value' => $config->get('og_image_home'),
            '#placeholder' => 'Enter the OG Image Url for Home Page.'
        ];

        $form['home_page_elements']['meta_data_home']['og_details']['og_url_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG URL for Home Page'),
            '#default_value' => $config->get('og_url_home'),
            '#placeholder' => 'Enter the OG URL for Home Page.'
        ];

        $form['home_page_elements']['meta_data_home']['twitter_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Twitter Details'),
            '#open' => false,
        ];

        $form['home_page_elements']['meta_data_home']['twitter_details']['twitter_title_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Title for Home Page'),
            '#default_value' => $config->get('twitter_title_home'),
            '#placeholder' => 'Enter the Twitter Title for Home Page.'
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

        $form['home_page_elements']['meta_data_home']['twitter_details']['twitter_image_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Image URL for Home Page'),
            '#default_value' => $config->get('twitter_image_home'),
            '#placeholder' => 'Enter the Twitter Image URL for Home Page.'
        ];

        $form['home_page_elements']['meta_data_home']['twitter_details']['twitter_url_home'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter URL for Home Page'),
            '#default_value' => $config->get('twitter_url_home'),
            '#placeholder' => 'Enter the Twitter URL for Home Page.'
        ];

        // Home Page Meta Data - End

        // Home Page Privacy Policy - Start

        $form['home_page_elements']['privacy_policy'] = [
            '#type' => 'details',
            '#title' => $this->t('Privacy Policy Module'),
            '#open' => false,
        ];

        $form['home_page_elements']['privacy_policy']['privacy_policy_text'] = [
            '#type' => 'text_format',
            '#title' => 'Privacy Policy Text',
            '#default_value' => isset($config->get('privacy_policy_text')['value']) ? $config->get('privacy_policy_text')['value'] : '',
            '#format' => 'basic_html',
            '#base_type' => 'textarea',
            '#placeholder' => 'Enter the Privacy Policy for Home Page.'
        ];

        // Home Page Privacy Policy - End

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

        $form['home_page_elements']['contact_form']['form_subtitle'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Form Sub-Title'),
            '#default_value' => $config->get('form_subtitle'),
            '#placeholder' => 'Enter the Module sub title.'
        ];

        $form['home_page_elements']['contact_form']['required_text'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Required Text'),
            '#default_value' => $config->get('required_text'),
            '#placeholder' => 'Enter the Required text.'
        ];

        $form['home_page_elements']['contact_form']['first_name_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First Name Label'),
            '#default_value' => $config->get('first_name_label'),
            '#placeholder' => 'Enter the First Name Label.'
        ];

        $form['home_page_elements']['contact_form']['first_name_placeholder'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First Name Placeholder'),
            '#default_value' => $config->get('first_name_placeholder'),
            '#placeholder' => 'Enter the First Name Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['last_name_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name Label'),
            '#default_value' => $config->get('last_name_label'),
            '#placeholder' => 'Enter the Last Name Label.'
        ];

        $form['home_page_elements']['contact_form']['last_name_placeholder'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name Placeholder'),
            '#default_value' => $config->get('last_name_placeholder'),
            '#placeholder' => 'Enter the Last Name Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['email_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email Label'),
            '#default_value' => $config->get('email_label'),
            '#placeholder' => 'Enter the Email Label.'
        ];

        $form['home_page_elements']['contact_form']['email_placeholder'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email Placeholder'),
            '#default_value' => $config->get('email_placeholder'),
            '#placeholder' => 'Enter the Email Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['phone_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Phone Label'),
            '#default_value' => $config->get('phone_label'),
            '#placeholder' => 'Enter the Phone Label.'
        ];

        $form['home_page_elements']['contact_form']['phone_placeholder'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Phone Placeholder'),
            '#default_value' => $config->get('phone_placeholder'),
            '#placeholder' => 'Enter the Phone Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['zip_code_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Zip Code Label'),
            '#default_value' => $config->get('zip_code_label'),
            '#placeholder' => 'Enter the Zip Code Label.'
        ];

        $form['home_page_elements']['contact_form']['zip_code_placeholder'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Zip Code Placeholder'),
            '#default_value' => $config->get('zip_code_placeholder'),
            '#placeholder' => 'Enter the Zip Code Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['message_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Message Label'),
            '#default_value' => $config->get('message_label'),
            '#placeholder' => 'Enter the Message Label.'
        ];

        $form['home_page_elements']['contact_form']['message_placeholder'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Message Placeholder'),
            '#default_value' => $config->get('message_placeholder'),
            '#placeholder' => 'Enter the Message Placeholder.'
        ];

        $form['home_page_elements']['contact_form']['opt_in_text'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Opt-in Checkbox Text'),
            '#default_value' => $config->get('opt_in_text'),
            '#placeholder' => 'Enter the Opt-in Checkbox Text.'
        ];

        $form['home_page_elements']['contact_form']['message_text'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Message Text'),
            '#default_value' => $config->get('message_text'),
            '#placeholder' => 'Enter the Message Text.'
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

        // About Page Meta Data - Start

        $form['about_page_elements']['meta_data_about'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Data'),
            '#open' => false,
        ];

        $form['about_page_elements']['meta_data_about']['meta_title_des'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Title and Description'),
            '#open' => false,
        ];

        $form['about_page_elements']['meta_data_about']['meta_title_des']['meta_title1_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for About Page'),
            '#default_value' => $config->get('meta_title1_about'),
            '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for About Page.',
            '#description' => 'First Name and Last Name will be dynamically filled in between the two parts of the Meta Title text.',
        ];

        $form['about_page_elements']['meta_data_about']['meta_title_des']['meta_title2_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for About Page'),
            '#default_value' => $config->get('meta_title2_about'),
            '#placeholder' => 'Enter the Meta Title (After First Name and Last Name) for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['meta_title_des']['meta_description_about'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description for About Page'),
            '#default_value' => $config->get('meta_description_about'),
            '#placeholder' => 'Enter the Meta Description (Text before First Name and Last Name) for About Page on English site.',
            '#description' => 'First Name and Last Name will be dynamically added after Meta Description text.',
        ];

        $form['about_page_elements']['meta_data_about']['og_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Open Graph Details'),
            '#open' => false,
        ];

        $form['about_page_elements']['meta_data_about']['og_details']['og_title_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Title for About Page'),
            '#default_value' => $config->get('og_title_about'),
            '#placeholder' => 'Enter the OG Title for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['og_details']['og_description_about'] = [
            '#type' => 'textarea',
            '#title' => $this->t('OG Description for About Page'),
            '#default_value' => $config->get('og_description_about'),
            '#placeholder' => 'Enter the OG Description for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['og_details']['og_type_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Type for About Page'),
            '#default_value' => $config->get('og_type_about'),
            '#placeholder' => 'Enter the OG Type for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['og_details']['og_image_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Image Url for About Page'),
            '#default_value' => $config->get('og_image_about'),
            '#placeholder' => 'Enter the OG Image Url for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['og_details']['og_url_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG URL for About Page'),
            '#default_value' => $config->get('og_url_about'),
            '#placeholder' => 'Enter the OG URL for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Twitter Details'),
            '#open' => false,
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details']['twitter_title_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Title for About Page'),
            '#default_value' => $config->get('twitter_title_about'),
            '#placeholder' => 'Enter the Twitter Title for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details']['twitter_description_about'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Twitter Description for About Page'),
            '#default_value' => $config->get('twitter_description_about'),
            '#placeholder' => 'Enter the Twitter Description for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details']['twitter_card_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Summary for About Page'),
            '#default_value' => $config->get('twitter_card_about'),
            '#placeholder' => 'Enter the Twitter Summary for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details']['twitter_image_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Image URL for About Page'),
            '#default_value' => $config->get('twitter_image_about'),
            '#placeholder' => 'Enter the Twitter Image URL for About Page.'
        ];

        $form['about_page_elements']['meta_data_about']['twitter_details']['twitter_url_about'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter URL for About Page'),
            '#default_value' => $config->get('twitter_url_about'),
            '#placeholder' => 'Enter the Twitter URL for About Page.'
        ];

        // About Page Meta Data - End

        // About Page Team Members - Start

        $form['about_page_elements']['team_members_section'] = [
            '#type' => 'details',
            '#title' => $this->t('Team Member Module'),
            '#open' => false,
        ];
        
        $form['about_page_elements']['team_members_section']['team_logo'] = [
            '#type' => 'select',
            '#title' => $this->t('Default Team Member Image'),
            '#empty_option' => $this->t('--Select--'),
            '#options' => $teamMemberDefaultImage,
            '#default_value' => $config->get('team_logo'),
        ];

        $form['about_page_elements']['team_members_section']['team_module_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Team Members Module Title'),
            '#default_value' => $config->get('team_module_title'),
            '#placeholder' => 'Enter the title for Team Members module.'
        ];

        $form['about_page_elements']['team_members_section']['team_module_description'] = [
            '#type' => 'text_format',
            '#title' => 'Team Members Module Description',
            '#default_value' => isset($config->get('team_module_description')['value']) ? $config->get('team_module_description')['value'] : '',
            '#format' => 'basic_html',
            '#base_type' => 'textarea',
            '#placeholder' => 'Enter the Description for Team Members Module.'
        ];

        // About Page Team Members - End
        
        // Solutions Page Meta Data - Start

        $form['solutions_page_elements']['meta_data_solutions'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Data'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['meta_data_solutions']['meta_title_des'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Title and Description'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['meta_data_solutions']['meta_title_des']['meta_title1_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Solutions Page'),
            '#default_value' => $config->get('meta_title1_solutions'),
            '#placeholder' => 'Enter the Meta Title (Before First Name and Last Name) for Solutions Page.',
            '#description' => 'First Name and Last Name will be dynamically filled in between the two parts of the Meta Title text.',
        ];

        $form['solutions_page_elements']['meta_data_solutions']['meta_title_des']['meta_title2_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Solutions Page'),
            '#default_value' => $config->get('meta_title2_solutions'),
            '#placeholder' => 'Enter the Meta Title (After First Name and Last Name) for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['meta_title_des']['meta_description1_solutions'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 1 for Solutions Page'),
            '#default_value' => $config->get('meta_description1_solutions'),
            '#placeholder' => 'Enter the Meta Description (Text before City and State) for Solutions Page on English site.',
            '#description' => 'City and State of office location will be dynamically filled in between the two parts of the Meta Description text.',
        ];

        $form['solutions_page_elements']['meta_data_solutions']['meta_title_des']['meta_description2_solutions'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 2 for Solutions Page'),
            '#default_value' => $config->get('meta_description2_solutions'),
            '#placeholder' => 'Enter the Meta Description (Text after City and State) for Solutions Page on English site.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Open Graph Details'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details']['og_title_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Title for Solutions Page'),
            '#default_value' => $config->get('og_title_solutions'),
            '#placeholder' => 'Enter the OG Title for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details']['og_description_solutions'] = [
            '#type' => 'textarea',
            '#title' => $this->t('OG Description for Solutions Page'),
            '#default_value' => $config->get('og_description_solutions'),
            '#placeholder' => 'Enter the OG Description for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details']['og_type_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Type for Solutions Page'),
            '#default_value' => $config->get('og_type_solutions'),
            '#placeholder' => 'Enter the OG Type for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details']['og_image_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Image Url for Solutions Page'),
            '#default_value' => $config->get('og_image_solutions'),
            '#placeholder' => 'Enter the OG Image Url for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['og_details']['og_url_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG URL for Solutions Page'),
            '#default_value' => $config->get('og_url_solutions'),
            '#placeholder' => 'Enter the OG URL for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Twitter Details'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details']['twitter_title_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Title for Solutions Page'),
            '#default_value' => $config->get('twitter_title_solutions'),
            '#placeholder' => 'Enter the Twitter Title for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details']['twitter_description_solutions'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Twitter Description for Solutions Page'),
            '#default_value' => $config->get('twitter_description_solutions'),
            '#placeholder' => 'Enter the Twitter Description for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details']['twitter_card_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Card for Solutions Page'),
            '#default_value' => $config->get('twitter_card_solutions'),
            '#placeholder' => 'Enter the Twitter Card for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details']['twitter_image_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Image URL for Solutions Page'),
            '#default_value' => $config->get('twitter_image_solutions'),
            '#placeholder' => 'Enter the Twitter Image URL for Solutions Page.'
        ];

        $form['solutions_page_elements']['meta_data_solutions']['twitter_details']['twitter_url_solutions'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter URL for Solutions Page'),
            '#default_value' => $config->get('twitter_url_solutions'),
            '#placeholder' => 'Enter the Twitter URL for Solutions Page.'
        ];

        // Solutions Page Meta Data - End

        // Solutions Page Banner - Start

        $form['solutions_page_elements']['solutions_banner'] = [
            '#type' => 'details',
            '#title' => $this->t('Banner Module'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['solutions_banner']['solution_image'] = [
            '#type' => 'select',
            '#title' => $this->t('Banner Image'),
            '#empty_option' => $this->t('--Select--'),
            '#options' => $solutionBanner,
            '#default_value' => $config->get('solution_image'),
        ];
        $form['solutions_page_elements']['solutions_banner']['solutions_alt_text'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ALT Text'),
            '#default_value' => $config->get('solutions_alt_text'),
            '#placeholder' => 'Enter the ALT text Solution Banner.'
        ];

        // Solutions Page Banner - End

        // Solutions Page Products - Start

        $form['solutions_page_elements']['product_section'] = [
            '#type' => 'details',
            '#title' => $this->t('Products Module'),
            '#open' => false,
        ];

        $form['solutions_page_elements']['product_section']['product_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Product Title'),
            '#default_value' => $config->get('product_title'),
            '#placeholder' => 'Enter the Product Title.'
        ];

        $form['solutions_page_elements']['product_section']['product_description'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Product Description'),
            '#default_value' => $config->get('product_description'),
            '#placeholder' => 'Enter the Product Description.'
        ];

        // Solutions Page Products - End

        // News Page Meta Data - Start

        $form['news_page_elements']['meta_data_news'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Data'),
            '#open' => false,
        ];

        $form['news_page_elements']['meta_data_news']['meta_title_des'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Details'),
            '#open' => false,
        ];

        $form['news_page_elements']['meta_data_news']['meta_title_des']['meta_title1_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Insights Page'),
            '#default_value' => $config->get('meta_title1_news'),
            '#placeholder' => 'Enter the Meta Title (First Name and Last Name) for Insights Page.',
            '#description' => 'First Name and Last Name will be dynamically filled in between the two parts of the Meta Title text.',
        ];

        $form['news_page_elements']['meta_data_news']['meta_title_des']['meta_title2_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Insights Page'),
            '#default_value' => $config->get('meta_title2_news'),
            '#placeholder' => 'Enter the Meta Title (First Name and Last Name) for Insights Page.'
        ];


        $form['news_page_elements']['meta_data_news']['meta_title_des']['meta_description1_news'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 1 for Insights Page'),
            '#default_value' => $config->get('meta_description1_news'),
            '#placeholder' => 'Enter the Meta Description (Text before City and State) for Insights Page on English site.',
            '#description' => 'City and State of office location will be dynamically filled in between the two parts of the Meta Description text.',
        ];

        $form['news_page_elements']['meta_data_news']['meta_title_des']['meta_description2_news'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 2 for Insights Page'),
            '#default_value' => $config->get('meta_description2_news'),
            '#placeholder' => 'Enter the Meta Description (Text after City and State) for Insights Page on English site.'
        ];

        $form['news_page_elements']['meta_data_news']['og_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Open Graph Details'),
            '#open' => false,
        ];

        $form['news_page_elements']['meta_data_news']['og_details']['og_title_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Title for Insights Page'),
            '#default_value' => $config->get('og_title_news'),
            '#placeholder' => 'Enter the OG Title for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['og_details']['og_description_news'] = [
            '#type' => 'textarea',
            '#title' => $this->t('OG Description for Insights Page'),
            '#default_value' => $config->get('og_description_news'),
            '#placeholder' => 'Enter the OG Description for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['og_details']['og_type_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Type for Insights Page'),
            '#default_value' => $config->get('og_type_news'),
            '#placeholder' => 'Enter the OG Type for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['og_details']['og_image_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Image Url for Insights Page'),
            '#default_value' => $config->get('og_image_news'),
            '#placeholder' => 'Enter the OG Image Url for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['og_details']['og_url_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG URL for Insights Page'),
            '#default_value' => $config->get('og_url_news'),
            '#placeholder' => 'Enter the OG URL for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Twitter Details'),
            '#open' => false,
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details']['twitter_title_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Title for Insights Page'),
            '#default_value' => $config->get('twitter_title_news'),
            '#placeholder' => 'Enter the Twitter Title for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details']['twitter_description_news'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Twitter Description for Insights Page'),
            '#default_value' => $config->get('twitter_description_news'),
            '#placeholder' => 'Enter the Twitter Description for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details']['twitter_card_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Card for Insights Page'),
            '#default_value' => $config->get('twitter_card_news'),
            '#placeholder' => 'Enter the Twitter Card for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details']['twitter_image_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Image URL for Insights Page'),
            '#default_value' => $config->get('twitter_image_news'),
            '#placeholder' => 'Enter the Twitter Image URL for Insights Page.'
        ];

        $form['news_page_elements']['meta_data_news']['twitter_details']['twitter_url_news'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter URL for Insights Page'),
            '#default_value' => $config->get('twitter_url_news'),
            '#placeholder' => 'Enter the Twitter URL for Insights Page.'
        ];

        // News Page Meta Data - End

        // News Page Title & Summary Data - Start

        $form['news_page_elements']['page_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Page Data'),
            '#open' => false,
        ];

        $form['news_page_elements']['page_details']['page_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Page Title'),
            '#default_value' => $config->get('page_title'),
            '#placeholder' => 'Enter the Page Title for Insights Page.'
        ];

        $form['news_page_elements']['page_details']['page_summary'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Page Summary'),
            '#default_value' => isset($config->get('page_summary')['value']) ? $config->get('page_summary')['value'] : '',
            '#placeholder' => 'Enter the Page Summary for Insights Page.',
            '#format' => 'basic_html',
            '#base_type' => 'textarea',
        ];

        // News Page Title & Summary Data - End

        // Events Page Meta Data - Start

        $form['events_page_elements']['meta_data_events'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Data'),
            '#open' => false,
        ];

        $form['events_page_elements']['meta_data_events']['meta_title_des'] = [
            '#type' => 'details',
            '#title' => $this->t('Meta Title and Description'),
            '#open' => false,
        ];

        $form['events_page_elements']['meta_data_events']['meta_title_des']['meta_title1_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Events Page'),
            '#default_value' => $config->get('meta_title1_events'),
            '#placeholder' => 'Enter the Meta Title (Text before First Name and Last Name) for Events Page.',
            '#description' => 'First Name and Last Name will be dynamically filled in between the two parts of the Meta Title text.',
        ];

        $form['events_page_elements']['meta_data_events']['meta_title_des']['meta_title2_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title for Events Page'),
            '#default_value' => $config->get('meta_title2_events'),
            '#placeholder' => 'Enter the Meta Title (Text after First Name and Last Name) for Events Page.'
        ];


        $form['events_page_elements']['meta_data_events']['meta_title_des']['meta_description1_events'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 1 for Events Page'),
            '#default_value' => $config->get('meta_description1_events'),
            '#placeholder' => 'Enter the Meta Description (Text before City and State) for Events Page on English site.',
            '#description' => 'City and State of office location will be dynamically filled in between the two parts of the Meta Description text.',
        ];

        $form['events_page_elements']['meta_data_events']['meta_title_des']['meta_description2_events'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Meta Description 2 for Events Page'),
            '#default_value' => $config->get('meta_description2_events'),
            '#placeholder' => 'Enter the Meta Description (Text after City and State) for Events Page on English site.'
        ];

        $form['events_page_elements']['meta_data_events']['og_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Open Graph Details'),
            '#open' => false,
        ];

        $form['events_page_elements']['meta_data_events']['og_details']['og_title_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Title for Events Page'),
            '#default_value' => $config->get('og_title_events'),
            '#placeholder' => 'Enter the OG Title for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['og_details']['og_description_events'] = [
            '#type' => 'textarea',
            '#title' => $this->t('OG Description for Events Page'),
            '#default_value' => $config->get('og_description_events'),
            '#placeholder' => 'Enter the OG Description for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['og_details']['og_type_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Type for Events Page'),
            '#default_value' => $config->get('og_type_events'),
            '#placeholder' => 'Enter the OG Type for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['og_details']['og_image_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG Image Url for Events Page'),
            '#default_value' => $config->get('og_image_events'),
            '#placeholder' => 'Enter the OG Image Url for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['og_details']['og_url_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('OG URL for Events Page'),
            '#default_value' => $config->get('og_url_events'),
            '#placeholder' => 'Enter the OG URL for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Twitter Details'),
            '#open' => false,
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details']['twitter_title_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Title for Events Page'),
            '#default_value' => $config->get('twitter_title_events'),
            '#placeholder' => 'Enter the Twitter Title for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details']['twitter_description_events'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Twitter Description for Events Page'),
            '#default_value' => $config->get('twitter_description_events'),
            '#placeholder' => 'Enter the Twitter Description for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details']['twitter_card_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Card for Events Page'),
            '#default_value' => $config->get('twitter_card_events'),
            '#placeholder' => 'Enter the Twitter Card for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details']['twitter_image_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter Image URL for Events Page'),
            '#default_value' => $config->get('twitter_image_events'),
            '#placeholder' => 'Enter the Twitter Image URL for Events Page.'
        ];

        $form['events_page_elements']['meta_data_events']['twitter_details']['twitter_url_events'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter URL for Events Page'),
            '#default_value' => $config->get('twitter_url_events'),
            '#placeholder' => 'Enter the Twitter URL for Events Page.'
        ];

        // Events Page Meta Data - End

        // Events Page Title & Summary Data - Start

        $form['events_page_elements']['page_details'] = [
            '#type' => 'details',
            '#title' => $this->t('Page Data'),
            '#open' => false,
        ];

        $form['events_page_elements']['page_details']['events_default_image'] = [
            '#type' => 'select',
            '#title' => $this->t('Default Event Image'),
            '#empty_option' => $this->t('--Select--'),
            '#options' => $eventsDefaultImage,
            '#default_value' => $config->get('events_default_image'),
        ];

        $form['events_page_elements']['page_details']['events_disclaimer_text'] = [
            '#type' => 'text_format',
            '#title' => 'Disclaimer Text',
            '#default_value' => isset($config->get('events_disclaimer_text')['value']) ? $config->get('events_disclaimer_text')['value'] : '',
            '#format' => 'basic_html',
            '#base_type' => 'textarea',
        ];

        // Events Page Title & Summary Data - End

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
        $config = $this->config('hearsay_admin_settings_library_individual.settings');
        foreach ($form_state->getValues() as $id => $value) {
            $config->set($id, $value);
        }
        $config->save();
    }
}
