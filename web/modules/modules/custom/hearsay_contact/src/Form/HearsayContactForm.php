<?php

/**
 * @file
 * Contains \Drupal\hearsay_contact\Form\HearsayContactForm.
 */

namespace Drupal\hearsay_contact\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\hearsay_client_customization\Controller\HearsayClientCustomization;
use Drupal\hearsay_client_customization\Controller\HSContactController;

class HearsayContactForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'hearsay_contact_form';
    }

    /**
     * Build form.
     *
     * @param array form
     * @param object $form_state object of FormStateInterface.
     * @return array returns form.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $contactController = new HSContactController();
        // Get Custom Form.
        $form = $contactController->buildCustomContactForm($form, $form_state);

        // Empty the form if No rsvp event for NPG / P3.
        $hearsayCommon = new HearsayCommon();
        $nodeType = $hearsayCommon->getThemeIdByNode()['node_type'];
        if ($nodeType == 'events_detail') {
            $responseData = $hearsayCommon->getEventsAPIData(TRUE);
            $currentUrl = $_SERVER['REQUEST_URI'];
            foreach ($responseData as $event) {
                $eventCanonical = '';
                $siteTools = \Drupal::service('hearsay_preview.site_tools');
                if ($siteTools->isPreview() == true) {
                    $eventCanonical = explode('?',explode('/', $currentUrl)[3])[0];
                }
                else{
                    $eventCanonical = explode('/', $currentUrl)[3];
                }
                if ($event->slug == $eventCanonical && $event->rsvp_type != 'form') {
                    $form = [];
                }
            }
        }
        return $form;
    }

    /**
     * Ajax Submission of Contact form to contact API.
     * @param array form
     * @param object $form_state
     *   object of FormStateInterface.
     */
    public function ajaxSubmitContact(array $form, FormStateInterface $form_state)
    {
        $hearsayCommon = new HearsayCommon();
        $hearsayClientCustomization = new HearsayClientCustomization();
        $contactController = new HSContactController();
        $profileData = $hearsayCommon->getProfileData(FALSE);
        $arrSettings = $hearsayCommon->getPlatformSettingConfig();
        $config = $hearsayClientCustomization->getAdminContentConfigByThemeId()['config'];

        // Get error messages in any of the field validation is failed.
        $responseAjax = new AjaxResponse();
        $responseAjax = $hearsayCommon->attachValidateForm($responseAjax, $form_state, $config);
        if ($responseAjax) {
            return $responseAjax;
        }
        // Clear user input.
        $form_state = $hearsayCommon->clearFormInputs($form_state);

        // Get Contact and node.
        $contactUsApiEndPoint = $arrSettings->get('contact_api');
        $node = \Drupal::routeMatch()->getParameter('node');

        if ($node && $contactUsApiEndPoint != '') {
            $contactController->customAjaxSubmitContactForm($form_state, $node, $arrSettings, $config, $profileData);
        }
        return $form['cms_container'];
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $contactController = new HSContactController();
        $contactController->customSubmitContactForm();
    }
}
