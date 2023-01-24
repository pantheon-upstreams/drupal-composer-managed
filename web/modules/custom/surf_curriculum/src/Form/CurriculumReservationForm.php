<?php

namespace Drupal\surf_curriculum\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the curriculum reservation entity edit forms.
 */
class CurriculumReservationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New curriculum reservation %label has been created.', $message_arguments));
        $this->logger('surf_curriculum')->notice('Created new curriculum reservation %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The curriculum reservation %label has been updated.', $message_arguments));
        $this->logger('surf_curriculum')->notice('Updated curriculum reservation %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.curriculum_reservation.canonical', ['curriculum_reservation' => $entity->id()]);

    return $result;
  }

}
