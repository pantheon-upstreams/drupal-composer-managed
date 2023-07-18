<?php

namespace Drupal\hearsay_common\Controller;

/**
 * Interface Hearsay Client Customization.
 */
interface HearsayClientCustomizationInterface
{
    /**
     * Get Client Customization Theme IDs.
     *
     * @return array
     *   Array of client specific theme IDs.
     */
    public function getProcessedData($moduleName, $isFirstCall = false);

    /**
     * Get custom config values from platform setting form.
     *
     * @return array
     *   Array of theme IDs.
     */
    public function getCsPlatformSettings();

    /**
     * Get Site Content Configuration data.
     *
     * @return object
     *   Object of configuration variables.
     */
    public function getAdminContentConfigByThemeId();
}
