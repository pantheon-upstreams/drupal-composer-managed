<?php

namespace Drupal\hearsay_common\Controller;
/**
 * Interface Hearsay Header.
 */

interface HearsayHeaderInterface
{
    /**
     * Build Custom Menu.
     *
     * @param array $headerMenuBlock
     *   Menu block.
     *
     * @return array
     *   Array of themes with variables.
     */
    public function buildHeader($headerMenuBlock);

    /**
     * Build Common Variables for all themes.
     *
     * @return array
     *   Array of themes with variables.
     */
    public function defineThemeVariablesForHeader();
}