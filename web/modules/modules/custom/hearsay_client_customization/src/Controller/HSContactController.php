<?php

namespace Drupal\hearsay_client_customization\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_common\Controller\HearsayBaseContactInterface;

/**
 * Class Hearsay Contact Controller.
 */
class HSContactController extends ControllerBase implements HearsayBaseContactInterface
{
    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_common\Controller\HearsayCommon
     */
    protected $hearsayCommon;

    /**
     * The Hearsay common module Helper Service.
     *
     * @var \Drupal\hearsay_client_customization\Controller\HearsayClientCustomization
     */
    protected $hearsayClientCustomization;

    /**
     * Constructor
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
    public function buildCustomContactForm($form, $form_state)
    {
        $themeName = '';
        $formData = [];
        $hearsayCommon = new HearsayCommon();
        $hearsayClientCustomization = new HearsayClientCustomization();
        $arrSettings = $hearsayCommon->getPlatformSettingConfig();
        $config = $hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];
        $themeName = $hearsayClientCustomization->getAdminContentConfigByThemeId()['themeName'];
        $formData = $this->getFormData($config, $themeName);
        $recaptchaSiteKey = $arrSettings->get('recaptcha_site_key');
        if ($themeName == HS_LIBRARY_INDIVIDUAL_AB || $themeName == HS_LIBRARY_TEAM_AB) {
            $form['form_label'] = [
                '#type' => 'label',
                '#title' => '<h2 class="form-title">' . $formData->moduleTitle . '</h2><p class="form-sub-title">' . $formData->moduleSubTitle . '</p>',
                '#prefix' => '<div id="contact-form" class="library-contact-form"><div class="container"><div class="form-wrapper form-content-wrapper"><div class="text-center">',
                '#suffix' => '</div>',
            ];

            $form['req'] = [
                '#type' => 'item',
                '#title' => $formData->requiredText,
                '#prefix' => '',
                '#suffix' => '',
                '#required' => TRUE
            ];

            $form['first_name'] = [
                '#type' => 'textfield',
                '#title' => $formData->firstNameLabel ?? '',
                '#prefix' => '<div class="g-recaptcha" data-sitekey="' . $recaptchaSiteKey . ' " data-size="invisible"><div class="form-group d-flex flex-lg-row flex-column text-fields"><div class="form-field">',
                '#suffix' => '<div class="first-name-error error-box"></div></div>',
                '#placeholder' => $formData->firstNamePlaceholder ?? '',
                '#required' => TRUE
            ];

            $form['last_name'] = [
                '#type' => 'textfield',
                '#title' => $formData->lastNameLabel ?? '',
                '#prefix' => '<div class="form-field">',
                '#suffix' => '<div class="last-name-error error-box"></div></div></div>',
                '#placeholder' => $formData->lastNamePlaceholder ?? '',
                '#required' => TRUE
            ];

            $form['email'] = [
                '#type' => 'email',
                '#title' => $formData->emailLabel ?? '',
                '#prefix' => '<div class="form-group d-flex flex-lg-row flex-column text-fields"><div class="form-field">',
                '#suffix' => '<div class="email-error error-box"></div></div>',
                '#placeholder' => $formData->emailPlaceholder ?? '',
                '#required' => TRUE
            ];

            $form['phone'] = [
                '#type' => 'tel',
                '#title' => $formData->phoneLabel ?? '',
                '#prefix' => '<div class="form-field">',
                '#suffix' => '<div class="phone-error error-box"></div></div></div>',
                '#placeholder' => $formData->phonePlaceholder ?? '',
                '#required' => TRUE
            ];

            $form['zip_code'] = [
                '#type' => 'textfield',
                '#title' => $formData->zipCodeLabel ?? '',
                '#prefix' => '<div class="form-group d-flex flex-lg-row flex-column text-fields"><div class="form-field">',
                '#suffix' => '<div class="zip-code-error error-box "></div></div>',
                '#placeholder' => $formData->zipCodePlaceholder ?? '',
                '#size' => 5,
                '#maxlength' => 5,
                '#required' => TRUE,
            ];

            $form['event_id'] = [
                '#type' => 'hidden',
                '#attributes' => [
                    'id' => 'event_id',
                ],
            ];

            $form['blueconic_profile_id'] = [
                '#type' => 'hidden',
                '#attributes' => [
                    'id' => 'blueconic_profile_id',
                ],
            ];

            $form['contact_message'] = [
                '#type' => 'textarea',
                '#title' => $formData->messageLabel ?? '',
                '#prefix' => '<div class="form-field">',
                '#suffix' => '<div class="contact-message-error error-box"></div></div></div>',
                '#placeholder' => $formData->messagePlaceholder ?? '',
                '#required' => TRUE
            ];

            $form['field_captcha_token'] = [
                '#type' => 'hidden',
                '#attributes' => [
                    'id' => 'field_captcha_token',
                ],
            ];

            if ($themeName == HS_LIBRARY_INDIVIDUAL_AB) {
                $form['required'] = array(
                    '#type' => 'checkbox',
                    '#title' => $formData->optInText ?? '',
                    '#required' => TRUE,
                    '#prefix' => '<div class="form-group d-flex flex-lg-row flex-column"><div class="form-field">',
                    '#suffix' => '</div>',
                );
            }

            $form['field_submit'] = [
                '#type' => 'hidden',
                '#value' => 'false',
                '#attributes' => [
                    'id' => 'field_submit',
                ],
            ];

            if ($themeName == HS_LIBRARY_INDIVIDUAL_AB) {
                $form['actions'] = [
                    '#type' => 'button',
                    '#value' => $formData->submitText ?? '',
                    '#ajax' => [
                        'callback' => '::ajaxSubmitContact', // Do not change Callback function name. It is been used in submitting the form.
                        'wrapper' => 'cms_container', // Do not change Container name. It is been used in displaying thank-you block.
                        'progress' => array(
                            'type' => 'throbber',
                            'message' => $this->t('Please Wait...'),
                        ),
                    ],
                    '#prefix' => '<div class="form-actions text-center">',
                    '#suffix' => '</div></div></div>',
                    '#attributes' => [
                        'class' => ['align-self-top contact_submit'],
                        'aria-label' => $formData->submitText ?? '',
                    ]
                ];
            } else if ($themeName == HS_LIBRARY_TEAM_AB) {
                $form['actions'] = [
                    '#type' => 'button',
                    '#value' => $formData->submitText ?? '',
                    '#ajax' => [
                        'callback' => '::ajaxSubmitContact', // Do not change Callback function name. It is been used in submitting the form.
                        'wrapper' => 'cms_container', // Do not change Container name. It is been used in displaying thank-you block.
                        'progress' => array(
                            'type' => 'throbber',
                            'message' => $this->t('Please Wait...'),
                        ),
                    ],
                    '#prefix' => '<div class="form-actions text-center">',
                    '#suffix' => '</div></div>',
                    '#attributes' => [
                        'class' => ['align-self-top contact_submit'],
                        'aria-label' => $formData->submitText ?? '',
                    ]
                ];
            }

            $form['div_close'] = [
                '#type' => 'hidden',
                '#prefix' => '',
                '#suffix' => '',
                '#attributes' => [
                    'aria-label' => $formData->submitText ?? '',
                    'data-value' => $formData->submitText ?? '',
                    'data-label' => $formData->submitText ?? '',
                    'id' => 'contactus_save',
                    'class' => ['']
                ],
            ];

            $form['disclaimer'] = [
                '#type' => 'item',
                '#title' => $formData->messageText ?? '',
                '#prefix' => '',
                '#suffix' => '</div>',
            ];

            $form['cms_container'] = [
                '#type' => 'container',
                '#attributes' => [
                    'id' => 'cms_container',
                    'class' => 'col-md-12'
                ],
                '#prefix' => '',
                '#suffix' => '</div></div>',
            ];

            if ($form_state->getValue('actions', NULL)) {
                $form['cms_container']['other_cms'] = [
                    '#type' => 'item',
                    '#title' => $this->t('<div class="thanks-section"><h2>' . $config->get('successful_submission_title') . '</h2> <p>' . $config->get('successful_submission_text') . '</p></div>'),
                ];
            }

            $form['message'] = [
                '#type' => 'markup',
                '#markup' => '<div class="result_message container"></div>',
            ];

            $form['#attached']['library'][] = 'hearsay_thrivent/library-contact-form';
        }

        if ($themeName == HS_NPG_AB || $themeName == HS_P3_AB) {
            $currentUrl = \Drupal::request()->getRequestUri();
            $explodedUrl = explode('/', $currentUrl);
            if (isset($explodedUrl[2]) && $explodedUrl[2] == 'events' || isset($explodedUrl[3]) && $explodedUrl[3] != '') {

                $form['form_label'] = [
                    '#type' => 'label',
                    '#title' => '<h2 class="form-title section-heading">' . $formData->moduleTitle . '</h2>',
                    '#prefix' => '<div id="rsvp-contact-form" class="npg-p3-contact-form section-container"><div class="container"><div class="form-wrapper form-content-wrapper"><div class="text-center form-title-container my-0">',
                    '#suffix' => '</div>',
                ];

                $form['first_name'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->firstNameLabel ?? $formData->firstNamePlaceholder ?? '',
                    '#prefix' => '<div class="g-recaptcha" data-sitekey="' . $recaptchaSiteKey . ' " data-size="invisible"><div class="d-flex form-groups-wrapper d-flex flex-md-row flex-column"><div class="form-group col-md-6 col-sm-12"><div class="form-name-group d-flex"><div class="form-field">',
                    '#suffix' => '<div class="first-name-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['last_name'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->lastNameLabel ?? $formData->lastNamePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="last-name-error error-box"></div></div></div>',
                    '#required' => TRUE
                ];

                $form['email'] = [
                    '#type' => 'email',
                    '#title' => $formData->emailLabel ?? $formData->emailPlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="form-field-icon email-icon"></div><div class="email-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['phone'] = [
                    '#type' => 'tel',
                    '#title' => $formData->phoneLabel ?? $formData->phonePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="form-field-icon phone-icon"></div><div class="phone-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['zip_code'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->zipCodeLabel ?? $formData->zipCodePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="zip-code-error error-box "></div></div></div>',
                    '#maxlength' => 5,
                    '#required' => TRUE,
                ];

                $form['contact_message'] = [
                    '#type' => 'textarea',
                    '#title' => $formData->messageLabel ?? $formData->messagePlaceholder ?? '',
                    '#prefix' => '<div class="form-field  col-md-6 col-sm-12 d-flex flex-column">',
                    '#suffix' => '<div class="contact-message-error error-box"></div></div></div>',
                    '#required' => FALSE,
                    '#resizable' => 'none',
                ];

                $form['field_captcha_token'] = [
                    '#type' => 'hidden',
                    '#attributes' => [
                        'id' => 'field_captcha_token',
                    ],
                ];

                $form['blueconic_profile_id'] = [
                    '#type' => 'hidden',
                    '#attributes' => [
                        'id' => 'blueconic_profile_id',
                    ],
                ];

                $form['text_message'] = [
                    '#type' => 'item',
                    '#title' => $formData->legalText ?? '',
                    '#prefix' => '<div class="text-center col-sm-12">',
                    '#suffix' => '</div>',
                ];

                $form['field_submit'] = [
                    '#type' => 'hidden',
                    '#value' => 'false',
                    '#attributes' => [
                        'id' => 'field_submit',
                    ],
                ];

                $form['actions'] = [
                    '#type' => 'button',
                    '#value' => $formData->submitText ?? '',
                    '#ajax' => [
                        'callback' => '::ajaxSubmitContact', // Do not change Callback function name. It is been used in submitting the form.
                        'wrapper' => 'cms_container', // Do not change Container name. It is been used in displaying thank-you block.
                        'progress' => array(
                            'type' => 'throbber',
                            'message' => $this->t('Please Wait...'),
                        ),
                    ],
                    '#prefix' => '<div class="form-actions text-center">',
                    '#suffix' => '</div></div></div>',
                    '#attributes' => [
                        'class' => ['align-self-top contact_submit form-button'],
                        'aria-label' => $formData->submitText ?? '',
                    ]
                ];

                $form['div_close'] = [
                    '#type' => 'hidden',
                    '#prefix' => '',
                    '#suffix' => '',
                    '#attributes' => [
                        'aria-label' => $formData->submitText ?? '',
                        'data-value' => $formData->submitText ?? '',
                        'data-label' => $formData->submitText ?? '',
                        'id' => 'contactus_save',
                    ],
                ];

                $form['cms_container'] = [
                    '#type' => 'container',
                    '#attributes' => [
                        'id' => 'cms_container',
                        'class' => 'col-md-12'
                    ],
                    '#prefix' => '<div class="load-spinner modal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content d-flex flex-column align-items-center">
                                                <div class="lds-dual-ring"></div>
                                            </div>
                                        </div>
                                    </div>',
                    '#suffix' => '</div></div>',
                ];

                if ($form_state->getValue('actions', NULL)) {
                    $form['cms_container']['other_cms'] = [
                        '#type' => 'item',
                        '#title' => $this->t('
                            <div class="thanks-section modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content d-flex flex-column align-items-center">
                                        <svg class="mdi-icon hcl-color-done" width="40" height="40" fill="#00ae4a" viewBox="0 0 24 24">
                                            <path d="M10,17L5,12L6.41,10.58L10,14.17L17.59,6.58L19,8M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z">
                                            </path>
                                        </svg>
                                        <p class="thanks-title text-center mt-2 mb-0">' . $config->get('rsvp_successful_submission_title') . '</p>
                                        <p class="thanks-text text-center py-1 mb-0">' . $config->get('rsvp_successful_submission_text') . '</p>
                                        <button class="mt-4 ok-button form-button" type="button">OK</button>
                                    </div>
                                </div>
                            </div>'),
                    ];
                }

                $form['message'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="result_message container"></div>',
                ];
            } else {
                $form['form_label'] = [
                    '#type' => 'label',
                    '#title' => '<h2 class="form-title section-heading">' . $formData->moduleTitle . '</h2>',
                    '#prefix' => '<div id="contact-form" class="npg-p3-contact-form section-container"><div class="container"><div class="form-wrapper form-content-wrapper"><div class="text-center form-title-container my-0">',
                    '#suffix' => '</div>',
                ];

                $form['first_name'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->firstNameLabel ?? $formData->firstNamePlaceholder ?? '',
                    '#prefix' => '<div class="g-recaptcha" data-sitekey="' . $recaptchaSiteKey . ' " data-size="invisible"><div class="d-flex form-groups-wrapper d-flex flex-md-row flex-column"><div class="form-group  col-md-6 col-sm-12"><div class="form-field">',
                    '#suffix' => '<div class="first-name-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['last_name'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->lastNameLabel ?? $formData->lastNamePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="last-name-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['email'] = [
                    '#type' => 'email',
                    '#title' => $formData->emailLabel ?? $formData->emailPlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="email-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['phone'] = [
                    '#type' => 'tel',
                    '#title' => $formData->phoneLabel ?? $formData->phonePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="phone-error error-box"></div></div>',
                    '#required' => TRUE
                ];

                $form['zip_code'] = [
                    '#type' => 'textfield',
                    '#title' => $formData->zipCodeLabel ?? $formData->zipCodePlaceholder ?? '',
                    '#prefix' => '<div class="form-field">',
                    '#suffix' => '<div class="zip-code-error error-box "></div></div></div>',
                    '#maxlength' => 5,
                    '#required' => TRUE,
                ];

                $form['contact_message'] = [
                    '#type' => 'textarea',
                    '#title' => $formData->messageLabel ?? $formData->messagePlaceholder ?? '',
                    '#prefix' => '<div class="form-field  col-md-6 col-sm-12 d-flex flex-column">',
                    '#suffix' => '<div class="contact-message-error error-box"></div></div></div>',
                    '#required' => TRUE,
                    '#resizable' => 'none',
                ];

                $form['field_captcha_token'] = [
                    '#type' => 'hidden',
                    '#attributes' => [
                        'id' => 'field_captcha_token',
                    ],
                ];

                $form['blueconic_profile_id'] = [
                    '#type' => 'hidden',
                    '#attributes' => [
                        'id' => 'blueconic_profile_id',
                    ],
                ];

                $form['text_message'] = [
                    '#type' => 'item',
                    '#title' => $formData->legalText ?? '',
                    '#prefix' => '<div class="text-center col-sm-12">',
                    '#suffix' => '</div>',
                ];

                $form['field_submit'] = [
                    '#type' => 'hidden',
                    '#value' => 'false',
                    '#attributes' => [
                        'id' => 'field_submit',
                    ],
                ];

                $form['actions'] = [
                    '#type' => 'button',
                    '#value' => $formData->submitText ?? '',
                    '#ajax' => [
                        'callback' => '::ajaxSubmitContact', // Do not change Callback function name. It is been used in submitting the form.
                        'wrapper' => 'cms_container', // Do not change Container name. It is been used in displaying thank-you block.
                        'progress' => array(
                            'type' => 'throbber',
                            'message' => $this->t('Please Wait...'),
                        ),
                    ],
                    '#prefix' => '<div class="form-actions text-center">',
                    '#suffix' => '</div></div></div>',
                    '#attributes' => [
                        'class' => ['align-self-top contact_submit form-button'],
                        'aria-label' => $formData->submitText ?? '',
                    ]
                ];

                $form['div_close'] = [
                    '#type' => 'hidden',
                    '#prefix' => '',
                    '#suffix' => '',
                    '#attributes' => [
                        'aria-label' => $formData->submitText ?? '',
                        'data-value' => $formData->submitText ?? '',
                        'data-label' => $formData->submitText ?? '',
                        'id' => 'contactus_save',
                    ],
                ];

                $form['cms_container'] = [
                    '#type' => 'container',
                    '#attributes' => [
                        'id' => 'cms_container',
                        'class' => 'col-md-12'
                    ],
                    '#prefix' => '<div class="load-spinner modal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content d-flex flex-column align-items-center">
                                                <div class="lds-dual-ring"></div>
                                            </div>
                                        </div>
                                    </div>',
                    '#suffix' => '</div></div>',
                ];

                if ($form_state->getValue('actions', NULL)) {
                    $form['cms_container']['other_cms'] = [
                        '#type' => 'item',
                        '#title' => $this->t('
                            <div class="thanks-section modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content d-flex flex-column align-items-center">
                                        <svg class="mdi-icon hcl-color-done" width="40" height="40" fill="#00ae4a" viewBox="0 0 24 24">
                                            <path d="M10,17L5,12L6.41,10.58L10,14.17L17.59,6.58L19,8M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z">
                                            </path>
                                        </svg>
                                        <p class="thanks-title text-center mt-2 mb-0">' . $config->get('successful_submission_title') . '</p>
                                        <p class="thanks-text text-center py-1 mb-0">' . $config->get('successful_submission_text') . '</p>
                                        <button class="mt-4 ok-button form-button" type="button">OK</button>
                                    </div>
                                </div>
                            </div>'),
                    ];
                }

                $form['message'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="result_message container"></div>',
                ];
            }
            $form['#attached']['library'][] = 'hearsay_thrivent/p3-npg-contact-form';
        }

        return $form;
    }

    /**
     * Get Form fields data, name and placeholders from Admin config form.
     *
     * @param object $config
     *   Config form data.
     * @param string $themeName
     *   Theme name of current site.
     *
     * @return object
     *   Array of Form fields data.
     */
    public function getFormData($config, $themeName)
    {
        $formData = [];
        if ($themeName == HS_NPG_AB || $themeName == HS_P3_AB) {
            $currentUrl = $_SERVER['REQUEST_URI'];
            $explodedUrl = explode('/', $currentUrl);
            if (isset($explodedUrl[2]) && $explodedUrl[2] == 'events' || isset($explodedUrl[3]) && $explodedUrl[3] != '') {
                $formData = [
                    'moduleTitle' => $config->get('rsvp_form_title') ?? NULL,
                    'firstNamePlaceholder' => $config->get('rsvp_first_name_placeholder') ?? NULL,
                    'lastNamePlaceholder' => $config->get('rsvp_last_name_placeholder') ?? NULL,
                    'emailPlaceholder' => $config->get('rsvp_email_placeholder') ?? NULL,
                    'phonePlaceholder' => $config->get('rsvp_phone_placeholder') ?? NULL,
                    'zipCodePlaceholder' => $config->get('rsvp_zip_code_placeholder') ?? NULL,
                    'messagePlaceholder' => $config->get('rsvp_message_placeholder') ?? NULL,
                    'legalText' => $config->get('rsvp_legal_text')['value'] ?? NULL,
                    'submitText' => $config->get('rsvp_submit_button_text') ?? NULL,
                ];
            } else {
                $formData = [
                    'moduleTitle' => $config->get('form_title') ?? NULL,
                    'firstNamePlaceholder' => $config->get('first_name_placeholder') ?? NULL,
                    'lastNamePlaceholder' => $config->get('last_name_placeholder') ?? NULL,
                    'emailPlaceholder' => $config->get('email_placeholder') ?? NULL,
                    'phonePlaceholder' => $config->get('phone_placeholder') ?? NULL,
                    'zipCodePlaceholder' => $config->get('zip_code_placeholder') ?? NULL,
                    'messagePlaceholder' => $config->get('message_placeholder') ?? NULL,
                    'legalText' => $config->get('legal_text')['value'] ?? NULL,
                    'submitText' => $config->get('submit_button_text') ?? NULL,
                ];
            }
        } elseif ($themeName == HS_LIBRARY_TEAM_AB) {
            $formData = [
                'moduleTitle' => $config->get('form_title') ?? NULL,
                'moduleSubTitle' => $config->get('form_subtitle') ?? NULL,
                'requiredText' => $config->get('required_text') ?? NULL,
                'firstNameLabel' => $config->get('first_name_label') ?? NULL,
                'firstNamePlaceholder' => $config->get('first_name_placeholder') ?? NULL,
                'lastNameLabel' => $config->get('last_name_label') ?? NULL,
                'lastNamePlaceholder' => $config->get('last_name_placeholder') ?? NULL,
                'emailLabel' => $config->get('email_label') ?? NULL,
                'emailPlaceholder' => $config->get('email_placeholder') ?? NULL,
                'phoneLabel' => $config->get('phone_label') ?? NULL,
                'phonePlaceholder' => $config->get('phone_placeholder') ?? NULL,
                'zipCodeLabel' => $config->get('zip_code_label') ?? NULL,
                'zipCodePlaceholder' => $config->get('zip_code_placeholder') ?? NULL,
                'messageLabel' => $config->get('message_label') ?? NULL,
                'messagePlaceholder' => $config->get('message_placeholder') ?? NULL,
                'messageText' => $config->get('message_text') ?? NULL,
                'submitText' => $config->get('submit_button_text') ?? NULL,
            ];
        } elseif ($themeName == HS_LIBRARY_INDIVIDUAL_AB) {
            $formData = [
                'moduleTitle' => $config->get('form_title') ?? NULL,
                'moduleSubTitle' => $config->get('form_subtitle') ?? NULL,
                'requiredText' => $config->get('required_text') ?? NULL,
                'firstNameLabel' => $config->get('first_name_label') ?? NULL,
                'firstNamePlaceholder' => $config->get('first_name_placeholder') ?? NULL,
                'lastNameLabel' => $config->get('last_name_label') ?? NULL,
                'lastNamePlaceholder' => $config->get('last_name_placeholder') ?? NULL,
                'emailLabel' => $config->get('email_label') ?? NULL,
                'emailPlaceholder' => $config->get('email_placeholder') ?? NULL,
                'phoneLabel' => $config->get('phone_label') ?? NULL,
                'phonePlaceholder' => $config->get('phone_placeholder') ?? NULL,
                'zipCodeLabel' => $config->get('zip_code_label') ?? NULL,
                'zipCodePlaceholder' => $config->get('zip_code_placeholder') ?? NULL,
                'optInText' => $config->get('opt_in_text') ?? NULL,
                'messageLabel' => $config->get('message_label') ?? NULL,
                'messagePlaceholder' => $config->get('message_placeholder') ?? NULL,
                'messageText' => $config->get('message_text') ?? NULL,
                'submitText' => $config->get('submit_button_text') ?? NULL,
            ];
        }
        return (object)$formData;
    }

