<?php

namespace Drupal\hearsay_config\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hearsay_client_customization\Controller\HSPlatformSettingController;

/**
 * Class HearsayPlatformSettingForm.
 *
 * @package Drupal\hearsay_config\Form
 */
class HearsayPlatformSettingForm extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['hs_platform_settings.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'hs_platform_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Form constructor.
        $form = parent::buildForm($form, $form_state);
        // Default settings.
        $config = $this->config(HS_PLATFORM_SETTINGS);

        $form['sites_api'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Api end point'),
            '#default_value' => $config->get('sites_api'),
            '#description' => $this->t('Enter your API end point.'),
        ];

        $form['contact_api'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Contact Us Form Api end point'),
            '#default_value' => $config->get('contact_api'),
            '#description' => $this->t('Enter Contact Us Form API end point.'),
        ];

        $form['token'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Token'),
            '#default_value' => $config->get('token'),
            '#description' => $this->t('Enter your token for API.'),
        ];

        $form['org_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Org ID'),
            '#default_value' => $config->get('org_id'),
            '#description' => $this->t('Enter your OrgID'),
        ];

        // Group theme settings.
        $form['theme_information'] = [
            '#type' => 'details',
            '#title' => $this->t('Theme Settings'),
            '#open' => false,
        ];

        // Field for Library Individual, Library Team, NPG and P3 slug theme ID
        $form['theme_information']['theme_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Theme ID'),
            '#default_value' => $config->get('theme_id'),
            '#description' => $this->t('Enter your only those Theme ID whose sites/nodes need to create (NPG, P3, Library Team and Library Individual). Enter comma separated theme id in case of more then one.'),
        ];

        $form['pager_size'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Pager Size for API call'),
            '#default_value' => $config->get('pager_size') ? $config->get('pager_size') : 20,
            '#description' => $this->t('Enter the size of pager used while calling API to get all the sites.'),
        ];

        // Get custom Form fields.
        $platformSetting = new HSPlatformSettingController();
        $form = $platformSetting->buildCustomPlatformSettingForm($form, $form_state);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);
        $commonFormValues =
            [
                "sites_api" => $form_state->getValue('sites_api'),
                "contact_api" => $form_state->getValue('contact_api'),
                "token" => $form_state->getValue('token'),
                "org_id" => $form_state->getValue('org_id'),
                "theme_id" => $form_state->getValue('theme_id'),
                "pager_size" => $form_state->getValue('pager_size'),
            ];

        // Get custom Form Submit values.
        $platformSetting = new HSPlatformSettingController();
        $customFormValues = $platformSetting->getCustomSubmitFormFields($form_state);
        if ($customFormValues) {
            $finalFormValues = array_merge($commonFormValues, $customFormValues);
        } else {
            $finalFormValues = $commonFormValues;
        }

        $this->config(HS_PLATFORM_SETTINGS)
            ->setData($finalFormValues)
            ->save();
    }
}
