<?php

namespace Drupal\imce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for help routes.
 */
class ImceHelpController extends ControllerBase {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The list of available modules.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $extensionListModule;

  /**
   * Creates a new HelpController.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extension_list_module
   *   The list of available modules.
   */
  public function __construct(RouteMatchInterface $route_match, ModuleExtensionList $extension_list_module) {
    $this->routeMatch = $route_match;
    $this->extensionListModule = $extension_list_module;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function index() {
    $build = [];
    $build['#theme'] = 'imce_help';
    $build['#title'] = 'Imce File Manager Help';
    $build['#markup'] = static::htmlHelp();
    $build['#videos'][1]['title'] = 'IMCE with CKEditor in Drupal 8';
    $build['#videos'][1]['video'] = 'https://www.youtube.com/embed/wnOmlvG4tRo';
    $build['#videos'][2]['title'] = 'Integration IMCE with image/file field in Drupal 8';
    $build['#videos'][2]['video'] = 'https://www.youtube.com/embed/MAHonUyKVc0';
    return $build;
  }

  /**
   * Returns html help.
   */
  public static function htmlHelp() {
    return '
      <h3>About</h3>
      <p>IMCE is an image/file uploader and browser that supports personal directories and quota.</p>

      <h3>Menu Integration</h3>
      <p>Create a custom menu item with /imce path.</p>

      <h3>CKEditor Integration</h3>
      <ol>
        <li type="1">Go to Administration > Configuration > Content Authoring > Text formats and editors > and <b>edit</b> a text format that uses CKEditor.</li>
        <li type="1">Enable CKEditor image button without image uploads.</li>
      </ol>
      <p><b>Note:</b> Image uploads must be disabled in order for IMCE link appear in the image
  dialog. There is also an image button provided by Imce but it can\'t be used for
  editing existing images.</p>

      <h3>BUEditor Integration</h3>
      <ol>
        <li type="1">Edit your editor at /admin/config/content/bueditor</li>
        <li type="1">Select Imce File Manager as the File browser under Settings.</li>
      </ol>

      <h3>File/Image Field Integration</h3>
      <ol>
        <li type="1">Go to form settings of your content type.<br/>Ex: /admin/structure/types/manage/article/form-display.</li>
        <li type="1">Edit widget settings of a file/image field.</li>
        <li type="1">Check the box saying "Allow users to select files from Imce File Manager
          for this field." and save.</li>
        <li type="1">You should now see the "Open File Browser" link above the upload widget
          in the content form.</li>
      </ol>';
  }

}
