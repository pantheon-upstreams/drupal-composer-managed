<?php

namespace Drupal\hearsay_preview\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DeletePreview.
 */
class DeletePreview extends ControllerBase
{
    /**
     * Returns the Expired page if slug preview is expired.
     */
    public function pageExpired()
    {
        return [
            '#theme' => 'expired_page',
        ];
    }

    /**
     * Used to set the cache time of a page.
     *
     * @return void
     *   Return 0.
     */
    public function getCacheMaxAge()
    {
        return 0;
    }
}
