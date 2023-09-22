<?php

namespace Drupal\Tests\surf_core\ExistingSite;

use Drupal\surf_core\Entity\UserRequest;

class UserRequestTest extends ExistingSiteBase {

  public function testCurriculumReservation() {
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet('form/curriculum-reservation');
    $values = [
      'email' => "asherry53@gmail.com",
      'name[first]' => "Alan",
      'name[last]' => "Sherry",
    ];
    $this->submitForm($values, 'Submit');
    $this->getCurrentPage()->hasContent('New submission added');

    $webform_submission = $this->getLastEntityOfType('webform_submission');
    //$user_request = $this->getLastEntityOfType('user_request');
    //$this->markEntityForCleanup($user_request);
    $this->markEntityForCleanup($webform_submission);


    $debug = 'true';
  }

  public function testSubmitVisit() {
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet('form/visit');
    $values = [
      'date[date]' => "2023-02-05",
      'date[time]' => "12:00:00",
      'email' => "asherry53@gmail.com",
      'name[first]' => "Alan",
      'name[last]' => "Sherry",
      'ref_grade_level' => "Early Elementary (166)",
      'notes' => "Test notes for this visit submission",
      'number_students' => "4",
      'request_type' => "field_trip",
      'school_district' => "Abbot",
      'ref_school_presentation' => "Test school presentation (5303)"
    ];
    $this->submitForm($values, 'Submit');
    $this->getCurrentPage()->hasContent('New submission added');

    $webform_submission = $this->getLastEntityOfType('webform_submission');
    $user_request = $this->getLastEntityOfType('user_request');
    $this->markEntityForCleanup($user_request);
    $this->markEntityForCleanup($webform_submission);


    $debug = 'true';
  }

}
