<?php

namespace Drupal\Tests\layout_builder_operation_link\Functional;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\user\Entity\Role;

/**
 * Tests Layout Builder Operation Link.
 *
 * @group layout_builder_operation_link
 */
class OperationLinkTest extends BrowserTestBase {

  use TaxonomyTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field_ui',
    'layout_builder_operation_link',
    'node',
    'taxonomy',
    'views',
  ];

  /**
   * A non-Layout Builder enabled node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $pageNode;

  /**
   * A Layout Builder enabled node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $layoutBuilderNode;

  /**
   * A user with Layout Builder permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $layoutUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create user with Layout Builder permissions.
    $this->layoutUser = $this->drupalCreateUser([
      'access administration pages',
      'access content overview',
      'access taxonomy overview',
      'administer node display',
      'administer node fields',
      'administer taxonomy',
      'administer taxonomy_term display',
      'administer taxonomy_term fields',
      'bypass node access',
      'configure any layout',
    ]);
    $this->drupalLogin($this->layoutUser);

    // Create content types.
    $this->createContentType(['type' => 'page']);
    $this->createContentType(['type' => 'bundle_with_section_field']);

    // Enable Layout Builder w/ overrides for bundle_with_section_field bundle.
    LayoutBuilderEntityViewDisplay::load('node.bundle_with_section_field.default')
      ->enableLayoutBuilder()
      ->setOverridable()
      ->save();

    // Create nodes.
    $this->pageNode = $this->createNode([
      'type' => 'page',
    ]);
    $this->layoutBuilderNode = $this->createNode([
      'type' => 'bundle_with_section_field',
    ]);
  }

  /**
   * Tests Layout Builder Operation Link.
   */
  public function testOperationLink() {
    $assert_session = $this->assertSession();

    // Check for Layout operation link with user with Layout Builder
    // permissions.
    $this->drupalGet('/admin/content');

    $assert_session->elementNotExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "node/' . $this->pageNode->id() . '/layout")]');
    $assert_session->elementExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "node/' . $this->layoutBuilderNode->id() . '/layout")]');

    // Create taxonomy vocabularies.
    $vocabulary = $this->createVocabulary();
    $vocabulary_with_section_field = $this->createVocabulary();

    // Create terms.
    $this->createTerm($vocabulary);
    $this->createTerm($vocabulary_with_section_field);
    $vocabulary_id = $vocabulary->id();
    $vocabulary_with_section_field_id = $vocabulary_with_section_field->id();

    // Enable Layout Builder w/ overrides for vocabulary_with_section_field_id
    // bundle.
    EntityViewDisplay::create([
      'targetEntityType' => 'taxonomy_term',
      'bundle' => $vocabulary_with_section_field_id,
      'mode' => 'default',
      'status' => TRUE,
    ])->save();

    LayoutBuilderEntityViewDisplay::load("taxonomy_term.$vocabulary_with_section_field_id.default")
      ->enableLayoutBuilder()
      ->setOverridable()
      ->save();

    $this->drupalGet("admin/structure/taxonomy/manage/$vocabulary_id/overview/");

    $assert_session->elementNotExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "term/1/layout")]');

    $this->drupalGet("admin/structure/taxonomy/manage/$vocabulary_with_section_field_id/overview/");

    $assert_session->elementExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "term/2/layout")]');

    // Check for Layout operation link with user without Layout Builder
    // permissions.
    // Create user without Layout Builder permissions.
    $auth_user = $this->drupalCreateUser([
      'access administration pages',
      'access content overview',
      'access taxonomy overview',
      'administer taxonomy',
      'bypass node access',
    ]);
    $this->drupalLogin($auth_user);

    $this->drupalGet('/admin/content');
    $assert_session->elementNotExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "node/2/layout")]');

    $this->drupalGet("admin/structure/taxonomy/manage/$vocabulary_id/overview/");

    $assert_session->elementNotExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "term/1/layout")]');

    $this->drupalGet("admin/structure/taxonomy/manage/$vocabulary_with_section_field_id/overview/");

    $assert_session->elementNotExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "term/2/layout")]');
  }

  /**
   * Tests language support.
   */
  public function testLanguage() {
    $assert_session = $this->assertSession();

    \Drupal::service('module_installer')->install([
      'content_translation',
      'language',
      'layout_builder_at',
    ]);

    Role::Load($this->layoutUser->getRoles(TRUE)[0])
      ->grantPermission('administer content translation')
      ->grantPermission('administer languages')
      ->grantPermission('translate any entity')
      ->save();

    $additional_langcode = 'es';

    // Enable additional language.
    ConfigurableLanguage::createFromLangcode($additional_langcode)
      ->save();

    // Enable translation for bundle_with_section_field bundle and ensure the
    // change is picked up.
    \Drupal::service('content_translation.manager')
      ->setEnabled('node', 'bundle_with_section_field', TRUE);

    // Create a translation through the UI.
    $url_options = ['language' => \Drupal::languageManager()->getLanguage($additional_langcode)];
    $this->drupalGet('node/' . $this->layoutBuilderNode->id() . '/translations/add/en/' . $additional_langcode, $url_options);
    $this->getSession()->getPage()->pressButton('Save (this translation)');

    // Make layout builder field translatable.
    $this->drupalGet('admin/config/regional/content-language');
    $edit = [
      'entity_types[node]' => TRUE,
      'settings[node][bundle_with_section_field][fields][' . OverridesSectionStorage::FIELD_NAME . ']' => TRUE,
    ];
    $this->submitForm($edit, 'Save configuration');

    $this->drupalGet('/admin/content');

    $assert_session->elementExists('xpath', '//table//ul[contains(@class, "dropbutton")]//a[contains(@href, "' . $additional_langcode . '/node/2/layout")]');
  }

}
