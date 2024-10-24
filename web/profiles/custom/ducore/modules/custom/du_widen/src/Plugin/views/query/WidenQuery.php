<?php

namespace Drupal\du_widen\Plugin\views\query;

use Drupal\du_widen\WidenService;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Widen views query plugin.
 *
 * Wraps calls to the Widen API in order to expose the results to views.
 *
 * @ViewsQuery(
 *   id = "widen_query",
 *   title = @Translation("WidenQuery"),
 *   help = @Translation("Query against the Widen API.")
 * )
 */
class WidenQuery extends QueryPluginBase {

  /**
   * Widen client.
   *
   * @var \Drupal\du_widen\WidenService
   */
  protected $widenClient;

  /**
   * Collection of filter criteria.
   *
   * @var array
   */
  protected $where;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, WidenService $widen_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->widenClient = $widen_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('widen.connector')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(ViewExecutable $view) {
    // Store the view in the object to be able to use it later.
    $this->view = $view;

    $view->initPager();

    // Let the pager modify the query to add limits.
    $view->pager->query();

  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view) {
    $params = [];

    if (isset($this->where)) {
      foreach ($this->where as $where) {
        foreach ($where['conditions'] as $condition) {
          // Remove dot from begining of the string.
          $field_name = ltrim($condition['field'], '.');

          // Handle irregular string vs array and set field value.
          // For now, no support for multivalue filters.
          $field_value = (is_array($condition['value']) ? $condition['value'][0] : $condition['value']);

          // Detailed search.
          if (!empty($field_value)) {
            $params[$field_name] = $field_value;
          }
        }
      }
    }

    // Use limit for pager if it is set. Set limit to something large if not
    // set. The API has a max limit that will be used. We have no clear way of
    // knowing what that is.
    $limit = !empty($this->limit) ? $this->limit : 100;

    // Use page. If no page or on page zero, we will use page 1 for the API.
    $page = (!empty($view->pager->current_page) ? $view->pager->current_page + 1 : 1);

    // Add more values to filter options array.
    $params += [
      'limit' => $limit,
      'offset' => ($page - 1) * $limit,
      'expand' => 'thumbnails,file_properties,metadata',
    ];

    // Filter on file types.
    if (!empty($params['query'])) {
      $params['query'] .= ' and (fn:*.jpg or fn:*.jpeg or fn:*.png or fn:*.gif or fn:*.svg)';
    }
    else {
      $params['query'] = '(fn:*.jpg or fn:*.jpeg or fn:*.png or fn:*.gif or fn:*.svg)';
    }

    // Get the search results from the API.
    $search = $this->widenClient->getSearchResults($params);

    // Set total items.
    if (!empty($search['total_count'])) {
      $view->total_rows = $view->pager->total_items = $search['total_count'];
      $view->pager->updatePageInfo();
    }

    // Parse callback into results.
    $results = $search['items'] ?? [];

    $index = 0;
    $alt_text_settings = [];
    foreach ($results as $data) {
      $row = [];
      $row['id'] = $data['id'];
      $row['external_id'] = $data['external_id'];
      $row['filename'] = $data['filename'];
      $row['width'] = $data['file_properties']['image_properties']['width'];
      $row['height'] = $data['file_properties']['image_properties']['height'];
      $row['thumbnail'] = $data['thumbnails']['160px']['url'];
      $row['alt_text'] = '';
      if (!empty($data['metadata']['fields']['alternativeText'][0])) {
        $row['alt_text'] = $data['metadata']['fields']['alternativeText'][0];
        $alt_text_settings[$row['external_id']] = $row['alt_text'];
      }
      $row['index'] = $index++;

      // Create a views result.
      $view->result[] = new ResultRow($row);
    }
    $view->element['#attached']['drupalSettings']['du_widen']['alt_text'] = $alt_text_settings;
  }

  /**
   * Adds a simple condition to the query.
   *
   * Collect data on the configured filter criteria so that we can appropriately
   * apply it in the query() and execute() methods.
   *
   * @param int $group
   *   The WHERE group to add these to; groups are used to create AND/OR
   *   sections. Groups cannot be nested. Use 0 as the default group.
   *   If the group does not yet exist it will be created as an AND group.
   * @param string $field
   *   The name of the field to check.
   * @param mixed $value
   *   The value to test the field against. In most cases, this is a scalar. For
   *   more complex options, it is an array. The meaning of each element in the
   *   array is dependent on the $operator.
   * @param string $operator
   *   The comparison operator, such as =, <, or >=. It also accepts more
   *   complex options such as IN, LIKE, LIKE BINARY, or BETWEEN. Defaults to =.
   *   If $field is a string you have to use 'formula' here.
   *
   * @see \Drupal\Core\Database\Query\ConditionInterface::condition()
   * @see \Drupal\Core\Database\Query\Condition
   */
  public function addWhere($group, $field, $value = NULL, $operator = NULL) {
    // Ensure all variants of 0 are actually 0. Thus '', 0 and NULL are all
    // the default group.
    if (empty($group)) {
      $group = 0;
    }

    // Check for a group.
    if (!isset($this->where[$group])) {
      $this->setWhereGroup('AND', $group);
    }

    $this->where[$group]['conditions'][] = [
      'field' => $field,
      'value' => $value,
      'operator' => $operator,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    return $options;
  }

  /**
   * Ensures a table exists in the query.
   *
   * Replicates Views' default SQL backend.
   * See https://www.drupal.org/node/2484565 for more information.
   *
   * @return string
   *   An empty string.
   */
  public function ensureTable($table, $relationship = NULL) {
    return '';
  }

  /**
   * Adds a field to the table.
   *
   * Widen API does not limit fields that come back.
   *
   * @param string $table
   *   NULL in most cases, we could probably remove this altogether.
   * @param string $field
   *   The name of the metric/dimension/field to add.
   * @param string $alias
   *   Probably could get rid of this too.
   * @param array $params
   *   Probably could get rid of this too.
   *
   * @return string
   *   The name that this field can be referred to as.
   *
   * @see \Drupal\views\Plugin\views\query\Sql::addField()
   */
  public function addField(string $table, string $field, string $alias = NULL, array $params = []) {
    return $field;
  }

}
