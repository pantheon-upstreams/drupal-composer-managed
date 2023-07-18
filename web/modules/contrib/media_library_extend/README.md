INTRODUCTION
------------

The Media Library Extend module is an API module that provides plugins and
configuration that allow other modules to integrate with Drupal core's Media
Library.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/media_library_extend

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/media_library_extend

REQUIREMENTS
------------

This module requires the following modules:

 * Media Library (starting with Drupal 8.7.0)

RECOMMENDED MODULES
-------------------

By itself, this module is not very useful. You either need to write a custom
MediaLibrarySource plugin for your service, or use one provided by the
community:

 * [Media Library Youtube](https://www.drupal.org/project/media_library_extend_youtube)

INSTALLATION
------------

 * Install as you would normally [install a contributed Drupal module](https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules).

CONFIGURATION
-------------

 * Configure available Media Library Panes in Administration » Configuration »
   Media » Media library » Panes

   Depending on the selected Media bundle, different source plugins may be
   available.

   Source plugins may provide configurations options that are specific to the
   pane being configured.

   Each configured pane is shown as a Tab in the Media Library whenever the
   associated media bundle is available.

MAINTAINERS
-----------

Current Maintainers:
 * [David I. (Mirroar)](https://www.drupal.org/u/mirroar)

This project has been sponsored by:
 * [werk21](https://www.werk21.de)

   werk21 is based in Berlin, Germany and specializes in the development of
   custom web applications using Drupal. Our 20 staff members care for over 300
   clients in the range of politics and NGO.

 * [visitBerlin](https://www.visitberlin.de)

   We promote Berlin – one of Europe’s top three travel destinations, after
   London and Paris. As Berlin’s official promotional organisation for tourism
   and conventions visitBerlin produces creative ideas and marketing campaigns
   for Berlin delivered around the world. We also provide support services for
   all those joining us in actively promoting Berlin as a world city.
