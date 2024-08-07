QUEUE UI
--------

 * Introduction
 * Requirements
 * Features
 * Installation
 * Maintainers


INTRODUCTION
------------

The Queue UI module provides a user interface to viewing and managing Drupal
queues created via the Queue API which began in Drupal 7.

QueueUI's dev releases will be packaged whilst D8 evolves. The current port
works with all existing base functionality. However, the dev version needs to be
extended to non-core classes of the Queue Inspection, which is going to need
converting to the plugin system before it can be extended by other contribute
modules.

 * For a full description of the module visit:
   https://www.drupal.org/project/queue_ui

 * To submit bug reports and feature suggestions, or to track changes visit:
   https://www.drupal.org/project/issues/queue_ui

 * Queue operations API:
   https://api.drupal.org/api/drupal/core%21core.api.php/group/queue


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


FEATURES
--------

 * View queues and number of items
 * Developers can define meta info about queues they create and process
 * Process queue with Batch API
 * Process queue during cron
 * Remove leases
 * Delete queue


INSTALLATION
------------

 * Install the Queue UI module as you would normally install a contributed
   Drupal module. Visit https://www.drupal.org/node/1897420 for further
   information.


MAINTAINERS
-----------

 * Brecht Ceyssens (bceyssens) - https://www.drupal.org/u/bceyssens

Supporting organization:

 * Nascom - https://www.drupal.org/nascom
