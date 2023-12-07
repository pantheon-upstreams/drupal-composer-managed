<?php

namespace Drupal\surf_curriculum\Entity\DownloadRequest;
use Drupal\download_request\Entity\DownloadRequest as DownloadRequestBase;

class CurriculumModule extends DownloadRequestBase {
  public function getStatusId() {
    if (!$item = $this->get('request_items')->first()) {
      return NULL;
    }
    $download_request_item = $item->entity;

    if ($state_item = $download_request_item->state->first()) {
      return $state_item->getId();
    }
    return NULL;
  }

  public function getStatusDescription() {
    if ($item = $this->get('request_items')->first()) {
      $download_request_item = $item->entity;
      $download_request = $download_request_item->getParentEntity();

      if ($state_item = $download_request_item->state->first()) {
        $state_id = $state_item->getId();
        $state_label = $state_item->getLabel();
        $status_description = '@label';
        $status_date = NULL;

        if ($state_id == 'delivered') {
          $status_description = '@label. Return by @date.';
          $status_date = $download_request->field_date_returned->date;
        }

        if ($state_id == 'returned') {
          $status_date = $download_request->field_dates->end_date;
          $current_date = new \Drupal\surf_core\DrupalDateTime();
          $text = $current_date->isAfterDate($status_date) ? 'Expired' : 'Expires';
          $status_description = $text . ' @date.';
        }

        return [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => t($status_description, [
            '@label' => $state_label,
            '@date' => $status_date ? $status_date->format('F j, Y') : '',
          ]),
          '#attributes' => [
            'class' => 'status-description',
          ],
        ];
      }
    }

    return [];
  }
}