    /**
     * Check for validations with error messages response.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     * @param object $config
     *   Config form data.
     *
     * @return array
     *   Array of Error messages after form validations.
     */
    public function getErrorMessages($form_state, $config)
    {
        $errorMessages = array();
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeName = $hearsayClientCustomization->getAdminContentConfigByThemeId()['themeName'];
        if ($form_state->getValue('first_name') == '') {
            $errorMessages['first-name-error'] = $config->get('empty_state_error') ?? NULL;
        }

        if ($form_state->getValue('last_name') == '') {
            $errorMessages['last-name-error'] = $config->get('empty_state_error') ?? NULL;
        }

        if ($form_state->getValue('zip_code') == '') {
            $errorMessages['zip-code-error'] = $config->get('empty_state_error') ?? NULL;
        }

        $currentUrl = $_SERVER['REQUEST_URI'];
        if ((explode('/', $currentUrl)[2] != 'events' or explode('/', $currentUrl)[3] == '') and ($themeName == HS_NPG_AB or $themeName = HS_P3_AB)) {
            if ($form_state->getValue('contact_message') == '') {
                $errorMessages['contact-message-error'] = $config->get('empty_state_error') ?? NULL;
            }
        }

        $phoneFilter = "/(^(\+?1([\s.-])?)?\(?\d{3}\)?([\s.-])?\d{3}([\s.-])?\d{4}?$)/";
        if ($form_state->getValue('phone') == '') {
            $errorMessages['phone-error'] = $config->get('phone_error') ?? NULL;
        } elseif (!preg_match($phoneFilter, $form_state->getValue('phone'))) {
            $errorMessages['phone-error'] = $config->get('phone_error') ?? NULL;
        }

        $email = $form_state->getValue('email');
        if ($email == '') {
            $errorMessages['email-error'] = $config->get('email_error') ?? NULL;
        } else {
            // validate email
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if ($email === false) {
                $errorMessages['email-error'] = $config->get('email_error') ?? NULL;
            }
        }
        return  $errorMessages;
    }

