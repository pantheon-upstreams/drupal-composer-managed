<?php

namespace Drupal\du_widen;

/**
 * Provides an interface defining a widen service.
 *
 * @ingroup user_api
 */
interface WidenServiceInterface {

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
  public function post($url, array $headers, $body, array $params);

  /**
   * Base GET request.
   *
   * @param string $url
   *   The URL to POST.
   * @param array $params
   *   Additional params.
   * @param array $headers
   *   Array of header data.
   *
   * @return mixed
   *   Returns the JSON decoded data.
   */
  public function get($url, array $params, array $headers);

}
