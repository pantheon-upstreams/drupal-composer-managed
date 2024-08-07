<?php

declare(strict_types=1);

namespace Drupal\google_tag\EventSubscriber;

use Drupal\Core\Render\Element;
use Drupal\google_tag\EventCollectorInterface;
use Drupal\search_api\Event\ProcessingResultsEvent;
use Drupal\search_api\Event\SearchApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Search API subscriber.
 */
final class SearchApiSubscriber implements EventSubscriberInterface {

  /**
   * Event Collector Service.
   *
   * @var \Drupal\google_tag\EventCollectorInterface
   */
  private EventCollectorInterface $collector;

  /**
   * SearchApiSubscriber constructor.
   *
   * @param \Drupal\google_tag\EventCollectorInterface $collector
   *   Collector service.
   */
  public function __construct(
    EventCollectorInterface $collector
  ) {
    $this->collector = $collector;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      SearchApiEvents::PROCESSING_RESULTS => 'onSearch',
    ];
  }

  /**
   * Fires an event on search.
   *
   * @param \Drupal\search_api\Event\ProcessingResultsEvent $event
   *   Event object.
   */
  public function onSearch(ProcessingResultsEvent $event) {
    $keys = $event->getResults()->getQuery()->getKeys();
    if ($keys !== NULL) {
      $keys = array_filter(
        !is_array($keys) ? [$keys] : $keys,
        [Element::class, 'child'],
        ARRAY_FILTER_USE_KEY
      );
      $this->collector->addEvent('search', [
        'search_term' => implode(' ', $keys),
      ]);
    }
  }

}
