<?php

namespace Drupal\section_library\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for section library edit forms.
 */
class SectionLibraryForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    $label = $this->entity->label();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('%label added to section library.', [
          '%label' => $label,
        ]));
        break;

      case SAVED_UPDATED:
        $this->messenger()->addMessage($this->t('%label has been updated.', [
          '%label' => $label,
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('%label saved to section library.', [
          '%label' => $label,
        ]));
    }

    $form_state->setRedirect('entity.section_library_template.collection');
  }

}