    /**
     * Get Event Detail for RSVP.
     * 
     * @param object $form_state
     *   object of FormStateInterface.
     * 
     * @return array
     *   Array of Event ID, Name and Start Date.
     */
    public function getEventDetail($form_state)
    {
        $currentUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $eventId = $eventName = $eventDate = '';
        $responseData = $this->hearsayCommon->getEventsAPIData(FALSE);
        $hearsayClientCustomization = new HearsayClientCustomization();
        $themeName = $hearsayClientCustomization->getAdminContentConfigByThemeId()['themeName'];
        if ($themeName == HS_LIBRARY_INDIVIDUAL_AB || $themeName == HS_LIBRARY_TEAM_AB) {
            $eventId = $form_state->getValue('event_id');
            foreach ($responseData as $event) {
                $event = (object)$event;
                if ($eventId == $event->id) {
                    $eventId = $event->id;
                    $eventName = $event->event_name;
                    $eventDate = $event->event_date;
                }
            }
        }
        if ($themeName == HS_NPG_AB || $themeName == HS_P3_AB) {
            foreach ($responseData as $event) {
                $event = (object)$event;
                if (str_contains($currentUrl, $event->slug)) {
                    $eventId = $event->id;
                    $eventName = $event->event_name;
                    $eventDate = $event->event_date;
                }
            }
        }
        return [
            'eventId' => $eventId,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'currentUrl' => $currentUrl,
        ];
    }

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
    public function getSerializedEntity($form_state, $themeID, $config, $profileData)
    {
        $SerializedEntity = [];
        // Check for UTM parameters
        $headers = getallheaders()['Referer'];
        $UTMflag = false;
        foreach (UTM_PARAMETERS as $param) {
            if (str_contains($headers, $param)) {
                $UTMflag = true;
            }
        }

        if ($form_state) {
            // Create serialized entity
            $eventData = $this->getEventDetail($form_state);
            $currentUrl = $eventData['currentUrl'];
            if (explode('/', $currentUrl)[4] == 'events' or explode('/', $currentUrl)[5] != '' or str_contains((explode('/', $currentUrl)[4]), 'events?')) {
                $SerializedEntity = json_encode(
                    [
                        "contactMethod" => "phone",
                        "firstName" => $form_state->getValue('first_name'),
                        "lastName" => $form_state->getValue('last_name'),
                        "themeId" => $themeID,
                        "email" => $form_state->getValue('email'),
                        "phone" => $form_state->getValue('phone'),
                        "message" => $form_state->getValue('contact_message'),
                        "isRelateMessageEnabled" => $form_state->getValue('required') == true ? true : false,
                        "optin" => $form_state->getValue('required') == true ? true : false,
                        "postalCode" => $form_state->getValue('zip_code'),
                        "submissionDatetime" => date('c'),
                        "currentURL" => $eventData['currentUrl'],
                        "referringURL" => $UTMflag == true ? $headers : '',
                        "cdpId" => $form_state->getValue('blueconic_profile_id') ?? '',
                        "textConsent" => $config->get('opt_in_text') ?? '',
                        "fpFirstName" => $profileData->first_name,
                        "fpLastName" => $profileData->last_name,
                        "event" => [
                            "id" => $eventData['eventId'],
                            "title" => $eventData['eventName'],
                            "date" => $eventData['eventDate'],
                        ]
                    ]
                );
            }
            // Create serialized entity if no event
            else {
                $SerializedEntity = json_encode(
                    [
                        "contactMethod" => "phone",
                        "firstName" => $form_state->getValue('first_name'),
                        "lastName" => $form_state->getValue('last_name'),
                        "themeId" => $themeID,
                        "email" => $form_state->getValue('email'),
                        "phone" => $form_state->getValue('phone'),
                        "message" => $form_state->getValue('contact_message'),
                        "isRelateMessageEnabled" => $form_state->getValue('required') == true ? true : false,
                        "optin" => $form_state->getValue('required') == true ? true : false,
                        "postalCode" => $form_state->getValue('zip_code'),
                        "submissionDatetime" => date('c'),
                        "currentURL" => $eventData['currentUrl'],
                        "referringURL" => $UTMflag == true ? $headers : '',
                        "cdpId" => $form_state->getValue('blueconic_profile_id') ?? '',
                        "textConsent" => $config->get('opt_in_text') ?? '',
                        "fpFirstName" => $profileData->first_name,
                        "fpLastName" => $profileData->last_name,
                    ]
                );
            }
        }
        return $SerializedEntity;
    }

