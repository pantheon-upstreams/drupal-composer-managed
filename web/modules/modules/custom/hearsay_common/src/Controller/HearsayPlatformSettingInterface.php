<?php

namespace Drupal\hearsay_common\Controller;

/**
 * Interface Hearsay Platform Setting.
 */
interface HearsayPlatformSettingInterface
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
     *   Array of Hearsay Platform Settings form fields.
     */
    public function buildCustomPlatformSettingForm($form, $form_state);

    /**
     * Get Form fields name and placeholders from Admin config form.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     *
     * @return object
     *   Array of Form fields data.
     */
    public function getCustomSubmitFormFields($form_state);
}
