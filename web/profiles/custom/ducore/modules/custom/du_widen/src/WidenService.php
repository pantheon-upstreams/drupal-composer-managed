<?php

namespace Drupal\du_widen;

use Drupal\Component\Serialization\Json;

/**
 * WidenService class.
 */
class WidenService implements WidenServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * Base Post request.
   *
   * @param string $url
   *   The URL to POST.
   * @param array $headers
   *   Array of header data.
   * @param string $body
   *   Body data.
   * @param array $params
   *   Additional params.
   *
   * @return mixed
   *   Returns the JSON decoded data.
   */
  public function post($url, array $headers = [], $body = NULL, array $params = []) {
    $result = NULL;

    $query = '';

    // Loop params and build url with params as a get variables appended.
    foreach ($params as $key => $value) {
      $query .= $key . '=' . $value . '&';
    }
    $url = $url . '?' . $query;

    $cid = 'widen_connector_post' . \Drupal::languageManager()->getCurrentLanguage()->getId() . $url;

    $result = NULL;
    if ($cache = \Drupal::cache()->get($cid)) {
      $result = $cache->data;
    }
    else {
      $curl = curl_init();
      // Set curl opts, timeouts prevent api down from affecting site up time.
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($curl, CURLOPT_TIMEOUT, 15);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = curl_exec($curl);
      curl_close($curl);

      // Set cache if we got any results back from the API. If there was an
      // error, then $result will be completely empty, but if there are zero
      // results it will have returned data in JSON.
      if (!empty($result)) {
        $cache_lifetime = 60 * 5;
        \Drupal::cache()->set($cid, $result, time() + $cache_lifetime);
      }
    }

    return Json::decode($result);
  }

  /**
   * Base GET request.
   *
   * @param string $url
   *   The URL to GET.
   * @param array $params
   *   Additional params.
   * @param array $headers
   *   Array of header data.
   *
   * @return mixed
   *   Returns the JSON decoded data.
   */
  public function get($url, array $params = [], array $headers = []) {
    $result = NULL;
    $query = '';

    $url = $url . '?' . http_build_query($params);

    $cid = 'widen_get' . \Drupal::languageManager()->getCurrentLanguage()->getId() . $url;

    $result = NULL;
    if ($cache = \Drupal::cache()->get($cid)) {
      $result = $cache->data;
    }
    else {
      $curl = curl_init();
      // Set curl opts timeouts re key to prevent api down from effecting site
      // up time.
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($curl, CURLOPT_TIMEOUT, 15);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($curl);
      curl_close($curl);

      // Set cache if we got any results back from the API. If there was an
      // error, then $result will be completely empty, but if there are zero
      // results it will have returned data in JSON.
      if (!empty($result)) {
        $cache_lifetime = 60 * 5;
        \Drupal::cache()->set($cid, $result, time() + $cache_lifetime);
      }
    }

    return Json::decode($result);
  }

  /**
   * Retrieves search results or full collection from ebsco.
   *
   * @param array $params
   *   API parameters.
   *
   * @return array
   *   The search results.
   */
  public function getSearchResults(array $params) {
    // Get the request headers.
    $request_headers = $this->getRequestHeaders();
    $request_headers[] = 'Content-Type: application/json';

    // Get the query params.
    $params += [
      'query' => '',
      'sort' => '',
      'limit' => 10,
      'offset' => 0,
      'expand' => 'thumbnails,file_properties,metadata',
    ];

    // Make the API call and return the results.
    return $this->get($this->getUrl() . '/assets/search', $params, $request_headers);
  }

  /**
   * Get the EBSCO url.
   *
   * @return string
   *   Returns the URL.
   */
  protected function getUrl() {
    $config = \Drupal::config('du_widen.settings');
    return $config->get('url');
  }

  /**
   * Retrieves the request headers needed to make a request.
   *
   * @return array
   *   Returns the request headers needed to make a request.
   */
  protected function getRequestHeaders() {
    $config = \Drupal::config('du_widen.settings');

    $headers = [
      'Authorization: Bearer ' . $config->get('key'),
      'Accept: application/json',
    ];

    return $headers;
  }

}
