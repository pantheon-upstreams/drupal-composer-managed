<?php

namespace Drupal\Tests\simple_gmap\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\Entity\Node;

/**
 * Creates a page to look at to test the Simple Google Maps module.
 *
 * Run the test, scroll to the bottom, open the last page link, and check
 * what the verbose message lines say to check on that page. If you want to
 * check the static maps, be sure to edit this file and put in an API key
 * that is valid for static maps.
 *
 * @group simple_gmap
 */
class SimpleGmapTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * API key for static maps.
   *
   * @var string
   */
  protected $apiKey = 'Static maps will not work unless you put in a key';

  /**
   * The entity object.
   *
   * @var Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'node',
    // TODO can I remove menu_ui?
    // (see node.type.simple_gmap_stress_test.yml)
    'menu_ui',
    'path',
    'field',
    'field_ui',
    'simple_gmap_stress_test',
    'text',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create and login as an authenticated user.
    $auth_user = $this->drupalCreateUser([
      'access content',
      'administer node display',
    ]);
    $this->drupalLogin($auth_user);

    // Create a stress test node with three fields where the default values
    // will exercise the formatter.
    $this->node = Node::create([
      'type' => 'simple_gmap_stress_test',
      'title' => 'A three field node',
    ]);
    $this->node->save();
  }

  /**
   * Fill out the settings form for the 'blank' field on the stress test page.
   *
   * The make assertions about the population of the blank settings
   * summary column of the fields display table.
   */
  public function testSettingsForm() {
    $session = $this->getSession();
    $assert_session = $this->assertSession();

    // To visiting the settings form we must first visitng the page
    // listing all its fields.
    $this->drupalGet('admin/structure/types/manage/simple_gmap_stress_test/display');

    // Click on the row's edit button (styled with a gear icon).
    $blank_row_button = $assert_session->waitForElementVisible('css', 'tr#field-blank input[alt="Edit"]');
    $this->assertNotNull($blank_row_button);
    $blank_row_button->click();

    // Wait for the settings form associated with the 'blank' field to appear
    // by waiting for the update button.
    $update_visible = $assert_session->waitForElementVisible('css', 'form input[value="Update"]');
    $this->assertNotNull($update_visible);

    $edit_form = $session->getPage();
    $edit_form->checkField('Include embedded dynamic map');
    $edit_form->checkField('Include embedded static map');

    // Checking the 'static map" options causes the API key field to appear.
    $visible_key = $assert_session->waitForElementVisible('css', 'label:contains("Google Maps API key")');
    $this->assertNotNull($visible_key);

    $edit_form2 = $session->getPage();
    $edit_form2->fillField('Google Maps API key', $this->apiKey);
    $edit_form2->fillField('Link text', 'use_address');
    $edit_form2->fillField('Zoom level', '5');
    $edit_form2->selectFieldOption('Map type', 'Satellite');
    $edit_form2->fillField('Language', 'xyz');
    $edit_form2->checkField('Include link to map');
    $edit_form2->checkField('Include original address text');
    $edit_form2->find('css', 'input[value="Update"]')->click();

    // As the update button completes the edit "gears" icon associated with
    // the 'blank' will reappear.
    $visible_gears1 = $assert_session->waitForElementVisible('css', "#field-blank input[alt=\"Edit\"]");
    $this->assertNotNull($visible_gears1);

    // Re-examine the field row containing field1.
    $display_page_updated = $session->getPage();
    $field_row_updated = $display_page_updated->find('css', "#field-blank");

    // Make assertions about the summary column.
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Dynamic map")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Static map")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Map link: use_address")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Map Type: Satellite")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Zoom Level: 5")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Language: xyz")'));
    $this->assertTrue($field_row_updated->has('css', 'div:contains("Original text displayed")'));
  }

}
