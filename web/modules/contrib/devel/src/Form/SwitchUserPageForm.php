<?php

namespace Drupal\devel\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\devel\SwitchUserListHelper;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define an accessible form to switch the user.
 */
class SwitchUserPageForm extends FormBase {

  /**
   * The FormBuilder object.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * A helper for creating the user list form.
   *
   * @var Drupal\devel\SwitchUserListHelper
   */
  protected $switchUserListHelper;

  /**
   * Constructs a new SwitchUserPageForm object.
   *
   * @param \Drupal\devel\SwitchUserListHelper $switchUserListHelper
   *   A helper for creating the user list form.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   */
  public function __construct(SwitchUserListHelper $switchUserListHelper, FormBuilderInterface $form_builder) {
    $this->switchUserListHelper = $switchUserListHelper;
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('devel.switch_user_list_helper'),
      $container->get('form_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'devel_switchuser_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if ($accounts = $this->switchUserListHelper->getUsers()) {
      $form['devel_links'] = $this->switchUserListHelper->buildUserList($accounts);
      $form['devel_form'] = $this->formBuilder->getForm('\Drupal\devel\Form\SwitchUserForm');
    }
    else {
      $this->messenger->addStatus('There are no user accounts present!');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do here. This is delegated to devel.switch via http call.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do here. This is delegated to devel.switch via http call.
  }

}