    /**
     * Submit ajax contact form.
     *
     * @param object $form_state
     *   object of FormStateInterface.
     * @param object $node
     *   Node object.
     * @param object $arrSettings
     *   Object of Platform settings form fields.
     * @param object $config
     *   Config form data.
     * @param array $profileData
     *   Site Profile data.
     */
    public function customAjaxSubmitContactForm($form_state, $node, $arrSettings, $config, $profileData)
    {
        $token = $orgId = $contactUsApiEndPoint = '';
        // Get Contact API, token, Org ID from Platform settings form
        $token = $arrSettings->get('token');
        $orgId = $arrSettings->get('org_id');
        $contactUsApiEndPoint = $arrSettings->get('contact_api');
        $node = \Drupal::routeMatch()->getParameter('node');
        $fieldCaptchaToken = $nodeBaseSlug = '';

        if ($form_state) {
            $fieldCaptchaToken = $form_state->getValue('field_captcha_token');
        }

        $nodeBaseSlug = $node->get('field_ref_taxo_base_slug')->referencedEntities();
        if ($nodeBaseSlug) {
            foreach ($nodeBaseSlug as $data) {
                $themeID = $data->field_theme_id->value;
                $slugName = $data->name->value;
            }
            $SerializedEntity = $this->getSerializedEntity($form_state, $themeID, $config, $profileData);

            if (!empty($SerializedEntity)) { // If Serialized array received
                if ($token != "" && $orgId != "" && $contactUsApiEndPoint != "" && $slugName != "") { // If all configurations properly filled
                    if ($fieldCaptchaToken != "") { // Check for Recaptcha token
                        $secret_key = $arrSettings->get('recaptcha_secret_key');
                        $this->hearsayCommon->sendContact($secret_key, $fieldCaptchaToken, $contactUsApiEndPoint, $orgId, $slugName, $SerializedEntity, $token);
                    } else {
                        \Drupal::logger('hearsay_contact')->notice('Recaptcha Token Missing.');
                    }
                } else {
                    \Drupal::logger('hearsay_contact')->notice('Token or Org ID or Contact Us API End point not properly filled in Platform Settings form OR Site name not fetched properly.');
                }
            } else {
                \Drupal::logger('hearsay_contact')->notice('Serialized entity array not Received.');
            }
        }
    }

    /**
     * Submit Simple contact form
     */
    public function customSubmitContactForm()
    {
    }
}
