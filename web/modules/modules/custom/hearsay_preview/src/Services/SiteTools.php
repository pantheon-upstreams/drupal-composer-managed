<?php

namespace Drupal\hearsay_preview\Services;

use Drupal\taxonomy\Entity\Term;
use Drupal\node\NodeInterface;
use Drupal\Core\Url;
use Drupal\hearsay_common\Controller\HearsayCommon;

/**
 * Defines a class to provide site utilities.
 *
 * @see \Drupal\hearsay_preview\Services
 */
class SiteTools
{
    /**
     * Set current path.
     *
     * @var string
     */
    protected $currentPath;

    /**
     * Set request object.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_common\Controller\HearsayCommon
     */
    protected $hearsayCommon;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->request = \Drupal::service('request_stack')->getCurrentRequest();
        $url = Url::fromRoute('<current>');
        $this->currentPath = $url->toString();
    }

    /**
     * Check if is delete expired.
     *
     * @return bool
     *   Returns the Expired status for slug and removes the expired slugs.
     */
    public function isDeleteExpired()
    {
        $previewConfig = \Drupal::config('sites_preview.api');
        $slug = $this->queryString('slug');
        $response_time = $previewConfig->get($slug);

        $current_time = \Drupal::time()->getCurrentTime();
        $date_formatter = \Drupal::service('date.formatter');
        $currentDate = $date_formatter->format($current_time, 'custom', 'Y-m-d H:i');

        $timeExpiration = $response_time['config_expiration'];
        $finalDate = '';
        if ($timeExpiration) {
            $finalDate = $date_formatter->format($timeExpiration, 'custom', 'Y-m-d H:i');
        }
        $expired = $currentDate > $finalDate ? true : false;
        return  $expired;
    }

    /**
     * Get current path.
     *
     * @return string
     *   Returns the current path string
     */
    public function currentPath()
    {
        return $this->currentPath;
    }

    /**
     * Get URL query string params.
     *
     * @param string $param
     *   URL query string params.
     *
     * @return string
     *   URL params.
     */
    public function queryString($param = null)
    {
        if (isset($param) == false) {
            return $this->request->query->all();;
        }

        if (isset($this->request)) {
            return $this->request->query->get($param);
        }
    }

    /**
     * Check if is preview true.
     *
     * @return bool
     *   Checks if the current url has 'preview' in it.
     */
    public function isPreview()
    {
        $curPath = $this->currentPath;
        if (strpos($curPath, 'preview') !== false) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get theme ID.
     *
     * @param int $termThemeId
     *   Base slug theme ID.
     *
     * @return int
     *   Returns theme ID from permanent slug or preview config.
     */
    public function getThemeID($termThemeId = '')
    {
        //Get the theme_id from the preview config, if available
        if ($this->isPreview() !== true) {
            $node = \Drupal::routeMatch()->getParameter('node');
            if ($node instanceof NodeInterface) {
                $nid = $node->id();
                $nodesData = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
                $termSlug = $nodesData->field_ref_taxo_base_slug->target_id;
                $termThemeId = Term::load($termSlug)->get('field_theme_id')->value;
            }
        } else {
            $hearsayCommon = new HearsayCommon();
            $slugData = $hearsayCommon->getProfileData();
            $termThemeId = $slugData->theme_id ?? $termThemeId;
        }
        return $termThemeId;
    }
}
