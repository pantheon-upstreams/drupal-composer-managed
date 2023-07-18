<?php

namespace Drupal\hearsay_automation_process;

use Drupal\taxonomy\Entity\Term;
use Drupal\pathauto\PathautoState;
use Drupal\node\Entity\Node;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;

/**
 * HSUtilityService will define some generic method to be used in application.
 */
class HSUtilityService
{
    public const DEFAULT_PAGER_SIZE = 200;

    /**
     * Set Logs object.
     *
     * @var string
     */
    public $logMessage = '';

    // Some API Call Functions.

    /**
     * Get all sites data from hearsay API.
     *
     * @return array
     *   Array of all sites from API.
     */
    public function getAllSitesFromAPI()
    {
        try {
            $finalResponse = [];
            // Get All Configurable Settings.
            $arrSettings = $this->getAllConfigurableSettings();
            if (($arrSettings['sites_api'] != null) && ($arrSettings['token'] != null) && ($arrSettings['org_id'] != null)) {
                $pageCount = 1;
                $this->logMessage .= 'Pulling all sites from API</br>';
                do {
                    $apiURL = $arrSettings['sites_api'] . $arrSettings['org_id'] . "/sites/?page_size=" . $arrSettings['pager_size'] . "&current_page=" . $pageCount;
                    try {
                        $request = \Drupal::httpClient()->request(
                            'GET',
                            $apiURL,
                            [
                                'headers' => ['x-auth-token' => $arrSettings['token']],
                            ]
                        );
                        $this->logMessage .= 'Page -' . $pageCount . ' api response is- ' . $request->getReasonPhrase() . ' - ' . $request->getStatusCode() . '</br>';
                        $arrResponse = json_decode($request->getBody(), true);
                        $finalResponse = array_merge($finalResponse, $arrResponse['data']);
                        $pageCount = $pageCount + 1;
                    } catch (\Exception $e) {
                        \Drupal::logger('api_logs')->error($e->getMessage());
                    }
                } while ($pageCount <= $arrResponse['num_pages']);
            } else {
                $this->logMessage .= 'Unable to call Get All Site API due to improper Configuration data (API Endpoint, Token Or OrgID).<br/>';
            }
            $this->logMessage .= 'Sites fetching process through HS API completed.<br>';
        } catch (\Exception $e) {
            \Drupal::logger('hearsay_automation_process')->error($e->getMessage());
        }
        return $finalResponse;
    }

    /**
     * Get site profile data from hearsay API.
     *
     * @return array
     *   Array of Profile data response from API.
     */
    public function getSiteProfileDataFromAPI()
    {
        try {
            $response = '';
            // Get All Configurable Settings.
            $arrSettings = $this->getAllConfigurableSettings();
            // Get node data using base slug.
            $nodeData = $this->getCurrentPathBaseSlugData();
            if ($nodeData) {
                if (($arrSettings['sites_api'] != null) && ($arrSettings['token'] != null) && ($arrSettings['org_id'] != null)) {
                    $apiURL = $arrSettings['sites_api'] . $arrSettings['org_id'] . "/groups/" . $nodeData['workspaceId'] . "/sites/" . $nodeData['accId'] . "/profile/";
                    try {
                        $request = \Drupal::httpClient()->request(
                            'GET',
                            $apiURL,
                            [
                                'headers' => ['x-auth-token' => $arrSettings['token']],
                            ]
                        );
                        $response = json_decode($request->getBody());
                    } catch (\Exception $e) {
                        \Drupal::logger('api_logs')->error($e->getMessage());
                    }
                } else {
                    \Drupal::logger('hearsay_automation_process')->notice('<pre>Unable to Call Site Profile API due to improper Configuration data (API Endpoint, Token Or OrgID). </pre>');
                }
            }
            // Setting a variable to store API data in variable.
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation', '')->save();
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation', $response)->save();
        } catch (\Exception $e) {
            \Drupal::logger('hearsay_automation_process')->notice($e->getMessage());
        }
        return $response;
    }

