<?php

namespace Drupal\hearsay_common\Controller;
/**
 * Interface Hearsay Base Contact.
 */
interface HearsayBaseContactInterface
{
    /**
     * Get Form Fields data.
     *
     * @param array $form
     *   Form data.
     * @param object $form_state
     *   object of FormStateInterface.
     *
     * @return array
     *   Array of contact form field.
     */
    public function buildCustomContactForm($form, $form_state);

    /**
     * Get Form fields name and placeholders from Admin config form.
     *
     * @param object $config
     *   Config form data.
     * @param string $themeName
     *   Theme name of current site.
     *
     * @return object
     *   Array of Form fields data.
     */
    public function getFormData($config, $themeName);

    /**
     * Check for validations and create error messages for it.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     * @param object $config
     *   Config form data.
     *
     * @return array
     *   Array of Error messages after form validations.
     */
    public function getErrorMessages($form_state, $config);

    /**
     * Get array of fields to be posted on contact form API.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     * @param string $themeID
     *   Theme ID of current site.
     * @param object $config
     *   Config form data.
     * @param array $profileData
     *   Site Profile data.
     *
     * @return array
     *   Array of Serialized entity.
     */
    public function getSerializedEntity($form_state, $themeID, $config, $profileData);

    /**
     * Submit ajax contact form.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     * @param object $node
     *   Node object.
     * @param array $arrSettings
     *   array of Platform settings form fields.
     * @param object $config
     *   Config form data.
     * @param array $profileData
     *   Site Profile data.
     */
    public function customAjaxSubmitContactForm($form_state, $node, $arrSettings, $config, $profileData);

    /**
     * Submit Simple contact form
     */
    public function customSubmitContactForm();
}