<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_common\Controller\HearsayPlatformSettingInterface;

/**
 * Class Hearsay Platform Setting Controller.
 */
class HSPlatformSettingController extends ControllerBase implements HearsayPlatformSettingInterface
{
    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_common\Controller\HearsayCommon
     */
    protected $hearsayCommon;

    /**
     * The Hearsay client customization module Helper Service.
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
     * Get Form Fields data.
     *
     * @param array $form
     *   Object of form data.
     * @param object $form_state
     *   Object of FormStateInterface.
     *
     * @return array
     *   Array of contact form field.
     */
    public function buildCustomPlatformSettingForm($form, $form_state)
    {

        $config = $this->config(HS_PLATFORM_SETTINGS);

        // Field for NPG theme ID
        $form['theme_information']['theme_id_npg'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Theme ID for NPG Theme Sites'),
            '#default_value' => $config->get('theme_id_npg'),
            '#description' => $this->t('Enter your only those Theme ID whose sites/nodes need to create (Only NPG Theme). Enter comma separated theme id in case of more then one.'),
        ];

        // Field for P3 theme ID
        $form['theme_information']['theme_id_p3'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Theme ID for P3 Theme Sites'),
            '#default_value' => $config->get('theme_id_p3'),
            '#description' => $this->t('Enter your only those Theme ID whose sites/nodes need to create (Only P3 Theme). Enter comma separated theme id in case of more then one.'),
        ];

        // Field for Library Team theme ID
        $form['theme_information']['theme_id_lt'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Theme ID for Library Team Theme Sites'),
            '#default_value' => $config->get('theme_id_lt'),
            '#description' => $this->t('Enter your only those Theme ID whose sites/nodes need to create (Only Library Team Theme). Enter comma separated theme id in case of more then one.'),
        ];

        // Field for Library Individual theme ID
        $form['theme_information']['theme_id_li'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Theme ID for Library Individual Theme Sites'),
            '#default_value' => $config->get('theme_id_li'),
            '#description' => $this->t('Enter your only those Theme ID whose sites/nodes need to create (Only Library Individual Theme). Enter comma separated theme id in case of more then one.'),
        ];

        // Group Recaptcha settings.
        $form['recaptcha_information'] = [
            '#type' => 'details',
            '#title' => $this->t('Recaptcha Settings(Site key, Secret Key)'),
            '#open' => false,
        ];

        // Recaptcha site and secret keys.
        $form['recaptcha_information']['recaptcha_site_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Recaptcha Site Key'),
            '#default_value' => $config->get('recaptcha_site_key'),
            '#description' => $this->t('Enter your Google Recaptcha Site Key'),
        ];

        $form['recaptcha_information']['recaptcha_secret_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Recaptcha Secret Key'),
            '#default_value' => $config->get('recaptcha_secret_key'),
            '#description' => $this->t('Enter your Google Recaptcha Secret Key'),
        ];

        // Group GTM tags and Data layer code.
        $form['tracking_information'] = [
            '#type' => 'details',
            '#title' => $this->t('Tracking Code(GTM, Google, Data layer script)'),
            '#open' => false,
        ];

        $form['tracking_information']['code_in_head'] = [
            '#type' => 'textarea',
            '#title' => $this->t('GTM Tags(In head)'),
            '#default_value' => $config->get('code_in_head'),
            '#description' => $this->t('Enter your GTM, Google etc tags or script.'),
        ];

        $form['tracking_information']['code_for_adobe_analytics'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Adobe Analytics Tags (In head)'),
            '#default_value' => $config->get('code_for_adobe_analytics'),
            '#description' => $this->t('Enter your Adobe Analytics Tags.'),
        ];

        $form['tracking_information']['code_after_open_body'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Code (after open body)'),
            '#default_value' => $config->get('code_after_open_body'),
        ];

        $form['tracking_information']['code_before_close_body'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Code (before close body)'),
            '#default_value' => $config->get('code_before_close_body'),
        ];

        $form['tracking_information']['data_layer'] = [
            '#type' => 'textarea',
            '#title' => $this->t('The data layer script'),
            '#default_value' => $config->get('data_layer'),
            '#description' => $this->t('Enter your data layer script.'),
        ];

        $form['tracking_information']['schema_metatag'] = [
            '#type' => 'textarea',
            '#title' => $this->t('The Schema Metatag script'),
            '#default_value' => $config->get('schema_metatag'),
            '#description' => $this->t('Enter your Schema Metatag script.'),
        ];

        $form['tracking_information']['js_context_script'] = [
            '#type' => 'textarea',
            '#title' => $this->t('The JS Context script'),
            '#default_value' => $config->get('js_context_script'),
            '#description' => $this->t('Enter your JS Context script.'),
        ];

        $form['tracking_information']['blueconic_script'] = [
            '#type' => 'textarea',
            '#title' => $this->t('The BlueConic script'),
            '#default_value' => $config->get('blueconic_script'),
            '#description' => $this->t('Enter your BlueConic script.'),
        ];

        $form['tracking_information']['onetrust_script'] = [
            '#type' => 'textarea',
            '#title' => $this->t('The OneTrust script'),
            '#default_value' => $config->get('onetrust_script'),
            '#description' => $this->t('Enter your OneTrust script.'),
        ];

        return $form;
    }

    /**
     * Get Submit form fields.
     *
     * @param object $form_state
     *   Object of FormStateInterface.
     *
     * @return array
     *   Array of settings form field.
     */
    public function getCustomSubmitFormFields($form_state)
    {
        $customFormValues = [];
        if ($form_state) {
            $customFormValues =
                [
                    "theme_id_npg" => $form_state->getValue('theme_id_npg'),
                    "theme_id_p3" => $form_state->getValue('theme_id_p3'),
                    "theme_id_lt" => $form_state->getValue('theme_id_lt'),
                    "theme_id_li" => $form_state->getValue('theme_id_li'),
                    "recaptcha_site_key" => $form_state->getValue('recaptcha_site_key'),
                    "recaptcha_secret_key" => $form_state->getValue('recaptcha_secret_key'),
                    "code_in_head" => $form_state->getValue('code_in_head'),
                    "code_for_adobe_analytics" => $form_state->getValue('code_for_adobe_analytics'),
                    "code_after_open_body" => $form_state->getValue('code_after_open_body'),
                    "code_before_close_body" => $form_state->getValue('code_before_close_body'),
                    "data_layer" => $form_state->getValue('data_layer'),
                    "schema_metatag" => $form_state->getValue('schema_metatag'),
                    "js_context_script" => $form_state->getValue('js_context_script'),
                    "blueconic_script" => $form_state->getValue('blueconic_script'),
                    "onetrust_script" => $form_state->getValue('onetrust_script'),
                ];
        }

        return $customFormValues;
    }
}