    /**
     * Get site recent updates/ Posts data from hearsay API.
     *
     * @return array
     *   Array of Recent Update response from API.
     */
    public function getRecentUpdatesDataFromAPI()
    {
        try {
            $response = '';
            // Get All Configurable Settings.
            $arrSettings = $this->getAllConfigurableSettings();
            // Hearsay preview service to get node data using base slug.
            $siteTools = \Drupal::service('hearsay_preview.site_tools');
            if ($siteTools->isPreview() == true) {
                $nodeData = $this->getPreviewBaseSlugData($siteTools);
            } else {
                $nodeData = $this->getCurrentPathBaseSlugData();
            }
            if ($nodeData) {
                if (($arrSettings['sites_api'] != null) && ($arrSettings['token'] != null) && ($arrSettings['org_id'] != null)) {
                    $apiURL = $arrSettings['sites_api'] . $arrSettings['org_id'] . "/poly/site/" . $nodeData['accId'] . "/data/posts";
                    try {
                        $request = \Drupal::httpClient()->request(
                            'GET',
                            $apiURL,
                            [
                                'headers' => ['x-auth-token' => $arrSettings['token']],
                            ]
                        );
                        $response = json_decode($request->getBody());
                        if ($response) {
                            $response = (object)$response;
                        }
                    } catch (\Exception $e) {
                        \Drupal::logger('api_logs')->error($e->getMessage());
                    }
                } else {
                    \Drupal::logger('hearsay_automation_process')->notice('<pre>Unable to call Post API due to improper Configuration data (API Endpoint, Token Or OrgID). </pre>');
                }
            }
            // Setting a variable to store API data in variable.
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation_post', '')->save();
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation_post', $response)->save();
        } catch (\Exception $e) {
            \Drupal::logger('hearsay_automation_process')->notice($e->getMessage());
        }
        return $response;
    }

    /**
     * Get site  events data from hearsay API.
     *
     * @return array
     *   Array of events from API.
     */
    public function getEventsDataFromAPI()
    {
        try {
            $response = '';
            // Get All Configurable Settings.
            $arrSettings = $this->getAllConfigurableSettings();
            $siteTools = \Drupal::service('hearsay_preview.site_tools');
            // Get node data using base slug.
            if ($siteTools->isPreview() == true) {
                $nodeData = $this->getPreviewBaseSlugData($siteTools);
            } else {
                $nodeData = $this->getCurrentPathBaseSlugData();
            }
            if ($nodeData) {
                if (($arrSettings['sites_api'] != null) && ($arrSettings['token'] != null) && ($arrSettings['org_id'] != null)) {
                    $apiURL = $arrSettings['sites_api'] . $arrSettings['org_id'] . "/poly/site/" . $nodeData['accId'] . "/data/events";
                    try {
                        $request = \Drupal::httpClient()->request(
                            'GET',
                            $apiURL,
                            [
                                'headers' => ['x-auth-token' => $arrSettings['token']],
                            ]
                        );
                        $response = json_decode($request->getBody());
                        if ($response) {
                            $response = (object)$response;
                        }
                    } catch (\Exception $e) {
                        \Drupal::logger('api_logs')->error($e->getMessage());
                    }
                }
            }
            // Setting a variable to store API data in variable.
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation_event', '')->save();
            \Drupal::service('config.factory')->getEditable('hearsay_automation.API')->set('hearsay_automation_event', $response)->save();
        } catch (\Exception $e) {
            \Drupal::logger('hearsay_automation_process')->notice($e->getMessage());
        }
        return $response;
    }

    /**
     * Get Preview baseSlug data.
     *
     * @return array
     *   Array of preview base slug.
     */
    public function getPreviewBaseSlugData($siteTools)
    {
        $nodeData = [];
        $baseSlug = $siteTools->queryString('slug');
        $termId = $this->checkBaseSlugExist($baseSlug);
        if ($termId) {
            $term = Term::load($termId);
            $nodeData['accId'] = $term->field_account_id->value;
            $nodeData['workspaceId'] = $term->field_workspace_id->value;
            $nodeData['themeId'] = $term->field_theme_id->value;
        }
        return $nodeData;
    }

    // Some Commonly used Functions.

