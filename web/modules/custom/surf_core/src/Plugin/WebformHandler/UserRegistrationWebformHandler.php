<?php

namespace Drupal\surf_core\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_user_registration\Plugin\WebformHandler\UserRegistrationWebformHandler as WebformHandlerContrib;

class UserRegistrationWebformHandler extends WebformHandlerContrib {

  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // TODO:
    // - check the right variables to make sure the plugin is enabled
    // - check if current user's email exists, (make sure to do this from the mapping)
    // - if exists set the $this->>userAccount variable to be saved later on for the webform submission.
    parent::validateForm($form, $form_state, $webform_submission);
  }

}
