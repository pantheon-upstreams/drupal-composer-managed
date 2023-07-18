README.md for Hearsay Custom Content Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Custom Content Section on Home, About Us and Solutions page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Custom Content Module contains the following files with respective functionality

- hearsay_custom_content.info.yml
  - Contains module information.

- hearsay_custom_content.module
  - Contains hook_theme function specifying the array to be returned to twig template.
  
- templates/hearsay-custom_content.html.twig
  - Contains the html code for display of block

- src/HearsayCustomContent.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.