    /**
     * Get all configurable settings from Drupal.
     *
     * @return array
     *   Return hearsay platform settings.
     */
    public function getAllConfigurableSettings()
    {
        $arrSettings = [];
        $config = \Drupal::config(HS_PLATFORM_SETTINGS);
        $hearsayClientCustomization = new HearsayClientCustomization();
        // Get custom form settings.
        $arrCSSettings = $hearsayClientCustomization->getCsPlatformSettings();
        $arrSettings['token'] = $config->get('token');
        $arrSettings['org_id'] = $config->get('org_id');
        $arrSettings['sites_api'] = $config->get('sites_api');
        $arrSettings['pager_size'] = $config->get('pager_size') ? $config->get('pager_size') : self::DEFAULT_PAGER_SIZE;
        $arrSettings['theme_id'] = $config->get('theme_id');
        if ($arrCSSettings) {
            $arrSettings = array_merge($arrSettings, $arrCSSettings);
        }
        return $arrSettings;
    }

    /**
     * Get current path baseSlug data.
     *
     * @return object
     *   Array of current path base slug.
     */
    public function getCurrentPathBaseSlugData()
    {
        $node = \Drupal::routeMatch()->getParameter('node');
        $nodeData = [];
        if ($node) {
            $node_baseSlug = $node->get('field_ref_taxo_base_slug')->referencedEntities();
            if ($node_baseSlug) {
                foreach ($node_baseSlug as $data) {
                    $nodeData['accId'] = $data->field_account_id->value;
                    $nodeData['workspaceId'] = $data->field_workspace_id->value;
                }
            }
        }
        return $nodeData;
    }

    /**
     * Verify site theme ID's.
     *
     * @param int $themeID
     *   Theme ID.
     *
     * @return bool
     *   Value for site belong to theme ID's or not.
     */
    public function verifyAllowedTheme($themeID)
    {
        $isPresentInAllowedThemes = false;
        $arrSettings = $this->getAllConfigurableSettings();
        if ($arrSettings) {
            $strThemeIds = $arrSettings['theme_id'];
            $arrThemeIds = explode(",", $strThemeIds);
            $isPresentInAllowedThemes = in_array($themeID, $arrThemeIds);
        }
        return $isPresentInAllowedThemes;
    }

    /**
     * Verify site ID's for sitemap entity.
     *
     * @param int $themeID
     *   Theme ID.
     * @param string $sitemapThemeIds
     *   Theme IDs from Sitemap entity.
     *
     * @return bool
     *   True/False value for site belong to theme ID in Sitemap.
     */
    public function verifyAllowedNode($themeID, $sitemapThemeIds)
    {
        $isPresentInSitemapThemes = false;
        if ($sitemapThemeIds) {
            $arrThemeIds = explode(",", $sitemapThemeIds);
            $isPresentInSitemapThemes = in_array($themeID, $arrThemeIds);
        }
        return $isPresentInSitemapThemes;
    }

    /**
     * Check Base Slug Term By Account ID.
     *
     * @param int $accountId
     *   Account ID.
     *
     * @return int
     *   Return base slug Term Id.
     */
    public function checkBaseSlugByAccountId($accountId)
    {
        $accountId = trim($accountId);
        $termId = '';
        if ($accountId) {
            $taxonomyQuery = \Drupal::entityQuery('taxonomy_term');
            $taxonomyQuery->condition('vid', 'base_slugs');
            $taxonomyQuery->condition('field_account_id', $accountId);
            $termIds = $taxonomyQuery->execute();
            if (!empty($termIds)) {
                $termId = reset($termIds);
                return $termId;
            }
        }
    }

    /**
     * Get  Existing account IDs In Drupal System.
     *
     * @return array
     *   Return accountIds exist in system.
     */
    public function getExistingAccountIds()
    {
        $arrAccountIds = [];
        $taxonomyQuery = \Drupal::entityQuery('taxonomy_term');
        $taxonomyQuery->condition('vid', 'base_slugs');
        $termIds = $taxonomyQuery->execute();
        if (!empty($termIds)) {
            $terms = Term::loadMultiple($termIds);
            foreach ($terms as $term) {
                array_push($arrAccountIds, $term->field_account_id->value);
            }
        }
        return $arrAccountIds;
    }

    /**
     * Check Base Slug Term By Base Slug Name.
     *
     * @param string $strBaseSlug
     *   Base Slug Name.
     *
     * @return int
     *   Return base slug Term Id.
     */
    public function checkBaseSlugExist($strBaseSlug)
    {
        $termId = '';
        if ($strBaseSlug) {
            $strBaseSlug = trim($strBaseSlug);
            $taxonomyQuery = \Drupal::entityQuery('taxonomy_term');
            $taxonomyQuery->condition('vid', 'base_slugs');
            $taxonomyQuery->condition('name', $strBaseSlug);
            $termIds = $taxonomyQuery->execute();
            if (!empty($termIds)) {
                $termId = reset($termIds);
                return $termId;
            }
        }
    }

