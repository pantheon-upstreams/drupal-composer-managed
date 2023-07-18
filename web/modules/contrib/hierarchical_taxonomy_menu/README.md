CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

Hierarchical Taxonomy Menu is a Drupal 8/9 module for creating menus from
taxonomy terms. You can display an image next to a menu item if your terms have
an image field, and there is also an option to make the menu collapsible. This
module comes with a Twig template, so you can customize the HTML structure any
way you want.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/hierarchical_taxonomy_menu

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/hierarchical_taxonomy_menu


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

 * Install the Hierarchical Taxonomy Menu module as you would normally install a
   contributed Drupal module. Visit https://www.drupal.org/node/1897420 for
   further information.


CONFIGURATION
-------------

After you install the module go to the block layout '/admin/structure/block' and
place 'Hierarchical Taxonomy Menu' block to any region you want. In block
settings, you can choose a vocabulary from which you want to create a menu, and
if that vocabulary has image fields you will see multiple options in a select
box. You can limit your menu to a part of taxonomy terms, by selecting a base
term. In this case, menu items will be generated only for its children's terms.


MAINTAINERS
-----------

Current maintainers:
 * Goran Nikolovski (gnikolovski) - https://www.drupal.org/u/gnikolovski

This project has been sponsored by:
 * Studio Present - https://www.drupal.org/studio-present
