README.md for Hearsay Events Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Events Section on Home, About Us and Solutions page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Events Module contains the following files with respective functionality

- hearsay_events.info.yml
  - Contains module information.

- hearsay_events.module
  - Contains hook_theme function specifying the array to be returned to twig template.
  
- templates/hearsay-events.html.twig
  - Contains the html code for display of block

- src/HearsayEvents.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.