    /**
     * Get Base Slug Term By URL.
     *
     * @param string $baseSlugURL
     *   Base Slug Url.
     *
     * @return string
     *   Base Slug String.
     */
    public function getBaseSlugByUrl($baseSlugURL)
    {
        $lastElement = '';
        if (!empty($baseSlugURL)) {
            $arrUrlParts = explode('/', rtrim($baseSlugURL, '/'));
            $lastElement = end($arrUrlParts);
            return $lastElement;
        }
        return $lastElement;
    }

    /**
     * Get Path segment by index value.
     *
     * @param string $path
     *   Base page URL.
     * @param int $index
     *   Array index value.
     *
     * @return string
     *   Base Slug String.
     */
    public function getPathSegmentByIndex($path, $index)
    {
        $slug = '';
        if (!empty($baseSlugURL) && !empty($index)) {
            $arrUrlParts = explode('/', rtrim($baseSlugURL, '/'));
            $slug = $arrUrlParts[$index];
            return $slug;
        }
        return $slug;
    }

    // Some Queue Processing Functions.

    /**
     * Create Taxonomy base slug data for each queue item.
     *
     * @param array $data
     *   Term ID.
     */
    public function createBaseSlug($data)
    {
        if ($data) {
            $isAllowedCreate = false;
            // If slug is a Preview slug.
            if ($data['baseSlug'] == 'preview') {
                $isAllowedCreate = true;
            } else {
                // Verify allowed theme to create terms.
                $isPresentInAllowedThemes = $this->verifyAllowedTheme($data['themeId']);
                if ($isPresentInAllowedThemes) {
                    $isAllowedCreate = true;
                } else {
                    return null;
                }
            }
            if ($isAllowedCreate == true) {
                $term = Term::create(
                    [
                        'vid' => 'base_slugs',
                        'name' => $data['baseSlug'],
                        'field_account_id' => $data['accountId'],
                        'field_workspace_id' => $data['workspaceId'],
                        'field_theme_id' => $data['themeId'],
                        'field_hearsay_site_name' => $data['workspaceSiteName'],
                    ]
                );
                $term->save();
                return $term->id();
            } else {
                return null;
            }
        }
    }

    /**
     * Create Nodes For Specific BaseSlug.
     *
     * @param int $newThemeId
     *   New theme ID.
     * @param int $baseSlugTermId
     *   Base slug term ID.
     * @param string $baseSlugName
     *   Base slug name.
     */
    public function createNodes($newThemeId, $baseSlugTermId, $baseSlugName)
    {
        // Taxonomy query for hearsay_menu items.
        $taxonomyQuery = \Drupal::entityQuery('taxonomy_term');
        $taxonomyQuery->condition('vid', 'hearsay_menu');
        $termIds = $taxonomyQuery->execute();
        if (!empty($termIds)) {
            $terms = Term::loadMultiple($termIds);
            foreach ($terms as $term) {
                $isAllowedCreate = false;
                $termContentTypeRef = $term->get('field_content_type_for_page')->referencedEntities();
                foreach ($termContentTypeRef as $key => $entity) {
                    $content_type = $entity->id();
                }
                if ($term->field_path_url->value == '/') {
                    $alias_path = '/' . ($alt_alias ?? $baseSlugName);
                } else {
                    $alias_path = '/' . ($alt_alias ?? $baseSlugName) . '/' . $term->field_path_url->value;
                }
                // If slug is a Preview slug.
                if ($baseSlugName == 'preview') {
                    $isAllowedCreate = true;
                } else {
                    // Get theme id from configuration.
                    $isAllowedTheme = $this->verifyAllowedTheme($newThemeId);
                    // Get allowed nodes from configuration.
                    $isAllowedNode = $this->verifyAllowedNode($newThemeId, $term->field_theme_ids->value);
                    if ($isAllowedTheme && $isAllowedNode) {
                        $isAllowedCreate = true;
                    }
                }
                if ($isAllowedCreate == true) {
                    $storage = \Drupal::entityTypeManager()->getStorage('node');

                    $node = $storage->create(
                        [
                            'type' => $content_type,
                            'created' => \Drupal::time()->getRequestTime(),
                            'changed' => \Drupal::time()->getRequestTime(),
                            'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
                            'uid' => 1,
                            'status' => 1,
                            'title' => $term->getName(),
                            'field_ref_taxo_base_slug' => [$baseSlugTermId],
                            'path' => [
                                'alias' => $alias_path,
                                'pathauto' => PathautoState::SKIP,
                            ],
                        ]
                    );
                    $node->save();
                }
            }
        }
    }

