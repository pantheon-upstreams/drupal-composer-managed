<?php

namespace Drupal\hearsay_common\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_client_customization\Controller\HSContactController;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\HtmlCommand;
use GuzzleHttp\Exception\RequestException;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Class Get Data.
 */
class HearsayCommon extends ControllerBase
{
    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_preview\Services\SiteTools
     */
    protected $siteTools;

    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_automation_process\HSUtilityService
     */
    protected $utility_obj;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->siteTools = \Drupal::service('hearsay_preview.site_tools');
        $this->utility_obj =  \Drupal::service('hearsay_automation_process_service.utility');
    }

    /**
     * Get API response.
     *
     * @param bool $isFirstCall
     *   Check if function call is the first call or not.
     *
     * @return object
     *   Object array received from API.
     */
    public function getProfileData($isFirstCall = FALSE)
    {
        $response = [];
        //Check current path for '/preview';
        // For preview mode  - use preview configuration.
        if ($this->siteTools->isPreview() == true) {
            $previewConfig = \Drupal::config('sites_preview.api');
            $slug = $this->siteTools->queryString('slug');
            if ($this->siteTools->isDeleteExpired() == true) {
                $path = "/expired_page";
                $response = new RedirectResponse($path);
                $response->send();
            } else {
                $response = $previewConfig->get($slug);
            }
            $response = json_decode(json_encode($response));
        } else {
            // For standard mode - use standard configuration
            // This case will be executed when first module loaded on page.
            if ($isFirstCall == TRUE) {
                $response = $this->utility_obj->getSiteProfileDataFromAPI();
            } else {
                $APIResponse_data = \Drupal::service('config.factory')->getEditable('hearsay_automation.API');
                $response = $APIResponse_data->get('hearsay_automation');
                if ($response == '') {
                    $response = $this->utility_obj->getSiteProfileDataFromAPI();
                }
            }
        }
        return (object)$response;
    }

    /**
     * Get Social Post API response.
     *
     * @param bool $isFirstCall
     *   Check if function call is the first call or not.
     *
     * @return object
     *   Object array received from Social Post API.
     */
    public function getSocialPostData($isFirstCall = FALSE)
    {
        $response = [];
        // This case will be executed when first module loaded on page.
        if ($isFirstCall == TRUE) {
            $response = $this->utility_obj->getRecentUpdatesDataFromAPI();
        } else {
            $APIResponse_data = \Drupal::service('config.factory')->getEditable('hearsay_automation.API');
            $response = $APIResponse_data->get('hearsay_automation_post');
            if ($response == '') {
                $response = $this->utility_obj->getRecentUpdatesDataFromAPI();
            }
        }
        return $response;
    }

    /**
     * Get Events API response.
     *
     * @param bool $isFirstCall
     *   Check if function call is the first call or not.
     *
     * @return object
     *   Object array received from Events API.
     */
    public function getEventsAPIData($isFirstCall = FALSE)
    {
        $response = array();
        if ($isFirstCall == TRUE) { // This case will be executed when first module loaded on page
            $response = $this->utility_obj->getEventsDataFromAPI();
        } else {
            $APIResponse_data = \Drupal::service('config.factory')->getEditable('hearsay_automation.API');
            $response = $APIResponse_data->get('hearsay_automation_event');
            if ($response == '') {
                $response = $this->utility_obj->getEventsDataFromAPI();
            }
        }
        return $response;
    }

    /**
     * Get Site Configuration data.
     *
     * @return object
     *   Object of configuration variables.
     */
    public function getAdminContentConfig()
    {
        $config = \Drupal::config(HS_ADMIN_SITE_SETTINGS);
        return $config;
    }

    /**
     * Get Platform Setting Configuration data.
     *
     * @return object
     *   Object of configuration variables.
     */
    public function getPlatformSettingConfig()
    {
        $config = \Drupal::config(HS_PLATFORM_SETTINGS);
        return $config;
    }

    /**
     * Get current Base Slug and Site's Language.
     *
     * @return array
     *   Array of current base slug and base slug language.
     */
    public function getBaseSlugDetailsByNode()
    {
        $data = [];
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node) {
            // Get Term ID from node.
            $baseSlugTermId = $node->get('field_ref_taxo_base_slug')->getValue()[0]['target_id']; // Get Term ID from node
            $term = Term::load($baseSlugTermId);
            if ($term) {
                // Create return array.
                $data = [
                    'baseSlug' => $term->get('name')->value,
                    'baseSlugLanguage' => $term->get('field_baseslug_default_language')->value,
                    'node_type' => $node->getType(),
                ];
            }
        }
        return $data;
    }

    /**
     * Get current Base Slug and Theme ID.
     *
     * @return array
     *   Array of current base slug and base slug theme id.
     */
    public function getThemeIdByNode()
    {
        $data = [];
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node) {
            // Get Term ID from node.
            $baseSlugTermId = $node->get('field_ref_taxo_base_slug')->getValue()[0]['target_id']; // Get Term ID from node
            $term = Term::load($baseSlugTermId);
            if ($term) {
                // For preview mode  - use preview configuration
                if ($this->siteTools->isPreview() == true) {
                    $profileData = $this->getProfileData();
                    $arrSettings = $this->getPlatformSettingConfig();
                    $previewData = $this->utility_obj->getPreviewBaseSlugData($this->siteTools);
                    // Create return array
                    $data = [
                        'baseSlug' => $term->get('name')->value,
                        'theme_id' => $previewData ? $previewData['themeId'] : ($profileData->theme_id ?? $arrSettings->get('theme_id_lt')),
                        'node_type' => $node->getType(),
                        'term_id' => $term->tid->value,
                    ];
                }
                else{
                    // For standard mode - use standard configuration.
                    $data = [
                        'baseSlug' => $term->get('name')->value,
                        'theme_id' => $term->get('field_theme_id')->value,
                        'node_type' => $node->getType(),
                        'term_id' => $term->tid->value,
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Get Media Data.
     *
     * @param string $media_type
     *   Media type to be fetched.
     *
     * @return array
     *   Array of media elements of specified media type.
     */
    public function getMediaDetails($media_type)
    {
        $media_elements = \Drupal::entityQuery('media')->condition('bundle', $media_type)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        $media_data = $media_details[0];
        return $media_data;
    }

    /**
     * Get Media Data by media category
     * @param string Media type to be fetched
     * @param string Category field name
     * @param string Category name
     * @return array Array of media elements of specified media type
     */
    public function getMediaDetailsByCategory($media_type,$fieldName,$category)
    {
        $media_elements = \Drupal::entityQuery('media')->condition('bundle', $media_type)->condition($fieldName, $category)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        $media_data = $media_details[0];
        return $media_data;
    }

    /**
     * Get Media using canonical name.
     *
     * @param string $canonicalName
     *   Canonical name of the media to be fetched.
     * @param string $canonicalFieldName
     *   Field machine name to compare the canonical name.
     *
     * @return array
     *   Array of media element of specified canonical name.
     */
    public function getCanonicalMediaDetails($canonicalName, $canonicalFieldName)
    {
        $media_elements = \Drupal::entityQuery('media')->condition($canonicalFieldName, $canonicalName)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        return $media_details[0];
    }

    /**
     * Get Media using canonical name and Theme ID
     * @param string Canonical name of the media to be fetched
     * @param string Field machine name to compare the canonical name
     * @param string Theme Name
     * @return array Array of media element of specified canonical name
     */
    public function getCanonicalMediaDetailsByTheme($canonicalName, $canonicalFieldName, $themeName)
    {
        $media_elements = \Drupal::entityQuery('media')->condition($canonicalFieldName, $canonicalName)->condition('field_banner_themes', $themeName)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        return $media_details[0];
    }

    /**
     * Get Banner Media Dropdown array using Media Data
     * @param array Array of media element
     * @return array Array of media element to be shown in dropdown
     */
    public function getGetBannerDropdownDetails($MediaDetails)
    {
        $bannerImages = [];
        if ($MediaDetails) {
            foreach ($MediaDetails as $media) {
                $bannerImages[$media->field_banner_canonical_name->value] = $this->t($media->getName());
            }
        }
        return $bannerImages;
    }

    /**
     * Get Default Images Media Dropdown array using Media Data
     * @param array Array of media element
     * @return array Array of media element to be shown in dropdown
     */
    public function getDefaultImageDropdownDetails($MediaDetails)
    {
        $defaultImages = [];
        if ($MediaDetails) {
            foreach ($MediaDetails as $media) {
                $defaultImages[$media->field_image_canonical_name->value] = $this->t($media->getName());
            }
        }
        return $defaultImages;
    }

    /**
     * Get Default Videos Media Dropdown array using Media Data
     * @param array Array of media element
     * @return array Array of media element to be shown in dropdown
     */
    public function getDefaultVideoDropdownDetails($MediaDetails)
    {
        $defaultVideos = [];
        if ($MediaDetails) {
            foreach ($MediaDetails as $media) {
                $defaultVideos[$media->field_video_canonical_name->value] = $this->t($media->getName());
            }
        }
        return $defaultVideos;
    }

    /**
     * Get Media Image URL.
     *
     * @param int $targetId
     *   Target ID of media image.
     *
     * @return string
     *   URL of media image.
     */
    public function getMediaImageUrl($targetId)
    {
        $img_url = '';
        if ($targetId) {
            $file = File::load($targetId);
            $img_url = $file->createFileUrl();
        }
        return $img_url;
    }

    /**
     * Get designations from config.
     *
     * @param object $config
     *   Settings form data.
     * @param array $arrDesignation
     *   Designation data.
     *
     * @return array
     *   Array of final designation.
     */
    public function getDesignations($config, $arrDesignation)
    {
        $strDesignation = $config->get('designation_value');
        $designations = [];
        if (!empty($strDesignation)) {
            $designations = explode("\r\n", $strDesignation);
        }
        $finalDesignation = [];
        foreach ($arrDesignation as $designationValue) {
            foreach ($designations as $piece) {
                $designationData = explode('|', $piece);
                if ($designationData[0] == $designationValue) {
                    $finalDesignation[] = $designationData[1];
                    // Exit the inner loop once a match is found.
                    break;
                }
            }
        }
        return $finalDesignation;
    }

    /**
     * Get absolute url of media by id.
     *
     * @param string $id
     *   Media ID.
     *
     * @return string
     *   Public media URL.
     */
    public function getPublicUrlMediaById($id)
    {
        $url = '';
        if (empty($id)) {
            return '';
        }
        try {
            $media = Media::load($id);
            $fid = $media->getSource()->getSourceFieldValue($media);
            $file = File::load($fid);
            if ($file) {
                // Generate absolute url by file uri.
                $url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
            }
        } catch (\Throwable $e) {
            \Drupal::logger('hearsay_common')->error($e->getMessage());
        }
        return $url;
    }

    /**
     * Get Media Data By Node.
     *
     * @param string $media_type
     *   Media type.
     * @param string $nodeType
     *   Node type.
     *
     * @return array
     *   Array of media elements of specified media type.
     */
    public function getMediaByNode($media_type, $node_type)
    {
        $media_elements = \Drupal::entityQuery('media')->condition('bundle', $media_type)->condition('field_banner_node_type', $node_type)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        $media_data = $media_details[0];
        return $media_data;
    }

    /**
     * get Media Data By Node and By Theme Name attached to media
     * @param string Media type to be fetched
     * @param string Node Type
     * @param string Theme name
     * @return array Array of media elements of specified media type
     */
    public function getMediaByNodeAndTheme($media_type, $node_type, $themeName)
    {
        $media_elements = \Drupal::entityQuery('media')->condition('bundle', $media_type)->condition('field_banner_node_type', $node_type)->condition('field_banner_themes', $themeName)->execute();
        $media_details[] = Media::loadMultiple($media_elements);
        $media_data = $media_details[0];
        return $media_data;
    }

    /**
     * Get Node Link for specific node type having same Slug as current.
     *
     * @param string $nodeType
     *   Node type for which we need URL.
     *
     * @return string
     *   Link for node.
     */
    public function getNodeLinkForCurrentSlug($nodeType){
        $nodeLink = '';
        $currentTermId = $this->getThemeIdByNode()['term_id'];
        $node = \Drupal::entityQuery('node')->condition('type', $nodeType)->condition('field_ref_taxo_base_slug', $currentTermId)->execute();
        if($node){
            $nodeLink = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.reset($node));
        }
        return ['node_id' => reset($node), 'link' => $nodeLink];
    }

    // Contact From Common Functions

    /**
     * Attach Form error messages to the form fields.
     *
     * @param object $responseAjax
     *   AjaxResponse Object.
     * @param object $form_state
     *   object of FormStateInterface.
     * @param object $config
     *   Config form data.
     *
     * @return object
     *   Object of AjaxResponse with attached error messages.
     */
    public function attachValidateForm($responseAjax, $form_state, $config)
    {
        $contactController = new HSContactController();
        $errorMassage = $contactController->getErrorMessages($form_state, $config);
        if ($errorMassage) {
            foreach ($errorMassage as $key => $value) {
                $responseAjax->addCommand(
                    new HtmlCommand(
                        '.' . $key,
                        '<div class="error-msg d-flex"><span>' . $this->t('@result', ['@result' =>  $value]) . '</span></div>'
                    )
                );
            }
            return $responseAjax;
        }
    }

    /**
     * Validate the recaptcha before form submission.
     *
     * @param string $secretKey
     *   Recaptcha secret key.
     * @param string $remoteIp
     *   User IP address.
     * @param string $gRecaptchaResponse
     *   Field reCaptcha token.
     *
     * @return bool
     *   Valid recaptcha or not.
     */
    public function checkSpamRecaptcha($secret_key, $remoteip, $g_recaptcha_response)
    {
        $spamCheck = true;
        $response = $data = $recaptcha = '';
        $client = \Drupal::httpClient();
        try {
            $body = [
                'form_params' => [
                    'secret' => $secret_key,
                    'response' => $g_recaptcha_response,
                    'remoteip' => $remoteip,
                ],
            ];
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', $body);
            $data = $response->getBody();
            $recaptcha = json_decode($data);
            if ($recaptcha->success) {
                $spamCheck = false;
            }
        } catch (RequestException $e) {
            \Drupal::logger('hearsay_contact')->notice('Token Null');
        }
        return $spamCheck;
    }

    /**
     * Send contact form to Hearsay Site.
     *
     * @param string $secretKey
     *   Recaptcha Secret key.
     * @param string $fieldCaptchaToken
     *   Recaptcha token.
     * @param string $contactUsApiEndPoint
     *   Contact API endpoint.
     * @param string $orgId
     *   Org ID of site.
     * @param string $slugName
     *   Site slug name.
     * @param array $serializedEntity
     *   Array of Serialized entity with form submission values.
     * @param string $token
     *   Site API token.
     */
    public function sendContact($secret_key, $fieldCaptchaToken, $contactUsApiEndPoint, $orgId, $slugName, $SerializedEntity, $token)
    {
        // Execute form submission
        $user_ip = \Drupal::request()->getClientIp();
        $spam = $this->checkSpamRecaptcha($secret_key, $user_ip, $fieldCaptchaToken);
        if (!$spam) {
            $contactUsApiEndPointUrl = $contactUsApiEndPoint . $orgId . "/contact/" . $slugName;
            // Form Submission
            try {
                $response = \Drupal::httpClient()->post(
                    $contactUsApiEndPointUrl,
                    [
                        'body' => $SerializedEntity,
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'x-auth-token' => $token
                        ],
                    ]
                );
                \Drupal::logger('hearsay_contact')->notice('Contact Submitted for' . $slugName . '.');
            } catch (\Exception $e) {
                \Drupal::logger('api_logs')->error($e->getMessage());
            }
        } else {
            \Drupal::logger('hearsay_contact')->notice('Invalid Recaptcha token.');
        }
    }

    /**
     * Clear Form inputs after submit.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     */
    public function clearFormInputs(FormStateInterface $form_state)
    {
        $input = $form_state->getUserInput();

        // We should not clear the system items from the user input.
        $clean_keys = $form_state->getCleanValueKeys();
        $clean_keys[] = 'ajax_page_state';

        foreach ($input as $key => $item) {
            if (!in_array($key, $clean_keys) && substr($key, 0, 1) !== '_') {
                unset($input[$key]);
            }
        }
        $form_state->setUserInput($input);
        $form_state->setRebuild();
        $form_state->setStorage([]);
        return $form_state;
    }

    /**
     * Get language toggled site URL if available.
     *
     * @return string
     *   URL of toggled site.
     */
    public function getLanguageToggleData()
    {
        $node = \Drupal::routeMatch()->getParameter('node');
        $nodeType = $slugData = $term = $slugNode = $nid = $alias = $oldTermData = $returnData = '';
        if ($node != null) {
            // For preview mode  - use preview configuration.
            if ($this->siteTools->isPreview() == true) {
                $slug = $this->siteTools->queryString('slug');
                $hearsayClientCustomization = new HearsayClientCustomization();
                // Get base slug language for co-operators.
                $slugLanguage = $hearsayClientCustomization->getBaseSlugLanguage($slug);
                $returnData = [
                    'toggleUrl' => $alias,
                    'currentUrlLanguage' => $slugLanguage,
                ];
            } else {
                $nodeType = $node->getType();
                // Get current base slug data.
                $slugData = $this->getBaseSlugDetailsByNode();
                // Get slug for en/fr site if exist.
                $oldTerm = \Drupal::entityQuery('taxonomy_term')->condition('vid', 'base_slugs')->condition('name', $slugData['baseSlug'])->execute();
                $oldTerm = reset($oldTerm);
                $oldTermData = Term::load($oldTerm);
                if ($slugData['baseSlugLanguage'] == 'English') {
                    // Create slug to check if en/fr site exist or not.
                    $slugToFind = substr($slugData['baseSlug'], 0, -2) . 'fr';
                } elseif ($slugData['baseSlugLanguage'] == 'French') {
                    // Create slug to check if en/fr site exist or not.
                    $slugToFind = substr($slugData['baseSlug'], 0, -2) . 'en';
                }
                // Get slug for en/fr site if exist.
                $term = \Drupal::entityQuery('taxonomy_term')->condition('vid', 'base_slugs')->condition('name', $slugToFind)->execute();
                $term = reset($term);
                if ($nodeType == 'home') {
                    // Get home page for.
                    $slugNode = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
                        'type' => 'home',
                        'field_ref_taxo_base_slug' => $term,
                    ]);
                } elseif ($nodeType == 'our_team') {
                    $slugNode = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
                        'type' => 'our_team',
                        'field_ref_taxo_base_slug' => $term,
                    ]);
                }
                if ($slugNode) {
                    $nid = reset($slugNode)->id();
                    $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
                }
                $returnData = [
                    'toggleUrl' => $alias,
                    'currentUrlLanguage' => $oldTermData->field_baseslug_default_language->value,
                ];
            }
        }
        return $returnData;
    }

    /**
     * Get provinces from all offices received in API response.
     *
     * @return array
     *   Provinces data array.
     */
    public function getAllProvinces()
    {
        $profileProvinces = [];
        $profileData = $this->getProfileData(false);
        if ($profileData->coop_office) {
            foreach ($profileData->coop_office as $office) {
                $provinces = explode(',', $office['state']);
                foreach ($provinces as $province) {
                    !in_array($province, $profileProvinces) ? array_push($profileProvinces, str_replace(" ", "", $province)) : '';
                }
            }
        }
        return $profileProvinces;
    }

    /**
     * Get cities from all offices received in API response.
     *
     * @return array
     *   Cities data array.
     */
    public function getAllCities()
    {
        $profileCities = [];
        $profileData = $this->getProfileData(false);
        if ($profileData->coop_office) {
            foreach ($profileData->coop_office as $office) {
                $cities = explode(',', $office['city']);
                foreach ($cities as $city) {
                    !in_array($city, $profileCities) ? array_push($profileCities, str_replace(" ", "", $city)) : '';
                }
            }
        }
        return $profileCities;
    }

    /**
     * Unpublish node to remove link from menu and sitemap.
     *
     * @param string $nodeType
     *   Page content type.
     * @param string $currentTermId
     *   Taxonomy term ID.
     *
     */
    public function unpublishNodes($nodeType, $currentTermId)
    {
        $query = \Drupal::entityQuery('node')->condition('type', $nodeType)->condition('field_ref_taxo_base_slug', $currentTermId);
        $entityDetails = $query->execute();
        $entityId = reset($entityDetails);
        if ($entityId) {
            $nodeLoad = Node::load($entityId);
            if ($nodeLoad instanceof NodeInterface) {
                $nodeLoad->setUnpublished();
                $nodeLoad->save();
            }
        }
    }

    /**
     * Publish node to add link in menu and sitemap.
     *
     * @param string $nodeType
     *   Page content type.
     * @param string $currentTermId
     *   Taxonomy term ID.
     *
     */
    public function publishNodes($nodeType, $currentTermId)
    {
        $query = \Drupal::entityQuery('node')->condition('type', $nodeType)->condition('field_ref_taxo_base_slug', $currentTermId);
        $entityDetails = $query->execute();
        $entityId = reset($entityDetails);
        if ($entityId) {
            $nodeLoad = Node::load($entityId);
            if ($nodeLoad instanceof NodeInterface) {
                $nodeLoad->setPublished();
                $nodeLoad->save();
            }
        }
    }
}