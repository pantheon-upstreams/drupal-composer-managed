<?php

namespace Drupal\imce\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\Core\Url;
use Drupal\imce\ImceSettersTrait;
use Drupal\user\RoleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Imce settings form.
 */
class ImceSettingsForm extends ConfigFormBase {

  use ImceSettersTrait;

  /**
   * Manages entity type plugin definitions.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The system file config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $configSystemFile;

  /**
   * Provides a StreamWrapper manager.
   *
   * @var Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\imce\Form\ImceSettingsForm
     */
    $instance = parent::create($container);
    $instance->setConfigSystemFile($container->get('config.factory')->get('system.file'));
    $instance->setEntityTypeManager($container->get('entity_type.manager'));
    $instance->setStreamWrapperManager($container->get('stream_wrapper_manager'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'imce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['imce.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('imce.settings');
    $form['roles_profiles'] = $this->buildRolesProfilesTable($config->get('roles_profiles') ?: []);
    // Common settings container.
    $form['common'] = [
      '#type' => 'details',
      '#title' => $this->t('Common settings'),
    ];
    $form['common']['abs_urls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable absolute URLs'),
      '#description' => $this->t('Make the file manager return absolute file URLs to other applications.'),
      '#default_value' => $config->get('abs_urls'),
    ];
    $form['common']['admin_theme'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use admin theme for IMCE paths'),
      '#default_value' => $config->get('admin_theme'),
      '#description' => $this->t('If you have user interface issues with the active theme you may consider switching to admin theme.'),
    ];
    $form['#attached']['library'][] = 'imce/drupal.imce.admin';
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('imce.settings');
    // Absolute URLs.
    $config->set('abs_urls', $form_state->getValue('abs_urls'));
    // Admin theme.
    $config->set('admin_theme', $form_state->getValue('admin_theme'));
    $roles_profiles = $form_state->getValue('roles_profiles');
    // Filter empty values.
    foreach ($roles_profiles as $rid => &$profiles) {
      if (!$profiles = array_filter($profiles)) {
        unset($roles_profiles[$rid]);
      }
    }
    $config->set('roles_profiles', $roles_profiles);
    $config->save();
    // Warn about anonymous access.
    if (!empty($roles_profiles[RoleInterface::ANONYMOUS_ID])) {
      $this->messenger()
        ->addMessage($this->t('You have enabled anonymous access to the file manager. Please make sure this is not a misconfiguration.'), 'warning');
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * Get the profile options.
   *
   * @return array
   *   The profile options.
   */
  public function getProfileOptions() {
    // Prepare profile options.
    $options = ['' => '-' . $this->t('None') . '-'];
    foreach ($this->entityTypeManager->getStorage('imce_profile')->loadMultiple() as $pid => $profile) {
      $options[$pid] = $profile->label();
    }
    return $options;
  }

  /**
   * Build header.
   *
   * @return array
   *   Array of headers items.
   */
  public function buildHeaderProfilesTable() : array {
    $wrappers = $this->streamWrapperManager->getNames(StreamWrapperInterface::WRITE_VISIBLE);
    $imce_url = Url::fromRoute('imce.page')->toString();
    $rp_table['#header'] = [$this->t('Role')];
    $default = $this->configSystemFile->get('default_scheme');
    foreach ($wrappers as $scheme => $name) {
      $url = $scheme === $default ? $imce_url : $imce_url . '/' . $scheme;
      $rp_table['#header'][]['data'] = ['#markup' => '<a href="' . $url . '">' . Html::escape($name) . '</a>'];
    }

    return $rp_table;
  }

  /**
   * Create tables profiles rows.
   */
  public function buildRowsProfilesTables($roles, $roles_profiles, $wrappers) {
    // Prepare roles.
    $rp_table = [];
    foreach ($roles as $rid => $role) {
      $rp_table[$rid]['role_name'] = [
        '#plain_text' => $role->label(),
      ];
      foreach ($wrappers as $scheme => $name) {
        $rp_table[$rid][$scheme] = [
          '#type' => 'select',
          '#options' => $this->getProfileOptions(),
          '#default_value' => isset($roles_profiles[$rid][$scheme]) ? $roles_profiles[$rid][$scheme] : '',
        ];
      }
    }

    return $rp_table;
  }

  /**
   * Returns roles-profiles table.
   */
  public function buildRolesProfilesTable(array $roles_profiles) {
    $rp_table = ['#type' => 'table'];

    $roles = user_roles();
    $wrappers = $this->streamWrapperManager->getNames(StreamWrapperInterface::WRITE_VISIBLE);

    $imce_url = Url::fromRoute('imce.page')->toString();

    $rp_table += $this->buildHeaderProfilesTable($wrappers);
    $rp_table += $this->buildRowsProfilesTables($roles, $roles_profiles, $wrappers);

    // Add description.
    $rp_table['#prefix'] = '<h3>' . $this->t('Role-profile assignments') . '</h3>';
    $desc = $this->t('Assign configuration profiles to user roles for available file systems. Users with multiple roles get the bottom most profile.');
    $desc .= ' ' . $this->t(
      'The default file system %name is accessible at :url path.',
      [
        '%name' => $wrappers[$this->configSystemFile->get('default_scheme')],
        ':url' => $imce_url,
      ]
    );
    $rp_table['#suffix'] = '<div class="description">' . $desc . '</div>';
    return $rp_table;
  }

}