    /**
     * Create OR Update BaseSlug And Nodes.
     *
     * @param array $data
     *   Term ID.
     */
    public function createOrUpdateBaseSlugAndNodes($data)
    {
        $objHSUtility = \Drupal::service('hearsay_automation_process_service.utility');
        if ($data) {
            $termIdOfAccId = $this->checkBaseSlugByAccountId($data['accountId']);
            $term = null;
            // Update IF site ID already present.
            if ($termIdOfAccId) {
                $term = Term::load($termIdOfAccId);
                if ($term) {
                    $isAllowedCreateUpdate = false;
                    // If slug is a Preview slug.
                    if ($data['baseSlug'] == 'preview') {
                        if (($data['workspaceSiteName'] != $term->field_hearsay_site_name->value) || ($data['workspaceId'] != $term->field_workspace_id->value) ||
                            ($data['baseSlug'] != $term->name->value)
                        ) {
                            $isAllowedCreateUpdate = true;
                        }
                    } else {
                        if (($data['workspaceSiteName'] != $term->field_hearsay_site_name->value) || ($data['workspaceId'] != $term->field_workspace_id->value) ||
                            ($data['themeId'] != $term->field_theme_id->value) || ($data['baseSlug'] != $term->name->value)
                        ) {
                            $isAllowedCreateUpdate = true;
                        }
                    }
                    if ($isAllowedCreateUpdate == true) {
                        $data['termId'] = $term->id();
                        $objHSUtility->deleteBaseSlugAndNodes($term->field_account_id->value);
                        $termID = $this->createBaseSlug($data);
                        $this->createNodes($data['themeId'], $termID, $data['baseSlug']);
                        return $termID;
                    }
                }
            } else {
                // Otherwise create new slug.
                $termID = $this->createBaseSlug($data);
                $this->createNodes($data['themeId'], $termID, $data['baseSlug']);
                return $termID;
            }
        }
    }

    /**
     * Delete BaseSlug And Nodes.
     *
     * @param array $accountId
     *   Account ID.
     */
    public function deleteBaseSlugAndNodes($accountId)
    {
        try {
            // Get term ID from SiteId.
            if ($accountId) {
                $termId = \Drupal::entityQuery("taxonomy_term")->condition("vid", "base_slugs")->condition("field_account_id", $accountId)->execute();
                $tid = reset($termId);
                $term = Term::load($tid);
                if ($term) {
                    // Query for getting all nodes of base slug.
                    $nodeQuery = \Drupal::entityQuery('node')->condition('field_ref_taxo_base_slug', $term->id());
                    $nodes = $nodeQuery->execute();
                    // Delete nodes.
                    if ($nodes) {
                        foreach ($nodes as $key => $nid) {
                            $node = Node::load($nid);
                            if ($node) {
                                $node->delete();
                            }
                        }
                    }
                    $term->delete();
                }
            }
        } catch (\Exception $e) {
            $this->logMessage .= 'Deletion Failed For - ' . $accountId . ' - ' . $term->label() . '<br/>';
        }
    }

