<?php

/**
 * @file
 * Provides API homepage for Advanced Aggregates.
 */

/**
 * Advanced Aggreggates API & Developer Documentation.
 *
 * @mainpage Advanced Aggreggates API & Developer Documentation
 * Welcome to the Advanced Aggreggates API & development documentation.
 *
 * Much of the effects of Advanced Aggregates is achieved through the core hooks
 * hook_js_alter() and hook_css_alter(). Most of AdvAgg is alterable though its
 * config settings. For other module developers, there is limited exposed
 * functionality beyond that although any of the classes can be re-used in
 * different ways theoretically. There are 2 events generated for other modules
 * to use; AssetOptimizationEvent::JS and AssetOptimizationEvent:CSS. You can
 * see examples and read more under
 * @link advagg_optimization_event Asset Optimization Event @endlink
 *
 * Newcomers to Drupal development should read the conceptual information
 * in @link https://www.drupal.org/docs/8/api/ Drupal API Introduction @endlink
 * or @link https://api.drupal.org/api/drupal/8.3.x Drupal API Docs @endlink.
 * The heavily documented
 * @link https://api.drupal.org/api/examples/8.x-1.x Example modules @endlink
 * may also be helpful.
 *
 * For any comments, support or questions see the
 * @link https://drupal.org/project/advagg module page @endlink.
 * Also, feel free to comment here if it is specific to one function/file.
 *
 * - Primary components of Advanced Aggregates:
 *   - @link advagg_drush Drush Integration @endlink
 *   - @link advagg_optimization_event Asset Optimization Event @endlink
 *   - @link advagg_tests Test Suite @endlink
 */
