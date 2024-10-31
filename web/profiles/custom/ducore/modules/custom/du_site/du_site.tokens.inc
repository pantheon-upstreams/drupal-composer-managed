<?php

/**
 * @file
 * Token integration for the the DU Site module.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\pathauto\Entity\PathautoPattern;

/**
 * Implements hook_token_info().
 */
function du_site_token_info() {
  $type = [
    'name' => t('DU Site'),
    'description' => t('Custom DU tokens.'),
    'needs-data' => 'node',
  ];

  $node['du-standard-page-url'] = [
    'name' => t("Standard Page URL"),
    'description' => t('Logic fot that standard page URL.'),
  ];

  $node['du-inline-unit'] = [
    'name' => t('Inline Unit'),
    'description' => t('Inline Unit base path'),
  ];

  return [
    'types' => ['node' => $type],
    'tokens' => ['node' => $node],
  ];
}

/**
 * Implements hook_tokens().
 */
function du_site_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $token_service = \Drupal::token();
  $path_cleaner = \Drupal::service('pathauto.alias_cleaner');

  $replacements = [];

  if ($type == 'node' && !empty($data['node'])) {
    $node = $data['node'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Standard page pattern.
        case 'du-standard-page-url':
          // Check for menu items settings.
          $menu_items = menu_ui_get_menu_link_defaults($node);
          $alias_path = '';

          // Use menu generated pathing.
          if (!empty($menu_items['entity_id'])) {
            // Get pathauto pattern.
            $path_object = PathautoPattern::load('standard_page_urls');
            $path_pattern = @$path_object->getPattern();
            // Create path.
            if (!empty($path_pattern)) {
              $alias_path_pieces = [];
              $path_pattern_pieces = explode('/', $path_pattern);
              foreach ($path_pattern_pieces as $piece) {
                if ($piece != '[node:menu-link:parents:join-path]') {
                  $alias_path_pieces[] = $path_cleaner->cleanString($token_service->replace($piece, ['node' => $node]));
                }
                else {
                  $updated_piece = $token_service->replace($piece, ['node' => $node]);
                  if ($piece != $updated_piece) {
                    $alias_path_pieces[] = $updated_piece;
                  }
                }
              }
              $alias_path = implode('/', $alias_path_pieces);

              // Set back to empty if replacements did not work.
              if ($path_pattern == $alias_path) {
                $alias_path = '';
              }
            }
          }

          // Use non-menu generated path.
          if (empty($alias_path)) {
            // Get pathauto pattern.
            $path_object = PathautoPattern::load('standard_page_urls_without_menu_link');
            $path_pattern = @$path_object->getPattern();
            // Create path.
            if (!empty($path_pattern)) {
              $alias_path_pieces = [];
              $path_pattern_pieces = explode('/', $path_pattern);
              foreach ($path_pattern_pieces as $piece) {
                if ($piece != '[node:menu-link:parents:join-path]') {
                  $alias_path_pieces[] = $path_cleaner->cleanString($token_service->replace($piece, ['node' => $node]));
                }
                else {
                  $updated_piece = $token_service->replace($piece, ['node' => $node]);
                  if ($piece != $updated_piece) {
                    $alias_path_pieces[] = $updated_piece;
                  }
                }
              }
              $alias_path = implode('/', $alias_path_pieces);

              // Set back to empty if replacements did not work.
              if ($path_pattern == $alias_path) {
                $alias_path = '';
              }
            }
          }

          // Default path if all else fails.
          if (empty($alias_path)) {
            $alias_path = $path_cleaner->cleanString('content/' . $token_service->replace('[node:title]', ['node' => $node]));
          }

          // Replace path.
          $replacements[$original] = $alias_path;
          break;

        // Subsite pattern.
        case 'du-inline-unit':
          if (\Drupal::moduleHandler()->moduleExists('du_inline_unit')) {
            // Get the inline unit entity if it exists.
            $inline_unit = du_inline_unit_get_inline_unit($node);
            if (!empty($inline_unit)) {
              $replacements[$original] = $inline_unit->get('field_slug')->value;
            }
          }
          break;

      }
    }
  }

  return $replacements;
}