    /**
     * Get Data Items With Processing Action like Create / Update / Delete.
     *
     * @param array $responseData
     *   Set of items for processing.
     */
    public function getDataItemsWithProcessingAction($responseData)
    {
        $deleteRecords = [];
        $arrFinalItemsToProcess = [];
        $responseAccIds = [];
        $arrSiteIdsInSystem = [];
        $arrSiteIdsInSystem = $this->getExistingAccountIds();

        if ($responseData) {
            foreach ($responseData as $element) {
                $isAllowedCreate = false;
                $baseSlugVal = $this->getBaseSlugByUrl($element['url']);
                // If slug is a Preview slug.
                if ($element['name'] == 'Preview') {
                    if (($element['id'] != null) && ($element['name'] != null) && ($element['workspace_id'] != null) &&
                        ($baseSlugVal != null)
                    ) {
                        $isAllowedCreate = true;
                    }
                } else {
                    if (($element['id'] != null) && ($element['name'] != null) && ($element['workspace_id'] != null) &&
                        ($element['theme'] != null) && ($baseSlugVal != null)
                    ) {
                        $isAllowedCreate = true;
                    }
                }
                if ($isAllowedCreate == true) {
                    $accountId = $element['id'];
                    $responseAccIds[$accountId] = $accountId;
                    $processAction = 'CREATE-UPDATE';
                    $termId = '';
                    $item = [
                        "processAction" => $processAction,
                        "termId" => $termId ? $termId : '',
                        "accountId" => $element['id'] ? $element['id'] : '',
                        "workspaceSiteName" => $element['name'] ? $element['name'] : '',
                        "createDate" => $element['create_date'] ? $element['create_date'] : '',
                        "workspaceId" => $element['workspace_id'] ? $element['workspace_id'] : '',
                        "baseSlug" => $baseSlugVal,
                        "themeId" => $element['theme'] ? $element['theme'] : '',
                    ];

                    array_push($arrFinalItemsToProcess, $item);
                }
            }
            // If records exists in drupal system.
            if ($arrSiteIdsInSystem) {
                $deleteRecords = array_diff($arrSiteIdsInSystem, $responseAccIds);
            }
            if ($deleteRecords) {
                foreach ($deleteRecords as $siteId) {
                    // Get term ID from slug name.
                    $termIds = \Drupal::entityQuery("taxonomy_term")->condition("vid", "base_slugs")->condition("field_account_id", $siteId)->execute();
                    if (!empty($termIds)) {
                        $termId = reset($termIds);
                        $term = Term::load($termId);
                        // Set process flag for current item to delete.
                        $processAction = 'DELETE';
                        $item = [
                            "processAction" => $processAction,
                            "termId" => $term->tid ? $term->tid : '',
                            "accountId" => $term->field_account_id->value ? $term->field_account_id->value : '',
                            "workspaceSiteName" => $term->field_hearsay_site_name->value ? $term->field_hearsay_site_name->value : '',
                            "createDate" => '',
                            "workspaceId" => $term->field_workspace_id->value ? $term->field_workspace_id->value : '',
                            "baseSlug" => $term->name,
                            "themeId" => $term->field_theme_id->value ? $term->field_theme_id->value : '',
                        ];
                        array_unshift($arrFinalItemsToProcess, $item);
                    }
                }
            }
        }
        return $arrFinalItemsToProcess;
    }

    /**
     * Store path alias for node.
     *
     * @param string $baseSlugName
     *   Base Slug.
     * @param string $altAlias
     *   Theme ID.
     * @param int $nodeId
     *   Node ID.
     */
    public function storePathAliasForNode($baseSlugName, $altAlias, $nodeId)
    {
        // Check for client specific slug matches.
        if ((str_contains($baseSlugName, "_abeille")) || (str_contains($baseSlugName, "_pa"))) {
            $path_alias = PathAlias::create([
                'path' => '/node/' . $nodeId,
                'alias' => $altAlias,
            ]);
            $path_alias->save();
        }
    }

    /**
     * Delete old path aliases for updated slug.
     *
     * @param string $nodeId
     *   The node id for alias deletion.
     * @param string $oldAlias
     *   The old alias name, to be deleted.
     */
    public function deleteOldAliases($nodeId, $oldAlias)
    {
        $objPathAliasStorage = \Drupal::entityTypeManager()->getStorage('path_alias');
        $arrAliases = $objPathAliasStorage->loadByProperties(['path' => '/node/' . $nodeId]);
        foreach ($arrAliases as $objAlias) {
            if ($objAlias->get('alias')->value == $oldAlias) {
                $objAlias->delete();
            }
        }
    }

    /**
     * Create preview slug element.
     *
     * @return array
     *   Array of preview data.
     */
    public function createPreviewSlug()
    {
        $arrPreviewData = [
            'id' => '1',
            'name' => "Preview",
            'create_date' => 1469482110,
            'url' => 'http://test-thrivent-hs.pantheonsite.io/preview/',
            'asset_type' => 13,
            'type' => "hearsay_site",
            'active' => true,
            'valid' => true,
            'theme' => '',
            'workspace_id' => '1',
        ];
        return $arrPreviewData;
    }
}
